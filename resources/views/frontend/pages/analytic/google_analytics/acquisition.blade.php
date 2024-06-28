<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader_acquisition">
    <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <ul class="nav nav-pills" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-ga nav-link p-md-0 active" data-toggle="tab" data-channel="gaAcquisitionAll"
                    href="#gaAcquisitionAll">
                    <span class="post-link btn font-size-h3 font-weight-bold active">All</span>
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
    </div>
</div>
<div class="container">
    <div class="row post-cards mb-12">
        {{-- Line Chart --}}
        <div class="col-lg-6 col-md-6 col-12">
            <div class="card">
                <div class="card-body line-chart-div align-items-center">
                    <div class="plateform_device_graph_container">
                        <div id="acquision_line_chart" style="height: 350px; width: 100%; display: none;"></div>
                    </div>
                    <div class="align-items-center justify-content-center no-data">
                        <h2 id="acquision_line_chart_loading_text" class="d-flex justify-content-center loading-text">
                            Loading...</h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pie Chart --}}
        <div class="col-lg-6 col-md-6 col-12">
            <div class="card">
                <div class="card-body pie-chart-div align-items-center">
                    <div class="plateform_device_graph_container">
                        <div id="acquision_pai_chart" style="height: 350px; width: 100%; display: none;"></div>
                    </div>
                    <div class="align-items-center justify-content-center no-data">
                        <h2 id="acquision_pie_chart_loading_text" class="d-flex justify-content-center loading-text">
                            Loading...</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row post-cards mb-6">
        {{-- Sessions --}}
        <div class="col-lg-4 col-md-6 mb-6 col-12 section-count-main-div">
            <div class="card section-count-active">
                <a class="section-count" data-element="sessions">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">Sessions</h4>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="count_sessions" class="d-flex justify-content-center loading-text">Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Total Users --}}
        <div class="col-lg-4 col-md-6 mb-6 col-12 section-count-main-div">
            <div class="card">
                <a class="section-count" data-element="totalUsers">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">Total Users</h4>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="count_totalUsers" class="d-flex justify-content-center loading-text">Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- User Engagement --}}
        <div class="col-lg-4 col-md-6 mb-6 col-12 section-count-main-div">
            <div class="card">
                <a class="section-count" data-element="userEngagementDuration">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">User Engagement</h4>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="count_userEngagementDuration" class="d-flex justify-content-center loading-text">
                                Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Views --}}
        <div class="col-lg-4 col-md-6 mb-6 col-12 section-count-main-div">
            <div class="card">
                <a class="section-count" data-element="screenPageViews">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">Views</h4>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="count_screenPageViews" class="d-flex justify-content-center loading-text">Loading...
                            </h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Conversions --}}
        <div class="col-lg-4 col-md-6 mb-6 col-12 section-count-main-div">
            <div class="card">
                <a class="section-count" data-element="conversions">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">Conversions</h4>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="count_conversions" class="d-flex justify-content-center loading-text">Loading...
                            </h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Event Count --}}
        <div class="col-lg-4 col-md-6 mb-6 col-12 section-count-main-div">
            <div class="card">
                <a class="section-count" data-element="eventCount">
                    <div class="card-header">
                        <h4 class="text-bold-700 count-title">Event Count</h6>
                    </div>
                    <div class="card-body">
                        <div class="align-items-center justify-content-center no-data">
                            <h2 id="count_eventCount" class="d-flex justify-content-center loading-text">Loading...</h2>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body data-table-div">
            {{-- Datatable Start --}}
            <table class="table table-bordered table-hover table-checkable" id="acquisition_all_table"
                style="margin-top: 13px !important"></table>
            {{-- Datatable End --}}
        </div>
    </div>
</div>

