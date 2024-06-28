<?php

namespace App\Services;

use App\Models\FacebookAds;
use Carbon\Carbon;
use Exception;
use Facebook\Facebook;
use Illuminate\Support\Facades\Http;

class FaceBookAdsService
{
    private  $multiPageResponse, $dailyData = [];
    public function __construct()
    {
    }
    public function getReporting($media_pages, $type, $media_ids, $start_date, $end_date, $api_source = 'web', $campaign_id = null)
    {
        $data = [];
        try {
            $requestFilter = [
                'type'          =>  $type,
                'page_id'       =>  $media_ids,
                'start_date'    =>  $start_date,
                'end_date'      =>  $end_date,
                'campaign_id'   =>  $campaign_id,
            ];

            // check if data is already exists and need to update
            $isFacebookAdsDataNeedsToUpdate = $this->checkFacebookAdsDataNeedsToUpdate($requestFilter, $type);

            if ($isFacebookAdsDataNeedsToUpdate) {
                if ($api_source == 'web' && !empty($isFacebookAdsDataNeedsToUpdate->response_web)) {
                    return json_decode($isFacebookAdsDataNeedsToUpdate->response_web);
                } elseif ($api_source == 'api' && !empty($isFacebookAdsDataNeedsToUpdate->response_api)) {
                    return json_decode($isFacebookAdsDataNeedsToUpdate->response_api);
                }
                return $data;
            }

            // Get Data of Multiple Pages from the Facebook API and Store In database
            foreach ($media_pages as $media_page) {
                $token = $media_page->socialMediaDetail->token;
                $response = $this->callFacebookAdsAPI($type, $media_page->page_id, $start_date, $end_date, $token, $campaign_id);

                if ($response) {
                    // Prepare Single Page Response
                    $preparedResponse = $this->prepareResponse($response, $type, $media_page->page_id, $start_date, $end_date, $token);
                    // dd($preparedResponse, '$preparedResponse');
                    // dd($preparedResponse, '$preparedResponse');
                    if (count($preparedResponse) <= 0) throw new Exception();
                    $this->multiPageResponse[$media_page->page_id] = $preparedResponse;
                }
            }

            if (empty($this->multiPageResponse)) throw new Exception();
            $mergedResponse = $this->getMergedResponse($this->multiPageResponse, $type, $start_date, $end_date);
            if (empty($mergedResponse)) throw new Exception();

            // Store Data in Database
            $this->storeFacebookAdsResponseInDB($requestFilter, $type, $this->multiPageResponse, $mergedResponse);

            return $mergedResponse;
        } catch (Exception $e) {
            // dd($e->getMessage(), $e->getLine());
        }
        return $data;
    }

    public function callFacebookAdsAPI($type, $page_id, $start_date, $end_date, $token, $campaign_id, $paginate_url = null)
    {
        $url = $paginate_url ?? $this->getRequestUrl($type, $page_id, $start_date, $end_date, $token, $campaign_id);
        $response = Http::get($url);

        if ($response->failed()) $response->throw();

        $response = $response->json();

        return $response;
    }

