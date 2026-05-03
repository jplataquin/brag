<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'username',
        'email',
        'google_id',
        'password',
        'email_verified_at',
        'birthdate',
        'gender',
        'avatar',
        'bio',
        'is_admin',
        'terms_version_agreed',
        'privacy_version_agreed',
        'suspended_until',
    ];

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var array
     */
    protected $appends = ['avatar_url'];

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
            'birthdate' => 'date',
            'is_admin' => 'boolean',
            'suspended_until' => 'datetime',
        ];
    }

    /**
     * Check if the user is currently suspended.
     */
    public function isSuspended(): bool
    {
        return $this->suspended_until && $this->suspended_until->isFuture();
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
     * Get the user's diamond ledger entries.
     */
    public function diamondTransactions()
    {
        return $this->hasMany(DiamondLedger::class);
    }

    /**
     * Calculate and return the user's current diamonds balance.
     */
    public function getDiamondsBalanceAttribute()
    {
        $credits = $this->diamondTransactions()->sum('credit');
        $debits = $this->diamondTransactions()->sum('debit');
        
        return $credits - $debits;
    }

    /**
     * Helper to add diamonds to the user's wallet.
     */
    public function addDiamonds($amount, $type, $remarks, $fromId = null, $transferId = null)
    {
        return $this->diamondTransactions()->create([
            'credit' => $amount,
            'debit' => 0,
            'type' => $type,
            'remarks' => $remarks,
            'from_id' => $fromId,
            'transfer_id' => $transferId,
        ]);
    }

    /**
     * Helper to deduct diamonds from the user's wallet.
     */
    public function deductDiamonds($amount, $type, $remarks, $fromId = null, $transferId = null)
    {
        return $this->diamondTransactions()->create([
            'credit' => 0,
            'debit' => $amount,
            'type' => $type,
            'remarks' => $remarks,
            'from_id' => $fromId,
            'transfer_id' => $transferId,
        ]);
    }

    /**
     * Battles where the user is a participant or marshall.
     */
    public function battles()
    {
        // This is not a standard relationship due to multiple columns, 
        // but we can provide a query builder.
        return Battle::where(function($q) {
            for ($i = 1; $i <= 6; $i++) {
                $q->orWhere("team_a_user_{$i}", $this->id)
                  ->orWhere("team_b_user_{$i}", $this->id);
            }
            $q->orWhere('marshall_id', $this->id);
        });
    }

    /**
     * Get the user's currently active or pending battle room.
     */
    public function currentBattleRoom()
    {
        return Battle::whereIn('status', ['pending', 'active'])
            ->where(function($q) {
                for ($i = 1; $i <= 6; $i++) {
                    $q->orWhere("team_a_user_{$i}", $this->id)
                      ->orWhere("team_b_user_{$i}", $this->id);
                }
                $q->orWhere('marshall_id', $this->id);
            })->first();
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
