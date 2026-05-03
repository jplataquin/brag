<?php
$html = file_get_contents('resources/views/battles/room.blade.php');

$inviteModalHtml = <<<HTML
                    <form action="{{ route('battles.action.invite', \$battle) }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" id="invite_nominee_id">
                        <label class="form-label">PLAYER USERNAME</label>
                        <div class="form-control d-flex align-items-center p-1" style="min-height: 42px; position: relative;">
                            <span class="badge d-flex align-items-center gap-2 p-2 d-none" id="invite_selected_badge" style="background: rgba(0,240,255,0.2); border: 1px solid #00f0ff; color: #00f0ff; font-size: 0.9rem;">
                                <i class="bi bi-person-fill"></i> 
                                <span id="invite_selected_username"></span>
                                <i class="bi bi-x-circle-fill ms-2" style="cursor: pointer;" onclick="clearInvite()"></i>
                            </span>
                            <input type="text" id="invite_search_input" class="border-0 bg-transparent text-white flex-grow-1 px-2" placeholder="Search username..." autocomplete="off" style="outline: none; box-shadow: none;" oninput="searchUsers(this.value, 'invite')">
                        </div>
                        <div class="position-absolute w-100 mt-1 d-none" id="invite_search_results" style="z-index: 1050; max-height: 200px; overflow-y: auto; background: rgba(10, 10, 30, 0.95); border: 1px solid #00f0ff; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                        </div>
                    </div>
                    <p class="text-muted small">Invited players will receive a notification to join this battle room.</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-neon w-100" id="invite_submit_btn" disabled>SEND INVITE</button>
                </div>
                </form>
HTML;

$html = preg_replace('/<label class="form-label">PLAYER USERNAME<\/label>.*?<div class="modal-footer border-0 pt-0">\s*<button type="button" class="btn btn-neon w-100" wire:click="sendInvite\(\)" data-bs-dismiss="modal" @if\(!\$inviteNomineeId\) disabled @endif>SEND INVITE<\/button>\s*<\/div>/s', $inviteModalHtml, $html);
file_put_contents('resources/views/battles/room.blade.php', $html);
