<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facebook_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('page_id')->unique();
            $table->string('page_name');
            $table->text('page_access_token');
            $table->text('page_profile_pic')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('connected_at')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facebook_pages');
    }
};