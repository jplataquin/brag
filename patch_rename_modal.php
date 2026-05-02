<?php
$content = file_get_contents('resources/views/livewire/battle-room.blade.php');

$pattern = '/<!-- Rename Team A Modal -->.*?@endif/s';
$content = preg_replace($pattern, '', $content, 1);

$pattern = '/<!-- Rename Team B Modal -->.*?@endif/s';
$content = preg_replace($pattern, '', $content, 1);

$bootstrapModal = <<<HTML
    <!-- Rename Team Modal -->
    <div class="modal fade" wire:ignore.self id="renameTeamModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="false" style="background: rgba(0, 0, 0, 0.8);">
        <div class="modal-dialog modal-dialog-centered">
            @if(Auth::id() == \$battle->team_a_user_1)
            <div class="modal-content p-4 neon-card" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #00f0ff; backdrop-filter: blur(20px);">
                <h5 class="orbitron text-cyan mb-4 text-center">RENAME TEAM A</h5>
                <div class="mb-4">
                    <input type="text" wire:model="newTeamNameA" class="form-control bg-dark text-white border-cyan text-center orbitron" placeholder="Enter new team name">
                </div>
                <div class="d-flex gap-3">
                    <button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">CANCEL</button>
                    <button type="button" class="btn btn-neon w-50 orbitron" wire:click.prevent="updateTeamName('A')" data-bs-dismiss="modal">SAVE</button>
                </div>
            </div>
            @elseif(Auth::id() == \$battle->team_b_user_1)
            <div class="modal-content p-4 neon-card" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #ff00ff; box-shadow: 0 0 30px rgba(255, 0, 255, 0.2); backdrop-filter: blur(20px);">
                <h5 class="orbitron text-magenta mb-4 text-center">RENAME TEAM B</h5>
                <div class="mb-4">
                    <input type="text" wire:model="newTeamNameB" class="form-control bg-dark text-white border-magenta text-center orbitron" placeholder="Enter new team name">
                </div>
                <div class="d-flex gap-3">
                    <button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">CANCEL</button>
                    <button type="button" class="btn btn-neon-magenta w-50 orbitron" wire:click.prevent="updateTeamName('B')" data-bs-dismiss="modal">SAVE</button>
                </div>
            </div>
            @endif
        </div>
    </div>
HTML;

$content = str_replace('<!-- Elect Marshall Modal -->', $bootstrapModal . "\n\n    <!-- Elect Marshall Modal -->", $content);
file_put_contents('resources/views/livewire/battle-room.blade.php', $content);
