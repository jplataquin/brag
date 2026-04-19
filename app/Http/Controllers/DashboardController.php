<?php

namespace App\Http\Controllers;

use App\Models\Battle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        $ownCards = $user->digitalCards()
            ->where('original_owner_id', $user->id)
            ->where('is_trophy', false)
            ->with('template.gameTitle')
            ->get();

        $trophies = $user->trophies()
            ->with('template.gameTitle', 'originalOwner')
            ->latest()
            ->take(6)
            ->get();

        $templates = $user->templates()->with('gameTitle')->latest()->take(6)->get();

        $recentBattles = Battle::where('challenger_id', $user->id)
            ->orWhere('opponent_id', $user->id)
            ->with(['challenger', 'opponent', 'challengerCard.template.gameTitle', 'opponentCard.template.gameTitle'])
            ->latest()
            ->take(5)
            ->get();

        $pendingInvites = $user->battleInvites()
            ->where('status', 'pending')
            ->with('battle.challenger')
            ->latest()
            ->get();

        $stats = [
            'total_cards' => $user->digitalCards()->count(),
            'total_trophies' => $user->trophies()->count(),
            'total_templates' => $user->templates()->count(),
            'total_wins' => Battle::where('winner_id', $user->id)->count(),
            'total_battles' => Battle::where('challenger_id', $user->id)
                ->orWhere('opponent_id', $user->id)
                ->where('status', 'completed')
                ->count(),
        ];

        return view('dashboard', compact('ownCards', 'trophies', 'templates', 'recentBattles', 'pendingInvites', 'stats'));
    }
}
