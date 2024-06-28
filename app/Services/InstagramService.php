<?php

namespace App\Services;

use App\Models\Country;
use App\Models\InstagramAnalytics;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;

class InstagramService
{
    private $timezone = 'Australia/Brisbane';
    private  $multiPageResponse = [];
    private $period = 'day';
    private $setKey = [];
    private $show_error = 0;
    private $error = ['common_message' => '', 'follower_gained' => ''];
    private $skip_follower_gained = 0;
    private $followers_count = 0;
    private $apply_date = '';


    public function getMonthsBetweenTwoDates($start_date, $end_date): array
    {
        $today = Carbon::today()->setTimezone($this->timezone)->format('Y-m-d');
        $start_date = Carbon::parse($start_date)->setTimezone($this->timezone);
        $end_date = Carbon::parse($end_date)->setTimezone($this->timezone);
        $months = [];
        $index = 1;
        $initialStartDate = $start_date->copy();
        //end_date is greater than today then we will set today as end date
        $initialEndDate = $end_date->gt(Carbon::parse($today)->setTimezone($this->timezone)) ? Carbon::parse($today)->setTimezone($this->timezone) : $end_date->copy();
        //need diffInMonths + 1 because we need to include start and end date month
        $diffInMonths = $start_date->diffInMonths($end_date) + 1;

        //Start date and end date is in same month
        $isSameMonth = $start_date->format('Y-m') == $end_date->format('Y-m') ? true : false;
        while ($start_date->lte($end_date)) {
            $lastMonth = Carbon::parse($initialEndDate)->setTimezone($this->timezone)->format('Y-m') == $start_date->format('Y-m') ? true : false;
            $thisMonthStartDate = $start_date->copy()->startOfMonth();
            $start_date = $start_date->startOfMonth();
            $thisMonthEndDate = $thisMonthStartDate->copy()->endOfMonth();
            if ($diffInMonths > 1 && $index == 1) {
                $months[$start_date->format('Y-m')] = [
                    'actual_start_date' => $initialStartDate->format('Y-m-d'),
                    'actual_end_date' => $thisMonthEndDate->format('Y-m-d'),
                ];
            } else if ($diffInMonths > 1 && $lastMonth) {
                $months[$start_date->format('Y-m')] = [
                    'actual_start_date' => $thisMonthStartDate->format('Y-m-d'),
                    'actual_end_date' => $initialEndDate->format('Y-m-d'),
                ];
            } elseif ($diffInMonths == 1) {
                $months[$start_date->format('Y-m')] = [
                    'actual_start_date' => $initialStartDate->format('Y-m-d'),
                    'actual_end_date' => $initialEndDate->format('Y-m-d'), //Change for temporay
                ];
            } else {
                $months[$start_date->format('Y-m')] = [
                    'actual_start_date' => $thisMonthStartDate->format('Y-m-d'),
                    'actual_end_date' => $thisMonthEndDate->format('Y-m-d'),
                ];
            }

            $months[$start_date->format('Y-m')]['start_date'] = $thisMonthStartDate->format('Y-m-d');
            $months[$start_date->format('Y-m')]['end_date'] = $thisMonthEndDate->format('Y-m-d');
            $months[$start_date->format('Y-m')]['is_full_month'] =  $months[$start_date->format('Y-m')]['start_date'] == $months[$start_date->format('Y-m')]['actual_start_date'] && $months[$start_date->format('Y-m')]['end_date'] == $months[$start_date->format('Y-m')]['actual_end_date'] ? true : false;

            $start_date->addMonth();
            $index++;
        }
        return $months;
    }

