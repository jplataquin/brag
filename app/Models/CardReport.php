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
        'admin_notes',
        'status',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function digitalCard()
    {
        return $this->belongsTo(DigitalCard::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class); // Reporter
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
