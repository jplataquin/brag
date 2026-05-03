<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$oldJS = <<<JS
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if(window.Echo) {
            window.Echo.channel('battle.{{ \$battle->id }}')
                .listen('BattleUpdated', (e) => {
                    window.location.reload();
                });
        }
    });
</script>
JS;

$newJS = <<<JS
<script>
    // Real-time Slot Updates via Hybrid Fetch
    document.addEventListener('DOMContentLoaded', () => {
        if(window.Echo) {
            window.Echo.channel('battle.{{ \$battle->id }}')
                .listen('BattleUpdated', (e) => {
                    if (e.type === 'update') {
                        const slots = document.querySelectorAll('.slot-container');
                        slots.forEach(slotEl => {
                            const idParts = slotEl.id.split('-'); 
                            const team = idParts[2];
                            const slotNum = idParts[3];
                            
                            slotEl.style.opacity = '0.5';
                            
                            fetch('/battles/{{ \$battle->id }}/partial-slot/' + team + '/' + slotNum)
                                .then(res => res.text())
                                .then(html => {
                                    slotEl.innerHTML = html;
                                    slotEl.style.opacity = '1';
                                    
                                    const newCanvases = slotEl.querySelectorAll('canvas[data-card-options]:not([data-initialized="true"])');
                                    newCanvases.forEach(canvas => {
                                        if (typeof DigitalCardRenderer !== 'undefined') {
                                            try {
                                                const renderer = new DigitalCardRenderer(canvas.id);
                                                const options = JSON.parse(canvas.getAttribute('data-card-options'));
                                                renderer.draw(options);
                                                canvas.dataset.initialized = 'true';
                                            } catch (err) {
                                                console.error("Failed to re-render card in slot", err);
                                            }
                                        }
                                    });
                                })
                                .catch(err => {
                                    console.error("Failed to fetch slot update", err);
                                    slotEl.style.opacity = '1';
                                });
                        });
                        
                        if (e.message.includes('started') || e.message.includes('finalized') || e.message.includes('cancelled') || e.message.includes('Team name updated') || e.message.includes('ready')) {
                            setTimeout(() => window.location.reload(), 1000);
                        }
                    }
                });
        }
    });
</script>
JS;

$content = str_replace($oldJS, $newJS, $content);

// Cleanup the duplicated reload script
$content = preg_replace("/<script>\s*document\.addEventListener\('DOMContentLoaded', \(\) => \{\s*if\(window\.Echo\) \{\s*window\.Echo\.channel\('battle\.\{\{ \\\$battle->id \}\}'\)\s*\.listen\('BattleUpdated', \(e\) => \{\s*window\.location\.reload\(\);\s*\}\);\s*\}\s*\}\);\s*<\/script>/s", '', $content);


file_put_contents('resources/views/battles/room.blade.php', $content);
