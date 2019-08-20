<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhoneNumberVerification extends Model
{
    protected $fillable=[
        'verification_code',
        'phone_number','used'];
}
