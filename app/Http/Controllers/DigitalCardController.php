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
            $query->orderBy('wins', $direction);
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
    public function show(DigitalCard $digitalCard)
    {
        $digitalCard->load('template.gameTitle', 'owner', 'originalOwner');
        return view('cards.show', compact('digitalCard'));
    }

    /**
     * Forge a new digital card from a template.
     */
    public function forge(Template $template)
    {
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
