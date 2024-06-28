<?php

namespace App\Http\Resources\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MediaListResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->map(function ($media) {
            return [
                'id'            => $media->custom_id ?? "",
                'name'          => $media->name ?? "",
                'image_url'     => $media->image_url ?? "",
                // 'mediaPages'    => $media->mediaPages->map(function ($page) use ($media) {
                //     return [
                //         'page_unique_id' => (string)$page->id,
                //         'id'            => $page->custom_id ?? "",
                //         'page_id'       => $page->page_id ?? "",
                //         'page_name'     => $page->page_name ?? "",
                //         'media_image_url'     => $media->image_url ?? "",
                //         'token_expired'     => '',
                //     ];
                // }),
                'mediaPages'    => MediaPagesResource::collection($media->mediaPages)
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
