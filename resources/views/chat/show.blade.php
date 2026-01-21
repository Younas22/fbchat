@extends('layouts.app')

@section('title', 'Chat - Facebook Chat Manager')

@section('page-title', 'Chat')

@section('content')
<div class="h-screen flex bg-gray-100">
    <!-- Main Chat Area -->
    <div class="flex-1 flex flex-col">
        <!-- Chat Header -->
        <div class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <img id="headerProfilePic" src="https://ui-avatars.com/api/?name=U&background=6366f1&color=fff&size=40" alt="Profile" class="w-10 h-10 rounded-full object-cover">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900" id="customerName">Loading...</h2>
                    <p class="text-sm text-gray-600">Last message: <span id="lastMessageTime">-</span></p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="syncMessages()" id="syncBtn" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 flex items-center space-x-2 transition-colors">
                    <svg id="syncIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span id="syncText">Sync</span>
                </button>
                <button onclick="saveCurrentChat()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
                    </svg>
                    <span>Save Chat</span>
                </button>
                <button onclick="toggleCustomerDetails()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center space-x-2" title="Customer Details">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Details</span>
                </button>
                <a href="/conversations" class="px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">Back</a>
            </div>
        </div>

        <!-- Messages Area -->
        <div id="messagesArea" class="flex-1 overflow-y-auto p-8 space-y-4 bg-gray-100">
            <p class="text-center text-gray-600">Loading messages...</p>
        </div>

        <!-- Message Input -->
        <div class="bg-white border-t border-gray-200 px-8 py-4 relative">
            <!-- File Preview Area -->
            <div id="filePreviewArea" class="hidden mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div id="previewThumbnail" class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center overflow-hidden">
                            <img id="imagePreview" class="hidden w-full h-full object-cover" src="" alt="Preview">
                            <svg id="docPreviewIcon" class="hidden w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p id="fileName" class="text-sm font-medium text-gray-900 truncate max-w-xs">filename.jpg</p>
                            <p id="fileSize" class="text-xs text-gray-500">2.5 MB</p>
                        </div>
                    </div>
                    <button onclick="clearAttachment()" class="p-1 text-gray-400 hover:text-red-500 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <!-- Attachment Buttons -->
                <div class="flex items-center space-x-1">
                    <!-- Image Upload -->
                    <input type="file" id="imageInput" accept="image/*" class="hidden" onchange="handleFileSelect(event, 'image')">
                    <button onclick="document.getElementById('imageInput').click()" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Send Image">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </button>

                    <!-- Document Upload -->
                    <input type="file" id="documentInput" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar" class="hidden" onchange="handleFileSelect(event, 'document')">
                    <button onclick="document.getElementById('documentInput').click()" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Send Document">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                    </button>

                    <!-- Emoji Picker Toggle -->
                    <button onclick="toggleEmojiPicker()" class="p-2 text-gray-500 hover:text-yellow-500 hover:bg-yellow-50 rounded-lg transition" title="Emoji">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>
                </div>

                <!-- Message Input -->
                <input type="text" id="messageInput" placeholder="Type your message..." class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">

                <!-- Send Button -->
                <button onclick="sendMessage()" id="sendBtn" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center space-x-2">
                    <span>Send</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </div>

            <!-- Emoji Picker -->
            <div id="emojiPicker" class="hidden absolute bottom-full left-16 mb-2 bg-white border border-gray-200 rounded-xl shadow-2xl p-3 z-50" style="width: 340px; max-height: 320px;">
                <div class="text-xs text-gray-500 font-medium mb-2 pb-2 border-b">Emojis</div>
                <div class="grid grid-cols-8 gap-1 max-h-56 overflow-y-auto pr-1">
                    <!-- Smileys & Emotion -->
                    <button onclick="insertEmoji('😀')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😀</button>
                    <button onclick="insertEmoji('😃')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😃</button>
                    <button onclick="insertEmoji('😄')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😄</button>
                    <button onclick="insertEmoji('😁')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😁</button>
                    <button onclick="insertEmoji('😅')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😅</button>
                    <button onclick="insertEmoji('😂')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😂</button>
                    <button onclick="insertEmoji('🤣')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">🤣</button>
                    <button onclick="insertEmoji('😊')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😊</button>
                    <button onclick="insertEmoji('😇')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😇</button>
                    <button onclick="insertEmoji('🙂')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">🙂</button>
                    <button onclick="insertEmoji('😉')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😉</button>
                    <button onclick="insertEmoji('😍')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😍</button>
                    <button onclick="insertEmoji('🥰')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">🥰</button>
                    <button onclick="insertEmoji('😘')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😘</button>
                    <button onclick="insertEmoji('😋')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😋</button>
                    <button onclick="insertEmoji('😎')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😎</button>
                    <button onclick="insertEmoji('🤔')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">🤔</button>
                    <button onclick="insertEmoji('😐')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😐</button>
                    <button onclick="insertEmoji('😑')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😑</button>
                    <button onclick="insertEmoji('😶')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😶</button>
                    <button onclick="insertEmoji('🙄')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">🙄</button>
                    <button onclick="insertEmoji('😏')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😏</button>
                    <button onclick="insertEmoji('😣')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😣</button>
                    <button onclick="insertEmoji('😥')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😥</button>
                    <button onclick="insertEmoji('😮')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😮</button>
                    <button onclick="insertEmoji('😯')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😯</button>
                    <button onclick="insertEmoji('😪')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😪</button>
                    <button onclick="insertEmoji('😫')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😫</button>
                    <button onclick="insertEmoji('😴')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😴</button>
                    <button onclick="insertEmoji('😌')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😌</button>
                    <button onclick="insertEmoji('😛')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😛</button>
                    <button onclick="insertEmoji('😜')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">😜</button>
                    <!-- Gestures -->
                    <button onclick="insertEmoji('👍')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">👍</button>
                    <button onclick="insertEmoji('👎')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">👎</button>
                    <button onclick="insertEmoji('👌')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">👌</button>
                    <button onclick="insertEmoji('✌️')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">✌️</button>
                    <button onclick="insertEmoji('🤞')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">🤞</button>
                    <button onclick="insertEmoji('👋')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">👋</button>
                    <button onclick="insertEmoji('🙏')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">🙏</button>
                    <button onclick="insertEmoji('💪')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">💪</button>
                    <!-- Hearts & Love -->
                    <button onclick="insertEmoji('❤️')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">❤️</button>
                    <button onclick="insertEmoji('🧡')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">🧡</button>
                    <button onclick="insertEmoji('💛')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">💛</button>
                    <button onclick="insertEmoji('💚')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">💚</button>
                    <button onclick="insertEmoji('💙')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">💙</button>
                    <button onclick="insertEmoji('💜')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">💜</button>
                    <button onclick="insertEmoji('🖤')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">🖤</button>
                    <button onclick="insertEmoji('💔')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">💔</button>
                    <!-- Objects & Symbols -->
                    <button onclick="insertEmoji('🎉')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">🎉</button>
                    <button onclick="insertEmoji('🎊')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">🎊</button>
                    <button onclick="insertEmoji('🎁')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">🎁</button>
                    <button onclick="insertEmoji('🔥')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">🔥</button>
                    <button onclick="insertEmoji('⭐')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">⭐</button>
                    <button onclick="insertEmoji('✨')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">✨</button>
                    <button onclick="insertEmoji('💯')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">💯</button>
                    <button onclick="insertEmoji('✅')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">✅</button>
                    <button onclick="insertEmoji('❌')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">❌</button>
                    <button onclick="insertEmoji('❓')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">❓</button>
                    <button onclick="insertEmoji('❗')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">❗</button>
                    <button onclick="insertEmoji('💬')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">💬</button>
                    <button onclick="insertEmoji('📷')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">📷</button>
                    <button onclick="insertEmoji('📱')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">📱</button>
                    <button onclick="insertEmoji('💻')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">💻</button>
                    <button onclick="insertEmoji('📧')" class="text-xl hover:bg-blue-50 hover:scale-110 p-1.5 rounded-lg transition-transform duration-150 cursor-pointer">📧</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Details Sidebar -->
    <div id="customerDetailsSidebar" class="w-80 bg-white border-l border-gray-200 flex-shrink-0 overflow-y-auto">
        <div class="p-6">
            <!-- Profile Section -->
            <div class="text-center mb-6">
                <img id="customerProfilePic" src="https://ui-avatars.com/api/?name=U&background=6366f1&color=fff&size=128" alt="Customer" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover bg-gray-200">
                <h3 id="sidebarCustomerName" class="text-xl font-semibold text-gray-900">Loading...</h3>
                <p class="text-sm text-gray-500">Facebook Customer</p>
            </div>

            <!-- Customer Info -->
            <div class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Facebook ID</h4>
                    <p id="customerFbId" class="text-gray-900 font-mono text-sm break-all">-</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Page-Scoped ID (PSID)</h4>
                    <p id="customerPsid" class="text-gray-900 font-mono text-sm break-all">-</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Last Active</h4>
                    <p id="customerLastActive" class="text-gray-900">-</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Status</h4>
                    <span id="customerStatus" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Active
                    </span>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 space-y-3">
                <h4 class="text-sm font-medium text-gray-500 mb-3">Quick Actions</h4>
                <a id="facebookProfileLink" href="#" target="_blank" class="hidden flex items-center space-x-3 p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span class="text-blue-700 font-medium">View Facebook Profile</span>
                </a>
                <div id="noFbIdMessage" class="hidden flex items-center space-x-3 p-3 bg-gray-100 rounded-lg text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm">FB Profile ID not available</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Save Chat Modal -->
