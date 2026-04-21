@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card bg-dark-glass text-white border-neon">
                <div class="card-header border-0 d-flex flex-column flex-sm-row justify-content-between align-items-center p-4 gap-3">
                    <h2 class="m-0 neon-text text-center text-sm-start fs-4 fs-sm-2" style="letter-spacing: 2px;">
                        <i class="bi bi-bell-fill me-2"></i> NOTIFICATIONS
                    </h2>
                    @if($notifications->where('read_at', null)->count() > 0)
                        <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="w-100 w-sm-auto text-center">
                            @csrf
                            <button type="submit" class="btn btn-outline-neon btn-sm w-100 w-sm-auto">
                                MARK ALL AS READ
                            </button>
                        </form>
                    @endif
                </div>
                
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($notifications as $notification)
                            <div class="list-group-item bg-transparent border-neon-faint p-3 p-md-4 notification-row {{ $notification->read_at ? '' : 'unread-row' }}">
                                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3 gap-md-4">
                                    <div class="d-flex align-items-start align-items-md-center gap-3 flex-grow-1 w-100">
                                        <div class="flex-shrink-0 mt-1 mt-md-0">
                                            @if(($notification->data['type'] ?? '') === 'invite')
                                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(0, 240, 255, 0.1); border: 2px solid rgba(0, 240, 255, 0.4);">
                                                    <i class="bi bi-envelope-fill fs-5" style="color: #00f0ff;"></i>
                                                </div>
                                            @elseif(($notification->data['type'] ?? '') === 'marshall_election')
                                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(255, 221, 0, 0.1); border: 2px solid rgba(255, 221, 0, 0.4);">
                                                    <i class="bi bi-shield-fill-check fs-5" style="color: #ffdd00;"></i>
                                                </div>
                                            @elseif(($notification->data['type'] ?? '') === 'poke')
                                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(0, 240, 255, 0.1); border: 2px solid rgba(0, 240, 255, 0.4);">
                                                    <i class="bi bi-hand-index-thumb-fill fs-5" style="color: #00f0ff;"></i>
                                                </div>
                                            @elseif(($notification->data['type'] ?? '') === 'victory')
                                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(57, 255, 20, 0.1); border: 2px solid rgba(57, 255, 20, 0.4);">
                                                    <i class="bi bi-trophy-fill fs-5" style="color: #39ff14;"></i>
                                                </div>
                                            @elseif(($notification->data['type'] ?? '') === 'defeat')
                                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(255, 0, 0, 0.1); border: 2px solid rgba(255, 0, 0, 0.4);">
                                                    <i class="bi bi-hand-thumbs-down-fill fs-5" style="color: #ff0000;"></i>
                                                </div>
                                            @else
                                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(255, 0, 255, 0.1); border: 2px solid rgba(255, 0, 255, 0.4);">
                                                    <i class="bi bi-lightning-fill fs-5" style="color: #ff00ff;"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1" style="color: #fff; font-size: 1rem; line-height: 1.4;">{{ $notification->data['message'] }}</h5>
                                            <p class="mb-0 text-muted" style="font-size: 0.8rem;">
                                                <i class="bi bi-clock me-1"></i> {{ $notification->created_at->format('M d, Y h:i A') }} ({{ $notification->created_at->diffForHumans() }})
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 align-self-end align-self-md-center mt-2 mt-md-0 w-100" style="max-width: 150px;">
                                        <a href="{{ $notification->data['action_url'] ?? '#' }}" class="btn btn-neon btn-sm w-100">
                                            VIEW
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-5 text-center">
                                <i class="bi bi-bell-slash d-block mb-3" style="font-size: 3rem; color: rgba(0, 240, 255, 0.1);"></i>
                                <h4 style="color: #555577;">No notifications to show</h4>
                                <p class="text-muted">You're all caught up!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                @if($notifications->hasPages())
                    <div class="card-footer bg-transparent border-0 p-4 d-flex justify-content-center">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .bg-dark-glass {
        background: rgba(10, 10, 30, 0.85);
        backdrop-filter: blur(20px);
    }
    .border-neon {
        border: 1px solid rgba(0, 240, 255, 0.3);
    }
    .border-neon-faint {
        border-color: rgba(0, 240, 255, 0.05) !important;
    }
    .notification-row {
        transition: all 0.3s ease;
    }
    .notification-row:hover {
        background: rgba(0, 240, 255, 0.03) !important;
    }
    .unread-row {
        background: rgba(0, 240, 255, 0.02) !important;
        border-left: 3px solid #00f0ff !important;
    }
    .btn-outline-neon {
        color: #00f0ff;
        border-color: #00f0ff;
        background: transparent;
        font-family: 'Orbitron', sans-serif;
        font-size: 0.7rem;
        letter-spacing: 1px;
    }
    .btn-outline-neon:hover {
        background: rgba(0, 240, 255, 0.1);
        color: #00f0ff;
        box-shadow: 0 0 10px rgba(0, 240, 255, 0.3);
    }
    .pagination .page-link {
        background: rgba(10, 10, 30, 0.8);
        border-color: rgba(0, 240, 255, 0.3);
        color: #00f0ff;
    }
    .pagination .page-item.active .page-link {
        background: #00f0ff;
        border-color: #00f0ff;
        color: #000;
    }
</style>
@endsection
