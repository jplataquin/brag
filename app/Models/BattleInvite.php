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
