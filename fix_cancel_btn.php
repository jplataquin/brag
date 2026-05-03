<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$old = <<<'HTML'
                                    @if(!$hasRequestedCancel)
                                        <form action="{{ route('battles.action.cancel', $battle) }}" method="POST" class="d-inline">@csrf <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-x-circle"></i> REQUEST CANCEL
                                        </button></form>
                                    @endif
HTML;

$new = <<<'HTML'
                                    @if(!$hasRequestedCancel)
                                        <form action="{{ route('battles.action.cancel', $battle) }}" method="POST" class="d-inline" id="requestCancelForm" onsubmit="event.preventDefault(); handleActionSubmit('requestCancelForm');">@csrf <button type="submit" class="btn btn-outline-danger btn-sm" onclick="window.neonConfirm('Are you sure you want to request to CANCEL this active match?').then(c => { if(c) handleActionSubmit('requestCancelForm'); }); return false;">
                                            <i class="bi bi-x-circle"></i> REQUEST CANCEL
                                        </button></form>
                                    @endif
HTML;

$content = str_replace($old, $new, $content);
file_put_contents('resources/views/battles/room.blade.php', $content);
