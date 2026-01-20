<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('page_id')->constrained('facebook_pages')->onDelete('cascade');
            $table->string('conversation_id')->unique();
            $table->string('customer_name')->nullable();
            $table->string('customer_psid')->nullable();
            $table->text('customer_profile_pic')->nullable();
            $table->text('last_message_preview')->nullable();
            $table->timestamp('last_message_time')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'page_id']);
            $table->index('conversation_id');
            $table->index('last_message_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};