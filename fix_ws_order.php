<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$old = <<<'JS'
                        // 2. Handle Real-time Slot Updates
                        const slots = document.querySelectorAll('.slot-container');
                        slots.forEach(slotEl => {
                            const idParts = slotEl.id.split('-'); 
                            const team = idParts[2];
                            const slotNum = idParts[3];
                            
                            // Visual cue that it is updating
                            slotEl.style.opacity = '0.5';
                            
                            // Ignore slot updates if we are actively reloading the page
                            if (window.isReloading) return;

                            fetch('/battles/{{ $battle->id }}/partial-slot/' + team + '/' + slotNum)
JS;

$new = <<<'JS'
                        // Major state changes check FIRST so we can flag isReloading
                        if (e.message.includes('ready')) {
                            const standUpBtn = document.getElementById('standUpForm');
                            if (standUpBtn) standUpBtn.style.display = 'none';
                        }
                        if (e.message.includes('started') || e.message.includes('finalized') || e.message.includes('cancelled') || e.message.includes('ready') || e.message.includes('requested cancellation') || e.message.includes('rejected')) {
                            window.isReloading = true;
                            setTimeout(() => window.location.reload(), 1000);
                            return; // Stop processing updates!
                        }

                        // 2. Handle Real-time Slot Updates ONLY if not reloading
                        const slots = document.querySelectorAll('.slot-container');
                        slots.forEach(slotEl => {
                            const idParts = slotEl.id.split('-'); 
                            const team = idParts[2];
                            const slotNum = idParts[3];
                            
                            // Visual cue that it is updating
                            slotEl.style.opacity = '0.5';
                            
                            fetch('/battles/{{ $battle->id }}/partial-slot/' + team + '/' + slotNum)
JS;

$content = str_replace($old, $new, $content);

$oldEnd = <<<'JS'
                        // Major state changes still require a reload to update buttons/permissions
                        if (e.message.includes('ready')) {
                            const standUpBtn = document.getElementById('standUpForm');
                            if (standUpBtn) standUpBtn.style.display = 'none';
                        }
                        if (e.message.includes('started') || e.message.includes('finalized') || e.message.includes('cancelled') || e.message.includes('ready') || e.message.includes('requested cancellation') || e.message.includes('rejected')) {
                            window.isReloading = true;
                            setTimeout(() => window.location.reload(), 1000);
                        }
                    }
JS;

$newEnd = <<<'JS'
                    }
JS;

$content = str_replace($oldEnd, $newEnd, $content);

file_put_contents('resources/views/battles/room.blade.php', $content);
