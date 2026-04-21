<?php

namespace App\Http\Controllers;

use App\Models\Battle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show($username)
    {
        $user = User::where('username', $username)->firstOrFail();

        $ownCards = $user->digitalCards()
            ->where('original_owner_id', $user->id)
            ->where('is_trophy', false)
            ->with('template.gameTitle')
            ->orderBy('level', 'desc')
            ->orderBy('wins', 'desc')
            ->get();

        $trophies = $user->trophies()
            ->with('template.gameTitle', 'originalOwner')
            ->orderBy('level', 'desc')
            ->orderBy('wins', 'desc')
            ->get();

        $availableGames = \App\Models\GameTitle::whereIn('id', $ownCards->pluck('template.gameTitle.id')->merge($trophies->pluck('template.gameTitle.id'))->unique())->get();
        $availableLevels = $ownCards->pluck('level')->merge($trophies->pluck('level'))->unique()->sort()->values();

        $templates = $user->templates()->with('gameTitle')->withCount('digitalCards')->get();

        $failed_count = Battle::where(function ($q) use ($user) {
            $q->where('challenger_id', $user->id)
              ->orWhere('opponent_id', $user->id);
        })->where('status', 'failed')->count();

        $completed_count = Battle::where(function ($q) use ($user) {
            $q->where('challenger_id', $user->id)
              ->orWhere('opponent_id', $user->id);
        })->where('status', 'completed')->count();

        $total_resolved = $failed_count + $completed_count;

        $stats = [
            'total_cards' => $user->digitalCards()->count(),
            'total_trophies' => $user->trophies()->count(),
            'failed_battles_pct' => $total_resolved > 0 ? round(($failed_count / $total_resolved) * 100) : 0,
            'completed_battles_pct' => $total_resolved > 0 ? round(($completed_count / $total_resolved) * 100) : 0,
        ];

        $isOwner = Auth::check() && Auth::id() === $user->id;

        return view('profile.show', compact('user', 'ownCards', 'trophies', 'templates', 'stats', 'isOwner', 'availableGames', 'availableLevels'));
    }

    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'bio' => 'nullable|string|max:500',
        ]);

        $data = $request->only(['bio']);

        if ($request->filled('temporary_avatar_path')) {
            $tmpPath = $request->input('temporary_avatar_path');
            if (Storage::disk('public')->exists($tmpPath)) {
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $newPath = 'avatars/' . basename($tmpPath);
                Storage::disk('public')->move($tmpPath, $newPath);
                Storage::disk('public')->setVisibility($newPath, 'public');
                $data['avatar'] = $newPath;
            }
        }

        $user->update($data);

        return redirect()->route('profile.show', $user->username)
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Search for users.
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $battle_id = $request->get('battle_id','');

        $users = [];

        
        $except = [];

        if($battle_id != ''){
            $battle = Battle::where('room_id',$battle_id)->first();

            if($battle){
                
                if($battle->challenger_id){
                    $except[] = $battle->challenger_id;
                }
                
                if($battle->opponent_id){
                    $except[] = $battle->opponent_id;
                }

                if($battle->marshall_id){
                    $except[] = $battle->marshall_id;
                }
            }
        }

        if (strlen($query) >= 1) {


            $users = User::where('username', 'like', "%{$query}%")->whereNotIn('id',$except)
                ->limit(20)
                ->get();
        }

        if ($request->ajax()) {
            return response()->json($users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'username' => $user->username,
                    'avatar_url' => $user->avatar_url,
                ];
            }));
        }

        return view('profile.search', compact('users', 'query'));
    }
}
