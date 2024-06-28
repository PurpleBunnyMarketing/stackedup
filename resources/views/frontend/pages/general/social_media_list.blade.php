@extends('frontend.layouts.app')

@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container  mt-10">
            <!--begin::Dashboard-->
            <!--begin::Row-->
            <div class="head-title-wrap">
                <h1 class="secondary-color">Link your Social Media channels and start stacking those posts!</h1>
            </div>
            <!-- for second page start  -->
            <input type="hidden" name="media_id" id="media_id" value="">
            <input type="hidden" name="mediapage_id" id="mediapage_id" value="">
            {{-- Facebook --}}
            <div class="row stacking-post-block mt-2">
                <div class="col-sm-8 col-lg-7">
                    <div class="stacking-posts">
                        <div class="stacking-header">
                            <div class="d-flex align-items-center">
                                <div class="stack-post-icon">
                                    <img src="{{ asset('frontend/assets/media/images/facebook.svg') }}">
                                </div>
                                <div class="stack-post-count ml-4">
                                    <span># Channels:</span><span>{{$usedPagefacebook ??
                                        0}}</span>/<span>{{$totalPagefacebook ?? 0}}</span>
                                </div>
                            </div>
                            <a class="custom-link link-btn" data-social-name="Facebook" href="javascript:;"
                                data-total-page="{{$totalPagefacebook}}" {{in_array(1,$social_media_detail)
                                ? "disabled=disabled" : "" }}>Link Facebook</a>
                        </div>
                        @if($getFacebookPage->isNotEmpty())
                        @foreach($getFacebookPage as $facebookPage)
                        <div class="stack-page-link stage-page-link-loop">
                            <h2 style="width: 320px !important;">{{$facebookPage->mediaPage->page_name}}</h2>
                            @php
                            $days =
                            \Carbon\Carbon::parse($facebookPage->mediaPage->socialMediaDetail->token_expiry)->diffInDays(now()->toDateString());
                            $isExpired = $facebookPage->mediaPage->socialMediaDetail->token_expiry <
                                \Carbon\Carbon::parse(now())->toDateString();
                                @endphp
                                <p
                                    class=" {{ $days >= 10 && !$isExpired ? (($days <= 40 && $days >= 10) ? 'text-warning' : 'text-success') : 'text-danger' }}">
                                    Token Expiry :
                                    {{ $isExpired ? 'Expired' : ($days == 0 ? 'Today' : '(in '. $days .' Days)') }}

                                    <i
                                        class="{{ $days >= 10 && !$isExpired ? ($days <= 40 && $days >= 10) ? ' fas fa-exclamation-circle text-warning' : 'far fa-check-circle text-success' : 'fas fa-exclamation-circle text-danger' }}"></i>
                                </p>
                                <div class="social-media-list-action-buttons-main">

                                    @if ($isExpired || $days <= 10) <a href="javascript:void(0)"
                                        class="custom-link relink-button" data-url="{{ route('auth.facebook') }}">
                                        Relink</a>
                                        @endif
                                        <button class="custom-link unlink_page"
                                            data-social-name="{{ $facebookPage->mediaPage->page_name}}"
                                            data-page-id="{{ $facebookPage->mediaPage->page_id}}"
                                            data-id="{{ $facebookPage->mediaPage->id }}"
                                            data-media-id="{{$facebookPage->media_id}}"
                                            id="addPage{{$facebookPage->mediaPage->id}}" data-isdelete="y"
                                            data-isused="n">Unlink</button>
                                </div>
                        </div>
                        @endforeach
                        @else
                        <div class="stack-page-link">
                            <center>
                                <h2>No pages</h2>
                            </center>
                        </div>
                        @endif
                        <!-- <div class="stack-page-link">
                            <h2>Facebook Page Name 1</h2>
                            <button class="custom-link">link</button>
                        </div> -->

                    </div>
                </div>
            </div>
            {{-- Facebook Ads --}}
            <div class="row stacking-post-block mt-2">
                <div class="col-sm-8 col-lg-7">
                    <div class="stacking-posts">
                        <div class="stacking-header">
                            <div class="d-flex align-items-center">
                                <div class="stack-post-icon">
                                    <img src="{{ asset('frontend/assets/media/images/facebook.svg') }}">
                                </div>
                                <div class="stack-post-count ml-4">
                                    <span># Channels:</span><span>{{$usedPagefacebookAds ??
                                        0}}</span>/<span>{{$totalPagefacebookAds ?? 0}}</span>
                                </div>
                            </div>
                            <a class="custom-link link-btn" data-social-name="Facebook_Ads" href="javascript:;"
                                data-total-page="{{$totalPagefacebookAds}}" {{in_array(8,$social_media_detail)
                                ? "disabled=disabled" : "" }}>Link Facebook Ads</a>
                        </div>
                        @if($getFacebookAdsPage->isNotEmpty())
                        @foreach($getFacebookAdsPage as $facebookAdsPage)
                        <div class="stack-page-link stage-page-link-loop">
                            <h2 style="width: 320px !important;">{{$facebookAdsPage->mediaPage->page_name}}</h2>
                            @php
                            $days =
                            \Carbon\Carbon::parse($facebookAdsPage->mediaPage->socialMediaDetail->token_expiry)->diffInDays(now()->toDateString());
                            $isFacebookAdsPageExpired = $facebookAdsPage->mediaPage->socialMediaDetail->token_expiry <
                                \Carbon\Carbon::parse(now())->toDateString();
                                @endphp
                                <p
                                    class=" {{ $days >= 10 && !$isFacebookAdsPageExpired ? (($days <= 40 && $days >= 10) ? 'text-warning' : 'text-success') : 'text-danger' }}">
                                    Token Expiry :
                                    {{ $isFacebookAdsPageExpired ? 'Expired' : ($days == 0 ? 'Today' : '(in '. $days .'
                                    Days)') }}

                                    <i
                                        class="{{ $days >= 10 && !$isFacebookAdsPageExpired ? (($days <= 40 && $days >= 10) ? ' fas fa-exclamation-circle text-warning' : 'far fa-check-circle text-success') : 'fas fa-exclamation-circle text-danger' }}"></i>
                                </p>
                                <div class="social-media-list-action-buttons-main">

                                    @if ($isFacebookAdsPageExpired || $days <= 10) <a href="javascript:void(0)"
                                        class="custom-link relink-button" data-url="{{ route('auth.facebook_ads') }}">
                                        Relink</a>
                                        @endif
                                        <button class="custom-link unlink_page"
                                            data-social-name="{{ $facebookAdsPage->mediaPage->page_name}}"
                                            data-page-id="{{ $facebookAdsPage->mediaPage->page_id}}"
                                            data-id="{{ $facebookAdsPage->mediaPage->id }}"
                                            data-media-id="{{$facebookAdsPage->media_id}}"
                                            id="addPage{{$facebookAdsPage->mediaPage->id}}" data-isdelete="y"
                                            data-isused="n">Unlink</button>
                                </div>
                        </div>
                        @endforeach
                        @else
                        <div class="stack-page-link">
                            <center>
                                <h2>No pages</h2>
                            </center>
                        </div>
                        @endif
                        <!-- <div class="stack-page-link">
                            <h2>Facebook Page Name 1</h2>
                            <button class="custom-link">link</button>
                        </div> -->

                    </div>
                </div>
            </div>
            {{-- Linkedin --}}
            <div class="row stacking-post-block mt-2">
                <div class="col-sm-8 col-lg-7">
                    <div class="stacking-posts">
                        <div class="stacking-header">
                            <div class="d-flex align-items-center">
                                <div class="stack-post-icon">
                                    <img src="{{ asset('frontend/assets/media/images/linkedin.svg') }}">
                                </div>
                                <div class="stack-post-count ml-4">
                                    <span># Channels:</span><span>{{$usedPagelinked ??
                                        0}}</span>/<span>{{$totalPagelinked?? 0}}</span>
                                </div>
                            </div>
                            <a class="custom-link link-btn" data-social-name="Linkedin" href="javascript:;"
                                data-total-page="{{$totalPagelinked}}" {{in_array(2,$social_media_detail)
                                ? "disabled=disabled" : "" }}>Link Linkedin</a>
                        </div>
                        @if($getLinkedinPage->isNotEmpty())
                        @foreach($getLinkedinPage as $linkedinPage)

                        <div class="stack-page-link stage-page-link-loop">
                            <h2 style="width: 320px !important;">{{$linkedinPage->mediaPage->page_name}}</h2>
                            @php
                            $days =
                            \Carbon\Carbon::parse($linkedinPage->mediaPage->socialMediaDetail->token_expiry)->diffInDays(now()->toDateString());
                            $isLinkedinPageExpired =$linkedinPage->mediaPage->socialMediaDetail->token_expiry <
                                \Carbon\Carbon::parse(now())->toDateString();
                                @endphp
                                <p
                                    class="{{ $days >= 10 && !$isLinkedinPageExpired ? (($days <= 40 && $days >= 10) ? 'text-warning' : 'text-success') : 'text-danger' }}">
                                    Token Expiry :
                                    {{ $isLinkedinPageExpired ? 'Expired' : ($days == 0 ? 'Today' : '(in '. $days .'
                                    Days)') }}
                                    <i
                                        class="{{ $days >= 10 && !$isLinkedinPageExpired ? (($days <= 40 && $days >= 10) ? ' fas fa-exclamation-circle text-warning' : 'far fa-check-circle text-success') : 'fas fa-exclamation-circle text-danger' }}"></i>
                                </p>
                                <div class="social-media-list-action-buttons-main">

                                    @if ($isLinkedinPageExpired || $days <= 10) <a href="javascript:void(0)"
                                        class="custom-link relink-button" data-url="{{ route('auth.linkedin') }}">
                                        Relink</a>
                                        @endif
                                        <button class="custom-link unlink_page"
                                            data-social-name="{{ $linkedinPage->mediaPage->page_name}}"
                                            data-page-id="{{ $linkedinPage->mediaPage->page_id }}"
                                            data-id="{{ $linkedinPage->mediaPage->id }}"
                                            data-media-id="{{$linkedinPage->media_id}}"
                                            id="addPage{{$linkedinPage->mediaPage->id}}" data-isdelete="y"
                                            data-isused="n">Unlink</button>

                                </div>
                        </div>
                        @endforeach
                        @else
                        <div class="stack-page-link">
                            <center>
                                <h2>No pages</h2>
                            </center>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            {{-- X(Twitter) --}}
            <div class="row stacking-post-block mt-2">
                <div class="col-sm-8 col-lg-7">
                    <div class="stacking-posts">
                        <div class="stacking-header">
                            <div class="d-flex align-items-center">
                                <div class="stack-post-icon">
                                    <img src="{{ asset('frontend/assets/media/images/twitter.svg') }}">
                                </div>
                                <div class="stack-post-count ml-4">
                                    <span># Channels:</span><span>{{$usedPagetwitter ??
                                        0}}</span>/<span>{{$totalPagetwitter ?? 0}}</span>
                                </div>
                            </div>
                            <a class="custom-link link-btn" data-social-name="Twitter" href="javascript:;"
                                data-total-page="{{$totalPagetwitter}}" {{in_array(3,$social_media_detail)
                                ? "disabled=disabled" : "" }}>Link X(Twitter)</a>
                        </div>
                        @if($getTwitterPage->isNotEmpty())
                        @foreach($getTwitterPage as $twitterPage)

                        <div class="stack-page-link stage-page-link-loop">
                            <h2 style="width: 320px !important;">{{$twitterPage->mediaPage->page_name}}</h2>
                            @if (isset($socialMediaExpiry['twitter']) && $socialMediaExpiry['twitter'] !== '')
                            <p class="text-success">
                                Token Expiry : {{$socialMediaExpiry['twitter'] ? 'Valid' : 'Expired'}} <i
                                    class="far fa-check-circle text-success"></i>
                            </p>
                            @endif
                            <div class="social-media-list-action-buttons-main">

                                @if (!$socialMediaExpiry['twitter'])
                                <a href="javascript:void(0)" class="custom-link relink-button"
                                    data-url="{{ route('auth.twitter') }}">
                                    Relink</a>
                                @endif
                                <button class="custom-link unlink_page"
                                    data-social-name="{{ $twitterPage->mediaPage->page_name}}"
                                    data-page-id="{{ $twitterPage->mediaPage->page_id}}"
                                    data-id="{{ $twitterPage->mediaPage->id }}"
                                    data-media-id="{{$twitterPage->media_id}}"
                                    id="addPage{{$twitterPage->mediaPage->id}}" data-isdelete="y"
                                    data-isused="n">Unlink</button>

                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="stack-page-link">
                            <center>
                                <h2>No pages</h2>
                            </center>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            {{-- Instagram --}}
            <div class="row stacking-post-block mt-2">
                <div class="col-sm-8 col-lg-7">
                    <div class="stacking-posts">
                        <div class="stacking-header">
                            <div class="d-flex align-items-center">
                                <div class="stack-post-icon">
                                    <img src="{{ asset('frontend/assets/media/images/instagram.svg') }}">
                                </div>
                                <div class="stack-post-count ml-4">
                                    <span># Channels:</span><span>{{$usedPageinsta ?? 0}}</span>/<span>{{$totalPageinsta
                                        ?? 0}}</span>
                                </div>
                            </div>
                            <a class="custom-link link-btn" data-social-name="Instagram" href="javascript:;"
                                data-total-page={{$totalPageinsta}} {{in_array(4,$social_media_detail)
                                ? "disabled=disabled" : "" }}>Link Instagram</a>
                        </div>
                        @if($getInstagramPage->isNotEmpty())
                        @foreach($getInstagramPage as $instagramPage)

                        <div class="stack-page-link stage-page-link-loop">
                            <h2 style="width: 320px !important;">{{$instagramPage->mediaPage->page_name}} </h2>
                            @php
                            $days =
                            \Carbon\Carbon::parse($instagramPage->mediaPage->socialMediaDetail->token_expiry)->diffInDays(now()->toDateString());
                            $isInstagramPageExpird = $instagramPage->mediaPage->socialMediaDetail->token_expiry <
                                \Carbon\Carbon::parse(now())->toDateString();
                                @endphp
                                <p
                                    class="{{ $days >= 10 && !$isInstagramPageExpird ? (($days <= 40 && $days >= 10) ? 'text-warning' : 'text-success') : 'text-danger' }}">
                                    Token Expiry :
                                    {{ $isInstagramPageExpird ? 'Expired' : ($days == 0 ? 'Today' : '(in '. $days .'
                                    Days)') }}
                                    <i
                                        class="{{ $days >= 10 && !$isInstagramPageExpird ? (($days <= 40 && $days >= 10) ? ' fas fa-exclamation-circle text-warning' : 'far fa-check-circle text-success') : 'fas fa-exclamation-circle text-danger' }}"></i>
                                </p>
                                <div class="social-media-list-action-buttons-main">

                                    @if ($isInstagramPageExpird || $days <= 10) <a href="javascript:void(0)"
                                        class="custom-link relink-button" data-url="{{ route('auth.instagram') }}">
                                        Relink</a>
                                        @endif
                                        <button class="custom-link unlink_page"
                                            data-social-name="{{ $instagramPage->mediaPage->page_name}}"
                                            data-page-id="{{ $instagramPage->mediaPage->page_id}}"
                                            data-id="{{ $instagramPage->mediaPage->id }}"
                                            data-media-id="{{$instagramPage->media_id}}" {{--
                                            data-id="{{ $instagramPage->id }}"
                                            data-media-id="{{$instagramPage->media_id}}" --}}
                                            id="addPage{{$instagramPage->mediaPage->id}}" data-isdelete="y"
                                            data-isused="n">Unlink</button>

                                </div>
                        </div>
                        @endforeach
                        @else
                        <div class="stack-page-link">
                            <center>
                                <h2>No pages</h2>
                            </center>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            {{-- Google My Business Field --}}
            <div class="row stacking-post-block mt-2">
                <div class="col-sm-8 col-lg-7">
                    <div class="stacking-posts">
                        <div class="stacking-header">
                            <div class="d-flex align-items-center">
                                <div class="stack-post-icon">
                                    <img src="{{ asset('frontend/assets/media/images/google-my-business.svg') }}">
                                </div>
                                <div class="stack-post-count ml-4">
                                    <span># Channels:</span><span>{{$usedPageGoogle ??
                                        0}}</span>/<span>{{$totalPageGoogle ?? 0}}</span>
                                </div>
                            </div>
                            <a class="custom-link link-btn" data-social-name="Google" href="javascript:;"
                                data-total-page={{$totalPageGoogle}} {{in_array(5,$social_media_detail)
                                ? "disabled=disabled" : "" }}>Link Google Account</a>
                        </div>
                        @if($getGooglePage->isNotEmpty())
                        @foreach($getGooglePage as $googlePage)
                        <div class="stack-page-link stage-page-link-loop">
                            <h2 style="width: 320px !important;">{{$googlePage->mediaPage->page_name}}</h2>
                            @php
                            $days =
                            \Carbon\Carbon::parse($googlePage->mediaPage->socialMediaDetail->token_expiry)->diffInDays(now()->toDateString());
                            $isGooglePageExpired = $googlePage->mediaPage->socialMediaDetail->token_expiry <
                                \Carbon\Carbon::parse(now())->toDateString();
                                @endphp
                                <p
                                    class="{{ $days >= 10 && !$isGooglePageExpired ? (($days <= 40 && $days >= 10) ? 'text-warning' : 'text-success') : 'text-danger' }}">
                                    Token Expiry :
                                    {{ $isGooglePageExpired ? 'Expired' : ($days == 0 ? 'Today' : '(in '. $days .'
                                    Days)') }}
                                    <i
                                        class="{{ $days >= 10 && !$isGooglePageExpired ? (($days <= 40 && $days >= 10) ? ' fas fa-exclamation-circle text-warning' : 'far fa-check-circle text-success') : 'fas fa-exclamation-circle text-danger' }}"></i>
                                </p>
                                <div class="social-media-list-action-buttons-main">
                                    @if ($isGooglePageExpired || $days <= 10 ) <a href="javascript:void(0)"
                                        class="custom-link relink-button" data-url="{{ route('auth.google') }}">
                                        Relink</a>
                                        @endif
                                        <button class="custom-link unlink_page"
                                            data-social-name="{{ $googlePage->mediaPage->page_name}}"
                                            data-page-id="{{ $googlePage->mediaPage->page_id}}"
                                            data-id="{{ $googlePage->mediaPage->id }}"
                                            data-media-id="{{$googlePage->media_id}}"
                                            id="addPage{{$googlePage->mediaPage->id}}" data-isdelete="y"
                                            data-isused="n">Unlink</button>
                                </div>
                        </div>
                        @endforeach
                        @else
                        <div class="stack-page-link">
                            <center>
                                <h2>No pages</h2>
                            </center>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            {{-- Google Analytics Field --}}
            <div class="row stacking-post-block mt-2">
                <div class="col-sm-8 col-lg-7">
                    <div class="stacking-posts">
                        <div class="stacking-header">
                            <div class="d-flex align-items-center">
                                <div class="stack-post-icon">
                                    <img src="{{ asset('frontend/assets/media/images/google-analytics-icon.svg') }}">
                                </div>
                                <div class="stack-post-count ml-4">
                                    <span># Channels:</span><span>{{$usedPageGoogleAnalytics ??
                                        0}}</span>/<span>{{$totalPageGoogleAnalytics ?? 0}}</span>
                                </div>
                            </div>
                            <a class="custom-link link-btn" data-social-name="Google_Analytics" href="javascript:;"
                                data-total-page="{{$totalPageGoogleAnalytics}}" {{in_array(6,$social_media_detail)
                                ? "disabled=disabled" : "" }}>Link GA4 Account</a>
                        </div>
                        @if($getGoogleAnalyticsPage->isNotEmpty())
                        @foreach($getGoogleAnalyticsPage as $googleAnalyticPage)
                        <div class="stack-page-link stage-page-link-loop">
                            <h2 style="width: 320px !important;">{{$googleAnalyticPage->mediaPage->page_name}}</h2>
                            @php
                            $days =
                            \Carbon\Carbon::parse($googleAnalyticPage->mediaPage->socialMediaDetail->token_expiry)->diffInDays(now()->toDateString());
                            $isGoogleAnalyticsPageExpired =
                            $googleAnalyticPage->mediaPage->socialMediaDetail->token_expiry <
                                \Carbon\Carbon::parse(now())->toDateString();
                                @endphp
                                <p
                                    class="{{ $days >= 10 && !$isGoogleAnalyticsPageExpired ? (($days <= 40 && $days >= 10) ? 'text-warning' : 'text-success') : 'text-danger' }}">
                                    Token Expiry :
                                    {{ $isGoogleAnalyticsPageExpired ? 'Expired' : ($days == 0 ? 'Today' : '(in '. $days
                                    .' Days)') }}

                                    <i
                                        class="{{ $days >= 10 && !$isGoogleAnalyticsPageExpired ? (($days <= 40 && $days >= 10) ? ' fas fa-exclamation-circle text-warning' : 'far fa-check-circle text-success') : 'fas fa-exclamation-circle text-danger' }}"></i>
                                </p>
                                <div class="social-media-list-action-buttons-main">

                                    @if ($isGoogleAnalyticsPageExpired || $days <= 10) <a href="javascript:void(0)"
                                        class="custom-link relink-button"
                                        data-url="{{ route('auth.google.analytics') }}">
                                        Relink</a>
                                        @endif
                                        <button class="custom-link unlink_page"
                                            data-social-name="{{ $googleAnalyticPage->mediaPage->page_name}}"
                                            data-page-id="{{ $googleAnalyticPage->mediaPage->page_id}}"
                                            data-id="{{ $googleAnalyticPage->mediaPage->id }}"
                                            data-media-id="{{$googleAnalyticPage->media_id}}"
                                            id="addPage{{$googleAnalyticPage->mediaPage->id}}" data-isdelete="y"
                                            data-isused="n">Unlink</button>
                                </div>
                        </div>
                        @endforeach
                        @else
                        <div class="stack-page-link">
                            <center>
                                <h2>No pages</h2>
                            </center>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            {{-- Google Ads Field --}}
            <div class="row stacking-post-block mt-2">
                <div class="col-sm-8 col-lg-7">
                    <div class="stacking-posts">
                        <div class="stacking-header">
                            <div class="d-flex align-items-center">
                                <div class="stack-post-icon">
                                    <img src="{{ asset('frontend/assets/media/images/google-ads-icon.svg') }}">
                                </div>
                                <div class="stack-post-count ml-4">
                                    <span># Channels:</span><span>{{$usedPageGoogleAds ??
                                        0}}</span>/<span>{{$totalPageGoogleAds ?? 0}}</span>
                                </div>
                            </div>
                            <a class="custom-link link-btn" data-social-name="google_ads" href="javascript:;"
                                data-total-page="{{$totalPageGoogleAds}}" {{in_array(7,$social_media_detail)
                                ? "disabled=disabled" : "" }}>Link Google Ads Account</a>
                        </div>
                        @if($getGoogleAdsPage->isNotEmpty())
                        @foreach($getGoogleAdsPage as $googleAdsPage)
                        <div class="stack-page-link stage-page-link-loop">
                            <h2 style="width: 320px !important;">{{$googleAdsPage->mediaPage->page_name}}</h2>
                            @php
                            $days =
                            \Carbon\Carbon::parse($googleAdsPage->mediaPage->socialMediaDetail->token_expiry)->diffInDays(now()->toDateString());
                            $isGoogleAdsPageExpired = $googleAdsPage->mediaPage->socialMediaDetail->token_expiry <
                                \Carbon\Carbon::parse(now())->toDateString();
                                @endphp
                                <p
                                    class="{{ $days >= 10 && !$isGoogleAdsPageExpired ? (($days <= 40 && $days >= 10) ? 'text-warning' : 'text-success') : 'text-danger' }}">
                                    Token Expiry :
                                    {{ $isGoogleAdsPageExpired ? 'Expired' : ($days == 0 ? 'Today' : '(in '. $days .'
                                    Days)') }}

                                    <i
                                        class="{{ $days >= 10 && !$isGoogleAdsPageExpired ? (($days <= 40 && $days >= 10) ? ' fas fa-exclamation-circle text-warning' : 'far fa-check-circle text-success') : 'fas fa-exclamation-circle text-danger' }}"></i>
                                </p>
                                <div class="social-media-list-action-buttons-main">
                                    @if ($isGoogleAdsPageExpired || $days <= 10) <a href="javascript:void(0)"
                                        class="custom-link relink-button" data-url="{{ route('auth.google.ads') }}">
                                        Relink</a>
                                        @endif
                                        <button class="custom-link unlink_page"
                                            data-social-name="{{ $googleAdsPage->mediaPage->page_name}}"
                                            data-page-id="{{ $googleAdsPage->mediaPage->page_id}}"
                                            data-id="{{ $googleAdsPage->mediaPage->id }}"
                                            data-media-id="{{$googleAdsPage->media_id}}"
                                            id="addPage{{$googleAdsPage->mediaPage->id}}" data-isdelete="y"
                                            data-isused="n">Unlink</button>
                                </div>
                        </div>
                        @endforeach
                        @else
                        <div class="stack-page-link">
                            <center>
                                <h2>No pages</h2>
                            </center>
                        </div>
                        @endif
                        <!-- <div class="stack-page-link">
                            <h2>Facebook Page Name 1</h2>
                            <button class="custom-link">link</button>
                        </div> -->

                    </div>
                </div>
            </div>
            <!-- for second page end  -->
            <!--end::Row-->

            <!--end::Dashboard-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->
<!-- Modal -->
<div class="modal fade list-media-modal soical-media-modal-main" id="listMediaPages" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <div class="stack-post-icon mr-4">
                        <img src="" id="image-media">
                    </div>
                    <h4 class="modal-title">Media Page List</h4>
                </div>
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row stacking-post-block">
                    <div class="col-12">
                        @if(count($getAllMediaPages) > 0)
                        @php $isalllinked=1; @endphp
                        @foreach($getAllMediaPages as $mediaPages)
                        @php $flag=0; @endphp
                        @foreach($linkedPage as $linked)
                        @if($linked->media_page_id == $mediaPages->id && $linked->is_deleted == 'n')
                        @php $flag=1; @endphp
                        @break
                        @endif
                        @endforeach
                        @if($flag == 0)
                        @php $isalllinked=0; @endphp
                        <div class="stacking-posts">
                            <div class="stacking-header">
                                <div class="stack-page-link">
                                    <!-- <div class="stack-post-icon">
                                                        <img src="{{$mediaPages->media->website_image_url}}">
                                                    </div> -->
                                    <h2>{{$mediaPages->page_name}}</h2>
                                    <button class="custom-link link_page" data-social-name="{{ $mediaPages->page_name}}"
                                        data-page-id="{{ $mediaPages->page_id}}" data-id="{{ $mediaPages->id }}"
                                        data-media-id="{{$mediaPages->media_id}}" id="addPage{{$mediaPages->id}}"
                                        data-isdelete="n" data-isused="y">Link</button>
                                </div>
                            </div>
                        </div>
                        @endif
                        @php $flag=0; @endphp
                        @endforeach
                        @endif
                        @if(count($getAllMediaPages) == 0)
                        <div>
                            <center>
                                <h2>No pages found.</h2>
                            </center>
                        </div>
                        @elseif($isalllinked == 1)
                        <div>
                            <center>
                                <h2>You have already linked all pages to your account.</h2>
                            </center>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="modal-done">Done</button>
            </div>
        </div>
    </div>
</div>

@endsection
@push('extra-js-scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $("#modal-done").on('click', function(){
            sessionStorage.removeItem("is_model_need_to_open");
            window.location.reload();
            window.location = "{{ route('social-media-list') }}";
        })
        var is_relink = false;
        var sessionData = sessionStorage.getItem('is_model_need_to_open');
        // console.log('sessionData',sessionData);
        // console.log('is_relink',is_relink);
        if(sessionData != null){

            is_relink = true;
            sessionStorage.removeItem("is_model_need_to_open");
        }
        var showModal = "{{ $showModal }}";
        var mediaName = "{{ $mediaType }}";
        var socialMediaName = '';
        var mediaPageCount = "{{ $getMediaPagesCount }}";
        // console.log(showModal,'showmodal',is_relink,'is_relink_after');
        // if(showModal && is_relink == false){
        if(showModal && is_relink == false){
            $('#listMediaPages').modal('show');
            let url = "{{ asset('frontend/assets/media/images/') }}";
            $('.modal-title').text(mediaName +' page list');
            if(mediaName == 'Google Ads') mediaName = 'google-ads-icon'
            if(mediaName == 'Google My Business') mediaName = 'google-my-business'
            if(mediaName == 'Facebook Ads') mediaName = 'facebook'
            mediaName = mediaName.toLowerCase();
            $('#image-media').attr('src',url+'/'+mediaName+'.svg');
        }
        $('#listMediaPages').on('hidden.bs.modal', function () {
            sessionStorage.removeItem("is_model_need_to_open");
            window.location = "{{ route('social-media-list') }}";
        });
       $mediaPage_id = [];
       $(document).on('click','.link-btn',function(){
            var is_subscribe = "{{ $user->is_subscribe ?? "" }}";
            var total_page = $(this).data('total-page');
            // console.log(total_page);
            if(is_subscribe == 'n'){
                toastr.error('Please Purchase Subscription First!');
            }else if(total_page == 0){
                toastr.error('Please Purchase Pages First!');
            }else{
                sessionStorage.removeItem("is_model_need_to_open");
                // console.log('out');
                if($(this).is('[disabled=disabled]')){
                    // console.log('in1');
                    socialMediaName = $(this).attr('data-social-name');
                    // window.location = "{{ route('social-media-list') }}"+"?"+"socialMedia="+socialMediaName;
                    // showMessage(200,"Already Link with social media");
                    window.location = "auth/" + socialMediaName.toLowerCase();
                }else if($(this).attr('data-social-name') == 'X(Twitter)'){
                    window.location = "{{ route('auth.twitter') }}";
                }else if($(this).attr('data-social-name') == 'Facebook'){
                    window.location = "{{ route('auth.facebook') }}";
                }else if($(this).attr('data-social-name') == 'Google'){
                    window.location = "{{ route('auth.google') }}";
                }else if($(this).attr('data-social-name') == 'Google_Analytics'){
                    window.location = "{{ route('auth.google.analytics') }}";
                }else if($(this).attr('data-social-name') == 'google_ads'){
                    window.location = "{{ route('auth.google.ads') }}";
                }else if($(this).attr('data-social-name') == 'Instagram'){
                    window.location = "{{ route('auth.instagram') }}";
                }else if($(this).attr('data-social-name') == 'Linkedin'){
                    window.location = "{{ route('auth.linkedin') }}";
                }else if($(this).attr('data-social-name') == 'Facebook_Ads'){
                    window.location = "{{ route('auth.facebook_ads') }}";
                }
            }
       });

        $(".link_page").on("click", function(){
            // console.log();
            $is_expiry = $("#expired").val();
            mediaPageCount = parseInt(mediaPageCount) - 1;
            if(mediaPageCount == 0){
                $(".link_page").prop("disabled", true);
            }
            // if($is_expiry == 'n'){
                $page_id = $(this).attr("data-page-id");
                $id = $(this).attr("data-id");
                $media_id = $(this).attr("data-media-id");
                // console.log($media_id);
                $isDelete = $(this).attr("data-isdelete");
                $isUsed = $(this).attr("data-isused");
                $(this).off('click');
                // $(this).prop("disabled", true);
                $(this).html("<i class='fa fa-check' aria-hidden='true'></i>");
                $mediaPage_id.push($id);
                $("#mediapage_id").val($mediaPage_id);
                $("#media_id").val($media_id);
                $.ajax({
                    url: "{{route('check-pages')}}",
                    type: "GET",
                    data: {mediapage_id : $id,page_id: $page_id, media_id: $media_id, is_delete: $isDelete, is_used: $isUsed},
                    dataType  : 'json',
                    success: function (response) {
                        // window.location = "{{ route('social-media-list') }}";
                        // console.log(response['data']);
                        // if(response['data'] === '' || response['data'] === 'null'){


                        // }else{

                        //    toastr.success('Page Added Successfully as your subscription is going on!');
                        // }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                    //    console.log(textStatus, errorThrown);
                    }
                });
            // }
            // else{
            //     toastr.error('Please Purchase Subscription First');
            // }


        });

        $(".unlink_page").on("click", function(){
            var pageCount = $(this).parents(".stacking-posts").find('.stack-page-link').length;
            $(this).parent().closest('.stack-page-link').remove();
            $(this).insertAfter('<span>No Pages</span>')
            $is_expiry = $("#expired").val();
            // if($is_expiry == 'n'){
                $page_id = $(this).attr("data-page-id");
                $id = $(this).attr("data-id");
                $media_id = $(this).attr("data-media-id");
                $isDelete = $(this).attr("data-isdelete");
                $isUsed = $(this).attr("data-isused");
                // $(this).off('click');
                // $(this).prop("disabled", true);
                $mediaPage_id.push($id);
                $("#mediapage_id").val($mediaPage_id);
                $("#media_id").val($media_id);
                $.ajax({
                    url: "{{route('check-pages')}}",
                    type: "GET",
                    data: {mediapage_id : $id,page_id: $page_id, media_id: $media_id, is_delete: $isDelete, is_used: $isUsed},
                    dataType  : 'json',
                    success: function (response) {
                        // console.log(response);
                        if(response.status == 200){
                            if( pageCount == 1)  window.location = "{{ route('social-media-list') }}";
                        }
                        // console.log(response['data']);
                        // if(response['data'] === '' || response['data'] === 'null'){


                        // }else{

                        //    toastr.success('Page Added Successfully as your subscription is going on!');
                        // }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                    //    console.log(textStatus, errorThrown);
                    }
                });
            // }
            // else{
            //     toastr.error('Please Purchase Subscription First');
            // }


        });

        if(mediaPageCount == 0){
          $(".link_page").prop("disabled", true);
        }

        $(document).on('click','.relink-button',function(e){
            e.preventDefault();
            var url = $(this).data('url');

            sessionStorage.setItem('is_model_need_to_open', false);
            window.location.href = url;
        });

    });


</script>


@endpush
