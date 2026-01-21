{{--
    Modern Topbar Component
    - Includes hamburger menu for mobile sidebar toggle
    - Page title and subtitle with modern typography
    - Action buttons with subtle animations
--}}

<header class="h-16 bg-white border-b border-slate-200 px-4 lg:px-6 flex items-center justify-between flex-shrink-0">
    <!-- Left Section: Mobile Menu + Page Info -->
    <div class="flex items-center gap-4">
        <!-- Mobile Menu Toggle Button -->
        <button onclick="toggleMobileSidebar()"
                class="lg:hidden flex items-center justify-center w-10 h-10 rounded-xl
                       text-slate-500 hover:text-slate-700 hover:bg-slate-100
                       transition-colors duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <!-- Page Title & Subtitle -->
        <div class="min-w-0">
            <h2 class="text-lg font-semibold text-slate-900 truncate">
                @yield('page-title', 'Dashboard')
            </h2>
            <p class="text-sm text-slate-500 truncate hidden sm:block">
                @yield('page-subtitle', 'Manage your Facebook pages and chats')
            </p>
        </div>
    </div>

    <!-- Right Section: Actions -->
    <div class="flex items-center gap-2 sm:gap-3">
        <!-- Refresh Button -->
        <button onclick="refreshData()"
                class="group flex items-center gap-2 px-3 sm:px-4 py-2 rounded-xl
                       bg-slate-100 hover:bg-slate-200
                       text-slate-600 hover:text-slate-800
                       transition-all duration-200">
            <svg class="w-4 h-4 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span class="hidden sm:inline text-sm font-medium">Refresh</span>
        </button>

        <!-- Optional: Notifications Bell (can be enabled later) -->
        {{--
        <button class="relative flex items-center justify-center w-10 h-10 rounded-xl
                       text-slate-500 hover:text-slate-700 hover:bg-slate-100
                       transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <!-- Notification Badge -->
            <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
        </button>
        --}}
    </div>
</header>

<script>
    // Global refresh function - can be overridden by individual pages
    function refreshData() {
        // Check if page has a custom loadDashboard function
        if (typeof loadDashboard === 'function') {
            loadDashboard();
        }
        // Check for other common refresh functions
        else if (typeof loadPages === 'function') {
            loadPages();
        }
        else if (typeof loadConversations === 'function') {
            loadConversations();
        }
        else if (typeof loadSavedChats === 'function') {
            loadSavedChats();
        }
        else {
            // Fallback: reload the page
            window.location.reload();
        }
    }
</script>
