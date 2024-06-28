<?php

namespace App\Http\Controllers;

use Session;
use Exception;
use App\Models\Media;
use Facebook\Facebook;
use App\Models\Analytics;
use App\Models\MediaPage;
use Illuminate\Http\Request;
use App\Models\SocialMediaDetail;
use App\Services\LinkedInService;
use App\Services\InstagramService;
use Illuminate\Support\Facades\DB;
use App\Services\FaceBookAdsService;
use Illuminate\Support\Facades\Hash;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Services\GoogleAnalyticsService;
use App\Services\FacebookAnalyticService;
use App\Services\GoogleAdsService;
use App\Services\GoogleMyBusinessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

set_time_limit(0);

class AnalyticsController extends Controller
{
    private $timezone = 'Australia/Brisbane';

    public function index(Request $request)
    {
        $user = auth()->user()->parentUser ?? auth()->user();
        $analytic =  $user->Analytic;
        $connectedSocialMedia =  $user->socialMediaDetail;
        $media = isset($request->type) ? $request->type : '';

        // for instagram please remove where condition
        $media = Media::whereHas('mediaPages', function ($q) use ($user) {
            // $media = Media::where('id', '<>', '4')->whereHas('mediaPages', function ($q) use ($user) {
            $q->whereHas('userMediaPages', function ($q) use ($user) {
                $q->where('user_id', $user->id);
                $q->where('is_deleted', 'n');
            });
        })->get();

        $update_data = 0;
        $check_update = $this->checkURLHash('filter_data');
        if ($check_update) $update_data = 1;

        $page_update = 0;
        $check_page_ids = $this->changePageIds();
        if ($check_page_ids) $page_update = 1;

        return view('frontend.pages.analytic.index', compact('analytic', 'connectedSocialMedia', 'media', 'update_data', 'page_update'))->withTitle('Analytic');
    }

