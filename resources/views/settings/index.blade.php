@extends('layouts.app')

@section('title', 'Settings - Facebook Chat Manager')

@section('page-title', 'Settings')
@section('page-subtitle', 'Manage your Facebook and webhook configuration')

@section('content')
<div class="p-4 lg:p-6 space-y-6">
    <!-- Toast Notification -->
    <div id="toast" class="hidden fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-lg transform transition-all duration-300 translate-y-[-100%] opacity-0">
        <div class="flex items-center gap-3">
            <div id="toastIcon"></div>
            <p id="toastMessage" class="font-medium"></p>
        </div>
    </div>

    <!-- Facebook Configuration Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 lg:px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.477 2 2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879V14.89h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.989C18.343 21.129 22 16.99 22 12c0-5.523-4.477-10-10-10z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Facebook Configuration</h3>
                    <p class="text-sm text-slate-500 mt-0.5">Configure your Facebook App credentials</p>
                </div>
            </div>
        </div>
        <div class="p-5 lg:p-6 space-y-5">
            <!-- App ID -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Facebook App ID</label>
                <input type="text" id="FACEBOOK_APP_ID"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl
                              focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                              transition-all duration-200 outline-none"
                       placeholder="Enter your Facebook App ID">
                <p class="text-xs text-slate-500 mt-1.5">Found in your Facebook Developer App Dashboard</p>
            </div>

            <!-- App Secret -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Facebook App Secret</label>
                <div class="relative">
                    <input type="password" id="FACEBOOK_APP_SECRET"
                           class="w-full px-4 py-3 pr-12 bg-slate-50 border border-slate-200 rounded-xl
                                  focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                                  transition-all duration-200 outline-none"
                           placeholder="Enter your Facebook App Secret">
                    <button type="button" onclick="toggleVisibility('FACEBOOK_APP_SECRET')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg class="w-5 h-5 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-slate-500 mt-1.5">Keep this secret - never share it publicly</p>
            </div>

            <!-- Graph API Version -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Graph API Version</label>
                <input type="text" id="FACEBOOK_GRAPH_API_VERSION"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl
                              focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                              transition-all duration-200 outline-none"
                       placeholder="v19.0">
                <p class="text-xs text-slate-500 mt-1.5">e.g., v19.0, v20.0 - Check Facebook docs for latest version</p>
            </div>

            <!-- Business Account Token -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Business Account Token</label>
                <div class="relative">
                    <textarea id="FACEBOOK_BUSINESS_ACCOUNT_TOKEN" rows="3"
                              class="w-full px-4 py-3 pr-12 bg-slate-50 border border-slate-200 rounded-xl
                                     focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                                     transition-all duration-200 outline-none resize-none"
                              placeholder="Enter your long-lived Business Account Token"></textarea>
                    <button type="button" onclick="toggleTextareaVisibility('FACEBOOK_BUSINESS_ACCOUNT_TOKEN')"
                            class="absolute right-3 top-3 p-1.5 text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg class="w-5 h-5 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-slate-500 mt-1.5">Your Facebook User Access Token with business permissions</p>
            </div>

            <!-- Webhook Verify Token -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Webhook Verify Token</label>
                <div class="relative">
                    <input type="password" id="FACEBOOK_WEBHOOK_VERIFY_TOKEN"
                           class="w-full px-4 py-3 pr-12 bg-slate-50 border border-slate-200 rounded-xl
                                  focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                                  transition-all duration-200 outline-none"
                           placeholder="Your custom webhook verification token">
                    <button type="button" onclick="toggleVisibility('FACEBOOK_WEBHOOK_VERIFY_TOKEN')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg class="w-5 h-5 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-slate-500 mt-1.5">Custom string used to verify webhook subscription in Facebook Developer Console</p>
            </div>
        </div>
    </div>

    <!-- Token Exchange Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 lg:px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Token Exchange</h3>
                    <p class="text-sm text-slate-500 mt-0.5">Exchange short-lived token for long-lived token (60 days)</p>
                </div>
            </div>
        </div>
        <div class="p-5 lg:p-6 space-y-4">
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-amber-800">Important</p>
                        <p class="text-sm text-amber-700 mt-1">Make sure you have saved your current Business Account Token before exchanging. The exchange will automatically update the token in settings.</p>
                    </div>
                </div>
            </div>

            <button type="button" id="exchangeTokenBtn" onclick="exchangeToken()"
                    class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-amber-500 to-orange-500
                           text-white font-semibold rounded-xl hover:from-amber-600 hover:to-orange-600
                           shadow-lg shadow-amber-500/25 hover:shadow-amber-500/40
                           transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Exchange Token</span>
            </button>

            <!-- Console Output -->
            <div id="tokenOutputContainer" class="hidden">
                <label class="block text-sm font-medium text-slate-700 mb-2">Command Output</label>
                <div class="bg-slate-900 rounded-xl p-4 overflow-x-auto">
                    <pre id="tokenOutput" class="text-sm text-green-400 font-mono whitespace-pre-wrap"></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Webhook Configuration Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 lg:px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Webhook Configuration</h3>
                    <p class="text-sm text-slate-500 mt-0.5">Configure your webhook URL for Facebook</p>
                </div>
            </div>
        </div>
        <div class="p-5 lg:p-6 space-y-5">
            <!-- Base URL -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Application Base URL</label>
                <input type="url" id="APP_URL"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl
                              focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                              transition-all duration-200 outline-none"
                       placeholder="https://your-domain.com"
                       onchange="updateWebhookUrl()">
                <p class="text-xs text-slate-500 mt-1.5">Your application's public URL (no trailing slash)</p>
            </div>

            <!-- Auto-generated Webhook URL -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Webhook Callback URL</label>
                <div class="flex gap-2">
                    <input type="text" id="webhookUrl" readonly
                           class="flex-1 px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl
                                  text-slate-600 cursor-not-allowed"
                           value="">
                    <button type="button" onclick="copyWebhookUrl()"
                            class="inline-flex items-center gap-2 px-4 py-3 bg-slate-100 border border-slate-200
                                   rounded-xl text-slate-600 hover:bg-slate-200 hover:text-slate-900
                                   transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span>Copy</span>
                    </button>
                </div>
                <p class="text-xs text-slate-500 mt-1.5">Use this URL in your Facebook Developer Console webhook settings</p>
            </div>

            <!-- Instructions -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-800">Setting up the Webhook</p>
                        <ol class="text-sm text-blue-700 mt-2 space-y-1 list-decimal list-inside">
                            <li>Go to your Facebook App in the Developer Console</li>
                            <li>Navigate to Products > Messenger > Settings</li>
                            <li>Under Webhooks, click "Edit Callback URL"</li>
                            <li>Paste the Webhook Callback URL above</li>
                            <li>Enter your Webhook Verify Token</li>
                            <li>Subscribe to: messages, messaging_postbacks, messaging_optins</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="sticky bottom-4 flex justify-end">
        <button type="button" id="saveBtn" onclick="saveSettings()"
                class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600
                       text-white font-semibold rounded-xl hover:from-blue-700 hover:to-indigo-700
                       shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40
                       transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span>Save Settings</span>
        </button>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const settingKeys = [
        'FACEBOOK_APP_ID',
        'FACEBOOK_APP_SECRET',
        'FACEBOOK_GRAPH_API_VERSION',
        'FACEBOOK_BUSINESS_ACCOUNT_TOKEN',
        'FACEBOOK_WEBHOOK_VERIFY_TOKEN',
        'APP_URL'
    ];

    // Track original values to detect changes
    let originalValues = {};
    let isTextareaHidden = true;

    // Toggle password visibility for inputs
    function toggleVisibility(inputId) {
        const input = document.getElementById(inputId);
        const button = input.parentElement.querySelector('button');
        const eyeIcon = button.querySelector('.eye-icon');
        const eyeOffIcon = button.querySelector('.eye-off-icon');

        if (input.type === 'password') {
            input.type = 'text';
            eyeIcon.classList.add('hidden');
            eyeOffIcon.classList.remove('hidden');
        } else {
            input.type = 'password';
            eyeIcon.classList.remove('hidden');
            eyeOffIcon.classList.add('hidden');
        }
    }

    // Toggle visibility for textarea (mask content)
    function toggleTextareaVisibility(textareaId) {
        const textarea = document.getElementById(textareaId);
        const button = textarea.parentElement.querySelector('button');
        const eyeIcon = button.querySelector('.eye-icon');
        const eyeOffIcon = button.querySelector('.eye-off-icon');

        if (isTextareaHidden) {
            // Show actual value
            textarea.value = textarea.dataset.actualValue || textarea.value;
            eyeIcon.classList.add('hidden');
            eyeOffIcon.classList.remove('hidden');
            isTextareaHidden = false;
        } else {
            // Store actual value and mask
            textarea.dataset.actualValue = textarea.value;
            eyeIcon.classList.remove('hidden');
            eyeOffIcon.classList.add('hidden');
            isTextareaHidden = true;
        }
    }

    // Update webhook URL based on APP_URL
    function updateWebhookUrl() {
        const appUrl = document.getElementById('APP_URL').value;
        const webhookUrl = appUrl ? appUrl.replace(/\/$/, '') + '/api/facebook/webhook' : '';
        document.getElementById('webhookUrl').value = webhookUrl;
    }

    // Copy webhook URL to clipboard
    async function copyWebhookUrl() {
        const webhookUrl = document.getElementById('webhookUrl').value;
        if (!webhookUrl) {
            showToast('Please set the Application Base URL first', 'error');
            return;
        }

        try {
            await navigator.clipboard.writeText(webhookUrl);
            showToast('Webhook URL copied to clipboard', 'success');
        } catch (err) {
            showToast('Failed to copy URL', 'error');
        }
    }

    // Show toast notification
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');
        const toastIcon = document.getElementById('toastIcon');

        // Set colors and icon based on type
        if (type === 'success') {
            toast.className = 'fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-lg transform transition-all duration-300 bg-emerald-50 border border-emerald-200';
            toastMessage.className = 'font-medium text-emerald-800';
            toastIcon.innerHTML = `<svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>`;
        } else {
            toast.className = 'fixed top-4 right-4 z-50 px-6 py-4 rounded-xl shadow-lg transform transition-all duration-300 bg-rose-50 border border-rose-200';
            toastMessage.className = 'font-medium text-rose-800';
            toastIcon.innerHTML = `<svg class="w-5 h-5 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>`;
        }

        toastMessage.textContent = message;

        // Show toast
        toast.classList.remove('hidden', 'translate-y-[-100%]', 'opacity-0');

        // Hide after 4 seconds
        setTimeout(() => {
            toast.classList.add('translate-y-[-100%]', 'opacity-0');
            setTimeout(() => toast.classList.add('hidden'), 300);
        }, 4000);
    }

    // Load settings from API
    async function loadSettings() {
        try {
            // Wait for authentication
            while (!window.ensureAuthenticated) {
                await new Promise(resolve => setTimeout(resolve, 100));
            }
            await window.ensureAuthenticated();

            const response = await axios.get(`${API_BASE}/settings`);

            if (response.data.success) {
                const { settings, webhook_url } = response.data.data;

                // Populate fields with values
                for (const group of Object.values(settings)) {
                    for (const setting of group) {
                        const input = document.getElementById(setting.key);
                        if (input) {
                            // For sensitive fields, use actual value but mask it
                            if (setting.is_encrypted || ['token', 'secret'].includes(setting.type)) {
                                input.value = setting.value || '';
                                if (input.tagName === 'TEXTAREA') {
                                    input.dataset.actualValue = setting.value || '';
                                }
                            } else {
                                input.value = setting.value || '';
                            }
                            originalValues[setting.key] = setting.value || '';
                        }
                    }
                }

                // Set webhook URL
                document.getElementById('webhookUrl').value = webhook_url;
            }
        } catch (error) {
            console.error('Error loading settings:', error);
            if (error.response?.status === 401) {
                localStorage.removeItem('token');
                window.location.href = '/';
            } else {
                showToast('Failed to load settings', 'error');
            }
        }
    }

    // Save settings
    async function saveSettings() {
        const saveBtn = document.getElementById('saveBtn');
        saveBtn.disabled = true;
        saveBtn.innerHTML = `
            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Saving...</span>
        `;

        try {
            const settings = settingKeys.map(key => {
                const input = document.getElementById(key);
                let value = input.value;

                // For textarea with hidden value, use the stored actual value
                if (input.tagName === 'TEXTAREA' && input.dataset.actualValue !== undefined && !isTextareaHidden) {
                    value = input.dataset.actualValue;
                }

                return { key, value };
            });

            const response = await axios.post(`${API_BASE}/settings`, { settings });

            if (response.data.success) {
                showToast('Settings saved successfully', 'success');
                // Update original values
                settings.forEach(s => originalValues[s.key] = s.value);
                // Update webhook URL
                if (response.data.data.webhook_url) {
                    document.getElementById('webhookUrl').value = response.data.data.webhook_url;
                }
            } else {
                showToast(response.data.message || 'Failed to save settings', 'error');
            }
        } catch (error) {
            console.error('Error saving settings:', error);
            showToast(error.response?.data?.message || 'Failed to save settings', 'error');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Save Settings</span>
            `;
        }
    }

    // Exchange token
    async function exchangeToken() {
        const exchangeBtn = document.getElementById('exchangeTokenBtn');
        const outputContainer = document.getElementById('tokenOutputContainer');
        const output = document.getElementById('tokenOutput');

        exchangeBtn.disabled = true;
        exchangeBtn.innerHTML = `
            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Exchanging...</span>
        `;

        outputContainer.classList.remove('hidden');
        output.textContent = 'Connecting to Facebook API...\n';

        try {
            const response = await axios.post(`${API_BASE}/settings/exchange-token`);

            output.textContent = response.data.output || '';

            if (response.data.success) {
                showToast('Token exchanged successfully', 'success');
                // Reload settings to get the new token
                await loadSettings();
            } else {
                showToast(response.data.message || 'Token exchange failed', 'error');
            }
        } catch (error) {
            console.error('Error exchanging token:', error);
            output.textContent = error.response?.data?.output || 'Error: ' + (error.response?.data?.message || error.message);
            output.classList.remove('text-green-400');
            output.classList.add('text-red-400');
            showToast(error.response?.data?.message || 'Token exchange failed', 'error');
        } finally {
            exchangeBtn.disabled = false;
            exchangeBtn.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>Exchange Token</span>
            `;
        }
    }

    // Load settings on page load
    loadSettings();
</script>
@endsection
