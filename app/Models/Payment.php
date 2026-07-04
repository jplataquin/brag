<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'diamond_package_id',
        'manual_payment_agreement_id',
        'reference',
        'tracer_id',
        'hitpay_id',
        'amount',
        'currency',
        'diamonds_amount',
        'status',
        'payment_type',
        'payment_method',
        'proof_path',
        'fees',
        'net_amount',
        'collected_at',
        'collected_by',
        'auto_approve_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'collected_at' => 'datetime',
        'auto_approve_at' => 'datetime',
    ];

    /**
     * The user who made the payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The user who collected the payment.
     */
    public function collector()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    /**
     * The diamond package being purchased.
     */
    public function package()
    {
        return $this->belongsTo(DiamondPackage::class, 'diamond_package_id');
    }

    /**
     * The agreement the user signed for this payment.
     */
    public function agreement()
    {
        return $this->belongsTo(ManualPaymentAgreement::class, 'manual_payment_agreement_id');
    }

    /**
     * Discussion thread for this payment.
     */
    public function comments()
    {
        return $this->hasMany(PaymentComment::class);
    }

    /**
     * Check if the payment was made manually.
     */
    public function isManual()
    {
        return $this->payment_method === 'manual';
    }
}
