<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable =['schedule_day' ,'schedule_time'];


    /**
     * get the Campaign associated with this schedule
     */
    public function Campaign(){
        return $this->hasOne(Campaign::class);
    }

}
