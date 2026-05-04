<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('username', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('firstname', 'like', '%' . $request->search . '%')
                  ->orWhere('lastname', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'is_admin' => 'boolean',
            'suspended_until' => 'nullable|date',
            'can_purchase_diamonds' => 'boolean',
        ]);

        $userData = $request->only(['firstname', 'lastname', 'username', 'email', 'is_admin']);
        $userData['can_purchase_diamonds'] = $request->has('can_purchase_diamonds');

        // Process suspension
        if ($request->filled('suspended_until')) {
            $userData['suspended_until'] = Carbon::parse($request->suspended_until);
        } else {
            $userData['suspended_until'] = null;
        }

        // Prevent self-suspension or un-admining if the admin is editing themselves
        if (auth()->id() === $user->id) {
            unset($userData['is_admin']);
            unset($userData['suspended_until']);
            unset($userData['can_purchase_diamonds']);
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')
                         ->with('success', "User '{$user->username}' updated successfully.");
    }

    public function updateDiamonds(Request $request, User $user)
    {
        $request->validate([
            'action' => 'required|in:credit,debit',
            'amount' => 'required|integer|min:1',
            'remarks' => 'required|string|max:255',
        ]);

        $amount = (int) $request->amount;

        if ($request->action === 'debit') {
            if ($user->diamonds_balance < $amount) {
                return back()->with('error', "Cannot deduct {$amount} diamonds. User only has {$user->diamonds_balance} diamonds.");
            }
            $user->deductDiamonds($amount, 'admin_adjustment', 'Admin: ' . $request->remarks, auth()->id());
            $message = "Successfully deducted {$amount} Diamonds from {$user->username}.";
        } else {
            $user->addDiamonds($amount, 'admin_adjustment', 'Admin: ' . $request->remarks, auth()->id());
            $message = "Successfully added {$amount} Diamonds to {$user->username}.";
        }

        return back()->with('success', $message);
    }
}
