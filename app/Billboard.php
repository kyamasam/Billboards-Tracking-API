<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Billboard extends Model
{
    /**
     * get the campaigns running on this Billboard
     *
     */
    public function Campaigns(){
        return $this->belongsToMany(BillboardCampaign::class , 'billboard_campaigns', 'billboard_id', 'campaign_id');
    }
}
