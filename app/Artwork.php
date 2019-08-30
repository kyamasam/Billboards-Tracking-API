<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Artwork extends Model
{
    protected $fillable = ['height', 'width', 'image_src','campaign_id' ,'billboard_id','file_type','animate','admin_feedback','approved'];


    /**
     * get the Campaigns that are using to this artwork
     *
     */
    public function Campaigns(){
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }
    /**
     * get the Billboard that are attached to this Artwork
     *
     */
    public function Billboards(){
        return $this->belongsTo(Billboard::class, 'billboard_id');
    }

}
