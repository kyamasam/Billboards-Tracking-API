<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
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
            "type"=>'Schedule',
            "id"=>$this->id,
            'attributes' => parent::toArray($request)
        ];
    }
    public function with($request)
    {
        return [
            'related' => [
                'ScheduleTimes' => new ScheduleTimesResource($this->ScheduleTimes),
            ],
        ];
    }
}
