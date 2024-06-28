<?php

namespace App\Services;

use App\Models\GoogleAds;
use App\Models\SocialMediaDetail;
use Carbon\Carbon;
use Exception;
use Google\Client;
use Illuminate\Support\Facades\Http;

final class GoogleAdsService
{
    protected $access_token, $client;
    protected $arrOriginalResponse, $jsonResponse  = [];
    protected $arrResponse = [];

    function getAnalyticsData($media_pages, $type, $media_ids, $start_date, $end_date, $api_source = 'web'): array
    {
        $data = [];
        try {
            $requestFilter = [
                'type'          =>  $type,
                'page_id'       =>  $media_ids,
                'start_date'    =>  $start_date,
                'end_date'      =>  $end_date,
            ];

            // If data already in db then return
            $googleAdsReporting     =   $this->getGoogleAdsDetails($requestFilter, $type);

            if ($googleAdsReporting) {
                if ($api_source == 'web' && !empty($googleAdsReporting->response_web)) {
                    return json_decode($googleAdsReporting->response_web, true);
                } elseif ($api_source == 'api' && !empty($googleAdsReporting->response_api)) {
                    return json_decode($googleAdsReporting->response_api, true);
                }
                return $data;
            }

            // Get details from third party api & store in db
            foreach ($media_pages as $media_page) {
                $this->access_token = $this->updateToken($media_page->social_media_detail_id);

                $response = $this->callGoogleAdsAPI($type, $media_page->page_id, $start_date, $end_date);
                if ($response) {
                    // Prepare Single Page Response
                    $preparedResponse = $this->prepareResponse($response, $type, $media_page->page_id, $start_date, $end_date);
                    if (count($preparedResponse) <= 0) throw new Exception();
                    $this->jsonResponse = $preparedResponse;
                }
            }

            if (empty($this->jsonResponse)) throw new Exception();
            $processedResponse = $this->getProccesedResponse($this->jsonResponse, $type, $start_date, $end_date);

            if (empty($processedResponse)) throw new Exception();

            // Store Data in Database
            $this->storeGoogleAdsResponseInDB($requestFilter, $type, $this->jsonResponse, $processedResponse);

            return $processedResponse;
        } catch (\Exception $e) {
            // dd($e->getMessage(), '$e->getMessage()', $e->getLine());
            return [];
        }

        return $data;
    }

    // Call Google Ads API
    public function callGoogleAdsAPI($type, $page_id, $start_date = null, $end_date = null)
    {
        $url = "https://googleads.googleapis.com/v15/{$page_id}/googleAds:search";
        $payload = [
            'query' => $this->prepareQuery($type, $start_date, $end_date),
        ];

        $response = Http::withToken($this->access_token)->withHeaders(['developer-token' => config('utility.GOOGLE_ADS_DEVELOPER_TOKEN')])->acceptJson()->post($url, $payload);

        if ($response->failed()) $response->throw();

        $response = $response->json();

        return $response;
    }

    public function prepareQuery($type, $start_date, $end_date)
    {
        $matrics = $this->getMatrics($type);
        $query = "SELECT {$matrics} FROM {$this->getTable($type)} WHERE segments.date BETWEEN '{$start_date}' AND '{$end_date}'";
        return $query;
    }

    public function getTable($type)
    {
        $tabel = '';
        if ($type == 'gads_campaigns_rows' || $type == 'gads_line_chart' || $type == 'gads_conversion_rows') {
            $tabel = 'campaign';
        }
        if ($type == 'gads_keywords_rows') {
            $tabel = 'keyword_view';
        }
        if ($type == 'gads_search_terms_rows') {
            $tabel = 'search_term_view';
        }
        // if ($type == 'gads_conversion_rows') {
        //     $tabel = 'conversion_action';
        // }
        return $tabel;
    }

