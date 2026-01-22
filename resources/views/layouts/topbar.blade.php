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
        <button id="refreshBtn" onclick="handleRefresh()"
                class="group flex items-center gap-2 px-3 sm:px-4 py-2 rounded-xl
                       bg-slate-100 hover:bg-slate-200
                       text-slate-600 hover:text-slate-800
                       transition-all duration-200">
            <svg id="refreshIcon" class="w-4 h-4 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span class="hidden sm:inline text-sm font-medium">Refresh</span>
        </button>

        <!-- Login Button (shown when not logged in) -->
        <button id="loginBtn" onclick="showLoginModal()"
                class="hidden items-center gap-2 px-3 sm:px-4 py-2 rounded-xl
                       bg-blue-600 hover:bg-blue-700
                       text-white
                       transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            <span class="hidden sm:inline text-sm font-medium">Login</span>
        </button>

        <!-- Logout Button (shown when logged in) -->
        <button id="logoutBtn" onclick="handleLogout()"
                class="hidden items-center gap-2 px-3 sm:px-4 py-2 rounded-xl
                       bg-red-100 hover:bg-red-200
                       text-red-600 hover:text-red-700
                       transition-all duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            <span class="hidden sm:inline text-sm font-medium">Logout</span>
        </button>
    </div>
</header>

<script>
    // Update auth buttons visibility based on login status
    function updateAuthButtons() {
        const token = localStorage.getItem('token');
        const loginBtn = document.getElementById('loginBtn');
        const logoutBtn = document.getElementById('logoutBtn');

        if (token) {
            loginBtn.classList.add('hidden');
            loginBtn.classList.remove('flex');
            logoutBtn.classList.remove('hidden');
            logoutBtn.classList.add('flex');
        } else {
            loginBtn.classList.remove('hidden');
            loginBtn.classList.add('flex');
            logoutBtn.classList.add('hidden');
            logoutBtn.classList.remove('flex');
        }
    }

    // Show login modal
    function showLoginModal() {
        const authModal = document.getElementById('authModal');
        if (authModal) {
            authModal.classList.remove('hidden');
        } else {
            // Redirect to dashboard which has the login modal
            window.location.href = '/';
        }
    }

    // Handle logout
    async function handleLogout() {
        try {
            const token = localStorage.getItem('token');
            if (token) {
                await axios.post('/api/logout');
            }
        } catch (error) {
            console.log('Logout API error (token may be expired):', error);
        } finally {
            localStorage.removeItem('token');
            delete axios.defaults.headers.common['Authorization'];
            updateAuthButtons();
            window.location.href = '/';
        }
    }

    // Handle refresh with animation
    async function handleRefresh() {
        const refreshIcon = document.getElementById('refreshIcon');
        const refreshBtn = document.getElementById('refreshBtn');

        // Add spinning animation
        refreshIcon.classList.add('animate-spin');
        refreshBtn.disabled = true;

        try {
            await refreshData();
        } catch (error) {
            console.error('Refresh error:', error);
        } finally {
            // Remove animation after a delay
            setTimeout(() => {
                refreshIcon.classList.remove('animate-spin');
                refreshBtn.disabled = false;
            }, 500);
        }
    }

    // Global refresh function - can be overridden by individual pages
    async function refreshData() {
        // Check if page has a custom loadDashboard function
        if (typeof loadDashboard === 'function') {
            await loadDashboard();
        }
        // Check for other common refresh functions
        else if (typeof loadPages === 'function') {
            await loadPages();
        }
        else if (typeof loadConversations === 'function') {
            await loadConversations();
        }
        else if (typeof loadSavedChats === 'function') {
            await loadSavedChats();
        }
        else {
            // Fallback: reload the page
            window.location.reload();
        }
    }

    // Initialize auth buttons on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateAuthButtons();
    });

    // Listen for storage changes (for cross-tab logout)
    window.addEventListener('storage', function(e) {
        if (e.key === 'token') {
            updateAuthButtons();
        }
    });
</script>
