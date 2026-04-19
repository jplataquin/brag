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
        Schema::create('battles', function (Blueprint $table) {
            $table->id();
            $table->string('room_id')->unique();
            $table->text('terms')->nullable();
            $table->foreignId('challenger_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('opponent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('adjudicator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('challenger_card_id')->constrained('digital_cards')->onDelete('cascade');
            $table->foreignId('opponent_card_id')->nullable()->constrained('digital_cards')->onDelete('set null');
            $table->foreignId('winner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battles');
    }
};