    public function getMatrics($type)
    {
        $matrics = ['metrics.clicks', 'metrics.average_cpc', 'metrics.view_through_conversions', 'metrics.conversions_from_interactions_rate', 'metrics.conversions', 'metrics.cost_micros', 'metrics.cost_per_conversion', 'metrics.impressions'];
        $additional_matrics = [];
        if ($type == 'gads_campaigns_rows') {
            $additional_matrics = ['campaign.id', 'campaign.status', 'campaign.name'];
        }
        if ($type == 'gads_line_chart') {
            $additional_matrics = ['segments.date'];
        }
        if ($type == 'gads_keywords_rows') {
            $additional_matrics = ['keyword_view.resource_name', 'ad_group_criterion.display_name'];
        }
        if ($type == 'gads_search_terms_rows') {
            $additional_matrics = ['search_term_view.resource_name', 'search_term_view.search_term'];
        }
        if ($type == 'gads_conversion_rows') {
            return implode(',', ['metrics.conversions', 'metrics.view_through_conversions', 'segments.conversion_action_name', 'segments.date', 'segments.conversion_action']);
        }
        return implode(',', array_merge($matrics, $additional_matrics));
    }

    // Prepare Response
    public function prepareResponse($response, $type)
    {
        $data = [];
        $arr = $response['results'];
        if ($type == 'gads_campaigns_rows') {
            $arr = array_filter($arr, fn ($row) => $row['campaign']['status'] == 'ENABLED');
            foreach ($arr as $record) {
                $data['rows'][] = [
                    'id'                    => $record['campaign']['id'],
                    'campaign'              => $record['campaign']['name'],
                    'view-through-conv'     => isset($record['metrics']['viewThroughConversions']) ? (int)$record['metrics']['viewThroughConversions'] ?? 0 : 0,
                    'averageCPC'            => isset($record['metrics']['averageCpc']) ? (int)$record['metrics']['averageCpc'] / 1000000 ?? 0 : 0,
                    'clicks'                => isset($record['metrics']['clicks']) ? (int)$record['metrics']['clicks'] ?? 0 : 0,
                    'conversion_rate'       => isset($record['metrics']['conversionsFromInteractionsRate']) ? $record['metrics']['conversionsFromInteractionsRate'] * 100 ?? 0 : 0,
                    'conversion'            => isset($record['metrics']['conversions']) ? $record['metrics']['conversions'] ?? 0 : 0,
                    'cost'                  => isset($record['metrics']['costMicros']) ? (int)$record['metrics']['costMicros'] / 1000000 ?? 0 : 0,
                    'impressions'           => isset($record['metrics']['impressions']) ? (int)$record['metrics']['impressions'] ?? 0 : 0,
                    'cost_per_conversion'   => isset($record['metrics']['costPerConversion']) ? $record['metrics']['costPerConversion'] / 1000000 ?? 0 : 0,
                ];
            }
            $data['counts'] = [
                'view-through-conv'     => array_sum(array_column($data['rows'], 'view-through-conv')),
                'averageCPC'            => array_sum(array_column($data['rows'], 'averageCPC')),
                'clicks'                => array_sum(array_column($data['rows'], 'clicks')),
                'conversion_rate'       => array_sum(array_column($data['rows'], 'conversion_rate')),
                'conversion'            => array_sum(array_column($data['rows'], 'conversion')),
                'cost'                  => array_sum(array_column($data['rows'], 'cost')),
                'impressions'           => array_sum(array_column($data['rows'], 'impressions')),
                'cost_per_conversion'   => array_sum(array_column($data['rows'], 'cost_per_conversion')),
            ];
        }
        if ($type == 'gads_line_chart') {
            $matrics = array_keys($arr[0]['metrics']);
            foreach ($arr as $dayValue) {
                foreach ($matrics as $matric) {
                    $value = $dayValue['metrics'][$matric] ?? 0;
                    if ($matric == 'averageCpc' || $matric == 'costMicros')  $value = isset($dayValue['metrics'][$matric]) ? $dayValue['metrics'][$matric] / 1000000 : 0;
                    // if ($matric == 'cost_per_conversion') $value = $dayValue['metrics'][$matric] / 1000000;
                    $data[$matric][] = [
                        'date'  => $dayValue['segments']['date'],
                        'value' => $value,
                    ];
                }
            }
        }
        if ($type == 'gads_search_terms_rows') {
            foreach ($arr as $record) {
                $data['rows'][] = [
                    'search_term'           => isset($record['searchTermView']['searchTerm']) ? $record['searchTermView']['searchTerm'] : '',
                    'view-through-conv'     => isset($record['metrics']['viewThroughConversions']) ? (int)$record['metrics']['viewThroughConversions'] : 0,
                    'averageCPC'            => isset($record['metrics']['averageCpc']) ? $record['metrics']['averageCpc'] / 1000000 : 0,
                    'clicks'                => isset($record['metrics']['clicks']) ? (int)$record['metrics']['clicks'] : 0,
                    'conversion_rate'       => isset($record['metrics']['conversionsFromInteractionsRate']) ? $record['metrics']['conversionsFromInteractionsRate'] * 100 : 0,
                    'conversion'            => isset($record['metrics']['conversions']) ? $record['metrics']['conversions'] : 0,
                    'cost'                  => isset($record['metrics']['costMicros']) ? $record['metrics']['costMicros'] / 1000000 : 0,
                    'impressions'           => isset($record['metrics']['impressions']) ? (int)$record['metrics']['impressions'] : 0,
                    'cost_per_conversion'   => isset($record['metrics']['costPerConversion']) ? $record['metrics']['costPerConversion'] / 1000000 : 0,
                ];
            }
        }
        if ($type == 'gads_keywords_rows') {
            foreach ($arr as $record) {
                $data['rows'][] = [
                    'keyword'               => isset($record['adGroupCriterion']['displayName']) ? $record['adGroupCriterion']['displayName'] : '',
                    'view-through-conv'     => isset($record['metrics']['viewThroughConversions']) ? (int)$record['metrics']['viewThroughConversions'] : 0,
                    'averageCPC'            => isset($record['metrics']['averageCpc']) ? $record['metrics']['averageCpc'] / 1000000 : 0,
                    'clicks'                => isset($record['metrics']['clicks']) ? (int)$record['metrics']['clicks'] : 0,
                    'conversion_rate'       => isset($record['metrics']['conversionsFromInteractionsRate']) ? $record['metrics']['conversionsFromInteractionsRate'] * 100 : 0,
                    'conversion'            => isset($record['metrics']['conversions']) ? $record['metrics']['conversions'] : 0,
                    'cost'                  => isset($record['metrics']['costMicros']) ? $record['metrics']['costMicros'] / 1000000 : 0,
                    'impressions'           => isset($record['metrics']['impressions']) ? (int)$record['metrics']['impressions'] : 0,
                    'cost_per_conversion'   => isset($record['metrics']['costPerConversion']) ? $record['metrics']['costPerConversion'] / 1000000 : 0,
                ];
            }
        }
        if ($type == 'gads_conversion_rows') {

            foreach ($arr as $record) {
                $id = $record['segments']['conversionAction'];
                $data['rows'][$id] = [
                    'id' => $id,
                    'conversion_name'     => isset($record['segments']['conversionActionName']) ? $record['segments']['conversionActionName'] : '',
                    'conversion'            => isset($data['rows'][$id]['conversion']) ?  $record['metrics']['conversions'] + $data['rows'][$id]['conversion']  : (isset($record['metrics']['conversions']) ? $record['metrics']['conversions'] : 0),
                    'view-through-conv'            => isset($record['metrics']['viewThroughConversions']) ? (int)$record['metrics']['viewThroughConversions'] : 0,
                ];
            }
            $data['counts'] = [
                'conversion' => array_sum(array_column($data['rows'], 'conversion')),
                'view-through-conv' => array_sum(array_column($data['rows'], 'view-through-conv')),
            ];
        }

        return $data;
    }

