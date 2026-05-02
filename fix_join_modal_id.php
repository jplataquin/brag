<?php
$content = file_get_contents('resources/views/livewire/battle-room.blade.php');
$content = str_replace(
    '<div class="custom-modal-backdrop" style="z-index: 2000;" wire:key="join-modal">',
    '<div class="modal fade" wire:ignore.self id="joinModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" style="background: rgba(0, 0, 0, 0.8);">
        <div class="modal-dialog modal-dialog-centered modal-lg">',
    $content
);
$content = preg_replace('/@if\(\$joiningTeam\)/', '', $content, 1);

// Find end of modal and add closing div for modal-dialog and remove @endif
$content = str_replace(
    '        </div>
    @endif

    <!-- Rename Team Modal -->',
    '        </div>
        </div>
    </div>

    <!-- Rename Team Modal -->',
    $content
);
file_put_contents('resources/views/livewire/battle-room.blade.php', $content);
