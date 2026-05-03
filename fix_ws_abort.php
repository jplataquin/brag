<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$old = <<<'JS'
                            fetch('/battles/{{ $battle->id }}/partial-slot/' + team + '/' + slotNum)
                                .then(res => res.text())
                                .then(html => {
JS;

$new = <<<'JS'
                            // Ignore slot updates if we are actively reloading the page
                            if (window.isReloading) return;

                            fetch('/battles/{{ $battle->id }}/partial-slot/' + team + '/' + slotNum)
                                .then(res => res.text())
                                .then(html => {
JS;

$content = str_replace($old, $new, $content);

$old2 = <<<'JS'
                        if (e.message.includes('started') || e.message.includes('finalized') || e.message.includes('cancelled') || e.message.includes('ready') || e.message.includes('requested cancellation') || e.message.includes('rejected')) {
                            setTimeout(() => window.location.reload(), 1000);
                        }
JS;

$new2 = <<<'JS'
                        if (e.message.includes('started') || e.message.includes('finalized') || e.message.includes('cancelled') || e.message.includes('ready') || e.message.includes('requested cancellation') || e.message.includes('rejected')) {
                            window.isReloading = true;
                            setTimeout(() => window.location.reload(), 1000);
                        }
JS;

$content = str_replace($old2, $new2, $content);
file_put_contents('resources/views/battles/room.blade.php', $content);