    public function getProccesedResponse($jsonResponse, $type, $start_date, $end_date)
    {
        $processedData = [];
        if ($type == 'gads_campaigns_rows') {
            foreach ($jsonResponse['rows'] as $campaign) {
                $processedData['rows'][] = [
                    'id'                    =>  $campaign['id'],
                    'campaign'              =>  $campaign['campaign'],
                    'view-through-conv'     => number_format_short($campaign['view-through-conv'] ?? 0, 2),
                    'averageCPC'            => '$' . number_format_short($campaign['averageCPC'], 2) ?? 0,
                    'clicks'                => number_format($campaign['clicks'] ?? 0),
                    'conversion_rate'       => number_format($campaign['conversion_rate'] ?? 0, 2) . '%',
                    'conversion'            => number_format($campaign['conversion'] ?? 0, 2),
                    'cost'                  => '$' . number_format($campaign['cost'] ?? 0), 2,
                    'impressions'           => number_format($campaign['impressions'] ?? 0),
                    'cost_per_conversion'   => '$' . number_format($campaign['cost_per_conversion'], 2),
                ];
            }
            $processedData['counts'] = [
                'viewThroughConv'   => number_format_short($jsonResponse['counts']['view-through-conv'], 2),
                'averageCPC'        => '$' . number_format_short($jsonResponse['counts']['averageCPC'], 2),
                'clicks'            => number_format($jsonResponse['counts']['clicks']),
                'conversionRate'    => number_format($jsonResponse['counts']['conversion_rate'], 2) . '%',
                'conversion'        => number_format($jsonResponse['counts']['conversion'], 2),
                'cost'              => '$' . number_format($jsonResponse['counts']['cost'], 2),
                'impressions'       => number_format($jsonResponse['counts']['impressions']),
                'costPerConversion' => '$' . number_format($jsonResponse['counts']['cost_per_conversion'], 2),
            ];
        }
        if ($type == 'gads_line_chart') {
            $dateDifferenceInYear = Carbon::parse($start_date)->diffInYears(Carbon::parse($end_date));
            $matrics = array_keys($jsonResponse);
            foreach ($matrics as $matric) {
                $processedData[$matric] = array_map(function ($item) use ($dateDifferenceInYear) {
                    $value = number_format_short($item['value'], 2);
                    return [
                        'date' => Carbon::parse($item['date'])->format($dateDifferenceInYear >= 1 ? 'd M,Y' : 'd M'),
                        'value' => $value
                    ];
                }, $jsonResponse[$matric]);
            }
        }
        if ($type == 'gads_search_terms_rows') {
            foreach ($jsonResponse['rows'] as $searchTerm) {
                $processedData['rows'][] = [
                    'search_term'           =>  $searchTerm['search_term'],
                    'view-through-conv'     => number_format($searchTerm['view-through-conv'] ?? 0, 2),
                    'averageCPC'            => '$' . number_format_short($searchTerm['averageCPC'], 2) ?? 0,
                    'clicks'                => number_format($searchTerm['clicks'] ?? 0),
                    'conversion_rate'       => number_format($searchTerm['conversion_rate'] ?? 0, 2) . '%',
                    'conversion'            => number_format($searchTerm['conversion'] ?? 0, 2),
                    'cost'                  => '$' . number_format($searchTerm['cost'] ?? 0, 2),
                    'impressions'           => number_format($searchTerm['impressions'] ?? 0),
                    'cost_per_conversion'   => '$' . number_format($searchTerm['cost_per_conversion'] ?? 0, 2),
                ];
            }
        }
        if ($type == 'gads_keywords_rows') {
            foreach ($jsonResponse['rows'] as $keyword) {
                $processedData['rows'][] = [
                    'keyword'               =>  $keyword['keyword'],
                    'view-through-conv'     => number_format_short($keyword['view-through-conv'] ?? 0, 2),
                    'averageCPC'            => '$' . number_format($keyword['averageCPC'] ?? 0, 2) ?? 0,
                    'clicks'                => number_format($keyword['clicks'] ?? 0),
                    'conversion_rate'       => number_format($keyword['conversion_rate'] ?? 0, 2) . '%',
                    'conversion'            => number_format($keyword['conversion'] ?? 0, 2),
                    'cost'                  => '$' . number_format($keyword['cost'] ?? 0, 2),
                    'impressions'           => number_format($keyword['impressions'] ?? 0),
                    'cost_per_conversion'   => '$' . number_format($keyword['cost_per_conversion'] ?? 0, 2),
                ];
            }
        }
        if ($type == 'gads_conversion_rows') {
            foreach ($jsonResponse['rows'] as $conversion_action) {
                $processedData['rows'][] = [
                    'id'                    =>  $conversion_action['id'],
                    'conversion_name'       =>  $conversion_action['conversion_name'],
                    'view-through-conv'     => number_format_short($conversion_action['view-through-conv'] ?? 0, 2),
                    'conversion'            => number_format($conversion_action['conversion'] ?? 0, 2),
                ];
            }
            $processedData['counts'] = [
                'viewThroughConv'   => number_format_short($jsonResponse['counts']['view-through-conv'], 2),
                'conversion'        => number_format($jsonResponse['counts']['conversion'], 2),
            ];
        }
        return $processedData;
    }

