<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HandlesBattleResults;

class Battle extends Model
{
    use HasFactory, HandlesBattleResults;

    protected $table = 'battles';

    protected $fillable = [
        'room_id',
        'terms',
        'challenger_id',
        'opponent_id',
        'marshall_id',
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
        'marshall_declared_user_win',
        'challenger_marshall_id',
        'opponent_marshall_id',
        'challenger_card_data',
        'opponent_card_data',
    ];

    protected $casts = [
        'challenger_cancel' => 'boolean',
        'opponent_cancel' => 'boolean',
        'challenger_cancel_timestamp' => 'datetime',
        'opponent_cancel_timestamp' => 'datetime',
        'challenger_card_data' => 'array',
        'opponent_card_data' => 'array',
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
     * The marshall of the battle.
     */
    public function marshall()
    {
        return $this->belongsTo(User::class, 'marshall_id');
    }

    public function challengerMarshall()
    {
        return $this->belongsTo(User::class, 'challenger_marshall_id');
    }

    public function opponentMarshall()
    {
        return $this->belongsTo(User::class, 'opponent_marshall_id');
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
