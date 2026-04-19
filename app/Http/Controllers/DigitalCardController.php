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
    public function index()
    {
        $user = Auth::user();

        $ownCards = $user->digitalCards()
            ->where('original_owner_id', $user->id)
            ->where('is_trophy', false)
            ->with('template.gameTitle')
            ->get();

        $trophies = $user->trophies()
            ->with('template.gameTitle', 'originalOwner')
            ->latest()
            ->get();

        return view('cards.index', compact('ownCards', 'trophies'));
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
