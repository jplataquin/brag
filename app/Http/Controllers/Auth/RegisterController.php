<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TermsOfService;
use App\Models\PrivacyPolicy;
use App\Rules\Turnstile;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $validator = Validator::make($data, [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:30', 'unique:users', 'regex:/^[a-zA-Z0-9_]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'birthdate' => ['required', 'date', 'before_or_equal:' . now()->subYears(13)->format('Y-m-d')],
            'gender' => ['nullable', 'string', 'in:Male,Female,None'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['required', 'accepted'],
            'privacy' => ['required', 'accepted'],
            'cf-turnstile-response' => ['required', new Turnstile],
        ], [
            'birthdate.before_or_equal' => 'You must be at least 13 years old to register.',
        ]);

        $validator->sometimes(['parent_firstname', 'parent_lastname', 'parent_birthdate', 'parent_id_path', 'parent_consent_agreed'], 'required', function ($input) {
            try {
                return \Carbon\Carbon::parse($input->birthdate)->age < 18;
            } catch (\Exception $e) {
                return false;
            }
        });

        $validator->sometimes('parent_birthdate', 'before_or_equal:' . now()->subYears(18)->format('Y-m-d'), function ($input) {
            try {
                return \Carbon\Carbon::parse($input->birthdate)->age < 18;
            } catch (\Exception $e) {
                return false;
            }
        });

        $validator->sometimes('parent_consent_agreed', 'accepted', function ($input) {
            try {
                return \Carbon\Carbon::parse($input->birthdate)->age < 18;
            } catch (\Exception $e) {
                return false;
            }
        });

        return $validator;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return User
     */
    protected function create(array $data)
    {
        $latestTerms = TermsOfService::latest('id')->first();
        $termsVersion = $latestTerms ? $latestTerms->id : 0;

        $latestPrivacy = PrivacyPolicy::latest('id')->first();
        $privacyVersion = $latestPrivacy ? $latestPrivacy->id : 0;

        $age = \Carbon\Carbon::parse($data['birthdate'])->age;
        $isMinor = $age < 18;
        
        $parentIdPath = null;
        if ($isMinor && !empty($data['parent_id_path'])) {
            $tempPath = $data['parent_id_path'];
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($tempPath)) {
                $filename = basename($tempPath);
                $permanentPath = 'uploads/parent_ids/' . $filename;
                \Illuminate\Support\Facades\Storage::disk('public')->move($tempPath, $permanentPath);
                $parentIdPath = $permanentPath;
            }
        }

        $user = User::create([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'username' => strtolower($data['username']),
            'email' => $data['email'],
            'birthdate' => $data['birthdate'] ?? null,
            'gender' => $data['gender'] ?? 'None',
            'password' => Hash::make($data['password']),
            'terms_version_agreed' => $termsVersion,
            'privacy_version_agreed' => $privacyVersion,
            'parent_firstname' => $isMinor ? ($data['parent_firstname'] ?? null) : null,
            'parent_lastname' => $isMinor ? ($data['parent_lastname'] ?? null) : null,
            'parent_birthdate' => $isMinor ? ($data['parent_birthdate'] ?? null) : null,
            'parent_id_path' => $parentIdPath,
            'parental_consent_status' => $isMinor ? 'pending' : 'not_required',
        ]);

        return $user;
    }
}
