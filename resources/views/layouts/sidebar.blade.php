{{--
    Modern Collapsible Sidebar Component
    - Desktop: Collapsible with toggle button (expanded: 260px, collapsed: 72px)
    - Mobile: Hidden by default, slides in from left with overlay
    - Features: Smooth transitions, tooltips when collapsed, active state highlighting
--}}

@php
    // Get current route for active state
    $currentRoute = Route::currentRouteName();
@endphp

<aside id="sidebar"
       class="fixed lg:relative inset-y-0 left-0 z-50
              w-64 bg-white border-r border-slate-200
              flex flex-col
              transition-all duration-300 ease-in-out
              -translate-x-full lg:translate-x-0
              shadow-xl lg:shadow-none">

    <!-- Logo Section -->
    <div class="h-16 flex items-center justify-between px-4 border-b border-slate-100">
        <!-- Logo & Brand -->
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0">
            <!-- Logo Icon -->
            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/25">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.477 2 2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879V14.89h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.989C18.343 21.129 22 16.99 22 12c0-5.523-4.477-10-10-10z"/>
                </svg>
            </div>
            <!-- Brand Text (hidden when collapsed) -->
            <div class="nav-text min-w-0 transition-opacity duration-200">
                <h1 class="text-base font-bold text-slate-900 truncate">FB Chat</h1>
                <p class="text-xs text-slate-500 truncate">Manager</p>
            </div>
        </a>

        <!-- Collapse Toggle Button (Desktop only) -->
        <button onclick="toggleSidebar()"
                class="hidden lg:flex items-center justify-center w-8 h-8 rounded-lg
                       text-slate-400 hover:text-slate-600 hover:bg-slate-100
                       transition-colors duration-200"
                title="Toggle Sidebar">
            <svg class="w-5 h-5 collapse-icon transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
        </button>

        <!-- Close Button (Mobile only) -->
        <button onclick="toggleMobileSidebar()"
                class="lg:hidden flex items-center justify-center w-8 h-8 rounded-lg
                       text-slate-400 hover:text-slate-600 hover:bg-slate-100
                       transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Navigation Section -->
    <nav class="flex-1 overflow-y-auto py-4 px-3">
        <!-- Main Menu Label -->
        <div class="nav-text px-3 mb-2">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Main Menu</span>
        </div>

        <div class="space-y-1">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
               class="nav-item group relative flex items-center gap-3 px-3 py-2.5 rounded-xl
                      transition-all duration-200
                      {{ $currentRoute === 'dashboard' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <div class="flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-lg
                            {{ $currentRoute === 'dashboard' ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-slate-200' }}
                            transition-colors duration-200">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4z"></path>
                        <path d="M3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z"></path>
                        <path d="M14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                    </svg>
                </div>
                <span class="nav-text font-medium transition-opacity duration-200">Dashboard</span>
                <!-- Tooltip for collapsed state -->
                <div class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 text-white text-sm rounded-md whitespace-nowrap z-50">
                    Dashboard
                </div>
            </a>

            <!-- Pages -->
            <a href="{{ route('pages.index') }}"
               class="nav-item group relative flex items-center gap-3 px-3 py-2.5 rounded-xl
                      transition-all duration-200
                      {{ $currentRoute === 'pages.index' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <div class="flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-lg
                            {{ $currentRoute === 'pages.index' ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-slate-200' }}
                            transition-colors duration-200">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <span class="nav-text font-medium transition-opacity duration-200">Pages</span>
                <!-- Tooltip -->
                <div class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 text-white text-sm rounded-md whitespace-nowrap z-50">
                    Pages
                </div>
            </a>

            <!-- Conversations -->
            <a href="{{ route('conversations.all') }}"
               class="nav-item group relative flex items-center gap-3 px-3 py-2.5 rounded-xl
                      transition-all duration-200
                      {{ $currentRoute === 'conversations.all' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <div class="flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-lg
                            {{ $currentRoute === 'conversations.all' ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-slate-200' }}
                            transition-colors duration-200">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <span class="nav-text font-medium transition-opacity duration-200">Conversations</span>
                <!-- Tooltip -->
                <div class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 text-white text-sm rounded-md whitespace-nowrap z-50">
                    Conversations
                </div>
            </a>

            <!-- Settings -->
            <a href="{{ route('settings.index') }}"
               class="nav-item group relative flex items-center gap-3 px-3 py-2.5 rounded-xl
                      transition-all duration-200
                      {{ $currentRoute === 'settings.index' ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <div class="flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-lg
                            {{ $currentRoute === 'settings.index' ? 'bg-blue-100' : 'bg-slate-100 group-hover:bg-slate-200' }}
                            transition-colors duration-200">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <span class="nav-text font-medium transition-opacity duration-200">Settings</span>
                <!-- Tooltip -->
                <div class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 text-white text-sm rounded-md whitespace-nowrap z-50">
                    Settings
                </div>
            </a>

        </div>
    </nav>

    <!-- User Section (Bottom) -->
    <div class="border-t border-slate-100 p-3">
        <div id="user-info" class="nav-item group relative flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-all duration-200">
            <!-- User Avatar -->
            <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold text-sm shadow-md">
                A
            </div>
            <!-- User Info (hidden when collapsed) -->
            <div class="nav-text flex-1 min-w-0 transition-opacity duration-200">
                <p class="text-sm font-medium text-slate-900 truncate">Admin User</p>
                <button onclick="logout()" class="text-xs text-red-500 hover:text-red-600 font-medium transition-colors">
                    Sign out
                </button>
            </div>
            <!-- Tooltip for collapsed state -->
            <div class="sidebar-tooltip absolute left-full ml-3 px-2 py-1 bg-slate-900 text-white text-sm rounded-md whitespace-nowrap z-50">
                Admin User
            </div>
        </div>
    </div>
</aside>

{{-- Sidebar-specific styles for collapsed state --}}
<style>
    /* When sidebar is collapsed, hide text elements and adjust width */
    #sidebar.sidebar-collapsed .nav-text {
        opacity: 0;
        width: 0;
        overflow: hidden;
    }

    #sidebar.sidebar-collapsed .nav-item {
        justify-content: center;
    }

    #sidebar.sidebar-collapsed .nav-item > div:first-child {
        margin: 0;
    }

    /* Rotate collapse icon when collapsed */
    #sidebar.sidebar-collapsed .collapse-icon {
        transform: rotate(180deg);
    }

    /* Adjust user section in collapsed state */
    #sidebar.sidebar-collapsed #user-info {
        justify-content: center;
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }
</style>

<script>
    // Fetch user info from API
    async function loadUserInfo() {
        // Wait for axios to be available
        while (!window.axios) {
            await new Promise(resolve => setTimeout(resolve, 100));
        }

        const token = localStorage.getItem('token');
        if (token) {
            try {
                const response = await axios.get('/api/me', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                if (response.data.success) {
                    const user = response.data.user;
                    const initial = user.name ? user.name.charAt(0).toUpperCase() : 'A';

                    // Update avatar initial
                    const avatarEl = document.querySelector('#user-info > div:first-child');
                    if (avatarEl) {
                        avatarEl.textContent = initial;
                    }

                    // Update name
                    const nameEl = document.querySelector('#user-info .text-slate-900');
                    if (nameEl) {
                        nameEl.textContent = user.name || 'Admin User';
                    }

                    // Update tooltip
                    const tooltipEl = document.querySelector('#user-info .sidebar-tooltip');
                    if (tooltipEl) {
                        tooltipEl.textContent = user.name || 'Admin User';
                    }
                }
            } catch (error) {
                console.error('Failed to load user info:', error);
            }
        }
    }

    async function logout() {
        // Wait for axios to be available
        while (!window.axios) {
            await new Promise(resolve => setTimeout(resolve, 100));
        }

        const token = localStorage.getItem('token');
        if (token) {
            try {
                await axios.post('/api/logout', {}, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
            } catch (error) {
                console.error('Logout error:', error);
            }
        }
        localStorage.removeItem('token');
        window.location.href = '/';
    }

    // Load user info on page load
    loadUserInfo();
</script>
