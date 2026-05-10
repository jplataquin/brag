<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IdentityVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    /**
     * List all verification applications.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $verifications = IdentityVerification::with('user')
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.verifications.index', compact('verifications', 'status'));
    }

    /**
     * Show a specific verification application.
     */
    public function show(IdentityVerification $verification)
    {
        $verification->load('user');
        return view('admin.verifications.show', compact('verification'));
    }

    /**
     * Serve a verification photo (since they are on the private disk).
     */
    public function viewPhoto($id, $type)
    {
        $verification = IdentityVerification::findOrFail($id);
        $path = ($type === 'id') ? $verification->id_photo_path : $verification->selfie_photo_path;

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($path));
    }

    /**
     * Approve a verification application.
     */
    public function approve(Request $request, IdentityVerification $verification)
    {
        $verification->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        $verification->user->update(['is_verified' => true]);

        return redirect()->route('admin.verifications.index')
            ->with('success', 'Verification approved successfully.');
    }

    /**
     * Reject a verification application.
     */
    public function reject(Request $request, IdentityVerification $verification)
    {
        $verification->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        $verification->user->update(['is_verified' => false]);

        return redirect()->route('admin.verifications.index')
            ->with('success', 'Verification rejected.');
    }
}
