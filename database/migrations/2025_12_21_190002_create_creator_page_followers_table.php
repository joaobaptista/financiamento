<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creator_page_followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_page_id')->constrained('creator_pages')->cascadeOnDelete();
            $table->foreignId('follower_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['creator_page_id', 'follower_id']);
            $table->index(['follower_id', 'creator_page_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creator_page_followers');
    }
};
