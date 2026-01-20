<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExchangeFacebookToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange:facebook-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exchange short-lived Facebook User Access Token for long-lived token (60 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=================================================');
        $this->info('Facebook Token Exchange');
        $this->info('=================================================');
        $this->newLine();

        // Get current token from .env
        $currentToken = config('services.facebook.business_token');

        if (!$currentToken) {
            $this->error('Error: FACEBOOK_BUSINESS_ACCOUNT_TOKEN not found in .env file');
            return 1;
        }

        $this->info('Current token (first 30 chars): ' . substr($currentToken, 0, 30) . '...');
        $this->newLine();

        // Confirm before proceeding
        if (!$this->confirm('Do you want to exchange this token for a long-lived token?', true)) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->info('Exchanging token with Facebook...');
        $this->newLine();

        try {
            $facebookService = app(\App\Services\FacebookService::class);
            $longLivedToken = $facebookService->getLongLivedToken($currentToken);

            if (!$longLivedToken) {
                $this->error('Failed to exchange token. Please check your Facebook App credentials.');
                $this->error('Make sure FACEBOOK_APP_ID and FACEBOOK_APP_SECRET are correct in .env');
                return 1;
            }

            $this->newLine();
            $this->info('✓ Success! Long-lived token generated (valid for 60 days)');
            $this->newLine();
            $this->line('─────────────────────────────────────────────────');
            $this->line('Your new long-lived token:');
            $this->line('─────────────────────────────────────────────────');
            $this->line($longLivedToken);
            $this->line('─────────────────────────────────────────────────');
            $this->newLine();

            $this->warn('IMPORTANT: Copy the token above and update your .env file:');
            $this->warn('FACEBOOK_BUSINESS_ACCOUNT_TOKEN=' . $longLivedToken);
            $this->newLine();

            $this->info('After updating .env, run these commands:');
            $this->line('  php artisan config:clear');
            $this->line('  php artisan cache:clear');
            $this->newLine();

            $this->info('Then reconnect your Facebook Pages to get fresh long-lived Page Access Tokens.');

            return 0;

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->newLine();
            $this->info('Troubleshooting:');
            $this->line('1. Make sure your current token is valid (not expired)');
            $this->line('2. Verify FACEBOOK_APP_ID and FACEBOOK_APP_SECRET in .env');
            $this->line('3. Check that your internet connection is working');
            return 1;
        }
    }
}
