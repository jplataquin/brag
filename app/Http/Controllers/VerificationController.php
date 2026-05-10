<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IdentityVerification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VerificationController extends Controller
{
    /**
     * Store a new verification application.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->is_verified) {
            return back()->with('info', 'You are already verified.');
        }

        if ($user->pending_verification) {
            return back()->with('error', 'You already have a pending verification application.');
        }

        $request->validate([
            'id_photo' => 'required|image|max:5120', // 5MB max
            'selfie_photo' => 'required|image|max:5120',
        ]);

        // Store photos securely on the private disk
        $idPath = $request->file('id_photo')->store('verifications/ids', 'local');
        $selfiePath = $request->file('selfie_photo')->store('verifications/selfies', 'local');

        IdentityVerification::create([
            'user_id' => $user->id,
            'id_photo_path' => $idPath,
            'selfie_photo_path' => $selfiePath,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Your verification application has been submitted and is awaiting review.');
    }
}
