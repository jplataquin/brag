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
        Schema::create('team_battles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_title_id')->constrained()->onDelete('cascade');
            $table->string('team_name_a');
            $table->string('team_name_b');
            $table->text('battle_terms');
            $table->integer('no_players_per_team'); // 2,3,4,5,6
            $table->string('status')->default('pending'); // pending, ready, active, completed, cancelled, failed
            
            // Cancellation flags
            $table->boolean('team_a_cancel_flag')->default(false);
            $table->boolean('team_b_cancel_flag')->default(false);
            $table->boolean('marshall_cancel_flag')->default(false);
            
            // Result declarations (user_id of winner or team identifier?)
            // User specified: team_a_declare_win, team_b_declare_win, marshall_declare_win
            // Usually these store the ID of the winning team or user. 
            // In team battle, it's likely which TEAM won. Let's use 'A' or 'B' or null.
            // Actually the user said "this is team A result declaration", "this is team B result declaration".
            // I'll use string to store 'A' or 'B'.
            $table->string('team_a_declare_win')->nullable();
            $table->string('team_b_declare_win')->nullable();
            $table->string('marshall_declare_win')->nullable();
            
            // Marshall election
            $table->foreignId('team_a_marshall_elect')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('team_b_marshall_elect')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('marshall_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Team A Players and Cards
            for ($i = 1; $i <= 6; $i++) {
                $table->foreignId("team_a_user_{$i}")->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId("team_a_card_{$i}")->nullable()->constrained('digital_cards')->onDelete('set null');
            }
            
            // Team B Players and Cards
            for ($i = 1; $i <= 6; $i++) {
                $table->foreignId("team_b_user_{$i}")->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId("team_b_card_{$i}")->nullable()->constrained('digital_cards')->onDelete('set null');
            }
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_battles');
    }
};
