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

            // Redirect to setup if birthdate is missing
            if (!$user->birthdate) {
                return redirect()->route('auth.google.setup');
            }

            return redirect()->intended('/dashboard');
        } else {
            // User doesn't exist. Store Google data in session and redirect to setup.
            session([
                'google_user_id' => $googleUser->id,
                'google_user_email' => $googleUser->email,
                'google_user_name' => $googleUser->name,
                'google_user_avatar' => $googleUser->avatar,
            ]);

            return redirect()->route('auth.google.setup');
        }
    }

    /**
     * Show the profile setup form for Google users.
     */
    public function showSetupProfile()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->birthdate) {
                return redirect('/dashboard');
            }
            $defaults = [
                'username' => $user->username,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
            ];
            $isNewUser = false;
        } else if (session()->has('google_user_id')) {
            $name = session('google_user_name');
            $email = session('google_user_email');
            
            $nameParts = explode(' ', $name, 2);
            $firstname = $nameParts[0] ?? '';
            $lastname = $nameParts[1] ?? '';
            
            $defaults = [
                'username' => $this->generateUsername($name ?: explode('@', $email)[0]),
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
            ];
            $isNewUser = true;
        } else {
            return redirect('/login')->with('error', 'Authentication session expired.');
        }

        return view('auth.google-setup', compact('defaults', 'isNewUser'));
    }

    /**
     * Save the profile setup data.
     */
    public function saveSetupProfile(Request $request)
    {
        $isNewUser = !Auth::check();

        $rules = [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'birthdate' => 'required|date|before:today',
        ];

        if ($isNewUser) {
            $rules['username'] = 'required|string|max:255|unique:users,username';
            $rules['terms'] = 'required|accepted';
            $rules['privacy'] = 'required|accepted';
        } else {
            $user = Auth::user();
            $rules['username'] = 'required|string|max:255|unique:users,username,' . $user->id;
            // Existing users are caught by EnsureTermsAgreed middleware
        }

        $request->validate($rules);

        if ($isNewUser) {
            if (!session()->has('google_user_id')) {
                return redirect('/login')->with('error', 'Authentication session expired.');
            }

            $latestTerms = \App\Models\TermsOfService::latest('id')->first();
            $latestPrivacy = \App\Models\PrivacyPolicy::latest('id')->first();

            $user = User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'username' => $request->username,
                'email' => session('google_user_email'),
                'google_id' => session('google_user_id'),
                'password' => null, // No password for social users
                'email_verified_at' => now(), // Google emails are verified
                'birthdate' => $request->birthdate,
                'terms_version_agreed' => $latestTerms ? $latestTerms->id : 0,
                'privacy_version_agreed' => $latestPrivacy ? $latestPrivacy->id : 0,
            ]);

            // Fire the Verified event so listeners (like GrantWelcomeDiamonds) are triggered
            event(new \Illuminate\Auth\Events\Verified($user));

            session()->forget(['google_user_id', 'google_user_email', 'google_user_name', 'google_user_avatar']);
            
            Auth::login($user);
        } else {
            $user = Auth::user();
            $user->update([
                'username' => $request->username,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'birthdate' => $request->birthdate,
            ]);
            
            Auth::setUser($user->fresh());
        }

        return redirect('/dashboard')->with('success', 'Profile setup complete! Welcome to the Arena.');
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
