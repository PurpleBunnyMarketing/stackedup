<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SubscriptionPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $type = ($this['payment'] && $this['payment']->type) ? $this['payment']->type : "";
        $monthlyOrYearlyAmount = $type == "monthly" ? $this['monthlyPrice']->amount : ($type == "yearly" ? $this['yearlyPrice']->amount : "");
        $data = [
            'subscription_plan' => [
                [
                    'page_count'    =>  Count($this['facebookPage']) ?? "",
                    'amount'       => ($this['facebookPage']) ? Count($this['facebookPage']) *  $monthlyOrYearlyAmount : "",
                    'image'                => asset('frontend/assets/media/images/facebook.svg' ?? "public/machine/no_image.png"),
                    'type'         => 'facebookPage'
                ],
                [
                    'page_count'    =>  Count($this['linkedInPage']) ?? "",
                    'amount'       => ($this['linkedInPage']) ? Count($this['linkedInPage']) *  $monthlyOrYearlyAmount : "",
                    'image'                => asset('frontend/assets/media/images/linkedin.svg' ?? "public/machine/no_image.png"),
                    'type'             => 'linkedin'
                ],
                [
                    'page_count'     =>  Count($this['twitterPage']) ?? "",
                    'amount'       => ($this['twitterPage']) ? Count($this['twitterPage']) *  $monthlyOrYearlyAmount : "",
                    'image'                => asset('frontend/assets/media/images/twitter.svg' ?? "public/machine/no_image.png"),
                    'type'  => 'twitter'
                ],
                [
                    'page_count'   =>  Count($this['instagramPage']) ?? "",
                    'amount'       => ($this['instagramPage']) ? Count($this['instagramPage']) *  $monthlyOrYearlyAmount : "",
                    'image'                => asset('frontend/assets/media/images/instagram.svg' ?? "public/machine/no_image.png"),
                    'type' =>   'instagram'
                ],
                [
                    'page_count'   =>  Count($this['googlePage']) ?? "",
                    'amount'       => ($this['googlePage']) ? Count($this['googlePage']) *  $monthlyOrYearlyAmount : "",
                    'image'                => asset('frontend/assets/media/images/google-logo.svg' ?? "public/machine/no_image.png"),
                    'type' =>   'googlePage'
                ],
                // [
                //     'page_count'   =>  Count($this['googleAnalyticsPage']) ?? "",
                //     'amount'       => ($this['googleAnalyticsPage'] && $this['monthlyPrice']) ? Count($this['googleAnalyticsPage']) *  $this['monthlyPrice']->amount : "",
                //     'image'                => asset('frontend/assets/media/images/google-analytics-icon.svg' ?? "public/machine/no_image.png"),
                //     'type' =>   'googleAnalytics'
                // ],
                // [
                //     'page_count'   =>  Count($this['googleAdsPage']) ?? "",
                //     'amount'       => ($this['googleAdsPage'] && $this['monthlyPrice']) ? Count($this['googleAdsPage']) *  $this['monthlyPrice']->amount : "",
                //     'image'                => asset('frontend/assets/media/images/google-ads-icon.svg' ?? "public/machine/no_image.png"),
                //     'type' =>   'googleAds'
                // ],
                // [
                //     'page_count'   =>  Count($this['facebookAdsPage']) ?? "",
                //     'amount'       => ($this['facebookAdsPage'] && $this['monthlyPrice']) ? Count($this['facebookAdsPage']) *  $this['monthlyPrice']->amount : "",
                //     'image'                => asset('frontend/assets/media/images/facebook.svg' ?? "public/machine/no_image.png"),
                //     'type' =>   'facebookAds'
                // ],
            ],
            'total_channel'             => $this['totalChannel'] ?? 0,
            'mothly_subscription'       => ($this['totalChannel'] && $this['monthlyPrice']) ? $this['totalChannel'] * $this['monthlyPrice']->amount : "",
            'annual_subscription'       => ($this['totalChannel'] && $this['yearlyPrice']) ? $this['totalChannel'] * $this['yearlyPrice']->amount : "",
            'plan'                      => $type,
            'total_subscription'        => ($this['payment']) ? $this['payment']->amount : ""


        ];
        return collect($data);
    }

    public function with($request)
    {
        return [
            'meta' => [
                'api' =>  'v.1.0',
                'url' =>  url()->current(),
            ],
        ];
    }
}
