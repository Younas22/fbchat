<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('chat_id');
            $table->longText('notes')->nullable();
            $table->timestamp('saved_at')->useCurrent();
            $table->timestamps();
            
            $table->index(['user_id', 'conversation_id']);
            $table->index('chat_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_chats');
    }
};