@push('extra-js-scripts')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="{{ asset('admin/plugins/canvasjs/canvasjs.min.js') }}"></script>
<script>
    CanvasJS.addColorSet("customColorSet1",["#3755A9", "#E77F01", "#F2B1E4", "#A5ECFF", "#39ac70", "#3EA0DD", "#F5A52A", "#23BFAA", "#FAA586", "#EB8CC6"]);

    $(function(){
        // $('#start_date').datepicker({
        //     todayHighlight: true,
        //     startDate: '01/01/2000',
        //     templates: {
        //         leftArrow: '<i class="la la-angle-left"></i>',
        //         rightArrow: '<i class="la la-angle-right"></i>',
        //     },
        // }).on('change', function() {
        //     var date = $(this).val();
        //     var time = $('#schedule_time').val();
        //     var hours = Number(time.match(/^(\d+)/)[1]);
        //     var minutes = Number(time.match(/:(\d+)/)[1]);
        //     var AMPM = time.match(/\s(.*)$/)[1];
        //     if (AMPM == "PM" && hours < 12) hours = hours + 12;
        //     if (AMPM == "AM" && hours == 12) hours = hours - 12;
        //     var sHours = hours.toString();
        //     var sMinutes = minutes.toString();
        //     if (hours < 10) sHours = "0" + sHours;
        //     if (minutes < 10) sMinutes = "0" + sMinutes;
        //     var time1 = sHours + ":" + sMinutes;
        //     var from = Date.parse(date + " " + time1);
        //     var close = Date.parse(new Date());

        //     if (close > from) {
        //         $('#error-time').css('display', 'block');
        //         $('#error-time').text('close time should be greater than open time');
        //         $('#error-time').addClass('invalid-feedback');
        //         $("input[type=submit], input[type=button], button[type=submit]").prop("disabled",
        //             "disabled");
        //         return false;
        //     } else {
        //         $('#error-time').removeClass('invalid-feedback');
        //         $("input[type=submit], input[type=button], button[type=submit]").prop("disabled",
        //             false);
        //         $('#error-time').html('');
        //         return true;
        //     }
        // });

        // $('#end_date').datepicker({
        //     todayHighlight: true,
        //     startDate: '01/01/2000',
        //     templates: {
        //         leftArrow: '<i class="la la-angle-left"></i>',
        //         rightArrow: '<i class="la la-angle-right"></i>',
        //     },
        // }).on('change', function() {
        //     var date = $(this).val();

        //     var from = Date.parse(date + " " + time1);
        //     var close = Date.parse(new Date());

        //     if (close > from) {
        //         $('#error-time').css('display', 'block');
        //         $('#error-time').text('close time should be greater than open time');
        //         $('#error-time').addClass('invalid-feedback');
        //         $("input[type=submit], input[type=button], button[type=submit]").prop("disabled",
        //             "disabled");
        //         return false;
        //     } else {
        //         $('#error-time').removeClass('invalid-feedback');
        //         $("input[type=submit], input[type=button], button[type=submit]").prop("disabled",
        //             false);
        //         $('#error-time').html('');
        //         return true;
        //     }
        // });
    });

    var acquision_line_chart = (title_text, data_points) => {
        // Format date
        data_points.forEach(data_point => {
            data_point['x'] = new Date(data_point['x']);
        });

        var chart = new CanvasJS.Chart(document.querySelector("#acquision_line_chart"), {
            theme: "light2",
            animationEnabled: true,
            title: {
                text: title_text,
            },
            colorSet:  "customColorSet1",
            height: 350,
            data: [{
                type: "spline",
                toolTipContent: "{x} - {displayLabel}",
                showInLegend: true,
                dataPoints: data_points
            }]
        });
        chart.render();
        $('.canvasjs-chart-credit').remove();
    }

    var acquision_pai_chart = (title_text, data_points) => {
        var chart = new CanvasJS.Chart(document.querySelector("#acquision_pai_chart"), {
            theme: "light2",
            animationEnabled: true,
            title: {
                text: title_text
            },
            colorSet:  "customColorSet1",
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

    jQuery(document).ready(function() {
        var urlParams           =   new URLSearchParams(window.location.search);
        var page_ids            =   urlParams.get('page_id');
        var start_date          =   urlParams.get('start_date');
        var end_date            =   urlParams.get('end_date');
        var start_date_time     =   new Date(start_date).getTime();
        var end_date_time       =   new Date(end_date).getTime();

        const loading_text                          =   'Loading...';
        const no_data_text                          =   'No details found';
        const ls_acquisition_all_line_chart         =   getKeyName('acquisition_all_line_chart');
        const ls_acquisition_all_pai_chart          =   getKeyName('acquisition_all_pai_chart');
        const ls_acquisition_channels_counts        =   getKeyName('acquisition_channels_counts');
        const ls_acquisition_channels_pie_charts    =   getKeyName('acquisition_channels_pie_charts');
        const ls_acquisition_channels_line_charts   =   getKeyName('acquisition_channels_line_charts');

        initialDataLoadings();

        // Intial loading
        function initialDataLoadings() {
            getAcquisitionAllCount();
            getAcquisionAllLineChart();
            loadDataTable();
            getAcquisitionChannelsCount();
            getAcquisitionChannelsLineCharts();
            getAcquisitionChannelsPieCharts();
        }

        // When main menu changed
        $(".main-nav-ga").on("click", function(){
            displayGraphLoading();

            var mainChannel = $(this).data('channel');
            if(mainChannel == 'gaAcquisition'){
                // $("#kt_subheader_acquisition").css("display","");
                $("#kt_subheader_acquisition").find(".nav li a span.active").removeClass("active");
                $("#kt_subheader_acquisition").find(".nav li a.active").removeClass("active");
                // console.log($("#kt_subheader_acquisition").find(".nav li").first().find("span"), 'span find');
                $("#kt_subheader_acquisition").find(".nav li").first().find("a span").addClass("active");
                $("#kt_subheader_acquisition").find(".nav li").first().find("a").addClass("active");

                initialDataLoadings();
            }
        });

        // When channel changed
        $(".nav-ga").on("click", function(){
            $("#kt_subheader_acquisition").find(".nav li a span.active").toggleClass("active");
            $("#kt_subheader_acquisition").find(".nav li a.active").toggleClass("active");
            // console.log($("#kt_subheader_acquisition").find(".nav li").first().find("span"), 'span find');
            $(this).find(".post-link").addClass("active");
            $(this).find("a").addClass("active");
            displayGraphLoading();
            var channel = $(this).data('channel');

            if(channel == 'gaAcquisitionAll'){
                getAcquisionAllLineChart();
                getAcquisitionAllCount();
            }else if(channel == 'gaAcquisitionOrganicSearch'){
                loadGraph(channel, 'line_chart', ls_acquisition_channels_line_charts);
                loadGraph(channel, 'pie_chart', ls_acquisition_channels_pie_charts);
            }else if(channel == 'gaAcquisitionPaidSearch'){
                loadGraph(channel, 'line_chart', ls_acquisition_channels_line_charts);
                loadGraph(channel, 'pie_chart', ls_acquisition_channels_pie_charts);
            }

            loadDataTable(channel);
        });

        // When count section changed
        $(".section-count").on('click', function(){
            var selector = $(this);
            var channel = $('.nav-ga.active').data('channel');

            if(channel == 'gaAcquisitionAll'){
                loadGraph(channel, 'line_chart', ls_acquisition_all_line_chart, selector);
                loadGraph(channel, 'pie_chart', ls_acquisition_all_pai_chart, selector);
            }else if(channel == 'gaAcquisitionOrganicSearch'){
                loadGraph(channel, 'line_chart', ls_acquisition_channels_line_charts, selector);
                loadGraph(channel, 'pie_chart', ls_acquisition_channels_pie_charts, selector);
            }else if(channel == 'gaAcquisitionPaidSearch'){
                loadGraph(channel, 'line_chart', ls_acquisition_channels_line_charts, selector);
                loadGraph(channel, 'pie_chart', ls_acquisition_channels_pie_charts, selector);
            }
        });

        // Acquisition line chart
        function getAcquisionAllLineChart(){
            $.ajax({
                url: "{{ route('analytics.google-analytics.ajax', ['type' => 'acquisition-all-line-chart']) }}",
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
                    displayCountLoading(loading_text);
                },
                success: function(res){
                    // store in local storage
                    setItem(ls_acquisition_all_line_chart, res.data.line_chart);

                    var channel = $('.nav-ga.active').data('channel');
                    loadGraph(channel, 'line_chart', ls_acquisition_all_line_chart);
                },
                error: function(jqXHR, error, errorThrown) {
                    $("#acquision_line_chart_loading_text").html(no_data_text);
                }
            });
        }

        // Acquisition all counts
        function getAcquisitionAllCount() {
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
                    displayCountLoading(loading_text);
                },
                success: function(res){
                    if(res.data.count){
                        setCountValues(res.data.count);
                    }
                },
                error: function(jqXHR, error, errorThrown) {
                    if($('.nav-ga.active').data('channel') == 'gaAcquisitionAll'){
                        displayCountLoading(no_data_text);
                    }
                }
            });
        }

        // Acquisition channels counts
        function getAcquisitionChannelsCount() {
            $.ajax({
                url: "{{ route('analytics.google-analytics.ajax', ['type' => 'acquisition-channels-counts']) }}",
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
                    // store in local storage
                    setItem(ls_acquisition_channels_counts, res.data);
                },
                error: function(jqXHR, error, errorThrown) {
                    if($('.nav-ga.active').data('channel') != 'gaAcquisitionAll'){
                        displayCountLoading(no_data_text);
                    }
                }
            });
        }

        // Acquisition channels line charts
        function getAcquisitionChannelsLineCharts() {
            $.ajax({
                url: "{{ route('analytics.google-analytics.ajax', ['type' => 'acquisition-channels-line-charts']) }}",
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
                    setItem(ls_acquisition_channels_line_charts, res.data);
                },
                error: function(jqXHR, error, errorThrown) {
                    if($('.nav-ga.active').data('channel') != 'gaAcquisitionAll'){
                        $("#acquision_line_chart_loading_text").html(no_data_text);
                    }
                }
            });
        }

        // Acquisition channels pie charts
        function getAcquisitionChannelsPieCharts() {
            $.ajax({
                url: "{{ route('analytics.google-analytics.ajax', ['type' => 'acquisition-channels-pie-charts']) }}",
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
                    setItem(ls_acquisition_channels_pie_charts, res.data);
                },
                error: function(jqXHR, error, errorThrown) {
                    if($('.nav-ga.active').data('channel') != 'gaAcquisitionAll'){
                        $("#acquision_pie_chart_loading_text").html(no_data_text);
                    }
                }
            });
        }

        // Datatable
        function loadDataTable(channel = null) {
            displayCountLoading(loading_text);

            if(channel == null) {
                var channel = $('.nav-ga.active').data('channel');
            }

            if(channel == 'gaAcquisitionAll'){
                var dataTableId     =   'acquisition_all_table';
                var dataTableType   =   'acquisition-all-rows';
                var channelType     =   null;
                var columns         =   [ { data: 'channels'} ];
                var title           =   "Channel";
            }else if(channel == 'gaAcquisitionOrganicSearch'){
                var dataTableId     =   'acquisition_organic_search_table';
                var dataTableType   =   'acquisition-organic-search-rows';
                var channelType     =   'Organic_Search';
                var columns         =   [ { data: 'keywords'} ];
                var title           =   "Keyword";
            }else if(channel == 'gaAcquisitionPaidSearch'){
                var dataTableId     =   'acquisition_paid_search_table';
                var dataTableType   =   'acquisition-paid-search-rows';
                var channelType     =   'Paid_Search';
                var columns         =   [ { data: 'keywords'} ];
                var title           =   "Keyword";
            }

            columns.push(
                { data: 'sessions' },
                { data: 'totalUsers' },
                { data: 'userEngagementDuration' },
                { data: 'screenPageViews' },
                { data: 'conversions'},
                { data: 'eventCount' }
            );

            // Count
            if(channel == 'gaAcquisitionOrganicSearch' || channel == 'gaAcquisitionPaidSearch'){
                var countData = getItem(ls_acquisition_channels_counts);
                if(countData && countData.count[channelType]){
                    setCountValues(countData.count[channelType]);
                }else{
                    displayCountLoading(no_data_text);
                }
            }

            // Data Table
            $(".data-table-div").html('<table class="table table-bordered table-hover table-checkable" id="'+dataTableId+'" style="margin-top: 13px !important"></table>');
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
                        return dataSrc(response, channel, channelType);
                    },
                    data    :   {
                        _token: '{{csrf_token()}}',
                        start_date,
                        end_date,
                        page_ids
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        $('#'+dataTableId).DataTable().clear().draw();

                        if(dataTableType == 'acquisition-all-rows'){
                            $("#acquision_pie_chart_loading_text").html(no_data_text);
                        }
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
                order: [ [1, 'desc'] ]
            });
        }

        // Data table response
        function dataSrc(response, channel, channelType = null) {
            var returnData = response.data.rows;
            if(channelType){
                returnData = response.data.rows[channelType];
            }

            if(channel == 'gaAcquisitionAll'){
                setItem(ls_acquisition_all_pai_chart, response.data.pie_chart);
                loadGraph(channel, 'pie_chart', ls_acquisition_all_pai_chart);
            }

            return returnData;
        }

        // Graph
        function loadGraph(channel, graphType, graphKey, selector = null){
            var is_error = true;
            if(selector == null){
                var selector = $(".section-count-active").find('.section-count');
            }

            selector.parents('.section-count-main-div').siblings().find('.section-count-active').toggleClass('section-count-active');
            selector.parent('.card').addClass('section-count-active');
            var elementTitle    =   selector.find('.count-title').html() ?? 'details';
            var chat_details    =   getItem(graphKey);

            if(chat_details && selector.find('.loading-text').text() != 0) {
                var element     =   selector.data('element');

                if(channel == 'gaAcquisitionAll'){
                    var chart_element = chat_details;
                }else if(channel == 'gaAcquisitionOrganicSearch'){
                    var chart_element = chat_details[graphType]['Organic_Search'];
                }else if(channel == 'gaAcquisitionPaidSearch'){
                    var chart_element = chat_details[graphType]['Paid_Search'];
                }

                if(chart_element && chart_element[element]){
                    if(graphType == 'pie_chart'){
                        is_error = false;
                        $("#acquision_pai_chart").css("display", "");
                        $("#acquision_pie_chart_loading_text").parent('div').css("display","none");
                        acquision_pai_chart(elementTitle, chart_element[element]);
                    }
                    if(graphType == 'line_chart'){
                        is_error = false;
                        $("#acquision_line_chart").css("display", "");
                        $("#acquision_line_chart_loading_text").parent('div').css("display","none");
                        acquision_line_chart(elementTitle, chart_element[element]);
                    }
                }
            }

            if(is_error){
                if(graphType == 'pie_chart'){
                    $("#acquision_pai_chart").empty();
                    $("#acquision_pai_chart").css("display", "none");
                    $("#acquision_pie_chart_loading_text").html('No '+elementTitle+' found for your date range');
                    $("#acquision_pie_chart_loading_text").parent('div').css("display","");
                }
                if(graphType == 'line_chart'){
                    $("#acquision_line_chart").empty();
                    $("#acquision_line_chart").css("display", "none");
                    $("#acquision_line_chart_loading_text").html('No '+elementTitle+' found for your date range');
                    $("#acquision_line_chart_loading_text").parent('div').css("display","");
                }
            }
        }

        // Display loading graph
        function displayGraphLoading() {
            $(".line-chart-div").empty();
            $(".line-chart-div").html( '<div class="plateform_device_graph_container">'+
                '<div id="acquision_line_chart" style="height: 350px; width: 100%; display: none;"></div>'+
                '</div>'+
                '<div class="align-items-center justify-content-center no-data">'+
                '<h2 id="acquision_line_chart_loading_text" class="d-flex justify-content-center loading-text">Loading...</h2>'+
                '</div>');

            $(".pie-chart-div").empty();
            $(".pie-chart-div").html('<div class="plateform_device_graph_container">'+
                '<div id="acquision_pai_chart" style="height: 350px; width: 100%; display: none;"></div>'+
                '</div>'+
                '<div class="align-items-center justify-content-center no-data">'+
                '<h2 id="acquision_pie_chart_loading_text" class="d-flex justify-content-center loading-text">Loading...</h2>'+
                '</div>');
        }

        // Display loading count
        function displayCountLoading(loading_text) {
            $("#count_sessions").html(loading_text);
            $("#count_totalUsers").html(loading_text);
            $("#count_userEngagementDuration").html(loading_text);
            $("#count_screenPageViews").html(loading_text);
            $("#count_conversions").html(loading_text);
            $("#count_eventCount").html(loading_text);
        }

        // Change count values
        function setCountValues(count) {
            $("#count_sessions").html(count.sessions);
            $("#count_totalUsers").html(count.totalUsers);
            $("#count_userEngagementDuration").html(count.userEngagementDuration);
            $("#count_screenPageViews").html(count.screenPageViews);
            $("#count_conversions").html(count.conversions);
            $("#count_eventCount").html(count.eventCount);
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
    });
</script>
@endpush