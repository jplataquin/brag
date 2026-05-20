<?php

namespace App\Http\Controllers;

use App\Models\GameTitle;
use Illuminate\Http\Request;

class GameTitleController extends Controller
{
    /**
     * Display a listing of active game titles.
     */
    public function index()
    {
        $gameTitles = GameTitle::where('status', 'active')
            ->withCount([
                'templates',
                'premiumTemplates' => function($query) {
                    $query->where('status', 'active');
                }
            ])
            ->orderBy('title', 'asc')
            ->get();

        return view('game_titles.index', compact('gameTitles'));
    }

    /**
     * Display the specified game title.
     */
    public function show(GameTitle $gameTitle)
    {
        if ($gameTitle->status !== 'active') {
            abort(404);
        }

        $gameTitle->load([
            'templates' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'premiumTemplates' => function($query) {
                $query->where('status', 'active')->orderBy('created_at', 'desc');
            }
        ]);

        // Fetch top 8 cards by leaderboard score
        $topCards = $gameTitle->digitalCards()
            ->with(['template', 'owner', 'originalOwner'])
            ->select('*')
            ->selectRaw('(CASE WHEN win_rate = 0 THEN 0 ELSE (level + win_rate + integrity_stat) END) as leaderboard_score')
            ->orderByRaw('(CASE WHEN win_rate = 0 THEN 0 ELSE (level + win_rate + integrity_stat) END) DESC')
            ->limit(8)
            ->get();

        return view('game_titles.show', compact('gameTitle', 'topCards'));
    }

    /**
     * Display the leaderboard for a specific game title.
     */
    public function leaderboard(GameTitle $gameTitle)
    {
        if ($gameTitle->status !== 'active') {
            abort(404);
        }

        $cards = $gameTitle->digitalCards()
            ->with(['template', 'owner'])
            ->select('*')
            ->selectRaw('(CASE WHEN win_rate = 0 THEN 0 ELSE (level + win_rate + integrity_stat) END) as leaderboard_score')
            ->orderByRaw('(CASE WHEN win_rate = 0 THEN 0 ELSE (level + win_rate + integrity_stat) END) DESC')
            ->paginate(50);

        return view('game_titles.leaderboard', compact('gameTitle', 'cards'));
    }
}