    public function getReporting($media_pages, $type, $media_ids, $start_date, $end_date, $api_source = 'web')
    {
        $data = [];
        try {
            $requestFilter = [
                'type'          =>  $type,
                'page_id'       =>  $media_ids,
                'start_date'    =>  $start_date,
                'end_date'      =>  $end_date,
            ];

            $this->period = Carbon::parse($start_date)->setTimezone($this->timezone)->diffInDays(Carbon::parse($end_date)) <= 31 ? 'day' : 'month';

            $months = $this->getMonthsBetweenTwoDates($start_date, $end_date);

            if ($type === 'dicovery_new_followers_bar_chart_and_count') {
                foreach ($media_pages as $media_page) {
                    $responseArr = [];
                    $yesterday = Carbon::yesterday()->setTimezone($this->timezone)->format('Y-m-d');
                    //exculding current day and get 30 day date and match with start date if between then we move forward
                    $last30DayAgoDate = Carbon::parse($yesterday)->setTimezone($this->timezone)->subDays(29)->format('Y-m-d');
                    $start_date = strtotime(Carbon::parse($last30DayAgoDate)->setTimezone($this->timezone)->startOfDay());
                    $end_date = strtotime(Carbon::parse($yesterday)->setTimezone($this->timezone)->endOfDay());
                    $response = $this->callInstagramAnalyticsAPI($type, $media_page->page_id, $start_date, $end_date, $media_page->socialMediaDetail->token);
                    if ($response) {
                        // Prepare Single Page Response
                        $preparedResponse = $this->prepareResponse($response, $type, $media_page->page_id, $start_date, $end_date, $media_page->socialMediaDetail->token);
                        if (count($preparedResponse) <= 0) throw new Exception();
                        $responseArr = $preparedResponse;
                    }

                    $uniqueMonth = [];
                    $dividedResponse = [];
                    foreach ($responseArr as $matric => $value) {
                        if (isset($value['values'])) {
                            foreach ($value['values'] as $item) {
                                $existMonth = Carbon::parse($item['date'])->setTimezone($this->timezone)->format('Y-m');
                                if (!in_array($existMonth, $uniqueMonth)) {
                                    $uniqueMonth[] = $existMonth;
                                }
                                $dividedResponse[$existMonth][$item['date']] = $item['value'];
                            }
                        }
                    }

                    foreach ($uniqueMonth as $month) {
                        $month_year = Carbon::parse($month)->setTimezone($this->timezone)->format('Y-m-d');
                        $instagramAnalytics = InstagramAnalytics::where([
                            'user_id' => auth()->user()->id,
                            'request_type' => $type,
                            'media_page_id' => $media_page->id,
                            'month_year' => $month_year,
                        ])
                            ->whereNotNull("{$this->period}_data")
                            ->latest()
                            ->first();
                        $monthResponse = [];
                        if ($instagramAnalytics) {

                            $dayResponse = json_decode($instagramAnalytics["day_data"], true);
                            $daysOfMonth = Carbon::parse($month)->setTimezone($this->timezone)->daysInMonth;
                            foreach (range(1, $daysOfMonth) as $day) {
                                $day = $day < 10 ? '0' . $day : $day;
                                $existDay = Carbon::parse($month . '-' . $day)->setTimezone($this->timezone)->format('d M Y');
                                if (isset($dividedResponse[$month][$existDay])) {
                                    $dayResponse['follower_count']['values'][$day - 1]['date'] = $existDay;
                                    $dayResponse['follower_count']['values'][$day - 1]['value'] = $dividedResponse[$month][$existDay];
                                } else if (isset($dayResponse['follower_count']['values'][$day - 1]) && $dayResponse['follower_count']['values'][$day - 1]['date'] == $existDay) {
                                    $dayResponse['follower_count']['values'][$day - 1]['date'] = $existDay;
                                    $dayResponse['follower_count']['values'][$day - 1]['value'] = $dayResponse['follower_count']['values'][$day - 1]['value'];
                                } else {
                                    $dayResponse['follower_count']['values'][$day - 1]['date'] = $existDay;
                                    $dayResponse['follower_count']['values'][$day - 1]['value'] = 0;
                                }
                            }
                        } else {
                            $dayResponse = [];
                            $daysOfMonth = Carbon::parse($month)->setTimezone($this->timezone)->daysInMonth;

                            foreach (range(1, $daysOfMonth) as $key => $day) {
                                $day = $day < 10 ? '0' . $day : $day;
                                $existDay = Carbon::parse($month . '-' . $day)->setTimezone($this->timezone)->format('d M Y');
                                if (isset($dividedResponse[$month][$existDay])) {
                                    $dayResponse['follower_count']['values'][$key]['date'] = $existDay;
                                    $dayResponse['follower_count']['values'][$key]['value'] = $dividedResponse[$month][$existDay];
                                } else {
                                    $dayResponse['follower_count']['values'][$key]['date'] = $existDay;
                                    $dayResponse['follower_count']['values'][$key]['value'] = 0;
                                }
                            }
                        }
                        $monthArr = isset($months[$month]) ? $months[$month] : ['start_date' => $month];
                        $monthResponse = $this->convertDayToMonthReponse($dayResponse, $monthArr, $type);
                        InstagramAnalytics::updateOrCreate([
                            'user_id'           =>  auth()->user()->id,
                            'request_type'      =>  $type,
                            'month_year'        =>  $month_year,
                            'media_page_id'     =>  $media_page->id,
                        ], [
                            'custom_id'         =>  getUniqueString('instagram_analytics'),
                            'request_filter'    =>  json_encode($requestFilter, true),
                            "day_data"          =>  json_encode($dayResponse, true),
                            "month_data"        =>  json_encode($monthResponse, true),
                        ]);
                    }
                }
            }
            foreach ($months as $month) {
                //get instagram analytics data if data available in database we will return data from database or not we will get data from api and store in database
                $this->checkInstagramDataAndReturn($requestFilter, $month, $media_pages);
                if ($this->skip_follower_gained) goto skip_follower_gained;
            }
            skip_follower_gained:

            if (empty($this->multiPageResponse)) throw new Exception();
            $mergedResponse = $this->getMergedResponse($this->multiPageResponse, $type, $start_date, $end_date);
            if (empty($mergedResponse)) throw new Exception();
            return $mergedResponse;
        } catch (Exception $e) {
            // dd($e->getMessage(), $e->getLine());
            return [];
        }
        return $data;
    }

