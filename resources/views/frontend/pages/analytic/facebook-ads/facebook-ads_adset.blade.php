<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader"></div>
<div class="container">
    <div class="row post-cards">
        {{-- Organic VS Paid Likes --}}
        <div class="col-lg-6 col-md-6 col-12">
            {{-- <div class="row">
                <div class="col-lg-6 col-md-12 col-12">
                    <div class="card">
                        <div class="card-header pb-0 ">
                            <h2 class="text-bold-700">Amount Spent</h2>
                        </div>
                        <div class="card-body">
                            <h2 class="text-bold-700 text-center loading-text" id="adset_total_amount_spent_count"
                                style="color:#E77F01">
                                Loading...
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-12">
                    <div class="card">
                        <div class="card-header pb-0 ">
                            <h2 class="text-bold-700">Total Clicks</h2>
                        </div>
                        <div class="card-body">
                            <h2 class="text-bold-700 text-center loading-text" id="adset_clicks_count"
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
                            <h2 class="text-bold-700 text-center loading-text" id="adset_impression_count"
                                style="color:#E77F01">
                                Loading...
                            </h2>
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="row">
                <div class="col-lg-6 col-md-12 col-12">
                    <div class="card">
                        <div class="card-header pb-0 ">
                            <h2 class="text-bold-700">Total Costs</h2>
                        </div>
                        <div class="card-body">
                            <h2 class="text-bold-700 text-center loading-text" id="adset_total_amount_spent_count"
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
                            <h2 class="text-bold-700 text-center loading-text" id="adset_impression_count"
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
                            <h2 class="text-bold-700 text-center loading-text" id="adset_total_leads_count"
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
                            <h2 class="text-bold-700 text-center loading-text" id="adset_average_cpl_count"
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
                <div class="card-body" id="adset_facebook_ads_publisher_platform_graph_container">
                    <div class="" id="adset_facebook_ads_publisher_platform_graph" style="display: none">
                        <div id="adset_facebook_ads_publisher_platform" class="d-flex justify-content-center"
                            style="height: 140px;max-height: 140px;"></div>
                    </div>
                    <div class="align-items-center justify-content-center no-data"
                        id="adset_facebook_ads_publisher_platform_loading_text">
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
                            <h2 class="text-bold-700 text-center loading-text" id="adset_clicks_count"
                                style="color:#E77F01">
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
                            <h2 class="text-bold-700 text-center loading-text" id="adset_average_cpc_count"
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
                            <h2 class="text-bold-700 text-center loading-text" id="adset_ctr_count"
                                style="color:#E77F01">
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
                    <div id="adset_facebook_ads_clicks_line_graph_container" style="display: none">
                        <canvas id="adset_facebook_ads_clicks_line_chart" height="150"></canvas>
                    </div>
                    <div class="align-items-center justify-content-center no-data"
                        id="adset_facebook_ads_clicks_loading_text_container">
                        <h2 id="adset_facebook_ads_clicks_loading_text"
                            class="d-flex justify-content-center loading-text">
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
                    <div class="card-body" id="facebookAdsDataTableContainer">
                        {{-- <table class="table table-bordered table-hover table-checkable fs-3"
                            id="campaignAdsAdsetTable" style="margin-top: 13px !important;"></table> --}}
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
    var adset_facebook_ads_publisher_plateform =  (lables,values, graph_id=null) => {
        const apexChart = `#${graph_id}`;
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
            colors: ['#3755A9','#E77F01','#F2B1E4','#FCEB62']
        };

        var chart = new ApexCharts(document.querySelector(apexChart), options);
        chart.render();
    }

    var adset_facebook_ads_clicks =  (lables,values,graph_id=null) => {
        var labels = lables;
        var value =  values;
        var ctx = document.getElementById(`${graph_id}`).getContext('2d');
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

        var campaign_id = null;

        $(document).on('click','#campaignName',function(){
            campaign_id = $(this).data('campaign-id');
            setAdsetDataTable();
            getSingleAdsetPublisherPlateformPieChartData();
            getSingalAdsetClicksLineChartData();
        });


        var urlParams           =   new URLSearchParams(window.location.search);
        var page_ids            =   urlParams.get('page_id');
        var start_date          =   urlParams.get('start_date');
        var end_date            =   urlParams.get('end_date');

        const request = {
            page_ids,start_date,end_date
        };

        const loading_text                          =   'Loading...';
        const no_data_text                          =   'No details found';

        var localstorage_request = getItem('fb_Ads_request');
        initialLoading();


        function initialLoading(){
            setAdsetDataTable();
            getAdsCounts();
            getInitialAdsetPublisherPlateformPieChartData();
            getInitialAdsetClicksLineChartData();
        }

        function displayCountLoading(loading_text) {
            $("#adset_clicks_count").html(loading_text);
            $("#adset_impression_count").html(loading_text);
            $("#adset_ctr_count").html(loading_text);
            $("#adset_average_cpc_count").html(loading_text);
            $("#adset_average_cpl_count").html(loading_text);
            $("#adset_total_leads_count").html(loading_text);
            $("#adset_total_amount_spent_count").html(loading_text);
        }

        function setAdsetCounts(counts){
            $("#adset_clicks_count").html(counts.clicks_count);
            $("#adset_impression_count").html(counts.impressions_count);
            $("#adset_ctr_count").html(counts.ctr_count);
            $("#adset_average_cpc_count").html(counts.average_cpc_count);
            $("#adset_average_cpl_count").html(counts.average_cpl_count);
            $("#adset_total_leads_count").html(counts.total_leads_count);
            $("#adset_total_amount_spent_count").html(counts.total_amount_spent_count);
        }

        function getAdsCounts(){
            var counts = getItem('fb_Ads_total_count');
            if((counts == null || counts == undefined) && checkRequestInLocalStorage()){
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
                        // setItem(ls_acquisition_channels_pie_charts, res.data);
                        setAdsetCounts(res.data.total_counts);
                    },
                    error: function(jqXHR, error, errorThrown) {
                        displayCountLoading(no_data_text);
                    }
                });
            }else{
                setAdsetCounts(counts);
            }
        }

        // Get Chart data Initially form the local storage  if it is there otherwise get from the APIS.
        function getInitialAdsetPublisherPlateformPieChartData(){
            var sessionPublisherPlateformPieChartData = getItem('fb_Ads_publisher_platform_pie_chart');

            if((sessionPublisherPlateformPieChartData == null || sessionPublisherPlateformPieChartData == undefined) && checkRequestInLocalStorage()){
                $.ajax({
                    url: "{{ route('analytics.facebook_ads.ajax', ['type' => 'campaigns_publisher_plateform_pie_chart']) }}",
                    type: 'POST',
                    data: {
                        _token: '{{csrf_token()}}',
                        page_ids,
                        start_date,
                        end_date,
                        campaign_id
                    },
                    async: true,
                    cache: false,
                    success: function(res){
                        var data = res.data.values.clicks;

                        var lables = $.map(data, function(element,index) {return index});
                        var values = $.map(data, function(element,index) {return element});
                        var graph_id = 'adset_facebook_ads_publisher_platform';
                        // setItem(ls_acquisition_channels_pie_charts, res.data);
                        // if(campaign_id !== null && campaign_id !== undefined){
                        //     $('#adset_facebook_ads_publisher_platform_graph').empty();
                        //     $('#adset_facebook_ads_publisher_platform_graph').html(`
                        //     <div id="adset_facebook_ads_single_campaign_publisher_platform" class="d-flex justify-content-center"
                        //         style="height: 140px;max-height: 140px;"></div>
                        //     `);
                        //     graph_id = 'adset_facebook_ads_single_campaign_publisher_platform';
                        // }else{
                        $('#adset_facebook_ads_publisher_platform_graph').css('display', 'block');
                        $('#adset_facebook_ads_publisher_platform_loading_text').css('display', 'none');
                        // }
                        adset_facebook_ads_publisher_plateform(lables,values,graph_id);
                    },
                    error: function(jqXHR, error, errorThrown) {
                        $('#adset_facebook_ads_publisher_platform_loading_text').html(no_data_text);
                    }
                });
            }else{

                $('#adset_facebook_ads_publisher_platform_graph').css('display', 'block');
                $('#adset_facebook_ads_publisher_platform_loading_text').css('display', 'none');

                var lables = $.map(sessionPublisherPlateformPieChartData.values.clicks, function(element,index) {return index});

                var values = $.map(sessionPublisherPlateformPieChartData.values.clicks, function(element,index) {return element});

                adset_facebook_ads_publisher_plateform(lables,values,'adset_facebook_ads_publisher_platform');
            }

        }
        // Get Chart data from the APIS of Single campaign.
        function getSingleAdsetPublisherPlateformPieChartData(){
            $('#adset_facebook_ads_publisher_platform_graph').css('display', 'none');
            $('#adset_facebook_ads_publisher_platform_loading_text').css('display', 'block');
            $.ajax({
                url: "{{ route('analytics.facebook_ads.ajax', ['type' => 'single_campaign_publisher_plateform_pie_chart']) }}",
                type: 'POST',
                data: {
                    _token: '{{csrf_token()}}',
                    page_ids,
                    start_date,
                    end_date,
                    campaign_id
                },
                async: true,
                cache: false,
                success: function(res){
                    var data = res.data.values.clicks;

                    var lables = $.map(data, function(element,index) {return index});
                    var values = $.map(data, function(element,index) {return element});
                    var graph_id = 'adset_facebook_ads_single_campaign_publisher_platform';
                    $('#adset_facebook_ads_publisher_platform_graph').css('display', 'block');
                    $('#adset_facebook_ads_publisher_platform_loading_text').css('display', 'none');
                    $('#adset_facebook_ads_publisher_platform_graph').empty();
                    $('#adset_facebook_ads_publisher_platform_graph').html(`
                    <div id="${graph_id}" class="d-flex justify-content-center"
                        style="height: 140px;max-height: 140px;"></div>
                    `);
                    adset_facebook_ads_publisher_plateform(lables,values,graph_id);
                },
                error: function(jqXHR, error, errorThrown) {
                    $('#adset_facebook_ads_publisher_platform_loading_text').html(no_data_text);
                }
            });
        }


        function getInitialAdsetClicksLineChartData(){
            var sessionLineChartData = getItem('fb_Ads_cliks_line_chart');
            var graph_id = 'adset_facebook_ads_clicks_line_chart';
            if((sessionLineChartData == null || sessionLineChartData == undefined) && checkRequestInLocalStorage() ){
                $.ajax({
                    url: "{{ route('analytics.facebook_ads.ajax', ['type' => 'campaigns_clicks_line_chart']) }}",
                    type: 'POST',
                    data: {
                        _token: '{{csrf_token()}}',
                        page_ids,
                        start_date,
                        end_date,
                        campaign_id
                    },
                    async: true,
                    cache: false,
                    success: function(res){
                        var data = res.data.values.clicks;
                        var lables = $.map(data, function(element) {return element.date});
                        var values = $.map(data, function(element) {return element.value});

                        $('#adset_facebook_ads_clicks_line_graph_container').css('display', '');
                        $('#adset_facebook_ads_clicks_loading_text_container').css('display', 'none');
                        adset_facebook_ads_clicks(lables,values,graph_id);
                    },
                    error: function(jqXHR, error, errorThrown) {
                        $('#adset_facebook_ads_clicks_loading_text').html(no_data_text);
                    }
                });
            }else{
                var lables = $.map(sessionLineChartData.values.clicks, function(element) {return element.date});
                var values = $.map(sessionLineChartData.values.clicks, function(element) {return element.value});

                $('#adset_facebook_ads_clicks_line_graph_container').css('display', '');
                $('#adset_facebook_ads_clicks_loading_text_container').css('display', 'none');
                adset_facebook_ads_clicks(lables,values,graph_id);
            }
        }

        function getSingalAdsetClicksLineChartData(){
            $('#adset_facebook_ads_clicks_line_graph_container').css('display', 'none');
            $('#adset_facebook_ads_clicks_loading_text_container').css('display', 'block');
            var graph_id = 'adset_single_facebook_ads_clicks_line_chart';
            $.ajax({
                url: "{{ route('analytics.facebook_ads.ajax', ['type' => 'single_campaign_clicks_line_chart']) }}",
                type: 'POST',
                data: {
                    _token: '{{csrf_token()}}',
                    page_ids,
                    start_date,
                    end_date,
                    campaign_id
                },
                async: true,
                cache: false,
                success: function(res){
                    var data = res.data.values.clicks;
                    var lables = $.map(data, function(element) {return element.date});
                    var values = $.map(data, function(element) {return element.value});
                    $('#adset_facebook_ads_clicks_line_graph_container').empty();
                    $('#adset_facebook_ads_clicks_line_graph_container').html(`
                    <canvas id="${graph_id}" height="150"></canvas>
                    `);
                    // setItem(ls_acquisition_channels_pie_charts, res.data);
                    $('#adset_facebook_ads_clicks_line_graph_container').css('display', '');
                    $('#adset_facebook_ads_clicks_loading_text_container').css('display', 'none');
                    adset_facebook_ads_clicks(lables,values,graph_id);
                },
                error: function(jqXHR, error, errorThrown) {
                    $('#adset_facebook_ads_clicks_loading_text').html(no_data_text);
                }
            });
        }

        function setAdsetDataTable(){
            var dataTableId = 'campaignAdsAdsetTable';
            if(campaign_id != undefined && campaign_id != null){
                dataTableId = `${campaign_id}campaignAdsTable`;
            }
            $("#facebookAdsDataTableContainer").html('<table class="table table-bordered table-hover table-checkable" id="'+dataTableId+'" style="margin-top: 13px !important"></table>');
            // var url = "{{ route('analytics.facebook_ads.ajax', ['type' => 'campaigns_table_rows']) }}";
            // $(`#${dataTableId}`).DataTable({
            //     responsive  :   true,
            //     processing  :   false,
            //     serverSide  :   false,
            //     searching   :   false,
            //     ordering    :   true,
            //     "bPaginate" :   true,
            //     "bInfo"     :   true,
            //     "info"      :   true,
            //     ajax: {
            //         type    :   'POST',
            //         url     :   url,
            //         dataSrc :   function (response) {
            //             return AdsetdataSrc(response);
            //         },
            //         data    :   {
            //             _token: '{{csrf_token()}}',
            //             start_date,
            //             end_date,
            //             page_ids
            //         },
            //         error: function(jqXHR, textStatus, errorThrown){
            //             $(`#${dataTableId}`).DataTable().clear().draw();
            //             // $("#acquision_pie_chart_loading_text").html(no_data_text);
            //         }
            //     },
            //     columns :   [
            //         { data:'campaign_name',},
            //         { data:'adset_name'},
            //         { data:'clicks',},
            //         { data:'impressions',},
            //         { data:'average_cpc',},
            //         { data:'ctr',},
            //         { data:'average_cpl',},
            //         { data:'on_facebook_lead',},
            //         // { data:'id',visible:'none'},
            //     ],
            //     columnDefs: [
            //         {
            //             targets     :   0,
            //             title       :   'Campaign',
            //             orderable   :   true
            //         },{
            //             targets     :   1,
            //             title       :   'AD Set',
            //             orderable   :   true
            //         },{
            //             targets     :   2,
            //             title       :   'Clicks',
            //             orderable   :   true
            //         },{
            //             targets     :   3,
            //             title       :   'Impression',
            //             orderable   :   true
            //         },{
            //             targets     :   4,
            //             title       :   'Average CPC',
            //             orderable   :   true
            //         },{
            //             targets     :   5,
            //             title       :   'CTR',
            //             orderable   :   true
            //         },{
            //             targets     :   6,
            //             title       :   'Cost Per Lead',
            //             orderable   :   true
            //         },{
            //             targets     :   7,
            //             title       :   'Leads (On-Facebook)',
            //             orderable   :   true
            //         }
            //     ],
            //     order: [ [0, 'asc']]
            // });
            $(`#${dataTableId}`).DataTable({
                responsive  :   true,
                processing  :   false,
                serverSide  :   false,
                searching   :   false,
                ordering    :   true,
                "bPaginate" :   true,
                "bInfo"     :   true,
                "info"      :   true,
                data : getDataTableRowData(),
                columns :   [
                    { data:'campaign_name',},
                    { data:'adset_name'},
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
                        title       :   'AD Set',
                        orderable   :   true
                    },{
                        targets     :   2,
                        title       :   'Clicks',
                        orderable   :   true
                    },{
                        targets     :   3,
                        title       :   'Impression',
                        orderable   :   true
                    },{
                        targets     :   4,
                        title       :   'Average CPC',
                        orderable   :   true
                    },{
                        targets     :   5,
                        title       :   'CTR',
                        orderable   :   true
                    },{
                        targets     :   6,
                        title       :   'Cost Per Lead',
                        orderable   :   true
                    },{
                        targets     :   7,
                        title       :   'Leads (On-Facebook)',
                        orderable   :   true
                    }
                ],
                order: [ [0, 'asc']]
            });
        }

        // Data table response
        function getDataTableRowData() {
            var tableRowData = getItem('fb_Ads_all_table_rows');
            if(tableRowData == null || tableRowData == undefined && checkRequestInLocalStorage()){
                return [];
            }else{
                if(campaign_id !== null && campaign_id !== undefined ){
                    var campaign;
                    campaign = tableRowData.filter(function($item){
                        return $item.campaign_id == campaign_id;
                    });

                    var counts = {
                        clicks_count:campaign[0].clicks,
                        impressions_count: campaign[0].impressions,
                        ctr_count: campaign[0].ctr,
                        average_cpc_count: campaign[0].average_cpc,
                        average_cpl_count: campaign[0].average_cpl,
                        total_leads_count: campaign[0].leads,
                        total_amount_spent_count: campaign[0].amount_spent,
                    }
                    setAdsetCounts(counts);
                    return campaign;
                }
                return tableRowData;
            }
        }

        function setItem(key, value){
            localStorage.setItem(key, JSON.stringify(value));
        }

        // Local storage get details
        function getItem(key){
            return JSON.parse(localStorage.getItem(key));
        }
        function checkRequestInLocalStorage(){
            console.log(request,localstorage_request);
            return JSON.stringify(request) === JSON.stringify(localstorage_request);
        }
        // function getSingleCampaignData(campaign_id){
        //     var tableRowData = localStorage.getItem('fb_Ads_all_table_rows');
        //     campaign = tableRowData.filter(function($item){
        //         return $item.campaign_id == campaign_id;
        //     });

        //     var counts = {
        //         clicks_count:campaign[0].clicks,
        //         impressions_count: campaign[0].impressions,
        //         ctr_count: campaign[0].ctr,
        //         average_cpc_count: campaign[0].average_cpc,
        //         average_cpl_count: campaign[0].average_cpl,
        //         total_leads_count: campaign[0].leads,
        //     }
        //     setAdsetCounts(counts);
        //     return campaign;
        // }


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