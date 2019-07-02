<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPaymentHistory extends Model
{
    protected $fillable = ['provider_id','amount_paid','msisdn','user_id','confirmation_receipt'];


    /**
     * The user who owns this history
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function User(){
        return $this->belongsTo(User::class);
    }
    /**
     * The Payment provider who has facilitated this payment
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function PaymentProvider(){
        return $this->belongsTo(PaymentProvider::class);
    }
}
