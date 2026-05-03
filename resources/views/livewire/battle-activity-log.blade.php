<div class="activity-log-container d-flex flex-column" style="max-height: 300px; overflow-y: auto;">
    @foreach($activities as $activity)
        <div class="activity-item mb-2 border-bottom border-secondary border-opacity-10 pb-1">
            <span class="text-muted small">[{{ $activity->created_at->format('H:i') }}]</span>
            <span class="small">{{ $activity->message }}</span>
        </div>
    @endforeach
</div>
