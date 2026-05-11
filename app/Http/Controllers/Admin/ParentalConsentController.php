<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ParentalConsentController extends Controller
{
    /**
     * Display a listing of pending parental consents.
     */
    public function index()
    {
        $pendingConsents = User::where('parental_consent_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.parental-consents.index', compact('pendingConsents'));
    }

    /**
     * Approve the parental consent.
     */
    public function approve(User $user)
    {
        if ($user->parental_consent_status !== 'pending') {
            return back()->with('error', 'This user is not pending parental consent.');
        }

        $user->update([
            'parental_consent_status' => 'approved'
        ]);

        return back()->with('success', "Parental consent approved for user: {$user->username}");
    }

    /**
     * Reject and purge the user account.
     */
    public function reject(User $user)
    {
        if ($user->parental_consent_status !== 'pending') {
            return back()->with('error', 'This user is not pending parental consent.');
        }

        $username = $user->username;
        $idPath = $user->parent_id_path;

        // Delete the ID file
        if ($idPath && Storage::disk('public')->exists($idPath)) {
            Storage::disk('public')->delete($idPath);
        }

        // Delete the user (Option 1: Purge)
        $user->delete();

        return back()->with('success', "Parental consent rejected and account purged for: {$username}");
    }

    /**
     * View the parent's ID photo.
     */
    public function viewId(User $user)
    {
        if (!$user->parent_id_path || !Storage::disk('public')->exists($user->parent_id_path)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($user->parent_id_path));
    }
}
