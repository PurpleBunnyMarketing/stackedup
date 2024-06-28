<?php

namespace App\Http\Controllers\api\v1;

use App\User;
use App\Models\Analytics;
use Illuminate\Http\Request;
use App\Models\SocialMediaDetail;
use App\Services\LinkedInService;
use App\Services\GoogleMyBusinessService;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AnalyticsController;
use Abraham\TwitterOAuth\TwitterOAuth;
use Exception;
use App\Http\Resources\v1\AnalyticResource;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Session;


class AnalyticController extends Controller
{
    private $timezone = 'Australia/Brisbane';
    use SendsPasswordResetEmails;
    private $version = "v.1.0";
    public function getVersion()
    {
        return $this->version;
    }
    public function __construct()
    {
        $this->stripe = new \Stripe\StripeClient(
            config('utility.STRIPE_SECRET')
        );
    }
    /* Get Analytic */
    public function getAnalytic(Request $request)
    {
        $rules = [
            'type'     => 'required|in:linkedin,x(twitter),facebook',
        ];
        if ($this->apiValidator($request->all(), $rules)) {


            try {
                $user = auth()->user()->parentUser ?? auth()->user();
                if ($request->type == 'x(twitter)') {

                    $rules = [
                        'page_id' => 'required'
                    ];
                    if (!$this->apiValidator($request->all(), $rules)) {
                        return $this->return_response();
                    }

                    $twitterRes = \App\Http\Controllers\AnalyticsController::TwitterAnalitics();
                    if (isset($twitterRes['status']) && $twitterRes['status'] == 'error') {
                        $this->status = $this->statusArr['something_wrong'];
                        $this->response['meta']['api'] = $this->version;
                        $this->response['meta']['url'] = url()->current();
                        $this->response['meta']['message']  =  $twitterRes['message'];
                        return $this->return_response();
                    }

                    if (isset($twitterRes['status']) && $twitterRes['status'] == 'success') {
                        $twitterRes = \App\Http\Controllers\AnalyticsController::TwitterFeed();
                    }
                    $analytic = Analytics::where('user_id', $user->id)->first();
                    if (isset($analytic->twitter_analytic)) {
                        $tdata = $analytic->twitter_analytic;
                        $tdata = json_decode($tdata, true);
                        $twitter_analytic[] = $tdata;
                    } else {
                        $twitter_analytic = [];
                    }

                    $twitter_follower_analytic = $analytic->twitter_follower_analytic ?? '{}';
                    $twitter_follower_analytic = json_decode($twitter_follower_analytic, true);

                    $tuser = [];
                    // if(isset($twitter_follower_analytic['users'])){
                    //     foreach($twitter_follower_analytic['users'] as $key => $val){
                    //         $date = \Carbon\Carbon::parse( $val['created_at'])->format('M d');
                    //         $tuser[$key]['date'] = $date;
                    //         $tuser[$key]['value'] = ($tuser[$key]['value'] ?? 0) + 1;
                    //     }
                    // }

                    if (isset($twitter_follower_analytic)) {
                        foreach ($twitter_follower_analytic as $val) {
                            if (isset($val['users'])) {
                                foreach ($val['users'] as $key => $val1) {
                                    $date = \Carbon\Carbon::parse($val1['created_at'])->format('M d');
                                    $tuser[$key]['date'] = $date;
                                    $tuser[$key]['value'] = ($tuser[$key]['value'] ?? 0) + 1;
                                }
                            }
                        }
                    }

                    $twitter_tweet_analytic = $analytic->twitter_tweet_analytic ?? '{}';
                    $twitter_tweet_analytic = json_decode($twitter_tweet_analytic, true);

                    $twt = [];
                    $includeArr = [];
                    if (is_array($twitter_tweet_analytic)) {
                        foreach ($twitter_tweet_analytic as $tt) {
                            $twt = array_merge($twt, ($tt['data'] ?? []));
                            $includeArr = array_merge($includeArr, (isset($tt['includes']['media']) ? $tt['includes']['media'] : []));
                        }
                    }
                    if (is_array($twt) && !empty($twt)) {
                        foreach ($twt as $key => $feed) {
                            if (isset($feed['attachments']['media_keys'][0]) && getMediaUrl($feed['attachments']['media_keys'][0], $includeArr)) {
                                $twt[$key]['media_url'] = getMediaUrl($feed['attachments']['media_keys'][0], $includeArr);
                            }
                        }
                    }
                    // dd($twt);

                    if (isset($analytic->twitter_analytic) ||  isset($analytic->twitter_follower_analytic) || isset($analytic->twitter_tweet_analytic)) {
                        $resData = [
                            'twitter_data' => $twitter_analytic,
                            'twitter_follower_data' => $tuser,
                            'ttweet_data' => $twt,
                            'is_available' => true
                        ];
                    } else {
                        $resData = [
                            'is_available' => false
                        ];
                    }


                    return (new AnalyticResource($resData))
                        ->additional([
                            'meta' => [
                                'message'     =>    trans('api.list', ['entity' => 'Analytic'])
                            ]
                        ]);
                }

                if ($request->type == 'linkedin') {

                    $rules = [
                        'start_date' => 'nullable|date|date_format:m/d/Y',
                        'end_date' => 'nullable|date|date_format:m/d/Y|after:start_date',
                        'page_id' => 'required'
                    ];
                    if (!$this->apiValidator($request->all(), $rules)) {
                        return $this->return_response();
                    }

                    // $linkedinObject = new AnalyticsController();
                    // $data = $linkedinObject->filterAnalytic($request);
                    // $url = parse_url(route('analytics.index', $request->all()));
                    // $currentUrl = $url['path'] . '?' . $url['query'];
                    // $check_data = $this->checkURLHash('linkedin_engagment', $currentUrl);
                    // dd($check_data);

                    // if ($check_data) {
                    //     $linkeinRes = $this->LinkedInEngagment($request->start_date, $request->end_date);
                    //     if (isset($linkeinRes['status']) && $linkeinRes['status'] == 'error') {
                    //         flash($linkeinRes['message'])->error();
                    //     }
                    // }

                    $start = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->startOfDay()->setTimezone($this->timezone)->timestamp * 1000 : \Carbon\Carbon::now($this->timezone)->startOfMonth()->timestamp * 1000;
                    $end = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->addDay()->endOfDay()->setTimezone($this->timezone)->timestamp * 1000 : \Carbon\Carbon::now($this->timezone)->endOfMonth()->addDay()->timestamp * 1000;

                    $social_media = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 2)->first();
                    if (isset($social_media->token) && isset($social_media->social_id)) {

                        $data = LinkedInService::getEnagagmentData($social_media->social_id, $social_media->token, $start, $end);
                        Analytics::updateOrCreate(
                            ['user_id' => $user->id],
                            [
                                'linkedin_follower' => json_encode($data['followdata']),
                                'linkedin_time_follower' => json_encode($data['timefollowdata']),
                                'linkedin_time_click' => json_encode($data['clickdata']),
                                'linkedin_geo_data' => json_encode($data['geoData']),
                                'linkedin_seniority_data' => json_encode($data['seniorityData']),
                                'linkedin_function_data' => json_encode($data['functionData']),
                                'linkedin_industry_data' => json_encode($data['industryData']),
                                'linkedin_posts' => json_encode($data['posts']),
                                'linkedin_social_action_data' => json_encode($data['socialActionData']),
                            ]
                        );
                    } else {
                        $this->status = $this->statusArr['something_wrong'];
                        $this->response['meta']['api'] = $this->version;
                        $this->response['meta']['url'] = url()->current();
                        $this->response['meta']['message']  =  'Please connect linkedin in your account';
                        return $this->return_response();
                    }

                    $analytic = Analytics::where('user_id', $user->id)->first();
                    $linkedin_total_follower = json_decode($analytic->linkedin_total_followers ?? '{}', true);
                    $linkedin_follower = json_decode($analytic->linkedin_follower ?? '{}', true);
                    $linkedin_geo_data = json_decode($analytic->linkedin_geo_data ?? '{}', true);
                    $linkedin_seniority_data = json_decode($analytic->linkedin_seniority_data ?? '{}', true);
                    $linkedin_function_data = json_decode($analytic->linkedin_function_data ?? '{}', true);
                    $linkedin_industry_data = json_decode($analytic->linkedin_industry_data ?? '{}', true);
                    $linkedin_social_action_data = json_decode($analytic->linkedin_social_action_data ?? '{}', true);

                    $followerCount = 0;
                    $organicFollowerCount = 0;
                    $totalFollowerCount = array_sum($linkedin_total_follower);
                    $paidFollowerCount = 0;
                    $followerCountsByGeoCountry = [];
                    $followerCountsByStaffCountRange = [];
                    $followerCountsBySeniority = [];
                    $followerCountsByFunction = [];
                    $followerCountsByIndustry = [];

                    $tfc = 0;
                    $tfcs = 0;
                    $tfcl = 0;
                    foreach ($linkedin_follower as $lf) {
                        foreach ($lf as $value) {
                            // if(isset($value['followerCountsBySeniority'])){
                            //     $arr = isset($value['followerCountsBySeniority'][0]) ? $value['followerCountsBySeniority'][0] : [];
                            //     $followerCount += isset($arr['followerCounts']['organicFollowerCount']) ? $arr['followerCounts']['organicFollowerCount'] : 0;
                            //     $organicFollowerCount +=  isset($arr['followerCounts']['organicFollowerCount']) ? $arr['followerCounts']['organicFollowerCount'] : 0;
                            //     $paidFollowerCount += isset($arr['followerCounts']['paidFollowerCount']) ? $arr['followerCounts']['paidFollowerCount'] : 0;
                            // }

                            //followerCountsByGeoCountry

                            if (isset($value['followerCountsByGeoCountry'])) {
                                foreach ($value['followerCountsByGeoCountry'] as $fc) {
                                    $tfc += (isset($fc['followerCounts']['organicFollowerCount']) ? $fc['followerCounts']['organicFollowerCount'] : 0);
                                    $followerCountsByGeoCountry[$fc['geo']] = ($followerCountsByGeoCountry[$fc['geo']] ?? 0) + (isset($fc['followerCounts']['organicFollowerCount']) ? $fc['followerCounts']['organicFollowerCount'] : 0);
                                }
                            }

                            //followerCountsByStaffCountRange
                            if (isset($value['followerCountsByStaffCountRange'])) {
                                foreach ($value['followerCountsByStaffCountRange'] as $fc) {

                                    $tfcs += (isset($fc['followerCounts']['organicFollowerCount']) ? $fc['followerCounts']['organicFollowerCount'] : 0);
                                    $followerCountsByStaffCountRange[$fc['staffCountRange']] =
                                        ($followerCountsByStaffCountRange[$fc['staffCountRange']] ?? 0) + (isset($fc['followerCounts']['organicFollowerCount']) ? $fc['followerCounts']['organicFollowerCount'] : 0);
                                }
                            }

                            //followerCountsBySeniority
                            if (isset($value['followerCountsBySeniority'])) {
                                foreach ($value['followerCountsBySeniority'] as $fc) {

                                    $tfcl += (isset($fc['followerCounts']['organicFollowerCount']) ? $fc['followerCounts']['organicFollowerCount'] : 0);
                                    $followerCountsBySeniority[$fc['seniority']] =
                                        ($followerCountsBySeniority[$fc['seniority']] ?? 0) + (isset($fc['followerCounts']['organicFollowerCount']) ? $fc['followerCounts']['organicFollowerCount'] : 0);
                                }
                            }

                            //followerCountsByFunction
                            if (isset($value['followerCountsByFunction'])) {
                                foreach ($value['followerCountsByFunction'] as $k => $fc) {
                                    if ($fc['followerCounts']['organicFollowerCount'] > 3) {
                                        if (isset($linkedin_function_data[$fc['function']])) {
                                            $followerCountsByFunction[$linkedin_function_data[$fc['function']]] =
                                                ($followerCountsByFunction[$fc['function']] ?? 0) + (isset($fc['followerCounts']['organicFollowerCount']) ? $fc['followerCounts']['organicFollowerCount'] : 0);
                                        } else {
                                            $followerCountsByFunction['null'] =
                                                ($followerCountsByFunction[$fc['function']] ?? 0) + (isset($fc['followerCounts']['organicFollowerCount']) ? $fc['followerCounts']['organicFollowerCount'] : 0);
                                        }
                                    }
                                }
                            }

                            //followerCountsByIndustry
                            if (isset($value['followerCountsByIndustry'])) {
                                foreach ($value['followerCountsByIndustry'] as $fc) {
                                    if ($fc['followerCounts']['organicFollowerCount'] > 3) {
                                        if (isset($linkedin_industry_data[$fc['industry']])) {
                                            $followerCountsByIndustry[$linkedin_industry_data[$fc['industry']]] =
                                                ($followerCountsByIndustry[$fc['industry']] ?? 0) + (isset($fc['followerCounts']['organicFollowerCount']) ? $fc['followerCounts']['organicFollowerCount'] : 0);
                                        } else {
                                            $followerCountsByIndustry['null'] =
                                                ($followerCountsByIndustry[$fc['industry']] ?? 0) + (isset($fc['followerCounts']['organicFollowerCount']) ? $fc['followerCounts']['organicFollowerCount'] : 0);
                                        }
                                        //$followerCountsByIndustry[$fc['industry']] =
                                        //($followerCountsByIndustry[$fc['industry']] ?? 0) + (isset($fc['followerCounts']['organicFollowerCount']) ? $fc['followerCounts']['organicFollowerCount'] : 0);
                                    }
                                }
                            }
                        }
                    }

                    /* New Follwers */

                    $newflarr = [];
                    $linkedin_time_follower = json_decode($analytic->linkedin_time_follower ?? '{}', true);
                    foreach ($linkedin_time_follower as $lrf) {
                        $organicFollowerCount +=  isset($lrf) ? array_sum(array_column(array_column($lrf, 'followerGains'), 'organicFollowerGain')) : 0;
                        $paidFollowerCount += isset($lrf) ? array_sum(array_column(array_column($lrf, 'followerGains'), 'paidFollowerGain')) : 0;
                        foreach ($lrf as $k => $val) {
                            $key = \Carbon\Carbon::parse($val['timeRange']['start'] / 1000)->format('M d');
                            // $newflarr[$key ] = ($newflarr[$key ] ?? 0) + $val['followerGains']['organicFollowerGain'];

                            $newflarr[$k]['date'] = $key;
                            $newflarr[$k]['value'] = ($newflarr[$k]['value'] ?? 0) + $val['followerGains']['organicFollowerGain'];
                        }
                    }
                    $pdarr[] = [
                        'organic' => $organicFollowerCount,
                        'paid' => $paidFollowerCount
                    ];

                    /* Click Data */

                    $clickarr = [];
                    $viewarr = [];
                    $linkedin_time_click = json_decode($analytic->linkedin_time_click ?? '{}', true);
                    foreach ($linkedin_time_click as $lrf) {
                        foreach ($lrf as $k => $val) {
                            $key = \Carbon\Carbon::parse($val['timeRange']['start'] / 1000)->format('M d');
                            // $clickSum = $val['totalPageStatistics']['clicks']['careersPageClicks']['careersPageBannerPromoClicks']+
                            // $val['totalPageStatistics']['clicks']['careersPageClicks']['careersPagePromoLinksClicks']+
                            // $val['totalPageStatistics']['clicks']['careersPageClicks']['careersPageEmployeesClicks']+
                            // $val['totalPageStatistics']['clicks']['careersPageClicks']['careersPageJobsClicks'];
                            // $clickarr[$key ] = ($clickarr[$key ] ?? 0) + $clickSum;

                            $clickSum = $val['totalShareStatistics']['clickCount'];

                            $clickarr[$k]['date'] = $key;
                            $clickarr[$k]['value'] = ($clickarr[$k]['value'] ?? 0) + $clickSum;


                            // $view = $val['totalPageStatistics']['views']['allPageViews']['pageViews'];
                            $view = $val['totalShareStatistics']['impressionCount'];
                            // $viewarr[$key ] = ($viewarr[$key ] ?? 0) + $view;
                            $viewarr[$k]['date'] = $key;
                            $viewarr[$k]['value'] = ($viewarr[$k]['value'] ?? 0) + $view;
                        }
                    }

                    $lf1 = [];
                    foreach ($followerCountsByFunction as $k => $v) {
                        $lf1[] = [
                            'key' => $k,
                            'value' => $v,
                        ];
                    }
                    $lf2 = [];
                    foreach ($followerCountsByIndustry as $k => $v) {
                        $lf2[] = [
                            'key' => $k,
                            'value' => $v,
                        ];
                    }

                    $lf3 = [];
                    foreach ($followerCountsByStaffCountRange as $k => $v) {
                        $lf3[] = [
                            'key' => $k,
                            'value' => $v,
                        ];
                    }
                    $lf4 = [];
                    foreach ($followerCountsByGeoCountry as $k => $v) {
                        $lf4[] = [
                            'key' => $linkedin_geo_data[$k] ?? '',
                            'value' => $v,
                        ];
                    }
                    $lf5 = [];
                    foreach ($followerCountsBySeniority as $k => $v) {
                        $lf5[] = [
                            'key' => $linkedin_seniority_data[$k] ?? '',
                            'value' => $v,
                        ];
                    }
                    $likes_count_array = [];
                    $comments_count_array = [];
                    $share_count_array = [];
                    // Social Action Data
                    if ($linkedin_social_action_data) {
                        foreach ($linkedin_social_action_data as  $single_page_data) {
                            foreach ($single_page_data as  $key => $data) {
                                $date = \Carbon\Carbon::parse($data['timeRange']['start'] / 1000)->format('M d');
                                $likes_count_array[$key]['date'] = $comments_count_array[$key]['date'] = $share_count_array[$key]['date'] = $date;
                                $likes_count_array[$key]['value'] = $data['totalShareStatistics']['likeCount'];
                                $comments_count_array[$key]['value'] = $data['totalShareStatistics']['commentCount'];
                                $share_count_array[$key]['value'] = $data['totalShareStatistics']['shareCount'];
                            }
                        }
                    }
                    $linkedin_social_actions = [
                        // 'social_actions_keys' => $social_actions_keys,
                        'likes_count' => $likes_count_array,
                        'comment_count' => $comments_count_array,
                        'share_count' => $share_count_array
                    ];


                    // Linkedin Posts
                    $linkedin_posts = json_decode($analytic->linkedin_posts ?? '{}', true);
                    $linkedin_posts = call_user_func_array('array_merge', $linkedin_posts);
                    array_multisort(array_column($linkedin_posts, 'likes_count'), SORT_DESC, $linkedin_posts);
                    $linkedin_posts_data_arr = $linkedin_posts;

                    if (
                        isset($analytic->linkedin_follower) ||
                        isset($analytic->linkedin_time_follower) ||
                        isset($analytic->linkedin_time_click) ||
                        isset($analytic->linkedin_geo_data) ||
                        isset($analytic->linkedin_seniority_data) ||
                        isset($analytic->linkedin_function_data) ||
                        isset($analytic->linkedin_industry_data)
                    ) {

                        $resData = [
                            'linkedin_follower' => $totalFollowerCount,
                            'linkedin_organic_paid' => $pdarr,
                            'linkedin_new_follower' => $newflarr,
                            'linkedin_click' => $clickarr,
                            'linkedin_impression' => $viewarr,
                            'linkedin_by_country' => $lf4,
                            'linkedin_by_company_size' => $lf3,
                            'linkedin_by_seniority_level' => $lf5,
                            'linkedin_by_job_function' => $lf1,
                            'linkedin_by_industry' => $lf2,
                            'linkedin_social_actions_date' => $linkedin_social_actions,
                            'linkedin_posts' => $linkedin_posts_data_arr,
                            'is_available' => true
                        ];
                    } else {
                        $resData = [
                            'is_available' => false
                        ];
                    }


                    return (new AnalyticResource($resData))
                        ->additional([
                            'meta' => [
                                'message'     =>    trans('api.list', ['entity' => 'Analytic'])
                            ]
                        ]);
                }

                if ($request->type == 'facebook') {

                    $rules = [
                        'start_date' => 'nullable|date|date_format:m/d/Y',
                        'end_date' => 'nullable|date|date_format:m/d/Y|after:start_date',
                        'page_id' => 'required'
                    ];
                    if (!$this->apiValidator($request->all(), $rules)) {
                        return $this->return_response();
                    }

                    $facebookRes = \App\Http\Controllers\AnalyticsController::FacebookAnaliticsApi('facebook_like');
                    if (isset($facebookRes['status']) && $facebookRes['status'] == 'error') {

                        $this->status = $this->statusArr['something_wrong'];
                        $this->response['meta']['api'] = $this->version;
                        $this->response['meta']['url'] = url()->current();
                        $this->response['meta']['message']  =  $facebookRes['message'];
                        return $this->return_response();
                    }
                    if (isset($facebookRes['status']) && $facebookRes['status'] == 'success') {
                        \App\Http\Controllers\AnalyticsController::FacebookAnaliticsApi('facebook_engagement');
                        \App\Http\Controllers\AnalyticsController::FacebookAnaliticsApi('facebook_reach');
                        \App\Http\Controllers\AnalyticsController::FacebookPosts();
                    }

                    $analytic = Analytics::where('user_id', $user->id)->first();
                    $facebook_like_analytic = $analytic->facebook_like_analytic ?? '{}';
                    $facebook_like_analytic = json_decode($facebook_like_analytic, true);

                    $ftotal_likes = 0;
                    $paid_like = 0;
                    $organic_like = 0;
                    $audaddarr = [];
                    $audremovedarr = [];

                    $facebook_like_age_arr = [
                        '13-17' => 0,
                        '18-24' => 0,
                        '25-34' => 0,
                        '35-44' => 0,
                        '45-54' => 0,
                        '55-64' => 0,
                        '65+' => 0,
                    ];

                    $facebook_like_gender = [
                        'Male' => 0,
                        'Female' => 0,
                        'Unspecified' => 0,
                    ];

                    $countryarr = [];
                    $cityarr = [];

                    foreach ($facebook_like_analytic as $fla) {
                        if (isset($fla['like'][0]['values'][0]['value'])) {
                            // $ftotal_likes += $fla['like'][2]['values'][1]['value'];
                            $ftotal_likes += $fla['like'][0]['values'][count($fla['like'][0]['values']) - 1]['value'];
                        }

                        // if(isset($fla['paid_organic_like'][4]['values'][0]['value'])){
                        //     $paid_like += $fla['paid_organic_like'][4]['values'][0]['value'];
                        // }
                        if (isset($fla['paid_organic_like'][0]['values'])) {
                            $paid_like += array_sum(array_column(array_column($fla['paid_organic_like'][0]['values'], 'value'), 'ads'));
                        }

                        // if(isset($fla['paid_organic_like'][5]['values'][0]['value'])){
                        //     $organic_like += $fla['paid_organic_like'][5]['values'][0]['value'];
                        // }
                        if (isset($fla['paid_organic_like'][0]['values'])) {
                            $organic_like += array_sum(array_column(array_column($fla['paid_organic_like'][0]['values'], 'value'), 'Your Page'));
                        }

                        //By Age

                        if (isset($fla['page_fans_gender_age'][0]['values'][1]['value'])) {
                            foreach ($fla['page_fans_gender_age'][0]['values'][count($fla['page_fans_gender_age'][0]['values']) - 1]['value'] as $key => $value) {
                                if (array_key_exists(substr($key, 2), $facebook_like_age_arr)) {
                                    $facebook_like_age_arr[substr($key, 2)] = $facebook_like_age_arr[substr($key, 2)] + $value;

                                    if (str_contains($key, 'M.')) {
                                        $facebook_like_gender['Male'] += $value;
                                    }
                                    if (str_contains($key, 'F.')) {
                                        $facebook_like_gender['Female'] += $value;
                                    }
                                    if (str_contains($key, 'U.')) {
                                        $facebook_like_gender['Unspecified'] += $value;
                                    }
                                }
                            }
                        }

                        //country
                        if (isset($fla['page_fans_country'][0]['values'][1]['value'])) {
                            foreach ($fla['page_fans_country'][0]['values'][1]['value'] as $key => $value) {
                                $countryarr[$key] = ($countryarr[$key] ?? 0) + $value;
                            }
                        }

                        //city
                        if (isset($fla['page_fans_city'][0]['values'][1]['value'])) {
                            foreach ($fla['page_fans_city'][0]['values'][1]['value'] as $key => $value) {
                                $cityarr[$key] =  ($cityarr[$key] ?? 0) +  $value;
                            }
                        }

                        //audience groth/lost
                        if (isset($fla['page_fan_adds'])) {
                            $page_fan_addsarr = array_filter($fla['page_fan_adds'], function ($arr) {
                                return  $arr['period'] == 'day';
                            });

                            if (isset($page_fan_addsarr) && count($page_fan_addsarr) > 0) {
                                $weekarr = array_values($page_fan_addsarr);
                                if (isset($weekarr) &&  isset($weekarr[0]['values'])) {
                                    foreach ($weekarr[0]['values'] as $val) {
                                        $date = \Carbon\Carbon::parse($val['end_time']['date'])->format('d M');
                                        $audaddarr[$date] = isset($audaddarr[$date]) ? ($audaddarr[$date] + $val['value']) : $val['value'];
                                    }
                                }
                            }
                        }

                        if (isset($fla['page_fan_removes'])) {
                            $page_fan_removesarr = array_filter($fla['page_fan_removes'], function ($arr) {
                                return  $arr['period'] == 'day';
                            });

                            if (isset($page_fan_removesarr) && count($page_fan_removesarr) > 0) {
                                $auddayarr = array_values($page_fan_removesarr);
                                if (isset($auddayarr) &&  isset($auddayarr[0]['values'])) {
                                    foreach ($auddayarr[0]['values'] as $val) {
                                        $date = \Carbon\Carbon::parse($val['end_time']['date'])->format('d M');
                                        $audremovedarr[$date] = isset($audremovedarr[$date]) ? ($audremovedarr[$date] + $val['value']) : $val['value'];
                                    }
                                }
                            }
                        }
                    }

                    $resaudaddarr = [];
                    foreach ($audaddarr as $key => $value) {
                        $resaudaddarr[] = [
                            'date' => $key,
                            'value' => $value
                        ];
                    }

                    $resaudremovedarr = [];
                    foreach ($audremovedarr as $key => $value) {
                        $resaudremovedarr[] = [
                            'date' => $key,
                            'value' => $value
                        ];
                    }

                    $resfacebook_like_age_arr = [];
                    foreach ($facebook_like_age_arr as $key => $value) {
                        $resfacebook_like_age_arr[] = [
                            'date' => $key,
                            'value' => $value
                        ];
                    }

                    $resfacebook_country_arr = [];
                    foreach (getCountriesForAnalytic($countryarr) as $key => $value) {
                        $resfacebook_country_arr[] = [
                            'key' => $key,
                            'value' => $value
                        ];
                    }

                    $resfacebook_city_arr = [];
                    foreach ($cityarr as $key => $value) {
                        $resfacebook_city_arr[] = [
                            'key' => $key,
                            'value' => $value
                        ];
                    }

                    //engagment

                    $engagementstype = [
                        'other' => 0,
                        'likes' => 0,
                        'share' => 0,
                        'comment' => 0,
                    ];

                    $audienceengagement = [];

                    $facebook_engaged_analytic = $analytic->facebook_engaged_analytic ?? '{}';
                    $facebook_engaged_analytic = json_decode($facebook_engaged_analytic, true);

                    foreach ($facebook_engaged_analytic as $fla) {
                        //engagment donut chart
                        if (isset($fla['page_enaged_all'][0]['values'])) {
                            // $engagementstype['likes'] = isset($fla['page_enaged_all'][2]['values'][1]['value']['like']) ? $fla['page_enaged_all'][2]['values'][1]['value']['like'] + $engagementstype['likes'] : $engagementstype['likes'] ;
                            // $engagementstype['other'] = isset($fla['page_enaged_all'][2]['values'][1]['value']['other']) ? $fla['page_enaged_all'][2]['values'][1]['value']['other'] + $engagementstype['likes'] : $engagementstype['other'] ;
                            // $engagementstype['share'] = isset($fla['page_enaged_all'][2]['values'][1]['value']['link']) ? $fla['page_enaged_all'][2]['values'][1]['value']['link'] + $engagementstype['likes'] : $engagementstype['share'] ;
                            // $engagementstype['comment'] = isset($fla['page_enaged_all'][2]['values'][1]['value']['comment']) ? $fla['page_enaged_all'][2]['values'][1]['value']['comment'] + $engagementstype['comment'] : $engagementstype['comment'] ;
                            $engagementstype['likes'] = isset($fla['page_enaged_all'][0]['values']) ? array_sum(array_column(array_column($fla['page_enaged_all'][0]['values'], 'value'), 'like')) : $engagementstype['likes'];
                            $engagementstype['other'] = isset($fla['page_enaged_all'][0]['values']) ? array_sum(array_column(array_column($fla['page_enaged_all'][0]['values'], 'value'), 'other')) : $engagementstype['other'];
                            $engagementstype['share'] = isset($fla['page_enaged_all'][0]['values']) ? array_sum(array_column(array_column($fla['page_enaged_all'][0]['values'], 'value'), 'link')) : $engagementstype['share'];
                            $engagementstype['comment'] = isset($fla['page_enaged_all'][0]['values']) ? array_sum(array_column(array_column($fla['page_enaged_all'][0]['values'], 'value'), 'comment')) : $engagementstype['comment'];
                        }

                        //engaged chart

                        if (isset($fla['page_enaged_all'][0]['values'])) {
                            foreach ($fla['page_enaged_all'][0]['values'] as $key => $value) {
                                $date = \Carbon\Carbon::parse($value['end_time']['date'])->format('d M');
                                $audienceengagement[$date] = isset($audienceengagement[$date]) ? ($audienceengagement[$date] + array_sum($value['value'])) : (count($value['value']) > 0 ? array_sum($value['value']) : 0);
                            }
                        }
                    }

                    $resaudienceengagement = [];
                    foreach ($audienceengagement as $key => $value) {
                        $resaudienceengagement[] = [
                            'date' => $key,
                            'value' => $value
                        ];
                    }

                    // Facebook Reach

                    $facebook_react_analytic = $analytic->facebook_react_analytic ?? '{}';
                    $facebook_react_analytic = json_decode($facebook_react_analytic, true);

                    $videoviewpaid = 0;
                    $videovieworganic = 0;
                    $page_Organic_reach = 0;
                    $page_paid_reach = 0;

                    $agereacharr = [
                        '13-17' => 0,
                        '18-24' => 0,
                        '25-34' => 0,
                        '35-44' => 0,
                        '45-54' => 0,
                        '55-64' => 0,
                        '65+' => 0,
                    ];

                    $agereacharr1 = [
                        'Male' => 0,
                        'Female' => 0,
                        'Unspecified' => 0,
                    ];
                    $totlareachArr = [];
                    $countryreacharr = [];
                    $citiesreacharr = [];

                    foreach ($facebook_react_analytic as $fla) {

                        if (isset($fla['page_video_views_organic'][0]['values'][1]['value'])) {
                            // $videovieworganic = $fla['page_video_views_organic'][2]['values'][1]['value'] + $videovieworganic;
                            $videovieworganic += array_sum(array_column($fla['page_video_views_organic'][0]['values'], 'value'));
                        }
                        if (isset($fla['page_video_views_paid'][0]['values'][1]['value'])) {
                            $videoviewpaid += array_sum(array_column($fla['page_video_views_paid'][0]['values'], 'value'));
                        }
                        if (isset($fla['page_organic_reach'][0]['values'])) {
                            // $videovieworganic = $fla['page_video_views_organic'][2]['values'][1]['value'] + $videovieworganic;
                            $page_Organic_reach += array_sum(array_column($fla['page_organic_reach'][0]['values'], 'value'));
                        }
                        if (isset($fla['page_paid_reach'][0]['values'])) {
                            // $videovieworganic = $fla['page_video_views_organic'][2]['values'][1]['value'] + $videovieworganic;
                            $page_paid_reach += array_sum(array_column($fla['page_paid_reach'][0]['values'], 'value'));
                        }
                        //page react // gender // age

                        if (isset($fla['page_reach_by_gender'][0]['values'])) {
                            foreach ($fla['page_reach_by_gender'][0]['values'] as $key => $value) {
                                if (isset($value['value'])) {
                                    foreach ($value['value'] as $key1 => $value1) {
                                        if (array_key_exists(substr($key1, 2), $agereacharr)) {
                                            $agereacharr[substr($key1, 2)] = $agereacharr[substr($key1, 2)] + $value1;

                                            if (str_contains($key1, 'M.')) {
                                                $agereacharr1['Male'] += $value1;
                                            }
                                            if (str_contains($key1, 'F.')) {
                                                $agereacharr1['Female'] += $value1;
                                            }
                                            if (str_contains($key1, 'U.')) {
                                                $agereacharr1['Unspecified'] += $value1;
                                            }
                                        }
                                    }
                                }
                                if (isset($fla['page_reach'][0]['values'])) {
                                    foreach ($fla['page_reach'][0]['values'] as $key => $value) {
                                        $date = \Carbon\Carbon::parse($value['end_time']['date'])->subDay()->format('d M');
                                        $totlareachArr[$date] = $value['value'] ?? 0;
                                    }
                                }
                            }
                        }
                        // Countries
                        $data = [];
                        if (isset($fla['page_content_activity_by_country_unique'][0]['values'][1]['value'])) {

                            $data = array_map(function ($value) {
                                return array_keys($value['value']);
                            }, $fla['page_content_activity_by_country_unique'][0]['values']);

                            $data = array_unique(array_flatten($data));
                            foreach ($fla['page_content_activity_by_country_unique'][0]['values'] as $key => $value) {
                                foreach ($value['value'] as $key1 => $countryData) {
                                    if (in_array($key1, $data) && isset($countryreacharr[$key1])) {
                                        $countryreacharr[$key1] += $countryData;
                                    } else {
                                        $countryreacharr[$key1] = $countryData;
                                    }
                                }
                            }
                        }
                        //cities
                        if (isset($fla['page_content_activity_by_city_unique'][0]['values'])) {
                            foreach ($fla['page_content_activity_by_city_unique'][0]['values'] as $key => $cities) {
                                foreach ($cities['value'] as $key => $city) {
                                    if (isset($citiesreacharr[$key])) $citiesreacharr[$key] += $city;
                                    else $citiesreacharr[$key] = $city;
                                }
                            }
                        }
                    }

                    // total reach
                    $restotlareachArr = [];
                    foreach ($totlareachArr as $key => $value) {
                        $restotlareachArr[] = [
                            'date' => $key,
                            'value' => $value
                        ];
                    }
                    $resagereacharr = [];
                    foreach ($agereacharr as $key => $value) {
                        $resagereacharr[] = [
                            'date' => $key,
                            'value' => $value
                        ];
                    }
                    $rescitiesreacharr = [];
                    foreach ($citiesreacharr as $key => $value) {
                        $rescitiesreacharr[] = [
                            'key' => $key,
                            'value' => $value
                        ];
                    }

                    $rescountryreacharr = [];
                    foreach (getCountriesForAnalytic($countryreacharr) as $key => $value) {
                        $rescountryreacharr[] = [
                            'key' => $key,
                            'value' => $value
                        ];
                    }

                    // Facebook Posts
                    $facebook_posts = $analytic->facebook_post ?? '{}';
                    $facebook_posts = json_decode($facebook_posts, true);
                    $facebook_posts = call_user_func_array('array_merge', $facebook_posts);
                    $total_reach_data = $facebook_posts ?  array_map(function ($post) {
                        return isset($post['postreach']) ? $post['postreach'][0]['values'][0]['value'] : '';
                    }, $facebook_posts) : '{}';
                    if ($facebook_posts) array_multisort($total_reach_data, SORT_DESC, $facebook_posts);
                    $facebook_posts = array_map(function ($post) {
                        return [
                            'created_date' => \Carbon\Carbon::parse($post['created_time']['date'])->format('j M, Y') ?? '',
                            'image' => $post['attachments']['image']['src'] ?? '',
                            'post_description' => $post['message'] ?? '',
                            'reach' => $post['postreach'][0]['values'][0]['value'] ?? 0,
                            'likes' => $feed['reactions'] ?? 0,
                            'shares' => $feed['post_share_count'] ?? 0,
                            'clicks' => $feed['insights'][0]['values'][0]['value'] ?? 0,
                            'post_activity_unique' => $feed['post_activity_unique'][0]['values'][0]['value'] ?? 0,
                        ];
                    }, $facebook_posts);


                    if (isset($analytic->facebook_like_analytic) || isset($analytic->facebook_engaged_analytic) || isset($analytic->facebook_react_analytic)) {

                        $resData = [
                            'facebook_total_like' => $ftotal_likes,
                            // 'facebook_total_like' => ($organic_like + $paid_like),
                            'facebook_organic_paid_like' => [
                                [
                                    'organic' => $organic_like,
                                    'paid' => $paid_like
                                ]
                            ],
                            'facebook_audience_growth_like' => $resaudaddarr,
                            'facebook_audience_remove_like' => $resaudremovedarr,
                            'facebook_age_like' => $resfacebook_like_age_arr,
                            'facebook_gender_like' => [
                                [
                                    'male' => $facebook_like_gender['Male'],
                                    'female' => $facebook_like_gender['Female'],
                                    'unspecified' => $facebook_like_gender['Unspecified'],
                                ]
                            ],
                            'facebook_country_like' => $resfacebook_country_arr,
                            'facebook_city_like' => $resfacebook_city_arr,
                            'facebook_audience_engagement' => $resaudienceengagement,
                            'facebook_engagement' => [
                                $engagementstype
                            ],
                            'facebook_total_reach' => $restotlareachArr,
                            'facebook_video_view_reach' => [
                                [
                                    'paid' => $videoviewpaid,
                                    'organic' => $videovieworganic
                                ]
                            ],
                            'facebook_gender_reach' => [
                                [
                                    'male' => $agereacharr1['Male'],
                                    'female' => $agereacharr1['Female'],
                                    'unspecified' => $agereacharr1['Unspecified'],
                                ]
                            ],
                            'facebook_organic_vs_paid_reach' => [
                                [
                                    'paid' => $page_paid_reach,
                                    'organic' => $page_Organic_reach,
                                ]
                            ],
                            'facebook_age_reach' => $resagereacharr,
                            'facebook_country_reach' => $rescountryreacharr,
                            'facebook_city_reach' => $rescitiesreacharr,
                            'facebook_analytics_posts' => $facebook_posts,
                            'is_available' => true
                        ];
                    } else {
                        $resData = [
                            'is_available' => false
                        ];
                    }

                    return (new AnalyticResource($resData))
                        ->additional([
                            'meta' => [
                                'message'     =>    trans('api.list', ['entity' => 'Analytic'])
                            ]
                        ]);
                }
            } catch (\Exception $ex) {
                // dd($ex->getMessage(), $ex->getLine());
                // $this->status = $this->statusArr['something_wrong'];
                $this->status = $ex->getMessage();
                $this->response['meta']['message'] = trans('api.error');
            }
        }
        return $this->return_response();
    }


    public function refreshAnalytic(Request $request)
    {

        $rules = [
            'type'     => 'required|in:linkedin,twitter,tweets,facebook',
        ];
        if ($this->apiValidator($request->all(), $rules)) {
            try {

                $user = auth()->user()->parentUser ?? auth()->user();
                if ($request->type == 'twitter') {
                    $data = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 3)->first();
                    if (isset($data->id) && isset($data->token) && isset($data->social_id)) {
                        $connection = new TwitterOAuth(config('utility.TWITTER_CONSUMER_KEY'), config('utility.TWITTER_CONSUMER_SECRET'), $data->token, $data->token_secret);
                        $verify_credentials = $connection->get("account/verify_credentials");
                        $connection->setApiVersion('1.1');
                        $retweets_of_me = $connection->get('statuses/retweets_of_me');
                        $followerlist = $connection->get('followers/list');
                        $twitter_analytic  = [
                            'followers_count' => $verify_credentials->followers_count,
                            'tweet_count' => $verify_credentials->statuses_count,
                            'like_count' => $verify_credentials->favourites_count,
                            'like_count' => $verify_credentials->favourites_count,
                            'retweet_count' => (isset($retweets_of_me) && count($retweets_of_me)) ? count($retweets_of_me) : 0
                        ];
                        Analytics::updateOrCreate(
                            ['user_id' => $user->id],
                            [
                                'twitter_analytic' => json_encode($twitter_analytic),
                                'twitter_follower_analytic' => json_encode($followerlist)
                            ]
                        );
                        $this->status = $this->statusArr['success'];
                        $this->response['meta']['api'] = $this->version;
                        $this->response['meta']['url'] = url()->current();
                        $this->response['meta']['message']  =  trans('api.attend', ['entity' => 'Data', 'detail' => 'Refresh']);
                    } else {

                        $this->status = $this->statusArr['something_wrong'];
                        $this->response['meta']['api'] = $this->version;
                        $this->response['meta']['url'] = url()->current();
                        $this->response['meta']['message']  =  'Please connect X(Twitter) in your account';
                    }
                }

                if ($request->type == 'tweets') {
                    $data = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 3)->first();
                    if (isset($data->id) && isset($data->token) && isset($data->social_id)) {
                        $connection = new TwitterOAuth(config('utility.TWITTER_CONSUMER_KEY'), config('utility.TWITTER_CONSUMER_SECRET'), $data->token, $data->token_secret);
                        $connection->setApiVersion('2');
                        $tweets = $connection->get("users/" . $data->social_id . "/tweets", ['max_results' => 100]);
                        if (isset($tweets->data) && count($tweets->data) > 0) {
                            $twarr = [];
                            foreach ($tweets->data as $tweet) {

                                $twt = $connection->get("tweets/" . $tweet->id, [
                                    'tweet.fields' => 'created_at,text,id,attachments,public_metrics',
                                    'media.fields' => 'url',
                                    'expansions' => 'attachments.media_keys'
                                ]);
                                $twarr[] = $twt;
                            }

                            Analytics::updateOrCreate(
                                ['user_id' => $user->id],
                                ['twitter_tweet_analytic' => json_encode($twarr)]
                            );
                        }

                        $this->status = $this->statusArr['success'];
                        $this->response['meta']['api'] = $this->version;
                        $this->response['meta']['url'] = url()->current();
                        $this->response['meta']['message']  =  trans('api.attend', ['entity' => 'Data', 'detail' => 'Refresh']);
                    } else {

                        $this->status = $this->statusArr['something_wrong'];
                        $this->response['meta']['api'] = $this->version;
                        $this->response['meta']['url'] = url()->current();
                        $this->response['meta']['message']  =  'Please connect X(Twitter) in your account';
                    }
                }

                if ($request->type == 'linkedin') {

                    if (isset($request->start_date) && isset($request->end_date)) {
                        $start = \Carbon\Carbon::parse($request->start_date)->timestamp * 1000;
                        $end = \Carbon\Carbon::parse($request->end_date)->timestamp * 1000;
                    } else {
                        $start = \Carbon\Carbon::now()->startOfMonth()->timestamp * 1000;
                        $end = \Carbon\Carbon::now()->endOfMonth()->timestamp * 1000;
                    }

                    $social_media = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 2)->first();
                    if (isset($social_media->token) && isset($social_media->social_id)) {

                        $data = LinkedInService::getEnagagmentData($social_media->social_id, $social_media->token, $start, $end);

                        Analytics::updateOrCreate(
                            ['user_id' => $user->id],
                            [
                                'linkedin_follower' => json_encode($data['followdata']),
                                'linkedin_time_follower' => json_encode($data['timefollowdata']),
                                'linkedin_time_click' => json_encode($data['clickdata']),
                                'linkedin_geo_data' => json_encode($data['geoData']),
                                'linkedin_seniority_data' => json_encode($data['seniorityData']),
                                'linkedin_function_data' => json_encode($data['functionData']),
                                'linkedin_industry_data' => json_encode($data['industryData']),
                            ]
                        );
                        $this->status = $this->statusArr['success'];
                        $this->response['meta']['api'] = $this->version;
                        $this->response['meta']['url'] = url()->current();
                        $this->response['meta']['message']  =  trans('api.attend', ['entity' => 'Data', 'detail' => 'Refresh']);
                    } else {
                        $this->status = $this->statusArr['something_wrong'];
                        $this->response['meta']['api'] = $this->version;
                        $this->response['meta']['url'] = url()->current();
                        $this->response['meta']['message']  =  'Please connect linkedin in your account';
                    }
                }

                if ($request->type == 'facebook') {
                    $facebookRes = \App\Http\Controllers\AnalyticsController::FacebookAnalitics('facebook_like');
                    if (isset($facebookRes['status']) && $facebookRes['status'] == 'error') {

                        $this->status = $this->statusArr['something_wrong'];
                        $this->response['meta']['api'] = $this->version;
                        $this->response['meta']['url'] = url()->current();
                        $this->response['meta']['message']  =  $facebookRes['message'];
                        return $this->return_response();
                    }
                    if (isset($facebookRes['status']) && $facebookRes['status'] == 'success') {
                        \App\Http\Controllers\AnalyticsController::FacebookAnalitics('facebook_engagement');
                        \App\Http\Controllers\AnalyticsController::FacebookAnalitics('facebook_reach');
                    }

                    $this->status = $this->statusArr['success'];
                    $this->response['meta']['api'] = $this->version;
                    $this->response['meta']['url'] = url()->current();
                    $this->response['meta']['message']  =  trans('api.attend', ['entity' => 'Data', 'detail' => 'Refresh']);
                }
            } catch (\Exception $ex) {

                $this->status = $this->statusArr['something_wrong'];
                $this->response['meta']['message'] = trans('api.error');
            }
        }
        return $this->return_response();
    }

    // public function LinkedinAnalytics(Request $request)
    // {
    //     $request['page_update'] = 0;
    //     $check_page_ids = $this->changePageIds();
    //     if ($check_page_ids) $request['page_update'] = 1;

    //     $linkedinObject = new AnalyticsController();
    //     $data = $linkedinObject->LinkedinAnalytics($request);
    //     dd($data);
    // }

    // private function changePageIds()
    // {
    //     $check_data = 1;
    //     $current_qury_string = request()->all();
    //     $session_hash = Session::get('analytic_page_ids');
    //     $analytic_page_ids = array_key_exists('page_id', $current_qury_string) ? $current_qury_string['page_id'] : 'all';
    //     if ($session_hash) {
    //         if ($analytic_page_ids === $session_hash) {
    //             $check_data = 0;
    //             return $check_data;
    //         }
    //     }
    //     Session::put('analytic_page_ids', $analytic_page_ids);
    //     return $check_data;
    // }

    public function googleBusinessAnalytics(Request $request)
    {
        $rules = [
            'type'          => 'required|in:platform_device,calls_website_direction,message_booking_food,posts,reviews,interactions',
            'start_date'    => 'nullable|date|date_format:m/d/Y',
            'end_date'      => 'nullable|date|date_format:m/d/Y|after:start_date',
            'page_ids'       => 'required'
        ];
        $resData = [];
        if ($this->apiValidator($request->all(), $rules)) {
            try {
                $googleMyBusinessService = new GoogleMyBusinessService();
                $resdata = AnalyticsController::googleMyBusinessAnalyticsData($request, $googleMyBusinessService);
                $this->response['data'] = $request->type == 'reviews' ? ['reviews' => $resdata['data'], 'average_rating' => $resdata['average_rating'], 'review_count' => $resdata['review_count']] : $resdata['data'];
                $this->status = $this->statusArr['success'];
                $this->response['meta']['message'] = trans('api.success');
            } catch (Exception $e) {

                $this->response['status'] = $this->statusArr['success'];
                $this->response['meta']['message'] = trans('api.error');
            }
        }
        $this->response['meta']['api'] = $this->version;
        $this->response['meta']['url'] = url()->current();
        return $this->return_response();
    }

    // Google Analytics
    public function googleAnalytics(Request $request)
    {
        $rules = [
            'type'          => 'required|in:' . implode(',', array_keys(config('utility.google_analytics.request_type'))),
            'start_date'    => 'nullable|date|date_format:m/d/Y',
            'end_date'      => 'nullable|date|date_format:m/d/Y|after:start_date',
            'page_ids'      => 'required'
        ];

        if ($this->apiValidator($request->all(), $rules)) {
            $this->status = $this->statusArr['not_found'];
            $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Google analytics")]);
            try {
                $analyticsController = new AnalyticsController();
                $googleAnalyticsServiceData = $analyticsController->googleAnalytics($request, $request->type, 'api');

                if ($googleAnalyticsServiceData && $googleAnalyticsServiceData->original['status'] == 200) {
                    $this->response['data']             =   $googleAnalyticsServiceData->original['data'];
                    $this->status                       =   $this->statusArr['success'];
                    $this->response['meta']['message']  =   trans('api.list', ['entity' => __("Google analytics")]);
                }
            } catch (Exception $e) {
                $this->status = $this->statusArr['forbidden'];
                $this->response['meta']['message'] = trans('api.error');
            }
        }
        $this->response['meta']['api'] = $this->version;
        $this->response['meta']['url'] = url()->current();
        return response()->json($this->response, $this->status);
    }

    // Facebook Ads Analytics
    public function facebookAdsReporting(Request $request)
    {
        $rules = [
            'type'          => 'required',
            'start_date'    => 'nullable|date|date_format:m/d/Y',
            'end_date'      => 'nullable|date|date_format:m/d/Y|after:start_date',
            'page_ids'      => 'required',
            'campaign_id'   => 'required_if:type,single_campaign_publisher_plateform_pie_chart,single_campaign_clicks_line_chart',
        ];

        if ($this->apiValidator($request->all(), $rules)) {
            $this->status = $this->statusArr['not_found'];
            $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Facebook Ads")]);
            try {
                $analyticsController = new AnalyticsController();
                $facebookAdsAnalyticsData = $analyticsController->facebookAdsReporting($request, $request->type, 'api');
                // dd($facebookAdsAnalyticsData, '$facebookAdsAnalyticsData');
                if ($facebookAdsAnalyticsData && $facebookAdsAnalyticsData->original['status'] == 200) {
                    $this->response['data']             =   $facebookAdsAnalyticsData->original['data'];
                    $this->status                       =   $this->statusArr['success'];
                    $this->response['meta']['message']  =   trans('api.list', ['entity' => __("Facebook Ads")]);
                }
            } catch (Exception $e) {
                $this->status = $this->statusArr['forbidden'];
                $this->response['meta']['message'] = trans('api.error');
            }
        }
        $this->response['meta']['api'] = $this->version;
        $this->response['meta']['url'] = url()->current();
        return response()->json($this->response, $this->status);
    }
    // Instagram Analytics
    public function instagramAnalytics(Request $request)
    {
        $rules = [
            'type'          => 'required|in:dicovery_new_followers_bar_chart_and_count,dicovery_reach_impression_line_chart,interaction_line_chart_and_count,audiance_age_bar_chart,audiance_gender_pie_chart,audiance_country_table_data,audiance_city_table_data,instagram_posts_table',
            'start_date'    => 'nullable|date|date_format:m/d/Y',
            'end_date'      => 'nullable|date|date_format:m/d/Y|after:start_date',
            'page_ids'      => 'required',
        ];
        if ($this->apiValidator($request->all(), $rules)) {
            $this->status = $this->statusArr['not_found'];
            $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Instagram")]);
            try {
                $analyticsController = new AnalyticsController();
                $instagramAnalyticsData = $analyticsController->instagramAnalytics($request, $request->type, 'api');

                if ($instagramAnalyticsData && $instagramAnalyticsData->original['status'] == 200) {
                    $this->response['data']             =   $instagramAnalyticsData->original['data'];
                    $this->status                       =   $this->statusArr['success'];
                    $this->response['meta']['message']  =   trans('api.list', ['entity' => __("Instagram")]);
                }
            } catch (Exception $e) {
                $this->status = $this->statusArr['forbidden'];
                $this->response['meta']['message'] = trans('api.error');
            }
        }
        $this->response['meta']['api'] = $this->version;
        $this->response['meta']['url'] = url()->current();
        return response()->json($this->response, $this->status);
    }
    // Google Ads Analytics
    public function googleAdsAnalytics(Request $request)
    {
        $rules = [
            'type'          => 'required|in:gads_campaigns_rows,gads_keywords_rows,gads_search_terms_rows,gads_conversion_rows,gads_line_chart',
            'start_date'    => 'nullable|date|date_format:m/d/Y',
            'end_date'      => 'nullable|date|date_format:m/d/Y|after:start_date',
            'page_ids'      => 'required',
        ];
        if ($this->apiValidator($request->all(), $rules)) {
            $this->status = $this->statusArr['not_found'];
            $this->response['meta']['message'] = trans('api.not_found', ['entity' => __("Google Ads")]);
            try {
                $analyticsController = new AnalyticsController();
                $googleAdsAnalyticsData = $analyticsController->getGoogleAdsReporting($request, $request->type, 'api');

                if ($googleAdsAnalyticsData && $googleAdsAnalyticsData->original['status'] == 200) {
                    $this->response['data']             =   $googleAdsAnalyticsData->original['data'];
                    $this->status                       =   $this->statusArr['success'];
                    $this->response['meta']['message']  =   trans('api.list', ['entity' => __("Google Ads")]);
                }
            } catch (Exception $e) {
                $this->status = $this->statusArr['forbidden'];
                $this->response['meta']['message'] = trans('api.error');
            }
        }
        $this->response['meta']['api'] = $this->version;
        $this->response['meta']['url'] = url()->current();
        return response()->json($this->response, $this->status);
    }
}
