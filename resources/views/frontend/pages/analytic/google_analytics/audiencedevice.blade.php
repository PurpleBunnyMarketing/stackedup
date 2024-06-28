<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader">
</div>
<div class="container">
    <div class="row post-cards mb-12">        
        <div class="col-lg-8 col-md-8">
            <div class="card">
                <div class="card-body line-chart-div-device align-items-center">
                    <div class="plateform_device_graph_container">
                        <div id="audience_device_line_chart" style="height: 350px; width: 100%; display: none;"></div>
                    </div>
                    <div class="align-items-center justify-content-center no-data">
                        <h2 id="audience_device_line_chart_loading_text" class="d-flex justify-content-center loading-text">Loading...</h2>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-4">
            <div class="card">
                <div class="card-body line-chart-div-device align-items-center">
                    <div class="plateform_device_graph_container">
                        <div id="audience_device_pie_chart" style="height: 350px; width: 100%; display: none;"></div>
                    </div>
                    <div class="align-items-center justify-content-center no-data">
                        <h2 id="audience_device_pie_chart_loading_text" class="d-flex justify-content-center loading-text">Loading...</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row post-cards">
        {{-- Sessions --}}        

        <div class="col-lg-4 col-md-6 mb-6 col-12 device-section-count-main-div">
            <div class="card device-section-count-active">
                <a class="device-section-count" data-element="sessions">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">Sessions</h4>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="audience_device_count_sessions" class="d-flex justify-content-center loading-text">Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Total Users --}}
        
        <div class="col-lg-4 col-md-6 mb-6 col-12 device-section-count-main-div">
            <div class="card">
                <a class="device-section-count" data-element="totalUsers">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">Total Users</h4>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="audience_device_count_totalUsers" class="d-flex justify-content-center loading-text">Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- User Engagement --}}
        <div class="col-lg-4 col-md-6 mb-6 col-12 device-section-count-main-div">
            <div class="card">
                <a class="device-section-count" data-element="userEngagementDuration">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">User Engagement</h4>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="audience_device_count_userEngagementDuration" class="d-flex justify-content-center loading-text">Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Views --}}
        
        <div class="col-lg-4 col-md-6 mb-6 col-12 device-section-count-main-div">
            <div class="card">
                <a class="device-section-count" data-element="screenPageViews">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">Views</h4>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="audience_device_count_screenPageViews" class="d-flex justify-content-center loading-text">Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>


        {{-- Conversions --}}
        
        <div class="col-lg-4 col-md-6 mb-6 col-12 device-section-count-main-div">
            <div class="card">
                <a class="device-section-count" data-element="conversions">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">Conversions</h4>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="audience_device_count_conversions" class="d-flex justify-content-center loading-text">Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Event Count --}}
        <div class="col-lg-4 col-md-6 mb-6 col-12 device-section-count-main-div">
            <div class="card">
                <a class="device-section-count" data-element="eventCount">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">Event Count</h6>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="audience_device_count_eventCount" class="d-flex justify-content-center loading-text">Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

    </div>
    
    <div class="card">
        <div class="card-body data-table-div-device">
            {{-- Datatable Start --}}
            <table class="table table-bordered table-hover table-checkable" id="audience_device_table"
                style="margin-top: 13px !important"></table>
            {{-- Datatable End --}}
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

        const ls_device_pie_chart    =   getKeyName('device_pie_chart');
        const ls_device_line_chart    =   getKeyName('device_line_chart');

        loadDeviceDataTable('device')
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
                displayDeviceCountLoading(loading_text);
            },
            success: function(res){
                var count = res.data.count;
                $("#audience_device_count_sessions").html(count.sessions);
                $("#audience_device_count_totalUsers").html(count.totalUsers);
                $("#audience_device_count_userEngagementDuration").html(count.userEngagementDuration);
                $("#audience_device_count_screenPageViews").html(count.screenPageViews);
                $("#audience_device_count_conversions").html(count.conversions);
                $("#audience_device_count_eventCount").html(count.eventCount);
            },
            error: function(e){
                displayDeviceCountLoading(no_data_text);
            }
        });
      

        function loadDeviceDataTable(channel = null) {
            var dataTableId     =   'audience_'+channel+'_table';
            var dataTableType   =   'audience-mobileDeviceMarketingName';
            var columns         =   [ { data: "mobileDeviceMarketingName"} ];
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
                        return dataSrc(response, channel);
                    },
                    data    :   {
                        _token: '{{csrf_token()}}',
                        start_date,
                        end_date,
                        page_ids
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        $('#'+dataTableId).DataTable().clear().draw();
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
            return returnData;
        }

        //Pie chart
        $.ajax({
            url: "{{ route('analytics.google-analytics.ajax', ['type' => 'audience-deviceCategory']) }}",
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
                setItem(ls_device_pie_chart, res.data.pie_chart);          
                loadDeviceGraph('pie_chart', ls_device_pie_chart)
            },
            error: function(e){
                //console.log(e);
            }
        });

        //Line chart
        $.ajax({
            url: "{{ route('analytics.google-analytics.ajax', ['type' => 'audience-deviceCategoryDate']) }}",
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
                setItem(ls_device_line_chart, res.data.line_chart);          
                loadDeviceGraph('line_chart', ls_device_line_chart)
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

        function loadDeviceGraph(graphType, graphKey,selector = null){
            var is_error = true;     
            if(selector == null){
                var selector = $(".device-section-count-active").find('.device-section-count');
            }
            selector.parents('.device-section-count-main-div').siblings().find('.device-section-count-active').toggleClass('device-section-count-active');
            selector.parent('.card').addClass('device-section-count-active');
            var elementTitle    =   selector.find('.count-title').html() ?? 'details';
                   
            var chat_details    =   getItem(graphKey);
            if(chat_details && selector.find('.loading-text').text() != 0) {
                var element     =   selector.data('element');
                var chart_element = chat_details;            
                if(chart_element && chart_element[element]){
                    if(graphType == 'pie_chart'){
                        is_error = false;
                        $("#audience_device_pie_chart").css("display", "");
                        $("#audience_device_pie_chart_loading_text").parent('div').css("display","none");
                        audience_device_pie_chart(elementTitle, chart_element[element]);
                    }   
                    if(graphType == 'line_chart'){
                        is_error = false;
                        $("#audience_device_line_chart").css("display", "");
                        $("#audience_device_line_chart_loading_text").parent('div').css("display","none");
                        audience_device_line_chart(elementTitle, chart_element[element]);
                    }                    
                }
            }

            if(is_error){
                if(graphType == 'pie_chart'){
                    $("#audience_device_pie_chart").empty();
                    $("#audience_device_pie_chart").css("display", "none");
                    $("#audience_device_pie_chart_loading_text").html('No '+elementTitle+' found for your date range');
                    $("#audience_device_pie_chart_loading_text").parent('div').css("display","");
                }

                if(graphType == 'line_chart'){
                    $("#audience_device_line_chart").empty();
                    $("#audience_device_line_chart").css("display", "none");
                    $("#audience_device_line_chart_loading_text").html('No '+elementTitle+' found for your date range');
                    $("#audience_device_line_chart_loading_text").parent('div').css("display","");
                }
            }
        }

        var audience_device_pie_chart = (title_text, data_points) => {
            var chart = new CanvasJS.Chart(document.querySelector("#audience_device_pie_chart"), {
                theme: "light2",
                animationEnabled: true,
                title: {
                    text: title_text
                },
                height: 350,
                data: [{
                    type: "pie",
                    indexLabel: "{indexLabel}",
                    toolTipContent: "{labels} - {indexLabel}",
                    indexLabelPlacement: "inside",
                    indexLabelFontColor: "#36454F",
                    indexLabelFontSize: 18,
                    indexLabelFontWeight: "bolder",
                    showInLegend: true,
                    legendText: "{labels}",
                    dataPoints: data_points
                }]
            });
            chart.render();
            $('.canvasjs-chart-credit').remove();
        }   
        
        var audience_device_line_chart = (title_text, data_points) => {
            // console.log(data_points)
            // return;
            const LineArr = [];

            Object.keys(data_points).forEach(function(key, index) {
                let ChartDataPoint = [];
                data_points[key].forEach(data_point => {
                    ChartDataPoint.push({
                        x: new Date(data_point['x']), y: data_point['y']
                    })
                });

                LineArr.push({
                    name: key,
                    type: "spline",
                    showInLegend: true,
                    dataPoints: ChartDataPoint
                })
            });

            var chart = new CanvasJS.Chart(document.querySelector("#audience_device_line_chart"), {
                animationEnabled: true,
                title:{
                    text: title_text
                },
                axisX: {
                    valueFormatString: "DD MMM"
                },
                legend:{
                    cursor: "pointer",
                    fontSize: 16
                },
                toolTip:{
                    shared: true
                },
                data: LineArr
            });
            chart.render();
            $('.canvasjs-chart-credit').remove();
        }   

        $(".device-section-count").on('click', function(){
            var selector = $(this);
            loadDeviceGraph('pie_chart', ls_device_pie_chart,selector)
            loadDeviceGraph('line_chart', ls_device_line_chart,selector)
        });

        // Display loading count
        function displayDeviceCountLoading(loading_text) {
            $("#audience_device_count_sessions").html(loading_text);
            $("#audience_device_count_totalUsers").html(loading_text);
            $("#audience_device_count_userEngagementDuration").html(loading_text);
            $("#audience_device_count_screenPageViews").html(loading_text);
            $("#audience_device_count_conversions").html(loading_text);
            $("#audience_device_count_eventCount").html(loading_text);
        }

    });
    
</script>
@endpush
