<?php
$html = file_get_contents('resources/views/battles/room.blade.php');

// We need to inject forms instead of wire:clicks for Join
// We will simply use Javascript to set a hidden input and submit the form.

// Let's add Echo listener to the bottom
$echoListener = <<<HTML
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if(window.Echo) {
            window.Echo.channel('battle.{{ \$battle->id }}')
                .listen('BattleUpdated', (e) => {
                    window.location.reload();
                });
        }
    });
</script>
HTML;
$html = str_replace("</script>\n</div>\n@endsection", "</script>\n" . $echoListener . "\n</div>\n@endsection", $html);

file_put_contents('resources/views/battles/room.blade.php', $html);
