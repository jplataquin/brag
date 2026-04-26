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
        Schema::create('terms_of_services', function (Blueprint $table) {
            $table->id();
            $table->longText('content');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false);
            $table->unsignedBigInteger('terms_version_agreed')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms_of_services');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_admin', 'terms_version_agreed']);
        });
    }
};
