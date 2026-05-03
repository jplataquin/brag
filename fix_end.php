<?php
$content = file_get_contents('resources/views/battles/room.blade.php');
$content = preg_replace('/<\/script>\s*<\/div>\s*@endsection/s', "</script>\n@endsection", $content);
file_put_contents('resources/views/battles/room.blade.php', $content);
