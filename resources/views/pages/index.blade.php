@extends('layouts.app')

@section('title', 'Pages - Facebook Chat Manager')

@section('page-title', 'Facebook Pages')
@section('page-subtitle', 'Manage your connected Facebook pages')

@section('content')
<div class="p-6 lg:p-8">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Connected Pages</h1>
            <p class="mt-1 text-sm text-slate-500">Manage and monitor your Facebook page connections</p>
        </div>
        <button onclick="connectPages()"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Connect Pages</span>
        </button>
    </div>

    <!-- Stats Bar -->
    <div id="statsBar" class="hidden grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-semibold text-slate-900" id="totalPages">0</p>
                    <p class="text-xs text-slate-500">Total Pages</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-semibold text-slate-900" id="activePages">0</p>
                    <p class="text-xs text-slate-500">Active</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-semibold text-slate-900" id="inactivePages">0</p>
                    <p class="text-xs text-slate-500">Inactive</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-4">
            <div class="flex items-center gap-3">
                <div class="flex-shrink-0 w-10 h-10 bg-violet-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-semibold text-slate-900" id="totalConversations">-</p>
                    <p class="text-xs text-slate-500">Conversations</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="flex flex-col items-center justify-center py-20">
        <div class="relative">
            <div class="w-12 h-12 rounded-full border-4 border-slate-200"></div>
            <div class="w-12 h-12 rounded-full border-4 border-blue-600 border-t-transparent animate-spin absolute top-0 left-0"></div>
        </div>
        <p class="mt-4 text-sm text-slate-500">Loading your pages...</p>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="hidden">
        <div class="flex flex-col items-center justify-center py-20 px-4">
            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-slate-900 mb-2">No pages connected</h3>
            <p class="text-sm text-slate-500 text-center max-w-sm mb-6">Get started by connecting your Facebook pages to manage conversations and engage with your audience.</p>
            <button onclick="connectPages()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Connect Your First Page</span>
            </button>
        </div>
    </div>

    <!-- Pages Grid -->
    <div id="pagesGrid" class="hidden">
        <div id="pagesList" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5">
            <!-- Cards will be inserted here -->
        </div>

        <!-- Pagination -->
        <div id="pagination" class="hidden mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 bg-white rounded-xl border border-slate-200 px-4 py-3">
            <p class="text-sm text-slate-500">
                Showing <span id="showingFrom" class="font-medium text-slate-700">1</span> to <span id="showingTo" class="font-medium text-slate-700">20</span> of <span id="showingTotal" class="font-medium text-slate-700">0</span> pages
            </p>
            <div class="flex items-center gap-1">
                <!-- Previous Button -->
                <button id="prevBtn" onclick="goToPage(currentPage - 1)"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>

                <!-- Page Numbers -->
                <div id="pageNumbers" class="flex items-center gap-1">
                    <!-- Page buttons will be inserted here -->
                </div>

                <!-- Next Button -->
                <button id="nextBtn" onclick="goToPage(currentPage + 1)"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Connect Modal -->
