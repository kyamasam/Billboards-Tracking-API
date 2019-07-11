<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    /**
     * UserResource associated with budget.
     *
     */
    public function User(){
        return $this->belongsTo(User::class);
    }

    /**
     * Campaign associated with budget.
     *
     */
    public function Campaign(){
        return $this->hasOne(Campaign::class);
    }
}
