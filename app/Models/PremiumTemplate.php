<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PremiumTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'game_title_id',
        'template_title',
        'price',
        'status',
        'designer_name',
        'description',
        'premium_config',
        'admin_editor_id',
    ];

    protected $casts = [
        'premium_config' => 'array',
    ];

    /**
     * Get the display photo for the premium template.
     */
    public function getDisplayPhotoAttribute()
    {
        // Try to get background image from premium_config if it exists
        if (!empty($this->premium_config['layers'])) {
            foreach ($this->premium_config['layers'] as $layer) {
                if ($layer['type'] === 'image' && !empty($layer['src'])) {
                    return $layer['src'];
                }
            }
        }
        return asset('images/default-card.png');
    }

    /**
     * The game title of this premium template.
     */
    public function gameTitle()
    {
        return $this->belongsTo(GameTitle::class);
    }

    /**
     * The admin user who last edited this template.
     */
    public function adminEditor()
    {
        return $this->belongsTo(User::class, 'admin_editor_id');
    }
}
