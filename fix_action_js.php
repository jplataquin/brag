<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$oldJS = <<<'JS'
                if (data.consensus === true || data.conflict === true) {
                    // Consensus reached or conflict triggered, reload page to show final/failed state
                    window.location.reload();
                } else if (data.message && data.message.includes('ready')) {
                    // Ready button. Replace with a badge.
                    form.outerHTML = '<span class="badge bg-success w-100 p-2"><i class="bi bi-check-circle"></i> YOU ARE READY</span>';
                } else {
                    // Declare Win/Lost vote registered, but no consensus yet.
                    // Replace the action buttons with the "Waiting for opponent" badge
                    const container = form.closest('.d-flex.gap-3.justify-content-center');
                    if (container) {
                        container.innerHTML = '<div class="alert alert-info py-2 small mb-0 text-center" style="border: 1px solid #00f0ff; background: rgba(0, 240, 255, 0.1); color: #00f0ff; width: 100%; max-width: 400px;"><i class="bi bi-hourglass-split"></i> You declared a vote. Waiting for opponent...</div>';
                    }
                }
JS;

$newJS = <<<'JS'
                if (data.consensus === true || data.conflict === true) {
                    window.location.reload();
                } else if (data.message && data.message.includes('ready')) {
                    form.outerHTML = '<span class="badge bg-success w-100 p-2"><i class="bi bi-check-circle"></i> YOU ARE READY</span>';
                } else if (data.message && data.message.includes('stood up')) {
                    window.location.reload();
                } else {
                    const container = form.closest('.d-flex.gap-3.justify-content-center') || form.closest('#actions-container');
                    if (container) {
                        container.innerHTML = '<div class="alert alert-info py-2 small mb-0 text-center" style="border: 1px solid #00f0ff; background: rgba(0, 240, 255, 0.1); color: #00f0ff; width: 100%; max-width: 400px;"><i class="bi bi-hourglass-split"></i> You declared a vote. Waiting for opponent...</div>';
                    }
                }
JS;

$content = str_replace($oldJS, $newJS, $content);
file_put_contents('resources/views/battles/room.blade.php', $content);
