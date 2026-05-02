<?php
$content = file_get_contents('resources/views/livewire/battle-room.blade.php');
// Livewire components must have exactly one root element. 
// Currently there is a <style> block outside the main <div>.
$content = str_replace(
    "<style>\n    .team-name-container {",
    "<div>\n<style>\n    .team-name-container {",
    $content
);
$content .= "\n</div>";
file_put_contents('resources/views/livewire/battle-room.blade.php', $content);
