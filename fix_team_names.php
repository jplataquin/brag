<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

$oldTeamA = <<<'HTML'
            <div class="text-center mb-4 team-name-container"
                 x-data="{ 
                    overflowing: false,
                    checkOverflow() {
                        if(this.$refs.nakedA) {
                            this.overflowing = this.$refs.nakedA.scrollWidth > this.$el.clientWidth;
                        }
                    }
                 }" 
                 x-init="setTimeout(() => checkOverflow(), 200)"
                 x-on:resize.window="checkOverflow()">
                <div :class="{ 'team-name-scroll': overflowing }">
                    <h4 class="orbitron text-cyan mb-0 d-inline-block" :class="{ 'pe-5': overflowing }" title="{{ $battle->team_name_a }}">
                        <span x-ref="nakedA">{{ $battle->team_name_a }}</span>
                    </h4>
                    <h4 x-show="overflowing" class="orbitron text-cyan mb-0 d-inline-block pe-5" title="{{ $battle->team_name_a }}">
                        {{ $battle->team_name_a }}
                    </h4>
                </div>
            </div>
HTML;

$newTeamA = <<<'HTML'
            <div class="text-center mb-4">
                <h4 class="orbitron text-cyan mb-0 text-truncate w-100" title="{{ $battle->team_name_a }}">
                    <span x-ref="nakedA">{{ $battle->team_name_a }}</span>
                </h4>
            </div>
HTML;

$oldTeamB = <<<'HTML'
            <div class="text-center mb-4 team-name-container"
                 x-data="{ 
                    overflowing: false,
                    checkOverflow() {
                        if(this.$refs.nakedB) {
                            this.overflowing = this.$refs.nakedB.scrollWidth > this.$el.clientWidth;
                        }
                    }
                 }" 
                 x-init="setTimeout(() => checkOverflow(), 200)"
                 x-on:resize.window="checkOverflow()">
                <div :class="{ 'team-name-scroll': overflowing }">
                    <h4 class="orbitron text-magenta mb-0 d-inline-block" :class="{ 'pe-5': overflowing }" title="{{ $battle->team_name_b }}">
                        <span x-ref="nakedB">{{ $battle->team_name_b }}</span>
                    </h4>
                    <h4 x-show="overflowing" class="orbitron text-magenta mb-0 d-inline-block pe-5" title="{{ $battle->team_name_b }}">
                        {{ $battle->team_name_b }}
                    </h4>
                </div>
            </div>
HTML;

$newTeamB = <<<'HTML'
            <div class="text-center mb-4">
                <h4 class="orbitron text-magenta mb-0 text-truncate w-100" title="{{ $battle->team_name_b }}">
                    <span x-ref="nakedB">{{ $battle->team_name_b }}</span>
                </h4>
            </div>
HTML;

$content = str_replace($oldTeamA, $newTeamA, $content);
$content = str_replace($oldTeamB, $newTeamB, $content);

file_put_contents('resources/views/battles/room.blade.php', $content);
