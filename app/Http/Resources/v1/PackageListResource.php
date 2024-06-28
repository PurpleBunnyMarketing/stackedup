<?php

namespace App\Http\Resources\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PackageListResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
         return $this->map(function($package){
            return [
                'id'            => $package->custom_id ?? "",
                'package_type'  => $package->package_type ?? "",
                'amount'        => $package->amount ?? "",
                'description'   => $package->description ?? "",
               
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
