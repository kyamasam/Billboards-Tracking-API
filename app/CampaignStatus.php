<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampaignStatus extends Model
{
    protected $fillable =['name', 'description',];

    /**
     * get the Campaigns associated with this CampaignStatus
     */
    public function Campaign(){
        return $this->hasMany(Campaign::class);
    }
}
