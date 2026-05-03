<?php
$content = file_get_contents('resources/views/battles/room.blade.php');
$old = <<<'JS'
                        if (e.message.includes('started') || e.message.includes('finalized') || e.message.includes('cancelled') || e.message.includes('ready') || e.message.includes('requested cancellation') || e.message.includes('rejected the cancellation request')) {
                            setTimeout(() => window.location.reload(), 1000);
                        }
JS;

$new = <<<'JS'
                        if (e.message.includes('started') || e.message.includes('finalized') || e.message.includes('cancelled') || e.message.includes('ready') || e.message.includes('requested cancellation') || e.message.includes('rejected')) {
                            setTimeout(() => window.location.reload(), 1000);
                        }
JS;

$content = str_replace($old, $new, $content);
file_put_contents('resources/views/battles/room.blade.php', $content);
