<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'card_title',
        'game_title_id',
        'quote',
        'photo',
        'ai_photo',
        'image_position_y',
        'background_color',
        'border_color',
        'section_color',
        'primary_text_color',
        'secondary_text_color',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($template) {
            $template->digitalCards()->update(['status' => 'Discontinued']);
        });

        static::restored(function ($template) {
            $template->digitalCards()->update(['status' => 'Maintained']);
        });
    }

    /**
     * The user who created this template.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The game title of this template.
     */
    public function gameTitle()
    {
        return $this->belongsTo(GameTitle::class);
    }

    /**
     * Digital cards forged from this template.
     */
    public function digitalCards()
    {
        return $this->hasMany(DigitalCard::class);
    }

    /**
     * Get the display photo (AI-enhanced if available, else original).
     */
    public function getDisplayPhotoAttribute()
    {
        if ($this->ai_photo) {
            return asset('storage/' . $this->ai_photo);
        }
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return asset('images/default-card.png');
    }

    /**
     * Count total cards in circulation.
     */
    public function getCardsInCirculationAttribute()
    {
        return $this->digitalCards()->count();
    }

    /**
     * Get the next serial number for forging.
     */
    public function getNextSerialNumberAttribute()
    {
        return ($this->digitalCards()->max('serial_number') ?? 0) + 1;
    }
}