    public function getRequestUrl($type, $page_id, $start_date, $end_date, $token, $campaign_id = null)
    {
        $url = '';
        if ($type == 'campaigns_table_rows') {
            $url = "https://graph.facebook.com/v18.0/{$page_id}/insights?fields=campaign_id,adset_id,adset_name,created_time,campaign_name,clicks,cpc,ctr,impressions,cost_per_action_type,actions,spend&time_range={'since': '{$start_date}', 'until': '{$end_date}'}&level=adset&access_token={$token}";
        } elseif ($type == 'campaigns_total_counts') {
            $url = "https://graph.facebook.com/v18.0/{$page_id}/insights?fields=cpc,impressions,ctr,clicks,date_start,date_stop,cost_per_action_type,actions,spend&time_range={'since': '{$start_date}', 'until': '{$end_date}'}&access_token={$token}";
        } elseif ($type == 'campaigns_publisher_plateform_pie_chart') {
            $url = "https://graph.facebook.com/v18.0/{$page_id}/insights?access_token={$token}&fields=cpc,impressions,ctr,clicks,date_start,date_stop,actions,spend&breakdowns=publisher_platform&time_range={'since': '{$start_date}', 'until': '{$end_date}'}";
        } elseif ($type == 'campaigns_clicks_line_chart') {
            $url = "https://graph.facebook.com/v18.0/{$page_id}/insights?access_token={$token}&fields=cpc,impressions,ctr,clicks,date_start,date_stop,actions,spend&time_range={'since': '{$start_date}', 'until': '{$end_date}'}&time_increment=1&limit=30";
        } elseif ($type == 'demographics_gender_pie_chart') {
            $url = "https://graph.facebook.com/v18.0/{$page_id}/insights?access_token={$token}&fields=cpc,impressions,ctr,clicks,actions,spend&time_range={'since': '{$start_date}', 'until': '{$end_date}'}&breakdowns=gender";
        } elseif ($type == 'demographics_age_pie_chart') {
            $url = "https://graph.facebook.com/v18.0/{$page_id}/insights?access_token={$token}&fields=cpc,impressions,ctr,clicks,actions,spend&time_range={'since': '{$start_date}', 'until': '{$end_date}'}&breakdowns=age";
        } elseif ($type == 'single_campaign_publisher_plateform_pie_chart') {
            $url = "https://graph.facebook.com/v18.0/{$campaign_id}/insights?access_token={$token}&fields=cpc,impressions,ctr,clicks,date_start,date_stop,actions,spend&breakdowns=publisher_platform&time_range={'since': '{$start_date}', 'until': '{$end_date}'}";
        } elseif ($type == 'single_campaign_clicks_line_chart') {
            $url = "https://graph.facebook.com/v18.0/{$campaign_id}/insights?access_token={$token}&fields=cpc,impressions,ctr,clicks,date_start,date_stop,actions,spend&time_range={'since': '{$start_date}', 'until': '{$end_date}'}&time_increment=1&limit=30";
        }

        return $url;
    }

