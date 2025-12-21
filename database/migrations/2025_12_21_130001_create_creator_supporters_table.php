<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creator_supporters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('supporter_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['creator_id', 'supporter_id']);
            $table->index(['supporter_id', 'creator_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creator_supporters');
    }
};
