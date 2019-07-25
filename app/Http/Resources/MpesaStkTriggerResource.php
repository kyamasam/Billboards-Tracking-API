<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MpesaStkTriggerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return
            [
                "type"=>'mpesa_stk_trigger',
                "id"=>$this->id,
                "attributes"=> parent::toArray($request)
            ];
    }
}
