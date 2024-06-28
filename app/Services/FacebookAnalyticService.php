<?php

namespace App\Services;

use Session;
use Facebook\Facebook;
use App\Models\Analytics;
use App\Models\MediaPage;
use App\Models\SocialMediaDetail;
use Illuminate\Support\Facades\Http;

class FacebookAnalyticService
{
    protected static $pages = [];
    /* Facebook Like Analytic*/
    public function FacebookLikes($socialMedia, $start, $end)
    {

        $page_id = Session::get('page_ids') != 'all' ? explode(',', Session::get('page_ids')) : null;
        $facebook_like_analytic = [];
        $user = auth()->user()->parentUser ?? auth()->user();
        foreach ($socialMedia as $fbmedia) {

            $facebook = new Facebook([
                'app_id' => config('utility.FACEBOOK_APP_ID'),
                'app_secret' => config('utility.FACEBOOK_APP_SECRET'),
                'default_graph_version' => config('utility.DEFAULT_FACEBOOK_API_VERSION'),
            ]);
            // $response = $facebook->get('/' . $fbmedia->social_id . '/accounts?fields=name,access_token', $fbmedia->token);
            // $facebook_pages = $response->getGraphEdge()->asArray();
            $facebook_pages = self::getFacebookPages($fbmedia->social_id, $fbmedia->token);
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


            if ($facebook_pages) {

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
            }
        }
        return [
            'facebook_like_analytic' => $facebook_like_analytic
        ];
    }

    public function prepareFacebookLikeData()
    {
        $user = auth()->user()->parentUser ?? auth()->user();
        $analytic =  $user->Analytic;

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
                $ftotal_likes += $fla['like'][0]['values'][count($fla['like'][0]['values']) - 1]['value'];
            }

            if (isset($fla['paid_organic_like'][0]['values'])) {
                $paid_like += array_sum(array_column(array_column($fla['paid_organic_like'][0]['values'], 'value'), 'ads'));
            }
            if (isset($fla['paid_organic_like'][0]['values'])) {
                $organic_like += array_sum(array_column(array_column($fla['paid_organic_like'][0]['values'], 'value'), 'Your Page'));
                $organic_like += array_sum(array_column(array_column($fla['paid_organic_like'][0]['values'], 'value'), 'Other'));
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
                $country_count = count($fla['page_fans_country'][0]['values']) - 1;

                foreach ($fla['page_fans_country'][0]['values'][$country_count]['value'] as $key => $value) {
                    $countryarr[$key] = ($countryarr[$key] ?? 0) + $value;
                }
            }

            //city
            if (isset($fla['page_fans_city'][0]['values'][1]['value'])) {
                $city_count = count($fla['page_fans_country'][0]['values']) - 1;
                foreach ($fla['page_fans_city'][0]['values'][$city_count]['value'] as $key => $value) {
                    $cityarr[$key] = ($cityarr[$key] ?? 0) + $value;
                }
            }

            //audience groth/lost
            if (isset($fla['page_fan_adds'])) {
                $page_fan_addsarr = array_filter($fla['page_fan_adds'], function ($arr) {
                    return $arr['period'] == 'day';
                });

                if (isset($page_fan_addsarr) && count($page_fan_addsarr) > 0) {
                    $weekarr = array_values($page_fan_addsarr);
                    if (isset($weekarr) && isset($weekarr[0]['values'])) {
                        foreach ($weekarr[0]['values'] as $val) {
                            $date = \Carbon\Carbon::parse($val['end_time']['date'])->subDay()->format('d M');
                            $audaddarr[$date] = isset($audaddarr[$date]) ? ($audaddarr[$date] + $val['value']) : $val['value'];
                        }
                    }
                }
            }

