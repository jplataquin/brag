<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualPaymentAgreement extends Model
{
    use HasFactory;

    protected $fillable = ['content'];

    /**
     * Payments that agreed to this version of terms.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'manual_payment_agreement_id');
    }
}
