<?php

namespace App\Http\Resources;

use App\AccountType;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            "type"=>'user',
            "id"=>$this->id,
            'attributes' => parent::toArray($request)
        ];
    }


    public function with($request)
    {
        //todo : fix issue where account type does not exist
        return [
            'related' => [
                'user_type' => new AccountTypeResource(AccountType::find($this->account_type)),
            ],
        ];
    }

}
