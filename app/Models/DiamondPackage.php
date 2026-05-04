<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiamondPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'diamonds',
        'price',
        'promo_price',
        'currency',
        'qr_path',
        'ocr_match_string',
        'is_active',
        'allow_manual',
        'allow_hitpay',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allow_manual' => 'boolean',
        'allow_hitpay' => 'boolean',
        'price' => 'decimal:2',
        'promo_price' => 'decimal:2',
    ];

    /**
     * Get the final price for the package.
     */
    public function getFinalPriceAttribute()
    {
        return $this->promo_price ?? $this->price;
    }

    /**
     * Scope a query to only include active packages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