    public function checkInstagramDataAndReturn($requestFilter, $month, $media_pages)
    {
        $type = $requestFilter['type'];
        foreach ($media_pages as $media_page) {
            $yesterday = Carbon::yesterday()->setTimezone($this->timezone)->format('Y-m-d');
            //exculding current day and get 30 day date and match with start date if between then we move forward
            $last30DayAgoDate = Carbon::parse($yesterday)->setTimezone($this->timezone)->subDays(29)->format('Y-m-d');
            //Update is_apply and apply_date colunn in media page table if is_apply is 0
            if ($media_page->is_apply == 0) {
                $media_page->is_apply = 1;
                $media_page->apply_date = Carbon::parse($last30DayAgoDate)->setTimezone($this->timezone)->format('Y-m-d H:i:s');
                $media_page->save();
            }
            $this->apply_date = $media_page->apply_date;
            $token = $media_page->socialMediaDetail->token;
            $start_date = $month['start_date'];
            $end_date = $month['end_date'];
            $requestFilter['media_page_id'] = $media_page->id;
            //extract month and year from start date and match wih month_year column in database
            $month_year = Carbon::parse($month['start_date'])->setTimezone($this->timezone)->format('Y-m-d');
            $instagramAnalytics = InstagramAnalytics::where([
                'user_id' => auth()->user()->id,
                'request_type' => $type,
                'media_page_id' => $media_page->id,
                'month_year' => $month_year,
            ])
                ->whereNotNull("{$this->period}_data")
                ->latest()
                ->first();
            // $instagramAnalytics = false;
            $monthResponse = [];

            if ($type == 'dicovery_new_followers_bar_chart_and_count') {


                $media_page_date = Carbon::parse($this->apply_date)->setTimezone($this->timezone)->format('Y-m-d');

                if ($month['actual_start_date'] < $media_page_date) { //temporary
                    $start_date = strtotime(Carbon::parse($last30DayAgoDate)->setTimezone($this->timezone)->startOfDay());
                    $end_date = strtotime(Carbon::parse($yesterday)->setTimezone($this->timezone)->endOfDay());

                    $response = $this->callInstagramAnalyticsAPI($type, $media_page->page_id, $start_date, $end_date, $token);
                    if ($response) {
                        // Prepare Single Page Response
                        $preparedResponse = $this->prepareResponse($response, $type, $media_page->page_id, $start_date, $end_date, $token);
                        if (count($preparedResponse) <= 0) throw new Exception();
                        $responseArr = $preparedResponse;
                    }
                    $media_page_date = Carbon::parse($media_page_date)->setTimezone($this->timezone)->format('d M,Y');
                    $this->show_error = 1;
                    $this->error = [
                        'common_message' => 'Follower Gained is only showing last 30 days of data',
                        'follower_gained' => "Follower Gained Data is only available from ({$media_page_date}). This is due to Instagram only providing data for upto 30 days excluding the current day prior to you connecting to this application",
                    ];
                    $this->multiPageResponse["{$media_page->page_id}"] = $responseArr;
                    $this->skip_follower_gained = 1;
                    return $this->multiPageResponse;
                }
            }
            $start_date = strtotime(Carbon::parse($start_date)->setTimezone($this->timezone)->startOfDay());
            $end_date = strtotime(Carbon::parse($end_date)->setTimezone($this->timezone)->endOfDay());

            if ($instagramAnalytics && $month['is_full_month']) {
                if ($this->period == 'month') {
                    $monthResponse = json_decode($instagramAnalytics["day_data"], true);
                    $monthResponse = $this->convertDayToMonthReponse($monthResponse, $month, $type);
                    $this->multiPageResponse["{$media_page->page_id}{$start_date}"] = $monthResponse;
                } else {
                    $this->multiPageResponse["{$media_page->page_id}{$start_date}"] = json_decode($instagramAnalytics["{$this->period}_data"], true);
                }
            } elseif ($month['is_full_month'] == false) { //temporary
                // we will check start date and actual_start_date month is same then we will give actual_start_date as start date
                $startDateMonth = Carbon::parse($month['start_date'])->setTimezone($this->timezone)->format('Y-m');
                $actualStartDateMonth = Carbon::parse($month['actual_start_date'])->setTimezone($this->timezone)->format('Y-m');
                $endDateMonth = Carbon::parse($month['end_date'])->setTimezone($this->timezone)->format('Y-m');
                $actualEndDateMonth = Carbon::parse($month['actual_end_date'])->setTimezone($this->timezone)->format('Y-m');

                $start_date = $startDateMonth == $actualStartDateMonth ? $month['actual_start_date'] : $start_date;
                $end_date = $endDateMonth == $actualEndDateMonth ? $month['actual_end_date'] : $end_date;
                $start_date = strtotime(Carbon::parse($start_date)->setTimezone($this->timezone)->startOfDay());
                $end_date = strtotime(Carbon::parse($end_date)->setTimezone($this->timezone)->endOfDay());
                // $start_date = strtotime(Carbon::parse($month['actual_start_date'])->setTimezone($this->timezone)->startOfDay());
                // $end_date = strtotime(Carbon::parse($month['actual_end_date'])->setTimezone($this->timezone)->endOfDay());

                if ($type == 'dicovery_new_followers_bar_chart_and_count') {
                    $instagramAnalytics = json_decode($instagramAnalytics["day_data"], true);
                    $instagramAnalytics = $instagramAnalytics['follower_count']['values'];
                    $instagramAnalytics = array_filter($instagramAnalytics, function ($item) use ($start_date, $end_date) {

                        return strtotime(Carbon::parse($item['date'])->setTimezone($this->timezone)->format('Y-m-d')) >= $start_date && strtotime(Carbon::parse($item['date'])->setTimezone($this->timezone)->format('Y-m-d')) <= $end_date;
                    });
                    $responseArr[] = [
                        'follower_count' => [
                            'values' => $instagramAnalytics,
                        ],
                    ];
                } else {
                    $response = $this->callInstagramAnalyticsAPI($type, $media_page->page_id, $start_date, $end_date, $token);
                    if ($response) {
                        // Prepare Single Page Response
                        $preparedResponse = $this->prepareResponse($response, $type, $media_page->page_id, $start_date, $end_date, $token);
                        if (count($preparedResponse) <= 0) throw new Exception();
                        $responseArr[] = $preparedResponse;
                    }
                }

                if ($this->period == 'month') {
                    $monthResponse = $this->convertDayToMonthReponse($responseArr[0], $month, $type);
                    $this->multiPageResponse["{$media_page->page_id}{$start_date}"] = $monthResponse;
                } else {
                    $this->multiPageResponse["{$media_page->page_id}{$start_date}"] = $responseArr[0];
                }
            } else {
                //if data not available in database we will get data from api and store in database
                $diffInDays = Carbon::parse($start_date)->setTimezone($this->timezone)->diffInDays(Carbon::parse($end_date)->addDay());
                //if diffInDays > 30 Day we will call api two times and merge response
                $i = 0;
                $end = $diffInDays > 30 ? 2 : 1;
                $responseArr = [];
                for ($i = 0; $i < $end; $i++) {
                    if ($i == 0 && $diffInDays > 30) $end_date = $end_date - 86400;
                    if ($i == 1 && $diffInDays > 30) {
                        $start_date = strtotime(Carbon::parse($end_date)->setTimezone($this->timezone)->addDay()->startOfDay());
                        $end_date = $end_date + 86400;
                    }
                    $response = $this->callInstagramAnalyticsAPI($type, $media_page->page_id, $start_date, $end_date, $token);
                    if ($response) {
                        // Prepare Single Page Response
                        $preparedResponse = $this->prepareResponse($response, $type, $media_page->page_id, $start_date, $end_date, $token);
                        $responseArr[] = $preparedResponse;
                    }
                }
                //Merge Same Month Response for diffInDays > 30
                if ($diffInDays > 30) {
                    $mergedResponse = $this->mergeSameMonthResponse($responseArr, $type);
                    $responseArr = [$mergedResponse];
                }
                if ($this->period == 'month') {
                    $monthResponse = $this->convertDayToMonthReponse($responseArr[0], $month, $type);
                    $this->multiPageResponse["{$media_page->page_id}{$start_date}"] = $monthResponse;
                } else {
                    $this->multiPageResponse["{$media_page->page_id}{$start_date}"] = $responseArr[0];
                }
                $this->storeInstaAdsResponseInDB($requestFilter, $month, $type, $responseArr[0],  $monthResponse);
            }
        }
        return $this->multiPageResponse;
    }

