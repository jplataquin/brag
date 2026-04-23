<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'reference',
        'hitpay_id',
        'amount',
        'currency',
        'shards_amount',
        'status',
    ];

    /**
     * The user who made the payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
