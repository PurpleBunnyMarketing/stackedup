<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserProfileDetail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $expiry_date = '';
        if($this->payments->isNotEmpty()){
             $expiry_date =  $this->payments[0]->end_date;
        }
        return [
            'id'                    =>  $this->custom_id ?? "",
            'full_name'             =>  $this->full_name ?? "",
            'email'                 =>  $this->email ?? "",
            'phone_code'            =>  $this->phone_code ?? "",
            'mobile_no'             =>  $this->mobile_no ?? "",
            'is_active'             =>  $this->is_active ?? "",
            'type'                  =>  $this->type ?? "",
            'expiry_date'           =>  $expiry_date  ?? "",
            'profile_photo'         =>  generateURL($this->profile_photo ?? ""),
            'media' => [
                'facebook' =>  FacebookPageResource::collection($this->facebookPages),
                'linkedin'  => LinkedinPageResource::collection($this->linkedinPages),
                'twitter' => TwitterPageResource::collection($this->twitterPages)
            ]
        ];
    }
    public function with($request)
    {
        return [
            'meta' => [
                'api'                       =>  'v.1.0',
                'url'                       =>  url()->current(),

            ],
        ];
    }
}