<div id="connectModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md transform transition-all">
            <!-- Modal Header -->
            <div class="px-6 pt-6 pb-4">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Connect Facebook Pages</h3>
                        <p class="text-sm text-slate-500">Sync your pages to start managing chats</p>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-4 border-t border-slate-100">
                <div class="flex items-start gap-3 p-4 bg-slate-50 rounded-xl">
                    <svg class="w-5 h-5 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-slate-600">This will sync all Facebook pages associated with your account and enable real-time chat management.</p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-slate-50 rounded-b-2xl flex gap-3">
                <button onclick="closeModal()"
                        class="flex-1 px-4 py-2.5 border border-slate-200 bg-white text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-200 transition-colors">
                    Cancel
                </button>
                <button onclick="confirmConnect()"
                        class="flex-1 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Connect Pages
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md transform transition-all">
            <div class="px-6 pt-6 pb-4">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Disconnect Page</h3>
                        <p class="text-sm text-slate-500">This action cannot be undone</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100">
                <p class="text-sm text-slate-600">Are you sure you want to disconnect <span id="deletePageName" class="font-medium text-slate-900"></span>? All conversation data will be preserved but you won't receive new messages.</p>
            </div>
            <div class="px-6 py-4 bg-slate-50 rounded-b-2xl flex gap-3">
                <button onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-2.5 border border-slate-200 bg-white text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-200 transition-colors">
                    Cancel
                </button>
                <button onclick="confirmDelete()"
                        class="flex-1 px-4 py-2.5 bg-red-600 text-white text-sm font-medium rounded-xl hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                    Disconnect
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Pagination state
    let allPages = [];
    let currentPage = 1;
    const itemsPerPage = 20;
    let pageToDelete = null;

    // Check authentication
    async function checkAuth() {
        while (!window.axios) {
            await new Promise(resolve => setTimeout(resolve, 100));
        }

        const token = localStorage.getItem('token');
        if (!token) {
            window.location.href = '/';
            return false;
        }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        return true;
    }

    async function loadPages() {
        if (!(await checkAuth())) return;

        // Show loading state
        document.getElementById('loadingState').classList.remove('hidden');
        document.getElementById('emptyState').classList.add('hidden');
        document.getElementById('pagesGrid').classList.add('hidden');
        document.getElementById('statsBar').classList.add('hidden');

        try {
            const res = await axios.get(`${API_BASE}/pages`);
            allPages = res.data;

            // Hide loading
            document.getElementById('loadingState').classList.add('hidden');

            if (allPages.length === 0) {
                document.getElementById('emptyState').classList.remove('hidden');
                return;
            }

            // Update stats
            updateStats();
            document.getElementById('statsBar').classList.remove('hidden');
            document.getElementById('statsBar').classList.add('grid');

            // Show pages grid
            document.getElementById('pagesGrid').classList.remove('hidden');

            // Render first page
            currentPage = 1;
            renderPages();
            renderPagination();

        } catch (error) {
            console.error('Error loading pages:', error);
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('emptyState').classList.remove('hidden');
        }
    }

    function updateStats() {
        const total = allPages.length;
        const active = allPages.filter(p => p.is_active !== false).length;
        const inactive = total - active;

        document.getElementById('totalPages').textContent = total;
        document.getElementById('activePages').textContent = active;
        document.getElementById('inactivePages').textContent = inactive;
    }

    function renderPages() {
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pagesToShow = allPages.slice(start, end);

        const pagesHTML = pagesToShow.map(page => {
            const isActive = page.is_active !== false;
            const statusClass = isActive
                ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                : 'bg-amber-50 text-amber-700 border-amber-200';
            const statusText = isActive ? 'Active' : 'Inactive';
            const statusDot = isActive ? 'bg-emerald-500' : 'bg-amber-500';

            return `
                <div class="group bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-lg hover:shadow-slate-200/50 hover:border-slate-300 transition-all duration-300">
                    <!-- Card Header with Profile -->
                    <div class="p-5 pb-4">
                        <div class="flex items-start gap-4">
                            <div class="relative flex-shrink-0">
                                <img src="${page.page_profile_pic || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(page.page_name) + '&background=e2e8f0&color=475569&size=80'}"
                                     alt="${page.page_name}"
                                     class="w-14 h-14 rounded-full object-cover ring-2 ring-slate-100"
                                     onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(page.page_name)}&background=e2e8f0&color=475569&size=80'">
                                <div class="absolute -bottom-0.5 -right-0.5 w-4 h-4 ${statusDot} rounded-full border-2 border-white"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-slate-900 truncate" title="${page.page_name}">${page.page_name}</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Connected ${formatDate(page.connected_at)}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="px-5 pb-4 space-y-3">
                        <!-- Page ID -->
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500">Page ID</span>
                            <span class="font-mono text-xs text-slate-700 bg-slate-100 px-2 py-1 rounded-lg truncate max-w-[120px]" title="${page.page_id}">${page.page_id}</span>
                        </div>

                        <!-- Status Badge -->
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-500">Status</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border ${statusClass}">
                                <span class="w-1.5 h-1.5 rounded-full ${statusDot}"></span>
                                ${statusText}
                            </span>
                        </div>
                    </div>

                    <!-- Card Footer - Actions -->
                    <div class="px-5 py-4 bg-slate-50/50 border-t border-slate-100 flex gap-2">
                        <a href="/conversations/${page.id}"
                           class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <span>Chats</span>
                        </a>
                        <button onclick="openDeleteModal(${page.id}, '${page.page_name.replace(/'/g, "\\'")}')"
                                class="inline-flex items-center justify-center w-10 h-10 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors"
                                title="Disconnect page">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            `;
        }).join('');

        document.getElementById('pagesList').innerHTML = pagesHTML;
    }

    function renderPagination() {
        const totalPages = Math.ceil(allPages.length / itemsPerPage);

        if (totalPages <= 1) {
            document.getElementById('pagination').classList.add('hidden');
            return;
        }

        document.getElementById('pagination').classList.remove('hidden');
        document.getElementById('pagination').classList.add('flex');

        // Update showing text
        const start = (currentPage - 1) * itemsPerPage + 1;
        const end = Math.min(currentPage * itemsPerPage, allPages.length);
        document.getElementById('showingFrom').textContent = start;
        document.getElementById('showingTo').textContent = end;
        document.getElementById('showingTotal').textContent = allPages.length;

        // Update prev/next buttons
        document.getElementById('prevBtn').disabled = currentPage === 1;
        document.getElementById('nextBtn').disabled = currentPage === totalPages;

        // Generate page numbers
        let pageNumbersHTML = '';
        const maxVisiblePages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        // First page + ellipsis
        if (startPage > 1) {
            pageNumbersHTML += `
                <button onclick="goToPage(1)" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">1</button>
            `;
            if (startPage > 2) {
                pageNumbersHTML += `<span class="w-9 h-9 flex items-center justify-center text-slate-400">...</span>`;
            }
        }

        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === currentPage;
            pageNumbersHTML += `
                <button onclick="goToPage(${i})"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium transition-colors ${
                            isActive
                                ? 'bg-blue-600 text-white'
                                : 'text-slate-700 hover:bg-slate-100'
                        }">
                    ${i}
                </button>
            `;
        }

        // Last page + ellipsis
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                pageNumbersHTML += `<span class="w-9 h-9 flex items-center justify-center text-slate-400">...</span>`;
            }
            pageNumbersHTML += `
                <button onclick="goToPage(${totalPages})" class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">${totalPages}</button>
            `;
        }

        document.getElementById('pageNumbers').innerHTML = pageNumbersHTML;
    }

    function goToPage(page) {
        const totalPages = Math.ceil(allPages.length / itemsPerPage);
        if (page < 1 || page > totalPages) return;

        currentPage = page;
        renderPages();
        renderPagination();

        // Scroll to top of grid
        document.getElementById('pagesGrid').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function formatDate(dateString) {
        if (!dateString) return 'Recently';
        const date = new Date(dateString);
        const now = new Date();
        const diffDays = Math.floor((now - date) / (1000 * 60 * 60 * 24));

        if (diffDays === 0) return 'Today';
        if (diffDays === 1) return 'Yesterday';
        if (diffDays < 7) return `${diffDays} days ago`;
        if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
        if (diffDays < 365) return `${Math.floor(diffDays / 30)} months ago`;
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function connectPages() {
        document.getElementById('connectModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('connectModal').classList.add('hidden');
    }

    function openDeleteModal(pageId, pageName) {
        pageToDelete = pageId;
        document.getElementById('deletePageName').textContent = pageName;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        pageToDelete = null;
        document.getElementById('deleteModal').classList.add('hidden');
    }

    async function confirmConnect() {
        closeModal();

        if (!(await checkAuth())) return;

        // Show loading state on button
        const connectBtn = document.querySelector('button[onclick="connectPages()"]');
        const originalContent = connectBtn.innerHTML;
        connectBtn.innerHTML = `
            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Connecting...</span>
        `;
        connectBtn.disabled = true;

        try {
            const res = await axios.post(`${API_BASE}/pages/connect`);
            if (res.data.success) {
                // Show success toast (you could enhance this with a proper toast)
                showToast('success', res.data.message || 'Pages connected successfully!');
                loadPages();
            }
        } catch (error) {
            showToast('error', 'Error: ' + (error.response?.data?.message || 'Failed to connect pages'));
        } finally {
            connectBtn.innerHTML = originalContent;
            connectBtn.disabled = false;
        }
    }

    async function confirmDelete() {
        if (!pageToDelete) return;

        closeDeleteModal();

        try {
            const res = await axios.delete(`${API_BASE}/pages/${pageToDelete}`);
            if (res.data.success) {
                showToast('success', 'Page disconnected successfully');
                loadPages();
            }
        } catch (error) {
            showToast('error', 'Error: ' + (error.response?.data?.message || 'Failed to disconnect page'));
        }

        pageToDelete = null;
    }

    // Simple toast notification
    function showToast(type, message) {
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-emerald-600' : 'bg-red-600';
        const icon = type === 'success'
            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';

        toast.className = `fixed bottom-4 right-4 ${bgColor} text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-3 z-50 animate-slide-up`;
        toast.innerHTML = `
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">${icon}</svg>
            <span class="text-sm font-medium">${message}</span>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Add animation keyframes
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slide-up {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-slide-up {
            animation: slide-up 0.3s ease;
        }
    `;
    document.head.appendChild(style);

    // Initialize
    loadPages();
</script>
@endsection
