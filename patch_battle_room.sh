#!/bin/bash
# 1. Change JOIN TEAM X to JOIN
sed -i 's/JOIN TEAM A/JOIN/g' resources/views/livewire/battle-room.blade.php
sed -i 's/JOIN TEAM B/JOIN/g' resources/views/livewire/battle-room.blade.php
sed -i 's/<h4 class="orbitron text-cyan mb-4 text-center">JOIN<\/h4>/<h4 class="orbitron text-cyan mb-4 text-center">JOIN TEAM {{ $joiningTeam }}<\/h4>/g' resources/views/livewire/battle-room.blade.php

# 2. Add marquee CSS
sed -i '/<div class="team-battle-room"/i <style>\n    .team-name-container {\n        width: 100%;\n        overflow: hidden;\n        white-space: nowrap;\n        position: relative;\n    }\n    .team-name-scroll {\n        display: inline-block;\n        white-space: nowrap;\n        animation: team-marquee 15s linear infinite;\n    }\n    .team-name-scroll:hover {\n        animation-play-state: paused;\n    }\n    @keyframes team-marquee {\n        0% { transform: translateX(0); }\n        100% { transform: translateX(-50%); }\n    }\n</style>\n' resources/views/livewire/battle-room.blade.php