<div id="saveChatModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Save Chat</h3>
        <textarea id="saveNotes" placeholder="Add notes about this conversation..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 mb-4" rows="5"></textarea>
        <div class="flex space-x-4">
            <button onclick="closeSaveModal()" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
            <button onclick="confirmSave()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const conversationId = "{{ $conversationId }}";
    let sidebarVisible = true;
    let selectedFile = null;
    let selectedFileType = null;

    function updateCustomerDetails(conversation) {
        if (!conversation) return;

        // Update header
        document.getElementById('customerName').textContent = conversation.customer_name || 'Unknown Customer';

        // Update header profile pic
        const headerPic = document.getElementById('headerProfilePic');
        const headerFallback = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(conversation.customer_name || 'U') + '&background=6366f1&color=fff&size=40';
        if (conversation.customer_profile_pic && conversation.customer_profile_pic.trim() !== '') {
            headerPic.src = conversation.customer_profile_pic;
            headerPic.onerror = function() { this.src = headerFallback; };
        } else {
            headerPic.src = headerFallback;
        }

        // Update sidebar
        document.getElementById('sidebarCustomerName').textContent = conversation.customer_name || 'Unknown Customer';
        document.getElementById('customerFbId').textContent = conversation.customer_fb_id || '-';
        document.getElementById('customerPsid').textContent = conversation.customer_psid || '-';

        // Update profile picture
        const profilePic = document.getElementById('customerProfilePic');
        const sidebarFallback = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(conversation.customer_name || 'U') + '&background=6366f1&color=fff&size=128';
        if (conversation.customer_profile_pic && conversation.customer_profile_pic.trim() !== '') {
            profilePic.src = conversation.customer_profile_pic;
            profilePic.onerror = function() { this.src = sidebarFallback; };
        } else {
            profilePic.src = sidebarFallback;
        }

        // Update last active
        if (conversation.last_message_time) {
            const date = new Date(conversation.last_message_time);
            document.getElementById('customerLastActive').textContent = date.toLocaleString();
            document.getElementById('lastMessageTime').textContent = date.toLocaleString();
        }

        // Update status badge
        const statusEl = document.getElementById('customerStatus');
        if (conversation.is_archived) {
            statusEl.textContent = 'Archived';
            statusEl.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800';
        } else {
            statusEl.textContent = 'Active';
            statusEl.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';
        }

        // Update Facebook profile link (only if real FB ID is available)
        const fbProfileLink = document.getElementById('facebookProfileLink');
        const noFbIdMessage = document.getElementById('noFbIdMessage');

        if (conversation.customer_fb_id) {
            fbProfileLink.href = `https://www.facebook.com/${conversation.customer_fb_id}`;
            fbProfileLink.classList.remove('hidden');
            noFbIdMessage.classList.add('hidden');
        } else {
            fbProfileLink.classList.add('hidden');
            noFbIdMessage.classList.remove('hidden');
        }
    }

    function toggleCustomerDetails() {
        const sidebar = document.getElementById('customerDetailsSidebar');
        sidebarVisible = !sidebarVisible;
        sidebar.classList.toggle('hidden', !sidebarVisible);
    }

    async function loadMessages() {
        try {
            // Wait for ensureAuthenticated to be available
            while (!window.ensureAuthenticated) {
                await new Promise(resolve => setTimeout(resolve, 100));
            }

            await window.ensureAuthenticated();

            const res = await axios.get(`${API_BASE}/chat/${conversationId}/messages`);
            const messages = res.data.data || [];

            // Update customer details from conversation data
            if (res.data.conversation) {
                updateCustomerDetails(res.data.conversation);
            }

            if (messages.length === 0) {
                document.getElementById('messagesArea').innerHTML = '<p class="text-center text-gray-600">No messages yet</p>';
                return;
            }

            const html = messages.map(msg => {
                // Check if message is from page or customer (using sender_type from database)
                const isFromPage = msg.sender_type === 'page';
                const messageText = msg.message_text || '';

                // Format time in 12-hour format
                const timestamp = msg.sent_at ? formatTime12Hour(new Date(msg.sent_at)) : '';

                // Convert /storage/ URLs to /files/ URLs (to fix 403 symlink issue)
                let attachmentUrl = msg.attachment_url || null;
                if (attachmentUrl && attachmentUrl.includes('/storage/')) {
                    attachmentUrl = attachmentUrl.replace('/storage/', '/files/');
                }

                // Get attachment type - if null, try to detect from URL
                let attachmentType = msg.attachment_type || null;
                if (attachmentUrl && !attachmentType) {
                    attachmentType = detectAttachmentType(attachmentUrl);
                }

                // Message status (seen, delivered, sent)
                const status = msg.status || (isFromPage ? 'sent' : null);
                const statusIcon = getStatusIcon(status, isFromPage);

                let contentHtml = '';

                // Handle attachments
                if (attachmentUrl) {
                    if (attachmentType === 'image') {
                        contentHtml = `
                            <img src="${attachmentUrl}" alt="Image" class="max-w-full rounded-lg mb-2 cursor-pointer hover:opacity-90" onclick="openImageModal('${attachmentUrl}')" style="max-height: 200px;">
                            ${messageText && messageText.trim() !== '' && messageText.trim() !== ' ' ? `<p>${messageText}</p>` : ''}
                        `;
                    } else if (attachmentType === 'file' || attachmentType === 'document') {
                        const fileName = attachmentUrl.split('/').pop();
                        contentHtml = `
                            <a href="${attachmentUrl}" target="_blank" class="flex items-center space-x-2 p-2 ${isFromPage ? 'bg-blue-500' : 'bg-gray-100'} rounded-lg mb-2 hover:opacity-80">
                                <svg class="w-8 h-8 ${isFromPage ? 'text-white' : 'text-gray-600'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span class="text-sm truncate max-w-[150px]">${fileName}</span>
                            </a>
                            ${messageText && messageText.trim() !== '' && messageText.trim() !== ' ' ? `<p>${messageText}</p>` : ''}
                        `;
                    } else if (attachmentType === 'video') {
                        contentHtml = `
                            <video src="${attachmentUrl}" controls class="max-w-full rounded-lg mb-2" style="max-height: 200px;"></video>
                            ${messageText && messageText.trim() !== '' && messageText.trim() !== ' ' ? `<p>${messageText}</p>` : ''}
                        `;
                    } else if (attachmentType === 'audio') {
                        contentHtml = `
                            <audio src="${attachmentUrl}" controls class="w-full mb-2"></audio>
                            ${messageText && messageText.trim() !== '' && messageText.trim() !== ' ' ? `<p>${messageText}</p>` : ''}
                        `;
                    } else {
                        contentHtml = `<p>${messageText}</p>`;
                    }
                } else {
                    contentHtml = `<p>${messageText}</p>`;
                }

                return `
                    <div class="flex ${isFromPage ? 'justify-end' : 'justify-start'}">
                        <div class="max-w-xs px-4 py-3 rounded-lg ${isFromPage ? 'bg-blue-600 text-white' : 'bg-white text-gray-900 border border-gray-300'}">
                            ${contentHtml}
                            <div class="flex items-center justify-end space-x-1 mt-2">
                                <span class="text-xs ${isFromPage ? 'text-blue-100' : 'text-gray-500'}">${timestamp}</span>
                                ${statusIcon}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            document.getElementById('messagesArea').innerHTML = html;
            document.getElementById('messagesArea').scrollTop = document.getElementById('messagesArea').scrollHeight;
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }

    async function sendMessage() {
        const message = document.getElementById('messageInput').value.trim();
        const hasFile = selectedFile !== null;

        if (!message && !hasFile) return;

        const sendBtn = document.getElementById('sendBtn');
        sendBtn.disabled = true;
        sendBtn.classList.add('opacity-50', 'cursor-not-allowed');

        try {
            // Wait for ensureAuthenticated to be available
            while (!window.ensureAuthenticated) {
                await new Promise(resolve => setTimeout(resolve, 100));
            }

            await window.ensureAuthenticated();

            let res;

            if (hasFile) {
                // Send with attachment using FormData
                const formData = new FormData();
                // If no message, send a space or empty placeholder for file-only sends
                formData.append('message', message || ' ');
                formData.append('attachment', selectedFile);
                formData.append('attachment_type', selectedFileType);

                res = await axios.post(`${API_BASE}/chat/${conversationId}/send`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
            } else {
                // Send text only
                res = await axios.post(`${API_BASE}/chat/${conversationId}/send`, { message });
            }

            if (res.data.success) {
                document.getElementById('messageInput').value = '';
                clearAttachment();
                loadMessages();
            }
        } catch (error) {
            alert('Error sending message: ' + (error.response?.data?.message || error.message));
        } finally {
            sendBtn.disabled = false;
            sendBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    // File handling functions
    function handleFileSelect(event, type) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file size (max 25MB for Facebook)
        const maxSize = 25 * 1024 * 1024; // 25MB
        if (file.size > maxSize) {
            alert('File size must be less than 25MB');
            event.target.value = '';
            return;
        }

        selectedFile = file;
        selectedFileType = type;

        // Show preview area
        document.getElementById('filePreviewArea').classList.remove('hidden');
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = formatFileSize(file.size);

        // Show appropriate preview
        const imagePreview = document.getElementById('imagePreview');
        const docPreviewIcon = document.getElementById('docPreviewIcon');

        if (type === 'image') {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.classList.remove('hidden');
                docPreviewIcon.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            imagePreview.classList.add('hidden');
            docPreviewIcon.classList.remove('hidden');
        }

        // Reset file input
        event.target.value = '';
    }

    function clearAttachment() {
        selectedFile = null;
        selectedFileType = null;
        document.getElementById('filePreviewArea').classList.add('hidden');
        document.getElementById('imagePreview').src = '';
        document.getElementById('imagePreview').classList.add('hidden');
        document.getElementById('docPreviewIcon').classList.add('hidden');
        document.getElementById('imageInput').value = '';
        document.getElementById('documentInput').value = '';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Detect attachment type from URL
    function detectAttachmentType(url) {
        if (!url) return null;

        const lowerUrl = url.toLowerCase();

        // Check for image extensions
        if (lowerUrl.match(/\.(jpg|jpeg|png|gif|webp|bmp|svg)(\?|$)/)) {
            return 'image';
        }

        // Check for video extensions
        if (lowerUrl.match(/\.(mp4|mov|avi|wmv|webm|mkv)(\?|$)/)) {
            return 'video';
        }

        // Check for audio extensions
        if (lowerUrl.match(/\.(mp3|wav|ogg|m4a|aac|flac)(\?|$)/)) {
            return 'audio';
        }

        // Check for document extensions
        if (lowerUrl.match(/\.(pdf|doc|docx|xls|xlsx|ppt|pptx|txt|zip|rar)(\?|$)/)) {
            return 'file';
        }

        // Check Facebook CDN patterns for images
        if (lowerUrl.includes('scontent') && (lowerUrl.includes('.fbcdn.net') || lowerUrl.includes('.xx.fbcdn.net'))) {
            return 'image';
        }

        // Default to file if we have a URL but can't determine type
        return 'file';
    }

    // Format time in 12-hour format with AM/PM
    function formatTime12Hour(date) {
        let hours = date.getHours();
        const minutes = date.getMinutes().toString().padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // 0 should be 12
        return `${hours}:${minutes} ${ampm}`;
    }

    // Get status icon for message (sent, delivered, seen)
    function getStatusIcon(status, isFromPage) {
        if (!isFromPage) return ''; // No status icon for received messages

        const iconColor = 'text-blue-100';

        switch(status) {
            case 'seen':
            case 'read':
                // Double check mark (blue/filled) for seen
                return `<svg class="w-4 h-4 ${iconColor}" fill="currentColor" viewBox="0 0 24 24" title="Seen">
                    <path d="M18 7l-1.41-1.41-6.34 6.34 1.41 1.41L18 7zm4.24-1.41L11.66 16.17 7.48 12l-1.41 1.41L11.66 19l12-12-1.42-1.41zM.41 13.41L6 19l1.41-1.41L1.83 12 .41 13.41z"/>
                </svg>`;
            case 'delivered':
                // Double check mark (outline) for delivered
                return `<svg class="w-4 h-4 ${iconColor}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" title="Delivered">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m0 0l4-4m-4 4l-4 4"/>
                </svg>`;
            case 'sent':
            default:
                // Single check mark for sent
                return `<svg class="w-4 h-4 ${iconColor}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" title="Sent">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>`;
        }
    }

    // Emoji picker functions
    function toggleEmojiPicker() {
        const picker = document.getElementById('emojiPicker');
        picker.classList.toggle('hidden');
    }

    function insertEmoji(emoji) {
        const input = document.getElementById('messageInput');
        const start = input.selectionStart;
        const end = input.selectionEnd;
        const text = input.value;
        input.value = text.substring(0, start) + emoji + text.substring(end);
        input.focus();
        input.selectionStart = input.selectionEnd = start + emoji.length;
    }

    // Image modal for viewing full size images
    function openImageModal(imageUrl) {
        const modal = document.createElement('div');
        modal.id = 'imageModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
        modal.onclick = function(e) { if (e.target === modal) closeImageModal(); };
        modal.innerHTML = `
            <div class="relative max-w-4xl max-h-[90vh] p-4">
                <button onclick="closeImageModal()" class="absolute top-2 right-2 text-white hover:text-gray-300 z-10">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <img src="${imageUrl}" class="max-w-full max-h-[85vh] rounded-lg" alt="Full size image">
            </div>
        `;
        document.body.appendChild(modal);
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        if (modal) modal.remove();
    }

    // Close emoji picker when clicking outside
    document.addEventListener('click', function(e) {
        const picker = document.getElementById('emojiPicker');
        const emojiBtn = e.target.closest('button[title="Emoji"]');
        if (!picker.contains(e.target) && !emojiBtn) {
            picker.classList.add('hidden');
        }
    });

    function saveCurrentChat() {
        document.getElementById('saveChatModal').classList.remove('hidden');
    }

    function closeSaveModal() {
        document.getElementById('saveChatModal').classList.add('hidden');
    }

    async function confirmSave() {
        const notes = document.getElementById('saveNotes').value;
        try {
            // Wait for ensureAuthenticated to be available
            while (!window.ensureAuthenticated) {
                await new Promise(resolve => setTimeout(resolve, 100));
            }

            await window.ensureAuthenticated();

            const res = await axios.post(`${API_BASE}/saved-chats/${conversationId}`, { notes });
            if (res.data.success) {
                alert('Chat saved successfully!');
                closeSaveModal();
                document.getElementById('saveNotes').value = '';
            }
        } catch (error) {
            alert('Error: ' + (error.response?.data?.message || error.message));
        }
    }

    async function syncMessages() {
        const btn = document.getElementById('syncBtn');
        const icon = document.getElementById('syncIcon');
        const text = document.getElementById('syncText');

        // Disable button and show syncing state
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        text.textContent = 'Syncing...';
        icon.classList.add('animate-spin');

        try {
            // Wait for ensureAuthenticated to be available
            while (!window.ensureAuthenticated) {
                await new Promise(resolve => setTimeout(resolve, 100));
            }

            await window.ensureAuthenticated();

            const res = await axios.post(`${API_BASE}/chat/${conversationId}/sync`);

            if (res.data.success) {
                // Reload messages after successful sync
                await loadMessages();

                const newMsgs = res.data.new_messages || 0;
                if (newMsgs > 0) {
                    text.textContent = `+${newMsgs} new!`;
                } else {
                    text.textContent = 'Up to date';
                }

                // Reset button after 2 seconds
                setTimeout(() => {
                    text.textContent = 'Sync';
                    icon.classList.remove('animate-spin');
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                }, 2000);
            } else {
                throw new Error(res.data.message || 'Sync failed');
            }
        } catch (error) {
            alert('Error syncing messages: ' + (error.response?.data?.message || error.message));
            text.textContent = 'Sync';
            icon.classList.remove('animate-spin');
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    // Load messages initially
    loadMessages();

    // Listen for real-time messages via WebSocket (Reverb)
    if (window.Echo) {
        console.log('Setting up WebSocket listener for conversation:', conversationId);

        window.Echo.private(`conversation.${conversationId}`)
            .listen('MessageSent', (event) => {
                console.log('New message received via WebSocket:', event);
                loadMessages(); // Reload messages when new message arrives
            });
    } else {
        console.warn('Echo (WebSocket) not available. Real-time updates disabled.');
        console.log('Use manual Sync button to check for new messages.');
        // Note: Auto-sync disabled to save Facebook API calls
        // Users can click "Sync" button manually to check for new messages
    }

    // Send message on Enter key
    document.getElementById('messageInput').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });
</script>
@endsection