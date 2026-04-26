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
        Schema::table('templates', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_editor_id')->nullable()->after('secondary_text_color');
            $table->timestamp('admin_edited_at')->nullable()->after('admin_editor_id');
            
            $table->foreign('admin_editor_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropForeign(['admin_editor_id']);
            $table->dropColumn(['admin_editor_id', 'admin_edited_at']);
        });
    }
};
