@extends('frontend.layouts.app')

@section('content')

@php
$request = request()->query();
$media_type = isset($request['type']) ? $request['type'] : null;
$media_id = isset($request['type']) && $request['type'] == 'facebook' ? 1 : (isset($request['type']) && $request['type']
== 'linkedin' ? 2 : (isset($request['type']) && $request['type'] == 'twitter' ? 3 : (isset($request['type']) &&
$request['type'] == 'instagram' ? 4 : ( isset($request['type']) && $request['type'] == 'google_business' ? 5 : (
isset($request['type']) && $request['type'] == 'google_analytics' ? 6 : ( isset($request['type']) && $request['type'] ==
'google_ads' ? 7 : ( isset($request['type']) && $request['type'] == 'facebook_ads' ? 8 : null)))))));

$media_page_ids = isset($request['page_id']) ? $request['page_id'] == 'all' ? 'all' : explode(',',$request['page_id']) :
'';
@endphp

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader pt-6 pt-lg-12 subheader-transparent" id="kt_subheader">
        <div class="container">
            <div class="d-flex align-items-end justify-content-between flex-wrap flex-sm-nowrap">
            </div>
            @php
            $medias = $media->pluck('name')->toArray();
            @endphp
            <div class="custom-filter-block">
                <div class="custom-filter-block-inner ">

                    <div class="head-select pr-3">
                        <select class="form-control media_id" id="media_id" name="media_id"
                            data-error-container="#media_id-error">
                            <option value="">Select Media</option>
                            <optgroup label="Socials">
                                @foreach ($media as $value)
                                {{-- Value id 8 is for the Facebook Ads --}}
                                @if($value->type !== 'social') @break; @endif
                                <option value="{{ $value->id }}" {{ $media_id==$value->id ? 'selected' : ''
                                    }}>{{$value->name }}</option>
                                @endforeach
                            </optgroup>
                            @if (in_array('Google Analytics 4',$medias))
                            <optgroup label="Analytics">
                                @if (in_array('Google Analytics 4',$medias))
                                <option value="6" {{ $media_type=='google_analytics' ? 'selected' : '' }}>Google
                                    Analytics 4</option>
                                @endif
                            </optgroup>
                            @endif
                            @if (in_array('Facebook Ads',$medias) || in_array('Google Ads',$medias))
                            <optgroup label="Paid Ads">
                                @if (in_array('Google Ads',$medias))
                                <option value="7" {{ $media_type=='google_ads' ? 'selected' : '' }}>Google Ads</option>
                                @endif
                                @if (in_array('Facebook Ads',$medias))
                                <option value="8" {{ $media_type=='facebook_ads' ? 'selected' : '' }}>Facebook Ads
                                </option>
                                @endif
                            </optgroup>
                            @endif
                        </select>
                        @if($errors->has('type'))
                        <span id="media_id-error" class="analytic_filter_error">{{ $errors->first('type') }}</span>
                        @else
                        <span id="media_id-error" class="analytic_filter_error"></span>
                        @endif
                    </div>

                    <div class="head-select pr-3">
                        <select class="form-control media_page_id" id="media_page_id" name="media_page_id[]"
                            data-error-container="#media_page_id-error">
                            <option value="">Select Social Media Pages
                            </option>
                            {{-- @foreach ($media as $value)
                            <optgroup label="{{ $value->name }}">
                                <option value="all" data-media-id="{{ $value->id ?? ''}}">All</option>
                                @foreach ($value->mediaPages as $page)
                                <option {{ (isset($request['page_id']) && $request['page_id']==$page->id) ? 'selected' :
                                    '' }} value="{{ $page->id }}" data-media-id="{{ $value->id ?? '' }}">
                                    {{$page->page_name }}</option>
                                @endforeach
                            </optgroup>
                            @endforeach --}}
                        </select>

                        @if($errors->has('page_id'))
                        <span id="media_page_id-error" class="analytic_filter_error">{{ $errors->first('page_id')
                            }}</span>
                        @else
                        <span id="media_page_id-error" class="analytic_filter_error"></span>
                        @endif
                    </div>

                    <div class="custom-filter-block-wrap filter_date_main">
                        <div class="custom-filter-block-wrap flex-column">
                            <div class="d-flex flex-xl-row flex-lg-row flex-md-row flex-column"
                                style="gap: 16px !important;">
                                <div class="datefield">
                                    <div class="form-group mb-0">
                                        <label class="col-form-label mr-4">Start Date</label>
                                        <div class="w-100">
                                            <input type="text" class="form-control" name="start_date"
                                                placeholder="Select date"
                                                value="{{ isset($request['start_date']) ?  $request['start_date'] : \Carbon\Carbon::now()->startOfMonth()->format('m/d/Y') }}"
                                                id="start_date" data-error-container="#start-date-error"
                                                autocomplete="off" />
                                            @if($errors->has('start_date'))
                                            <span id="start_date-error" class="analytic_filter_error">{{
                                                $errors->first('start_date') }}</span>
                                            @else
                                            <span id="start_date-error" class="analytic_filter_error"></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="datefield">
                                    <div class="form-group mb-0">
                                        <label class="col-form-label mr-4">End Date</label>
                                        @php
                                        // $date = \Carbon\Carbon::now()->format('m/d/Y') ==
                                        // \Carbon\Carbon::now()->startOfMonth()->format('m/d/Y') ?
                                        // \Carbon\Carbon::now()->endOfMonth()->format('m/d/Y') :
                                        // \Carbon\Carbon::now()->format('m/d/Y')

                                        $date = \Carbon\Carbon::now()->endOfMonth()->format('m/d/Y');
                                        @endphp
                                        <div class="">
                                            <input type=" text" class="form-control" name="end_date"
                                                placeholder="Select date"
                                                value="{{ isset($request['end_date']) ?  $request['end_date'] : $date }}"
                                                id="end_date" data-error-container="#start-date-error"
                                                autocomplete="off" />
                                            @if($errors->has('end_date'))
                                            <span id="end_date-error" class="analytic_filter_error">{{
                                                $errors->first('end_date')
                                                }}</span>
                                            @else
                                            <span id="end_date-error" class="analytic_filter_error"></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="w-100">
                                <span class="text-danger" id="date_error" style="display: none;"></span>
                            </div>
                        </div>
                        <button class="btn font-weight-bold px-6 common-btn filter-btn"
                            id="applyFilter">Apply
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Subheader-->
    @if(isset($request['type']))
        {{-- Twitter --}}
        @if(isset($request['type']) && $request['type'] == 'twitter')
            @include('frontend.pages.analytic.twiter-insight')
            {{-- @include('frontend.pages.analytic.twitterfeed') --}}
        @endif

        {{-- Linkedin --}}
        @if(isset($request['type']) && $request['type'] == 'linkedin' || !isset($request['type']))
            <div class="container">
                <div class="subheader pt-6 pt-lg-12 subheader-transparent" id="kt_subheader">
                    <ul class="nav nav-pills" id="myTab" role="tablist">
                        <li class="nav-item" id="linkedinAnalyticTab">
                            <a class="nav-link p-md-0" id="home-tab-4" data-toggle="tab" href="#linkedinAnalytic">
                                <span class="post-link btn font-size-h3
                                                                            font-weight-bold">LinkedIn Analytics</span>
                            </a>
                        </li>
                        <li class="nav-item" id="linkedinPostsTab">
                            <a class="nav-link p-md-0" id="home-tab-4" data-toggle="tab" href="#linkedinPostList">
                                <span class="post-link btn font-size-h3
                                                                            font-weight-bold">LinkedIn Feed</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="linkedinAnalytic" role="tabpanel" aria-labelledby="home-tab-4">
                    @include('frontend.pages.analytic.linkedin-engagment')
                </div>
                <div class="tab-pane fade" id="linkedinPostList" role="tabpanel" aria-labelledby="profile-tab-4">
                    @include('frontend.pages.analytic.linkedin-post')
                </div>
            </div>
        @endif

        {{-- Facebook --}}
        @if(isset($request['type']) && $request['type'] == 'facebook' || !isset($request['type']))
            <div class="container">
                <div class="subheader pt-6 pt-lg-12 subheader-transparent" id="kt_subheader">
                    <ul class="nav nav-pills" id="myTab" role="tablist">
                        <li class="nav-item" id="facebookAnalyticTab">
                            <a class="nav-link p-md-0" id="home-tab-4" data-toggle="tab" href="#facebookAnalytic">
                                <span class="post-link btn font-size-h3
                                                                                    font-weight-bold">Facebook Analytics</span>
                            </a>
                        </li>
                        <li class="nav-item" id="facebookPostsTab">
                            <a class="nav-link p-md-0" id="home-tab-4" data-toggle="tab" href="#facebookPostList">
                                <span class="post-link btn font-size-h3
                                                                                    font-weight-bold">Facebook Posts</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="facebookAnalytic" role="tabpanel" aria-labelledby="home-tab-4">
                    @include('frontend.pages.analytic.facebook-likes')
                    @include('frontend.pages.analytic.facebook-engagement')
                    @include('frontend.pages.analytic.facebook-reach')
                </div>
                <div class="tab-pane fade" id="facebookPostList" role="tabpanel" aria-labelledby="profile-tab-4">
                    @include('frontend.pages.analytic.facebook_post')
                </div>
            </div>
        @endif
        {{-- Instagram --}}
        @if(isset($request['type']) && $request['type'] == 'instagram' || !isset($request['type']))
            <div class="container">
                <div class="subheader pt-6 pt-lg-12 subheader-transparent" id="instagram_analytics_tabs_container">
                    <ul class="nav nav-pills" id="instagramAnalyticsTabs" role="tablist">
                        <li class="nav-item" id="instagramDiscoveryTab">
                            <a class="nav-link main-nav-insta p-md-0 active" id="home-tab-4" data-toggle="tab"
                                href="#instagramDiscovery">
                                <span class="post-link active btn font-size-h3 font-weight-bold">Instagram -
                                    Analytics</span>
                            </a>
                        </li>
                        <li class="nav-item" id="instagramPostsTab">
                            <a class="nav-link main-nav-insta p-md-0" id="home-tab-4" data-toggle="tab"
                                href="#instagramPostsFeed">
                                <span class="post-link btn font-size-h3 font-weight-bold">Instagram - Posts</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="instagramDiscovery" role="tabpanel" aria-labelledby="home-tab-4">
                    @include('frontend.pages.analytic.instagram.instagram-discovery')
                    @include('frontend.pages.analytic.instagram.instagram-interaction')
                    @include('frontend.pages.analytic.instagram.instagram-audiance')
                </div>
                <div class="tab-pane fade" id="instagramPostsFeed" role="tabpanel" aria-labelledby="profile-tab-4">
                    @include('frontend.pages.analytic.instagram.instagram-posts')
                </div>
            </div>
        @endif
        {{-- Facebook Ads --}}
        @if(isset($request['type']) && $request['type'] == 'facebook_ads' || !isset($request['type']))
            <div class="container">
                <div class="subheader pt-6 pt-lg-12 subheader-transparent" id="facebook_ads_tabs_container">
                    <ul class="nav nav-pills" id="facebookAdsTabs" role="tablist">
                        <li class="nav-item" id="facebookAdsCampaignTab">
                            <a class="main-nav-fbAds nav-link p-md-0 " id="home-tab-4" data-toggle="tab"
                                href="#facebookAdsCampaignContent">
                                <span class="post-link btn font-size-h3 font-weight-bold active">Facebook Ads -
                                    Campaign</span>
                            </a>
                        </li>
                        <li class="nav-item" id="facebookAdsAdsetTab">
                            <a class="main-nav-fbAds nav-link p-md-0 " id="home-tab-4" data-toggle="tab"
                                href="#facebookAdsAdsetContent">
                                <span class="post-link btn font-size-h3 font-weight-bold">Facebook Ads - AdSet</span>
                            </a>
                        </li>
                        <li class="nav-item" id="facebookAdsDemographicsTab">
                            <a class="main-nav-fbAds nav-link p-md-0" id="home-tab-4" data-toggle="tab"
                                href="#facebookAdsDemographicsContent">
                                <span class="post-link btn font-size-h3 font-weight-bold">Facebook Ads - Demographics</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content" id="facebookAdsTabsContent">
                <div class="tab-pane fade show active" id="facebookAdsCampaignContent" role="tabpanel"
                    aria-labelledby="home-tab-4">
                    @include('frontend.pages.analytic.facebook-ads.facebook-ads_campaign')
                </div>
                <div class="tab-pane fade" id="facebookAdsAdsetContent" role="tabpanel" aria-labelledby="home-tab-4">
                    @include('frontend.pages.analytic.facebook-ads.facebook-ads_adset')
                </div>
                <div class="tab-pane fade" id="facebookAdsDemographicsContent" role="tabpanel" aria-labelledby="profile-tab-4">
                    @include('frontend.pages.analytic.facebook-ads.facebook-ads_demographics')
                </div>
            </div>
        @endif
        {{-- Google Analytics Dummy data --}}
        @if(isset($request['type']) && $request['type'] == 'google_analytics' || !isset($request['type']))
            <div class="container">
                <div class="subheader pt-6 pt-lg-12 subheader-transparent" id="ga_headers_tabs">
                    <ul class="nav nav-pills" id="gaAcquisitionTab" role="tablist">
                        <li class="nav-item">
                            <a class="main-nav-ga nav-link p-md-0 " data-toggle="tab" data-channel="gaAcquisition"
                                href="#gaAcquisition">
                                <span class="post-link btn font-size-h3 font-weight-bold active">Acquisition</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="main-nav-ga nav-link p-md-0 " data-toggle="tab" data-channel="gaAudience"
                                href="#gaAudience">
                                <span class="post-link btn font-size-h3 font-weight-bold">Audience</span>
                            </a>
                        </li>
                    </ul>
                </div>
                {{-- <div class="subheader pt-6 pt-lg-12 subheader-transparent" id="kt_subheader_acquisition">
                    <ul class="nav nav-pills" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-ga nav-link p-md-0 active" data-toggle="tab" data-channel="gaAcquisitionAll"
                                href="#gaAcquisitionAll">
                                <span class="post-link btn font-size-h3 font-weight-bold">All</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-ga nav-link p-md-0" data-toggle="tab" data-channel="gaAcquisitionOrganicSearch"
                                href="#gaAcquisitionOrganicSearch">
                                <span class="post-link btn font-size-h3 font-weight-bold">Organic Search</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-ga nav-link p-md-0" data-toggle="tab" data-channel="gaAcquisitionPaidSearch"
                                href="#gaAcquisitionPaidSearch">
                                <span class="post-link btn font-size-h3 font-weight-bold">Paid Search</span>
                            </a>
                        </li>
                    </ul>
                </div> --}}
            </div>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="gaAcquisition" role="tabpanel" aria-labelledby="home-tab-4">
                    @include('frontend.pages.analytic.google_analytics.acquisition')
                </div>
                <div class="tab-pane fade show " id="gaAudience" role="tabpanel" aria-labelledby="home-tab-4">
                    @include('frontend.pages.analytic.google_analytics.audiance')
                </div>
            </div>
            <div class="container">
                {{-- <div class="subheader pt-6 pt-lg-12 subheader-transparent">
                    <ul class="nav nav-pills" role="tablist">
                        <li class="nav-item">
                            <a class="main-nav-ga nav-link p-md-0 active" data-toggle="tab" data-channel="gaAudience">
                                <span class="post-link btn font-size-h3 font-weight-bold">Audience</span>
                            </a>
                        </li>
                    </ul>
                </div> --}}
                {{-- <div class="subheader pt-6 pt-lg-12 subheader-transparent" id="kt_subheader">
                    <ul class="nav nav-pills" id="myTab" role="tablist">
                        <li class="nav-item" id="gaLocation">
                            <a class="nav-ga-data nav-link p-md-0 active" id="home-tab-4" data-toggle="tab"
                                data-channel="googleAnalyticsAudienceLocation" href="#googleAnalyticsAudienceLocation">
                                <span class="post-link btn font-size-h3 font-weight-bold">Location</span>
                            </a>
                        </li>
                        <li class="nav-item" id="gaLanguage">
                            <a class="nav-ga-data nav-link p-md-0" data-element="language" id="home-tab-4" data-toggle="tab"
                                href="#googleAnalyticsAudienceLanguage">
                                <span class="post-link btn font-size-h3 font-weight-bold">Language</span>
                            </a>
                        </li>
                        <li class="nav-item" id="gaAge">
                            <a class="nav-ga-data nav-link p-md-0" data-element="age" id="home-tab-4" data-toggle="tab"
                                href="#googleAnalyticsAudienceAge">
                                <span class="post-link btn font-size-h3 font-weight-bold">Age</span>
                            </a>
                        </li>
                        <li class="nav-item" id="gaGender">
                            <a class="nav-ga-data nav-link p-md-0" data-element="gender" id="home-tab-4" data-toggle="tab"
                                href="#googleAnalyticsAudienceGender">
                                <span class="post-link btn font-size-h3 font-weight-bold">Gender</span>
                            </a>
                        </li>
                        <li class="nav-item" id="gaDevice">
                            <a class="nav-ga-data nav-link p-md-0" data-element="device" id="home-tab-4" data-toggle="tab"
                                href="#googleAnalyticsAudienceDevice">
                                <span class="post-link btn font-size-h3 font-weight-bold">Device</span>
                            </a>
                        </li>
                        <li class="nav-item" id="gaBrowser">
                            <a class="nav-ga-data nav-link p-md-0" data-element="browser" id="home-tab-4" data-toggle="tab"
                                href="#googleAnalyticsAudienceBrowser">
                                <span class="post-link btn font-size-h3 font-weight-bold">Browser</span>
                            </a>
                        </li>
                    </ul>
                </div> --}}
            </div>
        @endif
        {{-- Google Business --}}
        @if(isset($request['type']) && $request['type'] == 'google_business' || !isset($request['type']))
            <div class="container">
                <div class="subheader pt-6 pt-lg-12 subheader-transparent" id="googleBusinessHeadersTabs">
                    <ul class="nav nav-pills" id="googleBusinessTabs" role="tablist">
                        <li class="nav-item" id="googleBusinessPerformanceTab">
                            <a class="nav-link p-md-0" id="home-tab-4" data-toggle="tab" href="#googleBusinessPerformance">
                                <span class="post-link btn font-size-h3
                                                                                    font-weight-bold active">GMB -
                                    Performance</span>
                            </a>
                        </li>
                        <li class="nav-item" id="googleBusinessReviewTab">
                            <a class="nav-link p-md-0" id="home-tab-4" data-toggle="tab" href="#googleBusinessReview">
                                <span class="post-link btn font-size-h3
                                                                                    font-weight-bold">GMB - Review</span>
                            </a>
                        </li>
                        <li class="nav-item" id="googleBusinessPostsTab">
                            <a class="nav-link p-md-0" id="home-tab-4" data-toggle="tab" href="#googleBusinessPosts">
                                <span class="post-link btn font-size-h3
                                                                                    font-weight-bold">GMB - Posts</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="googleBusinessPerformance" role="tabpanel"
                    aria-labelledby="home-tab-4">
                    @include('frontend.pages.analytic.google_business.googe_business_performence')
                </div>
                <div class="tab-pane fade" id="googleBusinessReview" role="tabpanel" aria-labelledby="profile-tab-4">
                    @include('frontend.pages.analytic.google_business.google_business_review')
                </div>
                <div class="tab-pane fade" id="googleBusinessPosts" role="tabpanel" aria-labelledby="profile-tab-4">
                    @include('frontend.pages.analytic.google_business.google_business_posts')
                </div>
            </div>
        @endif
        {{-- Google Ads --}}
        @if(isset($request['type']) && $request['type'] == 'google_ads' || !isset($request['type']))
            @include('frontend.pages.analytic.google_ads.google_ads_reporting')
        @endif
    @endif
