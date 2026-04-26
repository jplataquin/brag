<?php

namespace App\Http\Controllers;

use App\Models\TermsOfService;
use Illuminate\Http\Request;

class TermsOfServiceController extends Controller
{
    /**
     * Admin view to manage terms of service.
     */
    public function index()
    {
        $latestTerms = TermsOfService::latest('id')->first();
        return view('admin.terms.index', compact('latestTerms'));
    }

    /**
     * Admin action to save a new version of terms of service.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        TermsOfService::create([
            'content' => $request->content,
        ]);

        return redirect()->route('admin.terms.index')->with('success', 'New Terms of Service version created successfully.');
    }

    /**
     * User view to read the latest terms of service.
     */
    public function show()
    {
        $latestTerms = TermsOfService::latest('id')->first();
        
        if (!$latestTerms) {
            return redirect()->route('dashboard');
        }

        return view('terms.show', compact('latestTerms'));
    }

    /**
     * User action to agree to the latest terms of service.
     */
    public function agree(Request $request)
    {
        $latestTerms = TermsOfService::latest('id')->first();

        if ($latestTerms) {
            $user = auth()->user();
            $user->terms_version_agreed = $latestTerms->id;
            $user->save();
        }

        return redirect()->route('dashboard')->with('success', 'You have agreed to the latest Terms of Service.');
    }
}
