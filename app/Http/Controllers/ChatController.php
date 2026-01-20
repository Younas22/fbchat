<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\FacebookPage;
use App\Models\Message;
use App\Services\FacebookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    protected $facebookService;

    public function __construct(FacebookService $facebookService)
    {
        $this->facebookService = $facebookService;
    }

    /**
     * Specific conversation ke messages fetch karo (database se with caching)
     */
    public function getMessages($conversationId)
    {
        try {
            $conversation = Conversation::find($conversationId);

            if (!$conversation || $conversation->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found'
                ], 404);
            }

            // First try to get from database
            $messages = Message::where('conversation_id', $conversationId)
                ->orderBy('sent_at', 'desc')
                ->limit(100)
                ->get()
                ->reverse()
                ->values();

            // If no messages in DB, sync from Facebook
            if ($messages->isEmpty()) {
                $this->syncMessagesFromFacebook($conversation);
                $messages = Message::where('conversation_id', $conversationId)
                    ->orderBy('sent_at', 'desc')
                    ->limit(100)
                    ->get()
                    ->reverse()
                    ->values();
            }

            // Mark conversation as read
            $conversation->update([
                'unread_count' => 0,
                'last_read_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'data' => $messages,
                'conversation' => [
                    'id' => $conversation->id,
                    'customer_name' => $conversation->customer_name,
                    'customer_psid' => $conversation->customer_psid,
                    'customer_fb_id' => $conversation->customer_fb_id,
                    'customer_profile_pic' => $conversation->customer_profile_pic,
                    'last_message_time' => $conversation->last_message_time,
                    'is_archived' => $conversation->is_archived,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync messages from Facebook to database
     */
    private function syncMessagesFromFacebook($conversation)
    {
        $page = FacebookPage::find($conversation->page_id);

        $fbMessages = $this->facebookService->getConversationMessages(
            $conversation->conversation_id,
            $page->page_access_token,
            100
        );

        foreach ($fbMessages as $fbMsg) {
            $senderType = isset($fbMsg['from']['id']) && $fbMsg['from']['id'] === $page->page_id ? 'page' : 'customer';

            Message::updateOrCreate(
                ['message_id' => $fbMsg['id']],
                [
                    'conversation_id' => $conversation->id,
                    'message_text' => $fbMsg['message'] ?? null,
                    'sender_type' => $senderType,
                    'sender_id' => $fbMsg['from']['id'] ?? '',
                    'attachment_type' => isset($fbMsg['attachments'][0]['type']) ? $fbMsg['attachments'][0]['type'] : null,
                    'attachment_url' => isset($fbMsg['attachments'][0]['image_data']['url']) ? $fbMsg['attachments'][0]['image_data']['url'] : null,
                    'status' => 'sent',
                    'sent_at' => $fbMsg['created_time'] ?? now(),
                ]
            );
        }
    }

    /**
     * Customer ko message bhejo
     */
    public function sendMessage(Request $request, $conversationId)
    {
        try {
            // Validate - message required only if no attachment
            $request->validate([
                'message' => 'nullable|string|max:1000',
                'attachment' => 'nullable|file|max:25600', // 25MB max
                'attachment_type' => 'nullable|string|in:image,document,video,audio'
            ]);

            // At least message or attachment required
            if (!$request->message && !$request->hasFile('attachment')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Message or attachment is required'
                ], 400);
            }

            $conversation = Conversation::find($conversationId);

            if (!$conversation || $conversation->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found'
                ], 404);
            }

            $page = FacebookPage::find($conversation->page_id);

            // If customer PSID is null, try to get it from messages
            if (!$conversation->customer_psid) {
                $customerMessage = Message::where('conversation_id', $conversationId)
                    ->where('sender_type', 'customer')
                    ->first();

                if ($customerMessage && $customerMessage->sender_id) {
                    $conversation->customer_psid = $customerMessage->sender_id;
                    $conversation->save();
                }
            }

            if (!$conversation->customer_psid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer PSID not found. Please sync messages first.'
                ], 400);
            }

            // Use page token, if expired fetch fresh one from Facebook
            $token = $page->page_access_token;

            // Handle attachment upload if present
            $attachmentUrl = null;
            $attachmentType = null;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachmentType = $request->attachment_type ?? 'document';

                // Store file locally
                $path = $file->store('attachments/' . $conversation->id, 'public');

                // Generate full URL - Facebook needs publicly accessible URL
                // For local development, use APP_URL from config
                $baseUrl = rtrim(config('app.url'), '/');
                $attachmentUrl = $baseUrl . '/storage/' . $path;

                // Check if running on localhost - Facebook cannot access localhost
                $isLocalhost = str_contains($baseUrl, '127.0.0.1') || str_contains($baseUrl, 'localhost');

                if ($isLocalhost) {
                    Log::warning('Running on localhost - Facebook may not be able to access attachment URL', [
                        'url' => $attachmentUrl,
                        'suggestion' => 'Use ngrok or deploy to a public server for attachments to work'
                    ]);
                }

                Log::info('Attachment uploaded', [
                    'path' => $path,
                    'url' => $attachmentUrl,
                    'type' => $attachmentType,
                    'is_localhost' => $isLocalhost
                ]);
            }

            // First try with stored page token
            try {
                Log::info('Sending message (attempt 1 - stored page token)', [
                    'conversation_id' => $conversationId,
                    'customer_psid' => $conversation->customer_psid,
                    'page_id' => $page->id,
                    'page_name' => $page->page_name,
                    'has_attachment' => $attachmentUrl ? true : false,
                    'token_preview' => substr($token, 0, 20) . '...'
                ]);

                // Send with or without attachment
                if ($attachmentUrl && $attachmentType === 'image') {
                    $result = $this->facebookService->sendImageMessage(
                        $conversation->customer_psid,
                        $attachmentUrl,
                        $token
                    );
                } elseif ($attachmentUrl) {
                    $result = $this->facebookService->sendFileMessage(
                        $conversation->customer_psid,
                        $attachmentUrl,
                        $token
                    );
                } else {
                    $result = $this->facebookService->sendMessage(
                        $conversation->customer_psid,
                        $request->message,
                        $token
                    );
                }
            } catch (\Exception $e) {
                // If token expired, fetch fresh page token from Facebook
                Log::warning('Stored token failed, fetching fresh page token', [
                    'error' => $e->getMessage()
                ]);

                // Fetch all pages to get fresh token
                $fbPages = $this->facebookService->getAllPages();
                $freshToken = null;

                foreach ($fbPages as $fbPage) {
                    if ($fbPage['id'] === $page->page_id) {
                        $freshToken = $fbPage['access_token'];
                        // Update token in database
                        $page->page_access_token = $freshToken;
                        $page->save();
                        break;
                    }
                }

                if (!$freshToken) {
                    throw new \Exception('Could not fetch fresh page token');
                }

                Log::info('Sending message (attempt 2 - fresh page token)', [
                    'conversation_id' => $conversationId,
                    'customer_psid' => $conversation->customer_psid,
                    'token_preview' => substr($freshToken, 0, 20) . '...'
                ]);

                // Send with or without attachment
                if ($attachmentUrl && $attachmentType === 'image') {
                    $result = $this->facebookService->sendImageMessage(
                        $conversation->customer_psid,
                        $attachmentUrl,
                        $freshToken
                    );
                } elseif ($attachmentUrl) {
                    $result = $this->facebookService->sendFileMessage(
                        $conversation->customer_psid,
                        $attachmentUrl,
                        $freshToken
                    );
                } else {
                    $result = $this->facebookService->sendMessage(
                        $conversation->customer_psid,
                        $request->message,
                        $freshToken
                    );
                }
            }

            if (!isset($result['message_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send message'
                ], 400);
            }

            // Clean message text (remove placeholder space for file-only sends)
            $messageText = $request->message;
            if ($messageText && trim($messageText) === '') {
                $messageText = null;
            }

            // Save message to database
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'message_id' => $result['message_id'],
                'message_text' => $messageText,
                'sender_type' => 'page',
                'sender_id' => $page->page_id,
                'attachment_type' => $attachmentType,
                'attachment_url' => $attachmentUrl,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            // Update last message preview
            $previewText = $messageText ?? ($attachmentType === 'image' ? '📷 Image' : '📎 Attachment');
            $conversation->update([
                'last_message_preview' => $previewText,
                'last_message_time' => now()
            ]);

            // Broadcast the message via WebSocket
            broadcast(new MessageSent($message, $conversation->id));

            return response()->json([
                'success' => true,
                'message' => 'Message sent',
                'data' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync messages from Facebook to database for a conversation
     */
    public function syncMessages($conversationId)
    {
        try {
            $conversation = Conversation::find($conversationId);

            if (!$conversation || $conversation->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found'
                ], 404);
            }

            Log::info('Starting message sync', [
                'conversation_id' => $conversationId,
                'page_id' => $conversation->page_id
            ]);

            // Count messages before sync
            $messagesBefore = Message::where('conversation_id', $conversationId)->count();

            // Sync messages from Facebook
            $this->syncMessagesFromFacebook($conversation);

            // Count messages after sync
            $messagesAfter = Message::where('conversation_id', $conversationId)->count();
            $newMessages = $messagesAfter - $messagesBefore;

            Log::info('Message sync completed', [
                'conversation_id' => $conversationId,
                'messages_before' => $messagesBefore,
                'messages_after' => $messagesAfter,
                'new_messages' => $newMessages
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Messages synced successfully',
                'new_messages' => $newMessages,
                'total_messages' => $messagesAfter
            ]);

        } catch (\Exception $e) {
            Log::error('Message sync error', [
                'conversation_id' => $conversationId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}