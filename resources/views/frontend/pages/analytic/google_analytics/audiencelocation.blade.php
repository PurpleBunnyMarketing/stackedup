<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader">
</div>
<div class="container">
    <div class="row post-cards  mb-12 ">
        <div class="col-lg-6 col-md-6 col-12">
            <div class="card">
                <div class="card-body line-chart-div-location align-items-center">
                    <div class="plateform_device_graph_container">
                        <div id="audience_location_map_chart"></div>
                    </div>
                    <div class="align-items-center justify-content-center no-data">
                        <h2 id="audience_location_map_chart_loading_text" class="d-flex justify-content-center loading-text">Loading...</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-12">
            <div class="card">
                <div class="card-body pie-chart-div-location align-items-center">
                    <div class="plateform_device_graph_container">
                        <div id="audience_location_bar_chart" style="height: 350px; width: 100%; display: none;"></div>
                    </div>
                    <div class="align-items-center justify-content-center no-data">
                        <h2 id="audience_location_bar_chart_loading_text" class="d-flex justify-content-center loading-text">Loading...</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row post-cards location-card">
        {{-- Sessions --}}
        <div class="col-lg-4 col-md-6 mb-6 col-12 location-section-count-main-div">
            <div class="card location-section-count-active">
                <a class="location-section-count" data-element="sessions">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">Sessions</h4>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="audience_count_sessions" class="d-flex justify-content-center loading-text">Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Total Users --}}
        <div class="col-lg-4 col-md-6 mb-6 col-12 location-section-count-main-div">
            <div class="card">
                <a class="location-section-count" data-element="totalUsers">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">Total Users</h4>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="audience_count_totalUsers" class="d-flex justify-content-center loading-text">Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- User Engagement --}}
        <div class="col-lg-4 col-md-6 mb-6 col-12 location-section-count-main-div">
            <div class="card">
                <a class="location-section-count" data-element="userEngagementDuration">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">User Engagement</h4>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="audience_count_userEngagementDuration" class="d-flex justify-content-center loading-text">Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Views --}}
        <div class="col-lg-4 col-md-6 mb-6 col-12 location-section-count-main-div">
            <div class="card">
                <a class="location-section-count" data-element="screenPageViews">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">Views</h4>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="audience_count_screenPageViews" class="d-flex justify-content-center loading-text">Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Conversions --}}
        <div class="col-lg-4 col-md-6 mb-6 col-12 location-section-count-main-div">
            <div class="card">
                <a class="location-section-count" data-element="conversions">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">Conversions</h4>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="audience_count_conversions" class="d-flex justify-content-center loading-text">Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Event Count --}}

        <div class="col-lg-4 col-md-6 mb-6 col-12 location-section-count-main-div">
            <div class="card">
                <a class="location-section-count" data-element="eventCount">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">Event Count</h6>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="audience_count_eventCount" class="d-flex justify-content-center loading-text">Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

    </div>
    <div class="subheader pt-6 pt-lg-12 subheader-transparent ga_locationTabs" id="kt_subheader">
        <ul class="nav nav-pills" id="myTab" role="tablist">
            <li class="nav-item" id="instagramDiscoveryTab">
                <a class="location_tab nav-link loc-link p-md-0 active" id="home-tab-4" data-toggle="tab" href="#googleAnalyticsAudienceLocationCountry">
                    <span class="post-link btn font-size-h3 font-weight-bold active">Country</span>
                </a>
            </li>
            <li class="nav-item" id="facebookPostsTab">
                <a class="location_tab nav-link loc-link p-md-0" id="home-tab-4" data-toggle="tab" href="#googleAnalyticsAudienceLanguageRegion">
                    <span class="post-link btn font-size-h3 font-weight-bold">Region</span>
                </a>
            </li>
            <li class="nav-item" id="facebookPostsTab">
                <a class="location_tab nav-link loc-link p-md-0" id="home-tab-4" data-toggle="tab" href="#googleAnalyticsAudienceAgeCity">
                    <span class="post-link btn font-size-h3 font-weight-bold">City</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="tab-content mt-6" id="myTabContent">
        <div class="tab-pane fade show active" id="googleAnalyticsAudienceLocationCountry" role="tabpanel" aria-labelledby="home-tab-4">
            <div class="card">
                <div class="card-body data-table-div-country">
                    {{-- Datatable Start --}}
                    <table class="table table-bordered table-hover table-checkable" id="audience_location_country_table"
                        style="margin-top: 13px !important"></table>
                    {{-- Datatable End --}}
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="googleAnalyticsAudienceLanguageRegion" role="tabpanel" aria-labelledby="home-tab-4">
            <div class="card">
                <div class="card-body data-table-div-region">
                    {{-- Datatable Start --}}
                    <table class="table table-bordered table-hover table-checkable" id="audience_location_region_table"
                        style="margin-top: 13px !important"></table>
                    {{-- Datatable End --}}
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="googleAnalyticsAudienceAgeCity" role="tabpanel" aria-labelledby="home-tab-4">
            <div class="card">
                <div class="card-body data-table-div-city">
                    <table class="table table-bordered table-hover table-checkable" id="audience_location_city_table"
                        style="margin-top: 13px !important"></table>
                </div>
            </div>
        </div>
    </div>

