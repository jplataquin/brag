<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

// Add prompt to Ready Button
$oldReady = '<form action="{{ route(\'battles.action.ready\', $battle) }}" method="POST" class="d-inline w-100" id="readyForm" onsubmit="event.preventDefault(); handleActionSubmit(\'readyForm\');">@csrf <button type="submit" class="btn btn-neon-lime w-100" style="box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);"><i class="bi bi-check2-all"></i> READY</button></form>';
$newReady = '<form action="{{ route(\'battles.action.ready\', $battle) }}" method="POST" class="d-inline w-100" id="readyForm">@csrf <button type="button" class="btn btn-neon-lime w-100" style="box-shadow: 0 0 20px rgba(57, 255, 20, 0.4);" onclick="window.neonConfirm(\'Are you sure your team is ready? You will not be able to stand up once ready.\').then(c => { if(c) handleActionSubmit(\'readyForm\'); })"><i class="bi bi-check2-all"></i> READY</button></form>';
$content = str_replace($oldReady, $newReady, $content);

// Add prompt to Stand Up Button & Convert to AJAX
$oldStandUp = '<form action="{{ route(\'battles.action.standup\', $battle) }}" method="POST" class="d-inline w-100">@csrf <button type="submit" class="btn btn-outline-warning w-100"><i class="bi bi-box-arrow-right"></i> STAND UP</button></form>';
$newStandUp = '<form action="{{ route(\'battles.action.standup\', $battle) }}" method="POST" class="d-inline w-100" id="standUpForm">@csrf <button type="button" class="btn btn-outline-warning w-100" onclick="window.neonConfirm(\'Are you sure you want to stand up and leave your slot?\').then(c => { if(c) handleActionSubmit(\'standUpForm\'); })"><i class="bi bi-box-arrow-right"></i> STAND UP</button></form>';
$content = str_replace($oldStandUp, $newStandUp, $content);

file_put_contents('resources/views/battles/room.blade.php', $content);
