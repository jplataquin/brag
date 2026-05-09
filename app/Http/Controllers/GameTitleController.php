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

        return view('game_titles.show', compact('gameTitle'));
    }
}
