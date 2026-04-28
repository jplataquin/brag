<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ResetApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate all database records (except migrations) and delete all uploaded card images.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (app()->environment('production') && !$this->option('force')) {
            if (!$this->confirm('The application is in PRODUCTION. Do you really want to reset everything?')) {
                $this->error('Operation cancelled.');
                return 1;
            }
        } elseif (!$this->confirm('This will truncate all database records and delete all images. Are you sure?')) {
            $this->error('Operation cancelled.');
            return 1;
        }

        $this->info('Starting application reset...');

        // 1. Truncate Database Tables
        $this->truncateTables();

        // 2. Delete Images
        $this->deleteImages();

        // 3. Re-seed essential data
        //$this->info('Re-seeding database...');
        //$this->call('db:seed');

        // 4. Clear cache and views
        $this->info('Clearing cache and views...');
        $this->call('cache:clear');
        $this->call('view:clear');
        $this->call('config:clear');
        $this->call('route:clear');

        $this->info('Application reset successfully.');
        return 0;
    }

    /**
     * Truncate all database tables except migrations.
     */
    protected function truncateTables()
    {
        $this->info('Truncating database tables...');

        Schema::disableForeignKeyConstraints();

        $databaseName = DB::getDatabaseName();
        $tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' . $databaseName;

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;

            // Skip migrations table
            if ($tableName === 'migrations') {
                continue;
            }

            $this->comment("Truncating table: {$tableName}");
            DB::table($tableName)->truncate();
        }

        Schema::enableForeignKeyConstraints();
        $this->info('All tables truncated.');
    }

    /**
     * Delete all uploaded images and temporary files.
     */
    protected function deleteImages()
    {
        $this->info('Deleting uploaded images...');

        $directories = ['templates', 'tmp'];

        foreach ($directories as $directory) {
            if (Storage::disk('public')->exists($directory)) {
                $this->comment("Deleting directory: storage/app/public/{$directory}");
                Storage::disk('public')->deleteDirectory($directory);
                // Re-create the directory to keep the structure clean
                Storage::disk('public')->makeDirectory($directory);
            }
        }

        // Also check if there are any files directly in the public root that shouldn't be there
        // but normally we keep it clean.

        $this->info('Images and temporary files deleted.');
    }
}
