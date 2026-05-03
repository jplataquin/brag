<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$oldJS = <<<'JS'
                if (data.reload === true || data.consensus === true || data.conflict === true) {
                    window.location.reload();
                } else if (data.message && data.message.includes('ready')) {
JS;

$newJS = <<<'JS'
                if (data.reload === true || data.consensus === true || data.conflict === true) {
                    window.isReloading = true;
                    window.location.reload();
                } else if (data.message && data.message.includes('ready')) {
JS;

$content = str_replace($oldJS, $newJS, $content);
file_put_contents('resources/views/battles/room.blade.php', $content);
