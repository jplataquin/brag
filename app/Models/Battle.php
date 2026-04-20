<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Battle extends Model
{
    use HasFactory;

    protected $table = 'battles';

    protected $fillable = [
        'room_id',
        'terms',
        'challenger_id',
        'opponent_id',
        'adjudicator_id',
        'challenger_card_id',
        'opponent_card_id',
        'winner_id',
        'status',
        'challenger_cancel',
        'opponent_cancel',
        'challenger_cancel_timestamp',
        'opponent_cancel_timestamp',
        'challenger_declared_user_win',
        'opponent_declared_user_win',
        'adjudicator_declared_user_win',
        'challenger_adjudicator_id',
        'opponent_adjudicator_id',
    ];

    protected $casts = [
        'challenger_cancel' => 'boolean',
        'opponent_cancel' => 'boolean',
        'challenger_cancel_timestamp' => 'datetime',
        'opponent_cancel_timestamp' => 'datetime',
    ];

    /**
     * The challenger who created the battle.
     */
    public function challenger()
    {
        return $this->belongsTo(User::class, 'challenger_id');
    }

    /**
     * The opponent in the battle.
     */
    public function opponent()
    {
        return $this->belongsTo(User::class, 'opponent_id');
    }

    /**
     * The adjudicator of the battle.
     */
    public function adjudicator()
    {
        return $this->belongsTo(User::class, 'adjudicator_id');
    }

    public function challengerAdjudicator()
    {
        return $this->belongsTo(User::class, 'challenger_adjudicator_id');
    }

    public function opponentAdjudicator()
    {
        return $this->belongsTo(User::class, 'opponent_adjudicator_id');
    }

    /**
     * The challenger's card.
     */
    public function challengerCard()
    {
        return $this->belongsTo(DigitalCard::class, 'challenger_card_id');
    }

    /**
     * The opponent's card.
     */
    public function opponentCard()
    {
        return $this->belongsTo(DigitalCard::class, 'opponent_card_id');
    }

    /**
     * The winner of the battle.
     */
    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    /**
     * Invites for this battle.
     */
    public function invites()
    {
        return $this->hasMany(BattleInvite::class, 'battle_id');
    }

    /**
     * Activities for this battle.
     */
    public function activities()
    {
        return $this->hasMany(BattleActivity::class)->orderBy('id', 'asc');
    }

    /**
     * Check if the battle is joinable.
     */
    public function getIsJoinableAttribute()
    {
        return $this->status === 'pending' && is_null($this->opponent_id);
    }

    /**
     * Check if the battle can be decided.
     */
    public function getCanBeDecidedAttribute()
    {
        return in_array($this->status, ['active', 'failed'])
            && $this->challenger_card_id
            && $this->opponent_card_id
            && is_null($this->winner_id);
    }
}
