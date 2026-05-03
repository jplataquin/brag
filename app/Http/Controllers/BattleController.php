<?php

namespace App\Http\Controllers;

use App\Models\Battle;
use App\Models\GameTitle;
use App\Models\DigitalCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BattleController extends Controller
{
    /**
     * Display a listing of battles.
     */
    public function index()
    {
        $user = Auth::user();
        
        $myBattles = Battle::where(function($q) use ($user) {
            for ($i = 1; $i <= 6; $i++) {
                $q->orWhere("team_a_user_{$i}", $user->id)
                  ->orWhere("team_b_user_{$i}", $user->id);
            }
            $q->orWhere('marshall_id', $user->id);
        })
        ->with(['gameTitle', 'marshall'])
        ->orderBy('updated_at', 'desc')
        ->paginate(15);

        $pendingInvites = collect(); // Legacy feature removed

        return view('battles.index', compact('myBattles', 'pendingInvites'));
    }

    /**
     * Show the form for creating a new battle.
     */
    public function create(Request $request)
    {
        if (Auth::user()->currentBattleRoom()) {
            return redirect()->route('battles.index')->with('error', 'You are already in an active battle room.');
        }
        
        $preselectedGameId = $request->query('game_id');
        $preselectedCardId = $request->query('card_id');
        
        return view('battles.create', compact('preselectedGameId', 'preselectedCardId'));
    }

    /**
     * Handle the battle room view.
     */
        public function room(Battle $battle)
    {
        $user = Auth::user();
        
        if ($battle->status === 'pending') {
            $isParticipant = false;
            for ($i = 1; $i <= $battle->no_players_per_team; $i++) {
                if ($battle->{"team_a_user_{$i}"} == $user->id || $battle->{"team_b_user_{$i}"} == $user->id) {
                    $isParticipant = true;
                    break;
                }
            }
            if (!$isParticipant) {
                return redirect()->route('battles.join', $battle);
            }
        }

        $myEligibleCards = collect();
        if ($user) {
            $myEligibleCards = $user->digitalCards()
                ->where('life_points', '>', 0)
                ->get()
                ->filter(fn($c) => $c->template->game_title_id == $battle->game_title_id);
        }

        // Logic for Participant Check (re-evaluating strictly for UI permissions)
        $isParticipant = false;
        if ($user) {
            for ($i = 1; $i <= $battle->no_players_per_team; $i++) {
                if ($battle->{"team_a_user_{$i}"} == $user->id && $battle->{"team_a_card_{$i}"}) $isParticipant = true;
                if ($battle->{"team_b_user_{$i}"} == $user->id && $battle->{"team_b_card_{$i}"}) $isParticipant = true;
            }
            if ($battle->marshall_id == $user->id) $isParticipant = true;
        }

        
        return view('battles.room', compact('battle', 'myEligibleCards', 'isParticipant'));
    }

    /**
     * Handle joining a battle.
     */
    public function join(Battle $battle)
    {
        $user = Auth::user();
        
        $isParticipant = false;
        for ($i = 1; $i <= $battle->no_players_per_team; $i++) {
            if ($battle->{"team_a_user_{$i}"} == $user->id || $battle->{"team_b_user_{$i}"} == $user->id) {
                $isParticipant = true;
                break;
            }
        }
        if ($isParticipant) {
            return redirect()->route('battles.room', $battle);
        }

        $myEligibleCards = collect();
        if ($user) {
            $myEligibleCards = $user->digitalCards()
                ->where('life_points', '>', 0)
                ->get()
                ->filter(fn($c) => $c->template->game_title_id == $battle->game_title_id);
        }

        $isParticipant = false; // By definition, if they are here, they are not a participant in the UI context either

        return view('battles.room', compact('battle', 'myEligibleCards', 'isParticipant'));
    }
}
