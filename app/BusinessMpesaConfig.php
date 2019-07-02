<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessMpesaConfig extends Model
{
    protected $fillable =[
        'mpesa_short_code',
        'initiator_name',
        'msisdn',
        'lipa_na_mpesa_online_shortcode',
        'lipa_na_mpesa_online_passkey  ',
    ];

    protected $hidden =[
        'mpesa_consumer_key',
        'mpesa_consumer_secret',
        'initiator_password  ',
    ];
}
