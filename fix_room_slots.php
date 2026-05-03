<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$oldA = "@include('battles.partials.single-slot', ['team' => 'A', 'slot' => \$i, 'u' => \$u, 'c' => \$c])";
$newA = "@include('battles.partials.single-slot', ['team' => 'A', 'slot' => \$i, 'u' => \$u, 'c' => \$c, 'isFinal' => \$battle->status == 'completed', 'isMe' => \$u && \$u->id == Auth::id(), 'snapshot' => (\$battle->status == 'completed' && is_array(\$battle->team_a_card_data) && isset(\$battle->team_a_card_data[\$i])) ? \$battle->team_a_card_data[\$i] : null])";

$oldB = "@include('battles.partials.single-slot', ['team' => 'B', 'slot' => \$i, 'u' => \$u, 'c' => \$c])";
$newB = "@include('battles.partials.single-slot', ['team' => 'B', 'slot' => \$i, 'u' => \$u, 'c' => \$c, 'isFinal' => \$battle->status == 'completed', 'isMe' => \$u && \$u->id == Auth::id(), 'snapshot' => (\$battle->status == 'completed' && is_array(\$battle->team_b_card_data) && isset(\$battle->team_b_card_data[\$i])) ? \$battle->team_b_card_data[\$i] : null])";

$content = str_replace($oldA, $newA, $content);
$content = str_replace($oldB, $newB, $content);

file_put_contents('resources/views/battles/room.blade.php', $content);
