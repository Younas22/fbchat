<header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
    <div>
        <h2 class="text-xl font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h2>
        <p class="text-sm text-gray-600 mt-1">@yield('page-subtitle', 'Manage your Facebook pages and chats')</p>
    </div>
    
    <div class="flex items-center space-x-4">
        <button onclick="refreshData()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            <span>Refresh</span>
        </button>
    </div>
</header>