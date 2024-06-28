<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaPagesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $isPageMediaTokenExpired = checkMediaPageTokenExpiry($this->social_media_detail_id);
        return [
            'page_unique_id' => (string)$this->id,
            'id'            => $this->custom_id ?? "",
            'page_id'       => $this->page_id ?? "",
            'page_name'     => $this->page_name ?? "",
            'media_image_url'     => $this->media->image_url ?? "",
            'image_url' =>      $this->image_url ?? "",
            'is_token_expired'     => $isPageMediaTokenExpired,
            'token_expiry_message'     => $isPageMediaTokenExpired ? "Your {$this->media->name} Token is expired, Please Relink your {$this->media->name} Account." : '',
        ];
    }
}
