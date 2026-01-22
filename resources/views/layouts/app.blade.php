<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Facebook Chat Manager')</title>
    <script>
        // Set title from cached branding immediately
        (function() {
            const appName = localStorage.getItem('app_name');
            if (appName) {
                const currentTitle = document.title;
                if (currentTitle.includes('Facebook Chat Manager')) {
                    document.title = currentTitle.replace('Facebook Chat Manager', appName);
                }
            }
        })();
    </script>
    {{-- Tailwind CSS via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Axios CDN for API calls --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        /* Custom scrollbar for modern look */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Tooltip styles */
        .sidebar-tooltip {
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out;
        }
        .sidebar-collapsed .nav-item:hover .sidebar-tooltip {
            visibility: visible;
            opacity: 1;
        }
    </style>
    
</head>
<body class="bg-slate-50 antialiased">
    <!-- Mobile Sidebar Overlay -->
    <div id="sidebarOverlay"
         class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 lg:hidden hidden"
         onclick="toggleMobileSidebar()">
    </div>

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Bar -->
            @include('layouts.topbar')

            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto bg-slate-50">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Global Sidebar State Management -->
    <script>
        const API_BASE = "{{ url('/api') }}";

        // Sidebar state management
        const SIDEBAR_STATE_KEY = 'sidebar_collapsed';

        function getSidebarState() {
            return localStorage.getItem(SIDEBAR_STATE_KEY) === 'true';
        }

        function setSidebarState(collapsed) {
            localStorage.setItem(SIDEBAR_STATE_KEY, collapsed);
        }

        // Toggle desktop sidebar (expand/collapse)
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const isCollapsed = sidebar.classList.contains('sidebar-collapsed');

            if (isCollapsed) {
                // Expand
                sidebar.classList.remove('sidebar-collapsed', 'w-[72px]');
                sidebar.classList.add('w-64');
                setSidebarState(false);
            } else {
                // Collapse
                sidebar.classList.add('sidebar-collapsed', 'w-[72px]');
                sidebar.classList.remove('w-64');
                setSidebarState(true);
            }
        }

        // Toggle mobile sidebar (show/hide)
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (sidebar.classList.contains('-translate-x-full')) {
                // Show sidebar
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            } else {
                // Hide sidebar
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        // Initialize sidebar state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            if (getSidebarState() && window.innerWidth >= 1024) {
                sidebar.classList.add('sidebar-collapsed', 'w-[72px]');
                sidebar.classList.remove('w-64');
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (window.innerWidth >= 1024) {
                // Desktop: remove mobile classes
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            } else {
                // Mobile: ensure sidebar is hidden by default
                if (!overlay.classList.contains('hidden')) {
                    return; // Keep open if intentionally opened
                }
                sidebar.classList.add('-translate-x-full');
            }
        });

        // Wait for axios to be available (loaded by Vite)
        function waitForAxios() {
            return new Promise((resolve) => {
                if (window.axios) {
                    resolve();
                } else {
                    const checkInterval = setInterval(() => {
                        if (window.axios) {
                            clearInterval(checkInterval);
                            resolve();
                        }
                    }, 50);
                }
            });
        }

        // Initialize axios configuration once it's loaded
        waitForAxios().then(() => {
            console.log('Axios loaded successfully!');

            // Configure axios defaults
            axios.defaults.baseURL = "{{ url('/') }}";
            axios.defaults.headers.common['Accept'] = 'application/json';
            axios.defaults.headers.common['Content-Type'] = 'application/json';

            // Add request interceptor to ensure token is always included
            axios.interceptors.request.use(
                config => {
                    const token = localStorage.getItem('token');
                    if (token) {
                        config.headers.Authorization = `Bearer ${token}`;
                    }
                    console.log('Request interceptor - Token:', token ? 'exists' : 'missing');
                    console.log('Request interceptor - Auth header:', config.headers.Authorization);
                    return config;
                },
                error => {
                    return Promise.reject(error);
                }
            );

            // Check if user is authenticated - show login modal if not
            async function ensureAuthenticated() {
                let token = localStorage.getItem('token');
                console.log('Current token in localStorage:', token ? 'exists' : 'not found');

                // If token exists, validate it first
                if (token) {
                    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                    console.log('Testing existing token...');

                    try {
                        // Test token validity
                        const meResponse = await axios.get('/api/me');
                        console.log('Existing token is valid!', meResponse.data);
                        // Update auth buttons
                        if (typeof updateAuthButtons === 'function') {
                            updateAuthButtons();
                        }
                        return true;
                    } catch (error) {
                        console.log('Existing token is invalid, clearing it...', error.response?.status);
                        localStorage.removeItem('token');
                        delete axios.defaults.headers.common['Authorization'];
                        token = null;
                    }
                }

                // No valid token - show login modal
                if (!token) {
                    console.log('No valid token found, showing login modal...');
                    showGlobalLoginModal();
                    // Update auth buttons
                    if (typeof updateAuthButtons === 'function') {
                        updateAuthButtons();
                    }
                    return false;
                }

                return false;
            }

            // Show global login modal
            function showGlobalLoginModal() {
                const authModal = document.getElementById('authModal');
                if (authModal) {
                    authModal.classList.remove('hidden');
                }
            }

            // Make it globally available
            window.showGlobalLoginModal = showGlobalLoginModal;

            // Initialize auth on page load
            let authReady = false;
            ensureAuthenticated().then(success => {
                authReady = success;
                console.log('Authentication ready:', authReady);
                console.log('API Base URL:', API_BASE);
            });

            // Helper function to ensure API calls wait for auth
            window.apiCall = async function(fn) {
                if (!authReady) {
                    await ensureAuthenticated();
                }
                return fn();
            };

            // Make ensureAuthenticated globally available
            window.ensureAuthenticated = ensureAuthenticated;
        });
    </script>
    @yield('scripts')
</body>
</html>
