<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameTitle;
use Illuminate\Http\Request;

class GameTitleController extends Controller
{
    public function index(Request $request)
    {
        $query = GameTitle::withCount('templates');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $gameTitles = $query->orderBy('title', 'asc')->paginate(20)->withQueryString();

        return view('admin.game_titles.index', compact('gameTitles'));
    }

    public function create()
    {
        return view('admin.game_titles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:game_titles,title',
            'status' => 'required|in:active,hidden',
        ]);

        GameTitle::create($validated);

        return redirect()->route('admin.game_titles.index')
                         ->with('success', 'Game Title added successfully.');
    }

    public function edit(GameTitle $gameTitle)
    {
        return view('admin.game_titles.edit', compact('gameTitle'));
    }

    public function update(Request $request, GameTitle $gameTitle)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:game_titles,title,' . $gameTitle->id,
            'status' => 'required|in:active,hidden',
        ]);

        $gameTitle->update($validated);

        return redirect()->route('admin.game_titles.index')
                         ->with('success', 'Game Title updated successfully.');
    }

    public function destroy(GameTitle $gameTitle)
    {
        $templatesCount = $gameTitle->templates()->count();
        if ($templatesCount > 0) {
            return redirect()->route('admin.game_titles.index')
                             ->with('error', "Cannot delete '{$gameTitle->title}' because it has {$templatesCount} associated templates.");
        }

        $gameTitle->delete();

        return redirect()->route('admin.game_titles.index')
                         ->with('success', 'Game Title deleted successfully.');
    }
}
