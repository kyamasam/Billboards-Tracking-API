<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable =[
        'total_animation_cost',
        'total_campaign_cost',
        'final_cost',
        'start_date',
        'end_date',
    ];
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
