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

    /**
     * get the User who owns this Campaign
     *
     */
    public function Owner(){
        return $this->belongsTo(User::class);
    }
    /**
     * get the Budget associated with this Campaign
     *
     */
    public function Budget(){
        return $this->hasOne(Budget::class);
    }

    /**
     * get the schedule associated with this campaign
     */
    public function Schedule(){
        return $this->hasOne(Schedule::class);
    }
    /**
     * get the Campaign Status
     */
    public function CampaignStatus(){
        return $this->belongsTo(CampaignStatus::class);
    }


    /**
     * get the Billboards running this Campaign
     *
     */
    public function Billboards(){
        return $this->belongsToMany(BillboardCampaign::class ,'billboard_campaigns','campaign_id', 'billboard_id' );
    }
    /**
     * get the Artwork that belongs to this Campaign
     *
     */
    public function Artwork(){
        return $this->belongsToMany(CampaignArtwork::class ,'campaign_artworks','campaign_id', 'artwork_id' );
    }



}
