<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$script = <<<'JS'
<script>
    // Fix for Bootstrap 5 multiple modals: keep body class 'modal-open' if joinModal is still open
    document.addEventListener('hidden.bs.modal', function (event) {
        if (event.target.id !== 'joinModal') {
            const joinModalEl = document.getElementById('joinModal');
            if (joinModalEl && joinModalEl.classList.contains('show')) {
                document.body.classList.add('modal-open');
            }
        }
    });
</script>
JS;

$content = preg_replace('/<\/script>\s*@endsection/s', "</script>\n" . $script . "\n@endsection", $content);
file_put_contents('resources/views/battles/room.blade.php', $content);
