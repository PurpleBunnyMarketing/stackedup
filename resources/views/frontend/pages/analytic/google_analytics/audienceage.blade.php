<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader">
</div>
<div class="container">
    <div class="row post-cards mb-12">          
        <div class="col-lg-12 col-md-12 col-12">
            <div class="card">
                <div class="card-body bar-chart-div-age align-items-center">
                    <div class="plateform_device_graph_container">
                        <div id="audience_age_bar_chart" style="height: 350px; width: 100%; display: none;"></div>
                    </div>
                    <div class="align-items-center justify-content-center no-data">
                        <h2 id="audience_age_bar_chart_loading_text" class="d-flex justify-content-center loading-text">Loading...</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row post-cards">
        {{-- Sessions --}}        

        <div class="col-lg-4 col-md-6 mb-6 col-12 age-section-count-main-div">
            <div class="card age-section-count-active">
                <a class="age-section-count" data-element="sessions">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">Sessions</h4>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="audience_age_count_sessions" class="d-flex justify-content-center loading-text">Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Total Users --}}
        
        <div class="col-lg-4 col-md-6 mb-6 col-12 age-section-count-main-div">
            <div class="card">
                <a class="age-section-count" data-element="totalUsers">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">Total Users</h4>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="audience_age_count_totalUsers" class="d-flex justify-content-center loading-text">Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- User Engagement --}}
        <div class="col-lg-4 col-md-6 mb-6 col-12 age-section-count-main-div">
            <div class="card">
                <a class="age-section-count" data-element="userEngagementDuration">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">User Engagement</h4>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="audience_age_count_userEngagementDuration" class="d-flex justify-content-center loading-text">Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Views --}}
        
        <div class="col-lg-4 col-md-6 mb-6 col-12 age-section-count-main-div">
            <div class="card">
                <a class="age-section-count" data-element="screenPageViews">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">Views</h4>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="audience_age_count_screenPageViews" class="d-flex justify-content-center loading-text">Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>


        {{-- Conversions --}}
        
        <div class="col-lg-4 col-md-6 mb-6 col-12 age-section-count-main-div">
            <div class="card">
                <a class="age-section-count" data-element="conversions">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">Conversions</h4>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="audience_age_count_conversions" class="d-flex justify-content-center loading-text">Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Event Count --}}
        <div class="col-lg-4 col-md-6 mb-6 col-12 age-section-count-main-div">
            <div class="card">
                <a class="age-section-count" data-element="eventCount">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">Event Count</h6>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="audience_age_count_eventCount" class="d-flex justify-content-center loading-text">Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

    </div>

    
</div>

@push('extra-js-scripts')
<script>
    jQuery(document).ready(function() {
        var urlParams   =   new URLSearchParams(window.location.search);
        var page_ids     =   urlParams.get('page_id');
        var start_date  =   urlParams.get('start_date');
        var end_date    =   urlParams.get('end_date');
        var start_date_time     =   new Date(start_date).getTime();
        var end_date_time       =   new Date(end_date).getTime();

        const loading_text                          =   'Loading...';
        const no_data_text                          =   'No details found';

        const ls_age_bar_chart    =   getKeyName('age_bar_chart');

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
                displayAgeCountLoading(loading_text);
            },
            success: function(res){
                var count = res.data.count;
                $("#audience_age_count_sessions").html(count.sessions);
                $("#audience_age_count_totalUsers").html(count.totalUsers);
                $("#audience_age_count_userEngagementDuration").html(count.userEngagementDuration);
                $("#audience_age_count_screenPageViews").html(count.screenPageViews);
                $("#audience_age_count_conversions").html(count.conversions);
                $("#audience_age_count_eventCount").html(count.eventCount);
            },
            error: function(e){
                displayAgeCountLoading(no_data_text);
            }
        })

        $.ajax({
            url: "{{ route('analytics.google-analytics.ajax', ['type' => 'audience-userAgeBracket']) }}",
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                page_ids,
                start_date,
                end_date,
            },
            async: true,
            cache: false,
            success: function(res){    
                setItem(ls_age_bar_chart, res.data.bar_chart);          
                loadAgeGraph('bar_chart', ls_age_bar_chart)
            },
            error: function(e){
                //console.log(e);
            }
        });

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

        function loadAgeGraph(graphType, graphKey,selector = null){
            var is_error = true;     
            if(selector == null){
                var selector = $(".age-section-count-active").find('.age-section-count');
            }
            selector.parents('.age-section-count-main-div').siblings().find('.age-section-count-active').toggleClass('age-section-count-active');
            selector.parent('.card').addClass('age-section-count-active');
            var elementTitle    =   selector.find('.count-title').html() ?? 'details';
                   
            var chat_details    =   getItem(graphKey);
            if(chat_details && selector.find('.loading-text').text() != 0) {
                var element     =   selector.data('element');
                var chart_element = chat_details;

                if(chart_element && chart_element[element]){
                    if(graphType == 'bar_chart'){
                        is_error = false;
                        $("#audience_age_bar_chart").css("display", "");
                        $("#audience_age_bar_chart_loading_text").parent('div').css("display","none");
                        audience_age_bar_chart(elementTitle, chart_element[element]);
                    }                    
                }
            }

            if(is_error){
                if(graphType == 'bar_chart'){
                    $("#audience_age_bar_chart").empty();
                    $("#audience_age_bar_chart").css("display", "none");
                    $("#audience_age_bar_chart_loading_text").html('No '+elementTitle+' found for your date range');
                    $("#audience_age_bar_chart_loading_text").parent('div').css("display","");
                }
            }
        }

        $(".age-section-count").on('click', function(){
            var selector = $(this);
            loadAgeGraph('bar_chart', ls_age_bar_chart,selector)
        });

        var audience_age_bar_chart = (title_text, data_points) => {
            let slice_data_arr = data_points.slice(0, 7);
            var chart = new CanvasJS.Chart(document.querySelector("#audience_age_bar_chart"), {
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

        // Display loading count
        function displayAgeCountLoading(loading_text) {
            $("#audience_age_count_sessions").html(loading_text);
            $("#audience_age_count_totalUsers").html(loading_text);
            $("#audience_age_count_userEngagementDuration").html(loading_text);
            $("#audience_age_count_screenPageViews").html(loading_text);
            $("#audience_age_count_conversions").html(loading_text);
            $("#audience_age_count_eventCount").html(loading_text);
        }

    });
</script>
@endpush
