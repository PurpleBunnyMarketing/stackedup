<?php

namespace App\Services;

use App\Models\SocialMediaDetail;
use Carbon\Carbon;
use Google\Client;
use App\Models\Analytics;
use Illuminate\Support\Facades\Http;
use Exception;

class GoogleMyBusinessService
{
    public function __construct($social_media_id = null)
    {
        $googleMediaDetails = SocialMediaDetail::where(['user_id' => auth()->user()->parent_id ?? auth()->id(), 'media_id' => 5])->first();
        if ($social_media_id != null) {
            $googleMediaDetails = SocialMediaDetail::where('id', $social_media_id)->first();
        }

        $googleClient = new Client();
        $googleClient->setClientId(config('utility.GOOGLE_CLIENT_ID'));
        $googleClient->setClientSecret(config('utility.GOOGLE_CLIENT_SECRET'));
        $googleClient->setRedirectUri(config('services.google.redirect'));
        $googleClient->setAccessToken($googleMediaDetails->token);

        // if ($googleMediaDetails && Carbon::now()->lt($googleMediaDetails->token_expiry)) {
        $access_token = $this->updateAccessToken($googleClient, $googleMediaDetails->refresh_token);
        // dd($access_token);

        $googleMediaDetails->update([
            'token' => $access_token['access_token'],
            // 'token_expiry' => Carbon::now()->addSeconds($access_token['expires_in']),
        ]);
        // }
    }

