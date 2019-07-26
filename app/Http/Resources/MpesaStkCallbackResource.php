<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MpesaStkCallbackResource extends JsonResource
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
                "type"=>'mpesa_stk_callback',
                "id"=>$this->id,
                "attributes"=> parent::toArray($request)
            ];
    }
}
