<?php
$content = file_get_contents('resources/views/battles/room.blade.php');

// Replace Team A loop contents
$teamALoopOld = <<<'HTML'
                    <div class="w-100" style="max-width: 350px;">
                        @if($u && $c)
                            <div class="mb-2 text-center text-truncate">
                                <span class="fw-bold @if($isMe) text-cyan @endif">
                                    @if($i == 1)
                                    👑
                                    @endif
                                    {{ $isMe ? 'YOU' : $u->username }}</span>
                            </div>
                            <div class="{{ $cardClass }}" style="{{ $cardStyle }}">
                                <x-digital-card 
                                    id="card_a_{{ $i }}_{{ $c->id ?? 'none' }}"
                                    mode="thumbnail"
                                    fullscreen="true"
                                    :title="$c->template->card_title"
                                    :game="$c->template->gameTitle->title ?? 'GAME'"
                                    :creator="$c->originalOwner->username ?? 'Creator'"
                                    :quote="$c->template->quote"
                                    :image="$c->template->display_photo"
                                    :imagePositionY="$c->template->image_position_y ?? 50"
                                    :backgroundColor="$c->template->background_color"
                                    :borderColor="$c->template->border_color"
                                    :sectionColor="$c->template->section_color"
                                    :primaryTextColor="$c->template->primary_text_color"
                                    :secondaryTextColor="$c->template->secondary_text_color"
                                    :wins="$snapshot ? $snapshot['wins'] : $c->wins"
                                    :losses="$snapshot ? $snapshot['losses'] : $c->losses"
                                    :integrityStat="$snapshot ? $snapshot['integrity_stat'] : $c->integrity_stat"
                                    :lifePoints="$snapshot ? $snapshot['life_points'] : $c->life_points"
                                    :status="$snapshot ? $snapshot['status'] : $c->status"
                                    :rankLevel="$snapshot ? $snapshot['level'] : $c->level"
                                    :serialNumber="$c->serial_number"
                                    :rarity="$snapshot ? $snapshot['rarity'] : $c->rarity"
                                />
                            </div>
                        @else
                            <div class="mb-2 text-center text-truncate">
                                <span class="fw-bold text-muted">
                                    @if($i == 1)
                                    👑
                                    @endif
                                    ----</span>
                            </div>
                            <div class="empty-card-slot d-flex flex-column align-items-center justify-content-center p-3 rounded" style="border: 2px dashed rgba(0, 240, 255, 0.4); background: rgba(0, 240, 255, 0.05); aspect-ratio: 350 / 490; width: 100%;">
                                <div class="orbitron text-muted mb-3 fs-5">SLOT {{ $i }}</div>
                                @if($battle->status == 'pending' && Auth::id() != $battle->team_a_user_1)
                                    <button type="button" class="btn btn-outline-cyan btn-sm w-100" style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#joinModal" onclick="document.getElementById('joiningTeam').value='A'; document.getElementById('pairingSlot').value={{ $i }}; document.getElementById('join_team_name').innerText='A';">JOIN</button>
                                @endif
                            </div>
                        @endif
                    </div>
HTML;

$teamBLoopOld = <<<'HTML'
                    <div class="w-100" style="max-width: 350px;">
                        @if($u && $c)
                            <div class="mb-2 text-center text-truncate">
                                <span class="fw-bold @if($isMe) text-magenta @endif">
                                    @if($i == 1)
                                    👑
                                    @endif
                                    {{ $isMe ? 'YOU' : $u->username }}</span>
                            </div>
                            <div class="{{ $cardClass }}" style="{{ $cardStyle }}">
                                <x-digital-card 
                                    id="card_b_{{ $i }}_{{ $c->id ?? 'none' }}"
                                    mode="thumbnail"
                                    fullscreen="true"
                                    :title="$c->template->card_title"
                                    :game="$c->template->gameTitle->title ?? 'GAME'"
                                    :creator="$c->originalOwner->username ?? 'Creator'"
                                    :quote="$c->template->quote"
                                    :image="$c->template->display_photo"
                                    :imagePositionY="$c->template->image_position_y ?? 50"
                                    :backgroundColor="$c->template->background_color"
                                    :borderColor="$c->template->border_color"
                                    :sectionColor="$c->template->section_color"
                                    :primaryTextColor="$c->template->primary_text_color"
                                    :secondaryTextColor="$c->template->secondary_text_color"
                                    :wins="$snapshot ? $snapshot['wins'] : $c->wins"
                                    :losses="$snapshot ? $snapshot['losses'] : $c->losses"
                                    :integrityStat="$snapshot ? $snapshot['integrity_stat'] : $c->integrity_stat"
                                    :lifePoints="$snapshot ? $snapshot['life_points'] : $c->life_points"
                                    :status="$snapshot ? $snapshot['status'] : $c->status"
                                    :rankLevel="$snapshot ? $snapshot['level'] : $c->level"
                                    :serialNumber="$c->serial_number"
                                    :rarity="$snapshot ? $snapshot['rarity'] : $c->rarity"
                                />
                            </div>
                        @else
                            <div class="mb-2 text-center text-truncate">
                                <span class="fw-bold text-muted">
                                    @if($i == 1)
                                    👑
                                    @endif
                                    ----</span>
                            </div>
                            <div class="empty-card-slot d-flex flex-column align-items-center justify-content-center p-3 rounded" style="border: 2px dashed rgba(255, 0, 255, 0.4); background: rgba(255, 0, 255, 0.05); aspect-ratio: 350 / 490; width: 100%;">
                                <div class="orbitron text-muted mb-3 fs-5">SLOT {{ $i }}</div>
                                @if($battle->status == 'pending' && Auth::id() != $battle->team_a_user_1)
                                    <button type="button" class="btn btn-outline-magenta btn-sm w-100" style="max-width: 150px;" data-bs-toggle="modal" data-bs-target="#joinModal" onclick="document.getElementById('joiningTeam').value='B'; document.getElementById('pairingSlot').value={{ $i }}; document.getElementById('join_team_name').innerText='B';">JOIN</button>
                                @endif
                            </div>
                        @endif
                    </div>
HTML;

$teamALoopNew = <<<'HTML'
                    <div class="w-100 slot-container" id="slot-container-A-{{ $i }}" style="max-width: 350px;">
                        @include('battles.partials.single-slot', ['team' => 'A', 'slot' => $i, 'u' => $u, 'c' => $c])
                    </div>
HTML;

$teamBLoopNew = <<<'HTML'
                    <div class="w-100 slot-container" id="slot-container-B-{{ $i }}" style="max-width: 350px;">
                        @include('battles.partials.single-slot', ['team' => 'B', 'slot' => $i, 'u' => $u, 'c' => $c])
                    </div>
HTML;

$content = str_replace($teamALoopOld, $teamALoopNew, $content);
$content = str_replace($teamBLoopOld, $teamBLoopNew, $content);

file_put_contents('resources/views/battles/room.blade.php', $content);
