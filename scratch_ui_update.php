<?php
$roomPath = 'c:/Users/jplataquin/OneDrive/Desktop/dev/AI/brag/resources/views/battles/room.blade.php';
$livewirePath = 'c:/Users/jplataquin/OneDrive/Desktop/dev/AI/brag/resources/views/livewire/battle-room.blade.php';

$roomContent = file_get_contents($roomPath);

// 1. Process Livewire Component File
$livewireContent = file_get_contents($livewirePath);
// Remove @push('modals') and @endpush
$livewireContent = str_replace("@push('modals')", "", $livewireContent);
$livewireContent = str_replace("@endpush", "", $livewireContent);
// Add wire:ignore.self to modals
$livewireContent = str_replace('class="modal fade"', 'class="modal fade" wire:ignore.self', $livewireContent);
file_put_contents($livewirePath, $livewireContent);

// 2. Process Room.blade.php
// Get rows 1-11
$lines = explode("\n", $roomContent);
$newRoom = implode("\n", array_slice($lines, 0, 11)) . "\n";
$newRoom .= "    <livewire:battle-room :battle=\"\$battle\" />\n";

// Add everything from @section('scripts') downwards
$scriptsIndex = 0;
foreach ($lines as $i => $line) {
    if (strpos($line, "@section('scripts')") !== false) {
        $scriptsIndex = $i;
        break;
    }
}
$scriptsContent = implode("\n", array_slice($lines, $scriptsIndex));

// Modify Javascript
// Remove the setTimeout reload blocks
$scriptsContent = preg_replace('/else\s*\{\s*\/\/\s*Challenger.*\s*setTimeout\(\(\)\s*=>\s*\{\s*window\.location\.reload\(\);\s*\},\s*1000\);\s*return;\s*\}/', '', $scriptsContent);
$scriptsContent = preg_replace('/if\s*\(\[\'join\'.*?return;\s*\}/s', '', $scriptsContent);

// Remove manual DOM manipulation for Status Badge
$scriptsContent = preg_replace('/\/\/ Update Status Badge[\s\S]*?\}\n/s', '', $scriptsContent);
// Remove manual declaration text update
$scriptsContent = preg_replace('/if\s*\(e\.type === \'declare\' \|\| e\.type === \'conflict\'\) \{[\s\S]*?\}\s*\}/s', '', $scriptsContent);

// Add listener for Livewire update
$scriptsContent = str_replace("scrollLogToBottom();\r", "scrollLogToBottom();\n        window.addEventListener('battle-updated', event => {\n            scrollLogToBottom();\n        });", $scriptsContent);

$newRoom .= $scriptsContent;
file_put_contents($roomPath, $newRoom);
echo "DONE";