</div>

@push('extra-js-scripts')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">

</script>
<script>
    jQuery(document).ready(function() {
    //     initialLoadLocationData();
    // });
    // function initialLoadLocationData(){
        var urlParams   =   new URLSearchParams(window.location.search);
        var page_ids     =   urlParams.get('page_id');
        var start_date  =   urlParams.get('start_date');
        var end_date    =   urlParams.get('end_date');
        var start_date_time     =   new Date(start_date).getTime();
        var end_date_time       =   new Date(end_date).getTime();

        const loading_text                          =   'Loading...';
        const no_data_text                          =   'No details found';

        const ls_location_country_bar_chart    =   getKeyName('location_country_bar_chart');

        // all counts
        $.ajax({
            url: "{{ route('analytics.google-analytics.ajax', ['type' => 'acquisition-all-counts']) }}",
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                page_ids,
                start_date,
                end_date,
            },
            async: true,
            cache: false,
            beforeSend: function(msg){
                displayLocCountLoading(loading_text);
            },
            success: function(res){
                var count = res.data.count;
                $("#audience_count_sessions").html(count.sessions);
                $("#audience_count_totalUsers").html(count.totalUsers);
                $("#audience_count_userEngagementDuration").html(count.userEngagementDuration);
                $("#audience_count_screenPageViews").html(count.screenPageViews);
                $("#audience_count_conversions").html(count.conversions);
                $("#audience_count_eventCount").html(count.eventCount);
            },
            error: function(e){
                displayLocCountLoading(no_data_text);
            }
        })

        loadDataTable('country')
        loadDataTable('region')
        loadDataTable('city')

        function loadDataTable(channel = null) {
            console.log('loadDataTable',channel);
            var dataTableId     =   'audience_location_'+channel+'_table';
            var dataTableType   =   'audience-location-'+channel;
            var columns         =   [ { data: channel} ];
            var title           =   channel;

            columns.push(
                { data: 'sessions' },
                { data: 'totalUsers' },
                { data: 'userEngagementDuration' },
                { data: 'screenPageViews' },
                { data: 'conversions'},
                { data: 'eventCount' }
            );

            $(".data-table-div-"+channel).html('<table class="table table-bordered table-hover table-checkable" id="'+dataTableId+'" style="margin-top: 13px !important"></table>');
            var url =   "{{ route('analytics.google-analytics.ajax', ['type' => 'dataTableType']) }}";
            url     =   url.replace('dataTableType', dataTableType);

            $('#'+dataTableId).DataTable({
                responsive  :   true,
                processing  :   false,
                serverSide  :   false,
                searching   :   false,
                ordering    :   true,
                "bPaginate" :   true,
                "bInfo"     :   true,
                "info"      :   true,
                ajax: {
                    type    :   'POST',
                    url     :   url,
                    dataSrc :   function (response) {
                        if(channel == 'country'){
                            return dataSrc(response, channel);
                        }
                        return response.data.rows;
                    },
                    data    :   {
                        _token: '{{csrf_token()}}',
                        start_date,
                        end_date,
                        page_ids
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        $('#'+dataTableId).DataTable().clear().draw();
                        $("#audience_location_bar_chart_loading_text").html(no_data_text);
                        $("#audience_location_map_chart_loading_text").html(no_data_text);
                    }
                },
                columns :   columns,
                columnDefs: [
                    {
                        targets     :   0,
                        title       :   title,
                        orderable   :   true
                    },{
                        targets     :   1,
                        title       :   'Sessions',
                        orderable   :   true
                    },{
                        targets     :   2,
                        title       :   'Total Users',
                        orderable   :   true
                    },{
                        targets     :   3,
                        title       :   'User Engagement',
                        orderable   :   true
                    },{
                        targets     :   4,
                        title       :   'Views',
                        orderable   :   true
                    },{
                        targets     :   5,
                        title       :   'Conversions',
                        orderable   :   true
                    },{
                        targets     :   6,
                        title       :   'Event Count',
                        orderable   :   true
                    }
                ],
                order: [ [0, 'desc'] ]
            });

        }

        function dataSrc(response, channel) {
            var returnData = response.data.rows;
            if(channel == 'country'){
                setItem(ls_location_country_bar_chart, response.data.bar_chart);
                loadLocationGraph(channel, 'bar_chart', ls_location_country_bar_chart);
                loadCountryGraph();
            }
            return returnData;
        }

        $(".location-section-count").on('click', function(){
            var selector = $(this);
            var channel = "country";
            loadLocationGraph(channel, 'bar_chart', ls_location_country_bar_chart,selector)
            loadCountryGraph(selector);
        });

        function loadLocationGraph(channel, graphType, graphKey,selector = null){
            var is_error = true;
            if(selector == null){
                var selector = $(".location-section-count-active").find('.location-section-count');
            }
            selector.parents('.location-section-count-main-div').siblings().find('.location-section-count-active').toggleClass('location-section-count-active');
            selector.parent('.card').addClass('location-section-count-active');
            var elementTitle    =   selector.find('.count-title').html() ?? 'details';

            var chat_details    =   getItem(graphKey);
            if(chat_details && selector.find('.loading-text').text() != 0) {
                var element     =   selector.data('element');
                var chart_element = chat_details;

                if(chart_element && chart_element[element]){
                    if(graphType == 'bar_chart'){
                        is_error = false;
                        $("#audience_location_bar_chart").css("display", "");
                        $("#audience_location_bar_chart_loading_text").parent('div').css("display","none");
                        audience_location_bar_chart(elementTitle, chart_element[element]);
                    }

                }
            }

            if(is_error){
                if(graphType == 'bar_chart'){
                    $("#audience_location_bar_chart").empty();
                    $("#audience_location_bar_chart").css("display", "none");
                    $("#audience_location_bar_chart_loading_text").html('No '+elementTitle+' found for your date range');
                    $("#audience_location_bar_chart_loading_text").parent('div').css("display","");
                }
            }
        }

        // Local storage key name
        function getKeyName(keyName) {
            return page_ids+'_'+start_date_time+'_'+end_date_time+'_'+keyName;
        }

        // Local storage set details
        function setItem(key, value){
            localStorage.setItem(key, JSON.stringify(value));
        }

        // Local storage get details
        function getItem(key){
            return JSON.parse(localStorage.getItem(key));
        }

        var audience_location_bar_chart = (title_text, data_points) => {
            let slice_data_arr = data_points.slice(0, 7);
            var chart = new CanvasJS.Chart(document.querySelector("#audience_location_bar_chart"), {
                theme: "light2",
                animationEnabled: true,
                title: {
                    text: title_text
                },
                height: 350,
                data: [{
                    type: "column",
                    indexLabel: "{indexLabel}",
                    toolTipContent: "{label} - {indexLabel}",
                    legendText: "{labels}",
                    dataPoints: slice_data_arr
                }]
            });
            chart.render();
            $('.canvasjs-chart-credit').remove();
        }

        function loadCountryGraph(selector = null){

            var is_error = true;
            if(selector == null){
                var selector = $(".location-section-count-active").find('.location-section-count');
            }
            selector.parents('.location-section-count-main-div').siblings().find('.location-section-count-active').toggleClass('location-section-count-active');
            selector.parent('.card').addClass('location-section-count-active');
            var elementTitle    =   selector.find('.count-title').html() ?? 'details';
            var chat_details    =   getItem(ls_location_country_bar_chart);

            if(chat_details && selector.find('.loading-text').text() != 0) {
                var element     =   selector.data('element');
                var chart_element = chat_details;

                if(chart_element && chart_element[element]){

                    const conArr = [['Country', elementTitle]];
                    chart_element[element].map(res => {
                        conArr.push([
                            res.label,res.y
                        ])
                    })

                    is_error = false;
                    $("#audience_location_map_chart").css("display", "");
                    $("#audience_location_map_chart_loading_text").parent('div').css("display","none");
                    google.charts.load('current', {
                        'packages':['geochart'],
                        'mapsApiKey': 'AIzaSyD-9tSrke72PouQMnMX-a7eZSW0jkFMBWY'
                    });
                    google.charts.setOnLoadCallback(drawRegionsMap);

                    function drawRegionsMap() {
                        var data = google.visualization.arrayToDataTable(conArr);

                        var options = {
                            colorAxis: {colors: ['#E6E6E6', '#6131AB']}
                        };
                        var chart = new google.visualization.GeoChart(document.getElementById('audience_location_map_chart'));
                        chart.draw(data, options);
                    }
                }

            }

            if(is_error){
                $("#audience_location_map_chart").empty();
                $("#audience_location_map_chart").css("display", "none");
                $("#audience_location_map_chart_loading_text").html('No '+elementTitle+' found for your date range');
                $("#audience_location_map_chart_loading_text").parent('div').css("display","");
            }

        }


        // Display loading count
        function displayLocCountLoading(loading_text) {
            $("#audience_count_sessions").html(loading_text);
            $("#audience_count_totalUsers").html(loading_text);
            $("#audience_count_userEngagementDuration").html(loading_text);
            $("#audience_count_screenPageViews").html(loading_text);
            $("#audience_count_conversions").html(loading_text);
            $("#audience_count_eventCount").html(loading_text);
        }
        $(".location_tab").on("click", function(){
                // $("#kt_subheader_acquisition").css("display","");
                $(".ga_locationTabs").find(".nav li a span.active").toggleClass("active");
                $(".ga_locationTabs").find(".nav li a.active").toggleClass("active");
                // console.log($("#kt_subheader_audiance").find(".nav li").first().find("span"), 'span find');
                $(this).find(".post-link").addClass("active");
                $(this).find("a").addClass("active");
            });
    });
    // }
</script>
@endpush