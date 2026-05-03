<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$old = <<<'HTML'
                                            <form action="{{ route('battles.action.elect_marshall', $battle) }}" method="POST">
                        @csrf
                        <input type="hidden" name="marshall_id" id="marshall_nominee_id">
HTML;

$new = <<<'HTML'
                                            <form action="{{ route('battles.action.elect_marshall', $battle) }}" method="POST" id="electMarshallForm">
                        @csrf
                        <input type="hidden" name="marshall_id" id="marshall_nominee_id">
HTML;

$content = str_replace($old, $new, $content);

$oldError = <<<'HTML'
                    <p class="text-muted small">Both team leaders must elect the same user for them to be designated as the marshall.</p>
                </div>
HTML;

$newError = <<<'HTML'
                    <p class="text-muted small">Both team leaders must elect the same user for them to be designated as the marshall.</p>
                    <div class="form-error-display d-none text-danger small mt-2"></div>
                </div>
HTML;

$content = str_replace($oldError, $newError, $content);

file_put_contents('resources/views/battles/room.blade.php', $content);
