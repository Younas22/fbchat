@extends('layouts.app')

@section('title', 'Pages - Facebook Chat Manager')

@section('page-title', 'Facebook Pages')
@section('page-subtitle', 'Manage your connected Facebook pages')

@section('content')
<div class="p-8">
    <!-- Connect Pages Button -->
    <div class="mb-8">
        <button onclick="connectPages()" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Connect Facebook Pages</span>
        </button>
    </div>

    <!-- Pages List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div id="pagesList" class="col-span-full">
            <p class="text-gray-600">Loading pages...</p>
        </div>
    </div>
</div>

<!-- Connect Modal -->
<div id="connectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Connect Facebook Pages</h3>
        <p class="text-gray-600 mb-4">This will sync all your Facebook pages and enable chat management.</p>
        <div class="flex space-x-4">
            <button onclick="closeModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
            <button onclick="confirmConnect()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Connect</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Check authentication
    async function checkAuth() {
        // Wait for axios to be available
        while (!window.axios) {
            await new Promise(resolve => setTimeout(resolve, 100));
        }

        const token = localStorage.getItem('token');
        if (!token) {
            window.location.href = '/';
            return false;
        }
        // Ensure token is in axios headers
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        return true;
    }

    async function loadPages() {
        if (!(await checkAuth())) return;

        try {
            const res = await axios.get(`${API_BASE}/pages`);
            const pages = res.data;

            if (pages.length === 0) {
                document.getElementById('pagesList').innerHTML = '<div class="col-span-full text-center py-12"><p class="text-gray-600">No pages connected yet. Click "Connect Facebook Pages" to get started.</p></div>';
                return;
            }

            const pagesHTML = pages.map(page => `
                <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
                    <div class="p-6">
                        <div class="flex items-center space-x-4 mb-4">
                            <img src="${page.page_profile_pic || 'https://via.placeholder.com/64'}" alt="${page.page_name}" class="w-16 h-16 rounded-full">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">${page.page_name}</h4>
                                <p class="text-sm text-gray-600">Connected: ${new Date(page.connected_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                        <div class="space-y-2 mb-4">
                            <p class="text-sm"><span class="text-gray-600">Page ID:</span> <span class="font-mono text-gray-900">${page.page_id}</span></p>
                            <p class="text-sm"><span class="text-gray-600">Status:</span> <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">Active</span></p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="/conversations/${page.id}" class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-center text-sm">View Chats</a>
                            <button onclick="disconnectPage(${page.id})" class="px-3 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 text-sm">Disconnect</button>
                        </div>
                    </div>
                </div>
            `).join('');

            document.getElementById('pagesList').innerHTML = pagesHTML;
        } catch (error) {
            console.error('Error loading pages:', error);
        }
    }

    function connectPages() {
        document.getElementById('connectModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('connectModal').classList.add('hidden');
    }

    async function confirmConnect() {
        closeModal();

        if (!(await checkAuth())) return;

        try {
            const res = await axios.post(`${API_BASE}/pages/connect`);
            if (res.data.success) {
                alert(`✓ ${res.data.message}`);
                loadPages();
            }
        } catch (error) {
            alert('Error: ' + error.response.data.message);
        }
    }

    async function disconnectPage(pageId) {
        if (confirm('Are you sure you want to disconnect this page?')) {
            try {
                const res = await axios.delete(`${API_BASE}/pages/${pageId}`);
                if (res.data.success) {
                    alert('Page disconnected');
                    loadPages();
                }
            } catch (error) {
                alert('Error: ' + error.response.data.message);
            }
        }
    }

    loadPages();
</script>
@endsection