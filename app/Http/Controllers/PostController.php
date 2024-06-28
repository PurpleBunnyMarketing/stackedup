<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\PostRequest;
use App\Models\Media;
use App\Models\MediaPage;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostMedia;
use App\Models\PostTempImage;
use App\Models\SocialMediaDetail;
use App\User;
use App\Services\LinkedInService;
use App\Services\GoogleMyBusinessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Pawlox\VideoThumbnail\Facade\VideoThumbnail;
use Abraham\TwitterOAuth\TwitterOAuth;
use Exception;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
// use LinkedIn\Client;
// use LinkedIn\AccessToken;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $url = URL::previous();

        $id = explode('/', $url);
        $type = 'posts';
        if (!empty($id[5])) {
            $post = Post::find($id[5]);
            if (!empty($post)) {
                if ($post->schedule_date)
                    $type = 'scheduled_posts';
                else
                    $type = 'posts';
            } else {
                $type = 'posts';
            }
        }
        $user = auth()->user()->parent_id ? auth()->user()->company : auth()->user();
        $media = Media::where('type', 'social')->whereHas('mediaPages', function ($q) use ($user) {
            $q->whereHas('userMediaPages', function ($q) use ($user) {
                $q->where('user_id', $user->id);
                $q->where('is_deleted', 'n');
            });
        })->with(['mediaPages' => function ($q) use ($user) {
            $q->whereHas('userMediaPages', function ($q) use ($user) {
                $q->where('user_id', $user->id);
                $q->where('is_deleted', 'n');
            });
        }])->get();
        return view('frontend.pages.posts.list', compact('type', 'media'))->withTitle('Posts');
    }
    public function create()
    {
        $temps = PostTempImage::where('user_id', Auth::user()->id)->get();

        foreach ($temps as $temp) {
            if ($temp->upload_file) {
                if (Storage::exists($temp->upload_file)) {
                    Storage::delete($temp->upload_file);
                }
            }
            if ($temp->thumbnail) {
                if (Storage::exists($temp->thumbnail)) {
                    Storage::delete($temp->thumbnail);
                }
            }
            $temp->delete();
        }
        // $media = Media::get();
        $user = auth()->user();
        $media = Media::where('type', 'social')->whereHas('mediaPages', function ($q) use ($user) {
            $q->whereHas('userMediaPages', function ($q) use ($user) {
                $q->where('user_id', $user->id);
                $q->where('is_deleted', 'n');
            });
        })->with(['mediaPages' => function ($q) use ($user) {
            $q->whereHas('userMediaPages', function ($q) use ($user) {
                $q->where('user_id', $user->id);
                $q->where('is_deleted', 'n');
            });
        }])->get();

        $max_file_size = substr(ini_get('upload_max_filesize'), 0, -1);
        $social_media_video_size = config('utility.social_media_file_size_limit');
        return view('frontend.pages.posts.create', compact('media', 'max_file_size', 'social_media_video_size'))->withTitle('Add Post');
    }
    public function show($id)
    {

        $post = Post::where('id', $id)->with(['postMedia.mediaPage', 'postMedia.media', 'images'])->first();
        $media = Media::whereHas('postMedia', function ($q) use ($post) {
            $q->where('post_id', $post->id);
        })->with('mediaPages', function ($q) use ($post) {
            $q->whereHas('postMedia', function ($q) use ($post) {
                $q->where('post_id', $post->id);
            });
        })->get();

        return view('frontend.pages.posts.show', compact('post', 'media'))->withTitle('View Post');
    }

    public function store(PostRequest $request)
    {
        DB::beginTransaction();
        try {

            if (strpos($request->thumbnail_image, 'thumbnail') !== false) {
                $request['thumbnail_image'] = substr($request->thumbnail_image, strpos($request->thumbnail_image, 'thumbnail'));
            } else {
                $request['thumbnail_image'] = substr($request->thumbnail_image, strpos($request->thumbnail_image, 'upload_file'));
            }

            $request['is_active'] = 'y';
            $request['user_id'] = Auth::user()->id ?? "";
            $request['custom_id'] = getUniqueString('posts');
            $request['is_call_to_action'] = 'n';

            //if not getting from the cookies then we need to australia/brisbane
            $timeZone = isset($_COOKIE['timeZone']) ? $_COOKIE['timeZone'] : "Australia/Brisbane";

            if (!empty($request->schedule_time)) {
                $request['schedule_time'] = Carbon::createFromFormat('g:i A', $request->schedule_time)->format('H:i:s');
            }

            if (!empty($request->schedule_date)) {
                $request['schedule_date'] = Carbon::createFromFormat('m/d/Y', $request->schedule_date)->format('Y-m-d');
            }

            if (!empty($request->schedule_date) || !empty($request->schedule_time)) {
                $dateTime = $request['schedule_date'] . ' ' . $request['schedule_time'];
                $request['schedule_date_time'] = convertUTC($dateTime, $timeZone);
            }

            // For Google Fields
            if ($request->action_type !== '') {
                $request['is_call_to_action'] = 'y';
                $request['call_to_action_type'] = $request->action_type;
            }

            $post = Post::create($request->all());
            // if($request->has('upload_file')) {
            //     $upload_file = $request->file('upload_file')->store('upload_file');
            //     $post->upload_file = $upload_file;
            // }

            if ($request->has('thumbnail_image')) {
                $post->thumbnail = $request->thumbnail_image ?? "";

                PostTempImage::where('thumbnail', $request->thumbnail_image)->delete();
            }
            if (!empty($request->images)) {

                // $images = $request->images ?? "";
                $images = explode(',', $request->images);
                if (count($images) > 1) {
                    foreach ($images as $key => $value) {
                        $image_type = $value ? File::extension($value) : "";
                        if ($image_type == "mp4") {
                            $post->upload_file = $value;
                        } else {
                            $image = PostImage::create(['post_id' => $post->id, 'upload_image_file' => $value, 'position' => ($key + 1)]);
                        }
                        PostTempImage::where('upload_file', $value)->delete();
                    }
                } else {
                    $image_type = isset($images[0]) ? File::extension($images[0]) : "";
                    if ($image_type == "mp4") {
                        $post->upload_file = $images[0];
                    } else {
                        PostImage::create(['post_id' => $post->id, 'upload_image_file' => $images[0], 'position' => 0]);
                    }
                    PostTempImage::where('upload_file', $images[0])->delete();
                }
            }

            $post->save();
            if (!empty($request->media_page_id)) {
                $media_page_ids = $request->media_page_id;
                if (!empty($media_page_ids)) {
                    foreach ($media_page_ids as $media_page) {
                        $media_page_id = MediaPage::where('id', $media_page)->first();
                        $post_media = PostMedia::create(['custom_id' => getUniqueString('post_media'), 'media_id' => $media_page_id->media_id, 'post_id' => $post->id, 'media_page_id' => $media_page_id->id]);
                    }
                }
            }
            $post->load('images');
            if (empty($post->schedule_date_time)) {
                $media_page = MediaPage::whereIn('id', $request->media_page_id)->pluck('media_id')->toArray();
                $media = Media::whereIn('id', $media_page)->get();
                foreach ($media as $value) {
                    switch ($value->name) {
                        case 'X(Twitter)':

                            $post_medias = PostMedia::where('post_id', $post->id)->where('media_id', $value->id)->pluck('media_page_id')->toArray();
                            $media_pages = MediaPage::whereIn('id', $post_medias)->get();
                            $user_id = auth()->user()->parent_id ?? auth()->id();

                            foreach ($media_pages as $media_page) {

                                try {
                                    $socialMedia = SocialMediaDetail::where('media_id', $value->id)->where('id', $media_page->social_media_detail_id)->where('user_id', $user_id)->first();
                                    if (isset($socialMedia->id)) {
                                        $files = explode(',', $request->images)  ?? "";
                                        // $size = (Storage::size($request->images)) ?? "";

                                        $connection = new TwitterOAuth(config('utility.TWITTER_CONSUMER_KEY'), config('utility.TWITTER_CONSUMER_SECRET'), $socialMedia->token, $socialMedia->token_secret);
                                        $media_ids = [];
                                        $parameters = ['text' => str_replace("<br />", " ", nl2br($request->caption . "\n" . $request->hashtag)),];
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
                                                // $parameters['media_ids'] = implode(',', $media_ids);
                                                $parameters['media']['media_ids'] = $media_ids;
                                            }
                                        }
                                        if ($parameters) {
                                            $connection->setApiVersion('2');
                                            $result = $connection->post('tweets', $parameters, true);
                                            if (isset($result->errors) && $result->errors[0]->code === 89) throw new Exception('Your X(Twitter) Token is Expired, You need to Reconnect your X(Twitter) Account For Post in Twitter.', 89);
                                        }
                                    }
                                } catch (\Exception $ex) {
                                    if ($ex->getCode() == 89)
                                        flash($ex->getMessage())->error();
                                    else
                                        flash('Technical issue in X(Twitter), Please try after sometime to Post.')->error();
                                    // if ($post->postMedia->count() == 1) $post->delete();
                                    // else PostMedia::where('post_id', $post->id)->where('media_id', $value->id)->where('media_page_id', $media_page->id)->delete();
                                    PostMedia::where('post_id', $post->id)->where('media_id', $value->id)->where('media_page_id', $media_page->id)->update([
                                        'is_error' => 'y',
                                        'error_message' => $ex->getMessage(),
                                    ]);
                                    Log::info('X(Twitter) ' . $ex->getMessage());
                                }
                            }
                            break;
                        case 'Facebook':
                            // $size = Storage::size($request->images) ?? "";
                            $files = explode(',', $request->images)  ?? "";

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
                                    $socialMedia = SocialMediaDetail::where('id', $media_page->social_media_detail_id)->where('user_id', $user_id)->first();
                                    if (isset($socialMedia->id)) {
                                        // get Page list for access_token
                                        // $response = $facebook->get('/' . $socialMedia->social_id . '/accounts?fields=name,access_token', $socialMedia->token);

                                        // $statuscode = $response->getHttpStatusCode();
                                        // $facebook_pages = ($statuscode == 200) ?  $response->getGraphEdge()->asArray()  : [];
                                        $token = $this->getFacebookPagesToken($socialMedia, $media_page->page_id);

                                        // if ($facebook_pages) {
                                        //     foreach ($facebook_pages as $facebook_page) {
                                        //         if ($facebook_page['id'] == $media_page->page_id) {
                                        //             $token =  $facebook_page['access_token'];
                                        //         }
                                        //     }
                                        // }
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
                                                        $video_parameter['description'] = str_replace("<br />", " ", nl2br($request->caption . "\n" . $request->hashtag));
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
                                                    Log::debug('Facebook Response --- ' . json_encode($response));
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
                                                Log::debug('Facebook Response --- ' . json_encode($response));
                                                if (isset($response['id'])) {
                                                } else throw new Exception($response['error']['message']);
                                            } else {
                                                $response = $facebook->post($url, $parameter, $token)->getGraphNode()->asArray();
                                                Log::debug('Facebook Response --- ' . json_encode($response));
                                                if (isset($response['id'])) {
                                                } else throw new Exception($response['error']['message']);
                                            }
                                        }
                                    }
                                } catch (FacebookSDKException $e) {
                                    // if ($post->postMedia->count() == 1) $post->delete();
                                    // else PostMedia::where('post_id', $post->id)->where('media_id', $value->id)->where('media_page_id', $media_page->id)->delete();
                                    Log::info('Facebook ' . $e->getMessage());
                                    flash('Technical issue with ' . $media_page->name . ' , Try Again')->error();
                                    PostMedia::where('post_id', $post->id)->where('media_id', $value->id)->where('media_page_id', $media_page->id)->update([
                                        'is_error' => 'y',
                                        'error_message' => $e->getMessage(),
                                    ]);
                                }
                            }
                            break;
                        case 'Linkedin':
                            $params = $request->all();
                            $params['images'] = $request->images !== null ?  explode(',', $request->images) : [];
                            $media_page_id = [];
                            $user_id = auth()->user()->parent_id ?? auth()->id();
                            // $social_media = SocialMediaDetail::where('user_id', $user_id)->where('media_id', $value->id)->first();
                            $media_pages = MediaPage::whereIn('id', $params['media_page_id'])->get();
                            // dd($media_pages);
                            if ($media_pages) {
                                foreach ($media_pages as $val) {
                                    try {
                                        $social_media = SocialMediaDetail::where('id', $val->social_media_detail_id)->where('user_id', $user_id)->first();
                                        if (isset($social_media->id))
                                            LinkedInService::checkPage($social_media->social_id, $social_media->token, $params, [$val->page_id]);
                                    } catch (Exception $e) {
                                        Log::info('Linkedin' . $e->getMessage());
                                        PostMedia::where('post_id', $post->id)->where('media_id', $value->id)->where('media_page_id', $val->id)->update([
                                            'is_error' => 'y',
                                            'error_message' => $e->getMessage(),
                                        ]);
                                        // if ($post->postMedia->count() == 1) $post->delete();
                                        // else PostMedia::where('post_id', $post->id)->where('media_id', $value->id)->where('media_page_id', $media_page->id)->delete();
                                        flash('Please try after sometime to share the post on the linkedin')->error();
                                    }
                                }
                            }

                            // if ($social_media) {
                            //     LinkedInService::checkPage($social_media->social_id, $social_media->token, $params, $media_page_id);
                            //     // $this->checkPage($social_media->social_id,$social_media->token,$params,$media_page_id);
                            // }

                            break;
                        case 'Instagram':
                            $params = $request->all();
                            $media_page_id = [];
                            $user_id = auth()->user()->parent_id ?? auth()->id();
                            $media_pages = MediaPage::whereIn('id', $params['media_page_id'])->select('page_id', 'id', 'social_media_detail_id')->where('media_id', 4)->get();

                            $files = explode(',', $request->images)  ?? "";

                            $caption = str_replace("<br />", " ", nl2br($request->caption . "\n" . $request->hashtag));

                            if ($media_pages) {

                                foreach ($media_pages as $val) {
                                    $socialMediaData = SocialMediaDetail::where('user_id', $user_id)->where('media_id', $value->id)->where('id', $val->social_media_detail_id)->first();
                                    $page_ids = [];
                                    if (count($files) > 1) {
                                        foreach ($files as $file) {
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
                                            // if (isset($mediaRes['id'])) $page_ids[] = $mediaRes['id'];
                                            if (!isset($mediaRes['id'])) {
                                                throw new Exception($mediaRes['error']['message']);
                                            } else {
                                                $page_ids[] = $mediaRes['id'];
                                            }
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
                                        flash('Please try after sometime to share the post on the Instagram')->error();
                                    }
                                }
                            }
                            break;
                        case 'Google My Business':
                            $params = $request->all();
                            $user_id = auth()->user()->parent_id ?? auth()->id();
                            // $socialMediaData = SocialMediaDetail::where('user_id', $user_id)->where('media_id', $value->id)->first();
                            $media_pages = MediaPage::whereIn('id', $params['media_page_id'])->select('page_id', 'id', 'social_media_detail_id')->where('media_id', 5)->get();

                            $files = $request->images !== null ? explode(',', $request->images)  : [];

                            if ($media_pages) {
                                $payload = [
                                    'summary' => str_replace("<br />", " ", nl2br($request->caption ?? '' . "\n" . $request->hashtag ?? '')),
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
            if ($request->schedule_date) {
                flash('Schedule post created successfully!')->success();
            } else {
                flash('Post created successfully!')->success();
            }
            DB::commit();
        } catch (\Throwable $th) {
            // dd($th->getMessage(), $th->getLine(), $th->getFile());
            DB::rollBack();
            flash($th->getMessage())->error();
        }
        return redirect(route('posts.index'));
    }
    public function edit($id)
    {
        $temps = PostTempImage::where('user_id', Auth::user()->id)->get();

        foreach ($temps as $temp) {
            if ($temp->upload_file) {
                if (Storage::exists($temp->upload_file)) {
                    Storage::delete($temp->upload_file);
                }
            }
            if ($temp->thumbnail) {
                if (Storage::exists($temp->thumbnail)) {
                    Storage::delete($temp->thumbnail);
                }
            }
            $temp->delete();
        }
        $user = auth()->user();
        $media = Media::where('type', 'social')->whereHas('mediaPages', function ($q) use ($user) {
            $q->whereHas('userMediaPages', function ($q) use ($user) {
                $q->where('user_id', $user->id);
                // $q->whereNotIn('media_id', [6, 7]);
                $q->where('is_deleted', 'n');
            });
        })->with(['mediaPages' => function ($q) use ($user) {
            $q->whereHas('userMediaPages', function ($q) use ($user) {
                $q->where('user_id', $user->id);
                // $q->whereNotIn('media_id', [6, 7]);
                $q->where('is_deleted', 'n');
            });
        }])->get();
        $post = Post::where('id', $id)->first();
        $max_file_size = substr(ini_get('upload_max_filesize'), 0, -1);
        $social_media_video_size = config('utility.social_media_file_size_limit');
        return view('frontend.pages.posts.edit', compact('post', 'media', 'max_file_size', 'social_media_video_size'))->withTitle('Edit Post');
    }
    public function update(PostRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $post = Post::where('id', $id)->firstOrFail();

            $request['user_id'] = Auth::user()->id ?? "";
            // dd($request->all());

            // $schedule_time  = date("H:i:s", strtotime($request->schedule_time));
            if (!empty($request->schedule_time)) {
                $request['schedule_time'] = Carbon::createFromFormat('g:i A',  $request->schedule_time)->format('H:i:s');
            }
            if (!empty($request->schedule_date)) {
                $request['schedule_date'] = Carbon::createFromFormat('m/d/Y', $request->schedule_date)->format('Y-m-d');
            }

            // Google Fields Update
            $request['call_to_action_type'] = $request->action_type;

            //if not getting from the cookies then we need to australia/brisbane
            $timeZone = isset($_COOKIE['timeZone']) ? $_COOKIE['timeZone'] : "Australia/Brisbane";

            if (!empty($request->schedule_date) || !empty($request->schedule_time)) {
                // $request['schedule_date_time'] = $request['schedule_date'] . ' ' . $request['schedule_time'];
                $dateTime = $request['schedule_date'] . ' ' . $request['schedule_time'];
                $request['schedule_date_time'] = convertUTC($dateTime, $timeZone);
            }
            $post = $post->fill($request->except('upload_file', 'thumbnail'));

            if (strpos($request->thumbnail_image, 'thumbnail') !== false) {
                $request['thumbnail_image'] = substr($request->thumbnail_image, strpos($request->thumbnail_image, 'thumbnail'));
            } else {
                $request['thumbnail_image'] = substr($request->thumbnail_image, strpos($request->thumbnail_image, 'upload_file'));
            }
            if (!empty($request->remove_upload_image)) {
                if ($post->upload_file == null) {
                    $images = explode(',', $request->remove_upload_image);
                    foreach ($images as $image) {
                        if (Storage::exists($image)) {
                            Storage::delete($image);
                        }
                        PostImage::where('upload_image_file', $image)->first()->delete();
                    }
                } else {
                    $image = $request->remove_upload_image;
                    if (Storage::exists($image)) {
                        Storage::delete($image);
                    }
                    $post->upload_file = null;
                }
            }
            if (!empty($request->remove_thumbnail_image)) {

                if (Storage::exists($request->remove_thumbnail_image)) {
                    Storage::delete($request->remove_thumbnail_image);
                }
                $post->thumbnail = null;
            }
            if (!empty($request->images)) {
                $images = $request->images ?? "";

                if ($post->upload_file == null) {
                    $images = explode(',', $images);
                    foreach ($images as $value) {
                        $image_type = $value ? File::extension($value) : "";
                        if ($image_type == "mp4") {
                            $post->upload_file = $value;
                        } else {
                            $image = PostImage::create(['post_id' => $post->id, 'upload_image_file' => $value]);
                        }
                        PostTempImage::where('upload_file', $value)->delete();
                    }
                } else {
                    $image_type = isset($images) ? File::extension($images) : "";
                    $post->upload_file = $images;

                    PostTempImage::where('upload_file', $images)->delete();
                }
            }

            if (!empty($request->all_images)) {
                $image_path = explode(',', $request->all_images);
                foreach ($post->images as $image) {
                    $position = (array_search($image->upload_image_file, $image_path) + 1);
                    $image->position = $position;
                    $image->save();
                }
            }

            if (!empty($request->thumbnail_image)) {
                $post->thumbnail = $request->thumbnail_image ?? "";

                PostTempImage::where('thumbnail', $request->thumbnail_image)->delete();
            }

            $post->save();
            if (!empty($request->media_page_id)) {
                $media_page_ids = $request->media_page_id;
                if (!empty($media_page_ids)) {
                    foreach ($media_page_ids as $media_page) {
                        $media_page_id = MediaPage::where('id', $media_page)->first();
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

            flash('Schedule Post updated successfully!')->success();
            DB::commit();
            return redirect(route('posts.index'));
        } catch (Exception $e) {
            // dd($e->getMessage(), $e->getLine());
            DB::rollback();
            flash('Schedule Post updated successfully!')->success();
        }
    }
    public function paginatePosts(Request $request)
    {
        $type = $request->type ?? "";
        $startDate = date("Y-m-d ", strtotime($request->startDate));
        $endDate = date("Y-m-d ", strtotime($request->endDate));
        $parent_ids = null;
        $user = Auth::user();
        $parent_id = $user->parent_id ?? "";

        if (!empty($parent_id)) {
            $parent_user = User::where('id', $parent_id)->first();
            $parent_ids = $parent_user->parents->pluck('id')->toArray();
            $parent_ids[] = $parent_user->id;
        } else {
            $parent_ids = $user->parents->pluck('id')->toArray();
            $parent_ids[] = $user->id;;
        }

        $posts = Post::where('is_active', 'y');
        $posts = $posts->whereIn('user_id', $parent_ids);
        $var = (Carbon::parse(now())->format('Y-m-d H:i:s'));
        if (($type == 'posts') && empty($request->startDate)  && empty($request->endDate)) {
            $posts = $posts->whereBetween('created_at', [Carbon::now()->subDays(7)->toDateString() . ' 00:00:00', Carbon::parse(now())->toDateString() . ' 23:59:59']);
            $posts = $posts->where(function ($q) use ($var) {
                // $q->where('schedule_date_time', '<=', $var)->orWhere('schedule_date_time', null);
                $q->orWhere('schedule_date_time', null);
            });
            // $posts = $posts->where(function ($q) use ($var) {
            //     $q->where('schedule_date_time', '<=', $var)->orWhere('schedule_date_time', null);
            // });
            $posts = $posts->orderBy('created_at', 'desc');
        } elseif (($type == 'scheduled_posts') && empty($request->startDate)  && empty($request->endDate)) {
            $posts = $posts->whereBetween('schedule_date', [Carbon::parse(now())->toDateString(), Carbon::today()->addDays(7)]);
            // $posts = $posts->orWhere(function ($q) use ($var) {
            //     $q->where('schedule_date_time', '>=', $var);
            // });
            $posts = $posts->orderBy('schedule_date_time', 'desc');
        } elseif ((!empty($request->startDate)  && !empty($request->endDate)) && $type == 'posts') {
            $posts = $posts->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $posts = $posts->where(function ($q) use ($var) {
                $q->where('schedule_date_time', '<=', $var)->orWhere('schedule_date_time', null);
            });
            $posts = $posts->orderBy('created_at', 'desc');
        } elseif ((!empty($request->startDate)  && !empty($request->endDate)) && $type == 'scheduled_posts') {
            $posts = $posts->whereBetween('schedule_date', [$startDate, $endDate]);
            // $posts = $posts->where(function ($q) use ($var) {
            //     $q->where('schedule_date_time', '>=', $var);
            // });
            $posts = $posts->orderBy('schedule_date_time', 'desc');
        }
        if ($request->mediaPagesId) {
            $mediaPages = $request->mediaPagesId;
            $posts->whereHas('postMedia', function ($q) use ($mediaPages) {
                $q->whereIn('media_page_id', $mediaPages);
            });
        }
        // if ($type == 'posts') {
        //     // $posts = $posts->select('posts.*')->whereRaw(\DB::raw('concat(posts.schedule_date, " ", posts.schedule_time) <= '."'$var'"))->orWhere('schedule_date',null);
        //     $posts = $posts->where(function ($q) use ($var) {
        //         $q->where('schedule_date_time', '<=', $var)->orWhere('schedule_date_time', null);
        //     });
        //     $posts = $posts->orderBy('created_at', 'desc');
        // } else{
        //     // $posts = $posts->select('posts.*',\DB::raw('cast(concat(posts.schedule_date, " ", posts.schedule_time) as datetime) as schedule'))->having('schedule',">",Carbon::parse(now())->format('Y-m-d H:i:s'));
        //     $posts = $posts->where(function ($q) use ($var) {
        //         $q->where('schedule_date_time', '>=', $var);
        //     });
        //     $posts = $posts->orderBy('schedule_date_time', 'desc');
        // }
        $posts = $posts->with(['postMedia', 'postMedia.media', 'images'])->paginate(10);
        $post['data'] = view('frontend.pages.posts.data', compact('posts', 'type'))->render();
        $post['pages'] = view('frontend.pages.posts.page', compact('posts'))->render();
        return response()->json($post);
    }

    public function destroy(Request $request)
    {

        $id = $request->id ?? "";
        $page = $request->page ?? "";
        $post = Post::where('id', $id)->first();
        if ($post->upload_file) {
            if (Storage::exists($post->upload_file)) {
                Storage::delete($post->upload_file);
            }
        }
        if ($post->thumbnail) {
            if (Storage::exists($post->thumbnail)) {
                Storage::delete($post->thumbnail);
            }
        }
        $post->delete();
        $content = array('status' => 200, 'message' => trans('flash_message.delete', ['entity' => 'Post']));
        return response()->json($content);
    }
    public function postNow(Request $request)
    {
        DB::beginTransaction();
        try {
            $post = Post::where('id', $request->id)->first();
            $post->schedule_date = null;
            $post->schedule_time = null;
            $post->schedule_date_time = null;
            $post->created_at = Carbon::now();
            $post->save();
            $post->load('postMedia', 'images');
            // Post Now in Social Media
            // foreach ($this->posts as $post) {
            $post_media_ids = $post->postMedia->pluck('media_id')->toArray();
            $medias = Media::whereIn('id', $post_media_ids)->get();
            foreach ($medias as $media) {
                switch ($media->name) {
                    case 'X(Twitter)':

                        $post_medias = PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->pluck('media_page_id')->toArray();
                        $media_pages = MediaPage::whereIn('id', $post_medias)->get();
                        $user_id = auth()->user()->parent_id ?? auth()->id();

                        foreach ($media_pages as $media_page) {

                            try {

                                $socialMedia = SocialMediaDetail::where('media_id', $media->id)->where('id', $media_page->social_media_detail_id)->where('user_id', $user_id)->first();
                                if (isset($socialMedia->id)) {


                                    // $size = (Storage::size($post->upload_file)) ?? "";
                                    // $file = $post->upload_file  ?? "";
                                    $files = $post->upload_file ? collect($post->upload_file)->toArray() : $post->images->pluck('upload_image_file')->toArray();
                                    // dd($files);
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
                                        if (isset($result->errors) && $result->errors[0]->code === 89) throw new Exception('Your X(Twitter) Token is Expired, You need to Reconnect your X(Twitter) Account For Post in X(Twitter).', 89);
                                    }
                                }
                            } catch (\Exception $ex) {
                                if ($ex->getCode() == 89)
                                    flash($ex->getMessage())->error();
                                else
                                    flash('Technical issue in X(Twitter), Please try after sometime to Post.')->error();
                                PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->where('media_page_id', $media_page->id)->update([
                                    'is_error' => 'y',
                                    'error_message' => $ex->getMessage(),
                                ]);
                                Log::info('X(Twitter) ' . $ex->getMessage());
                            }
                        }
                        break;
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
                                // Get Social Media Details of Post User.
                                $socialMedia = SocialMediaDetail::where('id', $media_page->social_media_detail_id)->where('user_id', $user_id)->first();
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
                                    $token = $this->getFacebookPagesToken($socialMedia, $media_page->page_id);
                                    if ($token) {
                                        // $parameter = [
                                        //     'source'    => $facebook->fileToUpload(Storage::disk('public')->getAdapter()->getPathPrefix() . '' . $file),
                                        // ];
                                        // if ($image_type == "mp4") {
                                        //     $url = '/' . $media_page->page_id . '/videos';
                                        //     $parameter['description'] = str_replace("<br />", " ", nl2br($post->caption . "\n" . $post->hashtag));
                                        // } else {
                                        //     $url = '/' . $media_page->page_id . '/photos';
                                        //     $parameter['caption'] = str_replace("<br />", " ", nl2br($post->caption . "\n" . $post->hashtag));
                                        // }
                                        // $response = $facebook->post($url, $parameter, $token)->getGraphNode()->asArray();
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
                                                    Log::debug('Facebook Response --- ' . json_encode($response));
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
                                                Log::debug('Facebook Response --- ' . json_encode($response));
                                                if (isset($response['id'])) flash('Facebook Multiple Image Post Succesfully')->success();
                                                else throw new Exception($response['error']['message']);
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
                                            Log::debug('Facebook Response --- ' . json_encode($response));
                                            if (isset($response['id'])) {
                                            } else throw new Exception($response['error']['message']);
                                        } else {
                                            $response = $facebook->post($url, $parameter, $token)->getGraphNode()->asArray();
                                            Log::debug('Facebook Post Now Response --- ' . json_encode($response));
                                            if (isset($response['id'])) {
                                            } else throw new Exception($response['error']['message']);
                                        }
                                    }
                                }
                                // if (!$response) throw new Exception();
                            } catch (FacebookSDKException $e) {
                                PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->where('media_page_id', $media_page->id)->update([
                                    'is_error' => 'y',
                                    'error_message' => $e->getMessage(),
                                ]);
                                Log::debug('Facebook Error Post Now --- ' . $e->getMessage());
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
                        // $social_media = SocialMediaDetail::where('user_id', $user_id)->where('media_id', $media->id)->first();
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
                                    PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->where('media_page_id', $val->id)->update([
                                        'is_error' => 'y',
                                        'error_message' => $e->getMessage(),
                                    ]);
                                    Log::info('Linkedin' . $e->getMessage());
                                    flash('Please try after sometime to share the post on the linkedin')->error();
                                }
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
                        // $image_type = File::extension($post->upload_file) ?? "";
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
                                        Log::debug('Instagram Response --- ' . json_encode($contentRes));
                                        if (!isset($contentRes['id']) && isset($contentRes['error'])) {
                                            throw new Exception($contentRes['error']['message']);
                                        }
                                    }
                                } catch (Exception $e) {
                                    PostMedia::where('post_id', $post->id)->where('media_id', $media->id)->where('media_page_id', $val->id)->update([
                                        'is_error' => 'y',
                                        'error_message' => $e->getMessage(),
                                    ]);
                                    Log::info('Instagram Error ---- ' . $e->getMessage());
                                }
                            }
                        }
                        break;
                    case 'Google My Business':
                        $params = $post->toArray();
                        $user_id = auth()->user()->parent_id ?? auth()->id();

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
            DB::commit();
            $content = array('status' => 200, 'message' => "Post now successfully!");
            return response()->json($content);
        } catch (\Exception $ex) {
            DB::rollBack();
            $content = array('status' => 200, 'message' => $ex->getMessage());
            return response()->json($content);
        }
    }

    public function storeimage(Request $request)
    {
        $user = Auth::user();
        $path = '';
        $created_thumbnail = '';
        $page_ids = $request->media_page_id ? explode(',', $request->media_page_id) : '';

        try {
            if ($request->file('file')) {
                if ($request->type == 'upload_file') {
                    $path = $request->file('file')->store('upload_file');
                    $data[] = array('upload_file' => $path, 'user_id' => $user->id ?? "", 'custom_id' => getUniqueString('post_temp_images'));
                    $store = PostTempImage::insert($data);
                }
                // if ($page_ids !== '') {
                //     $is_instagram_media = MediaPage::whereIn('id', $page_ids)->where('media_id', 4)->first();
                //     if ($is_instagram_media) {
                //         $isMediaIsValid = $this->checkMediaForInstagram($path, $is_instagram_media->page_id);
                //         if (!$isMediaIsValid['status'] !== 200) {
                //             return response()->json(['errorType' => 'instagram_image', 'message' => $isMediaIsValid['error_user_msg'], 'message_title' => $isMediaIsValid['error_user_title']], 400);
                //         }
                //     }
                // }

                $fileName = substr($path, strpos($path, '/') + 1);
                $extension = pathinfo($fileName, PATHINFO_EXTENSION);

                if (!in_array($extension, ['jpg', 'jpeg', 'png', 'mp4'])) {
                    return response()->json('error', 400);
                }
                if ($extension != 'jpg' && $extension != 'jpeg' && $extension != 'png') {
                    $created_thumbnail = str_replace($extension, 'jpg', $fileName);
                    $vid =  VideoThumbnail::createThumbnail(
                        storage_path('app/public/' . $path),
                        storage_path('app/public/thumbnail/'),
                        $created_thumbnail,
                        2,
                        320,
                        320
                    );
                    //    $path = 'thumbnail/'.$created_thumbnail ;
                }
                // $data[] = array('upload_file' => $path, 'user_id' => $user->id ?? "", 'custom_id' => getUniqueString('post_temp_images'), 'thumbnail' =>$created_thumbnail != '' ? $created_thumbnail : $path);
                // $strore = PostTempImage::insert($data);

                if ($path) {
                    return response()->json(['path' => $path, 'created_thumbnail' => $extension == 'mp4' ? Storage::url('thumbnail/' . $created_thumbnail) : ''], 200);
                } else {
                    return response()->json('error', 400);
                }
            } else {
                return response()->json('error', 400);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    public function getImages(Request $request)
    {

        $file = [];
        $post = Post::whereCustomId($request->post_id)->with('images')->firstOrFail();
        if ($post->upload_file != null) {
            if (\File::extension($post->upload_file) == 'mp4') {
                $file[0]['name'] = $post->upload_file;
                $file[0]['path'] = generateURL($post->thumbnail);
                $file[0]['size'] = filesize(public_path('storage/' . $post->upload_file));
                $file[0]['type'] = (\File::extension($post->upload_file));
            } else {
                $file[0]['name'] = $post->upload_file;
                $file[0]['path'] = generateURL($post->upload_file);
                $file[0]['size'] = filesize(public_path('storage/' . $post->upload_file));
                $file[0]['type'] = (\File::extension($post->upload_file));
            }
        } else {
            foreach ($post->images->pluck('upload_image_file')->toArray() as $key => $image) {
                $file[$key]['name'] = $image;
                $file[$key]['path'] = generateURL($image);
                $file[$key]['type'] = (\File::extension($image));
                $file[$key]['size'] = filesize(public_path('storage/' . $image));
            }
        }
        return response()->json($file);
    }

    public function deleteimage(Request $request)
    {
        $content = ['status' => 204, 'message' => "something went wrong"];
        if ($request->type == "upload_file") {
            if (!empty($request->path)) {
                $data = PostTempImage::where('upload_file', $request->path)->first();
                if (!empty($data)) {
                    if (Storage::exists($data->upload_file)) {
                        Storage::delete($data->upload_file);
                    }
                    PostTempImage::where('upload_file', $request->path)->delete();
                    $content['message'] = 'File deleted successfully';
                    $content['path'] = $data->upload_file;
                    $content['success'] = 'true';
                } else {
                    $content['success'] = 'false';
                    $content['path'] = $request->path;
                    $content['message'] = 'The asset file will be deleted on submit';
                }
            }

            // return response()->json([ 'path' => $data->upload_file], 200);
        } else {
            if (!empty($request->path)) {
                $data = PostTempImage::where('thumbnail', $request->path)->first();
                if (!empty($data)) {
                    if (Storage::exists($data->thumbnail)) {
                        Storage::delete($data->thumbnail);
                    }
                    PostTempImage::where('thumbnail', $request->path)->delete();
                    $content['message'] = 'File deleted successfully';
                    $content['path'] = $data->thumbnail;
                }
            }
        }
        $content['status'] = 200;

        return response()->json($content);
    }

    public function postPreview(Request $request)
    {
        $id = $request->id ?? "";
        $caption = $request->caption ?? "";
        $hashtag = $request->hashtag ?? "";
        $schedule_date = $request->schedule_date ?? "";
        $schedule_time = $request->schedule_time ?? "";
        $upload_file = $request->upload_file ?? "";
        $thumbnail = $request->thumbnail ?? "";
        // if (strpos($request->thumbnail, 'thumbnail') !== false) {
        //     $thumbnail = substr($request->thumbnail, strpos($request->thumbnail, 'thumbnail'));
        // } else {
        //     $thumbnail = substr($request->thumbnail, strpos($request->upload_file, 'upload_file'));
        // }

        $media_page_id = $request->media_page_id ?? "";
        $pages = !empty($media_page_id) ? MediaPage::whereIn('id', $media_page_id)->get() : '';
        $user = Auth::user();
        $post = Post::where('id', $id)->with('images')->first();
        $media = !empty($media_page_id) ? $pages->pluck('media_id')->toArray() : '';
        $preview['media'] = $media ?? "";
        $preview['data'] = view('frontend.pages.posts.preview', compact('caption', 'hashtag', 'user', 'media_page_id', 'pages', 'thumbnail', 'post', 'schedule_date', 'upload_file', 'schedule_time'))->render();
        return response()->json($preview);
    }

    public function checkMediaForInstagram($file, $media_page_id): array
    {
        $content = ['message' => '', 'title' => '', 'status' => 200, 'error_user_msg' => ''];
        $socialMediaData = SocialMediaDetail::where('user_id', auth()->user()->parent_id ?? auth()->id())->where('media_id', 4)->first();
        $image_type = File::extension($file) ?? "";
        if ($image_type == "mp4") {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v18.0/" . $media_page_id . "/media");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                "media_type=VIDEO&video_url=" . Storage::url($file) . "&access_token=" . $socialMediaData->token . ""
            );

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close($ch);
            $mediaRes = json_decode($server_output, true);
            if (isset($mediaRes)) {
                $content = [
                    'status' => 400, 'message' => $mediaRes['error']['message'], 'error_user_msg' => $mediaRes['error']['error_user_msg'], 'error_user_title' => $mediaRes['error']['error_user_title'],
                ];
            }
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v18.0/" . $media_page_id . "/media");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                "image_url=https://stackedup.com.au/storage/upload_file/H4ZrkV93h69fKEazi6y5PMQ7LyA5h2v7gRuuwZH3.png&access_token=" . $socialMediaData->token,
            );

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close($ch);
            $mediaRes = json_decode($server_output, true);
            if (isset($mediaRes['error'])) {
                $content = [
                    'status' => 400, 'message' => $mediaRes['error']['message'], 'error_user_msg' => $mediaRes['error']['error_user_msg'], 'error_user_title' => $mediaRes['error']['error_user_title'],
                ];
            }
        }
        return $content;
    }

    public function storeCustomThumbnail(Request $request)
    {
        $response = ['status' => 400, 'data' => 'Something went wrong'];
        DB::beginTransaction();
        try {
            $thumbanil_image = '';
            if ($request->has('file')) {
                $thumbanil_image = $request->file('file')->store('thumbnail');
            }

            PostTempImage::create([
                'thumbnail' => $thumbanil_image,
                'user_id'   => auth()->id()
            ]);
            $response['status'] = 200;
            $response['data'] = ['path' => $thumbanil_image];
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
        return response()->json($response['data'], $response['status']);
    }

    public function getFacebookPagesToken($user, $page_id)
    {
        $params = 'name,access_token';
        $url = "https://graph.facebook.com/" . config('utility.DEFAULT_FACEBOOK_API_VERSION') . "/{$page_id}?fields={$params}&access_token={$user->token}";
        $response = Http::get($url);
        if ($response->failed()) $response->throw();
        $data = $response->json();
        return $data['access_token'];
    }
}
