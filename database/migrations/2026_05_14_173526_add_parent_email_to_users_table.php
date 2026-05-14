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
        Schema::table('users', function (Blueprint $schema) {
            $schema->string('parent_email')->nullable()->after('parent_birthdate');
            $schema->string('parent_consent_token')->nullable()->after('parental_consent_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $schema) {
            $schema->dropColumn(['parent_email', 'parent_consent_token']);
        });
    }
};