    public function mergeSameMonthResponse($responseArr, $type)
    {
        $mergedResponse = [];
        if (in_array($type, ['dicovery_reach_impression_line_chart', 'interaction_line_chart_and_count'])) {
            foreach ($responseArr as $response) {
                foreach ($response as $matric => $value) {
                    if (isset($mergedResponse[$matric])) {
                        $mergedResponse[$matric]['values'] = array_merge($mergedResponse[$matric]['values'], $value['values']);
                    } else {
                        $mergedResponse[$matric]['values'] = $value['values'];
                    }
                    $mergedResponse[$matric]['key_array'] = array_column($mergedResponse[$matric]['values'], 'date');
                    $mergedResponse[$matric]['values_array'] = array_column($mergedResponse[$matric]['values'], 'value');
                }
            }
        } else if ($type == 'instagram_posts_table') {
            foreach ($responseArr as $response) {
                foreach ($response as $post) {
                    $mergedResponse[] = $post;
                }
            }
        } else {
            $mergedResponse = $responseArr[0];
        }
        return $mergedResponse;
    }

    public  function convertDayToMonthReponse($preparedResponse, $month, $type)
    {
        $preparedMonthResponse = [];
        if ($type == 'dicovery_reach_impression_line_chart' || $type == 'interaction_line_chart_and_count') {
            //make sum of reach and impression
            foreach ($preparedResponse as $matric => $value) {
                $preparedMonthResponse[$matric]['values'][] = [
                    'date' =>  Carbon::parse($month['start_date'])->setTimezone($this->timezone)->format('M Y'),
                    'value' => collect($value['values'])->sum('value')
                ];
            }
        } elseif ($type == 'audiance_age_bar_chart' || $type == 'audiance_gender_pie_chart') {
            foreach ($preparedResponse as $matric => $value) {
                $preparedMonthResponse[$matric]['values'] = array_map(function ($item) {
                    return [
                        'dimension' => $item['dimension'],
                        'value' => $item['value'],
                    ];
                }, $value['values']);
            }
        } elseif ($type == 'audiance_country_table_data' || $type == 'audiance_city_table_data') {
            foreach ($preparedResponse as $matric => $value) {
                $sorted_array = $value['values'];
                array_multisort(array_column($value['values'], 'value'), SORT_DESC, $sorted_array);
                $preparedMonthResponse[$matric]['values'] = $sorted_array;
            }
        } elseif ($type == 'dicovery_new_followers_bar_chart_and_count') {
            foreach ($preparedResponse as $matric => $value) {
                $preparedMonthResponse[$matric]['values'][] = [
                    'date' =>  Carbon::parse($month['start_date'])->setTimezone($this->timezone)->format('M Y'),
                    'value' => collect($value['values'])->sum('value')
                ];
            }
        } elseif ($type == 'instagram_posts_table') {
            $preparedMonthResponse = $preparedResponse;
        }

        return $preparedMonthResponse;
    }

