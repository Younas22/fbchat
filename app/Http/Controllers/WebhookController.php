<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\FacebookPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WebhookController extends Controller
{
    /**
     * Verify webhook (GET request from Facebook)
     */
    public function verify(Request $request)
    {
        $verifyToken = config('services.facebook.verify_token', 'facebook_chat_manager_verify_token');

        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            Log::info('Facebook Webhook Verified');
            return response($challenge, 200);
        }

        return response('Forbidden', 403);
    }

    /**
     * Handle webhook callbacks (POST request from Facebook)
     */
    public function handle(Request $request)
    {
        $data = $request->all();

        Log::info('Facebook Webhook Received', ['data' => $data]);

        if (isset($data['object']) && $data['object'] === 'page') {
            foreach ($data['entry'] as $entry) {
                if (isset($entry['messaging'])) {
                    foreach ($entry['messaging'] as $event) {
                        $this->processMessagingEvent($event, $entry['id']);
                    }
                }
            }
        }

        return response('EVENT_RECEIVED', 200);
    }

    /**
     * Process individual messaging event
     */
    private function processMessagingEvent($event, $pageId)
    {
        // Find the Facebook page
        $page = FacebookPage::where('page_id', $pageId)->first();

        if (!$page) {
            Log::warning('Page not found for webhook event', ['page_id' => $pageId]);
            return;
        }

        // Handle different event types
        if (isset($event['message'])) {
            $this->handleMessage($event, $page);
        }

        if (isset($event['delivery'])) {
            $this->handleDelivery($event, $page);
        }

        if (isset($event['read'])) {
            $this->handleRead($event, $page);
        }
    }

    /**
     * Handle incoming message
     */
    private function handleMessage($event, $page)
    {
        $senderId = $event['sender']['id'];
        $recipientId = $event['recipient']['id'];
        $messageData = $event['message'];
        $timestamp = $event['timestamp'];

        // Determine sender type
        $senderType = ($senderId === $page->page_id) ? 'page' : 'customer';

        // Find or create conversation
        $conversation = Conversation::firstOrCreate(
            [
                'page_id' => $page->id,
                'customer_psid' => $senderId === $page->page_id ? $recipientId : $senderId,
            ],
            [
                'user_id' => $page->user_id,
                'conversation_id' => $senderId . '_' . $recipientId,
                'customer_name' => 'Customer',
                'last_message_time' => now(),
                'is_archived' => false,
            ]
        );

        // Store message text
        $messageText = $messageData['text'] ?? null;
        $attachmentType = null;
        $attachmentUrl = null;

        // Check for attachments
        if (isset($messageData['attachments'])) {
            $attachment = $messageData['attachments'][0];
            $attachmentType = $attachment['type'];
            $fbAttachmentUrl = $attachment['payload']['url'] ?? null;

            // Download and store attachment locally
            if ($fbAttachmentUrl) {
                $attachmentUrl = $this->downloadAndStoreAttachment(
                    $fbAttachmentUrl,
                    $conversation->id,
                    $messageData['mid'],
                    $attachmentType
                );
            }
        }

        // Save message to database
        $message = Message::updateOrCreate(
            ['message_id' => $messageData['mid']],
            [
                'conversation_id' => $conversation->id,
                'message_text' => $messageText,
                'sender_type' => $senderType,
                'sender_id' => $senderId,
                'attachment_type' => $attachmentType,
                'attachment_url' => $attachmentUrl,
                'status' => 'sent',
                'sent_at' => date('Y-m-d H:i:s', $timestamp / 1000),
            ]
        );

        // Update conversation
        $conversation->update([
            'last_message_preview' => $messageText ?? '[Attachment]',
            'last_message_time' => date('Y-m-d H:i:s', $timestamp / 1000),
        ]);

        // Increment unread count if message from customer
        if ($senderType === 'customer') {
            $conversation->increment('unread_count');
        }

        // Broadcast message via WebSocket for real-time updates
        broadcast(new MessageSent($message, $conversation->id));

        Log::info('Message saved from webhook', [
            'conversation_id' => $conversation->id,
            'message_id' => $messageData['mid']
        ]);
    }

    /**
     * Handle delivery receipt
     */
    private function handleDelivery($event, $page)
    {
        $messageIds = $event['delivery']['mids'] ?? [];

        foreach ($messageIds as $mid) {
            Message::where('message_id', $mid)->update(['status' => 'delivered']);
        }

        Log::info('Messages marked as delivered', ['message_ids' => $messageIds]);
    }

    /**
     * Handle read receipt
     */
    private function handleRead($event, $page)
    {
        $watermark = $event['read']['watermark'];
        $senderId = $event['sender']['id'];

        // Find conversation
        $conversation = Conversation::where('page_id', $page->id)
            ->where('customer_psid', $senderId)
            ->first();

        if ($conversation) {
            // Mark messages as read up to watermark
            Message::where('conversation_id', $conversation->id)
                ->where('sent_at', '<=', date('Y-m-d H:i:s', $watermark / 1000))
                ->update(['status' => 'read']);

            // Reset unread count
            $conversation->update([
                'unread_count' => 0,
                'last_read_at' => now()
            ]);

            Log::info('Messages marked as read', [
                'conversation_id' => $conversation->id,
                'watermark' => $watermark
            ]);
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
                Log::warning('Failed to download attachment from Facebook webhook', [
                    'url' => substr($fbUrl, 0, 100) . '...',
                    'http_code' => $httpCode
                ]);
                return $fbUrl; // Fallback to Facebook URL
            }

            // Determine file extension from content type
            $extension = $this->getExtensionFromContentType($contentType, $fbUrl, $attachmentType);

            // Generate unique filename
            $filename = 'fb_' . md5($messageId) . '_' . time() . '.' . $extension;
            $path = 'attachments/' . $conversationId . '/' . $filename;

            // Store file
            Storage::disk('public')->put($path, $fileContent);

            // Generate local URL
            $baseUrl = rtrim(config('app.url'), '/');
            $localUrl = $baseUrl . '/files/' . $path;

            Log::info('Webhook attachment downloaded and stored', [
                'message_id' => $messageId,
                'path' => $path,
                'type' => $attachmentType
            ]);

            return $localUrl;

        } catch (\Exception $e) {
            Log::error('Error downloading webhook attachment', [
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
        ];

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
}
