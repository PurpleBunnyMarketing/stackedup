<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader"></div>
<div class="container">
    <div class="row post-cards">
        {{-- Organic VS Paid Likes --}}
        <div class="col-lg-6 col-md-6 col-12">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-12">
                    <div class="card">
                        <div class="card-header pb-0 ">
                            <h2 class="text-bold-700">Total Costs</h2>
                        </div>
                        <div class="card-body">
                            <h2 class="text-bold-700 text-center loading-text" id="total_amount_spent_count"
                                style="color:#E77F01">
                                Loading...
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-12">
                    <div class="card mt-6 mt-lg-0">
                        <div class="card-header pb-0 ">
                            <h2 class="text-bold-700">Total Impression</h2>
                        </div>
                        <div class="card-body">
                            <h2 class="text-bold-700 text-center loading-text" id="impression_count"
                                style="color:#E77F01">
                                Loading...
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-6">

                <div class="col-lg-6 col-md-12 col-12">
                    <div class="card mt-6 mt-lg-0">
                        <div class="card-header pb-0 ">
                            <h2 class="text-bold-700">Total Leads</h2>
                        </div>
                        <div class="card-body">
                            <h2 class="text-bold-700 text-center loading-text" id="total_leads_count"
                                style="color:#E77F01">
                                Loading...
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-12">
                    <div class="card mt-6 mt-lg-0">
                        <div class="card-header pb-0 ">
                            <h2 class="text-bold-700">Average CPL</h2>
                        </div>
                        <div class="card-body">
                            <h2 class="text-bold-700 text-center loading-text" id="average_cpl_count"
                                style="color:#E77F01">
                                Loading...
                            </h2>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card mt-6">
                <div class="card-header  pb-0">
                    <h2 class="text-bold-700">Publisher Plateform</h2>
                </div>
                <div class="card-body" id="facebook_ads_publisher_platform_graph_container">
                    <div class="" id="facebook_ads_publisher_platform_graph" style="display: none">
                        <div id="facebook_ads_publisher_platform" class="d-flex justify-content-center"
                            style="height: 140px;max-height: 140px;"></div>
                    </div>
                    <div class="align-items-center justify-content-center no-data"
                        id="facebook_ads_publisher_platform_loading_text">
                        <h2 class="d-flex justify-content-center loading-text">Loading...</h2>
                    </div>
                </div>
            </div>
        </div>
        {{-- Total Likes --}}
        <div class="col-lg-6 col-md-6 mt-md-0 mt-6 col-12">
            <div class="row">

                <div class="col-lg-6 col-md-12 col-12">
                    <div class="card">
                        <div class="card-header pb-0 ">
                            <h2 class="text-bold-700">Total Clicks</h2>
                        </div>
                        <div class="card-body">
                            <h2 class="text-bold-700 text-center loading-text" id="clicks_count" style="color:#E77F01">
                                Loading...
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-12">
                    <div class="card">
                        <div class="card-header pb-0 ">
                            <h2 class="text-bold-700">Average CPC</h2>
                        </div>
                        <div class="card-body">
                            <h2 class="text-bold-700 text-center loading-text" id="average_cpc_count"
                                style="color:#E77F01">
                                Loading...
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-6">
                <div class="col-lg-6 col-md-12 col-12">
                    <div class="card">
                        <div class="card-header pb-0 ">
                            <h2 class="text-bold-700">CTR</h2>
                        </div>
                        <div class="card-body">
                            <h2 class="text-bold-700 text-center loading-text" id="ctr_count" style="color:#E77F01">
                                Loading...
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-6">
                <div class="card-header  pb-0">
                    <h2 class="text-bold-700">Clicks</h2>
                </div>
                <div class="card-body">
                    <div id="facebook_ads_clicks_line_graph_container" style="display: none">
                        <canvas id="facebook_ads_clicks_line_chart" height="140"></canvas>
                    </div>
                    <div class="align-items-center justify-content-center no-data"
                        id="facebook_ads_clicks_loading_text_container">
                        <h2 id="facebook_ads_clicks_loading_text" class="d-flex justify-content-center loading-text">
                            Loading...</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 mt-6 col-12">
            <div class="facebook-likes">
                <div class="card">
                    <div class="card-header pb-0 ">
                        <h2 class="text-bold-700">Campaigns</h2>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover table-checkable fs-3" id="campaignTable"
                            style="margin-top: 13px !important;"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('extra-js-scripts')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.min.js" type="text/javascript"></script>
