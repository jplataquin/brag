<?php

namespace App\Http\Controllers;

use App\Models\DigitalCard;
use App\Models\Battle;
use App\Models\BattleInvite;
use App\Models\User;
use App\Services\BattleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BattleController extends Controller
{
    protected $battleService;

    public function __construct(BattleService $battleService)
    {
        $this->battleService = $battleService;
    }

    /**
     * Display a listing of battles.
     */
    public function index()
    {
        $user = Auth::user();

        $myBattles = Battle::where('challenger_id', $user->id)
            ->orWhere('opponent_id', $user->id)
            ->orWhere('marshall_id', $user->id)
            ->with(['challenger', 'opponent', 'marshall', 'challengerCard.template', 'opponentCard.template'])
            ->latest()
            ->paginate(12);

        $pendingInvites = $user->battleInvites()
            ->active()
            ->with('battle.challenger', 'battle.challengerCard.template')
            ->get();

        return view('battles.index', compact('myBattles', 'pendingInvites'));
    }

    /**
     * Show the form for creating a new battle.
     */
    public function create(Request $request)
    {
        $cards = Auth::user()->digitalCards()
            ->where('life_points', '>', 0)
            ->with('template.gameTitle')
            ->get();

        // Get unique games the user has cards for
        $games = $cards->map(function ($card) {
            return $card->template->gameTitle;
        })->unique('id')->values();

        $preSelectedGameId = $request->query('game_id');
        $preSelectedCardId = $request->query('card_id');

        return view('battles.create', compact('cards', 'games', 'preSelectedGameId', 'preSelectedCardId'));
    }

    /**
     * Store a newly created battle.
     */
    public function store(Request $request)
    {
        $request->validate([
            'card_id' => 'required|exists:digital_cards,id',
            'terms' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $card = DigitalCard::findOrFail($request->card_id);

        try {
            $battle = $this->battleService->createBattle($user, $card, $request->terms);

            return redirect()->route('battles.room', $battle)
                ->with('success', '⚔️ Battle Room created! Waiting for an opponent.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified battle room.
     */
    public function room(Battle $battle)
    {
        $user = Auth::user();

        if($battle->status == 'pending' && $user->id != $battle->challenger_id){
            return redirect()->route('battles.join.ready', $battle);
        }

        $battle->load([
            'challenger', 'opponent', 'marshall',
            'challengerCard.template.gameTitle', 'opponentCard.template.gameTitle',
            'invites.invitedUser',
            'activities.user',
        ]);

      

        $availableCards = $user->digitalCards()
            ->with('template.gameTitle')
            ->get();

        $isParticipant = in_array($user->id, [
            $battle->challenger_id,
            $battle->opponent_id,
            $battle->marshall_id,
        ]);

        return view('battles.room', compact('battle', 'availableCards', 'isParticipant'));
    }

    /**
     * Return battle state as JSON for real-time UI updates.
     */
    public function json(Battle $battle)
    {
        $battle->load([
            'challenger', 'opponent', 'marshall',
            'challengerCard.template.gameTitle', 'opponentCard.template.gameTitle',
            'activities.user',
        ]);

        return response()->json([
            'battle' => $battle,
            'isParticipant' => in_array(Auth::id(), [
                $battle->challenger_id,
                $battle->opponent_id,
                $battle->marshall_id,
            ]),
            'authId' => Auth::id()
        ]);
    }
    public function showJoinReadyPage(Battle $battle)
    {
        $user = Auth::user();

        // Ensure battle is pending and does not have an opponent yet
        if ( ( $battle->status !== 'pending' || !is_null($battle->opponent_id) ) &&  $user->id != $battle->challenger_id ) {
            return redirect()->route('battles.room', $battle)->with('error', 'This battle is no longer joinable.');
        }

        

        // Get challenger's card details for filtering
        $challengerCard = $battle->challengerCard;
        if (!$challengerCard) {
            return back()->with('error', 'Challenger\'s card not found.');
        }

        // Filter user's cards: same game title, same level, and NOT currently in another active battle
        $activeBattleCardIds = \App\Models\Battle::whereIn('status', ['pending', 'ready', 'active'])
            ->where(function($q) {
                $q->whereNotNull('challenger_card_id')
                  ->orWhereNotNull('opponent_card_id');
            })
            ->get()
            ->flatMap(function($b) {
                return [$b->challenger_card_id, $b->opponent_card_id];
            })
            ->filter()
            ->unique()
            ->toArray();

        $eligibleCards = $user->digitalCards()
            ->where('id', '!=', $challengerCard->id) // Cannot bet the exact same card
            ->where('life_points', '>', 0)
            ->whereHas('template', function ($query) use ($challengerCard) {
                $query->where('game_title_id', $challengerCard->template->game_title_id);
            })
            ->whereNotIn('id', $activeBattleCardIds)
            ->with(['template.gameTitle', 'originalOwner'])
            ->get()
            ->filter(function ($card) use ($challengerCard) {
                return $card->level == $challengerCard->level;
            });
        
        $challengerCard->load(['template.gameTitle', 'originalOwner']);

        return view('battles.ready', compact('battle', 'challengerCard', 'eligibleCards'));
    }

    /**
     * Confirm joining a battle with a selected card.
     */
    public function confirmJoin(Request $request, Battle $battle)
    {
        // Ensure battle is pending and does not have an opponent yet
        if ($battle->status !== 'pending' || !is_null($battle->opponent_id)) {
            return redirect()->route('battles.room', $battle)->with('error', 'This battle is no longer joinable.');
        }

        $request->validate([
            'card_id' => 'required|exists:digital_cards,id',
        ]);

        $user = Auth::user();
        $opponentCard = DigitalCard::findOrFail($request->card_id);

        // Server-side re-validation for security
        $challengerCard = $battle->challengerCard;
        if (!$challengerCard) {
            return back()->with('error', 'Challenger\'s card not found.');
        }

        if ($opponentCard->owner_id !== $user->id) {
            return back()->with('error', 'You do not own the selected card.');
        }

        if ($opponentCard->template->game_title_id !== $challengerCard->template->game_title_id) {
            return back()->with('error', 'Your card must be from the same game title as the challenger\'s card.');
        }

        if ($opponentCard->level != $challengerCard->level) {
            return back()->with('error', 'Your card must be the same level as the challenger\'s card.');
        }

        // Final security check: ensure card is not in another active battle
        $isInBattle = \App\Models\Battle::whereIn('status', ['pending', 'ready', 'active'])
            ->where(function($q) use ($opponentCard) {
                $q->where('challenger_card_id', $opponentCard->id)
                  ->orWhere('opponent_card_id', $opponentCard->id);
            })
            ->exists();

        if ($isInBattle) {
            return back()->with('error', 'The selected card is already committed to another active battle.');
        }

        try {
            $this->battleService->joinBattle($battle, $user, $opponentCard);
            return redirect()->route('battles.room', $battle)
                ->with('success', '🎮 You joined the battle! Let the battle begin.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Start the battle (challenger only).
     */
    public function start(Battle $battle)
    {
        $user = Auth::user();

        try {
            $this->battleService->startBattle($battle, $user);
            return redirect()->route('battles.room', $battle)
                ->with('success', '🔥 The battle has officially begun! Good luck.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject the opponent (challenger only).
     */
    public function rejectOpponent(Battle $battle)
    {
        $user = Auth::user();

        try {
            $this->battleService->rejectOpponent($battle, $user);
            return redirect()->route('battles.room', $battle)
                ->with('success', 'Opponent rejected. The room is now open for new challengers.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Elect an marshall.
     */
    public function electMarshall(Request $request, Battle $battle)
    {
        $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                \Illuminate\Validation\Rule::notIn([$battle->challenger_id, $battle->opponent_id])
            ],
        ], [
            'user_id.not_in' => 'Battle participants cannot be elected as marshalls.',
            'user_id.exists' => 'The selected user is invalid.',
        ]);

        $nominee = User::findOrFail($request->user_id);

        try {
            $this->battleService->electMarshall($battle, Auth::user(), $nominee);
            return back()->with('success', "⚖️ You have elected {$nominee->username} as marshall.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Accept an marshall election.
     */
    public function acceptMarshall(Battle $battle)
    {
        try {
            $this->battleService->respondToMarshallElection($battle, Auth::user(), true);
            return redirect()->route('battles.room', $battle)
                ->with('success', '⚖️ You are now the marshall of this battle.');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', $e->getMessage());
        }
    }

    /**
     * Reject an marshall election.
     */
    public function rejectMarshall(Battle $battle)
    {
        try {
            $this->battleService->respondToMarshallElection($battle, Auth::user(), false);
            return redirect()->route('dashboard')->with('success', 'You have rejected the marshall role.');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('error', $e->getMessage());
        }
    }

    /**
     * Marshall leaves the battle.
     */
    public function leaveMarshall(Battle $battle)
    {
        try {
            $this->battleService->marshallLeave($battle, Auth::user());
            return redirect()->route('dashboard')->with('success', 'You have left the battle room.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Send an invite.
     */
    public function invite(Request $request, Battle $battle)
    {
        $request->validate([
            'username' => 'required|exists:users,username',
            'role' => 'required|in:opponent,marshall',
        ]);

        $invitedUser = User::where('username', $request->username)->firstOrFail();

        try {
            $this->battleService->sendInvite($battle, Auth::user(), $invitedUser, $request->role);
            return back()->with('success', "Invite sent to {$invitedUser->username} as " . strtoupper($request->role));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Decline a pending invite.
     */
    public function declineInvite(\App\Models\BattleInvite $invite)
    {
        if ($invite->invited_user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized to decline this invite.');
        }

        $invite->update(['status' => 'declined']);
        
        return back()->with('success', 'Invite declined successfully.');
    }

    /**
     * Declare the winner of the battle.
     */
    public function declareWinner(Request $request, Battle $battle)
    {
        $request->validate([
            'winner_id' => 'required|exists:users,id',
        ]);

        $user = Auth::user();
        $winner = User::findOrFail($request->winner_id);

        try {
            $this->battleService->declareWinner($battle, $winner, $user);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Winner declaration recorded.'
                ]);
            }
            
            return redirect()->route('battles.room', $battle)
                ->with('success', '🏆 Winner declared! The loser\'s card has been transferred.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Cancel a battle or request cancellation.
     */
    public function cancel(Request $request, Battle $battle)
    {
        $user = Auth::user();

        try {
            $updatedBattle = $this->battleService->cancelBattle($battle, $user);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'status' => $updatedBattle->status,
                    'message' => $updatedBattle->status === 'cancelled' ? 'Battle cancelled successfully.' : 'Cancellation request sent to the other player.'
                ]);
            }

            if ($updatedBattle->status === 'cancelled') {
                return redirect()->route('battles.index')
                    ->with('success', 'Battle cancelled successfully.');
            }

            return back()->with('success', 'Cancellation request sent to the other player.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Respond to a cancellation request.
     */
    public function respondToCancellation(Request $request, Battle $battle)
    {
        $request->validate([
            'agreed' => 'required|boolean',
        ]);

        $user = Auth::user();

        try {
            $updatedBattle = $this->battleService->respondToCancellation($battle, $user, (bool) $request->agreed);
            
            if ($updatedBattle->status === 'cancelled') {
                return redirect()->route('battles.index')
                    ->with('success', 'Battle cancelled by mutual agreement.');
            }

            if (!$request->agreed) {
                return back()->with('success', 'Cancellation request rejected.');
            }

            return back()->with('success', 'You agreed to the cancellation. Waiting for the other player.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Poke the other player in the room.
     */
    public function poke(Battle $battle)
    {
        try {
            $this->battleService->pokePlayer($battle, Auth::user());
            return response()->json(['success' => true, 'message' => 'You poked the other player!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
