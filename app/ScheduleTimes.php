<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleTimes extends Model
{
    protected $fillable =['start_time','end_time','days','number_of_clouts','total_cost',];

    /**
     * get the schedule where each of these belongs
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function Schedule(){
        return $this->belongsTo(Schedule::class);
    }
}
