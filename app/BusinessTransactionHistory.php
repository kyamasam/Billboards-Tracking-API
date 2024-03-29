<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessTransactionHistory extends Model
{
    protected $fillable =[
        'transaction_provider',
        'confirmation_receipt',
    ];

    /**
     * Payment Providers to who facilitated this transaction
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function PaymentProvider(){
        return $this->belongsTo(PaymentProvider::class);
    }
}
