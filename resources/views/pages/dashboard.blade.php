@extends('layouts.app')

@section('title', 'Dashboard - Facebook Chat Manager')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of your Facebook pages and conversations')

@section('content')
<!-- Login/Register Modal -->
<div id="authModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4 transform transition-all">
        <div class="text-center mb-6">
            <!-- Logo -->
            <div id="loginLogo" class="w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-blue-500/30">
                <svg class="w-10 h-10 text-white default-logo" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.477 2 2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879V14.89h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.989C18.343 21.129 22 16.99 22 12c0-5.523-4.477-10-10-10z"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-slate-900">Welcome Back</h3>
            <p id="loginSubtext" class="text-slate-500 mt-1">Sign in to continue to <span id="loginAppName">FB Chat Manager</span></p>
        </div>

        <!-- Login Form -->
        <div id="loginForm">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input type="email" id="loginEmail"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl
                                  focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                                  transition-all duration-200 outline-none"
                           placeholder="your@email.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <input type="password" id="loginPassword"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl
                                  focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500
                                  transition-all duration-200 outline-none"
                           placeholder="Enter your password">
                </div>
                <button onclick="login()"
                        class="w-full px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600
                               text-white font-semibold rounded-xl
                               hover:from-blue-700 hover:to-indigo-700
                               shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40
                               transition-all duration-200">
                    Sign In
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Dashboard Content -->
<div class="p-4 lg:p-6 space-y-6">
    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Total Pages Card -->
        <div class="bg-white rounded-2xl p-5 lg:p-6 shadow-sm border border-slate-100 hover:shadow-md hover:border-slate-200 transition-all duration-200">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Pages</p>
                    <p class="text-2xl lg:text-3xl font-bold text-slate-900 mt-1" id="totalPages">0</p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 lg:w-6 lg:h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs text-slate-500">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 font-medium">
                    Connected
                </span>
            </div>
        </div>

        <!-- Total Conversations Card -->
        <div class="bg-white rounded-2xl p-5 lg:p-6 shadow-sm border border-slate-100 hover:shadow-md hover:border-slate-200 transition-all duration-200">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Conversations</p>
                    <p class="text-2xl lg:text-3xl font-bold text-slate-900 mt-1" id="totalConversations">0</p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 lg:w-6 lg:h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs text-slate-500">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 font-medium">
                    All time
                </span>
            </div>
        </div>

        <!-- Unread Messages Card -->
        <div class="bg-white rounded-2xl p-5 lg:p-6 shadow-sm border border-slate-100 hover:shadow-md hover:border-slate-200 transition-all duration-200">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Unread</p>
                    <p class="text-2xl lg:text-3xl font-bold text-slate-900 mt-1" id="unreadMessages">0</p>
                </div>
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-rose-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 lg:w-6 lg:h-6 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-xs text-slate-500">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-50 text-rose-600 font-medium">
                    Pending
                </span>
            </div>
        </div>

    </div>

    <!-- Two Column Layout for Desktop -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Connected Pages Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-5 lg:px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Connected Pages</h3>
                    <p class="text-sm text-slate-500 mt-0.5">Your Facebook pages</p>
                </div>
                <a href="{{ route('pages.index') }}"
                   class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                    View all
                </a>
            </div>
            <div id="pagesList" class="divide-y divide-slate-100 max-h-[400px] overflow-y-auto">
                <!-- Loading State -->
                <div class="p-6 text-center">
                    <div class="inline-flex items-center gap-2 text-slate-500">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Loading pages...</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Conversations Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-5 lg:px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Recent Conversations</h3>
                    <p class="text-sm text-slate-500 mt-0.5">Latest customer chats</p>
                </div>
                <a href="{{ route('conversations.all') }}"
                   class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                    View all
                </a>
            </div>
            <div id="recentConversations" class="divide-y divide-slate-100 max-h-[400px] overflow-y-auto">
                <!-- Loading State -->
                <div class="p-6 text-center">
                    <div class="inline-flex items-center gap-2 text-slate-500">
                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Loading conversations...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Load branding for login modal
    function loadLoginBranding() {
        const appName = localStorage.getItem('app_name') || 'FB Chat Manager';
        const appLogo = localStorage.getItem('app_logo');

        const nameEl = document.getElementById('loginAppName');
        if (nameEl) nameEl.textContent = appName;

        const logoEl = document.getElementById('loginLogo');
        if (logoEl && appLogo) {
            logoEl.innerHTML = `<img src="${appLogo}" alt="${appName}" class="w-16 h-16 rounded-2xl object-cover" onerror="this.parentElement.innerHTML='<svg class=\\'w-10 h-10 text-white\\' fill=\\'currentColor\\' viewBox=\\'0 0 24 24\\'><path d=\\'M12 2C6.477 2 2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879V14.89h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.989C18.343 21.129 22 16.99 22 12c0-5.523-4.477-10-10-10z\\'/></svg>'">`;
            logoEl.classList.remove('bg-gradient-to-br', 'from-blue-600', 'to-indigo-600', 'shadow-lg', 'shadow-blue-500/30');
        }

        // Also try to fetch from API if not logged in
        fetchBrandingFromAPI();
    }

    async function fetchBrandingFromAPI() {
        try {
            const response = await axios.get('/api/settings/branding');
            if (response.data.success) {
                const { app_name, app_logo } = response.data.data;
                if (app_name) {
                    localStorage.setItem('app_name', app_name);
                    document.getElementById('loginAppName').textContent = app_name;
                }
                if (app_logo) {
                    localStorage.setItem('app_logo', app_logo);
                    const logoEl = document.getElementById('loginLogo');
                    if (logoEl) {
                        logoEl.innerHTML = `<img src="${app_logo}" alt="${app_name}" class="w-16 h-16 rounded-2xl object-cover">`;
                        logoEl.classList.remove('bg-gradient-to-br', 'from-blue-600', 'to-indigo-600', 'shadow-lg', 'shadow-blue-500/30');
                    }
                }
            }
        } catch (error) {
            console.log('Using cached branding');
        }
    }

    // Load branding immediately
    loadLoginBranding();

    // Check if user is authenticated
    function checkAuth() {
        const token = localStorage.getItem('token');
        if (!token) {
            document.getElementById('authModal').classList.remove('hidden');
            return false;
        }
        return true;
    }

    async function login() {
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;

        if (!email || !password) {
            alert('Please fill all fields');
            return;
        }

        try {
            const response = await axios.post(`${API_BASE}/login`, { email, password });

            if (response.data.success) {
                localStorage.setItem('token', response.data.token);
                axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
                document.getElementById('authModal').classList.add('hidden');
                location.reload();
            }
        } catch (error) {
            alert('Login failed: ' + (error.response?.data?.message || 'Invalid credentials'));
        }
    }

    async function loadDashboard() {
        try {
            // Wait for authentication
            while (!window.ensureAuthenticated) {
                await new Promise(resolve => setTimeout(resolve, 100));
            }
            await window.ensureAuthenticated();

            // Fetch pages data
            const pagesRes = await axios.get(`${API_BASE}/pages`);
            const pages = pagesRes.data;

            // Update stats
            document.getElementById('totalPages').textContent = pages.length;

            // Fetch conversations and unread count for all pages
            let totalConversations = 0;
            let totalUnread = 0;
            let recentConversationsArray = [];

            for (const page of pages) {
                try {
                    const convRes = await axios.get(`${API_BASE}/conversations/${page.id}`);
                    const conversations = convRes.data.data.data || [];
                    totalConversations += conversations.length;

                    // Count unread messages and add page info to each conversation
                    conversations.forEach(conv => {
                        totalUnread += conv.unread_count || 0;
                        conv.page_name = page.page_name;
                        conv.page_id = page.id;
                    });

                    // Get recent 5 conversations
                    recentConversationsArray.push(...conversations.slice(0, 5));
                } catch (error) {
                    console.error('Error fetching conversations for page', page.id, error);
                }
            }

            document.getElementById('totalConversations').textContent = totalConversations;
            document.getElementById('unreadMessages').textContent = totalUnread;

            // Pages HTML - Modern design
            const pagesHTML = pages.map(page => `
                <div class="px-5 lg:px-6 py-4 hover:bg-slate-50/50 transition-colors duration-150">
                    <div class="flex items-center gap-4">
                        <img src="${page.page_profile_pic || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(page.page_name) + '&size=48&background=3b82f6&color=fff'}"
                             alt="${page.page_name}"
                             class="w-11 h-11 rounded-xl object-cover ring-2 ring-slate-100"
                             onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(page.page_name)}&size=48&background=3b82f6&color=fff'">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-slate-900 truncate">${page.page_name}</p>
                            <p class="text-sm text-slate-500 truncate">ID: ${page.page_id}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                Active
                            </span>
                            <a href="/conversations/${page.id}"
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            `).join('');

            document.getElementById('pagesList').innerHTML = pagesHTML || `
                <div class="px-5 lg:px-6 py-12 text-center">
                    <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <p class="text-slate-600 font-medium">No pages connected</p>
                    <p class="text-sm text-slate-500 mt-1">Connect a Facebook page to get started</p>
                </div>
            `;

            // Recent Conversations HTML - Modern design
            const sortedRecent = recentConversationsArray
                .sort((a, b) => new Date(b.last_message_time) - new Date(a.last_message_time))
                .slice(0, 10);

            if (sortedRecent.length > 0) {
                const recentHTML = sortedRecent.map(conv => `
                    <a href="/conversations/${conv.page_id}" class="block px-5 lg:px-6 py-4 hover:bg-slate-50/50 transition-colors duration-150">
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <img src="${conv.customer_profile_pic || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(conv.customer_name || 'U') + '&size=44&background=random'}"
                                     alt="${conv.customer_name}"
                                     class="w-11 h-11 rounded-xl object-cover ring-2 ring-slate-100"
                                     onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(conv.customer_name || 'Unknown')}&size=44&background=random'">
                                ${conv.unread_count > 0 ? '<span class="absolute -top-1 -right-1 w-3 h-3 bg-blue-500 rounded-full ring-2 ring-white"></span>' : ''}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="font-medium text-slate-900 truncate">${conv.customer_name || 'Unknown'}</p>
                                    ${conv.unread_count > 0 ? `
                                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-xs font-semibold bg-blue-600 text-white">
                                            ${conv.unread_count}
                                        </span>
                                    ` : ''}
                                </div>
                                <p class="text-sm text-slate-500 truncate mt-0.5">${conv.last_message_preview || 'No messages yet'}</p>
                                <p class="text-xs text-blue-600 truncate mt-1">
                                    <svg class="w-3 h-3 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    ${conv.page_name}
                                </p>
                            </div>
                        </div>
                    </a>
                `).join('');
                document.getElementById('recentConversations').innerHTML = recentHTML;
            } else {
                document.getElementById('recentConversations').innerHTML = `
                    <div class="px-5 lg:px-6 py-12 text-center">
                        <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <p class="text-slate-600 font-medium">No conversations yet</p>
                        <p class="text-sm text-slate-500 mt-1">Messages will appear here once customers reach out</p>
                    </div>
                `;
            }

        } catch (error) {
            console.error('Error loading dashboard:', error);
            if (error.response?.status === 401) {
                localStorage.removeItem('token');
                checkAuth();
            }
        }
    }

    // Load on page load
    loadDashboard();

    // Refresh every 30 seconds
    setInterval(loadDashboard, 30000);
</script>
@endsection
