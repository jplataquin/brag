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
            'temporary_id_path' => 'required|string',
            'temporary_selfie_path' => 'required|string',
        ]);

        $tempIdPath = $request->input('temporary_id_path');
        $tempSelfiePath = $request->input('temporary_selfie_path');

        // Move photos securely from public temp storage to private storage
        // Using local disk (storage/app/verifications) which is not publicly accessible
        $idFilename = 'id_' . time() . '_' . Str::random(10) . '.' . pathinfo($tempIdPath, PATHINFO_EXTENSION);
        $selfieFilename = 'selfie_' . time() . '_' . Str::random(10) . '.' . pathinfo($tempSelfiePath, PATHINFO_EXTENSION);
        
        $finalIdPath = 'verifications/ids/' . $idFilename;
        $finalSelfiePath = 'verifications/selfies/' . $selfieFilename;

        // Note: Storage::disk('public') contains the temp uploads
        // We move them to Storage::disk('local') (private)
        if (Storage::disk('public')->exists($tempIdPath)) {
            Storage::disk('local')->put($finalIdPath, Storage::disk('public')->get($tempIdPath));
            Storage::disk('public')->delete($tempIdPath);
        } else {
            return back()->with('error', 'ID photo upload failed. Please try again.');
        }

        if (Storage::disk('public')->exists($tempSelfiePath)) {
            Storage::disk('local')->put($finalSelfiePath, Storage::disk('public')->get($tempSelfiePath));
            Storage::disk('public')->delete($tempSelfiePath);
        } else {
            return back()->with('error', 'Selfie photo upload failed. Please try again.');
        }

        IdentityVerification::create([
            'user_id' => $user->id,
            'id_photo_path' => $finalIdPath,
            'selfie_photo_path' => $finalSelfiePath,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Your verification application has been submitted and is awaiting review.');
    }
}
