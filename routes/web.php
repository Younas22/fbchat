<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
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


Route::get('/clear-cache', function() {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    return 'Cache cleared!';
});

// Serve files from storage (bypass symlink 403 issue)
Route::get('/files/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);

    if (!file_exists($fullPath)) {
        abort(404);
    }

    $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';

    return response()->file($fullPath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('files.serve');



// Auth routes disabled - using API authentication with Sanctum instead
// require __DIR__ . '/auth.php';