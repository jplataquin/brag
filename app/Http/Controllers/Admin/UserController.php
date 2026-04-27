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
        ]);

        // Process suspension
        if ($request->filled('suspended_until')) {
            $validated['suspended_until'] = Carbon::parse($request->suspended_until);
        } else {
            $validated['suspended_until'] = null;
        }

        // Prevent self-suspension or un-admining if the admin is editing themselves
        if (auth()->id() === $user->id) {
            unset($validated['is_admin']);
            unset($validated['suspended_until']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
                         ->with('success', "User '{$user->username}' updated successfully.");
    }

    public function updateShards(Request $request, User $user)
    {
        $request->validate([
            'action' => 'required|in:credit,debit',
            'amount' => 'required|integer|min:1',
            'remarks' => 'required|string|max:255',
        ]);

        $amount = (int) $request->amount;

        if ($request->action === 'debit') {
            if ($user->shards_balance < $amount) {
                return back()->with('error', "Cannot deduct {$amount} shards. User only has {$user->shards_balance} shards.");
            }
            $user->deductShards($amount, 'admin_adjustment', 'Admin: ' . $request->remarks, auth()->id());
            $message = "Successfully deducted {$amount} Shards from {$user->username}.";
        } else {
            $user->addShards($amount, 'admin_adjustment', 'Admin: ' . $request->remarks, auth()->id());
            $message = "Successfully added {$amount} Shards to {$user->username}.";
        }

        return back()->with('success', $message);
    }
}
