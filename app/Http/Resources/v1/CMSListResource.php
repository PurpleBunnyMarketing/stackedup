<?php

namespace App\Http\Resources\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CMSListResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
         return $this->map(function($page){
            return [
                'id'            => $page->id ?? "",
                'title'         => $page->title ?? "",
                'slug'          => $page->slug ?? "",
                'description'   => $page->description ?? "",
               
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
