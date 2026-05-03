<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$old = '<div class="col-lg-3 mt-4 mt-lg-0"><livewire:battle-activity-log :battle="$battle" /></div>' . "\n    </div> <!-- Close row g-4 -->";
$new = '<div class="col-lg-3 mt-4 mt-lg-0"><livewire:battle-activity-log :battle="$battle" /></div>' . "\n    </div> <!-- Close row g-4 -->\n</div> <!-- Close team-battle-room -->\n</div> <!-- Close root div from livewire -->";

$content = str_replace($old, $new, $content);
file_put_contents('resources/views/battles/room.blade.php', $content);
