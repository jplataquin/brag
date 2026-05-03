<?php
$content = file_get_contents('resources/views/components/digital-card.blade.php');

$oldBtn = <<<'HTML'
                <button type="button" class="btn btn-neon-magenta" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i> CLOSE
                </button>
HTML;

$newBtn = <<<'HTML'
                <button type="button" class="btn btn-neon-magenta" onclick="bootstrap.Modal.getInstance(document.getElementById('modal_{{ $id }}')).hide();">
                    <i class="bi bi-x-lg"></i> CLOSE
                </button>
HTML;

$content = str_replace($oldBtn, $newBtn, $content);
file_put_contents('resources/views/components/digital-card.blade.php', $content);
