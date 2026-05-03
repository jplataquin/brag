<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$old = <<<'HTML'
                                            <form action="{{ route('battles.action.invite', $battle) }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" id="invite_nominee_id">
HTML;

$new = <<<'HTML'
                                            <form action="{{ route('battles.action.invite', $battle) }}" method="POST" id="invitePlayerForm">
                        @csrf
                        <input type="hidden" name="user_id" id="invite_nominee_id">
HTML;

$content = str_replace($old, $new, $content);

$oldError = <<<'HTML'
                    <p class="text-muted small">Invited players will receive a notification to join this battle room.</p>
                </div>
HTML;

$newError = <<<'HTML'
                    <p class="text-muted small">Invited players will receive a notification to join this battle room.</p>
                    <div class="form-error-display d-none text-danger small mt-2"></div>
                </div>
HTML;

$content = str_replace($oldError, $newError, $content);

file_put_contents('resources/views/battles/room.blade.php', $content);