    public function prepareResponse($response, $type, $page_id, $start_date, $end_date, $token, $campaign_id = null)
    {
        $data = [];
        if ($type == 'campaigns_table_rows') {
            $rows = $response['data'];
            foreach ($rows as $campaign) {
                $data[] = $campaign;
            }
        }
        if ($type == 'campaigns_total_counts') {
            $data = $response['data'][0];
        }
        if ($type == 'campaigns_publisher_plateform_pie_chart') {
            $data = $response['data'];
        }
        if ($type == 'campaigns_clicks_line_chart') {
            if (!empty($response['data'])) {

                $valuesArr = array_map(function ($dayItem) {
                    $cost_per_lead = $on_facebook_lead = [];
                    if (isset($dayItem['cost_per_action_type'])) {
                        $cost_per_lead = array_values(array_filter($dayItem['cost_per_action_type'], fn ($item) =>  $item['action_type'] == 'lead'));
                    }
                    if (isset($dayItem['actions'])) {
                        $on_facebook_lead = array_values(array_filter($dayItem['actions'], fn ($item) => $item['action_type'] == 'lead'));
                    }
                    return [
                        'cpc' => $dayItem['cpc'],
                        'impressions' => $dayItem['impressions'],
                        'ctr' => $dayItem['ctr'],
                        'clicks' => $dayItem['clicks'],
                        'cost_per_lead' => !empty($cost_per_lead) ? $cost_per_lead[0]['value'] : 0,
                        'on_facebook_lead' => !empty($on_facebook_lead) ? $on_facebook_lead[0]['value'] : 0,
                        'date' => $dayItem['date_start'],
                    ];
                }, $response['data']);
                $this->dailyData = isset($response['data']) ? array_merge($this->dailyData, array_values($valuesArr)) : [];
                // get Paginate Data
                if (isset($response['paging']) && isset($response['paging']['next'])) {
                    $response = $this->callFacebookAdsAPI($type, $page_id, $start_date, $end_date, $token, $response['paging']['next']);
                    if ($response) $this->prepareResponse($response, $type, $page_id, $start_date, $end_date, $token);
                }
            }
            $data = $this->dailyData;
        }
        if ($type == 'demographics_gender_pie_chart') {
            $data = $response['data'];
        }
        if ($type == 'demographics_age_pie_chart') {
            $data = $response['data'];
        }
        if ($type == 'single_campaign_publisher_plateform_pie_chart') {
            $data = $response['data'];
        }
        if ($type == 'single_campaign_clicks_line_chart') {
            if (!empty($response['data'])) {

                $valuesArr = array_map(function ($dayItem) {
                    $cost_per_lead = $on_facebook_lead = [];
                    if (isset($dayItem['cost_per_action_type'])) {
                        $cost_per_lead = array_values(array_filter($dayItem['cost_per_action_type'], fn ($item) =>  $item['action_type'] == 'lead'));
                    }
                    if (isset($dayItem['actions'])) {
                        $on_facebook_lead = array_values(array_filter($dayItem['actions'], fn ($item) => $item['action_type'] == 'lead'));
                    }
                    return [
                        'cpc' => $dayItem['cpc'] ?? 0,
                        'impressions' => $dayItem['impressions'] ?? 0,
                        'ctr' => $dayItem['ctr'] ?? 0,
                        'clicks' => $dayItem['clicks'] ?? 0,
                        'cost_per_lead' => !empty($cost_per_lead) ? $cost_per_lead[0]['value'] : 0,
                        'on_facebook_lead' => !empty($on_facebook_lead) ? $on_facebook_lead[0]['value'] : 0,
                        'date' => $dayItem['date_start'],
                    ];
                }, $response['data']);
                $this->dailyData = isset($response['data']) ? array_merge($this->dailyData, array_values($valuesArr)) : [];
                // get Paginate Data
                if (isset($response['paging']) && isset($response['paging']['next'])) {
                    $response = $this->callFacebookAdsAPI($type, $page_id, $start_date, $end_date, $token, $campaign_id, $response['paging']['next']);
                    if ($response) $this->prepareResponse($response, $type, $page_id, $start_date, $end_date, $token, $campaign_id);
                }
            }
            $data = $this->dailyData;
        }
        return $data;
    }

