<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessTransactionHistory extends Model
{
    protected $fillable =[
        'transaction_provider',
        'confirmation_receipt',
    ];
}
