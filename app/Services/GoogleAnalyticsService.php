<?php

namespace App\Services;

use DateTime;
use Carbon\Carbon;
use Google\Client;
use Carbon\CarbonInterval;
use Google\Service\Analytics;
use App\Models\GoogleAnalytic;
use App\Models\SocialMediaDetail;
use GuzzleHttp\Client as GuzzleHttpClient;

class GoogleAnalyticsService
{
    protected $client;
    protected $access_token;
    protected $arrOriginalResponse = [];
    protected $arrResponse = [];

    /* Get Google Analytics Details */
    public function getGoogleAnalytics($media_pages, $type, $media_ids, $start_date, $end_date, $api_source = 'web')
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
            $googleAnalytic     =   $this->getGoogleAnalyticDetails($requestFilter, $type);

            if ($googleAnalytic) {
                if ($api_source == 'web' && !empty($googleAnalytic->response_web)) {
                    return json_decode($googleAnalytic->response_web, true);
                } elseif ($api_source == 'api' && !empty($googleAnalytic->response_api)) {
                    return json_decode($googleAnalytic->response_api, true);
                }
                return $data;
            }

            // Get details from third party api & store in db
            foreach ($media_pages as $media_page) {
                $this->access_token = updateToken($media_page->social_media_detail_id);

                if (!empty($media_page->account_properties)) {
                    $account_properties = json_decode($media_page->account_properties, true);
                    if ($account_properties && isset($account_properties['propertySummaries'])) {
                        foreach ($account_properties['propertySummaries'] as $propertySummary) {

                            if (isset($propertySummary['property'])) {
                                $propertyName       =   $propertySummary['property'];
                                $response           =   $this->callGoogleAnalyticsApi($propertyName, $type, $start_date, $end_date);

                                if ($response) {
                                    $this->arrOriginalResponse[] = $response;
                                    $preparedResponse       =   $this->prepareGoogleAnalyticsResponse($response, $type);
                                    if (count($preparedResponse) > 0) {
                                        $this->arrResponse[] = $preparedResponse;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (count($this->arrResponse) > 0) {
                $aggregateResponse = $this->aggregateGoogleAnalyticsResponse($this->arrResponse, $type);

                if (count($aggregateResponse) > 0) {
                    $this->storeGoogleAnalyticsResponse($requestFilter, $type, $this->arrOriginalResponse, $aggregateResponse);

                    return $aggregateResponse;
                }
            }
        } catch (\Exception $e) {
        }

        return $data;
    }

    // curl api call
    public function callGoogleAnalyticsApi($property_id, $type, $start_date, $end_date)
    {
        try {
            $requestUrl =   'https://analyticsdata.googleapis.com/v1beta/' . $property_id . ':runReport';
            $headers    =   [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->access_token,
            ];

            $payload = $this->getPayloadOfGoogleAnalytics($type);

            $payload["dateRanges"]    =   [
                "startDate" =>  $start_date,
                "endDate"   =>  $end_date
            ];

            $response = fireCURL($requestUrl, 'POST', $headers, json_encode($payload));
            if (!isset($response['error'])) {
                return $response;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    // prepare payload to pass details in google analytics api
    public function getPayloadOfGoogleAnalytics($type)
    {
        $payload = [];

        if (in_array($type, ["audience-location-country", "audience-location-region", "audience-location-city"])) {
            $typeArr = explode('-', $type);
            $rowName = $typeArr[count($typeArr) - 1];
            $payload['dimensions']    =   [
                ['name'  => $rowName]
            ];
        }
        if (in_array($type, ["audience-languageCode"])) {
            $payload['dimensions']    =   [
                ['name'  => 'languageCode']
            ];
        }
        if (in_array($type, ["audience-browser"])) {
            $payload['dimensions']    =   [
                ['name'  => 'browser']
            ];
        }
        if (in_array($type, ["audience-userAgeBracket"])) {
            $payload['dimensions']    =   [
                ['name'  => 'userAgeBracket']
            ];
        }
        if (in_array($type, ["audience-userGender"])) {
            $payload['dimensions']    =   [
                ['name'  => 'userGender']
            ];
        }
        if (in_array($type, ["audience-mobileDeviceMarketingName"])) {
            $payload['dimensions']    =   [
                ['name'  => 'mobileDeviceMarketingName']
            ];
        }
        if (in_array($type, ["audience-deviceCategory"])) {
            $payload['dimensions']    =   [
                ['name'  => 'deviceCategory']
            ];
        }
        if (in_array($type, ["audience-deviceCategoryDate"])) {
            $payload['dimensions']    =   [
                ['name'  => 'deviceCategory'],
                ['name'  => 'date']
            ];
        }
        // Acquisition -> All
        if ($type == 'acquisition-all-line-chart') {
            $payload['dimensions']    =   [
                ['name'  =>  'date']
            ];
        }
        if ($type == 'acquisition-all-rows') {
            $payload['dimensions']    =   [
                ['name'  =>  'sessionDefaultChannelGroup']
            ];
        }
        // Acquisition -> Channels
        if ($type == 'acquisition-paid-search-rows' || $type == 'acquisition-organic-search-rows') {
            $payload['dimensions']    =   [
                ['name'  =>  'sessionDefaultChannelGrouping'],
                ['name'  =>  'sessionGoogleAdsKeyword']
            ];
        }
        if ($type == 'acquisition-channels-line-charts') {
            $payload['dimensions']    =   [
                ['name'  =>  'sessionDefaultChannelGrouping'],
                ['name'  =>  'date']
            ];
        }
        if ($type == 'audience-languagecode-line-charts') {
            $payload['dimensions']    =   [
                ['name'  =>  'languageCode'],
                ['name'  =>  'date']
            ];
        }
        if ($type == 'acquisition-channels-pie-charts') {
            $payload['dimensions']    =   [
                ['name'  =>  'sessionDefaultChannelGrouping'],
                ['name'  =>  'sessionSource']
            ];
        }
        if ($type == 'acquisition-channels-counts') {
            $payload['dimensions']    =   [
                ['name'  =>  'sessionDefaultChannelGrouping']
            ];
        }

        if (
            $type == 'acquisition-all-line-chart' ||
            $type == 'acquisition-channels-line-charts' ||
            $type == 'acquisition-all-counts' ||
            $type == 'acquisition-all-rows' ||
            $type == 'acquisition-paid-search-rows' ||
            $type == 'acquisition-organic-search-rows' ||
            $type == 'acquisition-channels-pie-charts' ||
            $type == 'acquisition-channels-counts' ||
            $type == 'audience-location-country' ||
            $type == 'audience-location-region' ||
            $type == 'audience-location-city' ||
            $type == 'audience-languageCode' ||
            $type == 'audience-browser' ||
            $type == 'audience-userAgeBracket' ||
            $type == 'audience-userGender' ||
            $type == 'audience-mobileDeviceMarketingName' ||
            $type == 'audience-deviceCategory' ||
            $type == 'audience-deviceCategoryDate' ||
            $type == 'audience-languagecode-line-charts'
        ) {
            $payload['metrics']    =   [
                ['name'  =>  'sessions'],
                ['name'  =>  'totalUsers'],
                ['name'  =>  'userEngagementDuration'],
                ['name'  =>  'screenPageViews'],
                ['name'  =>  'conversions'],
                ['name'  =>  'eventCount']
            ];
        }

        return $payload;
    }

    // prepare response using google analytics api response
    public function prepareGoogleAnalyticsResponse($response, $type)
    {
        $data = [];

        if (
            isset($response['metricHeaders']) &&
            isset($response['rows'])
        ) {

            if ($type == 'acquisition-all-line-chart') {
                $metricHeaders = $response['metricHeaders'];
                $rows = $response['rows'];

                foreach ($rows as $key => $row) {
                    $dateValue = $row['dimensionValues'][0]['value'];
                    foreach ($row['metricValues'] as $keyMetric => $metricValue) {
                        $metricHeaderName = $metricHeaders[$keyMetric]['name'];

                        $indexLabelValue = $metricValue['value'];
                        if ($metricHeaderName == 'userEngagementDuration') {
                            $indexLabelValue = $this->convertSeconds($metricValue['value']);
                        }
                        $data['line_chart'][$metricHeaderName][$key]['x'] = $dateValue;
                        $data['line_chart'][$metricHeaderName][$key]['y'] = (int) $metricValue['value'];
                    }
                }
            }

            if ($type == 'acquisition-channels-line-charts') {
                $metricHeaders = $response['metricHeaders'];
                $rows = $response['rows'];

                foreach ($rows as $key => $row) {
                    $dimensionChannel   =   $row['dimensionValues'][0]['value'];

                    if ($dimensionChannel == 'Paid Search' || $dimensionChannel == 'Organic Search') {
                        $dimensionChannel   =   str_replace(' ', '_', $dimensionChannel);
                        $dateValue          =   $row['dimensionValues'][1]['value'];

                        foreach ($row['metricValues'] as $keyMetric => $metricValue) {
                            $metricHeaderName = $metricHeaders[$keyMetric]['name'];

                            $count = 0;
                            if (isset($data['line_chart'][$dimensionChannel][$metricHeaderName])) {
                                $count = count($data['line_chart'][$dimensionChannel][$metricHeaderName]);
                            }
                            $data['line_chart'][$dimensionChannel][$metricHeaderName][$count]['x'] = $dateValue;
                            $data['line_chart'][$dimensionChannel][$metricHeaderName][$count]['y'] = (int) $metricValue['value'];
                        }
                    }
                }
            }

            if ($type == 'acquisition-all-rows') {
                $metricHeaders = $response['metricHeaders'];
                $rows = $response['rows'];

                foreach ($rows as $key => $row) {
                    $dimensionValue = $row['dimensionValues'][0]['value'];

                    $count = 0;
                    if (isset($data['rows'])) {
                        $count = count($data['rows']);
                    }
                    $data['rows'][$count]['channels'] = $dimensionValue;

                    foreach ($row['metricValues'] as $keyMetric => $metricValue) {
                        $metricHeaderName   =   $metricHeaders[$keyMetric]['name'];
                        $indexLabelValue    =   (int) $metricValue['value'];

                        $data['rows'][$count][$metricHeaderName] = $indexLabelValue;
                    }
                }
            }

            if ($type == 'acquisition-all-counts') {
                $metricHeaders = $response['metricHeaders'];

                foreach ($metricHeaders as $metricHeaderKey => $metricHeader) {
                    $metricHeaderName = $metricHeaders[$metricHeaderKey]['name'];
                    $metricValues = $response['rows'][0]['metricValues'];

                    foreach ($metricValues as $metricValueKey => $metricValue) {
                        if ($metricHeaderKey == $metricValueKey) {
                            $data['count'][$metricHeaderName] = (int) $metricValues[$metricValueKey]['value'];
                        }
                    }
                }
            }

            if ($type == 'acquisition-channels-counts') {
                $metricHeaders = $response['metricHeaders'];
                $rows = $response['rows'];

                foreach ($rows as $key => $row) {
                    $dimensionChannel =  $row['dimensionValues'][0]['value'];

                    if ($dimensionChannel == 'Paid Search' || $dimensionChannel == 'Organic Search') {
                        foreach ($metricHeaders as $metricHeaderKey => $metricHeader) {
                            $metricHeaderName = $metricHeaders[$metricHeaderKey]['name'];
                            $metricValues = $response['rows'][$key]['metricValues'];

                            foreach ($metricValues as $metricValueKey => $metricValue) {
                                if ($metricHeaderKey == $metricValueKey) {
                                    $dimensionChannel = str_replace(' ', '_', $dimensionChannel);
                                    $data['count'][$dimensionChannel][$metricHeaderName] = (int) $metricValues[$metricValueKey]['value'];
                                }
                            }
                        }
                    }
                }
            }

            if (in_array($type, [
                "audience-location-country",
                "audience-location-region",
                "audience-location-city",
                "audience-languageCode",
                "audience-browser",
                "audience-userAgeBracket",
                "audience-userGender",
                "audience-mobileDeviceMarketingName",
                "audience-deviceCategory"
            ])) {
                $metricHeaders = $response['metricHeaders'];
                $rows = $response['rows'];
                $typeArr = explode('-', $type);
                $rowName = $typeArr[count($typeArr) - 1];
                $metricHeaders = $response['metricHeaders'];
                $rows = $response['rows'];

                foreach ($rows as $key => $row) {
                    $dimensionValue = $row['dimensionValues'][0]['value'];

                    $count = 0;
                    if (isset($data['rows'])) {
                        $count = count($data['rows']);
                    }
                    $data['rows'][$count][$rowName] = $dimensionValue;

                    foreach ($row['metricValues'] as $keyMetric => $metricValue) {
                        $metricHeaderName   =   $metricHeaders[$keyMetric]['name'];
                        $indexLabelValue    =   (int) $metricValue['value'];

                        $data['rows'][$count][$metricHeaderName] = $indexLabelValue;
                    }
                }
            }

            if ($type == 'audience-languagecode-line-charts') {
                $metricHeaders = $response['metricHeaders'];
                $rows = $response['rows'];

                foreach ($rows as $key => $row) {
                    $dimensionChannel   =   $row['dimensionValues'][0]['value'];
                    $dimensionChannel   =   str_replace(' ', '_', $dimensionChannel);
                    $dateValue          =   $row['dimensionValues'][1]['value'];

                    foreach ($row['metricValues'] as $keyMetric => $metricValue) {
                        $metricHeaderName = $metricHeaders[$keyMetric]['name'];

                        $count = 0;
                        if (isset($data['line_chart'][$dimensionChannel][$metricHeaderName])) {
                            $count = count($data['line_chart'][$dimensionChannel][$metricHeaderName]);
                        }
                        $data['line_chart'][$dimensionChannel][$metricHeaderName][$count]['x'] = $dateValue;
                        $data['line_chart'][$dimensionChannel][$metricHeaderName][$count]['y'] = (int) $metricValue['value'];
                    }
                }
            }
            if ($type == 'acquisition-channels-pie-charts') {
                $metricHeaders = $response['metricHeaders'];
                $rows = $response['rows'];

                foreach ($rows as $key => $row) {
                    $dimensionChannel       =   $row['dimensionValues'][0]['value'];
                    $dimensionChannelSource =   $row['dimensionValues'][1]['value'];
                    $metricValues           =   $response['rows'][$key]['metricValues'];

                    if ($dimensionChannel == 'Paid Search' || $dimensionChannel == 'Organic Search') {
                        foreach ($metricHeaders as $metricHeaderKey => $metricHeader) {
                            $metricHeaderName       =   $metricHeaders[$metricHeaderKey]['name'];

                            foreach ($metricValues as $metricValueKey => $metricValue) {
                                if ($metricHeaderKey == $metricValueKey) {
                                    $dimensionChannel = str_replace(' ', '_', $dimensionChannel);

                                    $count = 0;
                                    if (isset($data['pie_chart'][$dimensionChannel][$metricHeaderName])) {
                                        $count = count($data['pie_chart'][$dimensionChannel][$metricHeaderName]);
                                    }
                                    $data['pie_chart'][$dimensionChannel][$metricHeaderName][$count]['labels'] = $dimensionChannelSource;
                                    $data['pie_chart'][$dimensionChannel][$metricHeaderName][$count]['y'] = (int) $metricValues[$metricValueKey]['value'];
                                }
                            }
                        }
                    }
                }
            }
            if ($type == 'acquisition-paid-search-rows' || $type == 'acquisition-organic-search-rows') {
                $metricHeaders = $response['metricHeaders'];
                $rows = $response['rows'];
                foreach ($rows as $key => $row) {
                    $channelName = $row['dimensionValues'][0]['value'];

                    if ($channelName == 'Paid Search' || $channelName == 'Organic Search') {
                        $channelName = str_replace(' ', '_', $channelName);

                        $keywordName = $row['dimensionValues'][1]['value'];
                        $data['rows'][$channelName][$key]['keywords'] = $keywordName;

                        foreach ($row['metricValues'] as $keyMetric => $metricValue) {
                            $metricHeaderName = $metricHeaders[$keyMetric]['name'];
                            $data['rows'][$channelName][$key][$metricHeaderName] = $metricValue['value'];
                        }
                    }
                }

                // row position changed
                $tempData = [];
                foreach ($data as $newData) {
                    if ($type == 'acquisition-paid-search-rows') {
                        if (isset($newData['Paid_Search'])) {
                            foreach ($newData['Paid_Search'] as $value) {
                                $tempData['rows']['Paid_Search'][] = $value;
                            }
                        }
                    }
                    if ($type == 'acquisition-organic-search-rows') {
                        if (isset($newData['Organic_Search'])) {
                            foreach ($newData['Organic_Search'] as $value) {
                                $tempData['rows']['Organic_Search'][] = $value;
                            }
                        }
                    }
                }
                $data = $tempData;
            }
            if ($type == "audience-deviceCategoryDate") {
                $metricHeaders = $response['metricHeaders'];
                $rows = $response['rows'];

                foreach ($rows as $key => $row) {
                    $dateValue = $row['dimensionValues'][1]['value'];
                    $typeValue = $row['dimensionValues'][0]['value'];
                    foreach ($row['metricValues'] as $keyMetric => $metricValue) {
                        $metricHeaderName = $metricHeaders[$keyMetric]['name'];

                        $indexLabelValue = $metricValue['value'];
                        if ($metricHeaderName == 'userEngagementDuration') {
                            $indexLabelValue = $this->convertSeconds($metricValue['value']);
                        }
                        $data['line_chart'][$metricHeaderName][$typeValue][] = ['x' => $dateValue, 'y' => (int) $metricValue['value']];
                    }
                }
            }
        }

        return $data;
    }

    // aggregate response using prepare response
    public function aggregateGoogleAnalyticsResponse($arrResponse, $type)
    {
        $arrAggregation = $finalArray = [];
        $responseCount = count($arrResponse);

        if ($responseCount > 0) {
            if ($type == 'acquisition-all-counts') {
                foreach ($arrResponse as $responseData) {
                    foreach ($responseData['count'] as $key => $countValue) {
                        if (!isset($arrAggregation['count'][$key])) {
                            $arrAggregation['count'][$key] = 0;
                        }
                        $arrAggregation['count'][$key] += (int) $countValue;
                    }
                }
                if (count($arrAggregation) > 0) {
                    foreach ($arrAggregation['count'] as $key => $countValue) {
                        if ($key == 'userEngagementDuration') {
                            $arrAggregation['count'][$key] = $this->convertSeconds($countValue);
                        } else {
                            $arrAggregation['count'][$key] = $this->convertToInteger($countValue);
                        }
                    }
                }

                return $arrAggregation;
            }

            if ($type == 'acquisition-all-rows') {
                foreach ($arrResponse as $responseKey => $responseData) {
                    foreach ($responseData['rows'] as $row) {
                        $channelName   =   str_replace(' ', '_', $row['channels']);

                        foreach (array_keys($row) as $array_key) {
                            if ($array_key != 'channels') {
                                $count = 0;
                                if (isset($arrAggregation['rows'][$channelName][$array_key])) {
                                    $count = count($arrAggregation['rows'][$channelName][$array_key]);
                                }
                                if (!isset($arrAggregation['rows'][$channelName][$array_key][$count])) {
                                    $arrAggregation['rows'][$channelName][$array_key][$count] = 0;
                                }
                                $arrAggregation['rows'][$channelName]['channels'] = $row['channels'];
                                $arrAggregation['rows'][$channelName][$array_key][$count] += $row[$array_key];

                                if (!isset($arrAggregation['pie_chart'][$array_key][$channelName]['y'])) {
                                    $arrAggregation['pie_chart'][$array_key][$channelName]['y'] = 0;
                                }
                                $arrAggregation['pie_chart'][$array_key][$channelName]['labels'] = $row['channels'];
                                $arrAggregation['pie_chart'][$array_key][$channelName]['y'] += $row[$array_key];
                            }
                        }
                    }
                }

                if (count($arrAggregation) > 0) {
                    foreach ($arrAggregation['rows'] as $row) {
                        $count = 0;
                        if (isset($finalArray['rows'])) {
                            $count = count($finalArray['rows']);
                        }
                        $finalArray['rows'][$count]['channels'] = $row['channels'];

                        foreach (array_keys($row) as $array_key) {
                            if ($array_key != 'channels') {
                                $sumValue = array_sum($row[$array_key]);

                                if ($array_key == 'userEngagementDuration') {
                                    $indexLabelValue = $this->convertSeconds($sumValue);
                                    $finalArray['rows'][$count][$array_key] = $indexLabelValue;
                                } else {
                                    $finalArray['rows'][$count][$array_key] = $this->convertToInteger($sumValue);
                                }
                            }
                        }
                    }

                    foreach ($arrAggregation['pie_chart'] as $key => $row) {
                        foreach ($row as $rowValue) {
                            $count = 0;
                            if (isset($finalArray['pie_chart'][$key])) {
                                $count = count($finalArray['pie_chart'][$key]);
                            }
                            if ($key == 'userEngagementDuration') {
                                $rowValue['indexLabel'] = $this->convertSeconds($rowValue['y']);
                            } else {
                                $rowValue['indexLabel'] = $this->convertToInteger($rowValue['y']);
                            }
                            $finalArray['pie_chart'][$key][$count] = $rowValue;
                        }
                    }
                }

                return $finalArray;
            }

            if ($type == 'acquisition-all-line-chart') {
                foreach ($arrResponse as $responseData) {
                    foreach ($responseData['line_chart'] as $key => $row) {
                        foreach ($row as $rowData) {
                            $count = 0;
                            if (isset($arrAggregation['line_chart'][$key])) {
                                $count = count($arrAggregation['line_chart'][$key]);
                            }

                            if (!isset($arrAggregation['line_chart'][$key][$rowData['x']]['x'])) {
                                $arrAggregation['line_chart'][$key][$rowData['x']]['x'] = '';
                            }
                            if (!isset($arrAggregation['line_chart'][$key][$rowData['x']]['y'])) {
                                $arrAggregation['line_chart'][$key][$rowData['x']]['y'] = 0;
                            }

                            $arrAggregation['line_chart'][$key][$rowData['x']]['x'] = $rowData['x'];
                            $arrAggregation['line_chart'][$key][$rowData['x']]['y'] += $rowData['y'];
                        }
                    }
                }

                if (count($arrAggregation) > 0) {
                    foreach ($arrAggregation['line_chart'] as $sortingKey => $sortArray) {
                        $sortedKey = array_column($sortArray, 'x');
                        array_multisort($sortedKey, SORT_ASC, $sortArray);
                        $arrAggregation['line_chart'][$sortingKey] = $sortArray;
                    }

                    foreach ($arrAggregation['line_chart'] as $key => $row) {
                        foreach ($row as $rowKey => $rowData) {
                            $indexLabelValue = $rowData['y'];
                            if ($key == 'userEngagementDuration') {
                                $indexLabelValue = $this->convertSeconds($rowData['y']);
                            }

                            $arrAggregation['line_chart'][$key][$rowKey]['x'] = $this->getParseDate($rowData['x']);
                            $arrAggregation['line_chart'][$key][$rowKey]['y'] = (int) $rowData['y'];
                            $arrAggregation['line_chart'][$key][$rowKey]['displayLabel'] = (string) $indexLabelValue;
                        }
                    }
                }

                return $arrAggregation;
            }

            if ($type == 'acquisition-channels-counts') {
                foreach ($arrResponse as $responseData) {
                    foreach ($responseData['count'] as $channelName => $countData) {
                        foreach ($countData as $dimensionName => $countValue) {
                            if (!isset($arrAggregation['count'][$channelName][$dimensionName])) {
                                $arrAggregation['count'][$channelName][$dimensionName] = 0;
                            }
                            $arrAggregation['count'][$channelName][$dimensionName] += (int) $countValue;
                        }
                    }
                }
                if (count($arrAggregation) > 0) {
                    foreach ($arrAggregation['count'] as $channelName => $countData) {
                        foreach ($countData as $dimensionName => $countValue) {
                            if ($dimensionName == 'userEngagementDuration') {
                                $arrAggregation['count'][$channelName][$dimensionName] = $this->convertSeconds($countValue);
                            } else {
                                $arrAggregation['count'][$channelName][$dimensionName] = $this->convertToInteger($countValue);
                            }
                        }
                    }
                }

                return $arrAggregation;
            }

            if ($type == 'acquisition-channels-pie-charts') {
                foreach ($arrResponse as $chartData) {
                    foreach ($chartData['pie_chart'] as $rowKey => $row) {
                        foreach ($row as $rowValueKey => $rowValue) {
                            foreach ($rowValue as $rowDataKey => $rowData) {

                                if (!isset($arrAggregation['pie_chart'][$rowKey][$rowValueKey][$rowDataKey]['labels'])) {
                                    $arrAggregation['pie_chart'][$rowKey][$rowValueKey][$rowDataKey]['labels'] = '';
                                }
                                if (!isset($arrAggregation['pie_chart'][$rowKey][$rowValueKey][$rowDataKey]['y'])) {
                                    $arrAggregation['pie_chart'][$rowKey][$rowValueKey][$rowDataKey]['y'] = 0;
                                }
                                $arrAggregation['pie_chart'][$rowKey][$rowValueKey][$rowDataKey]['labels'] = $rowData['labels'];
                                $arrAggregation['pie_chart'][$rowKey][$rowValueKey][$rowDataKey]['y'] += $rowData['y'];
                            }
                        }
                    }
                }

                if (count($arrAggregation) > 0) {
                    foreach ($arrAggregation['pie_chart'] as $rowKey => $row) {
                        foreach ($row as $rowValueKey => $rowValue) {
                            foreach ($rowValue as $rowDataKey => $rowData) {

                                if ($rowValueKey == 'userEngagementDuration') {
                                    $indexLabelValue = $this->convertSeconds($rowData['y']);
                                } else {
                                    $indexLabelValue = $this->convertToInteger($rowData['y']);
                                }

                                $finalArray['pie_chart'][$rowKey][$rowValueKey][$rowDataKey]['labels']      =   $rowData['labels'];
                                $finalArray['pie_chart'][$rowKey][$rowValueKey][$rowDataKey]['y']           =   $rowData['y'];
                                $finalArray['pie_chart'][$rowKey][$rowValueKey][$rowDataKey]['indexLabel']  =   $indexLabelValue;
                            }
                        }
                    }
                }

                return $finalArray;
            }

            if ($type == 'acquisition-channels-line-charts') {
                foreach ($arrResponse as $responseData) {
                    foreach ($responseData['line_chart'] as $key => $lineChartData) {
                        foreach ($lineChartData as $rowKey => $row) {
                            foreach ($row as $rowData) {
                                if (!isset($arrAggregation['line_chart'][$key][$rowKey][$rowData['x']]['x'])) {
                                    $arrAggregation['line_chart'][$key][$rowKey][$rowData['x']]['x'] = '';
                                }
                                if (!isset($arrAggregation['line_chart'][$key][$rowKey][$rowData['x']]['y'])) {
                                    $arrAggregation['line_chart'][$key][$rowKey][$rowData['x']]['y'] = 0;
                                }

                                $arrAggregation['line_chart'][$key][$rowKey][$rowData['x']]['x'] = $rowData['x'];
                                $arrAggregation['line_chart'][$key][$rowKey][$rowData['x']]['y'] += $rowData['y'];
                            }
                        }
                    }
                }

                if (count($arrAggregation) > 0) {
                    foreach ($arrAggregation['line_chart'] as $key => $data) {
                        foreach ($data as $sortingKey => $sortArray) {
                            $sortedKey = array_column($sortArray, 'x');
                            array_multisort($sortedKey, SORT_ASC, $sortArray);
                            $arrAggregation['line_chart'][$key][$sortingKey] = $sortArray;
                        }
                    }

                    foreach ($arrAggregation['line_chart'] as $key => $lineChartData) {
                        foreach ($lineChartData as $rowKey => $row) {
                            foreach ($row as $rowDataKey => $rowData) {

                                $count = 0;
                                if (isset($finalArray['line_chart'][$key][$rowKey])) {
                                    $count = count($finalArray['line_chart'][$key][$rowKey]);
                                }

                                $indexLabelValue = $rowData['y'];
                                if ($rowKey == 'userEngagementDuration') {
                                    $indexLabelValue = $this->convertSeconds($rowData['y']);
                                }

                                $finalArray['line_chart'][$key][$rowKey][$count]['x'] = $this->getParseDate($rowData['x']);
                                $finalArray['line_chart'][$key][$rowKey][$count]['y'] = (int) $rowData['y'];
                                $finalArray['line_chart'][$key][$rowKey][$count]['displayLabel'] = (string) $indexLabelValue;
                            }
                        }
                    }
                }

                return $finalArray;
            }

            if ($type == 'acquisition-paid-search-rows' || $type == 'acquisition-organic-search-rows') {
                foreach ($arrResponse as $responseKey => $responseData) {
                    foreach ($responseData['rows'] as $channelName => $rows) {
                        foreach ($rows as $row) {
                            $keywordName   =   str_replace(' ', '_', $row['keywords']);

                            foreach (array_keys($row) as $array_key) {
                                if ($array_key != 'keywords') {
                                    $count = 0;

                                    if (isset($arrAggregation['rows'][$channelName][$keywordName][$array_key])) {
                                        $count = count($arrAggregation['rows'][$channelName][$keywordName][$array_key]);
                                    }
                                    if (!isset($arrAggregation['rows'][$channelName][$keywordName][$array_key][$count])) {
                                        $arrAggregation['rows'][$channelName][$keywordName][$array_key][$count] = 0;
                                    }
                                    $arrAggregation['rows'][$channelName][$keywordName]['keywords'] = $row['keywords'];
                                    $arrAggregation['rows'][$channelName][$keywordName][$array_key][$count] += $row[$array_key];
                                }
                            }
                        }
                    }
                }

                if (count($arrAggregation) > 0) {
                    foreach ($arrAggregation['rows'] as $channelName => $rows) {
                        foreach ($rows as $row) {
                            $count = 0;
                            if (isset($finalArray['rows'][$channelName])) {
                                $count = count($finalArray['rows'][$channelName]);
                            }
                            $finalArray['rows'][$channelName][$count]['keywords'] = $row['keywords'];

                            foreach (array_keys($row) as $array_key) {
                                if ($array_key != 'keywords') {
                                    $sumValue = array_sum($row[$array_key]);

                                    if ($array_key == 'userEngagementDuration') {
                                        $indexLabelValue = $this->convertSeconds($sumValue);
                                        $finalArray['rows'][$channelName][$count][$array_key] = $indexLabelValue;
                                    } else {
                                        $finalArray['rows'][$channelName][$count][$array_key] = $this->convertToInteger($sumValue);
                                    }
                                }
                            }
                        }
                    }
                }

                return $finalArray;
            }

            if ($type == 'audience-location-country' || $type == 'audience-location-region' || $type == 'audience-location-city') {
                $typeArr = explode('-', $type);
                $rowName = $typeArr[count($typeArr) - 1];

                foreach ($arrResponse as $responseKey => $responseData) {
                    foreach ($responseData['rows'] as $row) {
                        $channelName   =   str_replace(' ', '_', $row[$rowName]);

                        foreach (array_keys($row) as $array_key) {
                            if ($array_key != $rowName) {
                                $count = 0;
                                if (isset($arrAggregation['rows'][$channelName][$array_key])) {
                                    $count = count($arrAggregation['rows'][$channelName][$array_key]);
                                }
                                if (!isset($arrAggregation['rows'][$channelName][$array_key][$count])) {
                                    $arrAggregation['rows'][$channelName][$array_key][$count] = 0;
                                }
                                $arrAggregation['rows'][$channelName][$rowName] = $row[$rowName];
                                $arrAggregation['rows'][$channelName][$array_key][$count] += $row[$array_key];

                                if (!isset($arrAggregation['bar_chart'][$array_key][$channelName]['y'])) {
                                    $arrAggregation['bar_chart'][$array_key][$channelName]['y'] = 0;
                                }

                                $arrAggregation['bar_chart'][$array_key][$channelName]['label'] = $row[$rowName];
                                $arrAggregation['bar_chart'][$array_key][$channelName]['y'] += $row[$array_key];
                            }
                        }
                    }
                }

                if (count($arrAggregation) > 0) {
                    foreach ($arrAggregation['rows'] as $row) {
                        $count = 0;
                        if (isset($finalArray['rows'])) {
                            $count = count($finalArray['rows']);
                        }
                        $finalArray['rows'][$count][$rowName] = $row[$rowName];

                        foreach (array_keys($row) as $array_key) {
                            if ($array_key != $rowName) {
                                $sumValue = array_sum($row[$array_key]);

                                if ($array_key == 'userEngagementDuration') {
                                    $indexLabelValue = $this->convertSeconds($sumValue);
                                    $finalArray['rows'][$count][$array_key] = $indexLabelValue;
                                } else {
                                    $finalArray['rows'][$count][$array_key] = $this->convertToInteger($sumValue);
                                }
                            }
                        }
                    }

                    foreach ($arrAggregation['bar_chart'] as $key => $row) {
                        foreach ($row as $rowValue) {
                            $count = 0;
                            if (isset($finalArray['bar_chart'][$key])) {
                                $count = count($finalArray['bar_chart'][$key]);
                            }
                            if ($key == 'userEngagementDuration') {
                                $rowValue['indexLabel'] = $this->convertSeconds($rowValue['y']);
                            } else {
                                $rowValue['indexLabel'] = $this->convertToInteger($rowValue['y']);
                            }
                            $finalArray['bar_chart'][$key][$count] = $rowValue;
                        }
                    }
                }
                return $finalArray;
            }

            if ($type == 'audience-languageCode') {
                $typeArr = explode('-', $type);
                $rowName = $typeArr[count($typeArr) - 1];

                foreach ($arrResponse as $responseKey => $responseData) {
                    foreach ($responseData['rows'] as $row) {
                        $channelName   =   str_replace(' ', '_', $row[$rowName]);

                        foreach (array_keys($row) as $array_key) {
                            if ($array_key != $rowName) {
                                $count = 0;
                                if (isset($arrAggregation['rows'][$channelName][$array_key])) {
                                    $count = count($arrAggregation['rows'][$channelName][$array_key]);
                                }
                                if (!isset($arrAggregation['rows'][$channelName][$array_key][$count])) {
                                    $arrAggregation['rows'][$channelName][$array_key][$count] = 0;
                                }
                                $arrAggregation['rows'][$channelName][$rowName] = $row[$rowName];
                                $arrAggregation['rows'][$channelName][$array_key][$count] += $row[$array_key];

                                if (!isset($arrAggregation['bar_chart'][$array_key][$channelName]['y'])) {
                                    $arrAggregation['bar_chart'][$array_key][$channelName]['y'] = 0;
                                }

                                $arrAggregation['bar_chart'][$array_key][$channelName]['label'] = $row[$rowName];
                                $arrAggregation['bar_chart'][$array_key][$channelName]['y'] += $row[$array_key];
                            }
                        }
                    }
                }

                if (count($arrAggregation) > 0) {
                    foreach ($arrAggregation['rows'] as $row) {
                        $count = 0;
                        if (isset($finalArray['rows'])) {
                            $count = count($finalArray['rows']);
                        }
                        $finalArray['rows'][$count][$rowName] = $row[$rowName];

                        foreach (array_keys($row) as $array_key) {
                            if ($array_key != $rowName) {
                                $sumValue = array_sum($row[$array_key]);

                                if ($array_key == 'userEngagementDuration') {
                                    $indexLabelValue = $this->convertSeconds($sumValue);
                                    $finalArray['rows'][$count][$array_key] = $indexLabelValue;
                                } else {
                                    $finalArray['rows'][$count][$array_key] = $this->convertToInteger($sumValue);
                                }
                            }
                        }
                    }

                    foreach ($arrAggregation['bar_chart'] as $key => $row) {
                        foreach ($row as $rowValue) {
                            $count = 0;
                            if (isset($finalArray['bar_chart'][$key])) {
                                $count = count($finalArray['bar_chart'][$key]);
                            }
                            if ($key == 'userEngagementDuration') {
                                $rowValue['indexLabel'] = $this->convertSeconds($rowValue['y']);
                            } else {
                                $rowValue['indexLabel'] = $this->convertToInteger($rowValue['y']);
                            }
                            $finalArray['bar_chart'][$key][$count] = $rowValue;
                        }
                    }
                }
                return $finalArray;
            }

            if ($type == 'audience-languagecode-line-charts') {
                foreach ($arrResponse as $responseData) {
                    foreach ($responseData['line_chart'] as $key => $lineChartData) {
                        foreach ($lineChartData as $rowKey => $row) {
                            foreach ($row as $rowData) {
                                if (!isset($arrAggregation['line_chart'][$key][$rowKey][$rowData['x']]['x'])) {
                                    $arrAggregation['line_chart'][$key][$rowKey][$rowData['x']]['x'] = '';
                                }
                                if (!isset($arrAggregation['line_chart'][$key][$rowKey][$rowData['x']]['y'])) {
                                    $arrAggregation['line_chart'][$key][$rowKey][$rowData['x']]['y'] = 0;
                                }

                                $arrAggregation['line_chart'][$key][$rowKey][$rowData['x']]['x'] = $rowData['x'];
                                $arrAggregation['line_chart'][$key][$rowKey][$rowData['x']]['y'] += $rowData['y'];
                            }
                        }
                    }
                }

                if (count($arrAggregation) > 0) {
                    foreach ($arrAggregation['line_chart'] as $key => $data) {
                        foreach ($data as $sortingKey => $sortArray) {
                            $sortedKey = array_column($sortArray, 'x');
                            array_multisort($sortedKey, SORT_ASC, $sortArray);
                            $arrAggregation['line_chart'][$key][$sortingKey] = $sortArray;
                        }
                    }

                    foreach ($arrAggregation['line_chart'] as $key => $lineChartData) {
                        foreach ($lineChartData as $rowKey => $row) {
                            foreach ($row as $rowDataKey => $rowData) {

                                $count = 0;
                                if (isset($finalArray['line_chart'][$key][$rowKey])) {
                                    $count = count($finalArray['line_chart'][$key][$rowKey]);
                                }

                                $indexLabelValue = $rowData['y'];
                                if ($rowKey == 'userEngagementDuration') {
                                    $indexLabelValue = $this->convertSeconds($rowData['y']);
                                }

                                $finalArray['line_chart'][$key][$rowKey][$count]['x'] = $this->getParseDate($rowData['x']);
                                $finalArray['line_chart'][$key][$rowKey][$count]['y'] = (int) $rowData['y'];
                                $finalArray['line_chart'][$key][$rowKey][$count]['indexLabel'] = (string) $indexLabelValue;
                            }
                        }
                    }
                }

                return $finalArray;
            }

            if ($type == 'audience-userAgeBracket') {
                $typeArr = explode('-', $type);
                $rowName = $typeArr[count($typeArr) - 1];

                foreach ($arrResponse as $responseKey => $responseData) {
                    foreach ($responseData['rows'] as $row) {
                        $channelName   =   str_replace(' ', '_', $row[$rowName]);

                        foreach (array_keys($row) as $array_key) {
                            if ($array_key != $rowName) {
                                if (!isset($arrAggregation['bar_chart'][$array_key][$channelName]['y'])) {
                                    $arrAggregation['bar_chart'][$array_key][$channelName]['y'] = 0;
                                }

                                $arrAggregation['bar_chart'][$array_key][$channelName]['label'] = $row[$rowName];
                                $arrAggregation['bar_chart'][$array_key][$channelName]['y'] += $row[$array_key];
                            }
                        }
                    }
                }

                if (count($arrAggregation) > 0) {
                    foreach ($arrAggregation['bar_chart'] as $key => $row) {
                        foreach ($row as $rowValue) {
                            $count = 0;
                            if (isset($finalArray['bar_chart'][$key])) {
                                $count = count($finalArray['bar_chart'][$key]);
                            }
                            if ($key == 'userEngagementDuration') {
                                $rowValue['indexLabel'] = $this->convertSeconds($rowValue['y']);
                            } else {
                                $rowValue['indexLabel'] = $this->convertToInteger($rowValue['y']);
                            }
                            $finalArray['bar_chart'][$key][$count] = $rowValue;
                        }
                    }
                }
                return $finalArray;
            }

            if ($type == 'audience-userGender') {
                $typeArr = explode('-', $type);
                $rowName = $typeArr[count($typeArr) - 1];
                foreach ($arrResponse as $responseKey => $responseData) {
                    foreach ($responseData['rows'] as $row) {
                        $channelName   =   str_replace(' ', '_', $row[$rowName]);

                        foreach (array_keys($row) as $array_key) {
                            if ($array_key != $rowName) {
                                if (!isset($arrAggregation['pie_chart'][$array_key][$channelName]['y'])) {
                                    $arrAggregation['pie_chart'][$array_key][$channelName]['y'] = 0;
                                }
                                $arrAggregation['pie_chart'][$array_key][$channelName]['labels'] = $channelName;
                                $arrAggregation['pie_chart'][$array_key][$channelName]['y'] += $row[$array_key];
                            }
                        }
                    }
                }

                if (count($arrAggregation) > 0) {
                    foreach ($arrAggregation['pie_chart'] as $key => $row) {
                        foreach ($row as $rowValue) {
                            $count = 0;
                            if (isset($finalArray['pie_chart'][$key])) {
                                $count = count($finalArray['pie_chart'][$key]);
                            }
                            if ($key == 'userEngagementDuration') {
                                $rowValue['indexLabel'] = $this->convertSeconds($rowValue['y']);
                            } else {
                                $rowValue['indexLabel'] = $this->convertToInteger($rowValue['y']);
                            }
                            $finalArray['pie_chart'][$key][$count] = $rowValue;
                        }
                    }
                }
                return $finalArray;
            }

            if ($type == 'audience-mobileDeviceMarketingName') {
                $typeArr = explode('-', $type);
                $rowName = $typeArr[count($typeArr) - 1];

                foreach ($arrResponse as $responseKey => $responseData) {
                    foreach ($responseData['rows'] as $row) {
                        $channelName   =   str_replace(' ', '_', $row[$rowName]);

                        foreach (array_keys($row) as $array_key) {
                            if ($array_key != $rowName) {
                                $count = 0;
                                if (isset($arrAggregation['rows'][$channelName][$array_key])) {
                                    $count = count($arrAggregation['rows'][$channelName][$array_key]);
                                }
                                if (!isset($arrAggregation['rows'][$channelName][$array_key][$count])) {
                                    $arrAggregation['rows'][$channelName][$array_key][$count] = 0;
                                }
                                $arrAggregation['rows'][$channelName][$rowName] = $row[$rowName];
                                $arrAggregation['rows'][$channelName][$array_key][$count] += $row[$array_key];
                            }
                        }
                    }
                }

                if (count($arrAggregation) > 0) {
                    foreach ($arrAggregation['rows'] as $row) {
                        $count = 0;
                        if (isset($finalArray['rows'])) {
                            $count = count($finalArray['rows']);
                        }
                        $finalArray['rows'][$count][$rowName] = $row[$rowName];

                        foreach (array_keys($row) as $array_key) {
                            if ($array_key != $rowName) {
                                $sumValue = array_sum($row[$array_key]);

                                if ($array_key == 'userEngagementDuration') {
                                    $indexLabelValue = $this->convertSeconds($sumValue);
                                    $finalArray['rows'][$count][$array_key] = $indexLabelValue;
                                } else {
                                    $finalArray['rows'][$count][$array_key] = $this->convertToInteger($sumValue);
                                }
                            }
                        }
                    }
                }
                return $finalArray;
            }

            if ($type == 'audience-deviceCategory') {
                $typeArr = explode('-', $type);
                $rowName = $typeArr[count($typeArr) - 1];
                foreach ($arrResponse as $responseKey => $responseData) {
                    foreach ($responseData['rows'] as $row) {
                        $channelName   =   str_replace(' ', '_', $row[$rowName]);

                        foreach (array_keys($row) as $array_key) {
                            if ($array_key != $rowName) {
                                if (!isset($arrAggregation['pie_chart'][$array_key][$channelName]['y'])) {
                                    $arrAggregation['pie_chart'][$array_key][$channelName]['y'] = 0;
                                }
                                $arrAggregation['pie_chart'][$array_key][$channelName]['labels'] = $channelName;
                                $arrAggregation['pie_chart'][$array_key][$channelName]['y'] += $row[$array_key];
                            }
                        }
                    }
                }

                if (count($arrAggregation) > 0) {
                    foreach ($arrAggregation['pie_chart'] as $key => $row) {
                        foreach ($row as $rowValue) {
                            $count = 0;
                            if (isset($finalArray['pie_chart'][$key])) {
                                $count = count($finalArray['pie_chart'][$key]);
                            }
                            if ($key == 'userEngagementDuration') {
                                $rowValue['indexLabel'] = $this->convertSeconds($rowValue['y']);
                            } else {
                                $rowValue['indexLabel'] = $this->convertToInteger($rowValue['y']);
                            }
                            $finalArray['pie_chart'][$key][$count] = $rowValue;
                        }
                    }
                }
                return $finalArray;
            }

            if ($type == 'audience-deviceCategoryDate') {
                foreach ($arrResponse as $responseData) {
                    foreach ($responseData['line_chart'] as $matrixKey => $row) {
                        foreach ($row as $responseKey => $resData) {
                            foreach ($resData as $rKey => $rData) {
                                if (!isset($arrAggregation['line_chart'][$matrixKey][$responseKey][$rKey]['y'])) {
                                    $arrAggregation['line_chart'][$matrixKey][$responseKey][$rKey]['y'] = 0;
                                }
                                $arrAggregation['line_chart'][$matrixKey][$responseKey][$rKey]['x'] = $rData['x'];
                                $arrAggregation['line_chart'][$matrixKey][$responseKey][$rKey]['y'] += $rData['y'];
                            }
                        }
                    }
                }
                if (count($arrAggregation) > 0) {

                    foreach ($arrAggregation['line_chart'] as $sortingKey => $sortArray) {
                        foreach ($sortArray as $ssortingKey => $ssortArray) {
                            $sortedKey = array_column($ssortArray, 'x');
                            array_multisort($sortedKey, SORT_ASC, $ssortArray);
                            $arrAggregation['line_chart'][$sortingKey][$ssortingKey] = $ssortArray;
                        }
                    }

                    foreach ($arrAggregation['line_chart'] as $key => $row) {
                        foreach ($row as $rowKey => $rowData) {
                            foreach ($rowData as $rowKey1 => $rowData1) {
                                $indexLabelValue = $rowData1['y'];
                                if ($key == 'userEngagementDuration') {
                                    $indexLabelValue = $this->convertSeconds($rowData1['y']);
                                }

                                $arrAggregation['line_chart'][$key][$rowKey][$rowKey1]['x'] = $this->getParseDate($rowData1['x']);
                                $arrAggregation['line_chart'][$key][$rowKey][$rowKey1]['y'] = (int) $rowData1['y'];
                                $arrAggregation['line_chart'][$key][$rowKey][$rowKey1]['indexLabel'] = (string) $indexLabelValue;
                            }
                        }
                    }
                }
                return $arrAggregation;
            }
            if ($type == 'audience-browser') {
                $typeArr = explode('-', $type);
                $rowName = $typeArr[count($typeArr) - 1];

                foreach ($arrResponse as $responseKey => $responseData) {
                    foreach ($responseData['rows'] as $row) {
                        $channelName   =   str_replace(' ', '_', $row[$rowName]);

                        foreach (array_keys($row) as $array_key) {
                            if ($array_key != $rowName) {
                                $count = 0;
                                if (isset($arrAggregation['rows'][$channelName][$array_key])) {
                                    $count = count($arrAggregation['rows'][$channelName][$array_key]);
                                }
                                if (!isset($arrAggregation['rows'][$channelName][$array_key][$count])) {
                                    $arrAggregation['rows'][$channelName][$array_key][$count] = 0;
                                }
                                $arrAggregation['rows'][$channelName][$rowName] = $row[$rowName];
                                $arrAggregation['rows'][$channelName][$array_key][$count] += $row[$array_key];

                                if (!isset($arrAggregation['bar_chart'][$array_key][$channelName]['y'])) {
                                    $arrAggregation['bar_chart'][$array_key][$channelName]['y'] = 0;
                                }

                                $arrAggregation['bar_chart'][$array_key][$channelName]['label'] = $row[$rowName];
                                $arrAggregation['bar_chart'][$array_key][$channelName]['y'] += $row[$array_key];
                            }
                        }
                    }
                }

                if (count($arrAggregation) > 0) {
                    foreach ($arrAggregation['rows'] as $row) {
                        $count = 0;
                        if (isset($finalArray['rows'])) {
                            $count = count($finalArray['rows']);
                        }
                        $finalArray['rows'][$count][$rowName] = $row[$rowName];

                        foreach (array_keys($row) as $array_key) {
                            if ($array_key != $rowName) {
                                $sumValue = array_sum($row[$array_key]);

                                if ($array_key == 'userEngagementDuration') {
                                    $indexLabelValue = $this->convertSeconds($sumValue);
                                    $finalArray['rows'][$count][$array_key] = $indexLabelValue;
                                } else {
                                    $finalArray['rows'][$count][$array_key] = $this->convertToInteger($sumValue);
                                }
                            }
                        }
                    }

                    foreach ($arrAggregation['bar_chart'] as $key => $row) {
                        foreach ($row as $rowValue) {
                            $count = 0;
                            if (isset($finalArray['bar_chart'][$key])) {
                                $count = count($finalArray['bar_chart'][$key]);
                            }
                            if ($key == 'userEngagementDuration') {
                                $rowValue['indexLabel'] = $this->convertSeconds($rowValue['y']);
                            } else {
                                $rowValue['indexLabel'] = $this->convertToInteger($rowValue['y']);
                            }
                            $finalArray['bar_chart'][$key][$count] = $rowValue;
                        }
                    }
                }
                return $finalArray;
            }
        }

        return $arrAggregation;
    }

    // get details if available in db
    public function getGoogleAnalyticDetails($requestFilter, $type)
    {
        $expired_time = \Carbon\Carbon::now()->subSeconds(config('utility.google_analytics.request_expirations'))->toDateTimeString();
        return GoogleAnalytic::where([
            'user_id'           =>  auth()->user()->id,
            'request_type'      =>  $this->getRequestTypeEnum($type),
            'request_filter'    =>  json_encode($requestFilter),
        ])
            ->where('updated_at', '>', $expired_time)
            ->latest()
            ->first();
    }

    // store details in db
    public function storeGoogleAnalyticsResponse($requestFilter, $type, $response, $aggregateResponse)
    {
        return GoogleAnalytic::updateOrCreate([
            'user_id'           =>  auth()->user()->id,
            'request_type'      =>  $this->getRequestTypeEnum($type),
        ], [
            'custom_id'         =>  getUniqueString('google_analytics'),
            'request_filter'    =>  json_encode($requestFilter, true),
            'response_json'     =>  json_encode($response, true),
            'response_web'      =>  json_encode($aggregateResponse, true),
            'response_api'      =>  json_encode($aggregateResponse, true),
        ]);
    }

    // get request type enum details
    public function getRequestTypeEnum($type)
    {
        return config('utility.google_analytics.request_type.' . $type);
    }

    // get parse date
    public function getParseDate($date)
    {
        return \Carbon\Carbon::parse($date)->format('Y, m, d');
    }

    // convert in to seconds
    public function convertSeconds($seconds)
    {
        return \Carbon\CarbonInterval::seconds($seconds)->cascade()->forHumans(['short' => true]);
    }

    // convert in to integer
    public function convertToInteger($value)
    {
        return  number_format($value);
    }

    /* ****************************************************** EXTRA ***************************************************************************** */

    // property details using api
    // public function callPropertiesApi($account, $token){
    //     try{
    //         $client     =   new GuzzleHttpClient();
    //         $requestUrl =   'https://analyticsadmin.googleapis.com/v1alpha/properties?filter=parent:'.$account;

    //         $response   =   $client->get($requestUrl, [
    //             'headers' => [
    //                 'Authorization' =>  'Bearer ' . $token . '',
    //                 'Accept'        =>  'application/json',
    //             ],
    //         ]);
    //         $data = json_decode($response->getBody(), true);
    //         if($data){
    //             return $data;
    //         }
    //         return false;

    //     }catch(\Exception $e){
    //         return false;
    //     }
    // }

    // property details
    // public function getPropertyDetails(){
    // try{
    //     $analytics = new Analytics($this->client);
    //     $accountSummaries = $analytics->management_accountSummaries->listManagementAccountSummaries();
    //     foreach ($accountSummaries->getItems() as $account) {
    //         if( $account->getName() == 'Purple Bunny Marketing & Event Management' ){
    //             return $account->id;

    //             // foreach ($account->getWebProperties() as $property) {
    //                 // return $property;

    //                 // dd("property details:", $property);
    //             // }
    //         }
    //     }
    // }catch(\Exception $e){
    //     dd($e->getMessage());
    // }
    // }

    // report details
    // public function getReport($property_id){
    //     // $viewId  =   '293556076';

    //     $viewId     =   $property_id;
    //     $startDate  =   '2023-06-01';
    //     $endDate    =   '2023-06-30';
    //     $analytics  =   new Analytics($this->client);

    //     $response = $analytics->data_ga->get(
    //         "ga:" . $viewId,
    //         $startDate,
    //         $endDate,
    //         "ga:sessions,ga:pageviews",
    //         [
    //             'dimensions'    =>  'ga:country,ga:city',
    //             'sort'          =>  '-ga:sessions',
    //             'filters'       =>  'ga:country==United States',
    //             'max-results'   =>  10,
    //         ]
    //     );

    //     return $response->getRows();
    // }

    // http api call
    // public function callHttpApi($property_id){
    //     $client = new GuzzleHttpClient();
    //     $options = [
    //         'headers' => [
    //             'Authorization' =>  'Bearer ' . $this->access_token,
    //             'Content-Type'  =>  'application/json'
    //         ]
    //     ];
    //     $url = 'https://analyticsdata.googleapis.com/v1beta/properties/'.$property_id.':runReport';
    //     $requestData = [
    //         "dimensions"    =>  [
    //             "name"  =>  "city"
    //         ],
    //         "metrics"       =>  [
    //             "name"  =>  "activeUsers"
    //         ],
    //         "dateRanges"    =>  [
    //             "startDate" =>  "2023-05-01",
    //             "endDate"   =>  "2023-05-31"
    //         ]
    //         // 'dimensions'    =>  [['name' => 'country']],
    //         // 'metrics'       =>  [['name' => 'activeUsers']],
    //     ];

    //     // dd($url, $options, json_encode($requestData));
    //     try {
    //         $response = $client->post($url, $options, json_encode($requestData));
    //         $responseData = json_decode($response->getBody(), true);
    //         dd("responseData", $responseData);
    //     } catch (\Exception $e) {
    //         dd('Error: ' . $e->getMessage());
    //     }
    // }
}
