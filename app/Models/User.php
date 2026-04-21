<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'avatar',
        'bio',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Templates created by the user.
     */
    public function templates()
    {
        return $this->hasMany(Template::class);
    }

    /**
     * Digital cards currently owned by the user.
     */
    public function digitalCards()
    {
        return $this->hasMany(DigitalCard::class, 'owner_id');
    }

    /**
     * Digital cards originally created by the user.
     */
    public function createdCards()
    {
        return $this->hasMany(DigitalCard::class, 'original_owner_id');
    }

    /**
     * Own forged cards (not trophies).
     */
    public function ownForgedCards()
    {
        return $this->digitalCards()->where('is_trophy', false)->where('original_owner_id', $this->id);
    }

    /**
     * Trophy cards (won from other users).
     */
    public function trophies()
    {
        return $this->digitalCards()->where('is_trophy', true);
    }

    /**
     * Battles where the user is the challenger.
     */
    public function challengedBattles()
    {
        return $this->hasMany(Battle::class, 'challenger_id');
    }

    /**
     * Battles where the user is the opponent.
     */
    public function opponentBattles()
    {
        return $this->hasMany(Battle::class, 'opponent_id');
    }

    /**
     * Battles where the user is the marshall.
     */
    public function adjudicatedBattles()
    {
        return $this->hasMany(Battle::class, 'marshall_id');
    }

    /**
     * Battle invites received.
     */
    public function battleInvites()
    {
        return $this->hasMany(BattleInvite::class, 'invited_user_id');
    }

    /**
     * Get avatar URL or default.
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->username) . '&background=0a0a1a&color=00f0ff&bold=true';
    }
}
