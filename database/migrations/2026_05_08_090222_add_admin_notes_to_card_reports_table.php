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
        Schema::table('card_reports', function (Blueprint $table) {
            $table->text('admin_notes')->nullable()->after('notes');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null')->after('status');
            $table->timestamp('resolved_at')->nullable()->after('resolved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('card_reports', function (Blueprint $table) {
            $table->dropForeign(['resolved_by']);
            $table->dropColumn(['admin_notes', 'resolved_by', 'resolved_at']);
        });
    }
};
