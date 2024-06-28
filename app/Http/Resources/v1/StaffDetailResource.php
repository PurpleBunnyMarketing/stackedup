<?php

namespace App\Http\Resources\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class StaffDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
         return [
            'id'                =>  $this->custom_id ?? "",
            'full_name'         =>  $this->full_name ?? "",
            'email'             =>  $this->email ?? "",
            'phone_code'        =>  $this->phone_code ?? "",
            'mobile_no'         =>  $this->mobile_no ?? "",
            'is_active'         =>  $this->is_active ?? "",
            'type'              =>  $this->type ?? "",
            'profile_photo'     =>  generateURL($this->profile_photo ?? ""),
            'posts'             =>  $this->posts->map(function($post){
                return [
                    'id'            => $post->custom_id ?? "",
                    'caption'       => $post->caption ?? "",
                    'hashtag'       => $post->hashtag ?? "",
                    // 'created_at'    =>  Carbon::parse($post->created_at ?? "")->format('d M Y, h:iA'),
                    'created_at'    =>  convertAppUTCtoLocal($post->created_at, request("timezone", "Australia/Brisbane")),
                    'upload_file'   =>  generateURL($post->upload_file ?? ""),
                    'thumbnail'     =>  generateURL($post->thumbnail ?? ""),
                    'extension'     =>  \File::extension(generateURL($post->upload_file ?? "")),
                    'user'          =>  [
                        'id'            =>  $post->user->custom_id ?? "",
                        'full_name'     =>  $post->user->full_name ?? "",
                    ],
                ];
            }),
        ];
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
