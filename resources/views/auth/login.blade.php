@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="text-center mb-4">
            <h2 style="font-family: 'Orbitron', sans-serif; color: #00f0ff; text-shadow: 0 0 15px rgba(0,240,255,0.3);">
                <i class="bi bi-lightning-charge-fill"></i> LOGIN
            </h2>
            <p style="color: #555577; font-size: 0.9rem;">Enter the arena</p>
        </div>

        <div class="neon-card p-4">
            <form method="POST" action="{{ route('login') }}" id="login-form">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email Address') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                           placeholder="your@email.com">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                           name="password" required autocomplete="current-password"
                           placeholder="••••••••">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                           style="background-color: rgba(15,15,35,0.9); border-color: rgba(0,240,255,0.2);">
                    <label class="form-check-label" for="remember" style="font-size: 0.85rem; color: #8888aa;">
                        {{ __('Remember Me') }}
                    </label>
                </div>

                <button type="submit" class="btn btn-neon w-100 mb-3" id="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i> {{ __('Login') }}
                </button>

                <div class="d-flex align-items-center my-3">
                    <hr class="flex-grow-1" style="border-color: rgba(255,255,255,0.1);">
                    <span class="mx-3" style="color: #555577; font-size: 0.8rem; font-family: 'Orbitron', sans-serif;">OR</span>
                    <hr class="flex-grow-1" style="border-color: rgba(255,255,255,0.1);">
                </div>

                <a href="{{ route('auth.google') }}" class="btn btn-outline-light w-100 mb-3 d-flex align-items-center justify-content-center" style="border-color: rgba(255,255,255,0.2); font-family: 'Orbitron', sans-serif; font-size: 0.8rem; letter-spacing: 1px;">
                    <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" alt="Google" style="width: 18px; margin-right: 10px;">
                    {{ __('LOGIN WITH GOOGLE') }}
                </a>

                @if (Route::has('password.request'))
                    <div class="text-center">
                        <a href="{{ route('password.request') }}" style="color: #555577; font-size: 0.85rem;">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <div class="text-center mt-3">
            <span style="color: #555577; font-size: 0.85rem;">Don't have an account?</span>
            <a href="{{ route('register') }}" style="color: #00f0ff; font-size: 0.85rem;">Sign Up</a>
        </div>
    </div>
</div>
@endsection
