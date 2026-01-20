<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DebugFacebookToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:facebook-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug Facebook token validity and permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=================================================');
        $this->info('Facebook Token Debugger');
        $this->info('=================================================');
        $this->newLine();

        // Get token from .env
        $token = config('services.facebook.business_token');

        if (!$token) {
            $this->error('FACEBOOK_BUSINESS_ACCOUNT_TOKEN not found in .env');
            return 1;
        }

        $this->info('Token (first 50 chars): ' . substr($token, 0, 50) . '...');
        $this->newLine();

        // Test 1: Check if token is valid
        $this->info('Test 1: Validating token...');
        try {
            $url = 'https://graph.facebook.com/v19.0/me?access_token=' . urlencode($token);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $data = json_decode($response, true);

            if ($httpCode === 200 && !isset($data['error'])) {
                $this->info('✓ Token is VALID');
                $this->line('  User ID: ' . ($data['id'] ?? 'N/A'));
                $this->line('  User Name: ' . ($data['name'] ?? 'N/A'));
            } else {
                $this->error('✗ Token is INVALID or EXPIRED');
                if (isset($data['error'])) {
                    $this->error('  Error: ' . ($data['error']['message'] ?? 'Unknown error'));
                    $this->error('  Type: ' . ($data['error']['type'] ?? 'N/A'));
                    $this->error('  Code: ' . ($data['error']['code'] ?? 'N/A'));
                }
                $this->newLine();
                $this->warn('SOLUTION: Generate a fresh User Access Token from:');
                $this->line('https://developers.facebook.com/tools/explorer/');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('✗ Failed to validate token: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();

        // Test 2: Check token debug info (expiry, type)
        $this->info('Test 2: Getting token debug info...');
        try {
            $debugUrl = 'https://graph.facebook.com/v19.0/debug_token?input_token=' . urlencode($token) . '&access_token=' . urlencode($token);

            $ch = curl_init($debugUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($response, true);

            if (isset($data['data'])) {
                $tokenData = $data['data'];

                $this->line('  Token Type: ' . ($tokenData['type'] ?? 'N/A'));
                $this->line('  App ID: ' . ($tokenData['app_id'] ?? 'N/A'));
                $this->line('  Valid: ' . ($tokenData['is_valid'] ? 'Yes' : 'No'));

                if (isset($tokenData['expires_at'])) {
                    $expiryTime = date('Y-m-d H:i:s', $tokenData['expires_at']);
                    $this->line('  Expires At: ' . $expiryTime);

                    $now = time();
                    $secondsLeft = $tokenData['expires_at'] - $now;
                    if ($secondsLeft > 0) {
                        $hoursLeft = floor($secondsLeft / 3600);
                        $daysLeft = floor($hoursLeft / 24);
                        $this->info('  ✓ Time Remaining: ' . $daysLeft . ' days, ' . ($hoursLeft % 24) . ' hours');
                    } else {
                        $this->error('  ✗ Token EXPIRED');
                    }
                } else {
                    $this->info('  ✓ Token does NOT expire (long-lived)');
                }

                if (isset($tokenData['scopes'])) {
                    $this->newLine();
                    $this->line('  Permissions: ' . implode(', ', $tokenData['scopes']));
                }
            }
        } catch (\Exception $e) {
            $this->error('  Failed to get debug info: ' . $e->getMessage());
        }

        $this->newLine();

        // Test 3: Try to fetch pages
        $this->info('Test 3: Fetching pages...');
        try {
            $facebookService = app(\App\Services\FacebookService::class);
            $pages = $facebookService->getAllPages();

            if (count($pages) > 0) {
                $this->info('✓ Successfully fetched ' . count($pages) . ' page(s)');
                foreach ($pages as $page) {
                    $this->line('  - ' . $page['name'] . ' (ID: ' . $page['id'] . ')');
                    $this->line('    Token preview: ' . substr($page['access_token'], 0, 30) . '...');
                }
            } else {
                $this->warn('No pages found');
            }
        } catch (\Exception $e) {
            $this->error('✗ Failed to fetch pages: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('=================================================');
        $this->info('Debug complete!');
        $this->info('=================================================');

        return 0;
    }
}
