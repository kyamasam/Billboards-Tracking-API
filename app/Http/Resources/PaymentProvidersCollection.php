<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PaymentProvidersCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return
            [
                "type"=>'payment_providers',
                "id"=>$this->id,
                "attributes"=> parent::toArray($request)
            ];
    }
}