</div>

@endsection

@push('extra-js-scripts')
<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
<script src="{{ asset('admin/plugins/chart/apexcharts.js') }}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"
    integrity="sha512-CryKbMe7sjSCDPl18jtJI5DR5jtkUWxPXWaLCst6QjH8wxDexfRJic2WRmRXmstr2Y8SxDDWuBO6CQC6IE4KTA=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script type="text/javascript">
    $(document).ready(function() {

        $("#ga_headers_tabs").on("click", '.main-nav-ga' ,function(){

            $("#gaAcquisitionTab").find("li a span.active").removeClass("active");
            $("#gaAcquisitionTab").find("li a.active").removeClass("active");

            $(this).find(".post-link").addClass("active");
            $(this).find("a").addClass("active");


            $("#kt_subheader_audiance").find("li a span.active").removeClass("active");
            $("#kt_subheader_audiance").find("li a.active").removeClass("active");

            $("#kt_subheader_audiance").find(".nav li").first().find(".post-link").addClass("active");
            $("#kt_subheader_audiance").find(".nav li").first().find("a").addClass("active");

            if($(this).data('channel') == 'gaAudience'){
                $(".ga_audianceDataTabs").children("div.active").toggleClass("show").toggleClass("active");
                $(".ga_audianceDataTabs").find(".tab-pane").first().addClass("show").addClass('active');
                // initialLoadLocationData();
                // loadDataTable('country');
            }
        });

        $('#facebook_ads_tabs_container').on('click','.main-nav-fbAds',function(){

            $("#facebookAdsTabs").find("li a span.active").removeClass("active");
            $("#facebookAdsTabs").find("li a.active").removeClass("active");

            $(this).find(".post-link").addClass("active");
            $(this).find("a").removeClass("active");
        });
        $('#instagram_analytics_tabs_container').on('click','.main-nav-insta',function(){

            $("#instagramAnalyticsTabs").find("li a span.active").removeClass("active");
            $("#instagramAnalyticsTabs").find("li a.active").removeClass("active");

            $(this).find(".post-link").addClass("active");
            $(this).find("a").removeClass("active");
        });
        $('#googleBusinessHeadersTabs').on('click','.nav-link',function(){

            $("#googleBusinessTabs").find("li a span.active").removeClass("active");
            $("#googleBusinessTabs").find("li a.active").removeClass("active");

            $(this).find(".post-link").addClass("active");
            $(this).find("a").removeClass("active");
        });

        $('#googleAdsHeadersTabsContainer').on('click','.main-nav-googleAds',function(){
            if($("#googleAdsTabs").find("li a span.active").parent().closest('.nav-item').attr('id') != $(this).parent().attr('id')){
                $("#googleAdsTabs").find("li a span.active").removeClass("active");
                $("#googleAdsTabs").find("li a.active").removeClass("active");

                $(this).find(".post-link").addClass("active");
                $(this).find("a").removeClass("active");
            }
            // console.log('out');
        });

        removeOverlay();
        $('#media_page_id').selectpicker();
        $("#media_id").selectpicker();


        @if($media_id)
            $('#media_id').trigger('change');
        @endif
    })
    $(document).on('click','#campaignName',function(){

        $("#facebookAdsTabs").find("li a span.active").removeClass("active");
        $("#facebookAdsTabs").find("li a.active").removeClass("active");

        $("#facebookAdsTabsContent").find("div.active").removeClass("show active");

        $('#facebookAdsAdsetTab').find(".post-link").addClass("active");
        $('#facebookAdsAdsetTab').find("a").removeClass("active");

        $("#facebookAdsAdsetContent").addClass("show active");

        // $("#facebookAdsAdsetContent").attr('data-campaign-id',$(this).data('campaign-id'));

    });
    $(function(){

        $("#applyFilter").click(function(){

            var checkDateIsInRangeOrNot = checkIfMedisIsInstagramAndDateRangeIsOnly30Days();
            if(!checkDateIsInRangeOrNot){
                $('#date_error').css('display', '');
                $('#date_error').text('Instagram only provides the data of 30 days time Period. Please select Date Range within 30 days.');
            }else{
                let media_id = $("#media_id").val()
                let media_page_id= $("#media_page_id").val();

                // media_id
                social_media_type = "facebook";

                media_id = (media_id == 1 ? social_media_type : (media_id == 2 ? 'linkedin' : (media_id == 3 ? 'twitter' : (media_id == 4 ? 'instagram' : (media_id == 5 ?'google_business' : (media_id == 6 ? 'google_analytics': (media_id==7 ? 'google_ads' : (media_id == 8 ? 'facebook_ads' : null))))))))

                if(media_page_id.length > 0 && media_id){
                    addOverlay();
                    window.location.href = `{{ route('analytics.filter-analytic') }}?type=${media_id}&page_id=${media_page_id}&start_date=${$("#start_date").val()}&end_date=${$("#end_date").val()}`;
                }else{
                    $("#media_id-error").html('')
                    $("#media_page_id-error").html('')
                    if(!media_id )
                        $("#media_id-error").html('Please select social media.')

                    if(media_page_id.length == 0 )
                        $("#media_page_id-error").html('Please select any social media page.')
                }
            }
        });
        // var end_of_the_month = ;

        $('#start_date').datepicker({
            todayHighlight: true,
            startDate: '01/01/2000',
            endDate : "{{ \Carbon\Carbon::now()->endOfMonth()->format('m/d/Y') }}",
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>',
            },
        }).on('change', function() {
            var isDateIsCorrect = checkIfMedisIsInstagramAndDateRangeIsOnly30Days();
            if(!isDateIsCorrect){
                $('#date_error').css('display', '');
                $('#date_error').text('Instagram only provides the data of 30 days time Period. Please select Date Range within 30 days.');
            }else{
                $('#date_error').text('');
                $('#date_error').css('display', 'none');
            }
            var start_date = $(this).val();
            var end_date = $('#end_date').val();
            var start_date_year = new Date(start_date).getFullYear();
            var end_date_year = new Date(end_date).getFullYear();
            if (start_date > end_date && start_date_year == end_date_year) {
                $('#date_error').css('display', 'block');
                $('#date_error').text('Start date should not be after the end date');
                $('#date_error').addClass('invalid-feedback');
                $("#applyFilter").prop("disabled",true);
                return false;
            } else {
                $('#date_error').removeClass('invalid-feedback');
                $("#applyFilter").prop("disabled",
                    false);
                $('#date_error').html('');
                return true;
            }

        });
        $('#end_date').datepicker({
            todayHighlight: true,
            startDate: '01/01/2000',
            endDate : "{{ \Carbon\Carbon::now()->endOfMonth()->format('m/d/Y') }}",
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>',
            },
        }).on('change', function() {
            var isDateIsCorrect = checkIfMedisIsInstagramAndDateRangeIsOnly30Days();
            if(!isDateIsCorrect){
                $('#date_error').css('display', '');
                $('#date_error').text('Instagram only provides the data of 30 days time Period. Please select Date Range within 30 days.');
            }else{
                $('#date_error').text('');
                $('#date_error').css('display', 'none');
            }
            var start_date = $('#start_date').val();
            var end_date = $(this).val();
            var start_date_year = new Date(start_date).getFullYear();
            var end_date_year = new Date(end_date).getFullYear();
            if (start_date > end_date && start_date_year == end_date_year) {
                $('#date_error').css('display', 'block');
                $('#date_error').text('Start date should not be after the end date');
                $('#date_error').addClass('invalid-feedback');
                $("#applyFilter").prop("disabled",true);
                return false;
            } else {
                $('#date_error').removeClass('invalid-feedback');
                $("#applyFilter").prop("disabled",false);
                $('#date_error').html('');
                return true;
            }

        });

        // $("#filterfrm").validate({
		// 	rules: {
		// 		start_date: "required",
		// 		end_date: "required"
		// 	},
		// 	messages: {
		// 		start_date: "Please enter start date",
		// 		end_date: "Please enter end date"
		// 	},
        //     errorClass: 'invalid-feedback',
        //     errorElement: 'span',
        //     highlight: function (element) {
        //         $(element).addClass('is-invalid');
        //         $(element).siblings('label').addClass('text-danger'); // For Label
        //     },
        //     unhighlight: function (element) {
        //         $(element).removeClass('is-invalid');
        //         $(element).siblings('label').removeClass('text-danger'); // For Label
        //     },
        //     errorPlacement: function (error, element) {
        //         error.insertAfter(element);
        //     }
		// });
    });

    function checkIfMedisIsInstagramAndDateRangeIsOnly30Days()
    {
        let media_id = $("#media_id").val();
        let start_date = $("#start_date").val();
        let end_date = $("#end_date").val();
        // Get difference between start date and end date in days using moment js
        let date_diff = moment(new Date(end_date)).diff(moment(new Date(start_date)), 'days') + 1;

        if(media_id == 4 && date_diff > 30) return true;
        return true;
    }


    $("#media_id").change(function(){
        let media_id = $(this).val();
        $('.datefield').show();
        if(media_id == 3){
            $('.datefield').hide();
        }
        var media_pages_ids = '{!! json_encode($media_page_ids) !!}';

        var url = "{{ route('analytics.get-media-page') }}";
        $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            async:false,
            beforeSend: addOverlay,
            data: {
                _token: '{{ csrf_token() }}',
                media_id : media_id
            },
            success: function(response) {
                let opt
                if(response.length == 0){
                    opt = `<option value="" disabled>Please Link Social Medai Page First.</option>`;
                }else{
                    opt = `<option value="" >Select Media Pages</option>`;
                    response.map(pages => {
                        if(media_pages_ids !== 'all'){
                            selectedOrNot = media_pages_ids.includes(pages.id)
                        }
                        opt+= `<option value="${pages.id}" ${selectedOrNot ? 'selected' : ''}>${pages.page_name}</option>`
                    });
                }

                $("#media_page_id").html(opt)
                $('#media_page_id').selectpicker("refresh");
                removeOverlay();
            },
        });
        var isDateIsCorrect = checkIfMedisIsInstagramAndDateRangeIsOnly30Days();
        if(!isDateIsCorrect){
            $('#date_error').css('display', '');
            $('#date_error').text('Instagram only provides the data of 30 days time Period. Please select Date Range within 30 days.');
        }else{
            $('#date_error').text('');
            $('#date_error').css('display', 'none');
        }
    })

    $('#media_page_id').change(function() {
        var media_ids = $("#media_page_id option:selected").val();
        if(media_ids === 'all') {
            $('#media_page_id').selectpicker('val','all');
        }else if(media_ids){
            var is_expired = checkForTokenExpiry(media_ids);
            if(is_expired.status){
                $("#media_page_id option:selected").prop("selected", false);
                showMessage(412, is_expired.message);
                return;
            }
        }
    });

    function checkForTokenExpiry(media_page_id){
        var return_response = null;
        var url = "{{ route('check.token_expiry') }}";
        $.ajax({
            type: 'POST',
            url: url,
            dataType:'json',
            async: false, // Please do not Implement this line in future.
            data: {
                _token: '{{ csrf_token() }}',
                media_page_id: media_page_id ?? "",
            },
            success: function(response) {
                return_response = response;
            },
        });
        return return_response;
    }

    $(".nav-ga-data").on('click', function(){
        if($(this).data('element')){
            let ele = $(this).data('element');
            setTimeout(() => {
                $("."+ele+"-section-count:first").click()
            }, "1000");
        }
    });

</script>

@endpush
