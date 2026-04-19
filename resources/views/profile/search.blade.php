@extends('layouts.app')

@section('title', 'Search Players')

@section('content')
<h1 class="page-title">
    <span class="page-title-accent"><i class="bi bi-search"></i></span> SEARCH PLAYERS
</h1>

<div class="row justify-content-center mb-4">
    <div class="col-lg-8">
        <form action="{{ route('search') }}" method="GET" id="search-form">
            <div class="input-group">
                <input type="text" class="form-control" name="q" value="{{ $query }}" placeholder="Search by username..." autofocus>
                <button class="btn btn-neon" type="submit">
                    <i class="bi bi-search"></i> SEARCH
                </button>
            </div>
        </form>
    </div>
</div>

@if(count($users) > 0)
    <div class="row g-3">
        @foreach($users as $user)
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('profile.show', $user->username) }}" class="neon-card p-3 d-flex align-items-center gap-3" style="text-decoration: none; color: inherit;">
                <img src="{{ $user->avatar_url }}" alt="" style="width: 48px; height: 48px; border-radius: 50%; border: 2px solid rgba(0,240,255,0.2);">
                <div>
                    <div style="font-family: 'Orbitron', sans-serif; font-size: 0.9rem; color: #00f0ff;">@<span>{{ $user->username }}</span></div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
@elseif($query)
    <div class="empty-state">
        <div class="empty-icon">🔍</div>
        <div class="empty-text">No players found for "{{ $query }}"</div>
    </div>
@endif
@endsection
