<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserProfile extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
    //   dd('in');
        return [
                'id'                    =>  $this->custom_id ?? "",
                'full_name'             =>  $this->full_name ?? "",
                'email'                 =>  $this->email ?? "",
                'phone_code'            =>  $this->phone_code ?? "",
                'mobile_no'             =>  $this->mobile_no ?? "",
                'is_active'             =>  $this->is_active ?? "",
                'type'                  =>  $this->type ?? "",
                'profile_photo'         =>  generateURL($this->profile_photo ?? ""),
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