    public function storeInstaAdsResponseInDB($requestFilter, $month, $type, $preparedResponse, $monthResponse = [])
    {
        return InstagramAnalytics::updateOrCreate([
            'user_id'           =>  auth()->user()->id,
            'request_type'      =>  $type,
            'month_year'        =>  $month['start_date'],
            'media_page_id'     =>  $requestFilter['media_page_id'],
        ], [
            'custom_id'         =>  getUniqueString('instagram_analytics'),
            'request_filter'    =>  json_encode($requestFilter, true),
            "day_data"          =>  json_encode($preparedResponse, true),
            "month_data"        =>  json_encode($monthResponse, true),
        ]);
    }

    public function callInstagramAnalyticsAPI($type, $page_id, $start_date = null, $end_date = null, $token)
    {

        $url = "https://graph.facebook.com/v18.0/{$page_id}/insights";

        $parameter = ['access_token' => $token, 'since' => $start_date, 'until' => $end_date,];

        if ($type == 'instagram_posts_table') $url = "https://graph.facebook.com/v18.0/{$page_id}/media";

        if ($type == 'follower_count') $url = "https://graph.facebook.com/v18.0/{$page_id}";

        $parameter = $type == 'instagram_posts_table' ? $parameter :  array_merge($parameter, $this->getMetricsParameter($type));
        $response = Http::get($url, $parameter);

        if ($response->failed()) $response->throw();

        $response = $response->json();

        return $response;
    }

