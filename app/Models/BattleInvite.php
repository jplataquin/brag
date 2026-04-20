<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BattleInvite extends Model
{
    use HasFactory;

    protected $table = 'battle_invites';

    protected $fillable = [
        'battle_id',
        'invited_user_id',
        'role',
        'status',
    ];

    /**
     * Scope a query to only include active, valid pending invites.
     */
    public function scopeActive($query)
    {
        return $query->where('battle_invites.status', 'pending')
            ->join('battles', 'battles.id', '=', 'battle_invites.battle_id')
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('battle_invites.role', 'opponent')
                       ->where('battles.status', 'pending')
                       ->whereNull('battles.opponent_id');
                })->orWhere(function ($q2) {
                    $q2->where('battle_invites.role', 'adjudicator')
                       ->whereIn('battles.status', ['pending', 'ready', 'active', 'failed'])
                       ->whereNull('battles.adjudicator_id');
                });
            })
            ->select('battle_invites.*');
    }

    /**
     * The battle this invite belongs to.
     */
    public function battle()
    {
        return $this->belongsTo(Battle::class, 'battle_id');
    }

    /**
     * The user who was invited.
     */
    public function invitedUser()
    {
        return $this->belongsTo(User::class, 'invited_user_id');
    }
}
