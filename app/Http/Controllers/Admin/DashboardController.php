<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Battle;
use App\Models\Template;
use App\Models\DigitalCard;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_battles' => Battle::count(),
            'active_battles' => Battle::whereIn('status', ['pending', 'ready', 'active', 'adjudicating'])->count(),
            'total_templates' => Template::count(),
            'total_cards' => DigitalCard::count(),
            'pending_reports' => \App\Models\CardReport::where('status', 'pending')->count(),
        ];

        // Let's also get recent users
        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }
}
