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
        ];
    }

    public function with($request)
    {
        //check that the following are actually set

//        $budget = Budget::find()
//
//        schedule_id
//        campaign_status
        return [
            'related' => [
                'owner' => new UserResource($this->owner),
                'budget' => new BudgetResource($this->budget),
                'campaign_status' => new CampaignStatusResource($this->CampaignStatus),
                'schedule' => new ScheduleResource($this->Schedule)
            ],
        ];
    }


}
