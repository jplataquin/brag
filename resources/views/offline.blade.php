@extends('layouts.app')

@section('title', 'Connection Lost')

@section('content')
<div class="container py-5 text-center">
    <div class="py-5">
        <i class="bi bi-wifi-off mb-4" style="font-size: 5rem; color: #ff00ff; text-shadow: 0 0 20px rgba(255, 0, 255, 0.5);"></i>
        <h1 class="mb-3" style="font-family: 'Orbitron', sans-serif; color: #fff; letter-spacing: 2px;">CONNECTION LOST</h1>
        <p class="lead text-muted mb-5">
            The Arena requires an active uplink to synchronize battles. <br>
            Please check your connection and try again.
        </p>
        
        <div class="d-flex justify-content-center">
            <button onclick="window.location.reload()" class="btn btn-neon px-5 py-3">
                <i class="bi bi-arrow-clockwise"></i> RECONNECT TO ARENA
            </button>
        </div>
    </div>
</div>
@endsection
