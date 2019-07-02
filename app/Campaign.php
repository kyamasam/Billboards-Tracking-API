<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
            'campaign_name',
            'budget_id',
            'schedule_id',
            'campaign_status',
            'owner_id',
        ];
}
