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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'parent_firstname')) {
                $table->string('parent_firstname')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('users', 'parent_lastname')) {
                $table->string('parent_lastname')->nullable()->after('parent_firstname');
            }
            if (!Schema::hasColumn('users', 'parent_birthdate')) {
                $table->date('parent_birthdate')->nullable()->after('parent_lastname');
            }
            if (!Schema::hasColumn('users', 'parent_email')) {
                $table->string('parent_email')->nullable()->after('parent_birthdate');
            }
            if (!Schema::hasColumn('users', 'parent_id_path')) {
                $table->string('parent_id_path')->nullable()->after('parent_email');
            }
            if (!Schema::hasColumn('users', 'parental_consent_status')) {
                $table->enum('parental_consent_status', ['not_required', 'pending', 'approved'])
                    ->default('not_required')
                    ->after('parent_id_path');
            }
            if (!Schema::hasColumn('users', 'parent_consent_token')) {
                $table->string('parent_consent_token')->nullable()->after('parental_consent_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration for a repair script to avoid accidental data loss
    }
};
