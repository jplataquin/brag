<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DigitalCard;
use App\Models\Template;
use App\Models\User;
use Illuminate\Http\Request;

class DigitalCardController extends Controller
{
    /**
     * Display a listing of the digital cards.
     */
    public function index(Request $request)
    {
        $query = DigitalCard::with(['owner', 'originalOwner', 'template', 'adminEditor']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('id', $search)
                  ->orWhereHas('owner', function($q) use ($search) {
                      $q->where('username', 'like', "%{$search}%");
                  })
                  ->orWhereHas('template', function($q) use ($search) {
                      $q->where('card_title', 'like', "%{$search}%");
                  });
        }

        $cards = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        return view('admin.cards.index', compact('cards'));
    }

    /**
     * Show the form for editing the specified digital card.
     */
    public function edit($id)
    {
        $card = DigitalCard::with(['owner', 'originalOwner', 'template', 'adminEditor'])->findOrFail($id);
        $templates = Template::orderBy('card_title')->get();
        $users = User::orderBy('username')->get();

        return view('admin.cards.edit', compact('card', 'templates', 'users'));
    }

    /**
     * Update the specified digital card in storage.
     */
    public function update(Request $request, $id)
    {
        $card = DigitalCard::findOrFail($id);

        $request->validate([
            'template_id' => 'required|exists:templates,id',
            'owner_id' => 'nullable|exists:users,id',
            'original_owner_id' => 'required|exists:users,id',
            'level' => 'required|integer|min:1|max:5',
            'status' => 'required|string|in:Maintained,Discontinued',
            'wins' => 'required|integer|min:0',
            'losses' => 'required|integer|min:0',
            'life_points' => 'required|integer|min:0',
            'is_trophy' => 'required|boolean',
        ]);

        $card->update([
            'template_id' => $request->template_id,
            'owner_id' => $request->owner_id,
            'original_owner_id' => $request->original_owner_id,
            'level' => $request->level,
            'status' => $request->status,
            'wins' => $request->wins,
            'losses' => $request->losses,
            'life_points' => $request->life_points,
            'is_trophy' => $request->is_trophy,
            'admin_editor_id' => auth()->id(),
            'admin_edited_at' => now(),
        ]);

        return redirect()->route('admin.cards.index')->with('success', "Digital Card #{$card->id} updated successfully.");
    }

    /**
     * Toggle the censorship status of a digital card.
     */
    public function toggleCensor(DigitalCard $card)
    {
        $card->update(['is_censored' => !$card->is_censored]);
        $status = $card->is_censored ? 'censored' : 'un-censored';
        return back()->with('success', "Digital Card #{$card->id} has been {$status}.");
    }
}
