<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$old = <<<'HTML'
            <form action="{{ route('battles.action.rename', $battle) }}" method="POST" class="w-100">
            @csrf
            <input type="hidden" name="team" id="renameTeamVal" value="">
            <div class="modal-content p-4 neon-card" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #00f0ff; backdrop-filter: blur(20px);">
                <h5 class="orbitron text-cyan mb-4 text-center">RENAME TEAM <span id="rename_team_name"></span></h5>
                <div class="mb-4">
                    <input type="text" name="name" id="renameTeamInput" class="form-control bg-dark text-white border-cyan text-center orbitron" placeholder="Enter new team name" required>
                </div>
HTML;

$new = <<<'HTML'
            <form action="{{ route('battles.action.rename', $battle) }}" method="POST" class="w-100" id="renameTeamForm">
            @csrf
            <input type="hidden" name="team" id="renameTeamVal" value="">
            <div class="modal-content p-4 neon-card" style="background: rgba(10, 10, 30, 0.95); border: 1px solid #00f0ff; backdrop-filter: blur(20px);">
                <h5 class="orbitron text-cyan mb-4 text-center">RENAME TEAM <span id="rename_team_name"></span></h5>
                <div class="mb-4">
                    <input type="text" name="name" id="renameTeamInput" class="form-control bg-dark text-white border-cyan text-center orbitron" placeholder="Enter new team name" required>
                    <div class="form-error-display d-none text-danger small mt-2 text-center"></div>
                </div>
HTML;

$content = str_replace($old, $new, $content);
file_put_contents('resources/views/battles/room.blade.php', $content);
