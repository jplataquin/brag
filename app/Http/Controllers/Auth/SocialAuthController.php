<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Exception;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Google authentication failed.');
        }

        // Find existing user by google_id or email
        $user = User::where('google_id', $googleUser->id)
            ->orWhere('email', $googleUser->email)
            ->first();

        if ($user) {
            // Update google_id if it's not set (in case of existing email match)
            $updates = [];
            if (!$user->google_id) {
                $updates['google_id'] = $googleUser->id;
            }
            // Mark email as verified if it wasn't already
            if (!$user->email_verified_at) {
                $updates['email_verified_at'] = now();
            }

            if (!empty($updates)) {
                $user->update($updates);
            }
            
            Auth::login($user);
        } else {
            // Create a new user
            $username = $this->generateUsername($googleUser->name ?: explode('@', $googleUser->email)[0]);
            
            // Handle names
            $nameParts = explode(' ', $googleUser->name, 2);
            $firstname = $nameParts[0] ?? null;
            $lastname = $nameParts[1] ?? null;

            $user = User::create([
                'firstname' => $firstname,
                'lastname' => $lastname,
                'username' => $username,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'password' => null, // No password for social users
                'email_verified_at' => now(), // Google emails are verified
            ]);

            // Fire the Verified event so listeners (like GrantWelcomeShards) are triggered
            event(new \Illuminate\Auth\Events\Verified($user));

            Auth::login($user);
        }

        // Redirect to setup if birthdate is missing
        if (!$user->birthdate) {
            return redirect()->route('auth.google.setup');
        }

        return redirect()->intended('/home');
    }

    /**
     * Show the profile setup form for Google users.
     */
    public function showSetupProfile()
    {
        $user = Auth::user();
        
        // If profile is already complete, go to home
        if ($user->birthdate) {
            return redirect('/home');
        }

        return view('auth.google-setup', compact('user'));
    }

    /**
     * Save the profile setup data.
     */
    public function saveSetupProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'birthdate' => 'required|date|before:today',
        ]);

        $user->update([
            'username' => $request->username,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'birthdate' => $request->birthdate,
        ]);

        return redirect('/home')->with('success', 'Profile setup complete! Welcome to the Arena.');
    }

    /**
     * Generate a unique username based on a name suggestion.
     */
    protected function generateUsername($suggestion)
    {
        $base = Str::slug($suggestion, '');
        if (empty($base)) {
            $base = 'user';
        }
        
        $username = $base;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }
}
