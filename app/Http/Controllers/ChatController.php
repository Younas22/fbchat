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
use Illuminate\Support\Facades\Storage;

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

            // Extract attachment data - Facebook uses attachments.data[] structure
            $attachmentType = null;
            $attachmentUrl = null;

            if (isset($fbMsg['attachments']['data'][0])) {
                $attachment = $fbMsg['attachments']['data'][0];
                $attachmentType = $attachment['type'] ?? null;

                // Get Facebook CDN URL based on attachment type
                $fbAttachmentUrl = null;
                if (isset($attachment['image_data']['url'])) {
                    $fbAttachmentUrl = $attachment['image_data']['url'];
                } elseif (isset($attachment['video_data']['url'])) {
                    $fbAttachmentUrl = $attachment['video_data']['url'];
                } elseif (isset($attachment['audio_data']['url'])) {
                    $fbAttachmentUrl = $attachment['audio_data']['url'];
                } elseif (isset($attachment['file_url'])) {
                    $fbAttachmentUrl = $attachment['file_url'];
                }

                // Download and store attachment locally
                if ($fbAttachmentUrl) {
                    $attachmentUrl = $this->downloadAndStoreAttachment(
                        $fbAttachmentUrl,
                        $conversation->id,
                        $fbMsg['id'],
                        $attachmentType
                    );
                }
            }

            Message::updateOrCreate(
                ['message_id' => $fbMsg['id']],
                [
                    'conversation_id' => $conversation->id,
                    'message_text' => $fbMsg['message'] ?? null,
                    'sender_type' => $senderType,
                    'sender_id' => $fbMsg['from']['id'] ?? '',
                    'attachment_type' => $attachmentType,
                    'attachment_url' => $attachmentUrl,
                    'status' => 'sent',
                    'sent_at' => $fbMsg['created_time'] ?? now(),
                ]
            );
        }
    }

    /**
     * Download attachment from Facebook CDN and store locally
     */
    private function downloadAndStoreAttachment($fbUrl, $conversationId, $messageId, $attachmentType)
    {
        try {
            // Download file from Facebook CDN
            $ch = curl_init($fbUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            $fileContent = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            curl_close($ch);

            if ($httpCode !== 200 || empty($fileContent)) {
                Log::warning('Failed to download attachment from Facebook', [
                    'url' => substr($fbUrl, 0, 100) . '...',
                    'http_code' => $httpCode
                ]);
                return $fbUrl; // Fallback to Facebook URL
            }

            // Determine file extension from content type or URL
            $extension = $this->getExtensionFromContentType($contentType, $fbUrl, $attachmentType);

            // Generate unique filename
            $filename = 'fb_' . $messageId . '_' . time() . '.' . $extension;
            $path = 'attachments/' . $conversationId . '/' . $filename;

            // Store file
            Storage::disk('public')->put($path, $fileContent);

            // Generate local URL
            $baseUrl = rtrim(config('app.url'), '/');
            $localUrl = $baseUrl . '/files/' . $path;

            Log::info('Attachment downloaded and stored', [
                'message_id' => $messageId,
                'path' => $path,
                'type' => $attachmentType
            ]);

            return $localUrl;

        } catch (\Exception $e) {
            Log::error('Error downloading attachment', [
                'error' => $e->getMessage(),
                'url' => substr($fbUrl, 0, 100) . '...'
            ]);
            return $fbUrl; // Fallback to Facebook URL on error
        }
    }

    /**
     * Get file extension from content type or URL
     */
    private function getExtensionFromContentType($contentType, $url, $attachmentType)
    {
        // Map content types to extensions
        $mimeMap = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'video/mp4' => 'mp4',
            'video/quicktime' => 'mov',
            'audio/mpeg' => 'mp3',
            'audio/mp4' => 'm4a',
            'audio/ogg' => 'ogg',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        ];

        // Extract mime type (remove charset etc)
        $mime = explode(';', $contentType)[0];
        $mime = trim($mime);

        if (isset($mimeMap[$mime])) {
            return $mimeMap[$mime];
        }

        // Try to get from URL
        $urlPath = parse_url($url, PHP_URL_PATH);
        if ($urlPath) {
            $ext = pathinfo($urlPath, PATHINFO_EXTENSION);
            if ($ext && strlen($ext) <= 5) {
                return $ext;
            }
        }

        // Default based on attachment type
        $defaults = [
            'image' => 'jpg',
            'video' => 'mp4',
            'audio' => 'mp3',
            'file' => 'bin',
        ];

        return $defaults[$attachmentType] ?? 'bin';
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
            $localFilePath = null;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachmentType = $request->attachment_type ?? 'document';

                // Store file locally
                $path = $file->store('attachments/' . $conversation->id, 'public');

                // Get the full local file path for direct upload to Facebook
                $localFilePath = storage_path('app/public/' . $path);

                // Generate full URL for database storage (use /files/ route to bypass symlink 403)
                $baseUrl = rtrim(config('app.url'), '/');
                $attachmentUrl = $baseUrl . '/files/' . $path;

                Log::info('Attachment uploaded', [
                    'path' => $path,
                    'local_path' => $localFilePath,
                    'url' => $attachmentUrl,
                    'type' => $attachmentType
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
                    $result = $this->facebookService->sendAttachment(
                        $conversation->customer_psid,
                        'image',
                        $attachmentUrl,
                        $token,
                        $localFilePath
                    );
                } elseif ($attachmentUrl) {
                    $result = $this->facebookService->sendAttachment(
                        $conversation->customer_psid,
                        'file',
                        $attachmentUrl,
                        $token,
                        $localFilePath
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
                    $result = $this->facebookService->sendAttachment(
                        $conversation->customer_psid,
                        'image',
                        $attachmentUrl,
                        $freshToken,
                        $localFilePath
                    );
                } elseif ($attachmentUrl) {
                    $result = $this->facebookService->sendAttachment(
                        $conversation->customer_psid,
                        'file',
                        $attachmentUrl,
                        $freshToken,
                        $localFilePath
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