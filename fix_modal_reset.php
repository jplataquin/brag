<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$resetJs = <<<'JS'
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const joinModalEl = document.getElementById('joinModal');
        if (joinModalEl) {
            joinModalEl.addEventListener('hidden.bs.modal', event => {
                document.querySelectorAll('.selectable-card').forEach(e => e.classList.remove('selected'));
                document.getElementById('selectedCardId').value = '';
                const btn = document.getElementById('confirmJoinBtn');
                if (btn) btn.disabled = true;
            });
        }
    });
</script>
JS;

$content = preg_replace('/<\/script>\s*@endsection/s', "</script>\n" . $resetJs . "\n@endsection", $content);
file_put_contents('resources/views/battles/room.blade.php', $content);
