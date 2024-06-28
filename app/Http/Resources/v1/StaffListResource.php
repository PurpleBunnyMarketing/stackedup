<?php

namespace App\Http\Resources\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\ResourceCollection;

class StaffListResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
         return $this->map(function($staff){
            return [
                'id'            => $staff->custom_id ?? "",
                'full_name'     => $staff->full_name ?? "",
                'email'         => $staff->email ?? "",
                'mobile_no'     => $staff->mobile_no ?? "",
                'type'          => $staff->type ?? "",
                'profile_photo' =>  generateURL($staff->profile_photo ?? ""),
               
            ];
        });
    }

    public function with($request)
    {
        return [
            'meta' => [ 
                'api' =>  'v.1.0',
                'url' =>  url()->current(),
            ],
        ];
    }
}
