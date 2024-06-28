<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FacebookPageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // dd($this);
        return [
            'id'                => $this->media->custom_id ?? "",
            'page_id'           => $this->mediaPage->custom_id ?? "",
            'name'              => $this->mediaPage->page_name ?? "",
            'image_url'         => $this->media->image_url ?? "",
        ];
    }
}
