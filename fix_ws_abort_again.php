<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$old = <<<'JS'
                            slotEl.style.opacity = '0.5';
                            
                            // Ignore slot updates if we are actively reloading the page
                            if (window.isReloading) return;

                            fetch('/battles/{{ $battle->id }}/partial-slot/' + team + '/' + slotNum)
JS;

$new = <<<'JS'
                            // Ignore slot updates if we are actively reloading the page
                            if (window.isReloading) return;

                            slotEl.style.opacity = '0.5';
                            
                            fetch('/battles/{{ $battle->id }}/partial-slot/' + team + '/' + slotNum)
JS;

// Wait, I messed up the replacement earlier. Let's just do a clean replace of the fetch logic.
$content = preg_replace('/slotEl\.style\.opacity = \'0\.5\';\s*fetch/s', "if (window.isReloading) return;\n                            slotEl.style.opacity = '0.5';\n                            \n                            fetch", $content);

file_put_contents('resources/views/battles/room.blade.php', $content);
