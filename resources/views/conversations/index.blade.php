@extends('layouts.app')

@section('title', 'Chat Dashboard - Facebook Chat Manager')

@section('page-title', 'Chat Dashboard')
@section('page-subtitle', 'Manage all your Facebook conversations')

@section('content')
<div class="h-[calc(100vh-64px)] flex flex-col bg-slate-50">
    <!-- Header Section -->
    <div class="flex-shrink-0 bg-white border-b border-slate-200 px-4 py-3">
        <div class="flex items-center justify-between gap-4">
            <!-- Page Selector -->
            <div class="flex items-center gap-3 flex-1">
                <div class="relative">
                    <select id="pageSelector"
                            onchange="onPageChange()"
                            class="appearance-none bg-white border border-slate-200 rounded-xl pl-4 pr-10 py-2.5 text-sm font-medium text-slate-700 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer min-w-[200px]">
                        <option value="all">All Pages</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>

                <!-- Sync Button -->
                <button id="syncBtn"
                        onclick="syncPage()"
                        disabled
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg id="syncIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span id="syncText">Sync</span>
                </button>
            </div>

            <!-- Mobile Menu Toggle -->
            <button id="mobileConversationsToggle"
                    onclick="toggleMobileConversations()"
                    class="lg:hidden inline-flex items-center justify-center w-10 h-10 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 flex overflow-hidden">
        <!-- Left Sidebar - Conversations List -->
        <div id="conversationsSidebar"
             class="w-80 flex-shrink-0 bg-white border-r border-slate-200 flex flex-col
                    fixed inset-y-0 left-0 z-40 transform -translate-x-full lg:relative lg:translate-x-0 transition-transform duration-300 ease-in-out
                    lg:transform-none pt-[120px] lg:pt-0">

            <!-- Mobile Close Button -->
            <button onclick="toggleMobileConversations()"
                    class="lg:hidden absolute top-3 right-3 w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <!-- Search Bar -->
            <div class="p-4 border-b border-slate-100">
                <div class="relative">
                    <input type="text"
                           id="searchInput"
                           placeholder="Search conversations..."
                           oninput="searchConversations()"
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Conversations List -->
            <div id="conversationsList" class="flex-1 overflow-y-auto">
                <!-- Loading State -->
                <div id="conversationsLoading" class="p-4 space-y-3">
                    <div class="animate-pulse flex items-center gap-3 p-3">
                        <div class="w-12 h-12 bg-slate-200 rounded-full"></div>
                        <div class="flex-1">
                            <div class="h-4 bg-slate-200 rounded w-3/4 mb-2"></div>
                            <div class="h-3 bg-slate-200 rounded w-1/2"></div>
                        </div>
                    </div>
                    <div class="animate-pulse flex items-center gap-3 p-3">
                        <div class="w-12 h-12 bg-slate-200 rounded-full"></div>
                        <div class="flex-1">
                            <div class="h-4 bg-slate-200 rounded w-2/3 mb-2"></div>
                            <div class="h-3 bg-slate-200 rounded w-2/5"></div>
                        </div>
                    </div>
                    <div class="animate-pulse flex items-center gap-3 p-3">
                        <div class="w-12 h-12 bg-slate-200 rounded-full"></div>
                        <div class="flex-1">
                            <div class="h-4 bg-slate-200 rounded w-1/2 mb-2"></div>
                            <div class="h-3 bg-slate-200 rounded w-3/5"></div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="conversationsEmpty" class="hidden flex flex-col items-center justify-center h-full p-6 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-medium text-slate-900 mb-1">No conversations yet</h3>
                    <p class="text-xs text-slate-500">Select a page and sync to load conversations</p>
                </div>

                <!-- Conversations Container -->
                <div id="conversationsContainer" class="hidden divide-y divide-slate-100">
                    <!-- Conversation cards will be inserted here -->
                </div>
            </div>

            <!-- Load More Button -->
            <div id="loadMoreContainer" class="hidden p-4 border-t border-slate-100">
                <button id="loadMoreBtn"
                        onclick="loadMoreConversations()"
                        class="w-full py-2.5 text-sm font-medium text-blue-600 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors">
                    Load More
                </button>
            </div>
        </div>

        <!-- Mobile Overlay -->
        <div id="mobileOverlay"
             onclick="toggleMobileConversations()"
             class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-30 lg:hidden"></div>

        <!-- Chat Box - Right Side -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50">
            <!-- Empty State (No conversation selected) -->
            <div id="chatEmptyState" class="flex-1 flex flex-col items-center justify-center p-8 text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-slate-900 mb-2">Select a conversation</h3>
                <p class="text-sm text-slate-500 max-w-sm">Choose a conversation from the left sidebar to start chatting with your customers.</p>
            </div>

            <!-- Chat Container (Hidden initially) -->
            <div id="chatContainer" class="hidden flex-1 flex flex-col min-h-0">
                <!-- Chat Header -->
                <div class="flex-shrink-0 bg-white border-b border-slate-200 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 min-w-0">
                            <!-- Back button for mobile -->
                            <button onclick="deselectConversation()"
                                    class="lg:hidden -ml-1 mr-1 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100">
                                <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>

                            <div class="relative flex-shrink-0">
                                <img id="chatHeaderAvatar"
                                     src="https://ui-avatars.com/api/?name=U&background=e2e8f0&color=475569&size=40"
                                     alt="Customer"
                                     class="w-10 h-10 rounded-full object-cover ring-2 ring-slate-100">
                                <div id="chatHeaderStatus" class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-slate-400 rounded-full border-2 border-white"></div>
                            </div>
                            <div class="min-w-0">
                                <h3 id="chatHeaderName" class="font-semibold text-slate-900 truncate">Customer Name</h3>
                                <div class="flex items-center gap-2">
                                    <span id="chatHeaderPageBadge" class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600 truncate max-w-[150px]">
                                        Page Name
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- Sync Messages -->
                            <button id="syncMessagesBtn"
                                    onclick="syncMessages()"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                                <svg id="syncMessagesIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span id="syncMessagesText" class="hidden sm:inline">Sync</span>
                            </button>

                            <!-- Save Chat -->
                            <!-- <button onclick="saveCurrentChat()"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
                                </svg>
                                <span class="hidden sm:inline">Save</span>
                            </button> -->

                            <!-- Customer Details Toggle -->
                            <button id="detailsToggle"
                                    onclick="toggleCustomerDetails()"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="hidden sm:inline">Details</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Messages Area -->
                <div id="messagesArea" class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50">
                    <!-- Load older messages button -->
                    <div id="loadOlderContainer" class="hidden text-center pb-2">
                        <button onclick="loadOlderMessages()"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                            </svg>
                            Load older messages
                        </button>
                    </div>

                    <!-- Messages will be inserted here -->
                    <div id="messagesContainer"></div>

                    <!-- Loading State -->
                    <div id="messagesLoading" class="hidden flex justify-center py-8">
                        <div class="flex items-center gap-2 text-slate-500">
                            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm">Loading messages...</span>
                        </div>
                    </div>
                </div>

                <!-- Message Input -->
                <div class="flex-shrink-0 bg-white border-t border-slate-200 p-4">
                    <!-- File Preview -->
                    <div id="filePreviewArea" class="hidden mb-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div id="previewThumbnail" class="w-12 h-12 bg-slate-200 rounded-lg flex items-center justify-center overflow-hidden">
                                    <img id="imagePreview" class="hidden w-full h-full object-cover" src="" alt="Preview">
                                    <svg id="docPreviewIcon" class="hidden w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p id="fileName" class="text-sm font-medium text-slate-900 truncate max-w-[200px]">filename.jpg</p>
                                    <p id="fileSize" class="text-xs text-slate-500">2.5 MB</p>
                                </div>
                            </div>
                            <button onclick="clearAttachment()" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-end gap-3">
                        <!-- Attachment Buttons -->
                        <div class="flex items-center gap-1">
                            <input type="file" id="imageInput" accept="image/*" class="hidden" onchange="handleFileSelect(event, 'image')">
                            <button onclick="document.getElementById('imageInput').click()"
                                    class="p-2.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-colors"
                                    title="Send Image">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </button>

                            <input type="file" id="documentInput" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar" class="hidden" onchange="handleFileSelect(event, 'document')">
                            <button onclick="document.getElementById('documentInput').click()"
                                    class="p-2.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-colors"
                                    title="Send Document">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                            </button>

                            <button onclick="toggleEmojiPicker()"
                                    class="p-2.5 text-slate-400 hover:text-amber-500 hover:bg-amber-50 rounded-xl transition-colors"
                                    title="Emoji">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Message Textarea -->
                        <div class="flex-1 relative">
                            <textarea id="messageInput"
                                      placeholder="Type your message..."
                                      rows="1"
                                      disabled
                                      class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm placeholder-slate-400 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:opacity-50 disabled:cursor-not-allowed"
                                      style="max-height: 120px;"></textarea>

                            <!-- Emoji Picker -->
                            <div id="emojiPicker" class="hidden absolute bottom-full left-0 mb-2 bg-white border border-slate-200 rounded-xl shadow-xl p-3 z-50" style="width: 320px;">
                                <div class="text-xs text-slate-500 font-medium mb-2 pb-2 border-b border-slate-100">Emojis</div>
                                <div class="grid grid-cols-8 gap-1 max-h-48 overflow-y-auto">
                                    <button onclick="insertEmoji('😀')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">😀</button>
                                    <button onclick="insertEmoji('😃')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">😃</button>
                                    <button onclick="insertEmoji('😄')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">😄</button>
                                    <button onclick="insertEmoji('😁')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">😁</button>
                                    <button onclick="insertEmoji('😅')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">😅</button>
                                    <button onclick="insertEmoji('😂')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">😂</button>
                                    <button onclick="insertEmoji('🤣')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">🤣</button>
                                    <button onclick="insertEmoji('😊')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">😊</button>
                                    <button onclick="insertEmoji('😇')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">😇</button>
                                    <button onclick="insertEmoji('🙂')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">🙂</button>
                                    <button onclick="insertEmoji('😉')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">😉</button>
                                    <button onclick="insertEmoji('😍')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">😍</button>
                                    <button onclick="insertEmoji('🥰')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">🥰</button>
                                    <button onclick="insertEmoji('😘')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">😘</button>
                                    <button onclick="insertEmoji('😋')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">😋</button>
                                    <button onclick="insertEmoji('😎')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">😎</button>
                                    <button onclick="insertEmoji('🤔')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">🤔</button>
                                    <button onclick="insertEmoji('😐')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">😐</button>
                                    <button onclick="insertEmoji('😑')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">😑</button>
                                    <button onclick="insertEmoji('😶')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">😶</button>
                                    <button onclick="insertEmoji('🙄')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">🙄</button>
                                    <button onclick="insertEmoji('😏')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">😏</button>
                                    <button onclick="insertEmoji('👍')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">👍</button>
                                    <button onclick="insertEmoji('👎')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">👎</button>
                                    <button onclick="insertEmoji('👌')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">👌</button>
                                    <button onclick="insertEmoji('✌️')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">✌️</button>
                                    <button onclick="insertEmoji('🤞')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">🤞</button>
                                    <button onclick="insertEmoji('👋')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">👋</button>
                                    <button onclick="insertEmoji('🙏')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">🙏</button>
                                    <button onclick="insertEmoji('💪')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">💪</button>
                                    <button onclick="insertEmoji('❤️')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">❤️</button>
                                    <button onclick="insertEmoji('🧡')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">🧡</button>
                                    <button onclick="insertEmoji('💛')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">💛</button>
                                    <button onclick="insertEmoji('💚')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">💚</button>
                                    <button onclick="insertEmoji('💙')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">💙</button>
                                    <button onclick="insertEmoji('💜')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">💜</button>
                                    <button onclick="insertEmoji('🎉')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">🎉</button>
                                    <button onclick="insertEmoji('🔥')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">🔥</button>
                                    <button onclick="insertEmoji('⭐')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">⭐</button>
                                    <button onclick="insertEmoji('✨')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">✨</button>
                                    <button onclick="insertEmoji('💯')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">💯</button>
                                    <button onclick="insertEmoji('✅')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">✅</button>
                                    <button onclick="insertEmoji('❌')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">❌</button>
                                    <button onclick="insertEmoji('❓')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">❓</button>
                                    <button onclick="insertEmoji('❗')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">❗</button>
                                    <button onclick="insertEmoji('💬')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">💬</button>
                                    <button onclick="insertEmoji('📷')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">📷</button>
                                    <button onclick="insertEmoji('📱')" class="text-xl hover:bg-slate-100 p-1.5 rounded-lg transition-colors">📱</button>
                                </div>
                            </div>
                        </div>

                        <!-- Send Button -->
                        <button id="sendBtn"
                                onclick="sendMessage()"
                                disabled
                                class="flex-shrink-0 p-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Details Sidebar (Hidden by default) -->
        <div id="customerDetailsSidebar" class="hidden w-80 flex-shrink-0 bg-white border-l border-slate-200 overflow-y-auto">
            <div class="p-6">
                <!-- Close Button -->
                <div class="flex justify-end mb-4">
                    <button onclick="toggleCustomerDetails()" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Profile Section -->
                <div class="text-center mb-6">
                    <img id="customerProfilePic"
                         src="https://ui-avatars.com/api/?name=U&background=e2e8f0&color=475569&size=96"
                         alt="Customer"
                         class="w-24 h-24 rounded-full mx-auto mb-4 object-cover ring-4 ring-slate-100">
                    <h3 id="sidebarCustomerName" class="text-lg font-semibold text-slate-900">Customer Name</h3>
                    <p class="text-sm text-slate-500">Facebook Customer</p>
                </div>

                <!-- Customer Info -->
                <div class="space-y-4">
                    <div class="bg-slate-50 rounded-xl p-4">
                        <h4 class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Facebook ID</h4>
                        <p id="customerFbId" class="text-sm text-slate-900 font-mono break-all">-</p>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-4">
                        <h4 class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Page-Scoped ID (PSID)</h4>
                        <p id="customerPsid" class="text-sm text-slate-900 font-mono break-all">-</p>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-4">
                        <h4 class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Last Active</h4>
                        <p id="customerLastActive" class="text-sm text-slate-900">-</p>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-4">
                        <h4 class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-2">Status</h4>
                        <span id="customerStatus" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Active
                        </span>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-6 space-y-3">
                    <h4 class="text-xs font-medium text-slate-500 uppercase tracking-wider">Quick Actions</h4>
                    <a id="facebookProfileLink" href="#" target="_blank" class="hidden flex items-center gap-3 p-3 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors">
                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        <span class="text-sm font-medium text-blue-700">View Facebook Profile</span>
                    </a>
                    <div id="noFbIdMessage" class="hidden flex items-center gap-3 p-3 bg-slate-100 rounded-xl text-slate-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm">FB Profile ID not available</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="hidden fixed inset-0 z-50 bg-black/90 flex items-center justify-center" onclick="closeImageModal(event)">
    <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white/80 hover:text-white transition-colors">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
    <img id="modalImage" src="" alt="Full size" class="max-w-[90vw] max-h-[90vh] rounded-lg">
