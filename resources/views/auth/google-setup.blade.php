@extends('layouts.app')

@section('title', 'Complete Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="text-center mb-4">
            <h2 style="font-family: 'Orbitron', sans-serif; color: #ff00ff; text-shadow: 0 0 15px rgba(255,0,255,0.3);">
                <i class="bi bi-person-badge"></i> SETUP PROFILE
            </h2>
            <p style="color: #555577; font-size: 0.9rem;">Just one more step before you enter the Arena</p>
        </div>

        <div class="neon-card p-4" style="border-color: rgba(255,0,255,0.2);">
            <form method="POST" action="{{ route('auth.google.setup') }}">
                @csrf

                <div class="mb-3">
                    <label for="username" class="form-label">{{ __('Username') }}</label>
                    <input id="username" type="text" class="form-control @error('username') is-invalid @enderror"
                           name="username" value="{{ old('username', $defaults['username']) }}" required autocomplete="username"
                           placeholder="Enter your gamertag">
                    <div class="form-text" style="color: #555577; font-size: 0.75rem;">This is your unique handle in the Arena.</div>
                    @error('username')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="firstname" class="form-label">{{ __('First Name') }}</label>
                        <input id="firstname" type="text" class="form-control @error('firstname') is-invalid @enderror"
                               name="firstname" value="{{ old('firstname', $defaults['firstname']) }}" required autocomplete="given-name"
                               placeholder="First Name">
                        @error('firstname')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="lastname" class="form-label">{{ __('Last Name') }}</label>
                        <input id="lastname" type="text" class="form-control @error('lastname') is-invalid @enderror"
                               name="lastname" value="{{ old('lastname', $defaults['lastname']) }}" required autocomplete="family-name"
                               placeholder="Last Name">
                        @error('lastname')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="birthdate" class="form-label">{{ __('Birthdate') }}</label>
                    <input id="birthdate" type="date" class="form-control @error('birthdate') is-invalid @enderror"
                           name="birthdate" value="{{ old('birthdate') }}" required>
                    <div class="form-text" style="color: #555577; font-size: 0.75rem;">Used for age verification and special rewards.</div>
                    @error('birthdate')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                @if($isNewUser)
                <div class="mb-4 form-check">
                    <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" name="terms" id="terms" {{ old('terms') ? 'checked' : '' }} required>
                    <label class="form-check-label" for="terms" style="font-size: 0.85rem; color: #8888aa;">
                        I agree to the <a href="{{ route('terms.show') }}" target="_blank" style="color: #00f0ff;">Terms of Service</a>
                    </label>
                    @error('terms')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                @endif

                <button type="submit" class="btn btn-neon w-100" style="background: linear-gradient(45deg, #ff00ff, #00f0ff); border: none;">
                    <i class="bi bi-check-circle-fill"></i> COMPLETE SETUP
                </button>
            </form>
        </div>

        <div class="text-center mt-3">
            <p style="color: #555577; font-size: 0.8rem;">Setting up profile for: <span style="color: #00f0ff;">{{ $defaults['email'] }}</span></p>
        </div>
    </div>
</div>
@endsection
