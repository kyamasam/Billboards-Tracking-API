<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountStatus extends Model
{
    protected $fillable = [ 'status_name', 'description '];
}
