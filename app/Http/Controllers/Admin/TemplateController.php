<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\GameTitle;
use App\Models\User;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    /**
     * Display a listing of templates.
     */
    public function index(Request $request)
    {
        $query = Template::with(['user', 'gameTitle', 'adminEditor'])->withTrashed();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('id', $search)
                  ->orWhere('card_title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('username', 'like', "%{$search}%");
                  })
                  ->orWhereHas('gameTitle', function($q) use ($search) {
                      $q->where('title', 'like', "%{$search}%");
                  });
        }

        $templates = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        return view('admin.templates.index', compact('templates'));
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit($id)
    {
        $template = Template::with(['user', 'gameTitle', 'adminEditor'])->withTrashed()->findOrFail($id);
        $gameTitles = GameTitle::orderBy('title')->get();
        $users = User::orderBy('username')->get();

        return view('admin.templates.edit', compact('template', 'gameTitles', 'users'));
    }

    /**
     * Update the specified template in storage.
     */
    public function update(Request $request, $id)
    {
        $template = Template::withTrashed()->findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'card_title' => 'required|string|max:255|unique:templates,card_title,' . $template->id,
            'game_title_id' => 'required|exists:game_titles,id',
            'quote' => 'required|string|max:500',
            'image_mode' => 'required|string|in:upload,ai',
            'temporary_photo_path' => 'nullable|string',
            'generated_ai_photo' => 'nullable|string',
            'background_color' => 'nullable|string|max:50',
            'border_color' => 'nullable|string|max:50',
            'section_color' => 'nullable|string|max:50',
            'primary_text_color' => 'nullable|string|max:50',
            'secondary_text_color' => 'nullable|string|max:50',
            'image_position_y' => 'nullable|integer|min:0|max:100',
        ]);

        $dataToUpdate = [
            'user_id' => $request->user_id,
            'card_title' => $request->card_title,
            'game_title_id' => $request->game_title_id,
            'quote' => $request->quote,
            'background_color' => $request->background_color,
            'border_color' => $request->border_color,
            'section_color' => $request->section_color,
            'primary_text_color' => $request->primary_text_color,
            'secondary_text_color' => $request->secondary_text_color,
            'image_position_y' => $request->image_position_y,
            'admin_editor_id' => auth()->id(),
            'admin_edited_at' => now(),
        ];

        if ($request->image_mode === 'upload' && $request->filled('temporary_photo_path')) {
            // Delete old photos
            if ($template->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($template->photo);
            }
            if ($template->ai_photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($template->ai_photo);
                $dataToUpdate['ai_photo'] = null; // Remove AI photo since we are uploading a fresh base photo
            }

            // Move new photo from temp chunk directory to final destination
            $tmpPath = $request->input('temporary_photo_path');
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($tmpPath)) {
                $newPath = 'templates/' . basename($tmpPath);
                \Illuminate\Support\Facades\Storage::disk('public')->move($tmpPath, $newPath);
                $dataToUpdate['photo'] = $newPath;
            } else {
                $dataToUpdate['photo'] = $tmpPath;
            }
        } elseif ($request->image_mode === 'ai' && $request->filled('generated_ai_photo')) {
            if ($template->photo && $template->photo !== $template->ai_photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($template->photo);
            }
            if ($template->ai_photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($template->ai_photo);
            }
            $dataToUpdate['ai_photo'] = $request->generated_ai_photo;
            $dataToUpdate['photo'] = $request->generated_ai_photo; // Set as main photo as well
        }

        $template->update($dataToUpdate);

        return redirect()->route('admin.templates.index')->with('success', "Template #{$template->id} updated successfully.");
    }
}
