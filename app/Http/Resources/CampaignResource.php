<?php

namespace App\Http\Resources;

use App\Budget;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
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
            "type"=>'campaign',
            "id"=>$this->id,
            "campaign_name"=> $this->campaign_name,
            "campaign_description"=> $this->campaign_description,
        ];
    }

    public function with($request)
    {
        if(isset($this->budget)){
            $budget = new BudgetResource($this->budget);
        }
        else{
            $budget=[];
        }
        if(isset($this->artwork)){
            $artwork = ArtworkResource::collection($this->artwork);
        }
        else{
            $artwork=[];
        }
        if(isset($this->CampaignStatus)){
            $campaign_status = new CampaignStatusResource($this->CampaignStatus);
        }
        else{
            $campaign_status=[];
        }
        if(isset($this->Schedule)){
            $schedule = new ScheduleResource($this->Schedule);
        }
        else{
            $schedule=[];
        }
        if(isset($this->Billboards)){
            $billboards= new BillboardCollection($this->Billboards);
        }
        else{
            $billboards=[];
        }
        return [
            'related' => [
                'owner' => new UserResource($this->owner),
                'budget' => $budget,
                'campaign_status' => $campaign_status,
                'schedule' => $schedule,
                'artwork' => $artwork,
                'billboards' => $billboards
            ],
        ];
    }


}
