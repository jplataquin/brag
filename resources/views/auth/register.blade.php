@extends('layouts.app')

@section('title', 'Sign Up')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="text-center mb-4">
            <h2 style="font-family: 'Orbitron', sans-serif; color: #ff00ff; text-shadow: 0 0 15px rgba(255,0,255,0.3);">
                <i class="bi bi-person-plus-fill"></i> JOIN BRAG
            </h2>
            <p style="color: #555577; font-size: 0.9rem;">Create your account and enter the arena</p>
        </div>

        <div class="neon-card p-4">
            <form method="POST" action="{{ route('register') }}" id="register-form">
                @csrf

                <div class="mb-3">
                    <label for="username" class="form-label">USERNAME</label>
                    <input id="username" type="text" class="form-control @error('username') is-invalid @enderror"
                           name="username" value="{{ old('username') }}" required autocomplete="username"
                           placeholder="your_gamer_tag" maxlength="30" pattern="[a-zA-Z0-9_]+" autofocus>
                    <small style="color: #555577; font-size: 0.75rem;">Letters, numbers, and underscores only. This is your unique identity.</small>
                    @error('username')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email Address') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required autocomplete="email"
                           placeholder="your@email.com">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="birthdate" class="form-label">BIRTHDATE</label>
                    <input id="birthdate" type="date" class="form-control @error('birthdate') is-invalid @enderror"
                           name="birthdate" value="{{ old('birthdate') }}">
                    @error('birthdate')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="gender" class="form-label">GENDER</label>
                    <select id="gender" class="form-select @error('gender') is-invalid @enderror" name="gender">
                        <option value="None" {{ old('gender') === 'None' ? 'selected' : '' }}>None</option>
                        <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                           name="password" required autocomplete="new-password"
                           placeholder="••••••••">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>
                    <input id="password-confirm" type="password" class="form-control"
                           name="password_confirmation" required autocomplete="new-password"
                           placeholder="••••••••">
                </div>

                <button type="submit" class="btn btn-neon-magenta w-100" id="btn-register">
                    <i class="bi bi-person-plus-fill"></i> {{ __('CREATE ACCOUNT') }}
                </button>
            </form>
        </div>

        <div class="text-center mt-3">
            <span style="color: #555577; font-size: 0.85rem;">Already have an account?</span>
            <a href="{{ route('login') }}" style="color: #00f0ff; font-size: 0.85rem;">Login</a>
        </div>
    </div>
</div>
@endsection
