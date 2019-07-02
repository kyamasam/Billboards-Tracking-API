<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable =[
        'user_id','credit_balance'
    ];
    protected $hidden =['credit_balance_verifier'];
}
