<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

// Main routes - no auth middleware since this is API-based app with Sanctum
Route::get('/', function () {
    return view('pages.dashboard');
})->name('dashboard');

Route::get('/pages', function () {
    return view('pages.index');
})->name('pages.index');

Route::get('/conversations', function () {
    return view('conversations.index');
})->name('conversations.all');

Route::get('/conversations/{pageId}', function ($pageId) {
    return view('conversations.index', ['pageId' => $pageId]);
})->name('conversations.index');

Route::get('/chat/{conversationId}', function ($conversationId) {
    return view('chat.show', ['conversationId' => $conversationId]);
})->name('chat.show');

Route::get('/saved-chats', function () {
    return view('saved-chats.index');
})->name('saved-chats.index');

// Meta App Required Pages
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');

Route::get('/data-deletion', function () {
    return view('data-deletion');
})->name('data-deletion');

// Auth routes disabled - using API authentication with Sanctum instead
// require __DIR__ . '/auth.php';