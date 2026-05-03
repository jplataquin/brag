<?php
$html = file_get_contents('resources/views/battles/room.blade.php');
$html = str_replace(
    'id="card_a_{{ $i }}_{{ $c->id }}"',
    'id="card_a_{{ $i }}_{{ $c->id ?? \'none\' }}"',
    $html
);
$html = str_replace(
    'id="card_b_{{ $i }}_{{ $c->id }}"',
    'id="card_b_{{ $i }}_{{ $c->id ?? \'none\' }}"',
    $html
);
file_put_contents('resources/views/battles/room.blade.php', $html);
