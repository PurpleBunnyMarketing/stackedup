<?php

namespace App\Http\Resources\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;

class PostListResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return $this->map(function ($post) {
            // dd(!empty($post->schedule_date_time));
            $files = $post->upload_file ? collect($post->upload_file)->toArray() : $post->images->pluck('upload_image_file')->toArray();
            // dump($files);
            return [
                'id'            => $post->custom_id ?? "",
                'caption'       => $post->caption ?? "",
                'hashtag'       => $post->hashtag ?? "",
                // 'created_at'    =>  !empty($post->schedule_date_time) ? Carbon::parse($post->schedule_date_time ?? "")->format('d M Y, h:iA') : Carbon::parse($post->created_at ?? "")->format('M d Y, h:iA'),
                'created_at'    =>  !empty($post->schedule_date_time) ? Carbon::parse($post->schedule_date_time ?? "")->format('d M Y, h:iA') : convertAppUTCtoLocal($post->created_at, request("timezone", "Australia/Brisbane")),
                'upload_file'   =>  array_map(fn ($file) =>  generateURL($file), $files),
                'thumbnail'     =>  $post->thumbnail ? collect(generateURL($post->thumbnail ?? ""))->toArray() : [],
                'extension'     =>  \File::extension(generateURL($files[0] ?? "")),
                'is_call_to_action_post' => $post->is_call_to_action == 'y' ? true : false,
                'call_to_action_type' => $post->call_to_action_type ?? '',
                'call_to_action_link' => $post->action_link ?? '',
                'user'          =>  [
                    'id'            =>  $post->user->custom_id ?? "",
                    'full_name'     =>  $post->user->full_name ?? "",
                ],
                'media'         => $this->mediaList($post) ?? "",
                'mediaList'         => $post->postMediaList->map(function ($list) {
                    return [
                        'id'            => $list->mediaPage->custom_id ?? "",
                        'page_name'     => $list->mediaPage->page_name ?? "",
                        'page_id'       => $list->mediaPage->page_id ?? "",
                        'media'         => [
                            'id'            => $list->mediaPage->media->custom_id ?? "",
                            'name'          => $list->mediaPage->media->name ?? "",
                            'image_url'     => $list->mediaPage->media->image_url ?? "",
                        ],
                        'is_failed' => $list->mediaPage->is_error ? 'y' : 'n',
                        'error_message' => $list->mediaPage->error_message ?? "",
                    ];
                }),

            ];
        });
    }
    public function mediaList($post)
    {

        $mediaList = [];
        $media_id = [];
        foreach ($post->postMedia as $media) {
            if (!in_array($media->media_id, $media_id)) {
                $media_id[] = $media->media_id ?? "";
                $mediaList[] = [
                    'id'            => $media->media->custom_id ?? "",
                    'name'          => $media->media->name ?? "",
                    'image_url'     => $media->media->image_url ?? "",
                ];
            }
        }

        return $mediaList;
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
