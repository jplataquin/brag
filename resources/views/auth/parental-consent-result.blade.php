@extends('layouts.app')

@section('title', 'Parental Consent Result')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 text-center">
        <div class="neon-card p-5">
            @if($success)
                <i class="bi bi-check-circle-fill text-success mb-4" style="font-size: 4rem;"></i>
                <h2 style="font-family: 'Orbitron', sans-serif; color: #39ff14; margin-bottom: 20px;">CONSENT APPROVED</h2>
                <p class="text-white-50">{{ $message }}</p>
                @if($child)
                    <p class="mt-3">The account for <strong>{{ $child }}</strong> is now ready for use.</p>
                @endif
            @else
                <i class="bi bi-x-circle-fill text-danger mb-4" style="font-size: 4rem;"></i>
                <h2 style="font-family: 'Orbitron', sans-serif; color: #ff00ff; margin-bottom: 20px;">CONSENT REJECTED</h2>
                <p class="text-white-50">{{ $message }}</p>
            @endif

            <div class="mt-5">
                <a href="{{ url('/') }}" class="btn btn-outline-info">Return to Home</a>
            </div>
        </div>
    </div>
</div>
@endsection
