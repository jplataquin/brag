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

        $recentBattles = Battle::where(function($q) use ($user) {
                for ($i = 1; $i <= 6; $i++) {
                    $q->orWhere("team_a_user_{$i}", $user->id)
                      ->orWhere("team_b_user_{$i}", $user->id);
                }
                $q->orWhere('marshall_id', $user->id);
            })
            ->with(['gameTitle'])
            ->latest()
            ->take(5)
            ->get();

        $pendingInvites = collect(); // BattleInvites table was removed

        $stats = [
            'total_cards' => $user->digitalCards()->count(),
            'total_trophies' => $user->trophies()->count(),
            'total_templates' => $user->templates()->count(),
            'total_wins' => Battle::where('status', 'completed')->where(function($q) use ($user) {
                    $q->where('winner_team', 'A')->where(function($sq) use ($user) {
                        for ($i = 1; $i <= 6; $i++) { $sq->orWhere("team_a_user_{$i}", $user->id); }
                    })->orWhere(function($q2) use ($user) {
                        $q2->where('winner_team', 'B')->where(function($sq2) use ($user) {
                            for ($i = 1; $i <= 6; $i++) { $sq2->orWhere("team_b_user_{$i}", $user->id); }
                        });
                    });
                })->count(),
            'total_losses' => Battle::where('status', 'completed')
                ->where('winner_team', '!=', 'T')
                ->where(function($q) use ($user) {
                    $q->where(function($qA) use ($user) {
                        $qA->where('winner_team', 'B')->where(function($sq) use ($user) {
                            for ($i = 1; $i <= 6; $i++) { $sq->orWhere("team_a_user_{$i}", $user->id); }
                        });
                    })->orWhere(function($qB) use ($user) {
                        $qB->where('winner_team', 'A')->where(function($sq) use ($user) {
                            for ($i = 1; $i <= 6; $i++) { $sq->orWhere("team_b_user_{$i}", $user->id); }
                        });
                    });
                })->count(),
        ];

        return view('dashboard', compact('ownCards', 'trophies', 'templates', 'recentBattles', 'pendingInvites', 'stats'));
    }
}
