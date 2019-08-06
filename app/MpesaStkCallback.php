<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MpesaStkCallback extends Model
{

    public function User(){
        $this->belongsTo(User::class, 'user_id');
    }

    protected $fillable =[
        'resultCode',
        'resultDesc',
        'merchantRequestID',
        'checkoutRequestID',
        'amount',
        'mpesaReceiptNumber',
        'phoneNumber',
        'receipt',
        'user_id'
    ];

}