            if (isset($fla['page_fan_removes'])) {
                $page_fan_removesarr = array_filter($fla['page_fan_removes'], function ($arr) {
                    return $arr['period'] == 'day';
                });

                if (isset($page_fan_removesarr) && count($page_fan_removesarr) > 0) {
                    $auddayarr = array_values($page_fan_removesarr);
                    if (isset($auddayarr) && isset($auddayarr[0]['values'])) {
                        foreach ($auddayarr[0]['values'] as $val) {
                            $date = \Carbon\Carbon::parse($val['end_time']['date'])->subDay()->format('d M');
                            $audremovedarr[$date] = isset($audremovedarr[$date]) ? ($audremovedarr[$date] + $val['value']) : $val['value'];
                        }
                    }
                }
            }
        }

        $facebook_like_paid_organic_arr = [$organic_like, $paid_like];
        $facebook_like_paid_organic_arr = $facebook_like_paid_organic_arr;

        $audaddkey = array_keys($audaddarr);
        $audaddvalue = array_values($audaddarr);
        $audremovevalue = array_values($audremovedarr);

        $audremovevalue = array_map(function ($item) {
            return "-" . $item;
        }, $audremovevalue);

        $facebookagekey = array_keys($facebook_like_age_arr);
        $facebookagevalue = array_values($facebook_like_age_arr);

        $facebook_like_gender_arr = [
            $facebook_like_gender['Male'], $facebook_like_gender['Female'],
            $facebook_like_gender['Unspecified']
        ];

        //Engagment
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
            if (isset($fla['page_enaged_all'][0]['values'][1]['value'])) {
                $engagementstype['likes'] = isset($fla['page_enaged_all'][2]['values'][1]['value']['like']) ?
                    $fla['page_enaged_all'][2]['values'][1]['value']['like'] + $engagementstype['likes'] : $engagementstype['likes'];
                $engagementstype['other'] = isset($fla['page_enaged_all'][2]['values'][1]['value']['other']) ?
                    $fla['page_enaged_all'][2]['values'][1]['value']['other'] + $engagementstype['likes'] : $engagementstype['other'];
                $engagementstype['share'] = isset($fla['page_enaged_all'][2]['values'][1]['value']['link']) ?
                    $fla['page_enaged_all'][2]['values'][1]['value']['link'] + $engagementstype['likes'] : $engagementstype['share'];
                $engagementstype['comment'] = isset($fla['page_enaged_all'][2]['values'][1]['value']['comment']) ?
                    $fla['page_enaged_all'][2]['values'][1]['value']['comment'] + $engagementstype['comment'] : $engagementstype['comment'];
            }

            //engaged chart

            if (isset($fla['page_engaged'][2]['values'])) {
                foreach ($fla['page_engaged'][2]['values'] as $key => $value) {
                    $date = \Carbon\Carbon::parse($value['end_time']['date'])->subDay()->format('d M');
                    $audienceengagement[$date] = isset($audienceengagement[$date]) ? ($audienceengagement[$date] + $value['value']) :
                        $value['value'];
                }
            }
        }
        $fbengageddonut = [
            $engagementstype['likes'],
            $engagementstype['other'],
            $engagementstype['share'],
            $engagementstype['comment']
        ];

        $fb6key = array_keys($audienceengagement);
        $fb6value = array_values($audienceengagement);


        arsort($countryarr);
        $countryarr = array_slice($countryarr, 0, 10);

        arsort($cityarr);
        $cityarr = array_slice($cityarr, 0, 10);

        return [
            'facebook_like_paid_organic_arr' => $facebook_like_paid_organic_arr,
            'facebook_like_gender_arr' => $facebook_like_gender_arr,
            'facebook_like_audience_growth_arr' => [
                'labels' => $audaddkey,
                'value' => $audaddvalue,
                'audremovevalue' => $audremovevalue,
            ],
            'facebook_like_age' => [
                'labels' => $facebookagekey,
                'value' => $facebookagevalue
            ],
            'countries' => getCountriesForAnalytic($countryarr),
            'cities' => $cityarr,
            'total_likes' => $ftotal_likes,
        ];
    }

    public function getFacebookLikeAnalytics($start_date, $end_date, $page_update = 0)
    {
        $user = auth()->user()->parentUser ?? auth()->user();
        if ($page_update) {
            $socialMedia = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 1)->get();
            if (!empty($socialMedia)) {
                $data = self::FacebookLikes($socialMedia, $start_date, $end_date);
                Analytics::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'facebook_like_analytic' => json_encode($data['facebook_like_analytic'])
                    ]
                );
            }
            return self::prepareFacebookLikeData();
        } else {
            return self::prepareFacebookLikeData();
        }
    }

    /* Facebook Engagement Analytic*/

    public function FacebookEngagement($socialMedia, $start, $end)
    {

        $page_id = Session::get('page_ids') != 'all' ? explode(',', Session::get('page_ids')) : null;
        $facebook_engaged_analytic = [];
        $user = auth()->user()->parentUser ?? auth()->user();
        foreach ($socialMedia as $fbmedia) {

            $facebook = new Facebook();
            // $response = $facebook->get('/' . $fbmedia->social_id . '/accounts?fields=name,access_token', $fbmedia->token);
            // $facebook_pages = $response->getGraphEdge()->asArray();

            $facebook_pages = self::getFacebookPages($fbmedia->social_id, $fbmedia->token);

            $mediaPagesArr = MediaPage::where([
                'user_id' => $user->id,
                'media_id' => 1
            ])->whereHas('userMediaPages', function ($q) use ($user) {
                $q->where('user_id', $user->id);
                $q->where('is_deleted', 'n');
            })->when($page_id, function ($query) use ($page_id) {
                $query->whereIn('id', $page_id);
            })->pluck('page_id')->toArray();


            if ($facebook_pages) {

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
            }
        }
        return [
            'facebook_engaged_analytic' => $facebook_engaged_analytic
        ];
    }

    public function prepareFacebookEngagementData()
    {
        $user = auth()->user()->parentUser ?? auth()->user();
        $analytic =  $user->Analytic;

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
            //engagment donut
            if (isset($fla['page_enaged_all'][0]['values'])) {
                $engagementstype['likes'] = isset($fla['page_enaged_all'][0]['values']) ? array_sum(array_column(array_column($fla['page_enaged_all'][0]['values'], 'value'), 'like')) : $engagementstype['likes'];
                $engagementstype['other'] = isset($fla['page_enaged_all'][0]['values']) ? array_sum(array_column(array_column($fla['page_enaged_all'][0]['values'], 'value'), 'other')) : $engagementstype['other'];
                $engagementstype['share'] = isset($fla['page_enaged_all'][0]['values']) ? array_sum(array_column(array_column($fla['page_enaged_all'][0]['values'], 'value'), 'link')) : $engagementstype['share'];
                $engagementstype['comment'] = isset($fla['page_enaged_all'][0]['values']) ? array_sum(array_column(array_column($fla['page_enaged_all'][0]['values'], 'value'), 'comment')) : $engagementstype['comment'];
            }

            //engaged chart

            if (isset($fla['page_enaged_all'][0]['values'])) {
                foreach ($fla['page_enaged_all'][0]['values'] as $key => $value) {
                    $date = \Carbon\Carbon::parse($value['end_time']['date'])->subDay()->format('d M');
                    $audienceengagement[$date] = isset($audienceengagement[$date]) ? ($audienceengagement[$date] + array_sum($value['value'])) : (count($value['value']) > 0 ? array_sum($value['value']) : 0);
                }
            }
        }
        $fbengageddonut = [
            $engagementstype['likes'],
            $engagementstype['other'],
            $engagementstype['share'],
            $engagementstype['comment']
        ];

        $fb6key = array_keys($audienceengagement);
        $fb6value = array_values($audienceengagement);

        return [
            'fbengageddonut' => $fbengageddonut,
            'facebook_audience_engagement_arr' => [
                'labels' => $fb6key,
                'value' => $fb6value,
            ]
        ];
    }

    public function getFacebookEngagementAnalytics($start_date, $end_date, $page_update = 0)
    {
        $user = auth()->user()->parentUser ?? auth()->user();
        if ($page_update) {
            $socialMedia = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 1)->get();
            if (!empty($socialMedia)) {
                $data = self::FacebookEngagement($socialMedia, $start_date, $end_date);
                Analytics::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'facebook_engaged_analytic' => json_encode($data['facebook_engaged_analytic']),
                        'facebook_engagement_last_updated_at' => date('Y-m-d H:i:s', strtotime(NOW()))
                    ]
                );
            }
            return self::prepareFacebookEngagementData();
        } else {
            return self::prepareFacebookEngagementData();
        }
    }

    /* Facebook Reach */
    public function FacebookReach($socialMedia, $start, $end)
    {
        $page_id = Session::get('page_ids') != 'all' ? explode(',', Session::get('page_ids')) : null;
        $facebook_react_analytic = [];
        $user = auth()->user()->parentUser ?? auth()->user();
        foreach ($socialMedia as $fbmedia) {

            $facebook = new Facebook([
                'app_id' => config('utility.FACEBOOK_APP_ID'),
                'app_secret' => config('utility.FACEBOOK_APP_SECRET'),
                // 'default_graph_version' => config('utility.DEFAULT_FACEBOOK_API_VERSION'),
            ]);;
            // $response = $facebook->get('/' . $fbmedia->social_id . '/accounts?fields=name,access_token', $fbmedia->token);
            // $facebook_pages = $response->getGraphEdge()->asArray();
            $facebook_pages = self::getFacebookPages($fbmedia->social_id, $fbmedia->token);
            $mediaPagesArr = MediaPage::where([
                'user_id' => $user->id,
                'media_id' => 1
            ])->whereHas('userMediaPages', function ($q) use ($user) {
                $q->where('user_id', $user->id);
                $q->where('is_deleted', 'n');
            })->when($page_id, function ($query) use ($page_id) {
                $query->whereIn('id', $page_id);
            })->pluck('page_id')->toArray();


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
        }
        return [
            'facebook_react_analytic' => $facebook_react_analytic
        ];
    }
    public function prepareFacebookReachData()
    {
        $user = auth()->user()->parentUser ?? auth()->user();
        $analytic =  $user->Analytic;
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
            if (isset($fla['page_organic_reach'][0]['values'])) {
                // $videovieworganic = $fla['page_video_views_organic'][2]['values'][1]['value'] + $videovieworganic;
                $page_Organic_reach += array_sum(array_column($fla['page_organic_reach'][0]['values'], 'value'));
            }
            if (isset($fla['page_paid_reach'][0]['values'])) {
                // $videovieworganic = $fla['page_video_views_organic'][2]['values'][1]['value'] + $videovieworganic;
                $page_paid_reach += array_sum(array_column($fla['page_paid_reach'][0]['values'], 'value'));
            }
            if (isset($fla['page_video_views_paid'][0]['values'][1]['value'])) {
                // $videoviewpaid = $fla['page_video_views_paid'][2]['values'][1]['value'] + $videoviewpaid;
                $videoviewpaid += array_sum(array_column($fla['page_video_views_paid'][0]['values'], 'value'));
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
            //country
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
            // Cities
            if (isset($fla['page_content_activity_by_city_unique'][0]['values'])) {
                foreach ($fla['page_content_activity_by_city_unique'][0]['values'] as $key => $cities) {
                    foreach ($cities['value'] as $key => $city) {
                        if (isset($citiesreacharr[$key])) $citiesreacharr[$key] += $city;
                        else $citiesreacharr[$key] = $city;
                    }
                }
            }
        }

        $facebook_reach_video_view_arr = [$videovieworganic, $videoviewpaid];

        $trkey = array_keys($totlareachArr);
        $trvalue = array_values($totlareachArr);

        $facebook_organic_vs_paid_reach_arr = [$page_Organic_reach, $page_paid_reach];
        $facebook_gender_reach_arr = [$agereacharr1['Male'], $agereacharr1['Female'], $agereacharr1['Unspecified']];

        $fbreachkey = array_keys($agereacharr);
        $fbreachvalue = array_values($agereacharr);

        arsort($countryreacharr);
        $countryreacharr = array_slice($countryreacharr, 0, 10);

        arsort($citiesreacharr);
        $citiesreacharr = array_slice($citiesreacharr, 0, 10);

        return [
            'facebook_total_reach_arr' => [
                'lables' => $trkey,
                'value' => $trvalue
            ],
            'facebook_gender_reach_arr' => $facebook_gender_reach_arr,
            'facebook_organic_vs_paid_reach_arr' => $facebook_organic_vs_paid_reach_arr,
            'reach_countries' => $countryreacharr,
            'reach_cities' => $citiesreacharr,
            'facebook_reach_video_view_arr' => $facebook_reach_video_view_arr,
            'facebook_reach_engagement_age_arr' => [
                'labels' => $fbreachkey,
                'value' => $fbreachvalue
            ]
        ];
    }
    public function getFacebookReachAnalytics($start_date, $end_date, $page_update = 0)
    {
        $user = auth()->user()->parentUser ?? auth()->user();
        if ($page_update) {
            $socialMedia = SocialMediaDetail::where('user_id', $user->id)->where('media_id', 1)->get();
            if (!empty($socialMedia)) {
                $data = self::FacebookReach($socialMedia, $start_date, $end_date);
                Analytics::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'facebook_react_analytic' => json_encode($data['facebook_react_analytic']),
                        'facebook_reach_last_updated_at' => date('Y-m-d H:i:s', strtotime(NOW()))
                    ]
                );
            }
            return self::prepareFacebookReachData();
        } else {
            return self::prepareFacebookReachData();
        }
    }

    public function getFacebookPages($user_id, $user_token = null, $url = null)
    {
        $params = 'name,access_token';
        $url = $url  ?? "https://graph.facebook.com/" . config('utility.DEFAULT_FACEBOOK_API_VERSION') . "/{$user_id}/accounts?fields={$params}&access_token={$user_token}";
        // $response = $this->fb->get('/' . $user->id . '/accounts?fields=' . $params, $user->token, null, config('utility.DEFAULT_FACEBOOK_API_VERSION'));
        $response = Http::get($url);
        if ($response->failed()) $response->throw();
        $data = $response->json();

        self::$pages = isset($data['data']) ? array_merge(self::$pages, array_values($data['data'])) : [];

        if (isset($data['paging'], $data['paging']['next'])) {
            self::getFacebookPages($user_id, $data['paging']['next']);
        }
        return self::$pages;
    }
}
