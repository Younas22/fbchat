@extends('layouts.app')

@section('title', 'Saved Chats - Facebook Chat Manager')

@section('page-title', 'Saved Chats')
@section('page-subtitle', 'Your saved conversations and notes')

@section('content')
<div class="p-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div id="savedChatsList">
            <p class="text-gray-600">Loading saved chats...</p>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    async function loadSavedChats() {
        try {
            // Wait for authentication
            while (!window.ensureAuthenticated) {
                await new Promise(resolve => setTimeout(resolve, 100));
            }
            await window.ensureAuthenticated();

            const res = await axios.get(`${API_BASE}/saved-chats`);
            const chats = res.data.data.data || [];

            if (chats.length === 0) {
                document.getElementById('savedChatsList').innerHTML = '<div class="col-span-full text-center py-12"><p class="text-gray-600">No saved chats yet</p></div>';
                return;
            }

            const html = chats.map(chat => `
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">${chat.conversation.customer_name}</h4>
                            <p class="text-sm text-gray-600 mt-1">Chat ID: <span class="font-mono">${chat.chat_id.substring(0, 20)}...</span></p>
                        </div>
                        <button onclick="deleteSavedChat(${chat.id})" class="text-red-600 hover:text-red-700">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="bg-gray-50 p-3 rounded mb-4 max-h-24 overflow-y-auto">
                        <p class="text-sm text-gray-700">${chat.notes || 'No notes'}</p>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-600">
                        <span>${new Date(chat.saved_at).toLocaleDateString()}</span>
                        <button onclick="editSavedChat(${chat.id})" class="text-blue-600 hover:text-blue-700">Edit</button>
                    </div>
                </div>
            `).join('');

            document.getElementById('savedChatsList').innerHTML = html;
        } catch (error) {
            console.error('Error loading saved chats:', error);
        }
    }

    async function deleteSavedChat(chatId) {
        if (confirm('Are you sure you want to delete this saved chat?')) {
            try {
                await window.ensureAuthenticated();

                const res = await axios.delete(`${API_BASE}/saved-chats/${chatId}`);
                if (res.data.success) {
                    alert('Saved chat deleted successfully!');
                    loadSavedChats();
                }
            } catch (error) {
                alert('Error: ' + (error.response?.data?.message || error.message));
            }
        }
    }

    async function editSavedChat(chatId) {
        const notes = prompt('Update notes:');
        if (notes !== null) {
            try {
                await window.ensureAuthenticated();

                const res = await axios.patch(`${API_BASE}/saved-chats/${chatId}`, { notes });
                if (res.data.success) {
                    alert('Notes updated successfully!');
                    loadSavedChats();
                }
            } catch (error) {
                alert('Error: ' + (error.response?.data?.message || error.message));
            }
        }
    }

    loadSavedChats();
</script>
@endsection