</div>

@endsection

@section('scripts')
<script>
    // =============================================
    // STATE MANAGEMENT
    // =============================================
    let selectedPageId = 'all';
    let selectedConversationId = null;
    let pages = [];
    let conversations = [];
    let messages = [];
    let currentPage = 1;
    let hasMoreConversations = false;
    let selectedFile = null;
    let selectedFileType = null;
    let currentConversation = null;
    let detailsSidebarVisible = false;

    // =============================================
    // INITIALIZATION
    // =============================================
    async function init() {
        // Wait for auth
        while (!window.ensureAuthenticated) {
            await new Promise(resolve => setTimeout(resolve, 100));
        }
        await window.ensureAuthenticated();

        // Load pages for dropdown
        await loadPages();

        // Auto-select first page if available and load conversations
        if (pages.length > 0) {
            // Keep "all" selected but load conversations
            await loadConversations();
        }

        // Setup textarea auto-resize
        setupTextareaAutoResize();
    }

    // =============================================
    // PAGES MANAGEMENT
    // =============================================
    async function loadPages() {
        try {
            const res = await axios.get(`${API_BASE}/pages`);
            pages = res.data || [];

            const select = document.getElementById('pageSelector');
            let optionsHTML = '<option value="all">All Pages</option>';

            pages.forEach(page => {
                optionsHTML += `<option value="${page.id}" data-pic="${page.page_profile_pic || ''}">${page.page_name}</option>`;
            });

            select.innerHTML = optionsHTML;

            // Update sync button state
            updateSyncButtonState();
        } catch (error) {
            console.error('Error loading pages:', error);
            showToast('error', 'Failed to load pages');
        }
    }

    function onPageChange() {
        const select = document.getElementById('pageSelector');
        selectedPageId = select.value;
        currentPage = 1;
        conversations = [];

        // Update sync button state
        updateSyncButtonState();

        // Deselect conversation
        deselectConversation();

        // Load conversations for selected page
        loadConversations();
    }

    function updateSyncButtonState() {
        const syncBtn = document.getElementById('syncBtn');
        syncBtn.disabled = selectedPageId === 'all';
    }

    async function syncPage() {
        if (selectedPageId === 'all') {
            showToast('error', 'Please select a specific page to sync');
            return;
        }

        const btn = document.getElementById('syncBtn');
        const icon = document.getElementById('syncIcon');
        const text = document.getElementById('syncText');

        btn.disabled = true;
        icon.classList.add('animate-spin');
        text.textContent = 'Syncing...';

        try {
            const res = await axios.post(`${API_BASE}/conversations/${selectedPageId}/sync`);

            text.textContent = 'Synced!';
            icon.classList.remove('animate-spin');

            // Reload conversations
            currentPage = 1;
            await loadConversations();

            showToast('success', res.data.message || 'Conversations synced successfully');

            setTimeout(() => {
                text.textContent = 'Sync';
                btn.disabled = false;
            }, 2000);
        } catch (error) {
            console.error('Sync error:', error);
            showToast('error', error.response?.data?.message || 'Failed to sync conversations');
            text.textContent = 'Sync';
            icon.classList.remove('animate-spin');
            btn.disabled = false;
        }
    }

    // =============================================
    // CONVERSATIONS MANAGEMENT
    // =============================================
    async function loadConversations(append = false) {
        const loadingEl = document.getElementById('conversationsLoading');
        const emptyEl = document.getElementById('conversationsEmpty');
        const containerEl = document.getElementById('conversationsContainer');
        const loadMoreContainer = document.getElementById('loadMoreContainer');

        if (!append) {
            loadingEl.classList.remove('hidden');
            emptyEl.classList.add('hidden');
            containerEl.classList.add('hidden');
            loadMoreContainer.classList.add('hidden');
        }

        try {
            let url;
            if (selectedPageId === 'all') {
                // Load from all pages - we need to aggregate
                const allConversations = [];
                for (const page of pages) {
                    try {
                        const res = await axios.get(`${API_BASE}/conversations/${page.id}`);
                        const pageConvs = res.data.data?.data || res.data.data || [];
                        pageConvs.forEach(conv => {
                            conv.page_name = page.page_name;
                            conv.page_id = page.id;
                        });
                        allConversations.push(...pageConvs);
                    } catch (e) {
                        console.warn(`Failed to load conversations for page ${page.id}:`, e);
                    }
                }
                // Sort by last message time
                allConversations.sort((a, b) => new Date(b.last_message_time) - new Date(a.last_message_time));
                conversations = allConversations;
                hasMoreConversations = false;
            } else {
                const res = await axios.get(`${API_BASE}/conversations/${selectedPageId}?page=${currentPage}`);
                const pageData = res.data.data || res.data;
                const newConversations = pageData.data || pageData || [];

                // Add page info
                const selectedPage = pages.find(p => p.id == selectedPageId);
                newConversations.forEach(conv => {
                    conv.page_name = selectedPage?.page_name || 'Unknown Page';
                    conv.page_id = selectedPageId;
                });

                if (append) {
                    conversations = [...conversations, ...newConversations];
                } else {
                    conversations = newConversations;
                }

                hasMoreConversations = pageData.next_page_url != null;
            }

            loadingEl.classList.add('hidden');

            if (conversations.length === 0) {
                emptyEl.classList.remove('hidden');
                containerEl.classList.add('hidden');
            } else {
                emptyEl.classList.add('hidden');
                containerEl.classList.remove('hidden');
                renderConversations();
            }

            loadMoreContainer.classList.toggle('hidden', !hasMoreConversations);
        } catch (error) {
            console.error('Error loading conversations:', error);
            loadingEl.classList.add('hidden');
            emptyEl.classList.remove('hidden');
            showToast('error', 'Failed to load conversations');
        }
    }

    function renderConversations() {
        const container = document.getElementById('conversationsContainer');
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();

        const filteredConversations = conversations.filter(conv =>
            conv.customer_name?.toLowerCase().includes(searchTerm)
        );

        if (filteredConversations.length === 0) {
            container.innerHTML = `
                <div class="p-6 text-center">
                    <p class="text-sm text-slate-500">No conversations found</p>
                </div>
            `;
            return;
        }

        container.innerHTML = filteredConversations.map(conv => {
            const isActive = conv.id === selectedConversationId;
            const lastMessage = conv.last_message_preview || 'No messages';
            const truncatedMessage = lastMessage.length > 50 ? lastMessage.substring(0, 50) + '...' : lastMessage;
            const timeAgo = formatTimestamp(conv.last_message_time);
            const avatarUrl = conv.customer_profile_pic || `https://ui-avatars.com/api/?name=${encodeURIComponent(conv.customer_name || 'U')}&background=e2e8f0&color=475569&size=48`;

            return `
                <div onclick="selectConversation(${conv.id})"
                     class="flex items-center gap-3 p-4 cursor-pointer transition-all duration-200 hover:bg-slate-50 ${isActive ? 'bg-blue-50 border-l-4 border-blue-600' : 'border-l-4 border-transparent'}"
                     data-conversation-id="${conv.id}">
                    <div class="relative flex-shrink-0">
                        <img src="${avatarUrl}"
                             alt="${conv.customer_name}"
                             class="w-12 h-12 rounded-full object-cover ring-2 ring-white"
                             onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(conv.customer_name || 'U')}&background=e2e8f0&color=475569&size=48'">
                        <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-slate-400 rounded-full border-2 border-white"></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="font-medium text-slate-900 truncate text-sm">${conv.customer_name || 'Unknown'}</h4>
                            <span class="text-xs text-slate-500 flex-shrink-0 ml-2">${timeAgo}</span>
                        </div>
                        <p class="text-xs text-slate-500 truncate mb-1">${truncatedMessage}</p>
                        ${selectedPageId === 'all' ? `<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600">${conv.page_name}</span>` : ''}
                    </div>
                    ${conv.unread_count > 0 ? `<span class="flex-shrink-0 w-5 h-5 bg-red-500 text-white text-xs font-medium rounded-full flex items-center justify-center">${conv.unread_count}</span>` : ''}
                </div>
            `;
        }).join('');
    }

    function searchConversations() {
        renderConversations();
    }

    async function loadMoreConversations() {
        currentPage++;
        await loadConversations(true);
    }

    // =============================================
    // CONVERSATION SELECTION
    // =============================================
    async function selectConversation(conversationId) {
        selectedConversationId = conversationId;
        currentConversation = conversations.find(c => c.id === conversationId);

        // Update UI
        renderConversations(); // Re-render to show active state

        // Show chat container, hide empty state
        document.getElementById('chatEmptyState').classList.add('hidden');
        document.getElementById('chatContainer').classList.remove('hidden');

        // Enable message input
        document.getElementById('messageInput').disabled = false;
        document.getElementById('sendBtn').disabled = false;

        // Update chat header
        updateChatHeader();

        // Close mobile sidebar
        if (window.innerWidth < 1024) {
            toggleMobileConversations();
        }

        // Load messages
        await loadMessages();
    }

    function deselectConversation() {
        selectedConversationId = null;
        currentConversation = null;

        // Update UI
        renderConversations();

        // Hide chat container, show empty state
        document.getElementById('chatEmptyState').classList.remove('hidden');
        document.getElementById('chatContainer').classList.add('hidden');

        // Disable message input
        document.getElementById('messageInput').disabled = true;
        document.getElementById('sendBtn').disabled = true;

        // Hide customer details
        if (detailsSidebarVisible) {
            toggleCustomerDetails();
        }
    }

    function updateChatHeader() {
        if (!currentConversation) return;

        const avatarUrl = currentConversation.customer_profile_pic ||
            `https://ui-avatars.com/api/?name=${encodeURIComponent(currentConversation.customer_name || 'U')}&background=e2e8f0&color=475569&size=40`;

        document.getElementById('chatHeaderAvatar').src = avatarUrl;
        document.getElementById('chatHeaderAvatar').onerror = function() {
            this.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(currentConversation.customer_name || 'U')}&background=e2e8f0&color=475569&size=40`;
        };
        document.getElementById('chatHeaderName').textContent = currentConversation.customer_name || 'Unknown';
        document.getElementById('chatHeaderPageBadge').textContent = currentConversation.page_name || 'Unknown Page';

        // Update customer details sidebar
        updateCustomerDetails();
    }

    function updateCustomerDetails() {
        if (!currentConversation) return;

        const avatarUrl = currentConversation.customer_profile_pic ||
            `https://ui-avatars.com/api/?name=${encodeURIComponent(currentConversation.customer_name || 'U')}&background=e2e8f0&color=475569&size=96`;

        document.getElementById('customerProfilePic').src = avatarUrl;
        document.getElementById('customerProfilePic').onerror = function() {
            this.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(currentConversation.customer_name || 'U')}&background=e2e8f0&color=475569&size=96`;
        };
        document.getElementById('sidebarCustomerName').textContent = currentConversation.customer_name || 'Unknown';
        document.getElementById('customerFbId').textContent = currentConversation.customer_fb_id || '-';
        document.getElementById('customerPsid').textContent = currentConversation.customer_psid || '-';
        document.getElementById('customerLastActive').textContent = currentConversation.last_message_time ?
            new Date(currentConversation.last_message_time).toLocaleString() : '-';

        // Facebook profile link
        const fbLink = document.getElementById('facebookProfileLink');
        const noFbMsg = document.getElementById('noFbIdMessage');

        if (currentConversation.customer_fb_id) {
            fbLink.href = `https://www.facebook.com/${currentConversation.customer_fb_id}`;
            fbLink.classList.remove('hidden');
            noFbMsg.classList.add('hidden');
        } else {
            fbLink.classList.add('hidden');
            noFbMsg.classList.remove('hidden');
        }
    }

    function toggleCustomerDetails() {
        const sidebar = document.getElementById('customerDetailsSidebar');
        detailsSidebarVisible = !detailsSidebarVisible;
        sidebar.classList.toggle('hidden', !detailsSidebarVisible);
    }

    // =============================================
    // MESSAGES MANAGEMENT
    // =============================================
    async function loadMessages() {
        if (!selectedConversationId) return;

        const loadingEl = document.getElementById('messagesLoading');
        const containerEl = document.getElementById('messagesContainer');

        loadingEl.classList.remove('hidden');
        containerEl.innerHTML = '';

        try {
            const res = await axios.get(`${API_BASE}/chat/${selectedConversationId}/messages`);
            messages = res.data.data || [];

            // Update conversation data if returned
            if (res.data.conversation) {
                Object.assign(currentConversation, res.data.conversation);
                updateChatHeader();
            }

            loadingEl.classList.add('hidden');
            renderMessages();
            scrollToBottom();
        } catch (error) {
            console.error('Error loading messages:', error);
            loadingEl.classList.add('hidden');
            containerEl.innerHTML = `
                <div class="text-center py-8">
                    <p class="text-sm text-slate-500">Failed to load messages</p>
                </div>
            `;
        }
    }

    function renderMessages() {
        const container = document.getElementById('messagesContainer');

        if (messages.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <p class="text-sm text-slate-500">No messages yet. Start the conversation!</p>
                </div>
            `;
            return;
        }

        container.innerHTML = messages.map(msg => {
            const isFromPage = msg.sender_type === 'page';
            const timestamp = msg.sent_at ? formatTime12Hour(new Date(msg.sent_at)) : '';

            // Fix attachment URLs
            let attachmentUrl = msg.attachment_url || null;
            if (attachmentUrl && attachmentUrl.includes('/storage/')) {
                attachmentUrl = attachmentUrl.replace('/storage/', '/files/');
            }

            let attachmentType = msg.attachment_type || (attachmentUrl ? detectAttachmentType(attachmentUrl) : null);

            let contentHtml = '';

            // Handle attachments
            if (attachmentUrl) {
                if (attachmentType === 'image') {
                    contentHtml = `
                        <img src="${attachmentUrl}" alt="Image"
                             class="max-w-full rounded-lg mb-2 cursor-pointer hover:opacity-90"
                             onclick="openImageModal('${attachmentUrl}')"
                             style="max-height: 200px;">
                        ${msg.message_text && msg.message_text.trim() !== '' && msg.message_text.trim() !== ' ' ? `<p class="text-sm">${msg.message_text}</p>` : ''}
                    `;
                } else if (attachmentType === 'video') {
                    contentHtml = `
                        <video src="${attachmentUrl}" controls class="max-w-full rounded-lg mb-2" style="max-height: 200px;"></video>
                        ${msg.message_text && msg.message_text.trim() !== '' ? `<p class="text-sm">${msg.message_text}</p>` : ''}
                    `;
                } else if (attachmentType === 'audio') {
                    contentHtml = `
                        <audio src="${attachmentUrl}" controls class="w-full mb-2"></audio>
                        ${msg.message_text && msg.message_text.trim() !== '' ? `<p class="text-sm">${msg.message_text}</p>` : ''}
                    `;
                } else {
                    const fileName = attachmentUrl.split('/').pop();
                    contentHtml = `
                        <a href="${attachmentUrl}" target="_blank"
                           class="flex items-center gap-2 p-2 ${isFromPage ? 'bg-blue-500/20' : 'bg-slate-100'} rounded-lg mb-2 hover:opacity-80">
                            <svg class="w-6 h-6 ${isFromPage ? 'text-white' : 'text-slate-600'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="text-sm truncate max-w-[150px]">${fileName}</span>
                        </a>
                        ${msg.message_text && msg.message_text.trim() !== '' ? `<p class="text-sm">${msg.message_text}</p>` : ''}
                    `;
                }
            } else {
                contentHtml = `<p class="text-sm">${msg.message_text || ''}</p>`;
            }

            const statusIcon = getStatusIcon(msg.status, isFromPage);

            return `
                <div class="flex ${isFromPage ? 'justify-end' : 'justify-start'} mb-3">
                    <div class="max-w-[70%] ${isFromPage ? 'bg-blue-600 text-white' : 'bg-white border border-slate-200 text-slate-900'} px-4 py-3 rounded-2xl ${isFromPage ? 'rounded-br-md' : 'rounded-bl-md'} shadow-sm">
                        ${contentHtml}
                        <div class="flex items-center justify-end gap-1 mt-1">
                            <span class="text-xs ${isFromPage ? 'text-blue-100' : 'text-slate-400'}">${timestamp}</span>
                            ${statusIcon}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    async function sendMessage() {
        const input = document.getElementById('messageInput');
        const message = input.value.trim();
        const hasFile = selectedFile !== null;

        if (!message && !hasFile) return;
        if (!selectedConversationId) return;

        const sendBtn = document.getElementById('sendBtn');
        sendBtn.disabled = true;

        try {
            let res;

            if (hasFile) {
                const formData = new FormData();
                formData.append('message', message || ' ');
                formData.append('attachment', selectedFile);
                formData.append('attachment_type', selectedFileType);

                res = await axios.post(`${API_BASE}/chat/${selectedConversationId}/send`, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
            } else {
                res = await axios.post(`${API_BASE}/chat/${selectedConversationId}/send`, { message });
            }

            if (res.data.success) {
                input.value = '';
                input.style.height = 'auto';
                clearAttachment();
                await loadMessages();
            }
        } catch (error) {
            showToast('error', error.response?.data?.message || 'Failed to send message');
        } finally {
            sendBtn.disabled = false;
        }
    }

    async function syncMessages() {
        if (!selectedConversationId) return;

        const btn = document.getElementById('syncMessagesBtn');
        const icon = document.getElementById('syncMessagesIcon');
        const text = document.getElementById('syncMessagesText');

        btn.disabled = true;
        icon.classList.add('animate-spin');
        text.textContent = 'Syncing...';

        try {
            const res = await axios.post(`${API_BASE}/chat/${selectedConversationId}/sync`);

            if (res.data.success) {
                await loadMessages();
                const newMsgs = res.data.new_messages || 0;
                text.textContent = newMsgs > 0 ? `+${newMsgs} new!` : 'Up to date';

                setTimeout(() => {
                    text.textContent = 'Sync';
                    icon.classList.remove('animate-spin');
                    btn.disabled = false;
                }, 2000);
            }
        } catch (error) {
            showToast('error', error.response?.data?.message || 'Failed to sync messages');
            text.textContent = 'Sync';
            icon.classList.remove('animate-spin');
            btn.disabled = false;
        }
    }

    function loadOlderMessages() {
        // Implement if API supports pagination
        showToast('info', 'Loading older messages...');
    }

    // =============================================
    // FILE HANDLING
    // =============================================
    function handleFileSelect(event, type) {
        const file = event.target.files[0];
        if (!file) return;

        const maxSize = 25 * 1024 * 1024;
        if (file.size > maxSize) {
            showToast('error', 'File size must be less than 25MB');
            event.target.value = '';
            return;
        }

        selectedFile = file;
        selectedFileType = type;

        document.getElementById('filePreviewArea').classList.remove('hidden');
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = formatFileSize(file.size);

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

        event.target.value = '';
    }

    function clearAttachment() {
        selectedFile = null;
        selectedFileType = null;
        document.getElementById('filePreviewArea').classList.add('hidden');
        document.getElementById('imagePreview').src = '';
        document.getElementById('imagePreview').classList.add('hidden');
        document.getElementById('docPreviewIcon').classList.add('hidden');
    }

    // =============================================
    // EMOJI PICKER
    // =============================================
    function toggleEmojiPicker() {
        document.getElementById('emojiPicker').classList.toggle('hidden');
    }

    function insertEmoji(emoji) {
        const input = document.getElementById('messageInput');
        const start = input.selectionStart;
        const end = input.selectionEnd;
        const text = input.value;
        input.value = text.substring(0, start) + emoji + text.substring(end);
        input.focus();
        input.selectionStart = input.selectionEnd = start + emoji.length;
        toggleEmojiPicker();
    }

    // Close emoji picker when clicking outside
    document.addEventListener('click', function(e) {
        const picker = document.getElementById('emojiPicker');
        const emojiBtn = e.target.closest('button[title="Emoji"]');
        if (!picker.contains(e.target) && !emojiBtn) {
            picker.classList.add('hidden');
        }
    });

    // =============================================
    // IMAGE MODAL
    // =============================================
    function openImageModal(imageUrl) {
        document.getElementById('modalImage').src = imageUrl;
        document.getElementById('imageModal').classList.remove('hidden');
    }

    function closeImageModal(event) {
        if (event && event.target !== document.getElementById('imageModal') && event.target !== document.getElementById('modalImage').parentElement) {
            return;
        }
        document.getElementById('imageModal').classList.add('hidden');
    }

    // =============================================
    // MOBILE RESPONSIVE
    // =============================================
    function toggleMobileConversations() {
        const sidebar = document.getElementById('conversationsSidebar');
        const overlay = document.getElementById('mobileOverlay');

        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    // =============================================
    // UTILITY FUNCTIONS
    // =============================================
    function formatTimestamp(dateString) {
        if (!dateString) return '';

        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) return 'Just now';
        if (diffMins < 60) return `${diffMins}m ago`;
        if (diffHours < 24) return `${diffHours}h ago`;
        if (diffDays === 1) return 'Yesterday';
        if (diffDays < 7) return `${diffDays}d ago`;

        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    }

    function formatTime12Hour(date) {
        let hours = date.getHours();
        const minutes = date.getMinutes().toString().padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;
        return `${hours}:${minutes} ${ampm}`;
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function detectAttachmentType(url) {
        if (!url) return null;
        const lowerUrl = url.toLowerCase();

        if (lowerUrl.match(/\.(jpg|jpeg|png|gif|webp|bmp|svg)(\?|$)/)) return 'image';
        if (lowerUrl.match(/\.(mp4|mov|avi|wmv|webm|mkv)(\?|$)/)) return 'video';
        if (lowerUrl.match(/\.(mp3|wav|ogg|m4a|aac|flac)(\?|$)/)) return 'audio';
        if (lowerUrl.match(/\.(pdf|doc|docx|xls|xlsx|ppt|pptx|txt|zip|rar)(\?|$)/)) return 'file';
        if (lowerUrl.includes('scontent') && lowerUrl.includes('fbcdn.net')) return 'image';

        return 'file';
    }

    function getStatusIcon(status, isFromPage) {
        if (!isFromPage) return '';

        const iconColor = 'text-blue-100';

        switch(status) {
            case 'seen':
            case 'read':
                return `<svg class="w-4 h-4 ${iconColor}" fill="currentColor" viewBox="0 0 24 24" title="Seen">
                    <path d="M18 7l-1.41-1.41-6.34 6.34 1.41 1.41L18 7zm4.24-1.41L11.66 16.17 7.48 12l-1.41 1.41L11.66 19l12-12-1.42-1.41zM.41 13.41L6 19l1.41-1.41L1.83 12 .41 13.41z"/>
                </svg>`;
            case 'delivered':
                return `<svg class="w-4 h-4 ${iconColor}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" title="Delivered">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m0 0l4-4m-4 4l-4 4"/>
                </svg>`;
            default:
                return `<svg class="w-4 h-4 ${iconColor}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" title="Sent">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>`;
        }
    }

    function scrollToBottom() {
        const messagesArea = document.getElementById('messagesArea');
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }

    function setupTextareaAutoResize() {
        const textarea = document.getElementById('messageInput');

        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });
    }

    function showToast(type, message) {
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-emerald-600' : type === 'error' ? 'bg-red-600' : 'bg-blue-600';
        const icon = type === 'success'
            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
            : type === 'error'
            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';

        toast.className = `fixed bottom-4 right-4 ${bgColor} text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-3 z-50 transform transition-all duration-300`;
        toast.innerHTML = `
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icon}</svg>
            <span class="text-sm font-medium">${message}</span>
        `;

        document.body.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            toast.style.transform = 'translateY(0)';
            toast.style.opacity = '1';
        });

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // =============================================
    // REAL-TIME POLLING SYSTEM
    // =============================================
    let lastMessageId = 0;
    let lastPollTime = null;
    let messagePollingInterval = null;
    let sidebarPollingInterval = null;
    let isPollingMessages = false;
    let isInitialLoad = true;

    // Notification sound
    const notificationSound = new Audio('data:audio/mp3;base64,SUQzBAAAAAAAI1RTU0UAAAAPAAADTGF2ZjU4Ljc2LjEwMAAAAAAAAAAAAAAA//tQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAWGluZwAAAA8AAAACAAABhgC7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7u7//////////////////////////////////////////////////////////////////8AAAAATGF2YzU4LjEzAAAAAAAAAAAAAAAAJAAAAAAAAAAAAYYoRwmHAAAAAAD/+1DEAAAGAAGn9AAAIwiszv8wIBQhMgJ5dxjGMkAYBAYHA4fygIAgCD4Pg+D5//y4Pg+D4f/ygIAmD4Pn///5QEHwfB8HygIP/KAgICAhCAhCAIBAZB8HwfB8H/lAQCAhiAQEP/9YxDAMg+c5//6wfOc5znOc/8uAgIAgEAQBAEMg+D5znOc5/5cBAEAgCAIAgEP+sY5z//1g+c5znP/+XAQBAICAIYhkHwfP/rB8HwfB8HwfB8=');

    /**
     * Start real-time polling for new messages in current conversation
     */
    function startMessagePolling() {
        if (messagePollingInterval) {
            clearInterval(messagePollingInterval);
        }

        // Poll every 3 seconds for new messages
        messagePollingInterval = setInterval(pollNewMessages, 3000);
    }

    /**
     * Stop message polling
     */
    function stopMessagePolling() {
        if (messagePollingInterval) {
            clearInterval(messagePollingInterval);
            messagePollingInterval = null;
        }
    }

    /**
     * Poll for new messages in current conversation
     */
    async function pollNewMessages() {
        if (!selectedConversationId || isPollingMessages) return;

        isPollingMessages = true;

        try {
            const res = await axios.get(`${API_BASE}/chat/${selectedConversationId}/poll?last_message_id=${lastMessageId}`);

            if (res.data.success && res.data.has_new) {
                // Append new messages to the list
                const newMessages = res.data.data;
                messages = [...messages, ...newMessages];

                // Update last message ID
                if (newMessages.length > 0) {
                    lastMessageId = newMessages[newMessages.length - 1].id;
                }

                // Re-render messages and scroll to bottom
                renderMessages();
                scrollToBottom();

                // Play notification sound for customer messages
                const customerMessages = newMessages.filter(m => m.sender_type === 'customer');
                if (customerMessages.length > 0 && !isInitialLoad) {
                    playNotificationSound();
                }
            }
        } catch (error) {
            console.error('Polling error:', error);
        } finally {
            isPollingMessages = false;
            isInitialLoad = false;
        }
    }

    /**
     * Start sidebar polling for unread counts and new conversations
     */
    function startSidebarPolling() {
        if (sidebarPollingInterval) {
            clearInterval(sidebarPollingInterval);
        }

        // Poll every 5 seconds for sidebar updates
        sidebarPollingInterval = setInterval(pollSidebarUpdates, 5000);
    }

    /**
     * Poll for sidebar updates (unread counts, new messages in other conversations)
     */
    async function pollSidebarUpdates() {
        try {
            const params = new URLSearchParams();
            if (selectedPageId && selectedPageId !== 'all') {
                params.append('page_id', selectedPageId);
            }
            if (lastPollTime) {
                params.append('since', lastPollTime);
            }

            const res = await axios.get(`${API_BASE}/chat/sidebar-updates?${params.toString()}`);

            if (res.data.success) {
                // Update last poll time
                lastPollTime = res.data.server_time;

                // Update conversations with new data
                if (res.data.updated_conversations && res.data.updated_conversations.length > 0) {
                    updateConversationsFromPoll(res.data.updated_conversations);
                }

                // Update total unread badge in header if needed
                updateTotalUnreadBadge(res.data.total_unread);
            }
        } catch (error) {
            console.error('Sidebar polling error:', error);
        }
    }

    /**
     * Update conversations list from poll data
     */
    function updateConversationsFromPoll(updatedConvs) {
        let hasChanges = false;

        updatedConvs.forEach(update => {
            const existingIndex = conversations.findIndex(c => c.id === update.id);

            if (existingIndex >= 0) {
                // Update existing conversation
                const existing = conversations[existingIndex];
                if (existing.unread_count !== update.unread_count ||
                    existing.last_message_preview !== update.last_message_preview ||
                    existing.last_message_time !== update.last_message_time) {

                    conversations[existingIndex] = {
                        ...existing,
                        unread_count: update.unread_count,
                        last_message_preview: update.last_message_preview,
                        last_message_time: update.last_message_time,
                        customer_name: update.customer_name || existing.customer_name,
                        customer_profile_pic: update.customer_profile_pic || existing.customer_profile_pic
                    };
                    hasChanges = true;

                    // Play sound for new unread messages (not in currently selected conversation)
                    if (update.unread_count > existing.unread_count && update.id !== selectedConversationId) {
                        playNotificationSound();
                    }
                }
            } else {
                // New conversation - add to list
                const page = pages.find(p => p.id == update.page_id);
                conversations.unshift({
                    ...update,
                    page_name: page?.page_name || 'Unknown Page'
                });
                hasChanges = true;
                playNotificationSound();
            }
        });

        if (hasChanges) {
            // Re-sort by last_message_time
            conversations.sort((a, b) => new Date(b.last_message_time) - new Date(a.last_message_time));
            renderConversations();
        }
    }

    /**
     * Update total unread badge
     */
    function updateTotalUnreadBadge(totalUnread) {
        // Update page title with unread count
        const baseTitle = 'Chat Dashboard - Facebook Chat Manager';
        if (totalUnread > 0) {
            document.title = `(${totalUnread}) ${baseTitle}`;
        } else {
            document.title = baseTitle;
        }
    }

    /**
     * Play notification sound
     */
    function playNotificationSound() {
        try {
            notificationSound.currentTime = 0;
            notificationSound.volume = 0.5;
            notificationSound.play().catch(() => {});
        } catch (e) {}
    }

    /**
     * Override selectConversation to integrate polling
     */
    const originalSelectConversation = selectConversation;
    selectConversation = async function(conversationId) {
        // Stop current message polling
        stopMessagePolling();
        isInitialLoad = true;

        // Call original function
        await originalSelectConversation(conversationId);

        // Update lastMessageId from loaded messages
        if (messages.length > 0) {
            lastMessageId = messages[messages.length - 1].id;
        } else {
            lastMessageId = 0;
        }

        // Start polling for this conversation
        startMessagePolling();
    };

    /**
     * Override deselectConversation to stop polling
     */
    const originalDeselectConversation = deselectConversation;
    deselectConversation = function() {
        stopMessagePolling();
        lastMessageId = 0;
        originalDeselectConversation();
    };

    /**
     * Override sendMessage to update lastMessageId after sending
     */
    const originalSendMessage = sendMessage;
    sendMessage = async function() {
        await originalSendMessage();

        // Update lastMessageId from messages after send
        if (messages.length > 0) {
            lastMessageId = messages[messages.length - 1].id;
        }
    };

    // =============================================
    // INITIALIZE
    // =============================================
    init();

    // Start sidebar polling after init
    setTimeout(() => {
        startSidebarPolling();
    }, 2000);
</script>
@endsection
