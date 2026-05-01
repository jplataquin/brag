<?php

namespace App\Http\Controllers;

use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;

class PrivacyPolicyController extends Controller
{
    /**
     * Admin view to manage privacy policy.
     */
    public function index()
    {
        $latestPrivacy = PrivacyPolicy::latest('id')->first();
        $history = PrivacyPolicy::orderBy('id', 'desc')->get();
        return view('admin.privacy.index', compact('latestPrivacy', 'history'));
    }

    /**
     * Admin view to see a specific previous version.
     */
    public function showPrevious($id)
    {
        $privacy = PrivacyPolicy::findOrFail($id);
        return view('admin.privacy.show_previous', compact('privacy'));
    }

    /**
     * Admin action to save a new version of privacy policy.
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $latestPrivacy = PrivacyPolicy::latest('id')->first();

        // Prevent saving if there are no changes
        if ($latestPrivacy && trim($latestPrivacy->content) === trim($request->content)) {
            return redirect()->back()->with('error', 'No changes were detected. A new version was not created.');
        }

        PrivacyPolicy::create([
            'content' => $request->content,
        ]);

        return redirect()->route('admin.privacy.index')->with('success', 'New Privacy Policy version created successfully.');
    }

    /**
     * User view to read the latest privacy policy.
     */
    public function show()
    {
        $latestPrivacy = PrivacyPolicy::latest('id')->first();
        
        if (!$latestPrivacy) {
            return redirect()->route('dashboard');
        }

        return view('privacy.show', compact('latestPrivacy'));
    }

    /**
     * User action to agree to the latest privacy policy.
     */
    public function agree(Request $request)
    {
        $latestPrivacy = PrivacyPolicy::latest('id')->first();

        if ($latestPrivacy) {
            $user = auth()->user();
            $user->privacy_version_agreed = $latestPrivacy->id;
            $user->save();
        }

        return redirect()->route('dashboard')->with('success', 'You have agreed to the latest Privacy Policy.');
    }
}
