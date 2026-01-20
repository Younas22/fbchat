<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->string('message_id')->unique(); // Facebook message ID
            $table->text('message_text')->nullable();
            $table->string('sender_type'); // 'page' or 'customer'
            $table->string('sender_id'); // PSID or page ID
            $table->string('attachment_type')->nullable(); // image, video, file, etc.
            $table->text('attachment_url')->nullable();
            $table->enum('status', ['sent', 'delivered', 'read'])->default('sent');
            $table->timestamp('sent_at');
            $table->timestamps();

            // Indexes for performance
            $table->index(['conversation_id', 'sent_at']);
            $table->index('message_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
