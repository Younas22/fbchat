@extends('layouts.app')

@section('title', 'Conversations - Facebook Chat Manager')

@section('page-title', 'Conversations')
@section('page-subtitle', 'Manage customer conversations')

@section('content')
<div class="p-8">
    <!-- Filter & Sync Section -->
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <select id="pageFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                <option value="">All Pages</option>
            </select>
            <button id="syncBtn" onclick="syncConversations()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                <svg id="syncIcon" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span id="syncText">Sync</span>
            </button>
        </div>
    </div>

    <!-- Conversations List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div id="conversationsList" class="divide-y">
            <p class="p-8 text-gray-600 text-center">Loading conversations...</p>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let currentPageId = null;

    async function loadPages() {
        try {
            // Wait for ensureAuthenticated to be available
            while (!window.ensureAuthenticated) {
                await new Promise(resolve => setTimeout(resolve, 100));
            }

            // Always ensure auth is ready before making requests
            console.log('Waiting for authentication...');
            await window.ensureAuthenticated();

            console.log('Loading pages...');
            console.log('Current auth header:', axios.defaults.headers.common['Authorization']);
            console.log('About to call /api/pages...');
            const res = await axios.get(`${API_BASE}/pages`);
            console.log('Pages API response received:', res.status);
            const pages = res.data;
            const select = document.getElementById('pageFilter');

            const optionsHTML = pages.map(page => `<option value="${page.id}">${page.page_name}</option>`).join('');
            select.innerHTML = '<option value="">All Pages</option>' + optionsHTML;

            if (pages.length > 0) {
                select.value = pages[0].id;
                currentPageId = pages[0].id;
                loadConversations();
            }
            console.log('Pages loaded successfully:', pages.length);
        } catch (error) {
            console.error('Error loading pages:', error);
            console.error('Error details:', error.response?.data);
        }
    }

    async function loadConversations() {
        try {
            const pageId = document.getElementById('pageFilter').value || currentPageId;
            if (!pageId) return;

            const res = await axios.get(`${API_BASE}/conversations/${pageId}`);
            const conversations = res.data.data.data || [];

            if (conversations.length === 0) {
                document.getElementById('conversationsList').innerHTML = '<div class="p-8 text-center text-gray-600">No conversations yet. Click "Sync" to fetch from Facebook.</div>';
                return;
            }

            const html = conversations.map(conv => `
                <div class="p-6 hover:bg-gray-50 cursor-pointer transition" onclick="openChat(${conv.id}, '${conv.conversation_id}')">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 flex-1">
                            <img src="${conv.customer_profile_pic || 'https://via.placeholder.com/48'}"
                                 alt="${conv.customer_name}"
                                 class="w-12 h-12 rounded-full object-cover"
                                 onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(conv.customer_name)}&size=48&background=random'">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-900">${conv.customer_name}</h4>
                                <p class="text-sm text-gray-600 truncate">${conv.last_message_preview || 'No messages'}</p>
                            </div>
                        </div>
                        <div class="text-right ml-4">
                            <p class="text-sm text-gray-600">${new Date(conv.last_message_time).toLocaleDateString()}</p>
                            <button onclick="saveChat(event, ${conv.id})" class="text-blue-600 hover:text-blue-700 text-sm mt-1">Save</button>
                        </div>
                    </div>
                </div>
            `).join('');

            document.getElementById('conversationsList').innerHTML = html;
        } catch (error) {
            console.error('Error loading conversations:', error);
        }
    }

    async function syncConversations() {
        const btn = document.getElementById('syncBtn');
        const icon = document.getElementById('syncIcon');
        const text = document.getElementById('syncText');

        // Show loading state
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        icon.classList.add('animate-spin');
        text.textContent = 'Syncing...';

        try {
            // Wait for ensureAuthenticated to be available
            while (!window.ensureAuthenticated) {
                await new Promise(resolve => setTimeout(resolve, 100));
            }

            // Always ensure auth is ready before making requests
            console.log('Ensuring authentication for sync...');
            await window.ensureAuthenticated();

            const pageId = document.getElementById('pageFilter').value;
            if (!pageId) {
                alert('Please select a page first');
                resetSyncButton();
                return;
            }

            console.log('Syncing conversations for page:', pageId);
            const res = await axios.post(`${API_BASE}/conversations/${pageId}/sync`);
            console.log('Sync response:', res.data);

            // Show success state
            text.textContent = 'Synced!';
            icon.classList.remove('animate-spin');

            await loadConversations();

            // Reset button after 2 seconds
            setTimeout(resetSyncButton, 2000);
        } catch (error) {
            console.error('Sync error:', error);
            console.error('Error details:', error.response?.data);
            alert('Error: ' + (error.response?.data?.message || error.message));
            resetSyncButton();
        }
    }

    function resetSyncButton() {
        const btn = document.getElementById('syncBtn');
        const icon = document.getElementById('syncIcon');
        const text = document.getElementById('syncText');

        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
        icon.classList.remove('animate-spin');
        text.textContent = 'Sync';
    }

    function openChat(conversationId, fbConvId) {
        window.location.href = `/chat/${conversationId}`;
    }

    function saveChat(event, conversationId) {
        event.stopPropagation();
        const notes = prompt('Add notes for this chat:');
        if (notes !== null) {
            axios.post(`${API_BASE}/saved-chats/${conversationId}`, { notes })
                .then(res => alert('Chat saved!'))
                .catch(error => alert('Error: ' + error.response.data.message));
        }
    }

    document.getElementById('pageFilter').addEventListener('change', loadConversations);
    loadPages();
</script>
@endsection