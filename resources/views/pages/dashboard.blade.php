@extends('layouts.app')

@section('title', 'Dashboard - Facebook Chat Manager')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of your Facebook pages and conversations')

@section('content')
<!-- Login/Register Modal -->
<div id="authModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
        <h3 class="text-2xl font-bold text-gray-900 mb-6">Welcome to FB Chat Manager</h3>

        <!-- Login Form -->
        <div id="loginForm">
            <p class="text-gray-600 mb-4">Please login to continue</p>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="loginEmail" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="your@email.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" id="loginPassword" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="••••••••">
                </div>
                <button onclick="login()" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Login</button>
                <p class="text-center text-sm text-gray-600">
                    Don't have an account?
                    <button onclick="showRegister()" class="text-blue-600 hover:text-blue-700 font-medium">Register</button>
                </p>
            </div>
        </div>

        <!-- Register Form -->
        <div id="registerForm" class="hidden">
            <p class="text-gray-600 mb-4">Create a new account</p>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" id="registerName" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Your Name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="registerEmail" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="your@email.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" id="registerPassword" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="••••••••">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" id="registerPasswordConfirmation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="••••••••">
                </div>
                <button onclick="register()" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Register</button>
                <p class="text-center text-sm text-gray-600">
                    Already have an account?
                    <button onclick="showLogin()" class="text-blue-600 hover:text-blue-700 font-medium">Login</button>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="p-8">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Pages</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2" id="totalPages">0</p>
                </div>
                <svg class="w-12 h-12 text-blue-600 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Conversations</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2" id="totalConversations">0</p>
                </div>
                <svg class="w-12 h-12 text-green-600 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Unread Messages</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2" id="unreadMessages">0</p>
                </div>
                <svg class="w-12 h-12 text-red-600 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Saved Chats</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2" id="savedChatsCount">0</p>
                </div>
                <svg class="w-12 h-12 text-purple-600 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Connected Pages Section -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Connected Pages</h3>
        </div>
        <div id="pagesList" class="divide-y">
            <!-- Pages load here -->
        </div>
    </div>

    <!-- Recent Conversations -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Conversations</h3>
        </div>
        <div id="recentConversations" class="divide-y">
            <!-- Recent conversations load here -->
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Check if user is authenticated
    function checkAuth() {
        const token = localStorage.getItem('token');
        if (!token) {
            document.getElementById('authModal').classList.remove('hidden');
            return false;
        }
        return true;
    }

    function showLogin() {
        document.getElementById('registerForm').classList.add('hidden');
        document.getElementById('loginForm').classList.remove('hidden');
    }

    function showRegister() {
        document.getElementById('loginForm').classList.add('hidden');
        document.getElementById('registerForm').classList.remove('hidden');
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

    async function register() {
        const name = document.getElementById('registerName').value;
        const email = document.getElementById('registerEmail').value;
        const password = document.getElementById('registerPassword').value;
        const passwordConfirmation = document.getElementById('registerPasswordConfirmation').value;

        if (!name || !email || !password || !passwordConfirmation) {
            alert('Please fill all fields');
            return;
        }

        if (password !== passwordConfirmation) {
            alert('Passwords do not match');
            return;
        }

        try {
            const response = await axios.post(`${API_BASE}/register`, {
                name,
                email,
                password,
                password_confirmation: passwordConfirmation
            });

            if (response.data.success) {
                localStorage.setItem('token', response.data.token);
                axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
                document.getElementById('authModal').classList.add('hidden');
                location.reload();
            }
        } catch (error) {
            alert('Registration failed: ' + (error.response?.data?.message || 'Please try again'));
        }
    }

    async function loadDashboard() {
        try {
            // Wait for authentication
            while (!window.ensureAuthenticated) {
                await new Promise(resolve => setTimeout(resolve, 100));
            }
            await window.ensureAuthenticated();

            // Fetch all data in parallel
            const [pagesRes, savedChatsRes] = await Promise.all([
                axios.get(`${API_BASE}/pages`),
                axios.get(`${API_BASE}/saved-chats`)
            ]);

            const pages = pagesRes.data;
            const savedChats = savedChatsRes.data.data.data || [];

            // Update stats
            document.getElementById('totalPages').textContent = pages.length;
            document.getElementById('savedChatsCount').textContent = savedChats.length;

            // Fetch conversations and unread count for all pages
            let totalConversations = 0;
            let totalUnread = 0;
            let recentConversationsArray = [];

            for (const page of pages) {
                try {
                    const convRes = await axios.get(`${API_BASE}/conversations/${page.id}`);
                    const conversations = convRes.data.data.data || [];
                    totalConversations += conversations.length;

                    // Count unread messages
                    conversations.forEach(conv => {
                        totalUnread += conv.unread_count || 0;
                    });

                    // Get recent 5 conversations
                    recentConversationsArray.push(...conversations.slice(0, 5));
                } catch (error) {
                    console.error('Error fetching conversations for page', page.id, error);
                }
            }

            document.getElementById('totalConversations').textContent = totalConversations;
            document.getElementById('unreadMessages').textContent = totalUnread;

            // Pages HTML
            const pagesHTML = pages.map(page => `
                <div class="p-4 hover:bg-gray-50 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <img src="${page.page_profile_pic || 'https://via.placeholder.com/48'}" alt="${page.page_name}" class="w-12 h-12 rounded-full">
                        <div>
                            <p class="font-medium text-gray-900">${page.page_name}</p>
                            <p class="text-sm text-gray-600">ID: ${page.page_id}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Active</span>
                        <a href="/conversations/${page.id}" class="text-blue-600 hover:text-blue-700 font-medium">View</a>
                    </div>
                </div>
            `).join('');

            document.getElementById('pagesList').innerHTML = pagesHTML || '<div class="p-4 text-gray-600">No pages connected</div>';

            // Recent Conversations HTML
            const sortedRecent = recentConversationsArray
                .sort((a, b) => new Date(b.last_message_time) - new Date(a.last_message_time))
                .slice(0, 10);

            if (sortedRecent.length > 0) {
                const recentHTML = sortedRecent.map(conv => `
                    <div class="p-4 hover:bg-gray-50 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <img src="${conv.customer_profile_pic || 'https://via.placeholder.com/40'}"
                                 alt="${conv.customer_name}"
                                 class="w-10 h-10 rounded-full object-cover"
                                 onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(conv.customer_name || 'Unknown')}&size=40&background=random'">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">${conv.customer_name || 'Unknown'}</p>
                                <p class="text-sm text-gray-600 truncate" style="max-width: 300px;">
                                    ${conv.last_message_preview || 'No messages'}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            ${conv.unread_count > 0 ? `<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">${conv.unread_count} unread</span>` : ''}
                            <a href="/chat/${conv.id}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">View</a>
                        </div>
                    </div>
                `).join('');
                document.getElementById('recentConversations').innerHTML = recentHTML;
            } else {
                document.getElementById('recentConversations').innerHTML = '<div class="p-4 text-gray-600">No recent conversations</div>';
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