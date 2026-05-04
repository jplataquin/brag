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
        Schema::table('diamond_packages', function (Blueprint $table) {
            $table->string('ocr_match_string')->nullable()->after('qr_path')->comment('Custom text that OCR must find in proof screenshot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diamond_packages', function (Blueprint $table) {
            $table->dropColumn('ocr_match_string');
        });
    }
};
