<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;

class CronJobController extends Controller
{
    public function index(Schedule $schedule)
    {
        $events = collect($schedule->events())->map(function ($event) {
            // Extract command signature from command string (e.g. 'artisan' 'app:auto-cancel-battles')
            $command = trim(str_replace(["'artisan'", "artisan", "php artisan"], "", $event->command));
            
            // Get description from Artisan
            $description = '';
            $commands = Artisan::all();
            if (isset($commands[$command])) {
                $description = $commands[$command]->getDescription();
            }

            return [
                'command' => $command,
                'description' => $description,
                'expression' => $event->expression,
                'next_run_at' => $event->nextRunDate()->format('Y-m-d H:i:s'),
            ];
        });

        return view('admin.cron.index', compact('events'));
    }

    public function run(Request $request)
    {
        $request->validate([
            'command' => 'required|string',
        ]);

        $command = $request->command;
        $logPath = storage_path('logs/cron.log');

        try {
            $timestamp = "[" . now()->format('Y-m-d H:i:s') . "] MANUAL RUN: {$command}\n";
            File::append($logPath, $timestamp);
            
            Artisan::call($command);
            $output = Artisan::output();
            
            File::append($logPath, $output . "\n" . str_repeat('-', 20) . "\n");

            return back()->with('success', "Command '{$command}' executed successfully. Check logs for details.");
        } catch (\Exception $e) {
            File::append($logPath, "ERROR: " . $e->getMessage() . "\n" . str_repeat('-', 20) . "\n");
            return back()->with('error', "Failed to execute '{$command}': " . $e->getMessage());
        }
    }

    public function logs()
    {
        $logPath = storage_path('logs/cron.log');
        $logs = File::exists($logPath) ? File::get($logPath) : 'No logs found.';
        
        // Basic protection against massive files: show last 5000 chars if too big
        if (strlen($logs) > 50000) {
            $logs = "--- Log file is large. Showing only the tail end ---\n\n" . substr($logs, -50000);
        }

        return view('admin.cron.logs', compact('logs'));
    }
}
