<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MpesaStkTrigger extends Model
{
    protected $fillable =[
        'merchant_request_id',
        'checkout_request_id',
        'response_code',
        'response_description',
        'customer_message',
        ];
}
