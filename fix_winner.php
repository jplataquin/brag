<?php
$content = file_get_contents('resources/views/battles/index.blade.php');

$old = <<<'HTML'
                @if($b->status === 'completed' && $b->winner_team)
                    <span style="color: #39ff14; font-size: 0.8rem; font-weight: 600;">
                        🏆 {{ $b->winner_team === 'team_a' ? $b->team_name_a : $b->team_name_b }} WON
                    </span>
                @endif
HTML;

$new = <<<'HTML'
                @if($b->status === 'completed' && $b->winner_team)
                    <span style="color: #39ff14; font-size: 0.8rem; font-weight: 600;">
                        🏆 {{ $b->winner_team === 'A' ? $b->team_name_a : $b->team_name_b }} WON
                    </span>
                @endif
HTML;

$content = str_replace($old, $new, $content);
file_put_contents('resources/views/battles/index.blade.php', $content);
