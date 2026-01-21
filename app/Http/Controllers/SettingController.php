<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    /**
     * Get all settings grouped by their group field.
     */
    public function index()
    {
        $settings = SettingsService::getAllGrouped();
        $webhookUrl = SettingsService::getWebhookUrl();

        return response()->json([
            'success' => true,
            'data' => [
                'settings' => $settings,
                'webhook_url' => $webhookUrl,
            ],
        ]);
    }

    /**
     * Bulk update settings.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $settingsData = [];
            foreach ($request->input('settings') as $setting) {
                $key = $setting['key'];
                $value = $setting['value'] ?? null;

                // Validate URL type settings
                $dbSetting = Setting::where('key', $key)->first();
                if ($dbSetting && $dbSetting->type === 'url' && $value) {
                    if (!filter_var($value, FILTER_VALIDATE_URL)) {
                        return response()->json([
                            'success' => false,
                            'message' => "Invalid URL format for {$dbSetting->label}",
                        ], 422);
                    }
                }

                $settingsData[$key] = $value;
            }

            SettingsService::bulkUpdate($settingsData);

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully',
                'data' => [
                    'settings' => SettingsService::getAllGrouped(),
                    'webhook_url' => SettingsService::getWebhookUrl(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Run the Facebook token exchange command.
     */
    public function exchangeToken(Request $request)
    {
        try {
            // Get the current business token
            $currentToken = SettingsService::get('FACEBOOK_BUSINESS_ACCOUNT_TOKEN');

            if (!$currentToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Facebook Business Account Token found. Please set one first.',
                    'output' => 'Error: FACEBOOK_BUSINESS_ACCOUNT_TOKEN not found in settings.',
                ], 400);
            }

            // Get Facebook service credentials from settings
            $appId = SettingsService::get('FACEBOOK_APP_ID');
            $appSecret = SettingsService::get('FACEBOOK_APP_SECRET');

            if (!$appId || !$appSecret) {
                return response()->json([
                    'success' => false,
                    'message' => 'Facebook App ID and App Secret are required for token exchange.',
                    'output' => 'Error: FACEBOOK_APP_ID and FACEBOOK_APP_SECRET must be configured.',
                ], 400);
            }

            // Initialize Facebook SDK directly for token exchange
            $fb = new \Facebook\Facebook([
                'app_id' => $appId,
                'app_secret' => $appSecret,
                'default_graph_version' => SettingsService::get('FACEBOOK_GRAPH_API_VERSION', 'v19.0'),
            ]);

            $output = [];
            $output[] = '=================================================';
            $output[] = 'Facebook Token Exchange';
            $output[] = '=================================================';
            $output[] = '';
            $output[] = 'Current token (first 30 chars): ' . substr($currentToken, 0, 30) . '...';
            $output[] = '';
            $output[] = 'Exchanging token with Facebook...';
            $output[] = '';

            try {
                // Exchange the short-lived token for a long-lived one
                $oAuth2Client = $fb->getOAuth2Client();
                $longLivedToken = $oAuth2Client->getLongLivedAccessToken($currentToken);

                if (!$longLivedToken) {
                    $output[] = 'Error: Failed to exchange token. The token may already be long-lived or invalid.';
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to exchange token',
                        'output' => implode("\n", $output),
                    ], 400);
                }

                $newToken = $longLivedToken->getValue();

                // Update the setting with the new token
                SettingsService::set('FACEBOOK_BUSINESS_ACCOUNT_TOKEN', $newToken);

                $output[] = '✓ Success! Long-lived token generated (valid for 60 days)';
                $output[] = '';
                $output[] = '─────────────────────────────────────────────────';
                $output[] = 'Your new long-lived token:';
                $output[] = '─────────────────────────────────────────────────';
                $output[] = $newToken;
                $output[] = '─────────────────────────────────────────────────';
                $output[] = '';
                $output[] = '✓ Token has been automatically saved to settings.';
                $output[] = '';
                $output[] = 'Note: You should reconnect your Facebook Pages to get fresh long-lived Page Access Tokens.';

                return response()->json([
                    'success' => true,
                    'message' => 'Token exchanged successfully',
                    'output' => implode("\n", $output),
                    'new_token' => $newToken,
                ]);

            } catch (\Facebook\Exceptions\FacebookResponseException $e) {
                $output[] = 'Facebook API Error: ' . $e->getMessage();
                return response()->json([
                    'success' => false,
                    'message' => 'Facebook API Error: ' . $e->getMessage(),
                    'output' => implode("\n", $output),
                ], 400);
            } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                $output[] = 'Facebook SDK Error: ' . $e->getMessage();
                return response()->json([
                    'success' => false,
                    'message' => 'Facebook SDK Error: ' . $e->getMessage(),
                    'output' => implode("\n", $output),
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'output' => 'Error: ' . $e->getMessage() . "\n\nTroubleshooting:\n1. Make sure your current token is valid (not expired)\n2. Verify FACEBOOK_APP_ID and FACEBOOK_APP_SECRET are correct\n3. Check that your internet connection is working",
            ], 500);
        }
    }
}
