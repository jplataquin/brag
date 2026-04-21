<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalCard extends Model
{
    use HasFactory;

    const STATUS_MAINTAINED = 'Maintained';
    const STATUS_DISCONTINUED = 'Discontinued';

    const LEVEL_1 = 1; // Casual
    const LEVEL_2 = 2; // Competitive
    const LEVEL_3 = 3; // Elite
    const LEVEL_4 = 4; // Legendary
    const LEVEL_5 = 5; // GOAT

    protected $fillable = [
        'template_id',
        'owner_id',
        'original_owner_id',
        'serial_number',
        'level',
        'status',
        'wins',
        'losses',
        'life_points',
        'is_trophy',
        'forged_at',
    ];

    protected $casts = [
        'level' => 'integer',
        'life_points' => 'integer',
        'is_trophy' => 'boolean',
        'forged_at' => 'datetime',
    ];

    /**
     * The template this card was forged from.
     */
    public function template()
    {
        return $this->belongsTo(Template::class)->withTrashed();
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
     * Get the rarity tier based on circulation.
     */
    public function getRarityAttribute()
    {
        // Circulation of same template AND same level
        $copies = self::where('template_id', $this->template_id)
            ->where('level', $this->level)
            ->count();

        if ($copies >= 10) {
            return 'Common';
        }
        if ($copies >= 5) {
            return 'Rare';
        }
        return 'Ultra Rare';
    }

    /**
     * Get rarity slug for CSS.
     */
    public function getRaritySlugAttribute()
    {
        return str_replace(' ', '-', strtolower($this->rarity));
    }

    /**
     * Get rarity color for display.
     */
    public function getRarityColorAttribute()
    {
        return match($this->rarity) {
            'Ultra Rare' => '#ff0000', // Red (Legendary)
            'Rare' => '#ff00ff',       // Magenta
            'Common' => '#39ff14',     // Lime Green
        };
    }

    /**
     * Get level name.
     */
    public function getLevelNameAttribute()
    {
        return match($this->level) {
            self::LEVEL_1 => 'Casual',
            self::LEVEL_2 => 'Competitive',
            self::LEVEL_3 => 'Elite',
            self::LEVEL_4 => 'Legendary',
            self::LEVEL_5 => 'GOAT',
            default => 'Unknown',
        };
    }

    /**
     * Get level badge icon.
     */
    public function getLevelBadgeAttribute()
    {
        $level = min(5, max(1, $this->level));
        $path = "img/badge/lv{$level}.png";
        $version = file_exists(public_path($path)) ? filemtime(public_path($path)) : time();
        return asset($path) . "?v=" . $version;
    }

    /**
     * Get win rate percentage.
     */
    public function getWinRateAttribute()
    {
        $total = $this->wins + $this->losses;
        if ($total === 0) return 0;
        return ($this->wins / $total) * 100;
    }

    /**
     * Get distinct stat metric.
     * (Unique Users / Total Matches) * 100 rounded to 2 decimal places.
     */
    public function getDistinctStatAttribute()
    {
        $battlesAsChallenger = \App\Models\Battle::where('challenger_card_id', $this->id)
                                ->whereNotNull('opponent_id')->get();
        $battlesAsOpponent = \App\Models\Battle::where('opponent_card_id', $this->id)
                                ->whereNotNull('challenger_id')->get();

        $totalMatches = $battlesAsChallenger->count() + $battlesAsOpponent->count();

        if ($totalMatches === 0) {
            return 0;
        }

        $uniqueUsers = collect();

        foreach ($battlesAsChallenger as $battle) {
            $uniqueUsers->push($battle->opponent_id);
        }

        foreach ($battlesAsOpponent as $battle) {
            $uniqueUsers->push($battle->challenger_id);
        }

        $distinctCount = $uniqueUsers->unique()->count();

        return round(($distinctCount / $totalMatches) * 100, 2);
    }

    /**
     * Check and perform promotion if criteria met.
     */
    public function checkPromotion()
    {
        $currentLevel = $this->level;
        $wins = $this->wins;
        $winRate = $this->win_rate;

        $newLevel = $currentLevel;

        // Level 2 - Competitive: >= 5 wins, >= 51% win rate
        if ($currentLevel < 2 && $wins >= 5 && $winRate >= 51) {
            $newLevel = 2;
        }
        // Level 3 - Elite: >= 10 wins, >= 60% win rate
        if ($currentLevel < 3 && $wins >= 10 && $winRate >= 60) {
            $newLevel = 3;
        }
        // Level 4 - Legendary: >= 15 wins, >= 80% win rate
        if ($currentLevel < 4 && $wins >= 15 && $winRate >= 80) {
            $newLevel = 4;
        }
        // Level 5 - GOAT: >= 25 wins, >= 95% win rate
        if ($currentLevel < 5 && $wins >= 25 && $winRate >= 95) {
            $newLevel = 5;
        }

        if ($newLevel > $currentLevel) {
            $this->update(['level' => $newLevel]);
            return true;
        }

        return false;
    }
}