    public function getMetricsParameter($type)
    {
        $matrics = [];
        if ($type == 'dicovery_reach_impression_line_chart') $matrics = ['metric' => 'reach,impressions', 'period' => 'day'];
        if ($type == 'interaction_line_chart_and_count') $matrics = ['metric' => 'website_clicks,profile_views,email_contacts,get_directions_clicks,phone_call_clicks,text_message_clicks', 'period' => 'day'];
        if ($type == 'audiance_age_bar_chart') $matrics = ['metric' => 'follower_demographics', 'period' => 'lifetime', 'metric_type' => 'total_value', 'breakdown' => 'age'];
        if ($type == 'audiance_gender_pie_chart') $matrics = ['metric' => 'follower_demographics', 'period' => 'lifetime', 'metric_type' => 'total_value', 'breakdown' => 'gender'];
        if ($type == 'audiance_country_table_data') $matrics = ['metric' => 'follower_demographics', 'period' => 'lifetime', 'metric_type' => 'total_value', 'breakdown' => 'country'];
        if ($type == 'audiance_city_table_data') $matrics = ['metric' => 'follower_demographics', 'period' => 'lifetime', 'metric_type' => 'total_value', 'breakdown' => 'city'];
        if ($type == 'dicovery_new_followers_bar_chart_and_count') $matrics = ['metric' => 'follower_count', 'period' => 'day'];

        // This matrics comes from the Code, not from the frontend.
        if ($type == 'follower_count') $matrics = ['fields' => 'followers_count'];

        return $matrics;
    }

    public function prepareResponse($response, $type, $page_id, $start_date, $end_date, $token)
    {
        $data = [];
        $arr = $response['data'];
        $start_date = strtotime(Carbon::parse($start_date)->setTimezone($this->timezone));
        $end_date = strtotime(Carbon::parse($end_date)->setTimezone($this->timezone));
        if ($type == 'dicovery_new_followers_bar_chart_and_count') {
            $end_date = $end_date - 86400;
            $response = $this->callInstagramAnalyticsAPI('follower_count', $page_id, $start_date, $end_date, $token);
            foreach ($arr as $matric) {
                $data[$matric['name']]['values'] = array_map(function ($item) {
                    return [
                        'date' => Carbon::parse($item['end_time'])->setTimezone($this->timezone)->format('d M Y'),
                        'value' => $item['value'],
                    ];
                }, $matric['values']);
            }
            $this->followers_count = isset($response['followers_count']) ? $response['followers_count'] : 0;
            $data['counts'] = [
                'follower_count' => $this->followers_count
            ];
        }
        if ($type == 'dicovery_reach_impression_line_chart') {
            foreach ($arr as $matric) {
                $data[$matric['name']]['values'] = array_map(function ($item) {
                    return [
                        'date' => Carbon::parse($item['end_time'])->setTimezone($this->timezone)->format('d M'),
                        'value' => $item['value'],
                    ];
                }, $matric['values']);
            }
        };
        if ($type == 'interaction_line_chart_and_count') {

            foreach ($arr as $matric) {
                $data[$matric['name']]['values'] = array_map(function ($item) {
                    return [
                        'date' => Carbon::parse($item['end_time'])->setTimezone($this->timezone)->format('d M'),
                        'value' => $item['value'],
                    ];
                }, $matric['values']);
            }
        };
        if ($type == 'audiance_age_bar_chart' || $type == 'audiance_gender_pie_chart') {

            foreach ($arr as $matric) {
                $matric_name = $matric['total_value']['breakdowns'][0]['dimension_keys'][0];
                $data[$matric_name]['values'] = array_map(function ($item) {
                    return [
                        'dimension' => $item['dimension_values'][0],
                        'value' => $item['value'],
                    ];
                }, $matric['total_value']['breakdowns'][0]['results']);
            }
        }
        if ($type == 'audiance_country_table_data' || $type == 'audiance_city_table_data') {
            foreach ($arr as $matric) {
                $matric_name = $matric['total_value']['breakdowns'][0]['dimension_keys'][0];
                $data[$matric_name]['values'] = array_map(function ($item) use ($type) {
                    return [
                        'location_name' => ($type == 'audiance_country_table_data') ? $this->getCountryNameFromCode($item['dimension_values'][0]) : $item['dimension_values'][0],
                        'value' => $item['value'],
                    ];
                }, $matric['total_value']['breakdowns'][0]['results']);
            }
        }
        if ($type == 'instagram_posts_table') {
            foreach ($arr as $post) {
                $data[] = $this->getInstagramPostsDetails($post['id'], $token);
            }
        }
        return $data;
    }

