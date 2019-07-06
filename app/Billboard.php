<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Billboard extends Model
{
    use SoftDeletes;
    protected $fillable =[
        'display_duration',
        'location_lat',
        'location_long',
        'placement',
        'billboard_picture',
        'average_daily_views',
        'definition',
        'dimensions_width',
        'dimensions_height',
        'description',
    ];
    /**
     * get the campaigns running on this Billboard
     *
     */
    public function Campaigns(){
        return $this->belongsToMany(BillboardCampaign::class , 'billboard_campaigns', 'billboard_id', 'campaign_id');
    }
}