    public function getPlateformAndDeviceData($auth_user_token, $start_date, $end_date, $media_pages): array
    {
        $data = [];
        $impression_data = [];
        try {
            foreach ($media_pages as $media_page) {
                $start = Carbon::parse($start_date);
                $end = Carbon::parse($end_date);
                $url = "https://businessprofileperformance.googleapis.com/v1/{$media_page}:fetchMultiDailyMetricsTimeSeries?dailyMetrics=BUSINESS_IMPRESSIONS_DESKTOP_MAPS&dailyMetrics=BUSINESS_IMPRESSIONS_DESKTOP_SEARCH&dailyMetrics=BUSINESS_IMPRESSIONS_MOBILE_MAPS&dailyMetrics=BUSINESS_IMPRESSIONS_MOBILE_SEARCH&dailyRange.start_date.year={$start->format("Y")}&dailyRange.start_date.month={$start->format("n")}&dailyRange.start_date.day={$start->format("d")}&dailyRange.end_date.year={$end->format("Y")}&dailyRange.end_date.month={$end->format("n")}&dailyRange.end_date.day={$end->format("d")}";

                $plateform_device_response = Http::withHeaders(['Authorization' => 'Bearer ' . $auth_user_token])->get($url);


                if ($plateform_device_response->failed()) $plateform_device_response->throw();
                $palteform_device_data = $plateform_device_response->collect()->toArray()['multiDailyMetricTimeSeries'][0]['dailyMetricTimeSeries'];

                $desktop_maps_data = $this->getProcessMatricsArray(array_values(array_filter($palteform_device_data, fn ($matrics) => $matrics['dailyMetric'] == 'BUSINESS_IMPRESSIONS_DESKTOP_MAPS'))[0]);
                $desktop_search_data = $this->getProcessMatricsArray(array_values(array_filter($palteform_device_data, fn ($matrics) => $matrics['dailyMetric'] == 'BUSINESS_IMPRESSIONS_DESKTOP_SEARCH'))[0]);
                $mobile_maps_data = $this->getProcessMatricsArray(array_values(array_filter($palteform_device_data, fn ($matrics) => $matrics['dailyMetric'] == 'BUSINESS_IMPRESSIONS_MOBILE_MAPS'))[0]);
                $mobile_search_data = $this->getProcessMatricsArray(array_values(array_filter($palteform_device_data, fn ($matrics) => $matrics['dailyMetric'] == 'BUSINESS_IMPRESSIONS_MOBILE_SEARCH'))[0]);

                $keysArray = array_column($desktop_maps_data, 'date');
                for ($i = 0; $i < count($keysArray); $i++) {
                    $impression_data[] = [
                        'date' => $keysArray[$i],
                        'value' => $desktop_maps_data[$i]['value'] + $desktop_search_data[$i]['value'] + $mobile_maps_data[$i]['value'] + $mobile_search_data[$i]['value'],
                    ];
                }
                if (request()->ajax()) {
                    $data = [
                        'plateform_device' => [
                            'lables' => ['Desktop Maps', 'Desktop Search', 'Mobile Maps', 'Mobile Search'],
                            'values' => [array_sum(array_column($desktop_maps_data, 'value')), array_sum(array_column($desktop_search_data, 'value')), array_sum(array_column($mobile_maps_data, 'value')), array_sum(array_column($mobile_search_data, 'value'))],
                        ],
                        'desktop_vs_mobile' => [
                            'lables' => ['Desktop', 'Mobile'],
                            'values' => [array_sum(array_column($desktop_maps_data, 'value')) + array_sum(array_column($desktop_search_data, 'value')), array_sum(array_column($mobile_maps_data, 'value')) + array_sum(array_column($mobile_search_data, 'value'))]
                        ],
                        'search_vs_maps' => [
                            'lables' => ['Search', 'Maps'],
                            'values' => [array_sum(array_column($desktop_search_data, 'value')) + array_sum(array_column($mobile_search_data, 'value')), array_sum(array_column($desktop_maps_data, 'value')) + array_sum(array_column($mobile_maps_data, 'value'))]
                        ],
                        'impressions' => [
                            'lables' => array_column($impression_data, 'date'),
                            'values' => array_column($impression_data, 'value'),
                        ]
                    ];
                } else {

                    $data = [
                        'plateform_device' => [
                            array_combine(['Desktop Maps', 'Desktop Search', 'Mobile Maps', 'Mobile Search'], [array_sum(array_column($desktop_maps_data, 'value')), array_sum(array_column($desktop_search_data, 'value')), array_sum(array_column($mobile_maps_data, 'value')), array_sum(array_column($mobile_search_data, 'value'))])
                        ],
                        'desktop_vs_mobile' => [
                            array_combine(['Desktop', 'Mobile'], [array_sum(array_column($desktop_maps_data, 'value')) + array_sum(array_column($desktop_search_data, 'value')), array_sum(array_column($mobile_maps_data, 'value')) + array_sum(array_column($mobile_search_data, 'value'))])
                        ],
                        'search_vs_maps' => [array_combine(['Search', 'Maps'], [array_sum(array_column($desktop_search_data, 'value')) + array_sum(array_column($mobile_search_data, 'value')), array_sum(array_column($desktop_maps_data, 'value')) + array_sum(array_column($mobile_maps_data, 'value'))])],
                        'impressions' => $impression_data,
                    ];
                }
            }
            Analytics::updateorCreate(
                [
                    'user_id' => auth()->user()->parent_id ?? auth()->id(),
                ],
                [
                    'google_plateform_device' => json_encode($data),
                ]
            );
        } catch (Exception $e) {
            // dd($e->getMessage());
            return [];
        }
        return $data;
    }

