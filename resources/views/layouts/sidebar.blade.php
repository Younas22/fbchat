<aside class="w-64 bg-white shadow-lg border-r border-gray-200">
    <div class="p-6 border-b border-gray-200">
        <h1 class="text-2xl font-bold text-blue-600">FB Chat Manager</h1>
        <p class="text-sm text-gray-600 mt-1">Multi-Page Management</p>
    </div>

    <nav class="p-6 space-y-4">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4z"></path>
                <path d="M3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z"></path>
                <path d="M14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
            </svg>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('pages.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
            </svg>
            <span>Pages</span>
        </a>

        <a href="{{ route('conversations.all') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5z"></path>
                <path d="M6 11a1 1 0 11-2 0 1 1 0 012 0z"></path>
                <path d="M11 11a1 1 0 11-2 0 1 1 0 012 0z"></path>
                <path d="M16 11a1 1 0 11-2 0 1 1 0 012 0z"></path>
            </svg>
            <span>Conversations</span>
        </a>

        <a href="{{ route('saved-chats.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
            </svg>
            <span>Saved Chats</span>
        </a>
    </nav>

    <div class="absolute bottom-0 left-0 right-0 p-6 border-t border-gray-200 bg-gray-50 w-64">
        <div id="user-info" class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                A
            </div>
            <div>
                <p class="text-sm font-medium text-gray-900">Admin User</p>
                <button onclick="logout()" class="text-xs text-red-600 hover:text-red-700">Logout</button>
            </div>
        </div>
    </div>

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

                        document.querySelector('#user-info .bg-blue-600').textContent = initial;
                        document.querySelector('#user-info .text-gray-900').textContent = user.name || 'Admin User';
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
</aside>