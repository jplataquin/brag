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
        'game_title_id',
        'room_slug',
        'team_name_a',
        'team_name_b',
        'battle_terms',
        'no_players_per_team',
        'status',
        'winner_team',
        'team_a_card_data',
        'team_b_card_data',
        'team_b_ready',
        'team_a_cancel_flag',
        'team_b_cancel_flag',
        'marshall_cancel_flag',
        'team_a_declare_win',
        'team_b_declare_win',
        'marshall_declare_win',
        'team_a_marshall_elect',
        'team_b_marshall_elect',
        'marshall_id',
        'team_a_rematch_user_id',
        'team_b_rematch_user_id',
        'rematch_battle_id',
        'team_a_user_1', 'team_a_card_1', 'team_a_user_2', 'team_a_card_2',
        'team_a_user_3', 'team_a_card_3', 'team_a_user_4', 'team_a_card_4',
        'team_a_user_5', 'team_a_card_5', 'team_a_user_6', 'team_a_card_6',
        'team_b_user_1', 'team_b_card_1', 'team_b_user_2', 'team_b_card_2',
        'team_b_user_3', 'team_b_card_3', 'team_b_user_4', 'team_b_card_4',
        'team_b_user_5', 'team_b_card_5', 'team_b_user_6', 'team_b_card_6',
    ];

    protected $casts = [
        'team_b_ready' => 'boolean',
        'team_a_cancel_flag' => 'boolean',
        'team_b_cancel_flag' => 'boolean',
        'marshall_cancel_flag' => 'boolean',
        'team_a_card_data' => 'array',
        'team_b_card_data' => 'array',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'room_slug';
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($battle) {
            if (empty($battle->room_slug)) {
                $battle->room_slug = \Illuminate\Support\Str::random(10);
            }
        });
    }

    public function gameTitle()
    {
        return $this->belongsTo(GameTitle::class);
    }

    public function marshall()
    {
        return $this->belongsTo(User::class, 'marshall_id');
    }

    public function teamAMarshallElect()
    {
        return $this->belongsTo(User::class, 'team_a_marshall_elect');
    }

    public function teamBMarshallElect()
    {
        return $this->belongsTo(User::class, 'team_b_marshall_elect');
    }

    /**
     * Check if all player slots are filled
     */
    public function getIsFullAttribute()
    {
        for ($i = 1; $i <= $this->no_players_per_team; $i++) {
            if (empty($this->{"team_a_user_{$i}"}) || empty($this->{"team_b_user_{$i}"})) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if Team B slots are filled
     */
    public function getIsTeamBFullAttribute()
    {
        for ($i = 1; $i <= $this->no_players_per_team; $i++) {
            if (empty($this->{"team_b_user_{$i}"})) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get Team A Leader
     */
    public function getTeamALeaderAttribute()
    {
        return User::find($this->team_a_user_1);
    }

    /**
     * Get Team B Leader
     */
    public function getTeamBLeaderAttribute()
    {
        return User::find($this->team_b_user_1);
    }
}