    // Store Data in Database
    public function storeGoogleAdsResponseInDB($requestFilter, $type, $multiPageResponse, $mergedResponse)
    {
        return GoogleAds::updateOrCreate([
            'user_id'           =>  auth()->user()->id,
            'request_type'      =>  $type,
        ], [
            'custom_id'         =>  getUniqueString('google_ads'),
            'request_filter'    =>  json_encode($requestFilter, true),
            'response_json'     =>  json_encode($multiPageResponse, true),
            'response_web'      =>  json_encode($mergedResponse, true),
            'response_api'      =>  json_encode($mergedResponse, true),
        ]);
    }


    // get details if available in db
    public function getGoogleAdsDetails($requestFilter, $type)
    {
        $expired_time = \Carbon\Carbon::now()->subSeconds(config('utility.google_ads.request_expirations'))->toDateTimeString();
        return GoogleAds::where([
            'user_id'           =>  auth()->user()->id,
            'request_type'      =>  $type,
            'request_filter'    =>  json_encode($requestFilter),
        ])
            ->where('updated_at', '>', $expired_time)
            ->latest()
            ->first();
    }

    public function updateToken($social_media_id)
    {
        $googleMediaDetails = SocialMediaDetail::where('id', $social_media_id)->first();
        $this->access_token = $googleMediaDetails->token;

        $this->client = new Client();
        // set prompts of google oauth
        $this->client->setApplicationName('Stacked Up');
        $this->client->setClientId(config('utility.GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(config('utility.GOOGLE_CLIENT_SECRET'));
        // $this->client->setPrompt('consent select_account');
        // $this->client->setAccessType('offline');

        // $this->client->setAccessToken($googleMediaDetails->token);

        // if (!$googleMediaDetails->token_expiry_time || Carbon::parse($googleMediaDetails->token_expiry_time)->diffInSeconds(Carbon::now()) >= 3600) {
        $access_token = $this->updateAccessToken($googleMediaDetails->refresh_token);
        $this->access_token = $access_token['access_token'];
        $googleMediaDetails->update([
            'token'         =>  $access_token['access_token'],
            // 'token_expiry_time'  =>  Carbon::now()->addSeconds($access_token['expires_in'] ?? 3600),
        ]);
        // }

        // return $this->access_token;
        return $access_token['access_token'];
    }
    public function updateAccessToken($refresh_token)
    {
        $this->client->fetchAccessTokenWithRefreshToken($refresh_token);
        return $this->client->getAccessToken();
    }
}