    public function getMergedResponse($multiPageResponse, $type, $start_date, $end_date)
    {
        $mergedResponse = [];
        $parameters = $this->getFacebookAdsParameters();
        if ($type == 'campaigns_table_rows') {
            foreach ($multiPageResponse as $rows) {
                foreach ($rows as $row) {
                    $average_cpl = $on_facebook_lead = $leads = [];
                    if (isset($row['cost_per_action_type'])) {
                        $average_cpl = array_values(array_filter($row['cost_per_action_type'], function ($item) {
                            return $item['action_type'] == 'lead';
                        }));
                    }
                    if (isset($row['actions'])) {
                        $on_facebook_lead = array_values(array_filter($row['actions'], function ($item) {
                            return $item['action_type'] == 'leadgen_grouped';
                        }));
                        $leads = array_values(array_filter($row['actions'], function ($item) {
                            return $item['action_type'] == 'lead';
                        }));
                    }
                    $mergedResponse['rows'][] = [
                        'campaign_id'       => $row['campaign_id'],
                        'adset_name'        => $row['adset_name'],
                        'adset_id'          => $row['adset_id'],
                        'campaign_name'     => $row['campaign_name'],
                        'clicks'            => number_format($row['clicks']),
                        'average_cpc'       => '$' . number_format(isset($row['cpc']) ? $row['cpc'] : 0, 2),
                        'ctr'               => number_format(isset($row['ctr']) ? $row['ctr'] : 0, 2) . '%',
                        'impressions'       => isset($row['impressions']) ? number_format($row['impressions']) : 0,
                        'average_cpl'       =>  !empty($average_cpl) ? '$' . number_format($average_cpl[0]['value'], 2) : '---',
                        'on_facebook_lead'  => !empty($on_facebook_lead) ? number_format($on_facebook_lead[0]['value']) : '---',
                        'leads'             => !empty($on_facebook_lead) ? number_format($on_facebook_lead[0]['value']) : '---',
                        'amount_spent'      => '$ ' . $row['spend'] ?? 0,
                    ];
                }
            }
        }
        if ($type == 'campaigns_total_counts') {
            $initialCounts = ['clicks_counts' => 0, 'impressions_counts' => 0, 'ctr_counts' => 0, 'average_cpc_counts' => 0, 'average_cpl_counts' => 0, 'leads_counts' => 0, 'total_amount_spent_counts' => 0];
            foreach ($multiPageResponse as $page_counts) {
                $average_cpl_count = $leads_count = [];
                if (isset($page_counts['cost_per_action_type'])) {
                    $average_cpl_count = array_values(array_filter($page_counts['cost_per_action_type'], function ($item) {
                        return $item['action_type'] == 'lead';
                    }));
                }
                if (isset($page_counts['actions'])) {
                    $leads_count = array_values(array_filter($page_counts['actions'], function ($item) {
                        return $item['action_type'] == 'lead';
                    }));
                }
                $initialCounts['average_cpl_counts'] += !empty($average_cpl_count) ? $average_cpl_count[0]['value'] : 0;
                $initialCounts['clicks_counts'] += $page_counts['clicks'] ?? 0;
                $initialCounts['impressions_counts'] += $page_counts['impressions'] ?? 0;
                $initialCounts['ctr_counts'] += $page_counts['ctr'] ?? 0;
                $initialCounts['average_cpc_counts'] += $page_counts['cpc'] ?? 0;
                $initialCounts['leads_counts'] += !empty($leads_count) ? $leads_count[0]['value'] : 0;
                $initialCounts['total_amount_spent_counts'] += $page_counts['spend'] ?? 0;
            }
            $mergedResponse['total_counts'] = [
                'average_cpl_count'     => '$' . number_format($initialCounts['average_cpl_counts'], 2),
                'clicks_count'          => number_format($initialCounts['clicks_counts']),
                'impressions_count'     => number_format_short($initialCounts['impressions_counts'], 0),
                'ctr_count'             => number_format($initialCounts['ctr_counts'], 2) . '%',
                'average_cpc_count'     => '$' . number_format($initialCounts['average_cpc_counts'], 2),
                'leads_count'           => number_format($initialCounts['leads_counts']),
                'total_amount_spent_count' => '$' . $initialCounts['total_amount_spent_counts'],
            ];
        }
        if ($type == 'campaigns_publisher_plateform_pie_chart') {
            $arr = [];
            foreach ($multiPageResponse as $pageResponse) {
                $lables = array_column($pageResponse, 'publisher_platform');
                if (!empty($lables)) {
                    foreach ($parameters as $parameter) {
                        foreach ($lables as $lable) {
                            $lablesPlatformData = array_values(array_filter($pageResponse, function ($item) use ($lable) {
                                return $item['publisher_platform'] == $lable;
                            }));
                            $arr['values'][$parameter][$lable] = isset($lablesPlatformData[0][$parameter]) ? ($parameter == 'ctr' || $parameter == 'cpc' ? (float)$lablesPlatformData[0][$parameter] : (int)$lablesPlatformData[0][$parameter]) :  0;

                            if ($parameter == 'ctr') {
                                $arr['displayLabel'][$parameter][$lable] = isset($lablesPlatformData[0][$parameter]) ? number_format($lablesPlatformData[0][$parameter], 2) . '%' : '0';
                            } elseif ($parameter == 'cpc') {
                                $arr['displayLabel'][$parameter][$lable] = isset($lablesPlatformData[0][$parameter]) ? '$' . number_format($lablesPlatformData[0][$parameter], 2) : '0';
                            } else {
                                $arr['displayLabel'][$parameter][$lable] = isset($lablesPlatformData[0][$parameter]) ? number_format($lablesPlatformData[0][$parameter]) : '0';
                            }
                        }
                    }
                }
                // dd($arr, 'singlePageResponse');
                $mergedResponse = $arr;
            }
        }
        if ($type == 'campaigns_clicks_line_chart') {
            $arr = ['values' => []];
            $dates = getDatesBetweenTwoDates($start_date, $end_date);
            foreach ($multiPageResponse as $pageResponse) {
                foreach ($parameters as $parameter) {
                    foreach ($dates as $date) {
                        $value = array_values(array_filter($pageResponse, fn ($item) => $item['date'] == $date));
                        $date = Carbon::createFromFormat('Y-m-d', $date)->format('d M');
                        if (!isset($arr['values'][$parameter])) {
                            $arr['values'] = array_merge($arr['values'], [$parameter => [['date' => $date, 'value' => !empty($value) ? ($parameter == 'ctr' || $parameter == 'cpc' ? (float)$value[0][$parameter] : (int) $value[0][$parameter]) : 0]]]);
                        } else {
                            $arr['values'][$parameter] = array_merge($arr['values'][$parameter], [['date' => $date, 'value' => !empty($value) ?  ($parameter == 'ctr' || $parameter == 'cpc' ? (float)$value[0][$parameter] : (int) $value[0][$parameter]) : 0]]);
                        }
                    }
                }
            }
            $mergedResponse = $arr;
        }
        if ($type == 'demographics_gender_pie_chart') {
            $arr = [];
            foreach ($multiPageResponse as $pageResponse) {
                $lables = array_column($pageResponse, 'gender');

                if (!empty($lables)) {
                    foreach ($parameters as $parameter) {
                        foreach ($lables as $lable) {
                            $lablesPlatformData = array_values(array_filter($pageResponse, function ($item) use ($lable) {
                                return $item['gender'] == $lable;
                            }));
                            $arr['values'][$parameter][$lable] = isset($lablesPlatformData[0][$parameter]) ? ($parameter == 'ctr' || $parameter == 'cpc' ? (float)$lablesPlatformData[0][$parameter] : (int)$lablesPlatformData[0][$parameter]) :  0;

                            if ($parameter == 'ctr') {
                                $arr['displayLabel'][$parameter][$lable] = isset($lablesPlatformData[0][$parameter]) ? number_format($lablesPlatformData[0][$parameter], 2) . '%' : '0';
                            } elseif ($parameter == 'cpc') {
                                $arr['displayLabel'][$parameter][$lable] = isset($lablesPlatformData[0][$parameter]) ? '$' . number_format($lablesPlatformData[0][$parameter], 2) : '0';
                            } else {
                                $arr['displayLabel'][$parameter][$lable] = isset($lablesPlatformData[0][$parameter]) ? number_format($lablesPlatformData[0][$parameter]) : '0';
                            }
                        }
                    }
                }
            }
            $mergedResponse = $arr;
        }
        if ($type == 'demographics_age_pie_chart') {
            $arr = [];
            foreach ($multiPageResponse as $pageResponse) {
                $lables = array_column($pageResponse, 'age');

                if (!empty($lables)) {
                    foreach ($parameters as $parameter) {
                        foreach ($lables as $lable) {
                            $lablesPlatformData = array_values(array_filter($pageResponse, function ($item) use ($lable) {
                                return $item['age'] == $lable;
                            }));
                            $arr['values'][$parameter][$lable] = isset($lablesPlatformData[0][$parameter]) ? ($parameter == 'ctr' || $parameter == 'cpc' ? (float)$lablesPlatformData[0][$parameter] : (int)$lablesPlatformData[0][$parameter]) :  0;

                            if ($parameter == 'ctr') {
                                $arr['displayLabel'][$parameter][$lable] = isset($lablesPlatformData[0][$parameter]) ? number_format($lablesPlatformData[0][$parameter], 2) . '%' : '0';
                            } elseif ($parameter == 'cpc') {
                                $arr['displayLabel'][$parameter][$lable] = isset($lablesPlatformData[0][$parameter]) ? '$' . number_format($lablesPlatformData[0][$parameter], 2) : '0';
                            } else {
                                $arr['displayLabel'][$parameter][$lable] = isset($lablesPlatformData[0][$parameter]) ? number_format($lablesPlatformData[0][$parameter]) : '0';
                            }
                        }
                    }
                }
                $mergedResponse = $arr;
            }
        }
        if ($type == 'single_campaign_publisher_plateform_pie_chart') {
            $arr = [];
            foreach ($multiPageResponse as $pageResponse) {
                $lables = array_column($pageResponse, 'publisher_platform');
                if (!empty($lables)) {
                    foreach ($parameters as $parameter) {
                        foreach ($lables as $lable) {
                            $lablesPlatformData = array_values(array_filter($pageResponse, function ($item) use ($lable) {
                                return $item['publisher_platform'] == $lable;
                            }));
                            $arr['values'][$parameter][$lable] = isset($lablesPlatformData[0][$parameter]) ? ($parameter == 'ctr' || $parameter == 'cpc' ? (float)$lablesPlatformData[0][$parameter] : (int)$lablesPlatformData[0][$parameter]) :  0;

                            if ($parameter == 'ctr') {
                                $arr['displayLabel'][$parameter][$lable] = isset($lablesPlatformData[0][$parameter]) ? number_format($lablesPlatformData[0][$parameter], 2) . '%' : '0';
                            } elseif ($parameter == 'cpc') {
                                $arr['displayLabel'][$parameter][$lable] = isset($lablesPlatformData[0][$parameter]) ? '$' . number_format($lablesPlatformData[0][$parameter], 2) : '0';
                            } else {
                                $arr['displayLabel'][$parameter][$lable] = isset($lablesPlatformData[0][$parameter]) ? number_format($lablesPlatformData[0][$parameter]) : '0';
                            }
                        }
                    }
                }
                // dd($arr, 'singlePageResponse');
                $mergedResponse = $arr;
            }
        }
        if ($type == 'single_campaign_clicks_line_chart') {
            $arr = ['values' => []];
            $dates = getDatesBetweenTwoDates($start_date, $end_date);
            foreach ($multiPageResponse as $pageResponse) {
                foreach ($parameters as $parameter) {
                    foreach ($dates as $date) {
                        $value = array_values(array_filter($pageResponse, fn ($item) => $item['date'] == $date));
                        $date = Carbon::createFromFormat('Y-m-d', $date)->format('d M');
                        if (!isset($arr['values'][$parameter])) {
                            $arr['values'] = array_merge($arr['values'], [$parameter => [['date' => $date, 'value' => !empty($value) ? ($parameter == 'ctr' || $parameter == 'cpc' ? (float)$value[0][$parameter] : (int) $value[0][$parameter]) : 0]]]);
                        } else {
                            $arr['values'][$parameter] = array_merge($arr['values'][$parameter], [['date' => $date, 'value' => !empty($value) ?  ($parameter == 'ctr' || $parameter == 'cpc' ? (float)$value[0][$parameter] : (int) $value[0][$parameter]) : 0]]);
                        }
                    }
                }
            }
            $mergedResponse = $arr;
        }
        return $mergedResponse;
    }

    public function storeFacebookAdsResponseInDB($requestFilter, $type, $multiPageResponse, $mergedResponse)
    {
        return FacebookAds::updateOrCreate([
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

    public function checkFacebookAdsDataNeedsToUpdate($requestFilter, $type)
    {
        $expired_time = \Carbon\Carbon::now()->subSeconds(config('utility.facebook_ads.request_expirations'))->toDateTimeString();
        return FacebookAds::where([
            'user_id'           =>  auth()->user()->id,
            'request_filter'    =>  json_encode($requestFilter),
            'request_type'      =>  $type,
        ])
            ->where('updated_at', '>', $expired_time)
            ->latest()
            ->first();
    }

    public function getFacebookAdsParameters()
    {
        return ['cpc', 'impressions', 'ctr', 'clicks'];
    }
}
