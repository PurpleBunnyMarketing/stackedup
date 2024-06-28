<div class="container">
    <div class="subheader pt-6 pt-lg-12 subheader-transparent" id="kt_subheader_audiance">
        <ul class="nav nav-pills" id="myTab" role="tablist">
            <li class="nav-item" id="gaLocation">
                <a class="nav-ga-data nav-link p-md-0 active"  id="home-tab-4" data-toggle="tab" data-channel="googleAnalyticsAudienceLocation" href="#googleAnalyticsAudienceLocation">
                    <span class="post-link btn font-size-h3 font-weight-bold active" >Location</span>
                </a>
            </li>
            <li class="nav-item" id="gaLanguage">
                <a class="nav-ga-data nav-link p-md-0" data-element="language" id="home-tab-4" data-toggle="tab" href="#googleAnalyticsAudienceLanguage">
                    <span class="post-link btn font-size-h3 font-weight-bold">Language</span>
                </a>
            </li>
            <li class="nav-item" id="gaAge">
                <a class="nav-ga-data nav-link p-md-0" data-element="age" id="home-tab-4" data-toggle="tab" href="#googleAnalyticsAudienceAge">
                    <span class="post-link btn font-size-h3 font-weight-bold">Age</span>
                </a>
            </li>
            <li class="nav-item" id="gaGender">
                <a class="nav-ga-data nav-link p-md-0" data-element="gender" id="home-tab-4" data-toggle="tab" href="#googleAnalyticsAudienceGender">
                    <span class="post-link btn font-size-h3 font-weight-bold">Gender</span>
                </a>
            </li>
            <li class="nav-item" id="gaDevice">
                <a class="nav-ga-data nav-link p-md-0" data-element="device" id="home-tab-4" data-toggle="tab" href="#googleAnalyticsAudienceDevice">
                    <span class="post-link btn font-size-h3 font-weight-bold">Device</span>
                </a>
            </li>
            <li class="nav-item" id="gaBrowser">
                <a class="nav-ga-data nav-link p-md-0" data-element="browser" id="home-tab-4" data-toggle="tab" href="#googleAnalyticsAudienceBrowser">
                    <span class="post-link btn font-size-h3 font-weight-bold">Browser</span>
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="tab-content ga_audianceDataTabs" id="myTabContent">
    <div class="tab-pane fade show active" id="googleAnalyticsAudienceLocation" role="tabpanel" aria-labelledby="home-tab-4">
        @include('frontend.pages.analytic.google_analytics.audiencelocation')
    </div>
    <div class="tab-pane fade" id="googleAnalyticsAudienceLanguage" role="tabpanel" aria-labelledby="home-tab-4">
        @include('frontend.pages.analytic.google_analytics.audiencelanguage')
    </div>
    <div class="tab-pane fade" id="googleAnalyticsAudienceAge" role="tabpanel" aria-labelledby="home-tab-4">
        @include('frontend.pages.analytic.google_analytics.audienceage')
    </div>
    <div class="tab-pane fade" id="googleAnalyticsAudienceGender" role="tabpanel" aria-labelledby="home-tab-4">
        @include('frontend.pages.analytic.google_analytics.audiencegender')
    </div>
    <div class="tab-pane fade" id="googleAnalyticsAudienceDevice" role="tabpanel" aria-labelledby="home-tab-4">
        @include('frontend.pages.analytic.google_analytics.audiencedevice')
    </div>
    <div class="tab-pane fade" id="googleAnalyticsAudienceBrowser" role="tabpanel" aria-labelledby="home-tab-4">
        @include('frontend.pages.analytic.google_analytics.audiencebrowser')
    </div>
</div>


@push('extra-js-scripts')
    <script>
        $(document).ready(function(){
            $(".nav-ga-data").on("click", function(){
                // $("#kt_subheader_acquisition").css("display","");
                $("#kt_subheader_audiance").find(".nav li a span.active").removeClass("active");
                $("#kt_subheader_audiance").find(".nav li a.active").removeClass("active");
                // console.log($("#kt_subheader_audiance").find(".nav li").first().find("span"), 'span find');
                $(this).find(".post-link").addClass("active");
                $(this).find("a").addClass("active");
            });
        });
    </script>
@endpush
