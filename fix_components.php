<?php
$html = file_get_contents('resources/views/livewire/battle-room.blade.php');

// Replace the Status block with <livewire:battle-status :battle="$battle" />
$html = preg_replace('/<div class="neon-card p-4 mb-4">.*?<\/div>\n\s*<\/div>\n\s*@elseif\(Auth::id\(\) == \$battle->marshall_id && \$battle->status == \'active\'\).*?<\/div>\n\s*@endif\n\s*<\/div>/s', '<livewire:battle-status :battle="$battle" />\n</div>', $html, 1);

// Replace the Activity Log block with <livewire:battle-activity-log :battle="$battle" />
$html = preg_replace('/<div class="activity-log-container d-flex flex-column" style="max-height: 300px; overflow-y: auto;">.*?<\/div>/s', '<livewire:battle-activity-log :battle="$battle" />', $html, 1);

file_put_contents('resources/views/livewire/battle-room.blade.php', $html);
