<?php

namespace App\Services;

use Facebook\Facebook;
use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Support\Facades\Log;

class FacebookService
{
    protected $fb;
    protected $accessToken;

    public function __construct()
    {
        // Use custom cURL client with SSL verification disabled for local dev
        $customClient = new CustomFacebookCurlClient();

        // Get settings from SettingsService with fallback to config
        $appId = SettingsService::get('FACEBOOK_APP_ID', config('services.facebook.app_id'));
        $appSecret = SettingsService::get('FACEBOOK_APP_SECRET', config('services.facebook.app_secret'));
        $graphVersion = SettingsService::get('FACEBOOK_GRAPH_API_VERSION', config('services.facebook.graph_version'));

        $this->fb = new Facebook([
            'app_id' => $appId,
            'app_secret' => $appSecret,
            'default_graph_version' => $graphVersion,
            'http_client_handler' => $customClient
        ]);

        $this->accessToken = SettingsService::get('FACEBOOK_BUSINESS_ACCOUNT_TOKEN', config('services.facebook.business_token'));
    }

    /**
     * Exchange short-lived token for long-lived token (60 days)
     */
    public function getLongLivedToken($shortLivedToken)
    {
        try {
            // Get settings from SettingsService with fallback to config
            $graphVersion = SettingsService::get('FACEBOOK_GRAPH_API_VERSION', config('services.facebook.graph_version'));
            $appId = SettingsService::get('FACEBOOK_APP_ID', config('services.facebook.app_id'));
            $appSecret = SettingsService::get('FACEBOOK_APP_SECRET', config('services.facebook.app_secret'));

            // Use direct cURL instead of Facebook SDK for token exchange
            $url = 'https://graph.facebook.com/' . $graphVersion . '/oauth/access_token?' . http_build_query([
                'grant_type' => 'fb_exchange_token',
                'client_id' => $appId,
                'client_secret' => $appSecret,
                'fb_exchange_token' => $shortLivedToken
            ]);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                Log::error('Long-lived token exchange HTTP error', [
                    'http_code' => $httpCode,
                    'response' => $response
                ]);
                return null;
            }

            $data = json_decode($response, true);

            if (isset($data['error'])) {
                Log::error('Long-lived token exchange Facebook error', [
                    'error' => $data['error']
                ]);
                return null;
            }

            return $data['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error('Long-lived token exchange error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Sab pages fetch karo business account ke
     */
    public function getAllPages()
    {
        try {
            // Don't use cache for now to avoid stale timeout errors
            $response = $this->fb->get(
                '/me/accounts?fields=id,name,access_token,picture{url}',
                $this->accessToken
            );

            $pages = $response->getDecodedBody();
            return $pages['data'] ?? [];

        } catch (FacebookSDKException $e) {
            $errorMessage = $e->getMessage();
            Log::error('Facebook Pages Fetch Error: ' . $errorMessage);

            // Provide more specific error messages
            if (str_contains($errorMessage, 'timed out') || str_contains($errorMessage, 'Resolving')) {
                throw new \Exception('Connection timeout. Please check your internet connection and try again.');
            } elseif (str_contains($errorMessage, 'Invalid OAuth')) {
                throw new \Exception('Invalid access token. Please check your Facebook App credentials.');
            } elseif (str_contains($errorMessage, 'Could not resolve host')) {
                throw new \Exception('Cannot connect to Facebook. Please check your internet connection.');
            }

            throw new \Exception('Unable to fetch Facebook pages: ' . $errorMessage);
        }
    }

    /**
     * Specific page ke conversations fetch karo
     */
    public function getPageConversations($pageAccessToken, $limit = 25)
    {
        try {
            $response = $this->fb->get(
                '/me/conversations?fields=id,senders{name,id,picture},subject,updated_time,former_participants,messages.limit(1){message,created_time,from}&limit=' . $limit,
                $pageAccessToken
            );

            return $response->getDecodedBody()['data'] ?? [];
        } catch (FacebookSDKException $e) {
            Log::error('Facebook Conversations Fetch Error: ' . $e->getMessage());
            throw new \Exception('Unable to fetch conversations. Please try again.');
        }
    }

    /**
     * Specific conversation ke messages fetch karo
     */
    public function getConversationMessages($conversationId, $pageAccessToken, $limit = 50)
    {
        try {
            $response = $this->fb->get(
                '/' . $conversationId . '/messages?fields=id,message,created_time,from,to,sticker,attachments&limit=' . $limit,
                $pageAccessToken
            );

            return $response->getDecodedBody()['data'] ?? [];
        } catch (FacebookSDKException $e) {
            Log::error('Facebook Messages Fetch Error: ' . $e->getMessage());
            throw new \Exception('Unable to fetch messages. Please try again.');
        }
    }

    /**
     * Customer ka profile info
     */
    public function getCustomerProfile($customerId, $pageAccessToken)
    {
        try {
            $response = $this->fb->get(
                '/' . $customerId . '?fields=name,picture{url},email',
                $pageAccessToken
            );

            $data = $response->getDecodedBody();

            // Extract the profile picture URL from nested structure
            if (isset($data['picture']['data']['url'])) {
                $data['profile_pic'] = $data['picture']['data']['url'];
            }

            return $data;
        } catch (FacebookSDKException $e) {
            Log::error('Facebook Customer Profile Error: ' . $e->getMessage());
            throw new \Exception('Unable to fetch customer profile. Please try again.');
        }
    }

    /**
     * Extract real Facebook ID from profile picture redirect URL
     * Profile pic URL: graph.facebook.com/{PSID}/picture redirects to a URL containing real FB ID
     */
    public function extractRealFbId($psid, $pageAccessToken)
    {
        try {
            $url = "https://graph.facebook.com/{$psid}/picture?redirect=false&access_token={$pageAccessToken}";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($response, true);

            if (isset($data['data']['url'])) {
                $picUrl = $data['data']['url'];

                // Try to extract FB ID from scontent URL
                // Pattern: /p{number}x{number}/{fb_id}_
                if (preg_match('/\/p\d+x\d+\/(\d+)_/', $picUrl, $matches)) {
                    return $matches[1];
                }

                // Alternative pattern: /{fb_id}_{timestamp}
                if (preg_match('/\/(\d{10,})_\d+_/', $picUrl, $matches)) {
                    return $matches[1];
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::warning('Could not extract FB ID from profile picture', [
                'psid' => $psid,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Message bhejo conversation mein
     */
    public function sendMessage($recipientId, $message, $pageAccessToken)
    {
        try {
            // Use direct Graph API URL for sending messages
            $graphVersion = SettingsService::get('FACEBOOK_GRAPH_API_VERSION', config('services.facebook.graph_version'));
            $url = 'https://graph.facebook.com/' . $graphVersion . '/me/messages';

            $data = [
                'messaging_type' => 'RESPONSE',
                'recipient' => ['id' => $recipientId],
                'message' => ['text' => $message],
                'access_token' => $pageAccessToken
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $result = json_decode($response, true);

            if ($httpCode !== 200 || isset($result['error'])) {
                $errorMsg = isset($result['error']) ? $result['error']['message'] : 'Unknown error';
                Log::error('Facebook Send Message Error', [
                    'http_code' => $httpCode,
                    'error' => $errorMsg,
                    'response' => $response
                ]);
                throw new \Exception($errorMsg);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Facebook Send Message Error: ' . $e->getMessage());
            throw new \Exception('Unable to send message. Please try again.');
        }
    }

    /**
     * Send message with attachment (image, file, etc.)
     * Now uploads file directly to Facebook instead of providing URL
     */
    public function sendAttachment($recipientId, $attachmentType, $attachmentUrl, $pageAccessToken, $localFilePath = null)
    {
        try {
            $graphVersion = SettingsService::get('FACEBOOK_GRAPH_API_VERSION', config('services.facebook.graph_version'));
            $url = 'https://graph.facebook.com/' . $graphVersion . '/me/messages';

            // If we have a local file path, upload directly to Facebook
            if ($localFilePath && file_exists($localFilePath)) {
                return $this->sendAttachmentByUpload($recipientId, $attachmentType, $localFilePath, $pageAccessToken);
            }

            // Fallback: Try URL method first
            $data = [
                'messaging_type' => 'RESPONSE',
                'recipient' => ['id' => $recipientId],
                'message' => [
                    'attachment' => [
                        'type' => $attachmentType,
                        'payload' => [
                            'url' => $attachmentUrl,
                            'is_reusable' => true
                        ]
                    ]
                ],
                'access_token' => $pageAccessToken
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $result = json_decode($response, true);

            if ($httpCode !== 200 || isset($result['error'])) {
                $errorMsg = isset($result['error']) ? $result['error']['message'] : 'Unknown error';
                Log::error('Facebook Send Attachment Error', [
                    'http_code' => $httpCode,
                    'error' => $errorMsg,
                    'response' => $response
                ]);
                throw new \Exception($errorMsg);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Facebook Send Attachment Error: ' . $e->getMessage());

            // Check if URL is localhost (Facebook cannot access)
            if (str_contains($attachmentUrl, '127.0.0.1') || str_contains($attachmentUrl, 'localhost')) {
                throw new \Exception('Cannot send attachment from localhost. Facebook requires a publicly accessible URL. Use ngrok or deploy to a public server.');
            }

            throw new \Exception('Unable to send attachment. Please try again.');
        }
    }

    /**
     * Upload attachment directly to Facebook (bypasses robots.txt issues)
     */
    public function sendAttachmentByUpload($recipientId, $attachmentType, $localFilePath, $pageAccessToken)
    {
        try {
            $graphVersion = SettingsService::get('FACEBOOK_GRAPH_API_VERSION', config('services.facebook.graph_version'));
            $url = 'https://graph.facebook.com/' . $graphVersion . '/me/messages';

            // Get file info
            $filename = basename($localFilePath);
            $mimeType = mime_content_type($localFilePath) ?: 'application/octet-stream';

            // Build multipart form data
            $messageData = json_encode([
                'attachment' => [
                    'type' => $attachmentType,
                    'payload' => [
                        'is_reusable' => true
                    ]
                ]
            ]);

            $postFields = [
                'recipient' => json_encode(['id' => $recipientId]),
                'messaging_type' => 'RESPONSE',
                'message' => $messageData,
                'filedata' => new \CURLFile($localFilePath, $mimeType, $filename),
                'access_token' => $pageAccessToken
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 120); // Longer timeout for file upload

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                Log::error('Facebook Upload cURL Error', ['error' => $curlError]);
                throw new \Exception('Upload failed: ' . $curlError);
            }

            $result = json_decode($response, true);

            if ($httpCode !== 200 || isset($result['error'])) {
                $errorMsg = isset($result['error']) ? $result['error']['message'] : 'Unknown error';
                Log::error('Facebook Direct Upload Error', [
                    'http_code' => $httpCode,
                    'error' => $errorMsg,
                    'response' => $response
                ]);
                throw new \Exception($errorMsg);
            }

            Log::info('Facebook attachment uploaded successfully', [
                'recipient' => $recipientId,
                'file' => $filename,
                'message_id' => $result['message_id'] ?? null
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Facebook Direct Upload Error: ' . $e->getMessage());
            throw new \Exception('Unable to upload attachment. Please try again.');
        }
    }

    /**
     * Send image message
     */
    public function sendImageMessage($recipientId, $imageUrl, $pageAccessToken)
    {
        return $this->sendAttachment($recipientId, 'image', $imageUrl, $pageAccessToken);
    }

    /**
     * Send file/document message
     */
    public function sendFileMessage($recipientId, $fileUrl, $pageAccessToken)
    {
        return $this->sendAttachment($recipientId, 'file', $fileUrl, $pageAccessToken);
    }

    /**
     * Send video message
     */
    public function sendVideoMessage($recipientId, $videoUrl, $pageAccessToken)
    {
        return $this->sendAttachment($recipientId, 'video', $videoUrl, $pageAccessToken);
    }

    /**
     * Send audio message
     */
    public function sendAudioMessage($recipientId, $audioUrl, $pageAccessToken)
    {
        return $this->sendAttachment($recipientId, 'audio', $audioUrl, $pageAccessToken);
    }
}