<?php
$content = file_get_contents('resources/views/battles/room.blade.php');
// Ensure submitBtn is not null before accessing innerHTML
$oldJS = <<<'JS'
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnHTML = submitBtn.innerHTML;
JS;
$newJS = <<<'JS'
            const submitBtn = form.querySelector('button[type="submit"]');
            if (!submitBtn) return;
            const originalBtnHTML = submitBtn.innerHTML;
JS;
$content = str_replace($oldJS, $newJS, $content);
file_put_contents('resources/views/battles/room.blade.php', $content);
