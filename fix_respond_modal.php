<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$old = <<<'HTML'
                    <div class="d-flex gap-3">
                        <form action="{{ route('battles.action.respond_cancel', $battle) }}" method="POST" class="w-100">
                            @csrf <input type="hidden" name="agreed" value="1">
                            <button type="submit" class="btn btn-neon-magenta w-100"><i class="bi bi-check-lg"></i> AGREE & CANCEL</button>
                        </form>
                        <form action="{{ route('battles.action.respond_cancel', $battle) }}" method="POST" class="w-100">
                            @csrf <input type="hidden" name="agreed" value="0">
                            <button type="submit" class="btn btn-outline-secondary w-100" style="border-color: #555;"><i class="bi bi-x-lg"></i> REJECT</button>
                        </form>
                    </div>
HTML;

$new = <<<'HTML'
                    <div class="d-flex gap-3">
                        <form action="{{ route('battles.action.respond_cancel', $battle) }}" method="POST" class="w-100" id="agreeCancelForm" onsubmit="event.preventDefault(); handleActionSubmit('agreeCancelForm');">
                            @csrf <input type="hidden" name="agreed" value="1">
                            <button type="submit" class="btn btn-neon-magenta w-100"><i class="bi bi-check-lg"></i> AGREE & CANCEL</button>
                        </form>
                        <form action="{{ route('battles.action.respond_cancel', $battle) }}" method="POST" class="w-100" id="rejectCancelForm" onsubmit="event.preventDefault(); handleActionSubmit('rejectCancelForm');">
                            @csrf <input type="hidden" name="agreed" value="0">
                            <button type="submit" class="btn btn-outline-secondary w-100" style="border-color: #555;"><i class="bi bi-x-lg"></i> REJECT</button>
                        </form>
                    </div>
HTML;

$content = str_replace($old, $new, $content);
file_put_contents('resources/views/battles/room.blade.php', $content);