    public function getCallsData($auth_user_token, $start_date, $end_date, $media_pages): array
    {

        $data = [];
        try {
            foreach ($media_pages as $media_page) {
                $start = Carbon::parse($start_date);
                $end = Carbon::parse($end_date);
                $url = "https://businessprofileperformance.googleapis.com/v1/{$media_page}:fetchMultiDailyMetricsTimeSeries?dailyMetrics=CALL_CLICKS&dailyMetrics=BUSINESS_DIRECTION_REQUESTS&dailyMetrics=WEBSITE_CLICKS&dailyRange.start_date.year={$start->format("Y")}&dailyRange.start_date.month={$start->format("n")}&dailyRange.start_date.day={$start->format("d")}&dailyRange.end_date.year={$end->format("Y")}&dailyRange.end_date.month={$end->format("n")}&dailyRange.end_date.day={$end->format("d")}";

                $request = Http::withHeaders(['Authorization' => 'Bearer ' . $auth_user_token])->get($url);
                if ($request->failed()) $request->throw();
                $response = $request->collect()->toArray()['multiDailyMetricTimeSeries'][0]['dailyMetricTimeSeries'];

                $calls_clicks_data = $this->getProcessMatricsArray(array_values(array_filter($response, fn ($matrics) => $matrics['dailyMetric'] == 'CALL_CLICKS'))[0]);
                $website_clicks_data = $this->getProcessMatricsArray(array_values(array_filter($response, fn ($matrics) => $matrics['dailyMetric'] == 'WEBSITE_CLICKS'))[0]);
                $direction_clicks_data = $this->getProcessMatricsArray(array_values(array_filter($response, fn ($matrics) => $matrics['dailyMetric'] == 'BUSINESS_DIRECTION_REQUESTS'))[0]);

                if (request()->ajax()) {
                    $data = [
                        'calls_clicks_keys' => array_column($calls_clicks_data, 'date'),
                        'calls_clicks_values' => array_column($calls_clicks_data, 'value'),
                        'website_clicks_keys' => array_column($website_clicks_data, 'date'),
                        'website_clicks_values' => array_column($website_clicks_data, 'value'),
                        'direction_clicks_keys' => array_column($direction_clicks_data, 'date'),
                        'direction_clicks_values' => array_column($direction_clicks_data, 'value'),
                    ];
                } else {
                    $data = [
                        'calls_clicks' => $calls_clicks_data,
                        'website_clicks' => $website_clicks_data,
                        'direction_clicks' => $direction_clicks_data
                    ];
                }
                Analytics::updateorCreate(
                    ['user_id' => auth()->user()->parent_id ?? auth()->id(),],
                    ['google_calls_website_direction' => json_encode([
                        'calls' => $calls_clicks_data,
                        'website_clicks' => $website_clicks_data,
                        'direction_clicks' => $direction_clicks_data
                    ]),]
                );
            }
        } catch (Exception $e) {
            return [];
        }
        return $data;
    }

    public function getMessagesBookingsFoodOrderData($auth_user_token, $start_date, $end_date, $media_pages): array
    {
        $data = [];
        try {
            foreach ($media_pages as $media_page) {
                $start = Carbon::parse($start_date);
                $end = Carbon::parse($end_date);
                $url = "https://businessprofileperformance.googleapis.com/v1/{$media_page}:fetchMultiDailyMetricsTimeSeries?dailyMetrics=BUSINESS_CONVERSATIONS&dailyMetrics=BUSINESS_BOOKINGS&dailyMetrics=BUSINESS_FOOD_ORDERS&dailyRange.start_date.year={$start->format("Y")}&dailyRange.start_date.month={$start->format("n")}&dailyRange.start_date.day={$start->format("d")}&dailyRange.end_date.year={$end->format("Y")}&dailyRange.end_date.month={$end->format("n")}&dailyRange.end_date.day={$end->format("d")}";

                $request = Http::withHeaders(['Authorization' => 'Bearer ' . $auth_user_token])->get($url);
                if ($request->failed()) $request->throw();
                $response = $request->collect()->toArray()['multiDailyMetricTimeSeries'][0]['dailyMetricTimeSeries'];

                $messages_data = $this->getProcessMatricsArray(array_values(array_filter($response, fn ($matrics) => $matrics['dailyMetric'] == 'BUSINESS_CONVERSATIONS'))[0]);
                $bookings_data = $this->getProcessMatricsArray(array_values(array_filter($response, fn ($matrics) => $matrics['dailyMetric'] == 'BUSINESS_BOOKINGS'))[0]);
                $food_orders_data = $this->getProcessMatricsArray(array_values(array_filter($response, fn ($matrics) => $matrics['dailyMetric'] == 'BUSINESS_FOOD_ORDERS'))[0]);

                if (request()->ajax()) {
                    $data = [
                        'messages_keys'         => array_column($messages_data, 'date'),
                        'messages_values'       => array_column($messages_data, 'value'),
                        'bookings_keys'         => array_column($bookings_data, 'date'),
                        'bookings_values'       => array_column($bookings_data, 'value'),
                        'food_orders_keys'      => array_column($food_orders_data, 'date'),
                        'food_orders_values'    => array_column($food_orders_data, 'value'),
                    ];
                } else {
                    $data = ['messages_clicks' => $messages_data, 'bookings_clicks' => $bookings_data, 'food_ordes' => $food_orders_data];
                }
                Analytics::updateorCreate(
                    ['user_id' => auth()->user()->parent_id ?? auth()->id(),],
                    ['google_messages_bookings_food_order' => json_encode([
                        'messages' => $messages_data,
                        'bookings' => $bookings_data,
                        'food_ordes' => $food_orders_data
                    ]),]
                );
            }
        } catch (Exception $e) {
            // dd($e->getMessage());
            return [];
        }
        return $data;
    }

