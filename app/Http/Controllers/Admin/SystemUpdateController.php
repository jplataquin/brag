<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

class SystemUpdateController extends Controller
{
    /**
     * Run a specific deployment command step.
     */
    public function runCommand(Request $request)
    {
        $step = $request->input('step');
        $output = '';
        $success = false;
        $basePath = base_path();

        // Increase time limits for heavy processes (NPM/Composer)
        set_time_limit(300);

        try {
            switch ($step) {
                case 'fix_git_config':
                    $result = Process::path($basePath)->run("git config --global --add safe.directory {$basePath}");
                    break;

                case 'git_pull':
                    $result = Process::path($basePath)->run('git pull');
                    break;

                case 'composer_install':
                    $result = Process::path($basePath)->run('composer install --optimize-autoloader --no-dev');
                    break;

                case 'migrate':
                    $result = Process::path($basePath)->run('php artisan migrate --force');
                    break;
                    
                case 'npm_update':
                    $result = Process::path($basePath)->run('npm update');
                    break;

                case 'npm_install':
                    $result = Process::path($basePath)->run('npm install');
                    break;

                case 'npm_build':
                    $result = Process::path($basePath)->run('npm run build');
                    break;

                default:
                    return response()->json(['success' => false, 'output' => "Unknown deployment step: {$step}"]);
            }

            $output = $result->output() . "\n" . $result->errorOutput();
            $success = $result->successful();

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'output' => "System Exception: " . $e->getMessage()
            ]);
        }

        return response()->json([
            'success' => $success,
            'output' => trim($output) ?: 'Command completed successfully with no output.'
        ]);
    }
}
