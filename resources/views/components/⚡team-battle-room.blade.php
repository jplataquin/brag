<?php

use Livewire\Volt\Component;
use App\Models\TeamBattle;
use App\Models\DigitalCard;
use App\Models\User;
use App\Models\BattleActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

new class extends Component
{
    public TeamBattle $teamBattle;
    public $selectedCardId = '';
    public $joiningTeam = ''; // 'A' or 'B'
    public $pairingSlot = null;
    
    // For editing team names
    public $showEditTeamA = false;
    public $showEditTeamB = false;
    public $newTeamNameA = '';
    public $newTeamNameB = '';

    public function mount(TeamBattle $teamBattle)
    {
        $this->teamBattle = $teamBattle;
        $this->newTeamNameA = $teamBattle->team_name_a;
        $this->newTeamNameB = $teamBattle->team_name_b;
    }

    public function getListeners()
    {
        return [
            "echo:team-battle.{$this->teamBattle->id},TeamBattleUpdated" => 'refreshRoom',
        ];
    }

    public function refreshRoom()
    {
        $this->teamBattle->refresh();
        $this->dispatch('battle-updated');
    }

    public function joinTeam($team, $slot = null)
    {
        $this->joiningTeam = $team;
        $this->pairingSlot = $slot;
        $this->selectedCardId = '';
    }

    public function confirmJoin()
    {
        $this->validate([
            'selectedCardId' => 'required|exists:digital_cards,id',
            'joiningTeam' => 'required|in:A,B',
        ]);

        $user = Auth::user();
        $card = DigitalCard::find($this->selectedCardId);

        // Basic validations
        if ($card->template->game_title_id != $this->teamBattle->game_title_id) {
            $this->addError('selectedCardId', 'Card must match the game title.');
            return;
        }

        if ($card->life_points <= 0) {
            $this->addError('selectedCardId', 'Card has no life points.');
            return;
        }

        // Check if user is already in the battle
        for ($i = 1; $i <= 6; $i++) {
            if ($this->teamBattle->{"team_a_user_{$i}"} == $user->id || $this->teamBattle->{"team_b_user_{$i}"} == $user->id) {
                session()->flash('error', 'You are already in this battle.');
                return;
            }
        }

        DB::transaction(function () use ($user, $card) {
            // Lock the record for update to prevent race conditions
            $battle = TeamBattle::where('id', $this->teamBattle->id)->lockForUpdate()->first();

            $team = $this->joiningTeam;
            $slot = $this->pairingSlot;

            if ($slot) {
                // User wants to pair with someone in a specific slot
                $userField = "team_{$team}_user_{$slot}";
                $cardField = "team_{$team}_card_{$slot}";

                if ($battle->$userField) {
                     // Slot taken while user was selecting
                     $slot = null; // Fallback to auto-assignment
                } else {
                    $battle->update([
                        $userField => $user->id,
                        $cardField => $card->id,
                    ]);
                }
            }

            if (!$slot) {
                // Auto-assignment to next available slot in that team
                $assigned = false;
                for ($i = 1; $i <= $battle->no_players_per_team; $i++) {
                    $userField = "team_{$team}_user_{$i}";
                    $cardField = "team_{$team}_card_{$i}";
                    if (!$battle->$userField) {
                        $battle->update([
                            $userField => $user->id,
                            $cardField => $card->id,
                        ]);
                        $assigned = true;
                        break;
                    }
                }

                if (!$assigned) {
                    throw new \Exception("Team {$team} is already full.");
                }
            }

            $this->logActivity($user->id, 'join', "{$user->username} joined Team {$team}.");
        });

        $this->joiningTeam = '';
        $this->pairingSlot = null;
        $this->refreshRoom();
        $this->broadcastUpdate("{$user->username} joined the battle.");
    }

    public function startBattle()
    {
        $user = Auth::user();
        if ($user->id != $this->teamBattle->team_a_user_1) {
            session()->flash('error', 'Only Team A leader can start the battle.');
            return;
        }

        // Check if all slots are filled
        for ($i = 1; $i <= $this->teamBattle->no_players_per_team; $i++) {
            if (!$this->teamBattle->{"team_a_user_{$i}"} || !$this->teamBattle->{"team_b_user_{$i}"}) {
                session()->flash('error', 'All player slots must be filled before starting.');
                return;
            }
        }

        $this->teamBattle->update(['status' => 'active']);
        $this->logActivity($user->id, 'start', "Battle has officially BEGUN!");
        $this->broadcastUpdate("Battle started!");
    }

    public function declareWin($team)
    {
        $user = Auth::user();
        $isLeaderA = $user->id == $this->teamBattle->team_a_user_1;
        $isLeaderB = $user->id == $this->teamBattle->team_b_user_1;
        $isMarshall = $user->id == $this->teamBattle->marshall_id;

        if (!$isLeaderA && !$isLeaderB && !$isMarshall) {
             session()->flash('error', 'Unauthorized.');
             return;
        }

        DB::transaction(function () use ($team, $user, $isLeaderA, $isLeaderB, $isMarshall) {
            $battle = TeamBattle::where('id', $this->teamBattle->id)->lockForUpdate()->first();
            
            if ($isLeaderA) $battle->team_a_declare_win = $team;
            if ($isLeaderB) $battle->team_b_declare_win = $team;
            if ($isMarshall) $battle->marshall_declare_win = $team;
            
            $battle->save();

            $this->logActivity($user->id, 'declare', "{$user->username} declared Team {$team} as winner.");

            // Check for finalization
            $finalWinnerTeam = null;
            if ($isMarshall) {
                $finalWinnerTeam = $team;
            } elseif ($battle->team_a_declare_win && $battle->team_b_declare_win) {
                if ($battle->team_a_declare_win == $battle->team_b_declare_win) {
                    $finalWinnerTeam = $battle->team_a_declare_win;
                } else {
                    $battle->update(['status' => 'failed']);
                    $this->broadcastUpdate("Conflict in declaration!");
                }
            }

            if ($finalWinnerTeam) {
                $this->finalizeTeamBattle($battle, $finalWinnerTeam);
            } else {
                $this->broadcastUpdate("{$user->username} declared a winner.");
            }
        });
        
        $this->refreshRoom();
    }

    protected function finalizeTeamBattle($battle, $winnerTeam)
    {
        $loserTeam = $winnerTeam == 'A' ? 'B' : 'A';
        
        // Process results for each pair
        for ($i = 1; $i <= $battle->no_players_per_team; $i++) {
            $winnerUserId = $battle->{"team_{$winnerTeam}_user_{$i}"};
            $winnerCardId = $battle->{"team_{$winnerTeam}_card_{$i}"};
            $loserCardId = $battle->{"team_{$loserTeam}_card_{$i}"};
            
            $winnerUser = User::find($winnerUserId);
            $winnerCard = DigitalCard::find($winnerCardId);
            $loserCard = DigitalCard::find($loserCardId);
            
            if ($winnerCard && $loserCard && $winnerUser) {
                $battle->processBattleResult($winnerCard, $loserCard, $winnerUser);
            }
        }

        $battle->update(['status' => 'completed']);
        $this->logActivity(null, 'completed', "Team Battle finalized. Team {$winnerTeam} won!");
        $this->broadcastUpdate("Team Battle finalized!");
    }

    public function cancelBattle()
    {
        $user = Auth::user();
        $isLeaderA = $user->id == $this->teamBattle->team_a_user_1;
        $isLeaderB = $user->id == $this->teamBattle->team_b_user_1;
        $isMarshall = $user->id == $this->teamBattle->marshall_id;

        if (!$isLeaderA && !$isLeaderB && !$isMarshall) return;

        DB::transaction(function () use ($user, $isLeaderA, $isLeaderB, $isMarshall) {
            $battle = TeamBattle::where('id', $this->teamBattle->id)->lockForUpdate()->first();
            
            if ($isLeaderA) $battle->team_a_cancel_flag = true;
            if ($isLeaderB) $battle->team_b_cancel_flag = true;
            if ($isMarshall) $battle->marshall_cancel_flag = true;
            
            $battle->save();

            if ($isMarshall || ($battle->team_a_cancel_flag && $battle->team_b_cancel_flag)) {
                $battle->update(['status' => 'cancelled']);
                $this->broadcastUpdate("Battle cancelled.");
            } else {
                $this->broadcastUpdate("{$user->username} requested cancellation.");
            }
        });
        
        $this->refreshRoom();
    }

    public function electMarshall($nomineeId)
    {
        $user = Auth::user();
        $isLeaderA = $user->id == $this->teamBattle->team_a_user_1;
        $isLeaderB = $user->id == $this->teamBattle->team_b_user_1;
        
        if (!$isLeaderA && !$isLeaderB) return;

        if ($isLeaderA) $this->teamBattle->update(['team_a_marshall_elect' => $nomineeId]);
        if ($isLeaderB) $this->teamBattle->update(['team_b_marshall_elect' => $nomineeId]);

        if ($this->teamBattle->team_a_marshall_elect && $this->teamBattle->team_a_marshall_elect == $this->teamBattle->team_b_marshall_elect) {
             // Consensus
             $this->teamBattle->update([
                 'marshall_id' => $this->teamBattle->team_a_marshall_elect,
                 'team_a_marshall_elect' => null,
                 'team_b_marshall_elect' => null
             ]);
             $this->broadcastUpdate("Marshall elected!");
        } else {
            $this->broadcastUpdate("Marshall nominated.");
        }
        
        $this->refreshRoom();
    }

    public function updateTeamName($team)
    {
        $user = Auth::user();
        if ($team == 'A' && $user->id == $this->teamBattle->team_a_user_1) {
            $this->teamBattle->update(['team_name_a' => $this->newTeamNameA]);
            $this->showEditTeamA = false;
        } elseif ($team == 'B' && $user->id == $this->teamBattle->team_b_user_1) {
            $this->teamBattle->update(['team_name_b' => $this->newTeamNameB]);
            $this->showEditTeamB = false;
        }
        $this->broadcastUpdate("Team name updated.");
        $this->refreshRoom();
    }

    protected function logActivity($userId, $type, $message)
    {
        BattleActivity::create([
            'team_battle_id' => $this->teamBattle->id,
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
        ]);
    }

    protected function broadcastUpdate($message)
    {
        event(new \App\Events\TeamBattleUpdated($this->teamBattle, $message, 'update'));
    }

    public function with()
    {
        $user = Auth::user();
        $cards = $user->digitalCards()
            ->where('life_points', '>', 0)
            ->get()
            ->filter(fn($c) => $c->template->game_title_id == $this->teamBattle->game_title_id);

        return [
            'myEligibleCards' => $cards,
            'activities' => BattleActivity::where('team_battle_id', $this->teamBattle->id)->latest()->take(20)->get()->reverse()
        ];
    }
};
?>

