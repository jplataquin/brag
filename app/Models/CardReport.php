<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'digital_card_id',
        'user_id',
        'reason',
        'notes',
        'status',
    ];

    public function digitalCard()
    {
        return $this->belongsTo(DigitalCard::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
