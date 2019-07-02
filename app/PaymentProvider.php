<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentProvider extends Model
{
    protected $fillable =['provider_name'];

    /**
     * The User Payment History records facilitated by this Payment provider
     *
     */
    public function UserPaymentHistory(){
        return $this->hasMany(UserPaymentHistory::class);
    }

    /**
     * Return business transaction history facilitated by this provider
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function BusinessTransactionHistory(){
        return $this->hasMany(BusinessTransactionHistory::class);
    }
}
