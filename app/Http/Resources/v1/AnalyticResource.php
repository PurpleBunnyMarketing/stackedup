<?php

namespace App\Http\Resources\v1;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AnalyticResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = $this->map(function($analytic){
            return $analytic;
        });
        return $data ?? null;
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
