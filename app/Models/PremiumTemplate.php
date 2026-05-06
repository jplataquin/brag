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
