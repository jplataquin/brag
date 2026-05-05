<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Services\NanoBananaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    /**
     * Display a listing of the user's templates.
     */
    public function index()
    {
        $templates = Auth::user()->templates()->withCount('digitalCards')->latest()->get();
        return view('templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        $user = Auth::user();
        if (!\App\Models\PlatformSetting::current()->allow_template_creation) {
            return redirect()->route('dashboard')->with('error', 'Template creation is currently disabled by administrators.');
        }

        $gameTitles = \App\Models\GameTitle::where('status', 'active')->orderBy('title')->get();
        
        $gameTemplateCounts = $user->templates()
            ->select('game_title_id', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('game_title_id')
            ->pluck('count', 'game_title_id')
            ->toArray();

        return view('templates.create', compact('gameTitles', 'gameTemplateCounts'));
    }

    /**
     * Generate an AI preview photo based on the prompt.
     */
    public function generateAiPreview(Request $request, NanoBananaService $nanoBanana)
    {
        $request->validate([
            'ai_prompt' => 'required|string|max:200',
            'temporary_photo_path' => 'nullable|string',
        ]);

        $photoPath = $request->input('temporary_photo_path');

        try {
            $aiPhotoPath = $nanoBanana->generateImage($request->ai_prompt, $photoPath);
            return response()->json([
                'success' => true,
                'url' => asset('storage/' . $aiPhotoPath),
                'path' => $aiPhotoPath
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created template.
     */
    public function store(Request $request, NanoBananaService $nanoBanana)
    {
        $request->validate([
            'card_title' => 'required|string|max:50|unique:templates,card_title',
            'game_title_id' => 'required|exists:game_titles,id',
            'quote' => 'required|string|max:500',
            'image_mode' => 'required|in:upload,ai',
            'temporary_photo_path' => 'nullable|string|required_if:image_mode,upload',
            'ai_prompt' => 'nullable|string|max:200|required_if:image_mode,ai',
            'generated_ai_photo' => 'nullable|string|required_if:image_mode,ai',
            'image_position_y' => 'nullable|integer|min:0|max:100',
            'background_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'border_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'section_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'primary_text_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'secondary_text_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'auto_forge' => 'nullable|boolean',
        ], [
            'game_title_id.required' => 'The game title field is required.',
            'game_title_id.exists' => 'The selected game title is invalid.',
            'ai_prompt.required_if' => 'An art style prompt is required for AI generation.',
            'generated_ai_photo.required_if' => 'Please generate an AI preview before saving.',
            'temporary_photo_path.required_if' => 'Please upload a photo.',
            'background_color.regex' => 'The background color must be a valid hex code.',
            'border_color.regex' => 'The border color must be a valid hex code.',
            'section_color.regex' => 'The section color must be a valid hex code.',
            'primary_text_color.regex' => 'The primary text color must be a valid hex code.',
            'secondary_text_color.regex' => 'The secondary text color must be a valid hex code.',
        ]);

        $user = Auth::user();

        // Check rule of 3: max 3 templates per game title
        $existingTemplatesCount = $user->templates()
            ->where('game_title_id', $request->game_title_id)
            ->count();
        
        if ($existingTemplatesCount >= 3) {
            return back()->with('error', "You can only create a maximum of 3 templates per game title.")->withInput();
        }

        $baseCost = config('diamonds.costs.template_creation');
        $forgeCost = config('diamonds.costs.forging');
        $isAutoForge = $request->boolean('auto_forge');
        $totalCost = $isAutoForge ? ($baseCost + $forgeCost) : $baseCost;

        if ($user->diamonds_balance < $totalCost) {
            return back()->with('error', "You need at least {$totalCost} Diamonds. You currently have " . $user->diamonds_balance . '.')->withInput();
        }

        $data = $request->only(['card_title', 'game_title_id', 'quote', 'image_position_y', 'background_color', 'border_color', 'section_color', 'primary_text_color', 'secondary_text_color']);
        $data['card_title'] = strtoupper($data['card_title']);
        $data['user_id'] = $user->id;

        if ($request->image_mode === 'upload' && $request->filled('temporary_photo_path')) {
            $tmpPath = $request->input('temporary_photo_path');
            if (Storage::disk('public')->exists($tmpPath)) {
                $newPath = 'templates/' . basename($tmpPath);
                Storage::disk('public')->move($tmpPath, $newPath);
                Storage::disk('public')->setVisibility($newPath, 'public');
                $data['photo'] = $newPath;
            } else {
                $data['photo'] = $tmpPath;
            }
        } elseif ($request->image_mode === 'ai' && $request->filled('generated_ai_photo')) {
             $data['ai_photo'] = $request->generated_ai_photo;
             $data['photo'] = $request->generated_ai_photo; // Set as main photo as well
        }

        $template = null;
        \Illuminate\Support\Facades\DB::transaction(function () use ($user, $data, $isAutoForge, $baseCost, &$template) {
            $user->deductDiamonds($baseCost, 'system', "Created new template: {$data['card_title']}");
            $template = Template::create($data);

            if ($isAutoForge) {
                app(\App\Services\CardForgeService::class)->forge($user, $template);
            }
        });

        if ($isAutoForge) {
            return redirect()->route('templates.show', $template)
                ->with('success', 'Template created and first card forged successfully!');
        }

        return redirect()->route('templates.index')
            ->with('success', 'Template created successfully! You can now forge Digital Cards from it.');
    }

    /**
     * Display the specified template.
     */
    public function show(Template $template)
    {
        $template->load(['digitalCards' => function($query) use ($template) {
            $query->where('owner_id', $template->user_id);
        }, 'digitalCards.owner', 'user', 'gameTitle']);

        $canForge = false;
        $forgeStatus = null;

        if (Auth::check() && Auth::id() === $template->user_id) {
            $forgeService = app(\App\Services\CardForgeService::class);
            $forgeCheck = $forgeService->canForge(Auth::user(), $template);
            $canForge = $forgeCheck['can_forge'];
            $forgeStatus = $forgeCheck;
        }

        return view('templates.show', compact('template', 'canForge', 'forgeStatus'));
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(Template $template)
    {
        $this->authorize('update', $template);
        $gameTitles = \App\Models\GameTitle::where('status', 'active')->orderBy('title')->get();
        return view('templates.edit', compact('template', 'gameTitles'));
    }

    /**
     * Update the specified template.
     */
    public function update(Request $request, Template $template, NanoBananaService $nanoBanana)
    {
        $this->authorize('update', $template);

        $request->validate([
            'card_title' => 'required|string|max:50|unique:templates,card_title,' . $template->id,
            'quote' => 'required|string|max:500',
            'image_mode' => 'required|in:upload,ai',
            'temporary_photo_path' => 'nullable|string',
            'ai_prompt' => [
                'nullable',
                'string',
                'max:200',
                function ($attribute, $value, $fail) use ($request, $template) {
                    if ($request->image_mode === 'ai' && empty($value) && empty($template->ai_photo)) {
                        $fail('An art style prompt is required for AI generation.');
                    }
                },
            ],
            'generated_ai_photo' => 'nullable|string',
            'image_position_y' => 'nullable|integer|min:0|max:100',
            'background_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'border_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'section_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'primary_text_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'secondary_text_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ], [
            'ai_prompt.required_if' => 'An art style prompt is required for AI generation.',
            'background_color.regex' => 'The background color must be a valid hex code.',
            'border_color.regex' => 'The border color must be a valid hex code.',
            'section_color.regex' => 'The section color must be a valid hex code.',
            'primary_text_color.regex' => 'The primary text color must be a valid hex code.',
            'secondary_text_color.regex' => 'The secondary text color must be a valid hex code.',
        ]);

        $data = $request->only(['card_title', 'quote', 'image_position_y', 'background_color', 'border_color', 'section_color', 'primary_text_color', 'secondary_text_color']);
        $data['card_title'] = strtoupper($data['card_title']);

        if ($request->image_mode === 'upload') {
            if ($request->filled('temporary_photo_path')) {
                // Delete old photos
                if ($template->photo) {
                    Storage::disk('public')->delete($template->photo);
                }
                if ($template->ai_photo) {
                    Storage::disk('public')->delete($template->ai_photo);
                    $data['ai_photo'] = null;
                }

                $tmpPath = $request->input('temporary_photo_path');
                if (Storage::disk('public')->exists($tmpPath)) {
                    $newPath = 'templates/' . basename($tmpPath);
                    Storage::disk('public')->move($tmpPath, $newPath);
                    Storage::disk('public')->setVisibility($newPath, 'public');
                    $data['photo'] = $newPath;
                } else {
                    $data['photo'] = $tmpPath;
                }
            }
        } elseif ($request->image_mode === 'ai') {
            if ($request->filled('generated_ai_photo')) {
                if ($template->photo && $template->photo !== $template->ai_photo) {
                    Storage::disk('public')->delete($template->photo);
                }
                if ($template->ai_photo) {
                    Storage::disk('public')->delete($template->ai_photo);
                }
                $data['ai_photo'] = $request->generated_ai_photo;
                $data['photo'] = $request->generated_ai_photo;
            }
        }

        $template->update($data);

        return redirect()->route('templates.show', $template)
            ->with('success', 'Template updated successfully!');
    }

    /**
     * Remove the specified template.
     */
    public function destroy(Template $template)
    {
        $this->authorize('delete', $template);

        if ($template->photo) {
            Storage::disk('public')->delete($template->photo);
        }
        if ($template->ai_photo) {
            Storage::disk('public')->delete($template->ai_photo);
        }

        $template->delete();

        return redirect()->route('templates.index')
            ->with('success', 'Template deleted successfully.');
    }
}
