<?php

namespace App\Http\Controllers;

use App\Models\DigitalCard;
use App\Models\Template;
use App\Services\CardForgeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DigitalCardController extends Controller
{
    protected $forgeService;

    public function __construct(CardForgeService $forgeService)
    {
        $this->forgeService = $forgeService;
    }

    /**
     * Display a public gallery of all digital cards.
     */
    public function gallery(Request $request)
    {
        $sortBy = $request->query('sort', 'latest');
        $gameId = $request->query('game');
        $direction = $request->query('dir', 'desc');
        $search = $request->query('search');

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $query = DigitalCard::with(['template.gameTitle', 'owner', 'originalOwner']);

        if ($gameId) {
            $query->whereHas('template', function ($q) use ($gameId) {
                $q->where('game_title_id', $gameId);
            });
        }

        if ($search) {
            $query->whereHas('template', function ($q) use ($search) {
                $q->where('card_title', 'like', '%' . $search . '%');
            });
        }

        if ($sortBy === 'level') {
            $query->orderBy('level', $direction);
        } elseif ($sortBy === 'name') {
            $query->select('digital_cards.*')
                ->join('templates', 'digital_cards.template_id', '=', 'templates.id')
                ->orderBy('templates.card_title', $direction);
        } elseif ($sortBy === 'serial') {
            $query->orderBy('id', $direction);
        } else {
            $query->orderBy('updated_at', $direction);
        }

        $cards = $query->paginate(24)->appends($request->query());

        $games = \App\Models\GameTitle::all();

        return view('cards.gallery', compact('cards', 'sortBy', 'direction', 'games', 'gameId', 'search'));
    }

    /**
     * Display a listing of the user's digital cards.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $sortBy = $request->query('sort', 'latest');
        $gameId = $request->query('game');
        $direction = $request->query('dir', 'asc'); // Default to ascending as requested

        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'asc';
        }

        $query = $user->digitalCards()
            ->with(['template.gameTitle', 'originalOwner']);

        if ($gameId) {
            $query->whereHas('template', function ($q) use ($gameId) {
                $q->where('game_title_id', $gameId);
            });
        }

        if ($sortBy === 'level') {
            $query->orderBy('level', $direction);
        } elseif ($sortBy === 'name') {
            $query->select('digital_cards.*')
                ->join('templates', 'digital_cards.template_id', '=', 'templates.id')
                ->orderBy('templates.card_title', $direction);
        } elseif ($sortBy === 'serial') {
            $query->orderBy('id', $direction);
        } else {
            // For 'latest', if the user asks for 'asc', it should mean "show me newest first" logically.
            // But we'll map 'asc' -> 'desc' (latest first) and 'desc' -> 'asc' (oldest first).
            $query->orderBy('updated_at', $direction === 'asc' ? 'desc' : 'asc');
        }

        $cards = $query->get();

        // Get unique games the user has cards for to populate the filter dropdown
        $games = $user->digitalCards()
            ->with('template.gameTitle')
            ->get()
            ->map(function ($card) {
                return $card->template->gameTitle;
            })
            ->unique('id')
            ->values();

        return view('cards.index', compact('cards', 'sortBy', 'direction', 'games', 'gameId'));
    }

    /**
     * Display the specified digital card.
     */
    public function show($id)
    {
        $digitalCard = DigitalCard::withTrashed()
            ->with(['template.gameTitle', 'template.user', 'owner', 'originalOwner'])
            ->findOrFail($id);

        return view('cards.show', compact('digitalCard'));
    }

    /**
     * Heal a digital card.
     */
    public function heal($id)
    {
        $card = DigitalCard::findOrFail($id);
        $user = auth()->user();

        if ($card->owner_id !== $user->id) {
            return back()->with('error', 'You do not own this card.');
        }

        if ($card->life_points >= 3) {
            return back()->with('error', 'This card is already at maximum health.');
        }

        // Check if card is in an active battle (usually can't heal while battling)
        $inBattle = \App\Models\Battle::whereIn('status', ['pending', 'active', 'ready'])
            ->where(function ($q) use ($card) {
                for ($i = 1; $i <= 6; $i++) {
                    $q->orWhere("team_a_card_{$i}", $card->id)
                      ->orWhere("team_b_card_{$i}", $card->id);
                }
            })->exists();

        if ($inBattle) {
            return back()->with('error', 'You cannot heal a card that is currently in a battle.');
        }

        $cost = 5;

        if ($user->diamonds_balance < $cost) {
            return back()->with('error', "You need at least {$cost} Diamonds to heal this card.");
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($card, $cost, $user) {
            $user->deductDiamonds($cost, 'system', "Healed card #{$card->serial_number} ({$card->template->card_title})");
            
            $card->life_points += 1;
            $card->save();
        });

        return back()->with('success', "💖 Card successfully healed! Restored 1 Life Point for {$cost} Diamond(s).");
    }

    /**
     * Burn a digital card.
     */
    public function burn($id)
    {
        $card = DigitalCard::findOrFail($id);

        if ($card->owner_id !== auth()->id()) {
            return back()->with('error', 'You do not own this card.');
        }

        // Check if card is in an active battle
        $inBattle = \App\Models\Battle::whereIn('status', ['pending', 'active', 'ready'])
            ->where(function ($q) use ($card) {
                for ($i = 1; $i <= 6; $i++) {
                    $q->orWhere("team_a_card_{$i}", $card->id)
                      ->orWhere("team_b_card_{$i}", $card->id);
                }
            })->exists();

        if ($inBattle) {
            return back()->with('error', 'You cannot burn a card that is currently in a battle.');
        }

        // Give diamonds based on level
        $diamonds = $card->level;
        $user = auth()->user();

        \Illuminate\Support\Facades\DB::transaction(function () use ($card, $diamonds, $user) {
            $user->addDiamonds($diamonds, 'system', "Burned card #{$card->serial_number} ({$card->template->card_title} - Level {$card->level})");

            // Burn it
            $card->update([
                'burned_at' => now(), 
                'burned_by' => $user->id,
                'owner_id' => null
            ]);
            $card->delete();
        });

        return redirect()->route('cards.index')->with('success', "Card successfully burned! You received {$diamonds} Diamond(s).");
    }

    /**
     * Get the battle history for the card.
     */
    public function history($id, Request $request)
    {
        $digitalCard = DigitalCard::withTrashed()->findOrFail($id);
        $limit = 10;
        $offset = $request->query('offset', 0);

        $battles = \App\Models\Battle::where('status', 'completed')
            ->where(function ($query) use ($digitalCard) {
                for ($i = 1; $i <= 6; $i++) {
                    $query->orWhere("team_a_card_{$i}", $digitalCard->id)
                          ->orWhere("team_b_card_{$i}", $digitalCard->id);
                }
            })
            ->orderBy('updated_at', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get();

        return response()->json([
            'battles' => $battles->map(function ($battle) use ($digitalCard) {
                $isTeamA = false;
                $slotIndex = 0;
                for ($i = 1; $i <= $battle->no_players_per_team; $i++) {
                    if ($battle->{"team_a_card_{$i}"} == $digitalCard->id) {
                        $isTeamA = true;
                        $slotIndex = $i;
                        break;
                    } elseif ($battle->{"team_b_card_{$i}"} == $digitalCard->id) {
                        $isTeamA = false;
                        $slotIndex = $i;
                        break;
                    }
                }

                $isWin = ($isTeamA && $battle->winner_team === 'A') || (!$isTeamA && $battle->winner_team === 'B');
                
                // Identify the specific opponent in the opposing team's same slot
                $opponentId = $isTeamA ? $battle->{"team_b_user_{$slotIndex}"} : $battle->{"team_a_user_{$slotIndex}"};
                $opponent = \App\Models\User::find($opponentId);

                return [
                    'id' => $battle->id,
                    'room_id' => $battle->id, // Assuming room_id is now just id or similar
                    'date' => $battle->updated_at->format('M j, Y H:i'),
                    'opponent_name' => $opponent ? $opponent->username : 'Unknown',
                    'result' => $isWin ? 'WIN' : 'LOSS',
                    'result_color' => $isWin ? '#39ff14' : '#ff0000',
                ];
            }),
            'has_more' => $battles->count() == $limit
        ]);
    }

    /**
     * Forge a new digital card from a template.
     */
    public function forge(Template $template)
    {
        if (!\App\Models\PlatformSetting::current()->allow_card_forging) {
            return redirect()->route('dashboard')->with('error', 'Card forging is currently disabled by administrators.');
        }

        $user = Auth::user();

        try {
            $card = $this->forgeService->forge($user, $template);
            return redirect()->route('cards.show', $card)
                ->with('success', '🔥 Digital Card forged successfully! Serial #' . $card->serial_number);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
