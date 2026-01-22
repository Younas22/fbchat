<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FacebookPageController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SavedChatController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\SettingController;

// Public routes with rate limiting
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Facebook Webhook routes (must be public)
Route::get('/webhook/facebook', [WebhookController::class, 'verify']);
Route::post('/webhook/facebook', [WebhookController::class, 'handle']);

// Public branding settings (for login page, etc.)
Route::get('/settings/branding', [SettingController::class, 'getBranding']);

// Protected routes with rate limiting
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {

    // Auth routes
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/update-password', [AuthController::class, 'updatePassword']);

    // Facebook Pages
    Route::get('/pages', [FacebookPageController::class, 'index']);
    Route::post('/pages/connect', [FacebookPageController::class, 'connectPages'])->middleware('throttle:60,1');
    Route::delete('/pages/{pageId}', [FacebookPageController::class, 'disconnectPage']);
    Route::get('/pages/{pageId}', [FacebookPageController::class, 'show']);

    // Conversations
    Route::get('/conversations/{pageId}', [ConversationController::class, 'index']);
    Route::post('/conversations/{pageId}/sync', [ConversationController::class, 'syncConversations'])->middleware('throttle:100,1');
    Route::patch('/conversations/{conversationId}/archive', [ConversationController::class, 'archive']);
    Route::patch('/conversations/{conversationId}/unarchive', [ConversationController::class, 'unarchive']);

    // Chat Messages
    Route::get('/chat/{conversationId}/messages', [ChatController::class, 'getMessages']);
    Route::post('/chat/{conversationId}/send', [ChatController::class, 'sendMessage'])->middleware('throttle:100,1');
    Route::post('/chat/{conversationId}/sync', [ChatController::class, 'syncMessages'])->middleware('throttle:100,1');

    // Real-time polling endpoints (efficient - only fetch new data)
    Route::get('/chat/{conversationId}/poll', [ChatController::class, 'pollNewMessages'])->middleware('throttle:120,1');
    Route::get('/chat/unread-counts', [ChatController::class, 'getUnreadCounts'])->middleware('throttle:120,1');
    Route::get('/chat/sidebar-updates', [ChatController::class, 'getSidebarUpdates'])->middleware('throttle:120,1');

    // Saved Chats
    Route::post('/saved-chats/{conversationId}', [SavedChatController::class, 'store']);
    Route::get('/saved-chats', [SavedChatController::class, 'index']);
    Route::patch('/saved-chats/{savedChatId}', [SavedChatController::class, 'update']);
    Route::delete('/saved-chats/{savedChatId}', [SavedChatController::class, 'destroy']);

    // Settings
    Route::get('/settings', [SettingController::class, 'index']);
    Route::post('/settings', [SettingController::class, 'update']);
    Route::post('/settings/exchange-token', [SettingController::class, 'exchangeToken']);
    Route::post('/settings/upload-logo', [SettingController::class, 'uploadLogo']);
    Route::delete('/settings/logo', [SettingController::class, 'removeLogo']);
});