<script>
    var facebook_ads_publisher_plateform =  (lables,values) => {
        const apexChart = "#facebook_ads_publisher_platform";
        var options = {
            plotOptions:{
                pie:{
                    dataLabels:{
                        offset: -18,
                        minAngleToShowLabel: 5
                    },
                },
            },
            series:values,
            chart: {
                width: 380,
                type: 'pie',
            },
            legend : {
                show: true,
                position:'bottom'
            },
            dataLabels: {
                style: {
                    fontSize: '40px',
                },
            },
            labels : lables,
            responsive: [{
                // breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'middle'
                    }
                }
            }],
            colors: ['#3755A9','#E77F01','#F8B478','#FCEB62']
        };

        var chart = new ApexCharts(document.querySelector(apexChart), options);
        chart.render();
    }

    var facebook_ads_clicks =  (lables,values) => {
        var labels = lables;
        var value =  values;
        var ctx = document.getElementById("facebook_ads_clicks_line_chart").getContext('2d');
        var twitterFollower = new Chart(ctx, {
        type: 'line',
        data: {
                labels: labels,
                datasets: [{
                    label: 'Clicks',
                    data: value,
                    fill: false,
                    borderColor: "#E77F01",
                    tension: 0.5
                }]
            },
            options: {
                scales: {
                yAxes: [{
                    ticks: {
                    beginAtZero: true,
                    callback: function(value) {if (value % 1 === 0) {return value;}}
                    }
                }],
                xAxes: [{
                    gridLines: {
                        display: false
                    },
                    ticks: {
                        autoSkip: true,
                        callback: (t, i) => i % 3 ? '' : labels[i]
                    }
                }]
                }
            }
        });
	}


    jQuery(document).ready(function() {

        var urlParams           =   new URLSearchParams(window.location.search);
        var page_ids            =   urlParams.get('page_id');
        var start_date          =   urlParams.get('start_date');
        var end_date            =   urlParams.get('end_date');
        const request = {
            page_ids,start_date,end_date
        };

        setItem('fb_Ads_request',request);

        const loading_text                          =   'Loading...';
        const no_data_text                          =   'No details found';

        initialAdsetLoading();

        function initialAdsetLoading(){
            setDataTable();
            getCounts();
            getPublisherPlateformPieChartData();
            getClicksLineChartData();
        }

        function displayCountLoading(loading_text) {
            $("#clicks_count").html(loading_text);
            $("#impression_count").html(loading_text);
            $("#ctr_count").html(loading_text);
            $("#average_cpc_count").html(loading_text);
            $("#average_cpl_count").html(loading_text);
            $("#total_leads_count").html(loading_text);
            $("#total_amount_spent_count").html(loading_text);
        }

        function setCounts(counts){
            $("#clicks_count").html(counts.clicks_count);
            $("#impression_count").html(counts.impressions_count);
            $("#ctr_count").html(counts.ctr_count);
            $("#average_cpc_count").html(counts.average_cpc_count);
            $("#average_cpl_count").html(counts.average_cpl_count);
            $("#total_leads_count").html(counts.leads_count);
            $("#total_amount_spent_count").html(counts.total_amount_spent_count);
        }

        function getCounts(){
            $.ajax({
                url: "{{ route('analytics.facebook_ads.ajax', ['type' => 'campaigns_total_counts']) }}",
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
                    setItem('fb_Ads_total_count', res.data.total_counts);
                    setCounts(res.data.total_counts);
                },
                error: function(jqXHR, error, errorThrown) {
                    removeItem('fb_Ads_total_count')
                    displayCountLoading(no_data_text);
                }
            });
        }

        function getPublisherPlateformPieChartData(){
            $.ajax({
                url: "{{ route('analytics.facebook_ads.ajax', ['type' => 'campaigns_publisher_plateform_pie_chart']) }}",
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
                    var data = res.data.values.clicks;

                    var lables = $.map(data, function(element,index) {return index});
                    var values = $.map(data, function(element,index) {return element});

                    setItem('fb_Ads_publisher_platform_pie_chart', res.data);

                    $('#facebook_ads_publisher_platform_graph').css('display', 'block');
                    $('#facebook_ads_publisher_platform_loading_text').css('display', 'none');
                    facebook_ads_publisher_plateform(lables,values);
                },
                error: function(jqXHR, error, errorThrown) {
                    removeItem('fb_Ads_publisher_platform_pie_chart')
                    $('#facebook_ads_publisher_platform_loading_text').html(no_data_text);
                }
            });
        }

        function getClicksLineChartData(){
            $.ajax({
                url: "{{ route('analytics.facebook_ads.ajax', ['type' => 'campaigns_clicks_line_chart']) }}",
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
                    var data = res.data.values.clicks;
                    var lables = $.map(data, function(element) {return element.date});
                    var values = $.map(data, function(element) {return element.value});

                    setItem('fb_Ads_cliks_line_chart', res.data);
                    $('#facebook_ads_clicks_line_graph_container').css('display', '');
                    $('#facebook_ads_clicks_loading_text_container').css('display', 'none');
                    facebook_ads_clicks(lables,values);
                },
                error: function(jqXHR, error, errorThrown) {
                    removeItem('fb_Ads_cliks_line_chart')
                    $('#facebook_ads_clicks_loading_text').html(no_data_text);
                }
            });
        }

        function setDataTable(){
            var url = "{{ route('analytics.facebook_ads.ajax', ['type' => 'campaigns_table_rows']) }}";
            $('#campaignTable').DataTable({
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
                        return dataSrc(response);
                    },
                    data    :   {
                        _token: '{{csrf_token()}}',
                        start_date,
                        end_date,
                        page_ids
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        $('#campaignTable').DataTable().clear().draw();
                        // $("#acquision_pie_chart_loading_text").html(no_data_text);
                    }
                },
                columns :   [
                    {
                        data:'campaign_name',
                        render:function(data, type, full, meta){
                            return `<a href="javascript:void(0)" id="campaignName" data-campaign-id="${full.campaign_id}">${data}</a>`;
                        }
                    },
                    { data:'clicks',},
                    { data:'impressions',},
                    { data:'average_cpc',},
                    { data:'ctr',},
                    { data:'average_cpl',},
                    { data:'on_facebook_lead',},
                    // { data:'id',visible:'none'},
                ],
                columnDefs: [
                    {
                        targets     :   0,
                        title       :   'Campaign',
                        orderable   :   true
                    },{
                        targets     :   1,
                        title       :   'Clicks',
                        orderable   :   true
                    },{
                        targets     :   2,
                        title       :   'Impression',
                        orderable   :   true
                    },{
                        targets     :   3,
                        title       :   'Average CPC',
                        orderable   :   true
                    },{
                        targets     :   4,
                        title       :   'CTR',
                        orderable   :   true
                    },{
                        targets     :   5,
                        title       :   'Cost Per Lead',
                        orderable   :   true
                    },{
                        targets     :   6,
                        title       :   'Leads (On-Facebook)',
                        orderable   :   true
                    }
                ],
                order: [ [2, 'desc']]
            });
        }

        // Data table response
        function dataSrc(response) {
            if(response.status !== 200){
                removeItem('fb_Ads_all_table_rows')
            }
            var tableRowData = response.data.rows;
            setItem('fb_Ads_all_table_rows', tableRowData);
            return tableRowData;
        }
        // Local storage set details
        function setItem(key, value){
            localStorage.setItem(key, JSON.stringify(value));
        }

        // Local storage get details
        function getItem(key){
            return JSON.parse(localStorage.getItem(key));
        }

        function removeItem(key){
            localStorage.removeItem(key)
        }
    });


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

</script>
@endpush