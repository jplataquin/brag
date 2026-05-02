<?php
$content = file_get_contents('resources/views/livewire/battle-room.blade.php');
$content = str_replace(
    '<button type="button" class="btn btn-outline-secondary w-50 py-2" data-bs-dismiss="modal">CANCEL</button>',
    '<button type="button" class="btn btn-outline-secondary w-50 py-2" onclick="var m = bootstrap.Modal.getInstance(document.getElementById(\'joinModal\')); if(m) m.hide();">CANCEL</button>',
    $content
);
$content = str_replace(
    '<button type="button" class="btn btn-neon w-50 py-2 orbitron" wire:click.prevent="confirmJoin" data-bs-dismiss="modal" wire:loading.attr="disabled">',
    '<button type="button" class="btn btn-neon w-50 py-2 orbitron" wire:click.prevent="confirmJoin" onclick="var m = bootstrap.Modal.getInstance(document.getElementById(\'joinModal\')); if(m) m.hide();" wire:loading.attr="disabled">',
    $content
);
// fix rename dismiss
$content = str_replace(
    '<button type="button" class="btn btn-outline-secondary w-50" data-bs-dismiss="modal">CANCEL</button>',
    '<button type="button" class="btn btn-outline-secondary w-50" onclick="var m = bootstrap.Modal.getInstance(document.getElementById(\'renameTeamModal\')); if(m) m.hide();">CANCEL</button>',
    $content
);
$content = str_replace(
    '<button type="button" class="btn btn-neon w-50 orbitron" wire:click.prevent="updateTeamName(\'A\')" data-bs-dismiss="modal">SAVE</button>',
    '<button type="button" class="btn btn-neon w-50 orbitron" wire:click.prevent="updateTeamName(\'A\')" onclick="var m = bootstrap.Modal.getInstance(document.getElementById(\'renameTeamModal\')); if(m) m.hide();">SAVE</button>',
    $content
);
$content = str_replace(
    '<button type="button" class="btn btn-neon-magenta w-50 orbitron" wire:click.prevent="updateTeamName(\'B\')" data-bs-dismiss="modal">SAVE</button>',
    '<button type="button" class="btn btn-neon-magenta w-50 orbitron" wire:click.prevent="updateTeamName(\'B\')" onclick="var m = bootstrap.Modal.getInstance(document.getElementById(\'renameTeamModal\')); if(m) m.hide();">SAVE</button>',
    $content
);
file_put_contents('resources/views/livewire/battle-room.blade.php', $content);
