<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\PostListResource;
use App\Models\Media;
use App\Models\MediaPage;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostMedia;
use App\Models\SocialMediaDetail;
use App\Rules\MediaPageExists;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\GoogleMyBusinessService;
use Illuminate\Support\Facades\Storage;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Http\Controllers\PostController as ControllersPostController;
use App\Rules\MediaPagePermitted;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Illuminate\Support\Facades\File;
use App\Services\LinkedInService;
use Exception;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use FFMpeg;
use FFMpeg\Format\Video\WebM;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    private $version = "v.1.0";
    public function getVersion()
    {
        return $this->version;
    }

    /* Add Post API */
    public function addPost(Request $request)
    {
        ini_set('max_execution_time', 2400);
        ini_set('set_time_limit', 0);
        ini_set('memory_limit', '-1');
        $user = $request->user();
        $rules = [
            'images'       => 'nullable',
            'images.*'       => 'file|mimes:jpeg,png,jpg,mp4,mov,application/x-mpegURL',
            'thumbnail'         => 'sometimes|nullable|mimes:jpeg,png,jpg',
            'caption'           => 'required',
            'hashtag'           => 'nullable|sometimes',
            'schedule_date'     => 'sometimes|nullable|date|date_format:Y-m-d',
            'schedule_time'     => 'required_with:schedule_date|nullable|date_format:g:i A',
            'media_page_id'     => ['required', new MediaPageExists(), new MediaPagePermitted()],
            'call_to_action_type' => 'nullable|sometimes|in:ACTION_TYPE_UNSPECIFIED,BOOK,ORDER,SHOP,LEARN_MORE,CALL',
            // 'call_to_action_link' => 'required_unless:call_to_action_type,ACTION_TYPE_UNSPECIFIED,CALL'
        ];
        if ($this->apiValidator($request->all(), $rules)) {
            try {
                $request['is_active'] = 'y';
                $request['user_id'] = $user->id ?? "";
                $request['custom_id'] = getUniqueString('posts');
                if (!empty($request->schedule_time)) {
                    $request['schedule_time'] = \Carbon\Carbon::createFromFormat('h:i A', $request->schedule_time)->format('H:i:s');
                }
                if (!empty($request->schedule_date)) {
                    $request['schedule_date'] = \Carbon\Carbon::createFromFormat('Y-m-d', $request->schedule_date)->format('Y-m-d');
                }
                if (!empty($request->schedule_date) || !empty($request->schedule_time)) {
                    //before timezone
                    // $request['schedule_date_time'] = $request['schedule_date'] . ' ' . $request['schedule_time'];
                    $timeZone =  $request->has('timezone') ? $request->timezone : "Australia/Brisbane";
                    $dateTime = $request['schedule_date'] . ' ' . $request['schedule_time'];
                    $request['schedule_date_time'] = convertUTC($dateTime, $timeZone);
                }

                // Fields for Google
                if ($request->call_to_action_type !== '') {
                    $request['is_call_to_action'] = 'y';
                    $request['call_to_action_type'] = $request->call_to_action_type;
                    $request['action_link'] = $request->call_to_action_link;
                }

                $post = Post::create($request->all());

                if ($request->has('images')) {
                    $upload_files = $request->file('images');
                    if (count($upload_files) > 1) {
                        foreach ($upload_files as $file) {
                            $file_path  = $file->store('upload_file');
                            PostImage::create(['post_id' => $post->id, 'upload_image_file' => $file_path]);
                        }
                    } else {
                        $file = $upload_files[0]->store('upload_file');
                        $extension = File::extension($file);
                        if ($extension == "mp4") {
                            $post->upload_file = $file;
                        } elseif ($extension == 'mov') {
                            // if ($extension == 'mov') {
                            $file_path = storage_path() . '/app/public/' . $file;

                            // $destinationPath = storage_path().'/app/public/'.$file_path;

                            $destinationPath = str_replace(".mov", ".mp4", $file_path);

                            exec("echo 'SvTS9iQusP7SRPh@YMVDV' | sudo -kS chmod -R 777 /var/www/html/storage/app/public/");
                            $command = "/usr/bin/ffmpeg -i '" . $file_path . "' -c:v libx264 -profile:v main -vf format=yuv420p -c:a aac -movflags +faststart " . $destinationPath . " -y 2>&1";


                            $cmd =  exec("/usr/bin/ffmpeg -i '" . $file_path . "' -c:v libx264 -profile:v main -vf format=yuv420p -c:a aac -movflags +faststart " . $destinationPath . " -y 2>&1");

                            $response = [
                                'status'    =>  'success',
                                'message'   =>  $cmd,
                                'response'  =>  [
                                    'code'  =>  200,
                                    'file'  =>  $destinationPath,
                                ],
                            ];

                            // Log::info('RES MOVE FILE : ' .$destinationPath);
                            $file = explode('/', $file_path);

                            $destinationPath = explode('/', $destinationPath);

                            $storedFilePath = $file[8] . '/' . $file[9];
                            $storedDestinationPath = $destinationPath[9];

                            // $s3Data = $this->moveFielToS3($storedFilePath, $storedDestinationPath);
                            // Log::info('S3 : '.$s3Data);

                            // store to s3 bucket
                            $publicPath = $storedFilePath;
                            $data = explode('/', $storedFilePath);
                            array_shift($data);

                            // $destination = $storedDestinationPath ?? implode('/', $data);
                            Log::info('SOURCE FILE : ' . $storedFilePath);
                            Log::info('DESTI FILE : ' . $storedDestinationPath);

                            // Chnage according to the file destination
                            // In production Key will be 8 and 9
                            // In webdev it will be 9 and 10
                            $pathElementsCount = count($destinationPath);
                            $post->upload_file = $destinationPath[$pathElementsCount - 2] . '/' . $destinationPath[$pathElementsCount - 1];

                            // $post->upload_file = $destinationPath[8] . '/' . $destinationPath[9];
                            // }
                        } else {
                            PostImage::create(['post_id' => $post->id, 'upload_image_file' => $file, 'position' => 0]);
                        }
                    }
                    // $destinationPath = str_replace("upload_file","upload_file",$file_path);



                    // $this->convertToMp4($upload_file,$file_path, $file_path);

                    // converting mov to mp4
                    // if ($extension == 'mov') {
                    //     $file_path = storage_path() . '/app/public/' . $file_path;

                    //     // $destinationPath = storage_path().'/app/public/'.$file_path;

                    //     $destinationPath = str_replace(".mov", ".mp4", $file_path);

                    //     exec("echo 'SvTS9iQusP7SRPh@YMVDV' | sudo -kS chmod -R 777 /var/www/html/storage/app/public/");
                    //     $command = "/usr/bin/ffmpeg -i '" . $file_path . "' -c:v libx264 -profile:v main -vf format=yuv420p -c:a aac -movflags +faststart " . $destinationPath . " -y 2>&1";


                    //     $cmd =  exec("/usr/bin/ffmpeg -i '" . $file_path . "' -c:v libx264 -profile:v main -vf format=yuv420p -c:a aac -movflags +faststart " . $destinationPath . " -y 2>&1");

                    //     $response = [
                    //         'status'    =>  'success',
                    //         'message'   =>  $cmd,
                    //         'response'  =>  [
                    //             'code'  =>  200,
                    //             'file'  =>  $destinationPath,
                    //         ],
                    //     ];

                    //     // Log::info('RES MOVE FILE : ' .$destinationPath);
                    //     $file = explode('/', $file_path);

                    //     $destinationPath = explode('/', $destinationPath);

                    //     $storedFilePath = $file[8] . '/' . $file[9];
                    //     $storedDestinationPath = $destinationPath[9];

                    //     // $s3Data = $this->moveFielToS3($storedFilePath, $storedDestinationPath);
                    //     // Log::info('S3 : '.$s3Data);

                    //     // store to s3 bucket
                    //     $publicPath = $storedFilePath;
                    //     $data = explode('/', $storedFilePath);
                    //     array_shift($data);

                    //     // $destination = $storedDestinationPath ?? implode('/', $data);
                    //     Log::info('SOURCE FILE : ' . $storedFilePath);
                    //     Log::info('DESTI FILE : ' . $storedDestinationPath);


                    //     Storage::disk('s3')->put('/videos/' . $storedDestinationPath, file_get_contents($file_path));

                    //     $post->upload_file = $destinationPath[8] . '/' . $destinationPath[9];
                    // }
                }

                if ($request->has('thumbnail') && $request->thumbnail !== null) {

                    $thumbnail = $request->file('thumbnail')->store('thumbnail');
                    $post->thumbnail = $request['thumbnail'] = $thumbnail;
                }
                $post->save();

                if (!empty($request->media_page_id)) {
                    $media_page_ids = explode(',', $request->media_page_id);
                    if (!empty($media_page_ids)) {
                        foreach ($media_page_ids as $media_page) {
                            $media_page_id = MediaPage::where('custom_id', $media_page)->first();
                            $post_media = PostMedia::create(['custom_id' => getUniqueString('post_media'), 'media_id' => $media_page_id->media_id, 'post_id' => $post->id, 'media_page_id' => $media_page_id->id]);
                        }
                    }
                    $post->load('images');
                    if (empty($post->schedule_date_time)) {
                        $media_page = MediaPage::whereIn('custom_id', $media_page_ids)->pluck('media_id')->toArray();
                        $media = Media::whereIn('id', $media_page)->get();
                        foreach ($media as $value) {
                            switch ($value->name) {

                                case 'X(Twitter)':

                                    $post_medias = PostMedia::where('post_id', $post->id)->where('media_id', $value->id)->pluck('media_page_id')->toArray();
                                    $media_pages = MediaPage::whereIn('id', $post_medias)->get();
                                    foreach ($media_pages as $media_page) {

                                        try {
                                            $files = $post->upload_file ? collect($post->upload_file)->toArray() : $post->images->pluck('upload_image_file')->toArray();
                                            if (Auth::user()->type == 'company') {
                                                $user_id = Auth::id();
                                            } else {
                                                $user_id = Auth::user()->parent_id ?? "";
                                            }
                                            $socialMedia = SocialMediaDetail::where('media_id', $value->id)->where('id', $media_page->social_media_detail_id)->where('user_id', $user_id)->first();

                                            if (isset($socialMedia->id)) {

                                                $connection = new TwitterOAuth(config('utility.TWITTER_CONSUMER_KEY'), config('utility.TWITTER_CONSUMER_SECRET'), $socialMedia->token, $socialMedia->token_secret);
                                                $media_ids = [];
                                                $parameters = [
                                                    'text' => str_replace("<br />", " ", nl2br($request->caption . "\n" . $request->hashtag)),
                                                ];
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
                                                        // $parameters['media']['media_ids'] = implode(',', $media_ids);
                                                        $parameters['media']['media_ids'] = $media_ids;
                                                    }
                                                } else if ($files && count($files) == 1 && $files[0] !== '') {
                                                    $image_type = $files[0] ? File::extension($files[0]) : "";
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
                                                        // $parameters['media']['media_ids'] = implode(',', $media_ids);
                                                        $parameters['media']['media_ids'] = $media_ids;
                                                    }
                                                }
                                                if ($parameters) {
                                                    $connection->setApiVersion('2');
                                                    $result = $connection->post('tweets', $parameters, true);
                                                    Log::debug('X(Twitter) --' . json_encode($result));
                                                    if (isset($result->errors)) throw new Exception($result->errors['message']);
                                                    if (isset($result->errors) && $result->errors[0]->code === 89) throw new Exception('Your X(Twitter) Token is Expired, You need to Reconnect your X(Twitter) Account For Post in X(Twitter).', 89);
                                                }
                                            }
                                        } catch (\Exception $ex) {
                                            PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->where('media_page_id', $media_page->id)->update([
                                                'is_error' => 'y',
                                                'error_message' => $ex->getMessage(),
                                            ]);
                                            $this->response['meta']['message'] = $ex->getMessage();
                                            $this->status = $this->statusArr['something_wrong'];
                                        }
                                    }
                                    break;
                                case 'Facebook':
                                    // $size = (Storage::size($post->upload_file)) ?? "";
                                    $files = $post->upload_file ? collect($post->upload_file)->toArray() : $post->images->pluck('upload_image_file')->toArray();
                                    $facebook = new Facebook([
                                        'app_id' => config('utility.FACEBOOK_APP_ID'),
                                        'app_secret' => config('utility.FACEBOOK_APP_SECRET'),
                                        'default_graph_version' => config('utility.DEFAULT_FACEBOOK_API_VERSION'),
                                    ]);

                                    $post_medias = PostMedia::where('post_id', $post->id)->where('media_id', $value->id)->pluck('media_page_id')->toArray();

                                    $media_pages = MediaPage::whereIn('id', $post_medias)->get();

                                    foreach ($media_pages as $media_page) {
                                        try {
                                            $token = $url = '';
                                            $user_id = auth()->user()->parent_id ?? auth()->id();
                                            $socialMedia = SocialMediaDetail::where('media_id', $value->id)
                                                ->where('id', $media_page->social_media_detail_id)
                                                ->where('user_id', $user_id)->first();
                                            if (isset($socialMedia->id)) {
                                                // get Page list for access_token
                                                // $response = $facebook->get('/' . $socialMedia->social_id . '/accounts?fields=name,access_token', $socialMedia->token);
                                                // $statuscode = $response->getHttpStatusCode();
                                                // $facebook_pages = ($statuscode == 200) ?  $response->getGraphEdge()->asArray()  : [];

                                                // if ($facebook_pages) {
                                                //     foreach ($facebook_pages as $facebook_page) {
                                                //         if ($facebook_page['id'] == $media_page->page_id) {
                                                //             $token =  $facebook_page['access_token'];
                                                //         }
                                                //     }
                                                // }
                                                $token = (new ControllersPostController)->getFacebookPagesToken($socialMedia, $media_page->page_id);
                                                if ($token) {
                                                    $media_ids = [];
                                                    $post_parameter = [];
                                                    $video_parameter = [];
                                                    $url = '/' . $media_page->page_id . '/feed';
                                                    $parameter['message'] = str_replace("<br />", " ", nl2br($request->caption . "\n" . $request->hashtag));
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
                                                            Log::debug('Facebook --' . json_encode($response));
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
                                                        Log::debug('Facebook --' . json_encode($response));
                                                        if (isset($response['id'])) {
                                                        } else throw new Exception($response['error']['message']);
                                                    } else {
                                                        $response = $facebook->post($url, $parameter, $token)->getGraphNode()->asArray();
                                                        Log::debug('Facebook --' . json_encode($response));
                                                        if (isset($response['id'])) {
                                                        } else throw new Exception($response['error']['message']);
                                                    }
                                                    // $response = $facebook->post($url, $parameter, $token)->getGraphNode()->asArray();
                                                    // if (isset($response['id'])) {
                                                    // } else throw new Exception($response['error']['message']);
                                                }
                                            }
                                        } catch (FacebookSDKException $e) {
                                            PostMedia::where('post_id', $post->id)->where('media_id', $value->id)->where('media_page_id', $media_page->id)->update([
                                                'is_error' => 'y',
                                                'error_message' => $e->getMessage(),
                                            ]);
                                            Log::debug('Facebook ' . $e->getMessage());
                                            $this->status = $this->statusArr['something_wrong'];
                                            $this->response['meta']['message'] = $e->getMessage();
                                        }
                                    }
                                    break;
                                case 'Linkedin':
                                    $params = $request->all();
                                    $params['images'] = $post->upload_file ? collect($post->upload_file)->toArray() : $post->images->pluck('upload_image_file')->toArray();
                                    $media_page_id = [];
                                    $user_id = auth()->user()->parent_id ?? auth()->id();
                                    //$social_media = SocialMediaDetail::where('user_id', $user_id)->where('media_id', $value->id)->first();
                                    $media_pages = MediaPage::whereIn('custom_id', explode(",", $params['media_page_id']))->get();

                                    if ($media_pages) {
                                        foreach ($media_pages as $val) {
                                            // $media_page_id[] = $val->page_id;
                                            try {
                                                $social_media = SocialMediaDetail::where('id', $val->social_media_detail_id)->where('user_id', $user_id)->first();
                                                if (isset($social_media->id))
                                                    LinkedInService::checkPage($social_media->social_id, $social_media->token, $params, [$val->page_id]);
                                            } catch (Exception $e) {
                                                PostMedia::where('post_id', $post->id)->where('media_id', $value->id)->where('media_page_id', $val->id)->update([
                                                    'is_error' => 'y',
                                                    'error_message' => $e->getMessage(),
                                                ]);
                                                Log::info('Linkedin' . $e->getMessage());
                                                $this->status = $this->statusArr['something_wrong'];
                                                $this->response['meta']['message'] = $e->getMessage();
                                            }
                                        }
                                    }
                                    break;
                                case 'Instagram':
                                    $params = $post->toArray();
                                    $media_page_id = [];
                                    $user_id =  $post->user->parent_id ?? $post->user_id;
                                    // $socialMediaData = SocialMediaDetail::where('user_id', $user_id)->where('media_id', $value->id)->first();

                                    $post_medias = PostMedia::where('post_id', $post->id)->where('media_id', $value->id)->pluck('media_page_id')->toArray();
                                    // $media_pages = MediaPage::whereIn('id', $post_medias)->select('page_id')->where('media_id', 4)->get();
                                    $media_pages = MediaPage::whereIn('id', $post_medias)->select('page_id', 'id', 'social_media_detail_id')->get();
                                    $image_type = File::extension($post->upload_file) ?? "";
                                    $files = $post->upload_file ? collect($post->upload_file)->toArray() : $post->images->pluck('upload_image_file')->toArray();
                                    $caption = str_replace("<br />", " ", nl2br($post->caption . "\n" . $post->hashtag));



                                    if ($media_pages) {
                                        foreach ($media_pages as $val) {
                                            $socialMediaData = SocialMediaDetail::where('user_id', $user_id)->where('media_id', $value->id)->where('id', $val->social_media_detail_id)->first();

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
                                                    Log::debug('Instagram --' . json_encode($contentRes));
                                                    if (!isset($contentRes['id']) && isset($contentRes['error'])) {
                                                        throw new Exception($contentRes['error']['message']);
                                                    }
                                                }
                                            } catch (Exception $e) {
                                                PostMedia::where('post_id', $post->id)->where('media_id', $value->id)->where('media_page_id', $val->id)->update([
                                                    'is_error' => 'y',
                                                    'error_message' => $e->getMessage(),
                                                ]);
                                                Log::info('Instagram ' . $e->getMessage());
                                                $this->status = $this->statusArr['something_wrong'];
                                                $this->response['meta']['message'] = $e->getMessage();
                                            }
                                        }
                                    }
                                    break;
                                case 'Google My Business':
                                    $params = $post->toArray();
                                    $user_id = auth()->user()->parent_id ?? auth()->id();

                                    // $socialMediaData = SocialMediaDetail::where('user_id', $user_id)->where('media_id', $value->id)->first();
                                    $post_medias = PostMedia::where('post_id', $post->id)->where('media_id', $value->id)->pluck('media_page_id')->toArray();
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
                                            $socialMediaData = SocialMediaDetail::where('user_id', $user_id)->where('media_id', $value->id)->where('id', $media_page->social_media_detail_id)->first();

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
                                                $post_media = PostMedia::where('post_id', $post->id)->where('media_id', $value->id)->where('media_page_id', $media_page->id)->update([
                                                    'is_error' => 'y',
                                                    'error_message' => $e->getMessage(),
                                                ]);
                                                Log::info('Google Business ' . $e->getMessage());
                                                flash('Please try after sometime to share the post on the Google Business Profile')->error();
                                            }
                                        }
                                    }
                                    break;
                            }
                        }
                    }
                }
                $this->response['data']  = null;
                $this->status = $this->statusArr['success'];
                if (empty($post->schedule_date_time)) {
                    $this->response['meta']['message']  =  trans('api.add', ['entity' => 'Post']);
                } else {
                    $this->response['meta']['message']  =  trans('api.add', ['entity' => 'Schedule Post']);
                }
                DB::commit();
            } catch (\Exception $ex) {
                // dd($ex->getMessage(), $ex->getLine());
                DB::rollback();
                Log::debug('Genral API Error : ' . $ex->getMessage());
                $this->status = $this->statusArr['something_wrong'];
                $this->response['meta']['message'] = trans('api.error');
            }
            $this->response['meta']['api'] = $this->version;
            $this->response['meta']['url'] = url()->current();
        }
        return $this->return_response();
    }
    public function postList(Request $request)
    {
        $user = $request->user();

        $rules = [
            'start_date'     =>  'nullable|date',
            'end_date'       =>  'nullable|date|after_or_equal:start_date',
            'type'           =>  'required|in:posts,scheduled_posts',
            'limit'          =>  'nullable|gte:0',
            'offset'         =>  'nullable|gte:0',
            'view_all'       =>  'nullable|in:default',
            'media_pages_ids' => 'nullable|string'
        ];
        if ($this->apiValidator($request->all(), $rules)) {
            try {
                $parent_ids = null;
                $type = $request->type ?? "";
                $parent_id = $user->parent_id ?? "";
                $startDate = date("Y-m-d", strtotime($request->start_date));
                $endDate = date("Y-m-d", strtotime($request->end_date));
                // dd($startDate,$endDate);
                $postsCount = 0;
                $scheduleCount = 0;
                if (!empty($parent_id)) {
                    $parent_user = User::where('id', $parent_id)->first();
                    $parent_ids = $parent_user->parents->pluck('id')->toArray();
                    $parent_ids[] = $parent_user->id;
                } else {
                    $parent_ids = $user->parents->pluck('id')->toArray();
                    $parent_ids[] = $user->id;
                }
                $limit = ($request->limit) ? $request->limit : config('utility.pagination.limit');
                $offset = ($request->offset) ? $request->offset : config('utility.pagination.offset');
                $posts = Post::where('is_active', 'y');
                $posts = $posts->whereIn('user_id', $parent_ids);
                $var = (\Carbon\Carbon::parse(now())->format('Y-m-d H:i:s'));

                if ($type == 'posts' && empty($request->start_date)  && empty($request->end_date) && $request->view_all == '') {
                    // $posts = $posts->select('posts.*')->whereRaw(\DB::raw('concat(posts.schedule_date, " ", posts.schedule_time) <= '."'$var'"))->orWhere('schedule_date',null);
                    $posts = $posts->whereBetween('created_at', [Carbon::now()->subDays(7)->toDateString() . ' 00:00:00', Carbon::parse(now())->toDateString() . ' 23:59:59']);
                    $posts = $posts->where(function ($q) use ($var) {
                        $q->where('schedule_date_time', '<=', $var)->orWhere('schedule_date_time', null);
                    });

                    $posts = $posts->orderBy('created_at', 'desc');
                } elseif (($type == 'scheduled_posts') && empty($request->start_date)  && empty($request->end_date) && $request->view_all == '') {
                    // $posts = $posts->select('posts.*',\DB::raw('cast(concat(posts.schedule_date, " ", posts.schedule_time) as datetime) as schedule'))->having('schedule',">",\Carbon\Carbon::parse(now())->format('Y-m-d H:i:s'));
                    $posts = $posts->whereBetween('schedule_date', [Carbon::parse(now())->toDateString(), Carbon::today()->addDays(7)]);
                    $posts = $posts->where(function ($q) use ($var) {
                        $q->where('schedule_date_time', '>=', $var);
                    });

                    $posts = $posts->orderBy('schedule_date_time', 'desc');
                } elseif ((!empty($request->start_date)  && !empty($request->end_date)) && $type == 'posts' && $request->view_all == '') {
                    $posts = $posts->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                    $posts = $posts->where(function ($q) use ($var) {
                        $q->where('schedule_date_time', '<=', $var)->orWhere('schedule_date_time', null);
                    });

                    $posts = $posts->orderBy('created_at', 'desc');
                } elseif (!empty($request->start_date)  && !empty($request->end_date) && $type == 'scheduled_posts' && $request->view_all == '') {
                    $posts = $posts->whereBetween('schedule_date', [$startDate, $endDate]);
                    $posts = $posts->where(function ($q) use ($var) {
                        $q->where('schedule_date_time', '<>', null);
                    });

                    $posts = $posts->orderBy('schedule_date_time', 'desc');
                } elseif ($request->view_all == 'default' && $type == 'posts') {
                    $posts = $posts->where(function ($q) use ($var) {
                        $q->where('schedule_date_time', '<=', $var)->orWhere('schedule_date_time', null);
                    });
                    $posts = $posts->orderBy('created_at', 'desc');
                } elseif ($request->view_all == 'default' && $type == 'scheduled_posts') {
                    $posts = $posts->where(function ($q) use ($var) {
                        $q->where('schedule_date_time', '<>', null);
                    });
                    $posts = $posts->orderBy('schedule_date_time', 'desc');
                }
                if ($request->media_pages_ids) {
                    $mediaPages = explode(',', $request->media_pages_ids);
                    $posts->whereHas('postMedia', function ($q) use ($mediaPages) {
                        $q->whereIn('media_page_id', $mediaPages);
                    });
                }

                $count = $posts->latest()->count();
                $posts = $posts->with('postMediaList')->with(['postMedia', 'images']);
                $posts = $posts->limit($limit)->offset($offset)->get();
                return (new PostListResource($posts))
                    ->additional([
                        'meta' => [
                            'message' => trans('api.list', ['entity' => 'Post list']),
                            'count' => $count,
                        ]
                    ]);
            } catch (\Exception $ex) {
                $this->status = $this->statusArr['something_wrong'];
                $this->response['message'] = trans('api.error');
            }
        }
        return $this->return_response();
    }


    public function postNow(Request $request)
    {
        $rules = [
            'post_id'       => 'required|exists:posts,custom_id',
        ];
        if ($this->apiValidator($request->all(), $rules)) {
            DB::beginTransaction();
            try {
                $post = Post::where('custom_id', $request->post_id)->first();
                $post->schedule_date = null;
                $post->schedule_time = null;
                $post->schedule_date_time = null;
                $post->created_at = \Carbon\Carbon::now();;
                $post->save();

                $post->load(['postMedia', 'images']);

                $post_media_ids = $post->postMedia->pluck('media_id')->toArray();
                $medias = Media::whereIn('id', $post_media_ids)->get();
                foreach ($medias as $media) {
                    switch ($media->name) {
                        case 'Facebook':
                            $files = $post->upload_file ? collect($post->upload_file)->toArray() : $post->images->pluck('upload_image_file')->toArray();
                            $facebook = new Facebook([
                                'app_id' => config('utility.FACEBOOK_APP_ID'),
                                'app_secret' => config('utility.FACEBOOK_APP_SECRET'),
                                'default_graph_version' => config('utility.DEFAULT_FACEBOOK_API_VERSION'),
                            ]);

                            $post_medias = PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->pluck('media_page_id')->toArray();

                            $media_pages = MediaPage::whereIn('id', $post_medias)->get();

                            foreach ($media_pages as $media_page) {
                                try {
                                    $token = $url = '';
                                    $user_id = auth()->user()->parent_id ?? auth()->id();
                                    $socialMedia = SocialMediaDetail::where('media_id', $media->id)
                                        ->where('id', $media_page->social_media_detail_id)
                                        ->where('user_id', $user_id)->first();
                                    if (isset($socialMedia->id)) {
                                        // get Page list for access_token
                                        // $response = $facebook->get('/' . $socialMedia->social_id . '/accounts?fields=name,access_token', $socialMedia->token);
                                        // $statuscode = $response->getHttpStatusCode();
                                        // $facebook_pages = ($statuscode == 200) ?  $response->getGraphEdge()->asArray()  : [];

                                        // if ($facebook_pages) {
                                        //     foreach ($facebook_pages as $facebook_page) {
                                        //         if ($facebook_page['id'] == $media_page->page_id) {
                                        //             $token =  $facebook_page['access_token'];
                                        //         }
                                        //     }
                                        // }
                                        $token = (new ControllersPostController)->getFacebookPagesToken($socialMedia, $media_page->page_id);
                                        if ($token) {
                                            $media_ids = [];
                                            $post_parameter = [];
                                            $video_parameter = [];
                                            $url = '/' . $media_page->page_id . '/feed';
                                            $parameter['message'] = str_replace("<br />", " ", nl2br($request->caption . "\n" . $request->hashtag));
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
                                                    Log::debug('Facebook --' . json_encode($response));
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
                                                Log::debug('Facebook --' . json_encode($response));
                                                if (isset($response['id'])) {
                                                } else throw new Exception($response['error']['message']);
                                            } else {
                                                $response = $facebook->post($url, $parameter, $token)->getGraphNode()->asArray();
                                                Log::debug('Facebook --' . json_encode($response));
                                                if (isset($response['id'])) {
                                                } else throw new Exception($response['error']['message']);
                                            }
                                            // $response = $facebook->post($url, $parameter, $token)->getGraphNode()->asArray();
                                            // if (isset($response['id'])) {
                                            // } else throw new Exception($response['error']['message']);
                                        }
                                    }
                                } catch (FacebookSDKException $e) {
                                    PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->where('media_page_id', $media_page->id)->update([
                                        'is_error' => 'y',
                                        'error_message' => $e->getMessage(),
                                    ]);
                                    Log::debug('Facebook ' . $e->getMessage());
                                    $this->status = $this->statusArr['something_wrong'];
                                    $this->response['meta']['message'] = $e->getMessage();
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
                                    } catch (Exception $e) {
                                        Log::info('Linkedin' . $e->getMessage());
                                        PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->where('media_page_id', $val->id)->update([
                                            'is_error' => 'y',
                                            'error_message' => $e->getMessage(),
                                        ]);
                                        // flash('Please try after sometime to share the post on the linkedin')->error();
                                    }
                                }
                            }

                            // if ($media_pages) {
                            //     foreach ($media_pages as $val) {
                            //         $media_page_id[] = $val->page_id;
                            //     }
                            // }
                            // if ($social_media) {
                            //     LinkedInService::checkPage($social_media->social_id, $social_media->token, $params, $media_page_id);
                            // }
                            break;
                        case 'X(Twitter)':

                            $post_medias = PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->pluck('media_page_id')->toArray();
                            $media_pages = MediaPage::whereIn('id', $post_medias)->get();
                            foreach ($media_pages as $media_page) {

                                try {
                                    $files = $post->upload_file ? collect($post->upload_file)->toArray() : $post->images->pluck('upload_image_file')->toArray();
                                    if (Auth::user()->type == 'company') {
                                        $user_id = Auth::id();
                                    } else {
                                        $user_id = Auth::user()->parent_id ?? "";
                                    }
                                    $socialMedia = SocialMediaDetail::where('media_id', $media->id)->where('id', $media_page->social_media_detail_id)->where('user_id', $user_id)->first();

                                    if (isset($socialMedia->id)) {

                                        $connection = new TwitterOAuth(config('utility.TWITTER_CONSUMER_KEY'), config('utility.TWITTER_CONSUMER_SECRET'), $socialMedia->token, $socialMedia->token_secret);
                                        $media_ids = [];
                                        $parameters = [
                                            'text' => str_replace("<br />", " ", nl2br($request->caption . "\n" . $request->hashtag)),
                                        ];
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
                                                // $parameters['media']['media_ids'] = implode(',', $media_ids);
                                                $parameters['media']['media_ids'] = $media_ids;
                                            }
                                        } else if ($files && count($files) == 1 && $files[0] !== '') {
                                            $image_type = $files[0] ? File::extension($files[0]) : "";
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
                                                // $parameters['media']['media_ids'] = implode(',', $media_ids);
                                                $parameters['media']['media_ids'] = $media_ids;
                                            }
                                        }
                                        if ($parameters) {
                                            $connection->setApiVersion('2');
                                            $result = $connection->post('tweets', $parameters, true);
                                            Log::debug('X(Twitter) --' . json_encode($result));
                                            if (isset($result->errors)) throw new Exception($result->errors['message']);
                                            if (isset($result->errors) && $result->errors[0]->code === 89) throw new Exception('Your X(Twitter) Token is Expired, You need to Reconnect your X(Twitter) Account For Post in X(Twitter).', 89);
                                        }
                                    }
                                } catch (\Exception $ex) {
                                    PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->where('media_page_id', $media_page->id)->update([
                                        'is_error' => 'y',
                                        'error_message' => $ex->getMessage(),
                                    ]);
                                    $this->response['meta']['message'] = $ex->getMessage();
                                    $this->status = $this->statusArr['something_wrong'];
                                }
                            }

                            break;

                        case 'Instagram':
                            $params = $post->toArray();
                            $media_page_id = [];
                            $user_id =  $post->user->parent_id ?? $post->user_id;
                            // $socialMediaData = SocialMediaDetail::where('user_id', $user_id)->where('media_id', $media->id)->first();

                            $post_medias = PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->pluck('media_page_id')->toArray();
                            // $media_pages = MediaPage::whereIn('id', $post_medias)->select('page_id')->where('media_id', 4)->get();
                            $media_pages = MediaPage::whereIn('id', $post_medias)->select('page_id', 'id', 'social_media_detail_id')->get();
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
                                            Log::debug('Instagram --' . json_encode($contentRes));
                                            if (!isset($contentRes['id']) && isset($contentRes['error'])) {
                                                throw new Exception($contentRes['error']['message']);
                                            }
                                        }
                                    } catch (Exception $e) {
                                        PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->where('media_page_id', $val->id)->update([
                                            'is_error' => 'y',
                                            'error_message' => $e->getMessage(),
                                        ]);
                                        Log::info('Instagram ' . $e->getMessage());
                                        $this->status = $this->statusArr['something_wrong'];
                                        $this->response['meta']['message'] = $e->getMessage();
                                    }
                                }
                            }
                            break;
                        case 'Google My Business':
                            $params = $post->toArray();
                            $user_id =  $post->user->parent_id ?? $post->user_id;

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
                                        $post_media = PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->where('media_page_id', $media_page->id)->update([
                                            'is_error' => 'y',
                                            'error_message' => $e->getMessage(),
                                        ]);
                                        Log::info('Google Business ' . $e->getMessage());
                                        flash('Please try after sometime to share the post on the Google Business Profile')->error();
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
                $this->response['data']  = null;
                $this->status = $this->statusArr['success'];
                $this->response['meta']['message']  =  trans('api.add', ['entity' => 'Post']);
                DB::commit();
            } catch (\Exception $ex) {
                Db::rollBack();
                $this->status = $this->statusArr['something_wrong'];
                $this->response['message'] = trans('api.error');
            }
            $this->response['meta']['api'] = $this->version;
            $this->response['meta']['url'] = url()->current();
        }
        return $this->return_response();
    }

    /* Add Post API */
    public function editPost(Request $request)
    {
        ini_set('max_execution_time', 2400);
        ini_set('set_time_limit', 0);
        ini_set('memory_limit', '-1');
        $user = $request->user();
        $rules = [
            'post_id'           => 'required|exists:posts,custom_id',
            'images'             => 'sometimes|nullable',
            // 'images.*'             => 'file|mimes:jpeg,png,jpg,mp4,mov,application/x-mpegURL',
            'delete_file'       => 'nullable|sometimes',
            'thumbnail'         => 'sometimes|nullable|mimes:jpeg,png,jpg',
            'caption'           => 'required',
            'hashtag'           => 'nullable|sometimes',
            'schedule_date'     => 'sometimes|nullable|date|date_format:Y-m-d',
            'schedule_time'     => 'required_with:schedule_date|nullable|date_format:g:i A',
            'media_page_id'     => ['required', new MediaPageExists()],
            'call_to_action_type' => 'nullable|sometimes|in:ACTION_TYPE_UNSPECIFIED,BOOK,ORDER,SHOP,LEARN_MORE,CALL',
            // 'call_to_action_link' => 'required_unless:call_to_action_type,ACTION_TYPE_UNSPECIFIED,CALL'
        ];
        if ($this->apiValidator($request->all(), $rules)) {
            try {

                $post = Post::where('custom_id', $request->post_id)->first();
                // $check = Post::query();
                // $check =  $check->select('posts.*',\DB::raw('cast(concat(posts.schedule_date, " ", posts.schedule_time) as datetime) as schedule'))->having('schedule',">",\Carbon\Carbon::parse(now())->format('Y-m-d H:i:s'))->where('id',$post->id)->first();
                // if(!empty($check)){
                if (!empty($request->schedule_time)) {

                    $request['schedule_time'] = \Carbon\Carbon::createFromFormat('h:i A', $request->schedule_time)->format('H:i:s');
                }
                if (!empty($request->schedule_date)) {

                    $request['schedule_date'] = \Carbon\Carbon::createFromFormat('Y-m-d', $request->schedule_date)->format('Y-m-d');
                }
                if (!empty($request->schedule_date) || !empty($request->schedule_time)) {
                    // $request['schedule_date_time'] = $request['schedule_date'] . ' ' . $request['schedule_time'];
                    $timeZone =  $request->has('timezone') ? $request->timezone : "Australia/Brisbane";
                    $dateTime = $request['schedule_date'] . ' ' . $request['schedule_time'];
                    $request['schedule_date_time'] = convertUTC($dateTime, $timeZone);
                }

                $request['action_link'] = $request->call_to_action_link;

                $post = $post->fill($request->except('upload_file', 'thumbnail'));

                if (!empty($request->delete_file)) {

                    if ($post->upload_file == null) {
                        // $images = explode(',', $request->delete_file);
                        $images = $request->delete_file;
                        foreach ($images as $image) {
                            $path = strrchr($image, '/');
                            if (Storage::exists('upload_file' . $path)) {
                                Storage::delete('upload_file' . $path);
                            }
                            $post_image = PostImage::where('upload_image_file', 'upload_file' . $path)->first();
                            if ($post_image) {
                                $post_image->delete();
                            }
                        }
                    } else {
                        $image = $request->delete_file['0'];
                        $path = strrchr($image, '/');
                        if (Storage::exists('upload_file' . $path)) {
                            Storage::delete('upload_file' . $path);
                        }
                        $post->upload_file = null;
                    }
                }

                if ($request->has('images')) {
                    $images = $request->images ?? '';
                    foreach ($images as $key => $value) {
                        $image_type = is_file($value['media']) ? File::extension($value['media']) : '';
                        $file_path  = is_file($value['media']) ? $value['media']->store('upload_file') : str_replace(Storage::url(''), '', $value['media']);
                        if ($image_type == "mp4" || $image_type == "mov") {
                            $post->upload_file = $file_path;
                        } else {
                            $post->upload_file != null ? $post->upload_file == null : '';
                            $image = PostImage::wherePostId($post->id)->whereUploadImageFile($file_path)->first();
                            if ($image) {
                                if (substr($file_path, -3) == 'mp4' || substr($file_path, -3) == 'mov') {
                                    $post->upload_file = $file_path;
                                } else {
                                    $image->update(['position' => ($value['index'] + 1)]);
                                }
                            } else {
                                if (substr($file_path, -3) == 'mp4' || substr($file_path, -3) == 'mov') {
                                    $post->upload_file = $file_path;
                                } else {
                                    PostImage::create(['post_id' => $post->id, 'upload_image_file' => $file_path, 'position' => ($value['index'] + 1)]);
                                }
                            }
                        }
                    }
                }

                if ($request->has('thumbnail')) {
                    if ($post->thumbnail) {
                        if (Storage::exists($post->thumbnail)) {
                            Storage::delete($post->thumbnail);
                        }
                    }
                    if ($request->file('thumbnail')) {
                        $thumbnail = $request->file('thumbnail')->store('thumbnail');
                        $post->thumbnail = $thumbnail;
                    }
                }
                $post->save();
                if (!empty($request->media_page_id)) {
                    $media_page_ids = explode(',', $request->media_page_id);
                    if (!empty($media_page_ids)) {
                        foreach ($media_page_ids as $media_page) {
                            $media_page_id = MediaPage::where('custom_id', $media_page)->first();
                            $post_media = PostMedia::updateOrCreate(
                                [
                                    'post_id'       => $post->id,
                                    'media_page_id' => $media_page_id->id ?? '',
                                    'media_id'      => $media_page_id->media_id,
                                ],
                                [
                                    'custom_id'     => getUniqueString('post_media'),
                                    'post_id'       => $post->id,
                                    'media_page_id' => $media_page_id->id ?? '',
                                    'media_id'      => $media_page_id->media_id,
                                ]
                            );
                            $not_delete_post_media[] = $post_media->id;
                        }
                        PostMedia::where('post_id', $post->id)->whereNotIn('id', $not_delete_post_media)->delete();
                    }
                }

                $this->response['data']  = null;
                $this->status = $this->statusArr['success'];
                if (empty($post->schedule_date_time)) {
                    $this->response['meta']['message']  =  trans('api.update', ['entity' => 'Post']);
                } else {
                    $this->response['meta']['message']  =  trans('api.update', ['entity' => 'Schedule Post']);
                }

                // }else{
                //     $this->response['data']  = null;
                //     $this->status = $this->statusArr['unprocessable_entity'];
                //     $this->response['meta']['message']  = "The selected post id is invalid";
                // }

            } catch (\Exception $ex) {
                // dd($ex->getMessage());
                Log::info($ex->getMessage());
                $this->status = $this->statusArr['something_wrong'];
                $this->response['meta']['message'] = trans('api.error');
            }
            $this->response['meta']['api'] = $this->version;
            $this->response['meta']['url'] = url()->current();
        }
        return $this->return_response();
    }

    public function deletePost(Request $request)
    {
        $user = $request->user();
        $rules = [
            'post_id'           => 'required|exists:posts,custom_id',
        ];
        if ($this->apiValidator($request->all(), $rules)) {
            try {

                $post = Post::where('custom_id', $request->post_id)->with('images')->first();
                $files = $post->upload_file ? collect($post->upload_file)->toArray() : $post->images->pluck('upload_image_file')->toArray();
                if ($files) {
                    foreach ($files as $file) {
                        if (Storage::exists($file)) {
                            Storage::delete($file);
                        }
                    }
                }
                if ($post->thumbnail) {
                    if (Storage::exists($post->thumbnail)) {
                        Storage::delete($post->thumbnail);
                    }
                }
                $post->delete();
                $this->response['data']  = null;
                $this->status = $this->statusArr['success'];
                $this->response['meta']['message']  =  trans('api.delete', ['entity' => trans('Post')]);
            } catch (\Exception $ex) {
                $this->status = $this->statusArr['something_wrong'];
                $this->response['meta']['message'] = trans('api.error');
            }
            $this->response['meta']['api'] = $this->version;
            $this->response['meta']['url'] = url()->current();
        }
        return $this->return_response();
    }
}