    public function getGoogleBusinessPosts($auth_user_id, $auth_user_token, $start_date, $end_date, $media_pages)
    {
        $posts = [];
        try {
            foreach ($media_pages as $media_page) {
                $start = Carbon::parse($start_date);
                $end = Carbon::parse($end_date);
                $posts[$media_page] = $this->getUserPosts($auth_user_id, $auth_user_token, $start, $end, $media_page);
            }
            Analytics::updateorCreate(
                ['user_id' => auth()->user()->parent_id ?? auth()->id(),],
                ['google_posts' => json_encode($posts),]
            );
            $posts = call_user_func_array('array_merge', $posts);
        } catch (Exception $e) {
            return [];
        }
        return $posts;
    }

    public function getUserPosts($auth_user_id, $auth_user_token, $start, $end, $media_page): array
    {
        $user_posts = [];
        $parent_id = "accounts/{$auth_user_id}/{$media_page}";
        try {
            $url = "https://mybusiness.googleapis.com/v4/{$parent_id}/localPosts?pageSize=100";

            $postsRequest = Http::withHeaders(['Authorization' => 'Bearer ' . $auth_user_token])->get($url);

            if ($postsRequest->failed()) $postsRequest->throw();

            $postsResponse = $postsRequest->collect()->toArray();

            foreach ($postsResponse['localPosts'] as $post) {
                $post_created_date_timestamp = Carbon::parse($post['createTime'])->timestamp;
                if ($post_created_date_timestamp >= strtotime($start) && $post_created_date_timestamp <= strtotime($end)) {
                    $user_posts[] = [
                        'summary' => $post['summary'] ?? '',
                        'image_url' => isset($post['media'][0]) ? $post['media'][0]['googleUrl'] ?? '' : '',
                        'state' => $post['state'] ?? '',
                        'post_type' => isset($post['callToAction']) ? $post['callToAction']['actionType'] . ' POST' : 'POST',
                        'created_date' => isset($post['createTime']) ? Carbon::parse($post['createTime'])->format('M j, Y') : ''
                    ];
                }
            }
        } catch (Exception $e) {
            $user_posts = [];
        }
        return $user_posts;
    }

