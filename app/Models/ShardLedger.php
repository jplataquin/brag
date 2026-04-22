<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShardLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'debit',
        'credit',
        'type',
        'from_id',
        'transfer_id',
        'remarks',
    ];

    /**
     * The user this ledger entry belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The user who sent the shards (if applicable).
     */
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_id');
    }

    /**
     * The user who received the shards (if applicable).
     */
    public function transferUser()
    {
        return $this->belongsTo(User::class, 'transfer_id');
    }
}
