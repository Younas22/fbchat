<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Facebook Configuration Group
            [
                'key' => 'FACEBOOK_APP_ID',
                'value' => env('FACEBOOK_APP_ID'),
                'type' => 'text',
                'group' => 'facebook',
                'label' => 'Facebook App ID',
                'description' => 'Your Facebook App ID from the Developer Console',
                'is_encrypted' => false,
            ],
            [
                'key' => 'FACEBOOK_APP_SECRET',
                'value' => env('FACEBOOK_APP_SECRET'),
                'type' => 'secret',
                'group' => 'facebook',
                'label' => 'Facebook App Secret',
                'description' => 'Your Facebook App Secret - keep this confidential',
                'is_encrypted' => true,
            ],
            [
                'key' => 'FACEBOOK_GRAPH_API_VERSION',
                'value' => env('FACEBOOK_GRAPH_API_VERSION', 'v19.0'),
                'type' => 'text',
                'group' => 'facebook',
                'label' => 'Graph API Version',
                'description' => 'Facebook Graph API version (e.g., v19.0, v20.0)',
                'is_encrypted' => false,
            ],
            [
                'key' => 'FACEBOOK_BUSINESS_ACCOUNT_TOKEN',
                'value' => env('FACEBOOK_BUSINESS_ACCOUNT_TOKEN'),
                'type' => 'token',
                'group' => 'facebook',
                'label' => 'Business Account Token',
                'description' => 'Your Facebook User Access Token with business permissions',
                'is_encrypted' => true,
            ],
            [
                'key' => 'FACEBOOK_WEBHOOK_VERIFY_TOKEN',
                'value' => env('FACEBOOK_WEBHOOK_VERIFY_TOKEN', 'facebook_chat_manager_verify_token'),
                'type' => 'token',
                'group' => 'facebook',
                'label' => 'Webhook Verify Token',
                'description' => 'Custom string used to verify webhook subscription',
                'is_encrypted' => true,
            ],

            // Webhook/URL Configuration Group
            [
                'key' => 'APP_URL',
                'value' => env('APP_URL', 'http://localhost'),
                'type' => 'url',
                'group' => 'webhook',
                'label' => 'Application Base URL',
                'description' => 'Your application\'s public URL (without trailing slash)',
                'is_encrypted' => false,
            ],
        ];

        foreach ($settings as $settingData) {
            $existingSetting = Setting::where('key', $settingData['key'])->first();

            if ($existingSetting) {
                // Update existing setting but preserve the value if it already has one
                $existingSetting->update([
                    'type' => $settingData['type'],
                    'group' => $settingData['group'],
                    'label' => $settingData['label'],
                    'description' => $settingData['description'],
                    'is_encrypted' => $settingData['is_encrypted'],
                ]);
            } else {
                // Create new setting
                $setting = new Setting();
                $setting->key = $settingData['key'];
                $setting->type = $settingData['type'];
                $setting->group = $settingData['group'];
                $setting->label = $settingData['label'];
                $setting->description = $settingData['description'];
                $setting->is_encrypted = $settingData['is_encrypted'];

                // Handle encryption for value
                if ($settingData['is_encrypted'] && $settingData['value']) {
                    $setting->forceFill(['value' => Crypt::encryptString($settingData['value'])]);
                } else {
                    $setting->forceFill(['value' => $settingData['value']]);
                }

                $setting->save();
            }
        }

        $this->command->info('Settings seeded successfully!');
    }
}
