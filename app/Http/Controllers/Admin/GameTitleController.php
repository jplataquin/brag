<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GameTitle;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'description' => 'nullable|string',
            'temporary_header_path' => 'nullable|string',
        ]);

        $data = $request->only(['title', 'status', 'description']);

        if ($request->filled('temporary_header_path')) {
            $tempPath = $request->input('temporary_header_path');
            if (strpos($tempPath, 'tmp/uploads/') === 0 && Storage::disk('public')->exists($tempPath)) {
                $finalPath = 'game_titles/header_' . time() . '_' . Str::random(10) . '.' . pathinfo($tempPath, PATHINFO_EXTENSION);
                if (Storage::disk('public')->copy($tempPath, $finalPath)) {
                    $data['header_image'] = $finalPath;
                    Storage::disk('public')->delete($tempPath);
                }
            }
        }

        GameTitle::create($data);

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
            'description' => 'nullable|string',
            'temporary_header_path' => 'nullable|string',
        ]);

        $data = $request->only(['title', 'status', 'description']);

        if ($request->filled('temporary_header_path')) {
            $tempPath = $request->input('temporary_header_path');
            if (strpos($tempPath, 'tmp/uploads/') === 0 && Storage::disk('public')->exists($tempPath)) {
                $finalPath = 'game_titles/header_' . time() . '_' . Str::random(10) . '.' . pathinfo($tempPath, PATHINFO_EXTENSION);
                if (Storage::disk('public')->copy($tempPath, $finalPath)) {
                    // Delete old image if it exists
                    if ($gameTitle->header_image) {
                        Storage::disk('public')->delete($gameTitle->header_image);
                    }
                    $data['header_image'] = $finalPath;
                    Storage::disk('public')->delete($tempPath);
                }
            }
        }

        $gameTitle->update($data);

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

        if ($gameTitle->header_image) {
            Storage::disk('public')->delete($gameTitle->header_image);
        }

        $gameTitle->delete();

        return redirect()->route('admin.game_titles.index')
                         ->with('success', 'Game Title deleted successfully.');
    }
}
