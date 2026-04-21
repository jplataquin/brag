<div>
    <h5 class="section-header">
        <i class="bi bi-journal-text section-icon" style="color: #39ff14;"></i> ACTIVITY LOGS
    </h5>
    <div class="activity-log-container p-3" style="background: rgba(10, 10, 30, 0.6); border: 1px solid rgba(57, 255, 20, 0.1); border-radius: 12px; height: 500px; overflow-y: auto;">
        <div class="activity-list" id="activity-logs-list">
            @if($battle->activities->count() > 0)
                @foreach($battle->activities as $activity)
                    <div class="activity-item mb-3 pb-3" style="border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                        <div class="d-flex gap-2 align-items-start">
                            <div class="activity-icon-sm mt-1">
                                @switch($activity->type)
                                    @case('create') <i class="bi bi-plus-circle-fill text-info"></i> @break
                                    @case('join') <i class="bi bi-person-check-fill text-success"></i> @break
                                    @case('invite') <i class="bi bi-envelope-fill text-warning"></i> @break
                                    @case('elect_marshall') <i class="bi bi-shield-fill text-warning"></i> @break
                                    @case('marshall_election') <i class="bi bi-shield-check text-warning"></i> @break
                                    @case('marshall_accepted') <i class="bi bi-shield-lock-fill text-warning"></i> @break
                                    @case('marshall_rejected') <i class="bi bi-shield-x text-danger"></i> @break
                                    @case('marshall_leave') <i class="bi bi-box-arrow-right text-danger"></i> @break
                                    @case('declare') <i class="bi bi-megaphone-fill text-info"></i> @break
                                    @case('conflict') <i class="bi bi-exclamation-triangle-fill text-danger"></i> @break
                                    @case('marshall_decision') <i class="bi bi-shield-lock-fill text-warning"></i> @break
                                    @case('consensus') <i class="bi bi-people-fill text-success"></i> @break
                                    @case('winner') <i class="bi bi-trophy-fill text-success"></i> @break
                                    @case('cancel') <i class="bi bi-x-circle-fill text-danger"></i> @break
                                    @case('cancel_request') <i class="bi bi-exclamation-circle-fill text-warning"></i> @break
                                    @case('cancel_agree') <i class="bi bi-check-circle-fill text-success"></i> @break
                                    @case('cancel_reject') <i class="bi bi-x-circle text-danger"></i> @break
                                    @case('start') <i class="bi bi-play-circle-fill text-success"></i> @break
                                    @case('poke') <i class="bi bi-hand-index-thumb-fill text-info"></i> @break
                                    @default <i class="bi bi-dot text-muted"></i>
                                @endswitch
                            </div>
                            <div>
                                @php
                                    $formattedMessage = e($activity->message);
                                    $formattedMessage = preg_replace('/@([\w\-.]+)/', '<a href="/user/$1" target="_blank" style="color: #ffdd00; text-decoration: none; font-weight: bold;">@$1</a>', $formattedMessage);
                                @endphp
                                <div style="font-size: 0.85rem; color: #fff;">{!! $formattedMessage !!}</div>
                                <div style="font-size: 0.7rem; color: #555577;">{{ $activity->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div id="empty-activity" class="text-center py-5 text-muted" style="font-size: 0.9rem;">
                    <i class="bi bi-chat-dots" style="font-size: 2rem; opacity: 0.3;"></i>
                    <p class="mt-2">Waiting for activity...</p>
                </div>
            @endif
        </div>
    </div>
</div>
