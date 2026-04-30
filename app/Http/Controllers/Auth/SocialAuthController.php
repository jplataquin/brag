<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
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
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->id]);
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

            Auth::login($user);
        }

        return redirect()->intended('/home');
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
