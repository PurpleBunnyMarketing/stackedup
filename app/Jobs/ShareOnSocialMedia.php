<?php

namespace App\Jobs;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\Http\Controllers\PostController;
use App\Models\Media;
use App\Models\MediaPage;
use App\Models\PostMedia;
use App\Models\Post;
use App\Models\SocialMediaDetail;
use App\Services\LinkedInService;
use Exception;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\GoogleMyBusinessService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ShareOnSocialMedia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $posts;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($posts)
    {
        $this->posts = $posts;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            if ($this->posts) {
                foreach ($this->posts as $post) {

                    $post_media_ids = $post->postMedia->pluck('media_id')->toArray();
                    $medias = Media::whereIn('id', $post_media_ids)->get();
                    foreach ($medias as $media) {
                        switch ($media->name) {
                            case 'Facebook':
                                $image_type = File::extension($post->upload_file) ?? "";
                                $files = $post->upload_file ? collect($post->upload_file)->toArray() : $post->images->pluck('upload_image_file')->toArray();

                                $facebook = new Facebook([
                                    'app_id' => config('utility.FACEBOOK_APP_ID'),
                                    'app_secret' => config('utility.FACEBOOK_APP_SECRET'),
                                    'default_graph_version' => config('utility.DEFAULT_FACEBOOK_API_VERSION'),
                                ]);
                                // Get all the Media page if of the post.

                                $post_medias = PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->pluck('media_page_id')->toArray();
                                $media_pages = MediaPage::whereIn('id', $post_medias)->get();

                                foreach ($media_pages as $media_page) {
                                    try {
                                        $token = $url = '';
                                        // If Post User is staff then get the Company id otherwise get the post->user_id.
                                        $user_id = $post->user->parent_id ?? $post->user_id;
                                        // Log::info('user_id ' . $user_id);
                                        // Get Social Media Details of Post User.
                                        $socialMedia = SocialMediaDetail::where('id', $media_page->social_media_detail_id)->where('media_id', $media->id)->where('user_id', $user_id)->first();

                                        if (isset($socialMedia->id)) {
                                            // get Page list for access_token
                                            // $response = $facebook->get('/' . $socialMedia->social_id . '/accounts?fields=name,access_token', $socialMedia->token);
                                            // $statuscode = $response->getHttpStatusCode();
                                            // $facebook_pages = ($statuscode == 200) ?  $response->getGraphEdge()->asArray()  : [];
                                            // // Log::info('page_id ' . $media_page->page_id);
                                            // if ($facebook_pages) {
                                            //     foreach ($facebook_pages as $facebook_page) {
                                            //         // Log::info('page_name facebook');
                                            //         if ($facebook_page['id'] == $media_page->page_id) {
                                            //             $token =  $facebook_page['access_token'];
                                            //         }
                                            //     }
                                            // }
                                            $token = (new PostController)->getFacebookPagesToken($socialMedia, $media_page->page_id);
                                            if ($token) {
                                                $media_ids = [];
                                                $post_parameter = [];
                                                $video_parameter = [];
                                                $url = '/' . $media_page->page_id . '/feed';
                                                $parameter['message'] = str_replace("<br />", " ", nl2br($post->caption . "\n" . $post->hashtag));
                                                if ($files && count($files) > 1) {
                                                    foreach ($files as $key => $file) {
                                                        $image_type = File::extension($file) ?? "";
                                                        if ($image_type == "mp4") {
                                                            $video_parameter['source']  = $facebook->fileToUpload(Storage::disk('public')->getAdapter()->getPathPrefix() . '' . $file);
                                                            $video_parameter['description'] = str_replace("<br />", " ", nl2br($post->caption . "\n" . $post->hashtag));
                                                            $url = '/' . $media_page->page_id . '/videos';
                                                            $response = $facebook->post($url, $video_parameter, $token)->getGraphNode()->asArray();
                                                        } else {
                                                            $url = '/' . $media_page->page_id . '/photos';
                                                            $parameter = array('url' => Storage::url($file), 'published' => false);
                                                            // $parameter['url'] = Storage::url($file);
                                                            // $parameter['published']    = false;
                                                            $post_parameter['message'] = str_replace("<br />", " ", nl2br($post->caption . "\n" . $post->hashtag));
                                                            $response = $facebook->post($url, $parameter, $token)->getGraphNode()->asArray();
                                                            $post_parameter["attached_media[{$key}]"] = json_encode(['media_fbid' => $response['id']]);
                                                        }
                                                    }
                                                    if (count($post_parameter) > 1) {
                                                        $publish_url = '/' . $media_page->page_id . '/feed';
                                                        $response = $facebook->post($publish_url, $post_parameter, $token)->getGraphNode()->asArray();
                                                        Log::debug('Facebook Schedule Response ----' . json_encode($response));
                                                        if (isset($response['id'])) {
                                                        } else throw new Exception($response['error']['message']);
                                                    }
                                                } else if ($files && $files[0] !== '') {
                                                    $image_type = File::extension($files[0]) ?? "";
                                                    $parameter = [
                                                        'source'    => $facebook->fileToUpload(Storage::disk('public')->getAdapter()->getPathPrefix() . '' . $files[0]),
                                                    ];
                                                    if ($image_type == "mp4") {
                                                        $url = '/' . $media_page->page_id . '/videos';
                                                        $parameter['description'] = str_replace("<br />", " ", nl2br($post->caption . "\n" . $post->hashtag));
                                                    } else {
                                                        $url = '/' . $media_page->page_id . '/photos';
                                                        $parameter['caption'] = str_replace("<br />", " ", nl2br($post->caption . "\n" . $post->hashtag));
                                                    }
                                                    $response = $facebook->post($url, $parameter, $token)->getGraphNode()->asArray();
                                                    Log::debug('Facebook Schedule Response ----' . json_encode($response));
                                                    if (isset($response['id'])) {
                                                    } else throw new Exception($response['error']['message']);
                                                } else {
                                                    $response = $facebook->post($url, $parameter, $token)->getGraphNode()->asArray();
                                                    Log::debug('Facebook Schedule Response ----' . json_encode($response));
                                                    if (isset($response['id'])) {
                                                    } else throw new Exception($response['error']['message']);
                                                }
                                            }
                                            Log::info('===================Facebook Post Succesfully =================');
                                        }
                                    } catch (FacebookSDKException $e) {
                                        PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->where('media_page_id', $media_page->id)->update([
                                            'is_error' => 'y',
                                            'error_message' => $e->getMessage(),
                                        ]);
                                        Log::error('FacebookSDKException ' . $e->getMessage());
                                    } catch (Exception $e) {
                                        PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->where('media_page_id', $media_page->id)->update(['is_error' => 'y', 'error_message' => $e->getMessage()]);
                                        Log::error('Facebook General Error ' . $e->getMessage());
                                    }
                                }
                                break;
                            case 'Linkedin':

                                $params = $post->toArray();
                                $params['images'] = $post->upload_file ? collect($post->upload_file)->toArray() : $post->images->pluck('upload_image_file')->toArray();
                                $media_page_id = [];
                                $user_id = $post->user->parent_id ?? $post->user_id;
                                //$social_media = SocialMediaDetail::where('user_id', $user_id)->where('media_id', $media->id)->first();
                                $post_medias = PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->pluck('media_page_id')->toArray();
                                $media_pages = MediaPage::whereIn('id', $post_medias)->get();

                                if ($media_pages) {
                                    foreach ($media_pages as $val) {
                                        // $media_page_id[] = $val->page_id;
                                        try {
                                            $social_media = SocialMediaDetail::where('id', $val->social_media_detail_id)->where('user_id', $user_id)->first();
                                            if (isset($social_media->id))
                                                LinkedInService::checkPage($social_media->social_id, $social_media->token, $params, [$val->page_id]);
                                            Log::info('=====================Linkedin Post Succesfully=====================');
                                        } catch (Exception $e) {
                                            PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->where('media_page_id', $val->id)->update([
                                                'is_error' => 'y',
                                                'error_message' => $e->getMessage(),
                                            ]);
                                            Log::info('Linkedin error' . $e->getMessage());
                                            //flash('Please try after sometime to share the post on the linkedin')->error();
                                        }
                                    }
                                }

                                break;
                            case 'X(Twitter)':
                                $user_id = $post->user->parent_id ?? $post->user_id;
                                $post_medias = PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->pluck('media_page_id')->toArray();
                                $media_pages = MediaPage::whereIn('id', $post_medias)->get();
                                foreach ($media_pages as $media_page) {
                                    try {
                                        $socialMedia = SocialMediaDetail::where('media_id', $media->id)->where('id', $media_page->social_media_detail_id)->where('user_id', $user_id)->first();
                                        if (isset($socialMedia->id)) {


                                            $files = $post->upload_file ? collect($post->upload_file)->toArray() : $post->images->pluck('upload_image_file')->toArray();

                                            $connection = new TwitterOAuth(config('utility.TWITTER_CONSUMER_KEY'), config('utility.TWITTER_CONSUMER_SECRET'), $socialMedia->token, $socialMedia->token_secret);

                                            $parameters = ['text' => str_replace("<br />", " ", nl2br($post->caption . "\n" . $post->hashtag)),];
                                            if ($files && count($files) > 1) {
                                                foreach ($files as $file) {
                                                    $image_type = $file ? File::extension($file) : "";
                                                    if ($image_type == "mp4") {
                                                        $video_upload = $connection->upload('media/upload', array('media' => Storage::disk('public')->getAdapter()->getPathPrefix() . '' . $file, 'media_type' => 'video/mp4', 'media_category' => 'tweet_video'), true);

                                                        // Check Media Publication Status
                                                        $status = 'process';
                                                        while ($status == 'process') {
                                                            $media2 = $connection->mediaStatus($video_upload->media_id_string);
                                                            if ($media2->processing_info->state == 'succeeded') {
                                                                $status = 'success';
                                                            } else if (isset($media2->processing_info->error)) {
                                                                break;
                                                            }
                                                        }
                                                        $media_ids[] = $video_upload->media_id_string;
                                                    } else {
                                                        $media_upload = $connection->upload('media/upload', ['media' => Storage::disk('public')->getAdapter()->getPathPrefix() . '' . $file]);
                                                        $media_ids[] = $media_upload->media_id_string;
                                                    }
                                                }
                                                if (count($media_ids) > 0) {
                                                    // $parameters['media_ids'] = implode(',', $media_ids);
                                                    $parameters['media']['media_ids'] = $media_ids;
                                                }
                                            } else if ($files && count($files) == 1) {
                                                $image_type = File::extension($files[0]) ?? "";
                                                if ($image_type == "mp4") {
                                                    $single_video_upload = $connection->upload('media/upload', array('media' => Storage::disk('public')->getAdapter()->getPathPrefix() . '' . $files[0], 'media_type' => 'video/mp4', 'media_category' => 'tweet_video'), true);
                                                    // Check Media Publication Status
                                                    $status = 'process';
                                                    while ($status == 'process') {
                                                        $video_upload_status = $connection->mediaStatus($single_video_upload->media_id_string);
                                                        if ($video_upload_status->processing_info->state == 'succeeded') {
                                                            $status = 'success';
                                                        } else if (isset($video_upload_status->processing_info->error)) {
                                                            break;
                                                        }
                                                    }
                                                    $media_ids[] = $single_video_upload->media_id_string;
                                                } else {
                                                    $media1 = $connection->upload('media/upload', ['media' => Storage::disk('public')->getAdapter()->getPathPrefix() . '' . $files[0]]);
                                                    $media_ids[] = $media1->media_id_string;
                                                }
                                                if (count($media_ids) > 0) {
                                                    // $parameters['media_ids'] = implode(',', $media_ids);
                                                    $parameters['media']['media_ids'] = $media_ids;
                                                }
                                            }
                                            if ($parameters) {
                                                $connection->setApiVersion('2');
                                                $result = $connection->post('tweets', $parameters, true);
                                                Log::debug('Twitter Schedule Response ---- ' . json_encode($result));
                                                if (isset($result->errors) && $result->errors[0]->code === 89) throw new Exception('Your X(Twitter) Token is Expired, You need to Reconnect your X(Twitter) Account For Post in X(Twitter).', 89);
                                            }
                                            Log::info('==============Schedule Post Added In X(Twitter)=======');
                                        }
                                    } catch (\Exception $ex) {
                                        PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->where('media_page_id', $media_page->id)->update([
                                            'is_error' => 'y',
                                            'error_message' => $ex->getMessage(),
                                        ]);
                                        Log::error('X(Twitter) Schedule Error ' . $ex->getMessage());
                                    }
                                }
                                break;
                            case 'Instagram':

                                $params = $post->toArray();
                                $media_page_id = [];
                                $user_id =  $post->user->parent_id ?? $post->user_id;
                                // $socialMediaData = SocialMediaDetail::where('user_id', $user_id)->where('media_id', $media->id)->first();

                                $post_medias = PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->pluck('media_page_id')->toArray();

                                $media_pages = MediaPage::whereIn('id', $post_medias)->select('page_id', 'social_media_detail_id')->get();
                                $image_type = File::extension($post->upload_file) ?? "";
                                $files = $post->upload_file ? collect($post->upload_file)->toArray() : $post->images->pluck('upload_image_file')->toArray();
                                $caption = str_replace("<br />", " ", nl2br($post->caption . "\n" . $post->hashtag));


                                if ($media_pages) {
                                    foreach ($media_pages as $val) {
                                        $socialMediaData = SocialMediaDetail::where('user_id', $user_id)->where('media_id', $media->id)->where('id', $val->social_media_detail_id)->first();
                                        $page_ids = [];
                                        if (count($files) > 1) {
                                            foreach ($files as $file) {
                                                //  else {
                                                $ch = curl_init();
                                                curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v18.0/" . $val->page_id . "/media");
                                                curl_setopt($ch, CURLOPT_POST, 1);
                                                curl_setopt(
                                                    $ch,
                                                    CURLOPT_POSTFIELDS,
                                                    "image_url=" . Storage::url($file) . "&access_token=" . $socialMediaData->token . "&caption=" . $caption,
                                                );

                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                $server_output = curl_exec($ch);
                                                curl_close($ch);
                                                $mediaRes = json_decode($server_output, true);
                                                if (isset($mediaRes['id'])) $page_ids[] = $mediaRes['id'];
                                            }
                                        } else {
                                            $image_type = File::extension($files[0]) ?? "";
                                            if ($image_type == "mp4") {
                                                $ch = curl_init();
                                                curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v18.0/" . $val->page_id . "/media");
                                                curl_setopt($ch, CURLOPT_POST, 1);
                                                curl_setopt(
                                                    $ch,
                                                    CURLOPT_POSTFIELDS,
                                                    "media_type=VIDEO&video_url=" . Storage::url($files[0]) . "&access_token=" . $socialMediaData->token . "&caption=" . $caption
                                                );

                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                $server_output = curl_exec($ch);
                                                curl_close($ch);
                                                $mediaRes = json_decode($server_output, true);
                                                $page_ids[] = $mediaRes['id'];
                                                $status = 'PROCESSING';
                                                while ($status == 'PROCESSING') {
                                                    $ch = curl_init();
                                                    curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v18.0/" . $mediaRes['id'] . "?fields=status_code&access_token=" . $socialMediaData->token);
                                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                    $server_output = curl_exec($ch);
                                                    curl_close($ch);
                                                    $statusResponse = json_decode($server_output, true);
                                                    if ($statusResponse['status_code'] == 'FINISHED' || $statusResponse['status_code'] == 'ERROR') {
                                                        break;
                                                    }
                                                }
                                            } else {
                                                $ch = curl_init();
                                                curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v18.0/" . $val->page_id . "/media");
                                                curl_setopt($ch, CURLOPT_POST, 1);
                                                curl_setopt(
                                                    $ch,
                                                    CURLOPT_POSTFIELDS,
                                                    "image_url=" . Storage::url($files[0]) . "&access_token=" . $socialMediaData->token . "&caption=" . $caption,
                                                );

                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                $server_output = curl_exec($ch);
                                                curl_close($ch);
                                                $mediaRes = json_decode($server_output, true);
                                                if (isset($mediaRes['id'])) $page_ids[] = $mediaRes['id'];
                                            }
                                        }

                                        try {
                                            if ($page_ids !== null) {
                                                $page_ids = implode(',', $page_ids);
                                                $creationId = $page_ids;
                                                if (count($files) > 1) {
                                                    // Create Courosal container
                                                    $ch = curl_init();
                                                    curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v18.0/" . $val->page_id . "/media");
                                                    curl_setopt($ch, CURLOPT_POST, 1);
                                                    curl_setopt(
                                                        $ch,
                                                        CURLOPT_POSTFIELDS,
                                                        "children={$page_ids}&media_type=CAROUSEL&access_token=" . $socialMediaData->token . "&caption=" . $caption
                                                    );

                                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                    $server_output = curl_exec($ch);
                                                    curl_close($ch);
                                                    $response = json_decode($server_output, true);
                                                    $creationId = $response['id'];
                                                }

                                                // Publish Media
                                                $ch = curl_init();
                                                curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v18.0/" . $val->page_id . "/media_publish");
                                                curl_setopt($ch, CURLOPT_POST, 1);
                                                curl_setopt(
                                                    $ch,
                                                    CURLOPT_POSTFIELDS,
                                                    "creation_id={$creationId}&access_token=" . $socialMediaData->token . "&caption=" . $caption
                                                );

                                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                                $server_output = curl_exec($ch);
                                                curl_close($ch);
                                                $contentRes = json_decode($server_output, true);
                                                Log::debug('Schedule Instagram response  -----   ' . json_encode($contentRes));
                                                if (!isset($contentRes['id']) && isset($contentRes['error'])) {
                                                    throw new Exception($contentRes['error']['message']);
                                                }
                                            }
                                            Log::info('=======================Schedule Instagram Post Successfully================================');
                                        } catch (Exception $e) {
                                            PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->where('media_page_id', $val->id)->update([
                                                'is_error' => 'y',
                                                'error_message' => $e->getMessage(),
                                            ]);
                                            Log::info('Instagram ' . $e->getMessage());
                                        }
                                    }
                                }
                                break;
                            case 'Google My Business':
                                $params = $post->toArray();
                                $user_id = $user_id =  $post->user->parent_id ?? $post->user_id;

                                // $socialMediaData = SocialMediaDetail::where('user_id', $user_id)->where('media_id', $media->id)->first();
                                $post_medias = PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->pluck('media_page_id')->toArray();
                                $media_pages = MediaPage::whereIn('id', $post_medias)->select('page_id', 'id', 'social_media_detail_id')->get();
                                $files = $post->upload_file ? collect($post->upload_file)->toArray() : $post->images->pluck('upload_image_file')->toArray();

                                if ($media_pages) {
                                    $payload = [
                                        'summary' => str_replace("<br />", " ", nl2br($post->caption ?? '' . "\n" . $post->hashtag ?? '')),
                                        "topicType" => "STANDARD",
                                        "callToAction" => [
                                            'actionType' => $post->call_to_action_type,
                                            "url" =>  $post->action_link ?? ''
                                        ],
                                    ];
                                    if ($post->call_to_action_type == 'ACTION_TYPE_UNSPECIFIED') {
                                        unset($payload['callToAction']);
                                    }
                                    if ($post->call_to_action_type == 'CALL' || $post->call_to_action_type == 'ACTION_TYPE_UNSPECIFIED') {
                                        unset($payload['callToAction']['url']);
                                    }
                                    foreach ($media_pages as $media_page) {
                                        new GoogleMyBusinessService($media_page->social_media_detail_id);
                                        $socialMediaData = SocialMediaDetail::where('user_id', $user_id)->where('media_id', $media->id)->where('id', $media_page->social_media_detail_id)->first();

                                        $uploadeble_medias = [];
                                        $url = 'https://mybusiness.googleapis.com/v4/accounts/' . $socialMediaData->social_id . '/' . $media_page->page_id . '/localPosts';

                                        if ($files) {
                                            if (count($files) > 1) {
                                                foreach ($files as $file) {
                                                    $uploadeble_medias[] = ['mediaFormat' => 'PHOTO', 'sourceUrl' => Storage::url($file)];
                                                }
                                            } else if ($files[0] !== '') {
                                                $image_type = File::extension($files[0]) ?? "";
                                                if ($image_type == "mp4") {
                                                    $uploadeble_medias = ['mediaFormat' => 'VIDEO', 'sourceUrl' => Storage::url($files[0])];
                                                } else {
                                                    $uploadeble_medias = ['mediaFormat' => 'PHOTO', 'sourceUrl' => Storage::url($files[0])];
                                                }
                                            }
                                            $payload['media'] = $uploadeble_medias;
                                        }
                                        try {
                                            $response = Http::withHeaders(['Authorization' => 'Bearer ' . $socialMediaData->token, "Accept" => 'application/json'])->post($url, $payload);
                                            if ($response->failed()) $response->throw();
                                        } catch (Exception $e) {
                                            PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->where('media_page_id', $media_page->id)->update([
                                                'is_error' => 'y',
                                                'error_message' => $e->getMessage(),
                                            ]);
                                            Log::info('Google Business ' . $e->getMessage());
                                            // flash('Please try after sometime to share the post on the Google Business Profile')->error();
                                        }
                                    }
                                }
                                break;
                        }
                    }
                    $post->schedule_date = null;
                    $post->schedule_time = null;
                    $post->schedule_date_time = null;
                    $post->created_at = \Carbon\Carbon::now();
                    $post->save();
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('General Exception ' . $th->getMessage());
        }
    }
}
