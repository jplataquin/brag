<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$js = <<<'JS'
<script>
    // Real-time Room Updates via Hybrid Fetch & AJAX
    document.addEventListener('DOMContentLoaded', () => {
        if(window.Echo) {
            window.Echo.channel('battle.{{ $battle->id }}')
                .listen('BattleUpdated', (e) => {
                    if (e.type === 'update') {
                        // 1. Update Team Names
                        if (e.team_name_a) {
                            const nameA = document.querySelectorAll('[x-ref="nakedA"]');
                            nameA.forEach(el => el.innerText = e.team_name_a);
                        }
                        if (e.team_name_b) {
                            const nameB = document.querySelectorAll('[x-ref="nakedB"]');
                            nameB.forEach(el => el.innerText = e.team_name_b);
                        }

                        // 2. Handle Real-time Slot Updates
                        const slots = document.querySelectorAll('.slot-container');
                        slots.forEach(slotEl => {
                            const idParts = slotEl.id.split('-'); 
                            const team = idParts[2];
                            const slotNum = idParts[3];
                            
                            // Visual cue that it is updating
                            slotEl.style.opacity = '0.5';
                            
                            fetch('/battles/{{ $battle->id }}/partial-slot/' + team + '/' + slotNum)
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
                        
                        // Major state changes still require a reload to update buttons/permissions
                        if (e.message.includes('started') || e.message.includes('finalized') || e.message.includes('cancelled') || e.message.includes('ready')) {
                            setTimeout(() => window.location.reload(), 1000);
                        }
                    }
                });
        }
    });
</script>
JS;

// Match the existing consolidated script
$pattern = "/<script>\s*\/\/ Real-time Room Updates via Hybrid Fetch & AJAX.*?<\/script>/s";
$content = preg_replace($pattern, $js, $content);

file_put_contents('resources/views/battles/room.blade.php', $content);
