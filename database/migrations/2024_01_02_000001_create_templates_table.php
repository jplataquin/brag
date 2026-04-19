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
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('card_title')->unique();
            $table->foreignId('game_title_id')->constrained()->onDelete('cascade');
            $table->text('quote');
            $table->string('photo')->nullable();
            $table->string('ai_photo')->nullable();
            $table->timestamps();

            // One template per game title per user
            $table->unique(['user_id', 'game_title_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
