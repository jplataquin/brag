import re

with open('resources/views/battles/room.blade.php', 'r') as f:
    html = f.read()

# Remove Livewire wrapper
html = re.sub(r'<div>\n<style>', '<style>', html, count=1)
html = re.sub(r'</script>\n</div>\n\n</div>', '</script>\n</div>\n@endsection', html)
html = "@extends('layouts.app')\n@section('title', 'Battle Room #' . $battle->id)\n@section('content')\n" + html

# Remove wire:key, wire:poll, wire:ignore
html = re.sub(r'\s*wire:key="[^"]+"', '', html)
html = re.sub(r'\s*wire:ignore\.self', '', html)
html = re.sub(r'\s*wire:ignore', '', html)
html = re.sub(r'\s*wire:poll\.10s', '', html)
html = re.sub(r'\s*wire:model(\.live\.debounce\.[0-9]+ms)?="[^"]+"', '', html)
html = re.sub(r'\s*wire:click\.prevent="[^"]+"', '', html)
html = re.sub(r'\s*wire:click="[^"]+"', '', html)

# Replace the Status block with <livewire:battle-status :battle="$battle" />
html = re.sub(r'<div class="neon-card p-4 mb-4">.*?</div>\n\s*</div>\n\s*@elseif\(Auth::id\(\) == \$battle->marshall_id && \$battle->status == \'active\'\).*?</div>\n\s*@endif\n\s*</div>', '<livewire:battle-status :battle="$battle" />\n</div>', html, flags=re.DOTALL)

# Replace the Activity Log block with <livewire:battle-activity-log :battle="$battle" />
html = re.sub(r'<div class="activity-log-container d-flex flex-column" style="max-height: 300px; overflow-y: auto;">.*?</div>', '<livewire:battle-activity-log :battle="$battle" />', html, flags=re.DOTALL)

with open('resources/views/battles/room.blade.php', 'w') as f:
    f.write(html)