    public function getGoogleBusinessReviews($auth_user_id, $auth_user_token, $start_date, $end_date, $media_pages): array
    {
        $reviews = [
            'data' => [],
            'average_rating' => 0,
            'review_count' => 0
        ];
        $all_location_review_data = [];
        try {
            foreach ($media_pages as $media_page) {
                $start = Carbon::parse($start_date);
                $end = Carbon::parse($end_date);
                $all_location_review_data[$media_page] = $this->getLocationReviews($auth_user_id, $auth_user_token, $start, $end, $media_page);
            }
            $final_data = call_user_func_array('array_merge', $all_location_review_data);
            $reviews['data'] = $final_data ?? 'No data Found';
            $reviews['average_rating'] = $final_data ? round(array_sum(array_column($final_data, 'ratings')) / count($final_data)) : 0;
            $reviews['review_count'] = $final_data ? count($final_data) : 0;
            Analytics::updateorCreate(
                ['user_id' => auth()->user()->parent_id ?? auth()->id(),],
                ['google_reviews' => json_encode($reviews)]
            );
        } catch (Exception $e) {
        }
        return $reviews;
    }
    public function getLocationReviews($auth_user_id, $auth_user_token, $start, $end, $media_page): array
    {
        $reviews = [];
        $rating_array = ['ONE' => 1, 'TWO' => 2, 'THREE' => 3, 'FOUR' => 4, 'FIVE' => 5];
        $parent_id = "accounts/{$auth_user_id}/{$media_page}";
        try {
            $url = "https://mybusiness.googleapis.com/v4/{$parent_id}/reviews?pageSize=50";

            $reviewsRequest = Http::withHeaders(['Authorization' => 'Bearer ' . $auth_user_token])->get($url);

            if ($reviewsRequest->failed()) $reviewsRequest->throw();

            $reviewsResponse = $reviewsRequest->collect()->toArray();
            foreach ($reviewsResponse['reviews'] as $review) {
                $review_created_date_timestamp = Carbon::parse($review['createTime'])->timestamp;
                if ($review_created_date_timestamp >= strtotime($start) && $review_created_date_timestamp <= strtotime($end)) {
                    $reviews[] = [
                        'comment' => isset($review['comment']) ? str_limit($review['comment'], 120) ?? '' : '',
                        'name' => isset($review['reviewer'])  ? $review['reviewer']['displayName'] ?? '' : '',
                        'ratings' => $rating_array[$review['starRating']],
                        'reply' => isset($review['reviewReply']) ? true :  false,
                        'created_date' => isset($review['createTime']) ? Carbon::parse($review['createTime'])->format('M j, Y H:i A') : '',
                        'updated_date' => isset($review['updateTime']) ? Carbon::parse($review['createTime'])->format('M j, Y H:i A') : '',
                    ];
                }
            }
        } catch (Exception $e) {
            $reviews = [];
        }
        return $reviews;
    }

    public function getGoogleBusinessInteraction($auth_user_token, $start_date, $end_date, $media_page): array
    {
        $interaction_data = [];
        try {
            $data = Analytics::where('user_id', auth()->user()->parent_id ?? auth()->id())->select('google_calls_website_direction', 'google_messages_bookings_food_order')->first();
            $final_clicks_data = array_merge((array)json_decode($data->google_calls_website_direction, 2), (array)json_decode($data->google_messages_bookings_food_order, 2));

            $keysArray = array_column($final_clicks_data['calls'], 'date');
            extract($final_clicks_data);
            $processed_data = [];
            for ($i = 0; $i < count($keysArray); $i++) {
                $processed_data[] = [
                    'date' => $keysArray[$i],
                    'value' => $calls[$i]['value'] + $website_clicks[$i]['value'] + $direction_clicks[$i]['value'] + $messages[$i]['value'] + $bookings[$i]['value'] + $food_ordes[$i]['value'],
                ];
            }
            if (request()->ajax()) {
                $interaction_data = [
                    'interactions_graph_labels' => array_column($processed_data, 'date'),
                    'interactions_graph_values' => array_column($processed_data, 'value'),
                ];
            } else {
                $interaction_data = ['interactions' => $processed_data];
            }
        } catch (Exception $e) {
            return [];
        }
        return $interaction_data;
    }

    public function updateAccessToken($googleClient, $refresh_token)
    {

        $googleClient->fetchAccessTokenWithRefreshToken($refresh_token);

        // pass access token to some variable
        return $googleClient->getAccessToken();
    }
    public function getProcessMatricsArray(array $matricsArray)
    {
        return array_map(function ($value) {
            $date = $value['date']['year'] . '/' . $value['date']['month'] . '/' . $value['date']['day'];
            return [
                'date' => Carbon::parse($date)->format('d M'),
                'value' => isset($value['value']) ? (int)$value['value'] : 0
            ];
        }, $matricsArray['timeSeries']['datedValues']);
    }
}
