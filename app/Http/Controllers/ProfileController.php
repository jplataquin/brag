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
            ->get();

        $trophies = $user->trophies()
            ->with('template.gameTitle', 'originalOwner')
            ->latest()
            ->get();

        $templates = $user->templates()->with('gameTitle')->withCount('digitalCards')->get();

        $stats = [
            'total_cards' => $user->digitalCards()->count(),
            'total_trophies' => $user->trophies()->count(),
            'failed_battles' => Battle::where(function ($q) use ($user) {
                $q->where('challenger_id', $user->id)
                  ->orWhere('opponent_id', $user->id);
            })->where('status', 'failed')->count(),
            'total_wins' => Battle::where('winner_id', $user->id)->count(),
            'total_battles' => Battle::where(function ($q) use ($user) {
                $q->where('challenger_id', $user->id)
                  ->orWhere('opponent_id', $user->id);
            })->where('status', 'completed')->count(),
        ];

        $isOwner = Auth::check() && Auth::id() === $user->id;

        return view('profile.show', compact('user', 'ownCards', 'trophies', 'templates', 'stats', 'isOwner'));
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
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->only(['bio']);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
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

                if($battle->adjudicator_id){
                    $except[] = $battle->adjudicator_id;
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
