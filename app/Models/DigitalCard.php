<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'owner_id',
        'original_owner_id',
        'serial_number',
        'wins',
        'losses',
        'is_trophy',
        'forged_at',
    ];

    protected $casts = [
        'is_trophy' => 'boolean',
        'forged_at' => 'datetime',
    ];

    /**
     * The template this card was forged from.
     */
    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * The current owner of this card.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the serial number for the card (which is simply its ID).
     */
    public function getSerialNumberAttribute($value)
    {
        return $this->id;
    }

    /**
     * The original creator of this card.
     */
    public function originalOwner()
    {
        return $this->belongsTo(User::class, 'original_owner_id');
    }

    /**
     * Get the rarity tier based on stats.
     */
    public function getRarityAttribute()
    {
        $copies = $this->template->cards_in_circulation;
        $wins = $this->wins;

        if ($wins >= 50 && $copies <= 1) {
            return 'legendary';
        }
        if ($wins >= 30 && $copies <= 3) {
            return 'epic';
        }
        if ($wins >= 15 || $copies <= 3) {
            return 'rare';
        }
        if ($wins >= 5) {
            return 'uncommon';
        }
        return 'common';
    }

    /**
     * Get rarity color for display.
     */
    public function getRarityColorAttribute()
    {
        return match($this->rarity) {
            'legendary' => '#ff4444',
            'epic' => '#ffdd00',
            'rare' => '#cc44ff',
            'uncommon' => '#4488ff',
            'common' => '#44ff88',
        };
    }

    /**
     * Get rarity icon/emoji for display.
     */
    public function getRarityIconAttribute()
    {
        return match($this->rarity) {
            'legendary' => '🔴',
            'epic' => '🟡',
            'rare' => '🟣',
            'uncommon' => '🔵',
            'common' => '🟢',
        };
    }

    /**
     * Get level (based on wins).
     */
    public function getLevelAttribute()
    {
        return min(100, intdiv($this->wins, 5) + 1);
    }

    /**
     * Get win rate percentage.
     */
    public function getWinRateAttribute()
    {
        $total = $this->wins + $this->losses;
        if ($total === 0) return 0;
        return round(($this->wins / $total) * 100, 1);
    }
}
