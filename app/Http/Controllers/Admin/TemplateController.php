<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\PremiumTemplate;
use App\Models\GameTitle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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
        $premiumTemplates = PremiumTemplate::with(['gameTitle', 'adminEditor'])->orderBy('id', 'desc')->get();
        $gameTitles = GameTitle::where('status', 'active')->orderBy('title')->get();

        return view('admin.templates.index', compact('templates', 'premiumTemplates', 'gameTitles'));
    }

    /**
     * Store a newly created premium template.
     */
    public function storePremium(Request $request)
    {
        // Increase limits for processing large JSON with base64 images
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $request->validate([
            'template_title' => 'required|string|max:255|unique:premium_templates,template_title',
            'game_title_id' => 'required|exists:game_titles,id',
            'designer_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
            'temporary_json_path' => 'required|string',
        ]);

        $tempPath = $request->input('temporary_json_path');
        if (!Storage::disk('public')->exists($tempPath)) {
            return back()->with('error', 'Temporary template file not found.');
        }

        $jsonContent = Storage::disk('public')->get($tempPath);
        $config = json_decode($jsonContent, true);
        unset($jsonContent); // Free up raw string memory immediately

        if (!$config || !isset($config['levels'])) {
            return back()->with('error', 'Invalid JSON template format.');
        }

        $slug = Str::slug($request->template_title);
        $timestamp = time();
        $savedFiles = [];

        DB::beginTransaction();

        try {
            // Iterate levels and layers to extract base64 images and convert to WebP
            foreach ($config['levels'] as $levelKey => &$level) {
                foreach ($level['layers'] as $layerIndex => &$layer) {
                    if ($layer['type'] === 'image' && !empty($layer['data'])) {
                        $base64Data = $layer['data'];
                        
                        // Basic check for base64
                        if (str_contains($base64Data, ';base64,')) {
                            $parts = explode(';base64,', $base64Data);
                            $data = base64_decode($parts[1]);
                            
                            // Create image from string
                            $img = @imagecreatefromstring($data);
                            if ($img) {
                                $filename = "premium-templates/{$slug}_lv{$levelKey}_layer{$layerIndex}_{$timestamp}.webp";
                                
                                // Enable alpha blending and save as WebP
                                imagealphablending($img, false);
                                imagesavealpha($img, true);
                                
                                ob_start();
                                imagewebp($img, null, 90);
                                $webpData = ob_get_clean();
                                
                                Storage::disk('public')->put($filename, $webpData);
                                $savedFiles[] = $filename; // Track for potential rollback
                                imagedestroy($img);
                                
                                // Replace base64 with asset path
                                $layer['asset_path'] = $filename;
                                unset($layer['data']); // Crucial: free the base64 string from the array
                            }
                        }
                    }
                }
            }

            PremiumTemplate::create([
                'game_title_id' => $request->game_title_id,
                'template_title' => $request->template_title,
                'price' => $request->price,
                'status' => $request->status,
                'designer_name' => $request->designer_name,
                'description' => $request->description,
                'premium_config' => $config,
                'admin_editor_id' => auth()->id(),
            ]);

            DB::commit();

            // Cleanup temporary JSON file only on success
            Storage::disk('public')->delete($tempPath);

            return back()->with('success', 'Premium template uploaded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            // Cleanup any files saved during the failed attempt
            foreach ($savedFiles as $file) {
                Storage::disk('public')->delete($file);
            }

            return back()->with('error', 'Error processing template: ' . $e->getMessage());
        }
    }

    /**
     * Toggle premium template status.
     */
    public function toggleStatus(PremiumTemplate $template)
    {
        $template->status = $template->status === 'active' ? 'inactive' : 'active';
        $template->save();

        return back()->with('success', "Premium template status updated to {$template->status}.");
    }

    /**
     * Show the form for editing the specified premium template.
     */
    public function editPremium(PremiumTemplate $premiumTemplate)
    {
        $premiumTemplate->load(['gameTitle', 'adminEditor']);
        $gameTitles = GameTitle::where('status', 'active')->orderBy('title')->get();

        return view('admin.templates.edit_premium', compact('premiumTemplate', 'gameTitles'));
    }

    /**
     * Update the specified premium template in storage.
     */
    public function updatePremium(Request $request, PremiumTemplate $premiumTemplate)
    {
        $request->validate([
            'template_title' => 'required|string|max:255|unique:premium_templates,template_title,' . $premiumTemplate->id,
            'game_title_id' => 'required|exists:game_titles,id',
            'designer_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $premiumTemplate->update([
            'template_title' => $request->template_title,
            'game_title_id' => $request->game_title_id,
            'designer_name' => $request->designer_name,
            'description' => $request->description,
            'price' => $request->price,
            'status' => $request->status,
            'admin_editor_id' => auth()->id(),
        ]);

        return redirect()->route('admin.templates.index')->with('success', 'Premium template updated successfully!');
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
            'image_position_x' => 'nullable|integer|min:0|max:100',
            'image_position_y' => 'nullable|integer|min:0|max:100',
            'image_scale' => 'nullable|numeric|min:1.0|max:5',
            'price' => 'nullable|integer|min:0',
            'status' => 'nullable|in:active,inactive',
            'designer_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
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
            'image_position_x' => $request->image_position_x ?? 50,
            'image_position_y' => $request->image_position_y,
            'image_scale' => $request->image_scale ?? 1.0,
            'price' => $request->price ?? 0,
            'status' => $request->status ?? 'inactive',
            'designer_name' => $request->designer_name,
            'description' => $request->description,
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
