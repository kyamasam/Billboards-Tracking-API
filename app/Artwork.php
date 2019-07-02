<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Artwork extends Model
{
    protected $fillable = ['height', 'width', 'image_src', ];


    /**
     * get the Campaigns that are using to this artwork
     *
     */
    public function Campaigns(){
        return $this->belongsToMany(CampaignArtwork::class ,'campaign_artworks','artwork_id', 'campaign_id' );
    }
}
