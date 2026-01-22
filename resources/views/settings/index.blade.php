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

    <!-- App Branding Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 lg:px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-slate-900">App Branding</h3>
                    <p class="text-sm text-slate-500 mt-0.5">Customize your app name, logo and policy URLs</p>
                </div>
            </div>
        </div>
        <div class="p-5 lg:p-6 space-y-5">
            <!-- App Name -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Application Name</label>
                <input type="text" id="APP_NAME"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl
                              focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500
                              transition-all duration-200 outline-none"
                       placeholder="FB Chat Manager">
                <p class="text-xs text-slate-500 mt-1.5">Displayed in sidebar, topbar and login screen</p>
            </div>

            <!-- App Logo Upload -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Application Logo</label>
                <input type="hidden" id="APP_LOGO" value="">

                <!-- Logo Preview & Remove Section -->
                <div id="logoPreviewSection" class="hidden mb-3">
                    <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <img id="logoPreviewImg" src="" alt="Logo Preview" class="w-16 h-16 rounded-xl object-cover border border-slate-200">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-700">Current Logo</p>
                            <p id="logoFileName" class="text-xs text-slate-500 truncate max-w-xs"></p>
                        </div>
                        <button type="button" onclick="removeLogo()" id="removeLogoBtn"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-rose-600
                                       bg-rose-50 rounded-lg hover:bg-rose-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <span>Remove</span>
                        </button>
                    </div>
                </div>

                <!-- Drag & Drop Upload Area -->
                <div id="logoDropZone"
                     class="relative border-2 border-dashed border-slate-300 rounded-xl p-6 text-center
                            hover:border-indigo-400 hover:bg-indigo-50/50 transition-all duration-200 cursor-pointer"
                     onclick="document.getElementById('logoFileInput').click()">
                    <input type="file" id="logoFileInput" accept="image/*" class="hidden" onchange="handleLogoSelect(event)">

                    <div id="uploadIconArea">
                        <div class="w-12 h-12 mx-auto mb-3 bg-indigo-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-slate-700 mb-1">Click to upload or drag and drop</p>
                        <p class="text-xs text-slate-500">PNG, JPG, GIF, SVG or WebP (max 2MB)</p>
                    </div>

                    <!-- Upload Progress -->
                    <div id="uploadProgress" class="hidden">
                        <div class="w-12 h-12 mx-auto mb-3">
                            <svg class="w-12 h-12 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-indigo-600">Uploading...</p>
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-1.5">Recommended size: 64x64 pixels or larger square image</p>
            </div>

            <!-- Privacy Policy URL -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Privacy Policy URL</label>
                <input type="url" id="PRIVACY_POLICY_URL"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl
                              focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500
                              transition-all duration-200 outline-none"
                       placeholder="https://example.com/privacy-policy">
                <p class="text-xs text-slate-500 mt-1.5">Required for Facebook App Review</p>
            </div>

            <!-- Data Deletion URL -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">User Data Deletion URL</label>
                <input type="url" id="DATA_DELETION_URL"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl
                              focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500
                              transition-all duration-200 outline-none"
                       placeholder="https://example.com/data-deletion">
                <p class="text-xs text-slate-500 mt-1.5">Required for Facebook App Review - Link to data deletion instructions</p>
            </div>
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

    <!-- Update Password Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 lg:px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Update Password</h3>
                    <p class="text-sm text-slate-500 mt-0.5">Change your account password</p>
                </div>
            </div>
        </div>
        <div class="p-5 lg:p-6 space-y-5">
            <!-- Current Password -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Current Password</label>
                <div class="relative">
                    <input type="password" id="currentPassword"
                           class="w-full px-4 py-3 pr-12 bg-slate-50 border border-slate-200 rounded-xl
                                  focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500
                                  transition-all duration-200 outline-none"
                           placeholder="Enter your current password">
                    <button type="button" onclick="toggleVisibility('currentPassword')"
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
            </div>

            <!-- New Password -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">New Password</label>
                <div class="relative">
                    <input type="password" id="newPassword"
                           class="w-full px-4 py-3 pr-12 bg-slate-50 border border-slate-200 rounded-xl
                                  focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500
                                  transition-all duration-200 outline-none"
                           placeholder="Enter new password (min 8 characters)">
                    <button type="button" onclick="toggleVisibility('newPassword')"
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
            </div>

            <!-- Confirm New Password -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirm New Password</label>
                <div class="relative">
                    <input type="password" id="confirmNewPassword"
                           class="w-full px-4 py-3 pr-12 bg-slate-50 border border-slate-200 rounded-xl
                                  focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500
                                  transition-all duration-200 outline-none"
                           placeholder="Confirm new password">
                    <button type="button" onclick="toggleVisibility('confirmNewPassword')"
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
            </div>

            <!-- Update Password Button -->
            <button type="button" id="updatePasswordBtn" onclick="updatePassword()"
                    class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-purple-600 to-indigo-600
                           text-white font-semibold rounded-xl hover:from-purple-700 hover:to-indigo-700
                           shadow-lg shadow-purple-500/25 hover:shadow-purple-500/40
                           transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span>Update Password</span>
            </button>
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
        'APP_NAME',
        'APP_LOGO',
        'PRIVACY_POLICY_URL',
        'DATA_DELETION_URL',
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

                // Preview logo if exists
                previewLogo();

                // Update branding
                updateBranding();
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
                // Update branding across the app
                updateBranding();
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

    // Update Password
    async function updatePassword() {
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmNewPassword = document.getElementById('confirmNewPassword').value;

        // Validation
        if (!currentPassword || !newPassword || !confirmNewPassword) {
            showToast('Please fill all password fields', 'error');
            return;
        }

        if (newPassword.length < 8) {
            showToast('New password must be at least 8 characters', 'error');
            return;
        }

        if (newPassword !== confirmNewPassword) {
            showToast('New passwords do not match', 'error');
            return;
        }

        const updateBtn = document.getElementById('updatePasswordBtn');
        updateBtn.disabled = true;
        updateBtn.innerHTML = `
            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Updating...</span>
        `;

        try {
            const response = await axios.post(`${API_BASE}/update-password`, {
                current_password: currentPassword,
                new_password: newPassword,
                new_password_confirmation: confirmNewPassword
            });

            if (response.data.success) {
                showToast('Password updated successfully', 'success');
                // Clear password fields
                document.getElementById('currentPassword').value = '';
                document.getElementById('newPassword').value = '';
                document.getElementById('confirmNewPassword').value = '';
            } else {
                showToast(response.data.message || 'Failed to update password', 'error');
            }
        } catch (error) {
            console.error('Error updating password:', error);
            const errorMsg = error.response?.data?.message ||
                            error.response?.data?.errors?.current_password?.[0] ||
                            error.response?.data?.errors?.new_password?.[0] ||
                            'Failed to update password';
            showToast(errorMsg, 'error');
        } finally {
            updateBtn.disabled = false;
            updateBtn.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span>Update Password</span>
            `;
        }
    }

    // Preview logo
    function previewLogo() {
        const logoUrl = document.getElementById('APP_LOGO').value;
        const previewSection = document.getElementById('logoPreviewSection');
        const previewImg = document.getElementById('logoPreviewImg');
        const logoFileName = document.getElementById('logoFileName');
        const dropZone = document.getElementById('logoDropZone');

        if (logoUrl) {
            previewImg.src = logoUrl;
            previewImg.onerror = function() {
                previewSection.classList.add('hidden');
                dropZone.classList.remove('hidden');
            };
            previewImg.onload = function() {
                previewSection.classList.remove('hidden');
                // Extract filename from URL
                const urlParts = logoUrl.split('/');
                logoFileName.textContent = urlParts[urlParts.length - 1] || 'logo';
            };
        } else {
            previewSection.classList.add('hidden');
            dropZone.classList.remove('hidden');
        }
    }

    // Handle logo file selection
    async function handleLogoSelect(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            showToast('Please select a valid image file (PNG, JPG, GIF, SVG, WebP)', 'error');
            return;
        }

        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            showToast('File size must be less than 2MB', 'error');
            return;
        }

        await uploadLogo(file);
    }

    // Upload logo to server
    async function uploadLogo(file) {
        const dropZone = document.getElementById('logoDropZone');
        const uploadIconArea = document.getElementById('uploadIconArea');
        const uploadProgress = document.getElementById('uploadProgress');

        // Show upload progress
        uploadIconArea.classList.add('hidden');
        uploadProgress.classList.remove('hidden');

        try {
            const formData = new FormData();
            formData.append('logo', file);

            const response = await axios.post(`${API_BASE}/settings/upload-logo`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });

            if (response.data.success) {
                const logoUrl = response.data.data.logo_url;
                document.getElementById('APP_LOGO').value = logoUrl;
                showToast('Logo uploaded successfully', 'success');
                previewLogo();
                updateBranding();
            } else {
                showToast(response.data.message || 'Failed to upload logo', 'error');
            }
        } catch (error) {
            console.error('Error uploading logo:', error);
            showToast(error.response?.data?.message || 'Failed to upload logo', 'error');
        } finally {
            // Reset upload area
            uploadIconArea.classList.remove('hidden');
            uploadProgress.classList.add('hidden');
            document.getElementById('logoFileInput').value = '';
        }
    }

    // Remove logo
    async function removeLogo() {
        const removeBtn = document.getElementById('removeLogoBtn');
        removeBtn.disabled = true;
        removeBtn.innerHTML = `
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Removing...</span>
        `;

        try {
            const response = await axios.delete(`${API_BASE}/settings/logo`);

            if (response.data.success) {
                document.getElementById('APP_LOGO').value = '';
                document.getElementById('logoPreviewSection').classList.add('hidden');
                document.getElementById('logoDropZone').classList.remove('hidden');
                showToast('Logo removed successfully', 'success');
                updateBranding();
            } else {
                showToast(response.data.message || 'Failed to remove logo', 'error');
            }
        } catch (error) {
            console.error('Error removing logo:', error);
            showToast(error.response?.data?.message || 'Failed to remove logo', 'error');
        } finally {
            removeBtn.disabled = false;
            removeBtn.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <span>Remove</span>
            `;
        }
    }

    // Setup drag and drop
    function setupDragAndDrop() {
        const dropZone = document.getElementById('logoDropZone');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('border-indigo-500', 'bg-indigo-50');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
            }, false);
        });

        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const file = dt.files[0];
            if (file) {
                handleLogoSelect({ target: { files: [file] } });
            }
        }, false);
    }

    // Update branding in sidebar, topbar and store in localStorage for login page
    function updateBranding() {
        const appName = document.getElementById('APP_NAME').value || 'FB Chat Manager';
        const appLogo = document.getElementById('APP_LOGO').value;

        // Store in localStorage for pages that load before API call
        localStorage.setItem('app_name', appName);
        if (appLogo) {
            localStorage.setItem('app_logo', appLogo);
        } else {
            localStorage.removeItem('app_logo');
        }

        // Update sidebar brand name
        const sidebarName = document.querySelector('#sidebar .nav-text h1');
        if (sidebarName) {
            sidebarName.textContent = appName.split(' ')[0] || 'FB Chat';
        }
        const sidebarSubtext = document.querySelector('#sidebar .nav-text p');
        if (sidebarSubtext) {
            sidebarSubtext.textContent = appName.split(' ').slice(1).join(' ') || 'Manager';
        }

        // Update sidebar logo if custom logo provided
        const sidebarLogoContainer = document.querySelector('#sidebar .flex-shrink-0.w-10.h-10');
        if (sidebarLogoContainer && appLogo) {
            sidebarLogoContainer.innerHTML = `<img src="${appLogo}" alt="${appName}" class="w-10 h-10 rounded-xl object-cover">`;
        }

        // Update document title
        document.title = `Settings - ${appName}`;
    }

    // Load settings on page load
    loadSettings();

    // Setup drag and drop on page load
    setupDragAndDrop();
</script>
@endsection
