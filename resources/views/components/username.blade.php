<span class="username-wrapper {{ $user->is_untrustworthy ? 'untrustworthy-glow' : '' }}" style="{{ $user->is_untrustworthy ? 'color: #ff0000; text-shadow: 0 0 8px #ff0000; font-weight: bold;' : '' }}">
    @if($user->is_verified)
        <i class="bi bi-patch-check-fill text-primary me-1" title="Verified User"></i>
    @endif
    {{ $user->username }}
</span>

@if($user->is_untrustworthy)
<style>
    @keyframes red-glow {
        0% { text-shadow: 0 0 5px #ff0000, 0 0 10px #ff0000; }
        50% { text-shadow: 0 0 10px #ff0000, 0 0 20px #ff0000; }
        100% { text-shadow: 0 0 5px #ff0000, 0 0 10px #ff0000; }
    }
    .untrustworthy-glow {
        animation: red-glow 1.5s infinite alternate;
    }
</style>
@endif
