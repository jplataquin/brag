<?php
$html = file_get_contents('resources/views/battles/room.blade.php');
$html = str_replace(
    '<?php if($errors->has("selectedCardId")): ?><div class="text-danger small mt-2 text-center">{{ $errors->first("selectedCardId") }}</div><?php endif; ?>',
    '<?php if(isset($errors) && $errors->has("selectedCardId")): ?><div class="text-danger small mt-2 text-center">{{ $errors->first("selectedCardId") }}</div><?php endif; ?>',
    $html
);
file_put_contents('resources/views/battles/room.blade.php', $html);
