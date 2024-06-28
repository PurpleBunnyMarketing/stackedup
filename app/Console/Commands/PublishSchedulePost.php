<?php

namespace App\Console\Commands;

use App\Jobs\ShareOnSocialMedia;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishSchedulePost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule_posts:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish All the schedule posts on that schedule time and date';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $date = Carbon::parse(now()->toDateString())->format('Y-m-d');
        // $time = Carbon::parse(now()->toTimeString())->format('H:i:s');
        $time = Carbon::parse(now()->toTimeString())->format('H:i');
        // $posts = Post::where([['schedule_date', '=', $date], ['schedule_time', '=', $time], ['schedule_date_time', '=', $dateTime]])->with('postMedia.media', 'postMedia.mediaPage')->get();
        Log::info($date . ' - ' . $time);
        $posts = Post::where('schedule_date_time', $date . ' ' . $time)->with('postMedia.media', 'postMedia.mediaPage', 'images')->get();
        if ($posts->count() == 0) {
            Log::debug('No posts found');
        } else {
            ShareOnSocialMedia::dispatch($posts);
        }


        // foreach ($posts as $post) {
        //     $media_ids = $post->postMedia->pluck('media_id')->toArray();
        //     $medias = Media::whereIn('id', $media_ids)->get();
        //     foreach ($medias as $media) {
        //         switch ($media->name) {
        //             case 'Facebook':
        //                 $image_type = File::extension($post->upload_file) ?? "";
        //                 $file = $post->upload_file  ?? "";

        //                 $facebook = new Facebook([
                            //     'app_id' => config('utility.FACEBOOK_APP_ID'),
                            //     'app_secret' => config('utility.FACEBOOK_APP_SECRET'),
                            //     'default_graph_version' => config('utility.DEFAULT_FACEBOOK_API_VERSION'),
                            // ]);

        //                 $post_medias = $post->postMedia->pluck('media_page_id')->toArray();

        //                 $media_pages = MediaPage::whereIn('id', $post_medias)->get();

        //                 foreach ($media_pages as $media_page) {
        //                     try {
        //                         $token = $url = '';
        //                         $user_id = auth()->user()->parent_id ?? auth()->id();
        //                         $socialMedia = SocialMediaDetail::where('media_id', $media->id)->where('user_id', $post->user_id)->first();

        //                         // get Page list for access_token
        //                         $response = $facebook->get('/' . $socialMedia->social_id . '/accounts?fields=name,access_token', $socialMedia->token);
        //                         $facebook_pages = $response->getGraphEdge()->asArray();

        //                         if ($facebook_pages) {
        //                             foreach ($facebook_pages as $facebook_page) {
        //                                 if ($facebook_page['id'] == $media_page->page_id) {
        //                                     $token =  $facebook_page['access_token'];
        //                                 }
        //                             }
        //                         }

        //                         $parameter = [
        //                             'source'    => $facebook->fileToUpload(Storage::disk('public')->getAdapter()->getPathPrefix() . '' . $file),
        //                         ];
        //                         if ($image_type == "mp4") {
        //                             $url = '/' . $media_page->page_id . '/videos';
        //                             $parameter['description'] = str_replace("<br />", " ", nl2br($post->caption . "\n" . $post->hashtag));
        //                         } else {
        //                             $url = '/' . $media_page->page_id . '/photos';
        //                             $parameter['caption'] = str_replace("<br />", " ", nl2br($post->caption . "\n" . $post->hashtag));
        //                         }
        //                         $response = $facebook->post($url, $parameter, $token)->getGraphNode()->asArray();
        //                     } catch (FacebookSDKException $e) {
        //                         dd($e->getMessage());
        //                         // flash('Please try after sometime to share the post on the facebook')->error();
        //                     } catch (Exception $e) {
        //                         dd($e->getMessage());
        //                     }
        //                 }
        //                 break;
        //             case 'Linkedin':

        //                 break;
        //             case 'Twitter':

        //                 break;
        //         }
        //     }
        // }
    }
}
