<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArtworkResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "type"=>'Artwork',
            "id"=>$this->id,
            "attributes"=>[
                "id"=>$this->id,
                "height"=>$this->height,
                "campaign_id"=>$this->campaign_id, //left for redundancy
                "billboard_id"=>$this->billboard_id, //left for redundancy
                "width"=>$this->width,
                "image_src"=>$this->image_src,
            ]


        ];
    }

    public function with($request)
    {
        return [
            'related' => [
                'Campaigns' => new CampaignResource($this->Campaigns),
                'Billboards' => new BillboardResource($this->Billboards),

            ],
        ];
    }
}