    public function filterAnalytic(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:facebook,facebook_ads,linkedin,twitter,instagram,google_business,google_analytics,google_ads'
            ]);

            if (isset($request->type) && $request->type == 'twitter') {
                $request->validate([
                    'page_id' => 'required'
                ]);
                $twitterRes = $this->TwitterAnalitics();
                if (isset($twitterRes['status']) && $twitterRes['status'] == 'error') {
                    flash($twitterRes['message'])->error();
                }
                if (isset($twitterRes['status']) && $twitterRes['status'] == 'success') {
                    $twitterRes = $this->TwitterFeed();
                }
            }
            if (isset($request->type) && $request->type == 'linkedin') {
                $request->validate([
                    'start_date' => 'nullable|date|date_format:m/d/Y',
                    'end_date' => 'nullable|date|date_format:m/d/Y|after:start_date',
                    'page_id' => 'required'
                ]);

                $check_data = $this->checkURLHash('linkedin_engagment');

                if ($check_data) {
                    $linkeinRes = $this->LinkedInEngagment($request->start_date, $request->end_date);
                    if (isset($linkeinRes['status']) && $linkeinRes['status'] == 'error') {
                        flash($linkeinRes['message'])->error();
                    }
                }
            }

            if (isset($request->type) && $request->type == 'facebook') {

                $request->validate([
                    'start_date' => 'nullable|date|date_format:m/d/Y',
                    'end_date' => 'nullable|date|date_format:m/d/Y|after:start_date',
                    'page_id' => 'required'
                ]);

                $check_data = $this->checkURLHash('facebook_likes');
                if ($check_data) {
                    $facebookRes = $this->FacebookLikes($request->start_date, $request->end_date);
                    if (isset($facebookRes['status']) && $facebookRes['status'] == 'error') {
                        flash($facebookRes['message'])->error();
                    }
                }
                if (isset($facebookRes['status']) && $facebookRes['status'] == 'success') {
                    $this->FacebookPosts();
                }
            }

            if (isset($request->type) && $request->type == 'facebook_ads') {
                $request->validate([
                    'start_date' => 'nullable|date|date_format:m/d/Y',
                    'end_date' => 'nullable|date|date_format:m/d/Y|after:start_date',
                    'page_id' => 'required'
                ]);

                // $linkeinRes = $this->facebookAdsReporting($request->start_date, $request->end_date);
            }

            if (isset($request->type) && $request->type == 'instagram') {
                $request->validate([
                    'start_date' => 'nullable|date|date_format:m/d/Y|before_or_equal:end_date',
                    'end_date' => 'nullable|date|date_format:m/d/Y|after_or_equal:start_date',
                    'page_id' => 'required'
                ]);
            }
            if (isset($request->type) && ($request->type == 'google_business' || $request->type == 'google_ads')) {
                $request->validate([
                    'start_date' => 'nullable|date|date_format:m/d/Y',
                    'end_date' => 'nullable|date|date_format:m/d/Y|after:start_date',
                    'page_id' => 'required'
                ]);
                // $isDataUpdated = $this->googleMyBusinessAnalyticsData();
            }
            if (isset($request->type) && $request->type == 'google_analytics') {
                $request->validate([
                    'start_date'    => 'nullable|date|date_format:m/d/Y',
                    'end_date'      => 'nullable|date|date_format:m/d/Y|after:start_date',
                    'page_id'       => 'required'
                ]);
            }
            if (isset($request->type) && $request->type == 'facebook_ads') {
                $request->validate([
                    'start_date'    => 'nullable|date|date_format:m/d/Y',
                    'end_date'      => 'nullable|date|date_format:m/d/Y|after:start_date',
                    'page_id'       => 'required'
                ]);
            }
        } catch (\Throwable $th) {
            // dd($th->getMessage(), $th->getLine(), $th->getFile());
            flash('something went wrong')->error();
            return redirect()->back();
        }

        if (request()->expectsJson()) {
            return [
                'type' => $request->type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'page_id' => $request->page_id
            ];
        } else {
            return redirect()->route('analytics.index', [
                'type' => $request->type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'page_id' => $request->page_id
            ]);
        }
    }

    public function FacebookLikes()
    {
        try {
            $user = auth()->user()->parentUser ?? auth()->user();
            $socialMedia = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 1)->get();

            if (count($socialMedia) == 0) {
                $resArr = [
                    'status' => 'error',
                    'message' => 'Please connect facebook in your account.'
                ];
                return $resArr;
            }

            date_default_timezone_set('Australia/Brisbane');
            $start = request()->start_date ? \Carbon\Carbon::parse(request()->start_date)->startOfDay()->timestamp : \Carbon\Carbon::now()->startOfMonth()->timestamp;
            $end = request()->end_date ? \Carbon\Carbon::parse(request()->end_date)->endOfDay()->timestamp  : \Carbon\Carbon::now()->endOfMonth()->addDay()->timestamp;

            $start = strtotime('-1 day', $start);
            $end = strtotime('+1 day', $end);
            $needUpdateData = $this->needToUpdateFacebookData($user, 'facebook_last_updated_at');
            if ($needUpdateData) {
                /** Update 30 min old data */
                $data = FacebookAnalyticService::FacebookLikes($socialMedia, $start, $end);
                if (!isset($data['status'])) {
                    Analytics::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'facebook_like_analytic' => json_encode($data['facebook_like_analytic']),
                            'facebook_last_updated_at' => date('Y-m-d H:i:s', strtotime(NOW()))
                        ]
                    );
                } else {
                    return $data;
                }
            }
            $resArr = [
                'status' => 'success',
                'message' => ''
            ];
            return $resArr;
        } catch (\Exception $e) {
            $resArr = [
                'status' => 'error',
                'message' => 'Something went wrong please try again.'
            ];
            return $resArr;
        }
        return [
            'status' => 'success',
            'message' => ''
        ];
    }

    public function FacebookAnalitics(Request $request)
    {
        $user = auth()->user()->parentUser ?? auth()->user();
        $response =  [
            'data' => 'No data found',
            'status' => 500
        ];
        switch ($request->type) {
            case "facebook_like_analytic":

                $needUpdateData = $this->needToUpdateFacebookData($user, 'facebook_last_updated_at');
                $facebook_like_analytic = FacebookAnalyticService::getFacebookLikeAnalytics($request->start_date, $request->end_date, $needUpdateData);
                $response['data'] = $facebook_like_analytic;
                $response['status'] = 200;
                break;

            case "facebook_engagement":
                $needUpdateData = $this->needToUpdateFacebookData($user, 'facebook_engagement_last_updated_at');
                $facebook_engagement = FacebookAnalyticService::getFacebookEngagementAnalytics($request->start_date, $request->end_date, $needUpdateData);
                $response['data'] = $facebook_engagement;
                $response['status'] = 200;
                break;

            case "facebook_reach":
                $needUpdateData = $this->needToUpdateFacebookData($user, 'facebook_reach_last_updated_at');
                $facebook_reach = FacebookAnalyticService::getFacebookReachAnalytics($request->start_date, $request->end_date, $needUpdateData);
                $response['data'] = $facebook_reach;
                $response['status'] = 200;
                break;

            default:
                $response['data'] = "No data found.";
                $response['status'] = 200;
        }
        echo json_encode($response);
    }
    public function FacebookAnaliticsApi($type)
    {
        try {
            $user = auth()->user()->parentUser ?? auth()->user();
            $socialMedia = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 1)->get();

            if (count($socialMedia) == 0) {
                $resArr = [
                    'status' => 'error',
                    'message' => 'Please connect facebook in your account.'
                ];
                return $resArr;
            }

            $page_id = (request()->page_id && request()->page_id != 'all') ? explode(',', request()->page_id) : null;

            foreach ($socialMedia as $fbmedia) {

                $facebook = new Facebook([
                    'app_id' => config('utility.FACEBOOK_APP_ID'),
                    'app_secret' => config('utility.FACEBOOK_APP_SECRET'),
                    // 'default_graph_version' => config('utility.DEFAULT_FACEBOOK_API_VERSION'),
                ]);
                // $response = $facebook->get('/' . $fbmedia->social_id . '/accounts?fields=name,access_token', $fbmedia->token);
                // $facebook_pages = $response->getGraphEdge()->asArray();
                $facebook_pages = FacebookAnalyticService::getFacebookPages($fbmedia->social_id, $fbmedia->token);
                if (!$facebook_pages) {
                    $resArr = [
                        'status' => 'error',
                        'message' => 'Please connect facebook pages.'
                    ];
                    return $resArr;
                }

                $mediaPagesArr = MediaPage::where([
                    'user_id' => $user->id,
                    'media_id' => 1
                ])->whereHas('userMediaPages', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                    $q->where('is_deleted', 'n');
                })->when($page_id, function ($query) use ($page_id) {
                    $query->whereIn('id', $page_id);
                })->pluck('page_id')->toArray();

                date_default_timezone_set('Australia/Brisbane');
                $start = request()->start_date ? \Carbon\Carbon::parse(request()->start_date)->startOfDay()->timestamp : \Carbon\Carbon::now()->startOfMonth()->timestamp;
                $end = request()->end_date ? \Carbon\Carbon::parse(request()->end_date)->endOfDay()->timestamp  : \Carbon\Carbon::now()->addDay()->endOfMonth()->timestamp;

                switch ($type) {
                    case 'facebook_like':
                        $start = strtotime('-1 day', $start);
                        $end = strtotime('-1 day', $end);
                        if ($facebook_pages) {
                            $facebook_like_analytic = [];
                            foreach ($facebook_pages as $facebook_page) {
                                if (in_array($facebook_page['id'], $mediaPagesArr)) {
                                    $token =  $facebook_page['access_token'];

                                    //Total Likes
                                    $likeresponse = $facebook->get('/' . $facebook_page['id'] . '/insights?metric=page_fans&since=' . $start . '&until=' . $end, $token);
                                    // $likeresponse = $facebook->get('/' . $facebook_page['id'] . '/insights?metric=page_actions_post_reactions_like_total&since=' . $start . '&until=' . $end, $token);
                                    $statuscode = $likeresponse->getHttpStatusCode();
                                    $likeresponsearr = ($statuscode == 200) ? $likeresponse->getGraphEdge()->asArray() : [];


                                    //Paid/Organic Likes
                                    // $paid_organic_like_response = $facebook->get('/' . $facebook_page['id'] . '/insights?metric=page_posts_impressions_paid_unique,page_posts_impressions_organic_unique&since=' . $start . '&until=' . $end, $token);
                                    // dump($start, $end);

                                    // dump($paid_organic_start, $paid_organic_end);
                                    $paid_organic_like_response = $facebook->get('/' . $facebook_page['id'] . '/insights?metric=page_fans_by_like_source_unique&since=' . $start . '&until=' . $end, $token);
                                    // dump($paid_organic_like_response);
                                    $statuscode = $paid_organic_like_response->getHttpStatusCode();
                                    $paid_organic_like_responsearr = ($statuscode == 200) ?  $paid_organic_like_response->getGraphEdge()->asArray()  : [];
                                    // dd($paid_organic_like_responsearr);

                                    //Age-Gender
                                    $page_fans_gender_age_response = $facebook->get('/' . $facebook_page['id'] . '/insights?metric=page_fans_gender_age&since=' . $start . '&until=' . $end, $token);
                                    $statuscode = $page_fans_gender_age_response->getHttpStatusCode();
                                    $page_fans_gender_age_responsearr = ($statuscode == 200) ?  $page_fans_gender_age_response->getGraphEdge()->asArray()  : [];

                                    //audience grouth
                                    $page_fan_adds_response = $facebook->get('/' . $facebook_page['id'] . '/insights?metric=page_fan_adds&since=' . $start . '&until=' . $end, $token);
                                    $statuscode = $page_fan_adds_response->getHttpStatusCode();
                                    $page_fan_adds_responsearr = ($statuscode == 200) ?  $page_fan_adds_response->getGraphEdge()->asArray()  : [];


                                    //audience Removed
                                    $page_fan_removes_response = $facebook->get('/' . $facebook_page['id'] . '/insights?metric=page_fan_removes&since=' . $start . '&until=' . $end, $token);
                                    $statuscode = $page_fan_removes_response->getHttpStatusCode();
                                    $page_fan_removes_responsearr = ($statuscode == 200) ?  $page_fan_removes_response->getGraphEdge()->asArray()  : [];


                                    //city
                                    $page_fans_city_response = $facebook->get('/' . $facebook_page['id'] . '/insights?metric=page_fans_city&since=' . $start . '&until=' . $end, $token);
                                    $statuscode = $page_fans_city_response->getHttpStatusCode();
                                    $page_fans_city_responsearr = ($statuscode == 200) ?  $page_fans_city_response->getGraphEdge()->asArray()  : [];

                                    //country
                                    $page_fans_country_response = $facebook->get('/' . $facebook_page['id'] . '/insights?metric=page_fans_country&since=' . $start . '&until=' . $end, $token);
                                    $statuscode = $page_fans_country_response->getHttpStatusCode();
                                    $page_fans_country_responsearr = ($statuscode == 200) ?  $page_fans_country_response->getGraphEdge()->asArray()  : [];
                                    // dd($page_fans_country_responsearr);


                                    $facebook_like_analytic[$facebook_page['id']] = [
                                        'like' => $likeresponsearr,
                                        'paid_organic_like' => $paid_organic_like_responsearr,
                                        'page_fans_gender_age' => $page_fans_gender_age_responsearr,
                                        'page_fan_adds' => $page_fan_adds_responsearr,
                                        'page_fan_removes' => $page_fan_removes_responsearr,
                                        'page_fans_city' => $page_fans_city_responsearr,
                                        'page_fans_country' => $page_fans_country_responsearr,
                                    ];
                                }
                            }


                            Analytics::updateOrCreate(
                                ['user_id' => $user->id],
                                ['facebook_like_analytic' => json_encode($facebook_like_analytic)]
                            );
                        }
                        break;
                    case 'facebook_engagement':
                        if ($facebook_pages) {
                            $start = request()->start_date ? \Carbon\Carbon::parse(request()->start_date)->startOfDay()->timestamp : \Carbon\Carbon::now()->startOfMonth()->timestamp;
                            $end = request()->end_date ? \Carbon\Carbon::parse(request()->end_date)->endOfDay()->timestamp  : \Carbon\Carbon::now()->addDay()->endOfMonth()->timestamp;

                            foreach ($facebook_pages as $facebook_page) {
                                if (in_array($facebook_page['id'], $mediaPagesArr)) {
                                    $token =  $facebook_page['access_token'];

                                    //Page Engaged
                                    $page_engaged_users_response = $facebook->get('/' . $facebook_page['id'] . '/insights?metric=page_engaged_users&since=' . $start . '&until=' . $end, $token);
                                    $statuscode = $page_engaged_users_response->getHttpStatusCode();
                                    $pageengagedearr = ($statuscode == 200) ?  $page_engaged_users_response->getGraphEdge()->asArray()  : [];


                                    // Page Engaged reactions, comments, shares and more
                                    $page_enaged_all_response = $facebook->get('/' . $facebook_page['id'] . '/insights?metric=page_positive_feedback_by_type&since=' . $start . '&until=' . $end, $token);
                                    $statuscode = $page_enaged_all_response->getHttpStatusCode();
                                    $pageenagedallresponsearr = ($statuscode == 200) ?  $page_enaged_all_response->getGraphEdge()->asArray()  : [];

                                    $facebook_engaged_analytic[$facebook_page['id']] =  [
                                        'page_engaged' => $pageengagedearr,
                                        'page_enaged_all' => $pageenagedallresponsearr
                                    ];
                                }
                            }
                            Analytics::updateOrCreate(
                                ['user_id' => $user->id],
                                ['facebook_engaged_analytic' => json_encode($facebook_engaged_analytic)]
                            );
                        }

                        break;

                    case 'facebook_reach':

                        $start = request()->start_date ? \Carbon\Carbon::parse(request()->start_date)->startOfDay()->timestamp : \Carbon\Carbon::now()->startOfMonth()->timestamp;
                        $end = request()->end_date ? \Carbon\Carbon::parse(request()->end_date)->endOfDay()->timestamp  : \Carbon\Carbon::now()->addDay()->endOfMonth()->timestamp;

                        $facebook_react_analytic = [];
                        if ($facebook_pages) {
                            foreach ($facebook_pages as $facebook_page) {
                                if (in_array($facebook_page['id'], $mediaPagesArr)) {
                                    $token =  $facebook_page['access_token'];

                                    //Total Reach //Gender //Age
                                    // $reach_response = $facebook->get('/' . $facebook_page['id'] . '/insights?metric=page_views_by_age_gender_logged_in_unique&since=' . $start . '&until=' . $end, $token);
                                    $reach_response = $facebook->get('/' . $facebook_page['id'] . '/insights?metric=page_impressions_unique&since=' . $start . '&until=' . $end, $token);
                                    $statuscode = $reach_response->getHttpStatusCode();
                                    $reacharr = ($statuscode == 200) ?  $reach_response->getGraphEdge()->asArray()  : [];

                                    $reach_by_gender_response = $facebook->get('/' . $facebook_page['id'] . '/insights?metric=page_impressions_by_age_gender_unique&since=' . $start . '&until=' . $end, $token);
                                    $statuscode = $reach_by_gender_response->getHttpStatusCode();
                                    $reachByGenderarr = ($statuscode == 200) ?  $reach_by_gender_response->getGraphEdge()->asArray()  : [];


                                    //page_video_views_organic
                                    $page_video_views_organic_response = $facebook->get('/' . $facebook_page['id'] . '/insights?metric=page_video_views_organic&since=' . $start . '&until=' . $end, $token);
                                    $statuscode = $page_video_views_organic_response->getHttpStatusCode();
                                    $pagevideovieworganicarr = ($statuscode == 200) ?  $page_video_views_organic_response->getGraphEdge()->asArray()  : [];


                                    //page_video_views_paid
                                    $page_video_views_paid_response = $facebook->get('/' . $facebook_page['id'] . '/insights?metric=page_video_views_paid&since=' . $start . '&until=' . $end, $token);
                                    $statuscode = $page_video_views_paid_response->getHttpStatusCode();
                                    $pagevideoviewpaidarr = ($statuscode == 200) ?  $page_video_views_paid_response->getGraphEdge()->asArray()  : [];


                                    //page_content_activity_by_country_unique
                                    // $page_content_activity_by_country_unique = $facebook->get('/' . $facebook_page['id'] . '/insights?metric=page_content_activity_by_country_unique&since=' . $start . '&until=' . $end, $token);
                                    $page_content_activity_by_country_unique = $facebook->get('/' . $facebook_page['id'] . '/insights?metric=page_impressions_by_country_unique&since=' . $start . '&until=' . $end, $token);
                                    $statuscode = $page_content_activity_by_country_unique->getHttpStatusCode();
                                    $pagecontentactivitybycountryunique = ($statuscode == 200) ?  $page_content_activity_by_country_unique->getGraphEdge()->asArray()  : [];


                                    //page_content_activity_by_city_unique
                                    $page_content_activity_by_city_unique = $facebook->get('/' . $facebook_page['id'] . '/insights?metric=page_impressions_by_city_unique&since=' . $start . '&until=' . $end, $token);
                                    $statuscode = $page_content_activity_by_city_unique->getHttpStatusCode();
                                    $pagecontentactivitybycitiesunique = ($statuscode == 200) ?  $page_content_activity_by_city_unique->getGraphEdge()->asArray()  : [];

                                    $page_paid_reach_unique = $facebook->get('/' . $facebook_page['id'] . '/insights?metric=page_impressions_paid_unique&since=' . $start . '&until=' . $end, $token);
                                    $statuscode = $page_paid_reach_unique->getHttpStatusCode();
                                    $pagePaidReach = ($statuscode == 200) ?  $page_paid_reach_unique->getGraphEdge()->asArray()  : [];

                                    $page_organic_reach_unique = $facebook->get('/' . $facebook_page['id'] . '/insights?metric=page_impressions_organic_unique_v2&since=' . $start . '&until=' . $end, $token);
                                    $statuscode = $page_organic_reach_unique->getHttpStatusCode();
                                    $pageOrganicReach = ($statuscode == 200) ?  $page_organic_reach_unique->getGraphEdge()->asArray()  : [];

                                    $facebook_react_analytic[$facebook_page['id']] = [
                                        'page_reach' => $reacharr,
                                        'page_reach_by_gender' => $reachByGenderarr,
                                        'page_video_views_organic' => $pagevideovieworganicarr,
                                        'page_video_views_paid' => $pagevideoviewpaidarr,
                                        'page_content_activity_by_country_unique' => $pagecontentactivitybycountryunique,
                                        'page_content_activity_by_city_unique' => $pagecontentactivitybycitiesunique,
                                        'page_organic_reach' => $pageOrganicReach,
                                        'page_paid_reach' => $pagePaidReach,
                                    ];
                                }
                            }
                        }
                        Analytics::updateOrCreate(
                            ['user_id' => $user->id],
                            ['facebook_react_analytic' => json_encode($facebook_react_analytic)]
                        );

                        break;

                    default:
                }
            }
        } catch (\Exception $e) {

            $resArr = [
                'status' => 'error',
                'message' => 'Something went wrong please try again.'
            ];
            return $resArr;
        }
        return [
            'status' => 'success',
            'message' => ''
        ];
    }

    public function FacebookPosts()
    {
        $user = auth()->user()->parentUser ?? auth()->user();

        try {
            $socialMedia = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 1)->get();
            date_default_timezone_set('Australia/Brisbane');
            $start = request()->start_date ? \Carbon\Carbon::parse(request()->start_date)->timestamp : \Carbon\Carbon::now()->startOfMonth()->timestamp;
            $end = request()->end_date ? \Carbon\Carbon::parse(request()->end_date)->timestamp : \Carbon\Carbon::now()->endOfMonth()->timestamp;
            foreach ($socialMedia as $sm) {
                $facebook = new Facebook([
                    'app_id' => config('utility.FACEBOOK_APP_ID'),
                    'app_secret' => config('utility.FACEBOOK_APP_SECRET'),
                    'default_graph_version' => config('utility.DEFAULT_FACEBOOK_API_VERSION'),
                ]);;
                $response = $facebook->get('/' . $sm->social_id . '/accounts?fields=name,access_token', $sm->token);
                $facebook_pages = $response->getGraphEdge()->asArray();
                $selected_media_page = MediaPage::whereIn('id', explode(',', request()->page_id))->pluck('page_id')->toArray();
                $postArr = [];
                foreach ($selected_media_page as $facebook_page) {
                    // if (in_array($facebook_page['id'], $selected_media_page)) {
                    $token =  array_column(array_filter($facebook_pages, function ($page) use ($facebook_page) {
                        if ($facebook_page == $page['id']) return $page;
                    }), 'access_token')[0];
                    $post = $facebook->get('/' . $facebook_page . '/feed?since=' . $start . '&until=' . $end, $token);
                    // $post = $facebook->get('/' . $facebook_page . '/posts', $token);

                    $statuscode = $post->getHttpStatusCode();
                    $respost = ($statuscode == 200) ?  $post->getGraphEdge()->asArray()  : [];
                    foreach ($respost as $key => $singlepost) {
                        // if ($posts_created_date_timestamp >= $start && $posts_created_date_timestamp <= $end) {
                        $attachments = $facebook->get('/' . $singlepost['id'] . '/attachments', $token);
                        $statuscode = $attachments->getHttpStatusCode();
                        $attachments = ($statuscode == 200) ?  $attachments->getGraphEdge()->asArray()  : [];
                        $respost[$key]['attachments'] = isset($attachments[0]['media']) ? $attachments[0]['media'] : [];

                        $postreach = $facebook->get('/' . $singlepost['id'] . '/insights?metric=post_impressions_unique&since=' . $start . '&until=' . $end, $token);
                        $statuscode = $postreach->getHttpStatusCode();
                        $postreach = ($statuscode == 200) ?  $postreach->getGraphEdge()->asArray()  : [];
                        $respost[$key]['postreach'] = $postreach;

                        // $reactions = $facebook->get('/' . $singlepost['id'] . '/insights?metric=post_reactions_like_total&since=' . $start . '&until=' . $end, $token);
                        $reactions = $facebook->get('/' . $singlepost['id'] . '/likes?summary=total_count', $token);
                        $statuscode = $reactions->getHttpStatusCode();
                        $reactions = ($statuscode == 200) ?  $reactions->getDecodedBody()['summary']['total_count'] ?? 0  : [];
                        $respost[$key]['reactions'] = $reactions;

                        $insights = $facebook->get('/' . $singlepost['id'] . '/insights?metric=post_clicks&since=' . $start . '&until=' . $end, $token);
                        $statuscode = $insights->getHttpStatusCode();
                        $insights = ($statuscode == 200) ?  $insights->getGraphEdge()->asArray()  : [];
                        $respost[$key]['insights'] = $insights;

                        $post_activity_unique = $facebook->get('/' . $singlepost['id'] . '/insights?metric=post_activity_unique&since=' . $start . '&until=' . $end, $token);
                        $statuscode = $post_activity_unique->getHttpStatusCode();
                        $post_activity_unique_response = ($statuscode == 200) ?  $post_activity_unique->getGraphEdge()->asArray()  : [];
                        $respost[$key]['post_activity_unique'] = $post_activity_unique_response;

                        $post_share = $facebook->get('/' . $singlepost['id'] . '?fields=shares', $token);
                        $statuscode = $post_share->getHttpStatusCode();
                        $post_share_response = ($statuscode == 200) ?  $post_share->getDecodedBody()['shares']['count'] ?? 0 : [];
                        $respost[$key]['post_share_count'] = $post_share_response;
                        // }
                    }
                    $postArr[$facebook_page] = $respost;
                }
                Analytics::updateOrCreate(
                    ['user_id' => $user->id],
                    ['facebook_post' => json_encode($postArr)]
                );
            }
        } catch (\Exception $e) {
            $resArr = [
                'status' => 'error',
                'message' => 'Something went wrong please try again.'
            ];
            return $resArr;
        }
    }

    public function FacebookReview()
    {
        $user = auth()->user()->parentUser ?? auth()->user();

        try {
            $mediaPagesArr = MediaPage::where([
                'user_id' => $user->id,
                'media_id' => 1
            ])->whereHas('userMediaPages', function ($q) use ($user) {
                $q->where('user_id', $user->id);
                $q->where('is_deleted', 'n');
            })->pluck('page_id')->toArray();

            $socialMedia = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 1)->get();

            foreach ($socialMedia as $sm) {
                $facebook = new Facebook([
                    'app_id' => config('utility.FACEBOOK_APP_ID'),
                    'app_secret' => config('utility.FACEBOOK_APP_SECRET'),
                    'default_graph_version' => config('utility.DEFAULT_FACEBOOK_API_VERSION'),
                ]);;
                $response = $facebook->get('/' . $sm->social_id . '/accounts?fields=name,access_token', $sm->token);
                $facebook_pages = $response->getGraphEdge()->asArray();

                $ratingArr = [];
                foreach ($facebook_pages as $facebook_page) {
                    if (in_array($facebook_page['id'], $mediaPagesArr)) {

                        $token =  $facebook_page['access_token'];
                        $ratings = $facebook->get('/' . $facebook_page['id'] . '/ratings', $token);

                        $statuscode = $ratings->getHttpStatusCode();
                        $resratings = ($statuscode == 200) ?  $ratings->getGraphEdge()->asArray()  : [];
                        $ratingArr[$facebook_page['id']] = $resratings;
                    }
                }

                Analytics::updateOrCreate(
                    ['user_id' => $user->id],
                    ['facebook_review' => json_encode($ratingArr)]
                );
            }
        } catch (\Exception $e) {
            $resArr = [
                'status' => 'error',
                'message' => 'Something went wrong please try again.'
            ];
            return $resArr;
        }

        return [
            'status' => 'success',
            'message' => ''
        ];
    }

    public function TwitterAnalitics()
    {

        try {

            $user = auth()->user()->parentUser ?? auth()->user();
            $socialMedia = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 3)->get();

            if (count($socialMedia) == 0) {
                $resArr = [
                    'status' => 'error',
                    'message' => 'Please connect twitter in your account.'
                ];
                return $resArr;
            }
            $page_id = (request()->page_id && request()->page_id != 'all') ? explode(',', request()->page_id) : null;

            $mediaPagesArr = MediaPage::where([
                'user_id' => $user->id,
                'media_id' => 3
            ])->whereHas('userMediaPages', function ($q) use ($user) {
                $q->where('user_id', $user->id);
                $q->where('is_deleted', 'n');
            })->when($page_id, function ($query) use ($page_id) {
                $query->whereIn('id', $page_id);
            })->pluck('social_media_detail_id')->toArray();

            $twitter_analytic = [];
            $followerlist = [];

            $followers_count = 0;
            $statuses_count = 0;
            $favourites_count = 0;
            $retweets_of_me1 = 0;


            foreach ($socialMedia as $tmedia) {
                if (in_array($tmedia->id, $mediaPagesArr)) {

                    $connection = new TwitterOAuth(config('utility.TWITTER_CONSUMER_KEY'), config('utility.TWITTER_CONSUMER_SECRET'), $tmedia->token, $tmedia->token_secret);
                    $connection->setTimeouts(1000, 1000);
                    $verify_credentials = $connection->get("account/verify_credentials");
                    $connection->setApiVersion('1.1');

                    $retweets_of_me = $connection->get('statuses/retweets_of_me');
                    if (isset($retweets_of_me->errors)) $retweets_of_me = [];
                    $fl = $connection->get('followers/list');

                    if (!isset($fl->errors)) {
                        $followerlist[] = $fl;
                    }

                    $followers_count = $followers_count + ((int)$verify_credentials->followers_count ?? 0);
                    $statuses_count = $statuses_count + ((int)$verify_credentials->statuses_count ?? 0);
                    $favourites_count =  $favourites_count + ((int)$verify_credentials->favourites_count ?? 0);
                    $retweets_of_me = (isset($retweets_of_me) && count($retweets_of_me)) ? (count($retweets_of_me) + $retweets_of_me1) : $retweets_of_me1;
                }
            }

            $twitter_analytic  = [
                'followers_count' => $followers_count ?? '',
                'tweet_count' => $statuses_count ?? '',
                'like_count' => $favourites_count ?? '',
                'retweet_count' => $retweets_of_me1 ?? ''
            ];

            Analytics::updateOrCreate(
                ['user_id' => $user->id],
                ['twitter_analytic' => json_encode($twitter_analytic), 'twitter_follower_analytic' => json_encode($followerlist)]
            );

            $resArr = [
                'status' => 'success',
                'message' => ''
            ];
            return $resArr;
        } catch (\Exception $e) {
            // dd($e->getMessage());
            $resArr = [
                'status' => 'error',
                'message' => 'Something went wrong please try again.'
            ];
            return $resArr;
        }
        return [
            'status' => 'success',
            'message' => ''
        ];
    }

    public function TwitterFeed()
    {
        try {

            $user = auth()->user()->parentUser ?? auth()->user();
            $socialMedia = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 3)->get();

            if (count($socialMedia) == 0) {
                $resArr = [
                    'status' => 'error',
                    'message' => 'Please connect twitter in your account.'
                ];
                return $resArr;
            }
            $page_id = (request()->page_id && request()->page_id != 'all') ? explode(',', request()->page_id) : null;

            $mediaPagesArr = MediaPage::where([
                'user_id' => $user->id,
                'media_id' => 3
            ])->whereHas('userMediaPages', function ($q) use ($user) {
                $q->where('user_id', $user->id);
                $q->where('is_deleted', 'n');
            })->when($page_id, function ($query) use ($page_id) {
                $query->whereIn('id', $page_id);
            })->pluck('social_media_detail_id')->toArray();

            $twarr = [];
            foreach ($socialMedia as $tmedia) {
                if (in_array($tmedia->id, $mediaPagesArr)) {
                    $connection = new TwitterOAuth(config('utility.TWITTER_CONSUMER_KEY'), config('utility.TWITTER_CONSUMER_SECRET'), $tmedia->token, $tmedia->token_secret);
                    $connection->setTimeouts(1000, 1000);
                    $connection->setApiVersion('2');
                    $tweets = $connection->get("users/" . $tmedia->social_id . "/tweets", [
                        'max_results' => 100,
                        'tweet.fields' => 'created_at,text,id,attachments,public_metrics',
                        'media.fields' => 'url',
                        'expansions' => 'attachments.media_keys'
                    ]);
                    $twarr[] = $tweets;
                    // if(isset($tweets->data) && count($tweets->data)>0){
                    //     foreach($tweets->data as $tweet){
                    //         $twt = $connection->get("tweets/".$tweet->id,[
                    //             'tweet.fields' => 'created_at,text,id,attachments,public_metrics',
                    //             'media.fields' => 'url',
                    //             'expansions' => 'attachments.media_keys'
                    //         ]);
                    //         $twarr[] = $twt;
                    //     }
                    // }

                }
            }

            Analytics::updateOrCreate(
                ['user_id' => $user->id],
                ['twitter_tweet_analytic' => json_encode($twarr)]
            );

            $resArr = [
                'status' => 'success',
                'message' => ''
            ];
            return $resArr;


            // if(isset($data->id)){
            //     $connection = new TwitterOAuth(config('utility.TWITTER_CONSUMER_KEY'), config('utility.TWITTER_CONSUMER_SECRET'), $data->token, $data->token_secret);
            //     $connection->setApiVersion('2');
            //     $tweets = $connection->get("users/".$data->social_id."/tweets",['max_results' => 100]);
            //     if(isset($tweets->data) && count($tweets->data)>0){
            //         $twarr = [];
            //         foreach($tweets->data as $tweet){

            //             $twt = $connection->get("tweets/".$tweet->id,[
            //                 'tweet.fields' => 'created_at,text,id,attachments,public_metrics',
            //                 'media.fields' => 'url',
            //                 'expansions' => 'attachments.media_keys'
            //             ]);
            //             $twarr[] = $twt;
            //         }

            //         Analytics::updateOrCreate(
            //             ['user_id' => $user->id],
            //             ['twitter_tweet_analytic' => json_encode($twarr)]
            //         );

            //         $resArr = [
            //             'status' => 'success',
            //             'message' => ''
            //         ];
            //         return $resArr;

            //     }
            // }else{
            //     $resArr = [
            //         'status' => 'error',
            //         'message' => 'Please connect twitter in your account.'
            //     ];
            //     return $resArr;
            // }

        } catch (\Exception $e) {

            $resArr = [
                'status' => 'error',
                'message' => 'Something went wrong please try again.'
            ];
            return $resArr;
        }
        $resArr = [
            'status' => 'success',
            'message' => ''
        ];
        return $resArr;
    }

    public function LinkedInEngagment($start_date = null, $end_date = null)
    {
        try {
            $user = auth()->user()->parentUser ?? auth()->user();
            $social_media = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 2)->first();
            if (!isset($social_media->id)) {

                $resArr = [
                    'status' => 'error',
                    'message' => 'Please connect linkedin in your account.'
                ];
                return $resArr;
            }

            $needUpdateData = $this->needToUpdateLinkedinData($user);

            if ($needUpdateData) {
                /** Update 30 min old data */
                $data = LinkedInService::getFollowerData($social_media->social_id, $social_media->token);
                Analytics::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'linkedin_follower' => json_encode($data['followdata']),
                        'linkedin_last_updated_at' => date('Y-m-d H:i:s', strtotime(NOW()))
                    ]
                );
            }
            $resArr = [
                'status' => 'success',
                'message' => ''
            ];
            return $resArr;
        } catch (\Exception $e) {
            $resArr = [
                'status' => 'error',
                'message' => 'Something went wrong please try again.'
            ];
            return $resArr;
        }

        return [
            'status' => 'success',
            'message' => ''
        ];
    }

    public function LinkedinAnalytics(Request $request)
    {
        $response =  [
            'data' => 'No data found',
            'status' => 500
        ];
        switch ($request->type) {
            case "total_counts":

                $linkedin_total_follower = LinkedInService::getLinkedintotalFollowerData($request->orgIds, $request->page_update);
                $totalFollowerCount = array_sum($linkedin_total_follower['totalFollowerData']);
                $response['data'] = $totalFollowerCount;
                $response['status'] = 200;
                break;

            case "organic_paid_new_followers":

                $start_date = $request->start_date ?? null;
                $end_date = $request->end_date ?? null;
                $start = $start_date ? \Carbon\Carbon::parse($start_date)->startOfDay()->setTimezone($this->timezone)->timestamp * 1000 : \Carbon\Carbon::now($this->timezone)->startOfMonth()->timestamp * 1000;
                $end = $end_date ? \Carbon\Carbon::parse($end_date)->addDay()->endOfDay()->setTimezone($this->timezone)->timestamp * 1000 : \Carbon\Carbon::now($this->timezone)->endOfMonth()->addDay()->timestamp * 1000;
                $organic_paid_new_followers = LinkedInService::getLinkedinTimeFollowData($request->orgIds, $start, $end, $request->update_data);

                $response['data'] = $organic_paid_new_followers;
                $response['status'] = 200;

                break;

            case "viewers":

                $start_date = $request->start_date ?? null;
                $end_date = $request->end_date ?? null;

                $start = $start_date ? \Carbon\Carbon::parse($start_date)->startOfDay()->setTimezone($this->timezone)->timestamp * 1000 : \Carbon\Carbon::now($this->timezone)->startOfMonth()->timestamp * 1000;
                $end = $end_date ? \Carbon\Carbon::parse($end_date)->addDay()->endOfDay()->setTimezone($this->timezone)->timestamp * 1000 : \Carbon\Carbon::now($this->timezone)->endOfMonth()->addDay()->timestamp * 1000;
                $click_view_data = LinkedInService::getLinkedinClickData($request->orgIds, $start, $end, $request->update_data);
                $response['data'] = $click_view_data;
                $response['status'] = 200;

                break;

            case "social_count":

                $start_date = $request->start_date ?? null;
                $end_date = $request->end_date ?? null;

                $start = $start_date ? \Carbon\Carbon::parse($start_date)->startOfDay()->setTimezone($this->timezone)->timestamp * 1000 : \Carbon\Carbon::now($this->timezone)->startOfMonth()->timestamp * 1000;
                $end = $end_date ? \Carbon\Carbon::parse($end_date)->addDay()->endOfDay()->setTimezone($this->timezone)->timestamp * 1000 : \Carbon\Carbon::now($this->timezone)->endOfMonth()->addDay()->timestamp * 1000;
                $social_action_data = LinkedInService::getLinkedinSocialActionsData($request->orgIds, $start, $end, $request->update_data);

                $response['data'] = $social_action_data;
                $response['status'] = 200;

                break;

            case "posts":

                $start_date = $request->start_date ?? null;
                $end_date = $request->end_date ?? null;

                $start = $start_date ? \Carbon\Carbon::parse($start_date)->startOfDay()->setTimezone($this->timezone)->timestamp * 1000 : \Carbon\Carbon::now($this->timezone)->startOfMonth()->timestamp * 1000;
                $end = $end_date ? \Carbon\Carbon::parse($end_date)->addDay()->endOfDay()->setTimezone($this->timezone)->timestamp * 1000 : \Carbon\Carbon::now($this->timezone)->endOfMonth()->addDay()->timestamp * 1000;
                $posts = LinkedInService::LinkedInPost($start, $end, $request->update_data);

                $response['data'] = $posts;
                $response['status'] = 200;

                break;

            case "follower-by-country":

                $followerCountsByGeoCountry = LinkedInService::getLinkedinGeoData($request->analytic_id, $request->page_update);
                $render = view('frontend.pages.analytic.linkedin.followers-by-country')->with($followerCountsByGeoCountry)->render();;
                $response['data'] = $render;
                $response['status'] = 200;
                break;

            case "follower-by-company":

                $followerByCompany = LinkedInService::getLinkedinCompanyData($request->analytic_id, $request->page_update);
                $render = view('frontend.pages.analytic.linkedin.followers-by-company')->with($followerByCompany)->render();;
                $response['data'] = $render;
                $response['status'] = 200;
                break;

            case "follower-by-senority":

                $followerCountsBySeniority = LinkedInService::getLinkedinSenorityData($request->analytic_id, $request->page_update);
                // dump($followerCountsBySeniority);
                $render = view('frontend.pages.analytic.linkedin.followers-by-senority')->with($followerCountsBySeniority)->render();;
                $response['data'] = $render;
                $response['status'] = 200;
                break;

            case "follower-by-job-function":

                $followerByJobFunctions = LinkedInService::getLinkedinFunctionData($request->analytic_id, $request->page_update);
                $response['data'] = $followerByJobFunctions;
                $response['status'] = 200;
                break;

            case "follower-by-industry":

                $followerByindustry = LinkedInService::getLinkedinIndustryData($request->analytic_id, $request->page_update);
                $response['data'] = $followerByindustry;
                $response['status'] = 200;
                break;

            default:
                $response['data'] = "No data found.";
                $response['status'] = 200;
        }
        echo json_encode($response);
    }

    public function LinkedInEngagmentBk($start_date = null, $end_date = null)
    {
        try {
            $user = auth()->user()->parentUser ?? auth()->user();
            $social_media = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 2)->first();
            if (!isset($social_media->id)) {

                $resArr = [
                    'status' => 'error',
                    'message' => 'Please connect linkedin in your account.'
                ];
                return $resArr;
            }

            $start = $start_date ? \Carbon\Carbon::parse($start_date)->startOfDay()->setTimezone($this->timezone)->timestamp * 1000 : \Carbon\Carbon::now($this->timezone)->startOfMonth()->timestamp * 1000;
            $end = $end_date ? \Carbon\Carbon::parse($end_date)->addDay()->endOfDay()->setTimezone($this->timezone)->timestamp * 1000 : \Carbon\Carbon::now($this->timezone)->endOfMonth()->addDay()->timestamp * 1000;
            $data = LinkedInService::getEnagagmentData($social_media->social_id, $social_media->token, $start, $end);
            $this->LinkedInPost($start, $end);
            Analytics::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'linkedin_social_action_data' => json_encode($data['socialActionData']),
                    'linkedin_total_followers' => json_encode($data['totalFollowerData']),
                    'linkedin_follower' => json_encode($data['followdata']),
                    'linkedin_time_follower' => json_encode($data['timefollowdata']),
                    'linkedin_time_click' => json_encode($data['clickdata']),
                    'linkedin_geo_data' => json_encode($data['geoData']),
                    'linkedin_seniority_data' => json_encode($data['seniorityData']),
                    'linkedin_function_data' => json_encode($data['functionData']),
                    'linkedin_industry_data' => json_encode($data['industryData']),
                ]
            );
            $resArr = [
                'status' => 'success',
                'message' => ''
            ];
            return $resArr;
        } catch (\Exception $e) {
            $resArr = [
                'status' => 'error',
                'message' => 'Something went wrong please try again.'
            ];
            return $resArr;
        }

        return [
            'status' => 'success',
            'message' => ''
        ];
    }

    public function LinkedInPost($start, $end, $update_data = 0, $orgIds = [])
    {
        $linkedin_posts_data_arr = [];
        try {
            $user = auth()->user()->parentUser ?? auth()->user();
            $linkedin_posts = [];
            if ($update_data) {
                $social_media = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 2)->first();

                $linkedin_posts_data = LinkedInService::userPost($social_media->social_id, $social_media->token, $start, $end, $orgIds);

                if (!$linkedin_posts_data) throw new Exception();
                Analytics::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'linkedin_posts' => json_encode($linkedin_posts_data)
                    ]
                );

                $linkedin_posts = $linkedin_posts_data;
            } else {
                $analytic = Analytics::where('user_id', $user->id)->first('linkedin_posts');
                if ($analytic) {
                    $linkedin_posts = json_decode($analytic->linkedin_posts ?? '{}', true);
                }
            }

            if (!empty($linkedin_posts)) {
                $linkedin_posts = call_user_func_array('array_merge', $linkedin_posts);
                array_multisort(array_column($linkedin_posts, 'likes_count'), SORT_DESC, $linkedin_posts);
                $linkedin_posts_data_arr = $linkedin_posts;
            }
        } catch (\Exception $e) {
            logger()->error('linkedin Post' . $e->getMessage());
            flash('Something Went Wrong')->error();
            return redirect()->back();
        }
        return $linkedin_posts_data_arr;
        // return redirect()->back();
    }

    public function nextTweetFeed()
    {
        $page = request()->page;
        return view('frontend.pages.analytic.twitterfeed', compact('page'));
    }

    public function getPages(Request $request)
    {

        $media_id = $request->media_id;
        $user = auth()->user();

        $mediapage = MediaPage::whereHas('userMediaPages', function ($q) use ($user, $media_id) {
            $q->where('user_id', $user->id)->where('is_deleted', 'n')->where('media_id', $media_id);
        })->get();

        return $mediapage;
    }

    public function getInstagramDiscovery($start_date, $end_date, $page_ids)
    {
        try {
            $instagramService = new InstagramService();
            $facebook = new Facebook([
                'app_id' => config('utility.FACEBOOK_APP_ID'),
                'app_secret' => config('utility.FACEBOOK_APP_SECRET'),
                'default_graph_version' => config('utility.DEFAULT_FACEBOOK_API_VERSION'),
            ]);;
            $user_id = auth()->user()->parent_id ?? auth()->id();
            $socialMedia = SocialMediaDetail::where('user_id', $user_id)->where('media_id', 4)->first();
            $discoveryData = [];
            // Get All the Facebook Pages
            // $response = $facebook->get('/me/accounts?fields=id,access_token', $socialMedia->token);
            // $facebookPages = $response->getGraphEdge()->asArray();

            // Get Instagram Pages From the Facebook pages
            // $instagramPages = array_filter(array_map(function ($page) use ($facebook) {
            //     $instareq = $facebook->get('/' . $page['id'] . '?fields=instagram_business_account', $page['access_token']);
            //     $instares = $instareq->getGraphNode()->asArray();
            //     return isset($instares['instagram_business_account']) ? $instares : [];
            // }, $facebookPages));
            // Get All the Data Of the Perticuler Instagram Linked Pages
            foreach ($page_ids as $page_id) {
                // To get The Facebook Page Id for the Checking the page and Access token
                // $instagram_linked_page = array_values(array_filter($instagramPages, function ($page) use ($page_id) {
                //     return $page['instagram_business_account']['id'] == $page_id;
                // }))[0];

                // // Get Facebook Page Access From All the facebook Pages
                // $page_access_token = array_values(array_filter($facebookPages, fn ($page) => $page['id'] == $instagram_linked_page['id']))[0]['access_token'];
                // Get Instagram Discovery Data
                $discoveryData[$page_id] = $instagramService->getDiscoveryData($start_date, $end_date, $page_id, $socialMedia->token);
            }
        } catch (Exception $e) {
            // dd($e->getMessage(), $e->getLine());
        }
    }

    public function getInstagramPosts($start_date, $end_date, $page_ids)
    {
        DB::beginTransaction();
        try {
            $instagramService = new InstagramService();
            $user_id = auth()->user()->parent_id ?? auth()->id();
            $socialMedia = SocialMediaDetail::where('user_id', $user_id)->where('media_id', 4)->first();
            $posts = [];
            foreach ($page_ids as $page_id) {
                $posts[$page_id] = $instagramService->getPostsData($start_date, $end_date, $page_id, $socialMedia->token);
            }
            $user_id = auth()->user()->parent_id ?? auth()->id();
            Analytics::updateOrCreate(
                ['user_id' => $user_id],
                ['instagram_posts' => json_encode($posts)]
            );
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            logger()->error($e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Something Went Wrong while getting Posts.'
            ];
        }
    }

    public function getLinkedinOrgIds()
    {
        $orgIds = [];
        $user = auth()->user()->parentUser ?? auth()->user();
        $social_media = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 2)->first();
        if (isset($social_media->id)) {
            $orgIds = LinkedInService::getOrgIds($social_media->social_id);
        }
        return $orgIds;
    }

    public function needToUpdateLinkedinData($user)
    {
        $needToUpdate = 0;

        $oldData = $this->checkLinkedinOldData($user);
        $changePageIds = $this->checkPageIds();
        if ($oldData || $changePageIds) {
            $needToUpdate = 1;
        }
        return $needToUpdate;
    }

    public function needToUpdateFacebookData($user, $type)
    {
        $needToUpdate = 0;
        //$oldData = $this->checkFacebookOldData($user,$type);
        $checkRequest = $this->checkRequest($type);

        if ($checkRequest) {
            $needToUpdate = 1;
        }
        return $needToUpdate;
    }

    public function checkRequest($type)
    {

        $check_data = 1;
        $requestFilter = [
            'page_id'       =>  request()->page_id
        ];
        if (request()->has('start_date'))
            $requestFilter['start_date'] = request()->start_date;

        if (request()->has('end_date'))
            $requestFilter['end_date'] = request()->end_date;

        $session_hash = Session::get($type);
        if ($session_hash) {
            if (json_encode($requestFilter) == $session_hash) {
                $check_data = 0;
                return $check_data;
            }
        }
        Session::put('page_ids', request()->page_id);
        Session::put($type, json_encode($requestFilter));
        return $check_data;
    }

    private function checkPageIds()
    {
        $check_data = 1;
        $current_qury_string = request()->query();
        $session_hash = Session::get('page_ids');
        $page_ids = array_key_exists('page_id', $current_qury_string) ? $current_qury_string['page_id'] : 'all';
        if ($session_hash) {
            if ($page_ids === $session_hash) {
                $check_data = 0;
                return $check_data;
            }
        }
        Session::put('page_ids', $page_ids);
        return $check_data;
    }

    private function changePageIds()
    {
        $check_data = 1;
        $current_qury_string = request()->query();
        $session_hash = Session::get('analytic_page_ids');
        $analytic_page_ids = array_key_exists('page_id', $current_qury_string) ? $current_qury_string['page_id'] : 'all';
        if ($session_hash) {
            if ($analytic_page_ids === $session_hash) {
                $check_data = 0;
                return $check_data;
            }
        }
        Session::put('analytic_page_ids', $analytic_page_ids);
        return $check_data;
    }

    private function checkLinkedinOldData($user)
    {
        // check data null not not
        $analytic = Analytics::where('user_id', $user->id)->whereNull('linkedin_last_updated_at')->first(['linkedin_follower', 'linkedin_last_updated_at']);
        if ($analytic) {
            return  $analytic;
        }

        $analytic = Analytics::where('user_id', $user->id)->whereRaw('linkedin_last_updated_at < (NOW() - INTERVAL 60 MINUTE)')->first(['linkedin_follower', 'linkedin_last_updated_at']);
        return $analytic;
    }

    private function checkFacebookOldData($user, $type)
    {
        // check data null not not
        $analytic = Analytics::where('user_id', $user->id)->whereNull($type)->first([$type]);
        if ($analytic) {
            return  $analytic;
        }

        $analytic = Analytics::where('user_id', $user->id)->whereRaw("$type < (NOW() - INTERVAL 60 MINUTE)")->first([$type]);
        return $analytic;
    }

    public function googleMyBusinessAnalyticsData(Request $request, GoogleMyBusinessService $googleService): array
    {

        $response = [];
        try {
            $page_ids = $request->page_ids !== 'all' ? explode(',', $request->page_ids) : 'all';

            $user_id = auth()->user()->parent_id ?? auth()->id();

            $social_media = SocialMediaDetail::where('user_id', $user_id)->where('media_id', 5)->first();
            $data = [];
            date_default_timezone_set('Australia/Brisbane');
            $start_date =   $request->start_date ? \Carbon\Carbon::parse(request()->start_date)->format('Y-m-d') : \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
            $end_date   =   $request->end_date ? \Carbon\Carbon::parse(request()->end_date)->format('Y-m-d')  : \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');

            $media_pages = MediaPage::query();
            if ($page_ids !== 'all') {
                $media_pages->whereIn('id', $page_ids);
            } else {
                $media_pages->where(['user_id' => $user_id, 'media_id' => 5, 'is_old' => 'n']);
            }
            $media_pages = $media_pages->pluck('page_id')->toArray();
            switch ($request->type) {
                case 'platform_device':
                    $data = $googleService->getPlateformAndDeviceData($social_media->token, $start_date, $end_date, $media_pages);
                    if (!$data) throw new Exception();
                    break;
                case 'calls_website_direction':
                    $data = $googleService->getCallsData($social_media->token, $start_date, $end_date, $media_pages);
                    if (!$data) throw new Exception();
                    break;
                case 'message_booking_food':
                    $data = $googleService->getMessagesBookingsFoodOrderData($social_media->token, $start_date, $end_date, $media_pages);
                    if (!$data) throw new Exception();
                    break;
                case 'posts':
                    $data = $googleService->getGoogleBusinessPosts($social_media->social_id, $social_media->token, $start_date, $end_date, $media_pages);
                    break;
                case 'reviews':
                    $data = $googleService->getGoogleBusinessReviews($social_media->social_id, $social_media->token, $start_date, $end_date, $media_pages);
                    if (!$data) throw new Exception();
                    return $data;
                    break;
                case 'interactions':
                    $data = $googleService->getGoogleBusinessInteraction($social_media->token, $start_date, $end_date, $media_pages);
                    if (!$data) throw new Exception();
                    break;
            }
            $response['data'] = $data;
            $response['status'] = 200;
        } catch (Exception $e) {
            $response = [
                'data' => 'No data found',
                'status' => 500
            ];
        }
        return $response;
    }

    /***
     *
     * Google analytics version 4
     * Request $request
     * @param string $type of request
     * @return $response Response array
     *
     */
    public function googleAnalytics(Request $request, $type, $api_source = 'web')
    {
        $response = [
            'data'      =>  trans('No data found'),
            'status'    =>  404
        ];
        try {
            $media_id   =   6;
            $page_ids   =   $request->page_ids;
            date_default_timezone_set('Australia/Brisbane');
            $start_date =   $request->start_date ? \Carbon\Carbon::parse(request()->start_date)->format('Y-m-d') : \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
            $end_date   =   $request->end_date ? \Carbon\Carbon::parse(request()->end_date)->format('Y-m-d')  : \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');

            // media pages details
            $user_id = auth()->user()->parent_id ?? auth()->id();
            $page_ids = $request->page_ids !== 'all' ? explode(',', $request->page_ids) : 'all';
            $media_pages = MediaPage::query();
            if ($page_ids !== 'all') {
                $media_pages->whereIn('id', $page_ids);
            } else {
                $media_pages->whereHas('userMediaPages', function ($q) use ($user_id, $media_id) {
                    $q->where('user_id', $user_id)->where('is_deleted', 'n')->where('media_id', $media_id);
                })->where(['user_id' => $user_id, 'media_id' => $media_id, 'is_old' => 'n']);
            }
            $media_ids      =   $media_pages->pluck('id')->toArray();
            $media_pages    =   $media_pages->get();

            // service
            $googleAnalyticsService     =   new GoogleAnalyticsService();
            $googleAnalyticsServiceData =   $googleAnalyticsService->getGoogleAnalytics($media_pages, $type, $media_ids, $start_date, $end_date, $api_source);

            if ($googleAnalyticsServiceData) {
                $response['data']   =   $googleAnalyticsServiceData;
                $response['status'] =   200;
            }
        } catch (Exception $e) {
            $response = [
                'data'      =>  trans('Something went wrong'),
                'status'    =>  500
            ];
        }

        return response()->json($response, $response['status']);
    }

    /**
     * Facebook Ads Reporting
     * @param Request $request
     * @param string $type
     * @param string $api_source
     * @return JsonResponse
     */
    public function facebookAdsReporting(Request $request, $type, $api_source = 'web')
    {
        $response = [
            'data'      =>  trans('No data found'),
            'status'    =>  $api_source == 'api' ? 200 : 404
        ];
        try {
            $media_id   =   8;
            $page_ids   =   $request->page_ids;
            $campaign_id = $request->campaign_id;
            date_default_timezone_set('Australia/Brisbane');
            $start_date =   $request->start_date ? \Carbon\Carbon::parse(request()->start_date)->format('Y-m-d') : \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
            $end_date   =   $request->end_date ? \Carbon\Carbon::parse(request()->end_date)->format('Y-m-d')  : \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');

            // media pages details
            $user_id = auth()->user()->parent_id ?? auth()->id();
            $page_ids = $request->page_ids !== 'all' ? explode(',', $request->page_ids) : 'all';
            $media_pages = MediaPage::query();
            if ($page_ids !== 'all') {
                $media_pages->whereIn('id', $page_ids);
            } else {
                $media_pages->whereHas('userMediaPages', function ($q) use ($user_id, $media_id) {
                    $q->where('user_id', $user_id)->where('is_deleted', 'n')->where('media_id', $media_id);
                })->where(['user_id' => $user_id, 'media_id' => $media_id, 'is_old' => 'n']);
            }
            $media_ids      =   $media_pages->pluck('id')->toArray();
            $media_pages    =   $media_pages->get();

            // service
            $facebookAdsService     =   new FaceBookAdsService();
            $facebookAdsReportingData =   $facebookAdsService->getReporting($media_pages, $type, $media_ids, $start_date, $end_date, $api_source, $campaign_id);

            if ($facebookAdsReportingData) {
                $response['data']   =   $facebookAdsReportingData;
                $response['status'] =   200;
            }
        } catch (Exception $e) {
            $response = [
                'data'      =>  trans('Something went wrong'),
                'status'    =>  500
            ];
        }

        return response()->json($response, $response['status']);
    }

    /**
     * Instagram Analytics
     * @param Request $request
     * @param string $type
     * @param string $api_source
     * @return JsonResponse
     */
    public function instagramAnalytics(Request $request, $type, $api_source = 'web'): JsonResponse
    {
        $response = [
            'data'      =>  trans('No data found'),
            'status'    =>  $api_source == 'api' ? 200 : 404,
        ];
        try {
            $media_id   =   4;
            $page_ids   =   $request->page_ids;
            date_default_timezone_set('Australia/Brisbane');
            $start_date =   $request->start_date ? \Carbon\Carbon::parse(request()->start_date)->endOfDay()->timestamp : \Carbon\Carbon::now()->startOfMonth()->timestamp;
            $end_date   =   $request->end_date ? \Carbon\Carbon::parse(request()->end_date)->endOfDay()->timestamp  : \Carbon\Carbon::now()->endOfMonth()->timestamp;
            // media pages details
            $user_id = auth()->user()->parent_id ?? auth()->id();
            $page_ids = $request->page_ids !== 'all' ? explode(',', $request->page_ids) : 'all';
            $media_pages = MediaPage::query();
            if ($page_ids !== 'all') {
                $media_pages->whereIn('id', $page_ids);
            } else {
                $media_pages->whereHas('userMediaPages', function ($q) use ($user_id, $media_id) {
                    $q->where('user_id', $user_id)->where('is_deleted', 'n')->where('media_id', $media_id);
                })->where(['user_id' => $user_id, 'media_id' => $media_id, 'is_old' => 'n']);
            }
            $media_ids      =   $media_pages->pluck('id')->toArray();
            $media_pages    =   $media_pages->get();

            // service
            $instagramService     =   new InstagramService();
            $facebookAdsReportingData =   $instagramService->getReporting($media_pages, $type, $media_ids, $start_date, $end_date, $api_source);

            if ($facebookAdsReportingData) {
                $response['data']   =   $facebookAdsReportingData;
                $response['status'] =   200;
            }
        } catch (Exception $e) {
            // dd($e->getMessage(), 'error');
            $response = [
                'data'      =>  trans('Something went wrong'),
                'status'    =>  500
            ];
        }

        return response()->json($response, $response['status']);
    }

    /**
     * Google Ads Reporting
     * @param Request $request
     * @param string $type
     * @param string $api_source
     * @return JsonResponse
     */
    public function getGoogleAdsReporting(Request $request, $type, $api_source = 'web'): JsonResponse
    {
        $response = [
            'data'      =>  trans('No data found'),
            'status'    =>  $api_source == 'api' ? 200 : 404,
        ];
        try {

            date_default_timezone_set('Australia/Brisbane');

            $start_date =   \Carbon\Carbon::parse(request()->start_date)->startOfDay()->format('Y-m-d');
            $end_date   =   \Carbon\Carbon::parse(request()->end_date)->endOfDay()->format('Y-m-d');

            // $user_id = auth()->user()->parent_id ?? auth()->id();

            // media pages details
            $page_ids =  explode(',', $request->page_ids);

            $media_pages = MediaPage::whereIn('id', $page_ids)->get();

            if (!$media_pages) throw new Exception('No media pages found');

            $media_ids      =   $page_ids;

            // Get data from the service
            $googleAdsService     =   new GoogleAdsService();
            $googleAdsReportingData =   $googleAdsService->getAnalyticsData($media_pages, $type, $media_ids, $start_date, $end_date, $api_source);

            if ($googleAdsReportingData) {
                $response['data']   =   $googleAdsReportingData;
                $response['status'] =   200;
            }
        } catch (Exception $e) {
            // dd($e->getMessage(), 'error');
            $response = [
                'data'      =>  trans('Something went wrong'),
                'status'    =>  500
            ];
        }
        return response()->json($response, $response['status']);
    }
}
