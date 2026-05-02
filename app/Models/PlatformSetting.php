<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_maintenance_mode',
        'allow_template_creation',
        'allow_card_forging',
        'allow_battle_creation'
    ];

    protected $casts = [
        'is_maintenance_mode' => 'boolean',
        'allow_template_creation' => 'boolean',
        'allow_card_forging' => 'boolean',
        'allow_battle_creation' => 'boolean',
    ];

    /**
     * Get the current platform settings.
     */
    public static function current()
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'is_maintenance_mode' => false,
                'allow_template_creation' => true,
                'allow_card_forging' => true,
                'allow_battle_creation' => true,
            ]
        );
    }
}
