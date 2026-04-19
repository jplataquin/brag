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

        $query = $user->digitalCards()
            ->with(['template.gameTitle', 'originalOwner']);

        if ($sortBy === 'level') {
            $query->orderBy('wins', 'desc');
        } elseif ($sortBy === 'game') {
            $query->select('digital_cards.*')
                ->join('templates', 'digital_cards.template_id', '=', 'templates.id')
                ->join('game_titles', 'templates.game_title_id', '=', 'game_titles.id')
                ->orderBy('game_titles.title', 'asc');
        } else {
            $query->latest('updated_at');
        }

        $cards = $query->get();

        return view('cards.index', compact('cards', 'sortBy'));
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
