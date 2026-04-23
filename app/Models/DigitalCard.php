<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DigitalCard extends Model
{
    use HasFactory, SoftDeletes;

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
        'burned_at',
        'burned_by',
    ];

    protected $casts = [
        'level' => 'integer',
        'life_points' => 'integer',
        'is_trophy' => 'boolean',
        'forged_at' => 'datetime',
        'burned_at' => 'datetime',
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
     * The user who originally forged the card.
     */
    public function originalOwner()
    {
        return $this->belongsTo(User::class, 'original_owner_id');
    }

    /**
     * The user who burned the card.
     */
    public function burnedBy()
    {
        return $this->belongsTo(User::class, 'burned_by');
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
        $levelConfig = config("leveling.conditions.{$this->level}");
        return $levelConfig['name'] ?? 'Unknown Level';
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
     * Get integrity stat metric.
     * (Unique Users / Total Matches) * 100 rounded to 2 decimal places.
     */
    public function getIntegrityStatAttribute()
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

        $integrityCount = $uniqueUsers->unique()->count();

        return round(($integrityCount / $totalMatches) * 100, 2);
    }

    /**
     * Check and perform promotion if criteria met.
     */
    public function checkPromotion()
    {
        $currentLevel = $this->level;
        $wins = $this->wins;
        $winRate = $this->win_rate;
        $integrity = $this->integrity_stat;

        $newLevel = $currentLevel;
        $levelConditions = config('leveling.conditions', []);

        foreach ($levelConditions as $level => $conditions) {
            $minIntegrity = $conditions['min_integrity'] ?? 0;
            if ($currentLevel < $level && $wins >= $conditions['min_wins'] && $winRate >= $conditions['min_win_rate'] && $integrity >= $minIntegrity) {
                $newLevel = max($newLevel, $level);
            }
        }

        if ($newLevel > $currentLevel) {
            $this->update([
                'level' => $newLevel,
                'life_points' => 3
            ]);
            return true;
        }

        return false;
    }
}
