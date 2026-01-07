<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fretes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reward_id')->constrained('rewards')->onDelete('cascade');
            $table->enum('regiao', ['norte', 'nordeste', 'centro-oeste', 'sudeste', 'sul']);
            $table->bigInteger('valor'); // valor em centavos
            $table->timestamps();

            $table->unique(['reward_id', 'regiao']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fretes');
    }
};
