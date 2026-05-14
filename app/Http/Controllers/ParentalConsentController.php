<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ParentalConsentController extends Controller
{
    /**
     * Confirm parental consent.
     */
    public function confirm($token)
    {
        $user = User::where('parent_consent_token', $token)->firstOrFail();

        $user->update([
            'parental_consent_status' => 'approved',
            'parent_consent_token' => null, // Clear token after use
        ]);

        return view('auth.parental-consent-result', [
            'success' => true,
            'message' => 'Consent approved! Your child\'s account is now active.',
            'child' => $user->username
        ]);
    }

    /**
     * Reject parental consent.
     */
    public function reject($token)
    {
        $user = User::where('parent_consent_token', $token)->firstOrFail();

        // Delete the user account as requested
        $user->delete();

        return view('auth.parental-consent-result', [
            'success' => false,
            'message' => 'Account registration has been rejected and deleted.',
            'child' => null
        ]);
    }
}
