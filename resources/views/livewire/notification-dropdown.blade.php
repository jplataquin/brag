<div>
    @if($isMobile)
    <div class="nav-item notification-dropdown">
        <a href="#" class="nav-link d-flex align-items-center position-relative" wire:click.prevent="markAllAsReadAndRedirect">
            <i class="bi bi-bell-fill fs-5"></i>
            @if($unreadCount > 0)
                <span id="notification-bubble-mobile" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; border: 1px solid #000; transition: opacity 0.2s;">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
            @endif
        </a>
    </div>
    @else
    <div class="nav-item dropdown notification-dropdown">
        <a id="navbarNotificationDropdown" class="nav-link d-flex align-items-center position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" wire:click="markAllAsRead" onclick="hideNotificationBubble()">
            <i class="bi bi-bell-fill fs-5"></i>
            @if($unreadCount > 0)
                <span id="notification-bubble-desktop" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; border: 1px solid #000; transition: opacity 0.2s;">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
            @endif
        </a>
        <div class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="navbarNotificationDropdown" style="width: 320px; background: rgba(10, 10, 30, 0.95); border: 1px solid rgba(0, 240, 255, 0.3); backdrop-filter: blur(20px);">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom" style="border-color: rgba(0, 240, 255, 0.1) !important;">
                <h6 class="m-0 neon-text" style="font-size: 0.85rem; letter-spacing: 1px;">NOTIFICATIONS</h6>
                @if($unreadCount > 0)
                    <button wire:click="markAllAsRead" class="btn btn-link btn-sm p-0 text-decoration-none" style="color: #555577; font-size: 0.75rem;">Mark all as read</button>
                @endif
            </div>
            
            <div class="notification-list" style="max-height: 400px; overflow-y: auto;">
                @forelse($notifications as $notification)
                    <div class="notification-item p-3 border-bottom {{ $notification->read_at ? '' : 'unread' }}" 
                         style="border-color: rgba(0, 240, 255, 0.05) !important; transition: background 0.2s;"
                         wire:click="markAsRead('{{ $notification->id }}')">
                        <a href="{{ $notification->data['action_url'] ?? '#' }}" class="text-decoration-none">
                            <div class="d-flex gap-3">
                                <div class="flex-shrink-0">
                                    @if(($notification->data['type'] ?? '') === 'invite')
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; background: rgba(0, 240, 255, 0.1); border: 1px solid rgba(0, 240, 255, 0.3);">
                                            <i class="bi bi-envelope-fill" style="color: #00f0ff;"></i>
                                        </div>
                                    @elseif(($notification->data['type'] ?? '') === 'marshall_election')
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; background: rgba(255, 221, 0, 0.1); border: 1px solid rgba(255, 221, 0, 0.3);">
                                            <i class="bi bi-shield-fill-check" style="color: #ffdd00;"></i>
                                        </div>
                                    @elseif(($notification->data['type'] ?? '') === 'poke')
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; background: rgba(0, 240, 255, 0.1); border: 1px solid rgba(0, 240, 255, 0.3);">
                                            <i class="bi bi-hand-index-thumb-fill" style="color: #00f0ff;"></i>
                                        </div>
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; background: rgba(255, 0, 255, 0.1); border: 1px solid rgba(255, 0, 255, 0.3);">
                                            <i class="bi bi-lightning-fill" style="color: #ff00ff;"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <p class="m-0 mb-1" style="font-size: 0.85rem; color: #fff; line-height: 1.4;">
                                        {{ $notification->data['message'] }}
                                    </p>
                                    <small style="color: #555577; font-size: 0.7rem;">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="p-4 text-center">
                        <i class="bi bi-bell-slash d-block mb-2" style="font-size: 2rem; color: rgba(0, 240, 255, 0.1);"></i>
                        <p class="m-0" style="color: #555577; font-size: 0.85rem;">No notifications yet</p>
                    </div>
                @endforelse
            </div>
            
            @if(count($notifications) > 0)
                <div class="p-2 text-center border-top" style="border-color: rgba(0, 240, 255, 0.1) !important;">
                    <a href="{{ route('notifications.index') }}" class="btn btn-link btn-sm text-decoration-none neon-text" style="font-size: 0.75rem;">VIEW ALL</a>
                </div>
            @endif
        </div>
    </div>

    <style>
        .notification-item:hover {
            background: rgba(0, 240, 255, 0.05);
        }
        .notification-item.unread {
            background: rgba(0, 240, 255, 0.02);
            border-left: 2px solid #00f0ff !important;
        }
        .notification-list::-webkit-scrollbar {
            width: 4px;
        }
        .notification-list::-webkit-scrollbar-track {
            background: transparent;
        }
        .notification-list::-webkit-scrollbar-thumb {
            background: rgba(0, 240, 255, 0.2);
            border-radius: 10px;
        }
    </style>
    <script>
        function hideNotificationBubble() {
            const mobileBubble = document.getElementById('notification-bubble-mobile');
            const desktopBubble = document.getElementById('notification-bubble-desktop');
            if (mobileBubble) mobileBubble.style.opacity = '0';
            if (desktopBubble) desktopBubble.style.opacity = '0';
        }

        // Livewire hook to restore opacity when component updates (i.e. a new notification comes in and Livewire re-renders)
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('morph.updated', (el, component) => {
                const mobileBubble = document.getElementById('notification-bubble-mobile');
                const desktopBubble = document.getElementById('notification-bubble-desktop');
                if (mobileBubble) mobileBubble.style.opacity = '1';
                if (desktopBubble) desktopBubble.style.opacity = '1';
            });
        });
    </script>
    @endif
</div>
