@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<h1 class="page-title">
    <span class="page-title-accent"><i class="bi bi-gear-fill"></i></span> EDIT PROFILE
</h1>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="neon-card p-4">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profile-edit-form">
                @csrf
                @method('PUT')

                <div class="text-center mb-4">
                    <img src="{{ $user->avatar_url }}" alt="" style="width: 100px; height: 100px; border-radius: 50%; border: 3px solid rgba(0,240,255,0.3); margin-bottom: 0.5rem;">
                    <div style="font-size: 0.85rem; color: #00f0ff;">@<span>{{ $user->username }}</span></div>
                </div>

                <div class="mb-3">
                    <label for="bio" class="form-label">BIO</label>
                    <textarea class="form-control @error('bio') is-invalid @enderror"
                              id="bio" name="bio" rows="3" maxlength="500"
                              placeholder="Tell the world about your gaming prowess...">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="avatar" class="form-label">AVATAR</label>
                    <input type="file" class="form-control @error('avatar') is-invalid @enderror"
                           id="avatar" name="avatar" accept="image/*">
                    <small style="color: #555577; font-size: 0.75rem;">Max 2MB. Supports JPEG, PNG, GIF, WebP.</small>
                    @error('avatar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-neon" id="btn-update-profile">
                        <i class="bi bi-check-lg"></i> SAVE CHANGES
                    </button>
                    <a href="{{ route('profile.show', $user->username) }}" class="btn btn-neon-danger">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