<div class="team-battle-room" wire:poll.10s>
    <div class="row">
        <!-- Team A -->
        <div class="col-md-5">
            <div class="neon-card p-3 mb-4" style="border-color: #00f0ff !important;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    @if($showEditTeamA)
                        <div class="input-group input-group-sm">
                            <input type="text" wire:model="newTeamNameA" class="form-control bg-dark text-white border-cyan">
                            <button class="btn btn-neon btn-sm" wire:click="updateTeamName('A')">SAVE</button>
                        </div>
                    @else
                        <h4 class="orbitron text-cyan mb-0">
                            {{ $teamBattle->team_name_a }}
                            @if(Auth::id() == $teamBattle->team_a_user_1)
                                <i class="bi bi-pencil-square cursor-pointer ms-2" style="font-size: 0.8rem;" wire:click="$set('showEditTeamA', true)"></i>
                            @endif
                        </h4>
                    @endif
                    <span class="badge bg-cyan text-dark">TEAM A</span>
                </div>

                <div class="team-slots">
                    @for($i = 1; $i <= $teamBattle->no_players_per_team; $i++)
                        @php 
                            $u = User::find($teamBattle->{"team_a_user_{$i}"});
                            $c = DigitalCard::find($teamBattle->{"team_a_card_{$i}"});
                        @endphp
                        <div class="slot-item p-2 border-bottom border-secondary d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="slot-number me-3 orbitron text-muted">{{ $i }}</div>
                                @if($u)
                                    <div>
                                        <div class="fw-bold">{{ $u->username }}</div>
                                        <div class="small text-cyan">{{ $c->template->card_title }} (LVL {{ $c->level }})</div>
                                    </div>
                                @else
                                    <div class="text-muted italic">Empty Slot</div>
                                @endif
                            </div>
                            @if(!$u && $teamBattle->status == 'pending')
                                <button class="btn btn-outline-cyan btn-sm" wire:click="joinTeam('A', {{ $i }})">JOIN</button>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- VS Divider -->
        <div class="col-md-2 d-flex align-items-center justify-content-center my-3">
            <div class="orbitron text-white h2">VS</div>
        </div>

        <!-- Team B -->
        <div class="col-md-5">
            <div class="neon-card p-3 mb-4" style="border-color: #ff00ff !important;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    @if($showEditTeamB)
                        <div class="input-group input-group-sm">
                            <input type="text" wire:model="newTeamNameB" class="form-control bg-dark text-white border-magenta">
                            <button class="btn btn-neon-magenta btn-sm" wire:click="updateTeamName('B')">SAVE</button>
                        </div>
                    @else
                        <h4 class="orbitron text-magenta mb-0">
                            {{ $teamBattle->team_name_b }}
                            @if(Auth::id() == $teamBattle->team_b_user_1)
                                <i class="bi bi-pencil-square cursor-pointer ms-2" style="font-size: 0.8rem;" wire:click="$set('showEditTeamB', true)"></i>
                            @endif
                        </h4>
                    @endif
                    <span class="badge bg-magenta text-white">TEAM B</span>
                </div>

                <div class="team-slots">
                    @for($i = 1; $i <= $teamBattle->no_players_per_team; $i++)
                        @php 
                            $u = User::find($teamBattle->{"team_b_user_{$i}"});
                            $c = DigitalCard::find($teamBattle->{"team_b_card_{$i}"});
                        @endphp
                        <div class="slot-item p-2 border-bottom border-secondary d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="slot-number me-3 orbitron text-muted">{{ $i }}</div>
                                @if($u)
                                    <div>
                                        <div class="fw-bold">{{ $u->username }}</div>
                                        <div class="small text-magenta">{{ $c->template->card_title }} (LVL {{ $c->level }})</div>
                                    </div>
                                @else
                                    <div class="text-muted italic">Empty Slot</div>
                                @endif
                            </div>
                            @if(!$u && $teamBattle->status == 'pending')
                                <button class="btn btn-outline-magenta btn-sm" wire:click="joinTeam('B', {{ $i }})">JOIN</button>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Battle Controls -->
    <div class="row justify-content-center mt-4">
        <div class="col-lg-7">
            <div class="neon-card p-4">
                <div class="row">
                    <div class="col-md-7">
                        <h5 class="orbitron text-cyan">BATTLE STATUS: <span class="text-white">{{ strtoupper($teamBattle->status) }}</span></h5>
                        <p class="small text-muted">{{ $teamBattle->battle_terms }}</p>
                        
                        @if($teamBattle->marshall_id)
                            <div class="mt-3">
                                <span class="badge bg-warning text-dark"><i class="bi bi-shield-check"></i> MARSHALL: {{ $teamBattle->marshall->username }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-5 text-md-end">
                        @if($teamBattle->status == 'pending')
                            @if(Auth::id() == $teamBattle->team_a_user_1)
                                <button class="btn btn-neon btn-lg orbitron" wire:click="startBattle">START BATTLE</button>
                            @else
                                <div class="alert alert-info py-2 small">Waiting for Team A Leader to start...</div>
                            @endif
                        @elseif($teamBattle->status == 'active')
                            <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                                @if(Auth::id() == $teamBattle->team_a_user_1 || Auth::id() == $teamBattle->team_b_user_1 || Auth::id() == $teamBattle->marshall_id)
                                    <button class="btn btn-neon btn-sm" wire:click="declareWin('A')">TEAM A WON</button>
                                    <button class="btn btn-neon-magenta btn-sm" wire:click="declareWin('B')">TEAM B WON</button>
                                    <button class="btn btn-outline-danger btn-sm" wire:click="cancelBattle">CANCEL</button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Activity Log -->
        <div class="col-lg-3 mt-4 mt-lg-0">
            <div class="neon-card p-3 h-100">
                <h6 class="orbitron text-cyan mb-3 border-bottom border-secondary pb-2">ACTIVITY LOG</h6>
                <div class="activity-log-container" style="max-height: 300px; overflow-y: auto;">
                    @foreach($activities as $activity)
                        <div class="activity-item mb-2 border-bottom border-secondary border-opacity-10 pb-1">
                            <span class="text-muted small">[{{ $activity->created_at->format('H:i') }}]</span>
                            <span class="small">{{ $activity->message }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Join Modal (Simulated) -->
    @if($joiningTeam)
        <div class="custom-modal-backdrop">
            <div class="custom-modal p-4 neon-card" style="max-width: 600px; width: 90%;">
                <h4 class="orbitron text-cyan mb-4">JOIN TEAM {{ $joiningTeam }}</h4>
                
                <div class="mb-4">
                    <label class="form-label small">SELECT YOUR CARD TO BET</label>
                    <div class="row g-2" style="max-height: 300px; overflow-y: auto;">
                        @foreach($myEligibleCards as $card)
                            <div class="col-4">
                                <div class="selectable-card {{ $selectedCardId == $card->id ? 'selected' : '' }}" 
                                     wire:click="$set('selectedCardId', {{ $card->id }})">
                                    <img src="{{ $card->template->display_photo }}" class="img-fluid rounded mb-1">
                                    <div style="font-size: 0.6rem; line-height: 1;" class="text-center text-truncate">{{ $card->template->card_title }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('selectedCardId') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-neon w-100" wire:click="confirmJoin">CONFIRM JOIN</button>
                    <button class="btn btn-outline-secondary w-100" wire:click="$set('joiningTeam', '')">CANCEL</button>
                </div>
            </div>
        </div>
    @endif

    <style>
        .custom-modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(5px);
            z-index: 1050;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .custom-modal {
            background: #0a0a1a;
            border: 1px solid rgba(0,240,255,0.3);
            box-shadow: 0 0 30px rgba(0,240,255,0.2);
        }
        .selectable-card {
            cursor: pointer;
            border: 2px solid transparent;
            padding: 2px;
            border-radius: 5px;
        }
        .selectable-card.selected {
            border-color: #00f0ff;
            background: rgba(0,240,255,0.1);
        }
        .cursor-pointer { cursor: pointer; }
        .activity-log-container::-webkit-scrollbar {
            width: 4px;
        }
        .activity-log-container::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.2);
        }
        .activity-log-container::-webkit-scrollbar-thumb {
            background: rgba(0,240,255,0.3);
            border-radius: 2px;
        }
    </style>
</div>
