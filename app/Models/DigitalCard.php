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
        'win_rate',
        'integrity_stat',
        'life_points',
        'is_trophy',
        'is_censored',
        'forged_at',
        'burned_at',
        'burned_by',
        'admin_editor_id',
        'admin_edited_at',
    ];

    protected $casts = [
        'level' => 'integer',
        'life_points' => 'integer',
        'win_rate' => 'decimal:2',
        'integrity_stat' => 'decimal:2',
        'is_trophy' => 'boolean',
        'is_censored' => 'boolean',
        'forged_at' => 'datetime',
        'burned_at' => 'datetime',
        'admin_edited_at' => 'datetime',
    ];

    /**
     * The admin user who last edited this card.
     */
    public function adminEditor()
    {
        return $this->belongsTo(User::class, 'admin_editor_id');
    }

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
        $path = "img/badge/lv{$level}.webp";
        $version = file_exists(public_path($path)) ? filemtime(public_path($path)) : time();
        return asset($path) . "?v=" . $version;
    }

    /**
     * Get win rate percentage (calculated from stored column).
     */
    public function getWinRateAttribute($value)
    {
        return (float) $value;
    }

    /**
     * Update and persist leaderboard stats (win rate and integrity).
     */
    public function updateLeaderboardStats()
    {
        // 1. Calculate Win Rate
        $total = $this->wins + $this->losses;
        $newWinRate = ($total === 0) ? 0 : ($this->wins / $total) * 100;

        // 2. Calculate Integrity Stat
        $uniqueUsers = collect();
        $totalMatches = 0;

        $battles = \App\Models\Battle::where('status', 'completed')
            ->where(function($q) {
                for ($i = 1; $i <= 6; $i++) {
                    $q->orWhere("team_a_card_{$i}", $this->id)
                      ->orWhere("team_b_card_{$i}", $this->id);
                }
            })->get();

        foreach ($battles as $b) {
            for ($i = 1; $i <= $b->no_players_per_team; $i++) {
                if ($b->{"team_a_card_{$i}"} == $this->id) {
                    if ($b->{"team_b_user_{$i}"}) {
                        $uniqueUsers->push($b->{"team_b_user_{$i}"});
                        $totalMatches++;
                    }
                } elseif ($b->{"team_b_card_{$i}"} == $this->id) {
                    if ($b->{"team_a_user_{$i}"}) {
                        $uniqueUsers->push($b->{"team_a_user_{$i}"});
                        $totalMatches++;
                    }
                }
            }
        }

        // Tie the integrity denominator to whichever is higher between total matches and total wins.
        // This neutralizes the "No Quarter" multiplier exploit (getting lots of wins in very few matches).
        $integrityDenominator = max($totalMatches, $this->wins);
        $newIntegrity = ($integrityDenominator === 0) ? 0 : round(($uniqueUsers->unique()->count() / $integrityDenominator) * 100, 2);

        // Update the model and database directly to avoid recursion
        $this->win_rate = $newWinRate;
        $this->integrity_stat = $newIntegrity;
        $this->saveQuietly();
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