    public function getMergedResponse($multiPageResponse, $type, $start_date, $end_date)
    {
        $mergedResponse = [];
        $arr = $multiPageResponse;
        if ($type == 'dicovery_new_followers_bar_chart_and_count') {
            $followers_count = 0;
            if ($this->error) {
                $dateFormat = 'd M';
            } else {
                $dateFormat = $this->period == 'month' ? 'M Y' : 'd M';
            }

            foreach ($arr as $pageResponse) {

                foreach ($pageResponse as $matric => $value) {
                    if (isset($mergedResponse[$matric])) {
                        //use dateFormat for date M-Y or d M
                        $mergedResponse[$matric]['values'] = array_merge($mergedResponse[$matric]['values'], $value['values']);
                    } else {
                        $mergedResponse[$matric]['values'] = isset($value['values']) ? $value['values'] : [];
                    }
                    //period month then date M-Y and period day then date d M
                    $mergedResponse[$matric]['key_array'] = [];
                    foreach (array_column($mergedResponse[$matric]['values'], 'date') as $date) {
                        $date = Carbon::parse($date)->setTimezone($this->timezone)->format($dateFormat);
                        if (!in_array($date, $mergedResponse[$matric]['key_array'])) {
                            $mergedResponse[$matric]['key_array'][] = $date;
                        }
                    }
                    $mergedResponse[$matric]['values_array'] = array_column($mergedResponse[$matric]['values'], 'value');
                    $followers_count += array_sum(array_column($mergedResponse[$matric]['values'], 'value'));
                }
            }
            $mergedResponse['follower_count']['values'] = array_map(function ($item) use ($dateFormat) {
                return [
                    'date' => Carbon::parse($item['date'])->setTimezone($this->timezone)->format($dateFormat),
                    'value' => $item['value'],
                ];
            }, $mergedResponse['follower_count']['values']);
            $mergedResponse['counts'] = [
                'follower_count' => $this->followers_count
            ];
            $mergedResponse['error_message'] = $this->error;
            $mergedResponse['error'] = $this->show_error;
            $mergedResponse['follower_gain_count'] = array_sum(array_column($mergedResponse['follower_count']['values'], 'value'));
        }
        if ($type == 'dicovery_reach_impression_line_chart') {
            //Merge Same Response
            $mergedResponse = [];
            foreach ($arr as $pageResponse) {
                foreach ($pageResponse as $matric => $value) {
                    if (isset($mergedResponse[$matric])) {
                        $mergedResponse[$matric]['values'] = array_merge($mergedResponse[$matric]['values'], $value['values']);
                    } else {
                        $mergedResponse[$matric]['values'] = $value['values'];
                    }
                    $mergedResponse[$matric]['key_array'] = array_column($mergedResponse[$matric]['values'], 'date');
                    $mergedResponse[$matric]['values_array'] = array_column($mergedResponse[$matric]['values'], 'value');
                }
            }
        };
        if ($type == 'interaction_line_chart_and_count') {
            foreach ($arr as $pageResponse) {
                foreach ($pageResponse as $matric => $value) {
                    if (isset($mergedResponse[$matric])) {
                        $mergedResponse[$matric]['values'] = array_merge($mergedResponse[$matric]['values'], $value['values']);
                    } else {
                        $mergedResponse[$matric]['values'] = $value['values'];
                    }
                    $mergedResponse[$matric]['key_array'] = array_column($mergedResponse[$matric]['values'], 'date');
                    $mergedResponse[$matric]['values_array'] = array_column($mergedResponse[$matric]['values'], 'value');
                    $mergedResponse['counts'][$matric . "_count"] = array_sum(array_column($mergedResponse[$matric]['values'], 'value'));
                }
            }
        };
        if ($type == 'audiance_age_bar_chart') {
            $this->setKey = [];
            foreach ($arr as $pageResponse) {
                foreach ($pageResponse as $matric => $value) {
                    foreach ($value['values'] as $item) {
                        if (!in_array($item['dimension'], $this->setKey)) {
                            $dimension = '';
                            if ($item['dimension'] === 'F') $dimension = 'Female';
                            else if ($item['dimension'] === 'M') $dimension = 'Male';
                            else if ($item['dimension'] === 'U') $dimension = 'Unspecified';
                            else $dimension = $item['dimension'];
                            $this->setKey[] = $dimension;
                            $mergedResponse[$matric]['values'][] = [
                                'dimension' => $dimension,
                                'value' => $item['value'],
                            ];
                            $mergedResponse[$matric]['key_array'][] = $dimension;
                            $mergedResponse[$matric]['values_array'][] = $item['value'];
                        }
                    }
                }
            }
        };

        if ($type == 'audiance_gender_pie_chart') {
            $this->setKey = [];
            foreach ($arr as $pageResponse) {
                foreach ($pageResponse as $matric => $value) {
                    foreach ($value['values'] as $item) {
                        if (!in_array($item['dimension'], $this->setKey)) {
                            $this->setKey[] = $item['dimension'];
                            $mergedResponse[$matric]['values'][] = [
                                'dimension' => $item['dimension'],
                                'value' => $item['value'],
                            ];
                            $mergedResponse[$matric]['key_array'][] = $item['dimension'];
                            $mergedResponse[$matric]['values_array'][] = $item['value'];
                        }
                    }
                }
            }
        }

        if ($type == 'audiance_country_table_data' || $type == 'audiance_city_table_data') {
            foreach ($arr as $pageResponse) {
                foreach ($pageResponse as $matric => $value) {
                    $sorted_array = $value['values'];
                    array_multisort(array_column($value['values'], 'value'), SORT_DESC, $sorted_array);
                    $mergedResponse[$matric]['values'] = $sorted_array;
                }
            }
        };
        if ($type == 'instagram_posts_table') {
            foreach ($arr as $pageResponse) {
                foreach ($pageResponse as $post) {
                    $mergedResponse[] = [
                        'id' => $post['id'],
                        'caption' => $post['caption'],
                        'comments_count' => $post['comments_count'],
                        'like_count' => $post['like_count'],
                        'media_type' => $post['media_type'],
                        'media_url' => $post['media_url'] ?? '',
                        'date' => Carbon::parse($post['timestamp'], $this->timezone)->format('d M,Y'),
                        'permalink' => $post['permalink'] ?? '',
                    ];
                }
            }
            usort($mergedResponse, function ($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        }
        return $mergedResponse;
    }

    public function storeFacebookAdsResponseInDB($requestFilter, $type, $multiPageResponse, $mergedResponse)
    {
        return InstagramAnalytics::updateOrCreate([
            'user_id'           =>  auth()->user()->id,
            'request_type'      =>  $type,
        ], [
            'custom_id'         =>  getUniqueString('facebook_ads'),
            'request_filter'    =>  json_encode($requestFilter, true),
            'response_json'     =>  json_encode($multiPageResponse, true),
            'response_web'      =>  json_encode($mergedResponse, true),
            'response_api'      =>  json_encode($mergedResponse, true),
        ]);
    }

    public function getCountryNameFromCode($code)
    {
        $country = Country::where('code', $code)->first();
        return $country ? $country->name : $code;
    }

    public function getInstagramPostsDetails($post_id, $user_access_token)
    {
        try {
            $request = Http::get("https://graph.facebook.com/" . config('utility.DEFAULT_FACEBOOK_API_VERSION') . "/{$post_id}", [
                'access_token' => $user_access_token,
                'fields' => 'id,caption,comments_count,like_count,media_type,media_url,timestamp,permalink'
            ]);
            if ($request->failed()) $request->throw();
            return $request->json();
        } catch (Exception $e) {
            return [];
        }
    }

    public function checkInstagramDataNeedsToUpdate($requestFilter, $type)
    {
        $expired_time = \Carbon\Carbon::now()->subSeconds(config('utility.instagram.request_expirations'))->toDateTimeString();
        return InstagramAnalytics::where([
            'user_id'           =>  auth()->user()->id,
            'request_filter'    =>  json_encode($requestFilter),
            'request_type'      =>  $type,
        ])
            ->where('updated_at', '>', $expired_time)
            ->latest()
            ->first();
    }
}
