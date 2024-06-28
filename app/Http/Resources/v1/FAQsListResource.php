<?php

namespace App\Http\Resources\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\ResourceCollection;

class FAQsListResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
         $data = $this->map(function($faqs){
            return [
                'id'            => $faqs->custom_id ?? "",
                'question'      => $faqs->question ?? "",
                'answer'        => $faqs->answer ?? "",
               
            ];
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
