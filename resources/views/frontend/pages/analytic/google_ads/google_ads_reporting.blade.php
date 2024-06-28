<style>
    .clickable_active_count {
        /* border: 2px solid rgb(231, 127, 1); */
    }

    .clickable_count_card {
        /* cursor: pointer; */
    }
</style>
<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader">
    <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <div class="d-flex align-items-center flex-wrap mr-1" id="ads_tab_container">
            <a href="javascript:;" class="google_ads_tabs post-link active btn font-size-h3
                    font-weight-bold my-2 mr-1" data-type="gadsCampaign">Campaign</a>
            <a href="javascript:;" class="google_ads_tabs post-link btn font-size-h3
                    font-weight-bold my-2 mr-1" data-type="gadsKeyword">Keyword</a>
            <a href="javascript:;" class="google_ads_tabs post-link btn font-size-h3
                    font-weight-bold my-2 mr-1" data-type="gadsSearchTerms">Search Terms</a>
            <a href="javascript:;" class="google_ads_tabs post-link btn font-size-h3
                    font-weight-bold my-2 mr-1" data-type="gadsConversion">Conversion</a>
        </div>
    </div>
</div>
<div class="container">
    <div class="row post-cards">

        <div class="col-lg-12 col-md-12 col-12">
            <div class="card">
                <div class="card-header  pb-0">
                    <h2 class="text-bold-700" id="gads_line_graph_title">Clicks</h2>
                </div>
                <div class="card-body" id="">
                    <div id="gads_line_graph_container" style="display: none;">
                        <canvas id="gads_line_graph" height="90" class="d-flex justify-content-center"></canvas>
                    </div>
                    <div class="align-items-center justify-content-center no-data " id="gads_line_graph_loading">
                        <h2 class="text-bold-700" id="gads_line_graph_loading_text">Loading...</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Counts --}}
    <div class="row post-cards mt-6" id="clickable_count_container">
        <div class="col-lg-3 col-md-3 col-12" data-count-category="conversions">
            <div class="card mt-6">
                <div class="card-header pb-0">
                    <h4 class="">View-Through Conv.</h4>
                </div>
                <div class="card-body text-center clickable_count_card " data-count-type="view_through_conversion">
                    <div class="align-items-center justify-content-center no-data click-default ">
                        <h2 class="text-bold-700 mt-1" id="count_viewThroughConv">Loading...</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-12" data-count-category="general">
            <div class="card mt-6">
                <div class="card-header pb-0">
                    <h4 class="">Avg CPC</h4>
                </div>
                <div class="card-body text-center clickable_count_card " data-count-type="avg_cpc">
                    <div class="align-items-center justify-content-center no-data click-default ">
                        <h2 class="text-bold-700 mt-1 calls_clicks_loading" id="count_averageCpc">Loading..</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-12" data-count-category="general">
            <div class="card mt-6">
                <div class="card-header pb-0">
                    <h4 class="">Clicks</h4>
                </div>
                <div class="card-body text-center clickable_count_card" data-count-type="clicks">
                    <div class="align-items-center justify-content-center no-data click-default ">
                        <h2 class="text-bold-700 mt-1 calls_clicks_loading" id="count_clicks">Loading... </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-12" data-count-category="general">
            <div class="card mt-6">
                <div class="card-header pb-0">
                    <h4 class="">Conversion Rate</h4>
                </div>
                <div class="card-body text-center clickable_count_card" data-count-type="conversion_rate">
                    <div class="align-items-center justify-content-center no-data click-default ">
                        <h2 class="text-bold-700 mt-1 calls_clicks_loading" id="count_conversionRate"> Loading...
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-12" data-count-category="conversions">
            <div class="card mt-6">
                <div class="card-header pb-0">
                    <h4 class="">Conversions</h4>
                </div>
                <div class="card-body text-center clickable_count_card" data-count-type="conversion">
                    <div class="align-items-center justify-content-center no-data click-default ">
                        <h2 class="text-bold-700 mt-1 calls_clicks_loading" id="count_conversions"> Loading </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-12" data-count-category="general">
            <div class="card mt-6">
                <div class="card-header pb-0">
                    <h4 class="">Cost</h4>
                </div>
                <div class="card-body text-center clickable_count_card" data-count-type="cost">
                    <div class="align-items-center justify-content-center no-data click-default ">
                        <h2 class="text-bold-700 mt-1 calls_clicks_loading" id="count_costs"> Loading... </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-12" data-count-category="general">
            <div class="card mt-6">
                <div class="card-header pb-0">
                    <h4 class="">Cost / Conversion</h4>
                </div>
                <div class="card-body text-center clickable_count_card" data-count-type="cost_per_conversion">
                    <div class="align-items-center justify-content-center no-data click-default ">
                        <h2 class="text-bold-700 mt-1 calls_clicks_loading" id="count_costPerConversion">Loading
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-3 col-12" data-count-category="general">
            <div class="card mt-6">
                <div class="card-header pb-0">
                    <h4 class="">Impressions</h4>
                </div>
                <div class="card-body text-center clickable_count_card" data-count-type="impressoin">
                    <div class="align-items-center justify-content-center no-data click-default ">
                        <h2 class="text-bold-700 mt-1 calls_clicks_loading" id="count_impressions"> Loading... </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Table --}}
    <div class="row post-cards">
        <div class="col-lg-12 col-md-12 mt-6 col-12">
            <div class="card">
                <div class="card-body data-table-div">
                    {{-- Datatable Start --}}
                    <table class="table table-bordered table-hover table-checkable" id="acquisition_campaign_table"
                        style="margin-top: 13px !important"></table>
                    {{-- Datatable End --}}
                </div>
            </div>
        </div>
    </div>
</div>

@push('extra-js-scripts')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
    // function gads_bar_graph (){
    //     var ctx = document.getElementById("gads_bar_graph").getContext('2d');
    //     var labels = ['1 Jun','2 Jun','3 Jun','4 Jun','5 Jun','6 Jun','6 Jun','7 Jun','9 Jun','10 Jun','11 Jun','12 Jun','13 Jun','14 Jun','15 Jun','16 Jun','17 Jun','18 Jun','19 Jun','20 Jun'];
    //     var values = [1,10,5,12,7,6,1,3,9,1,11,2,13,5,15,9,17,18,19,2];
    //     Chart.defaults.global.legend.display = false;
    //     var twitterFollower = new Chart(ctx, {
    //     type: 'bar',
    //     data: {
    //             labels: labels,
    //             datasets: [{
    //                 label: 'Interactions',
    //                 data: values,
    //                 backgroundColor: "#E77F01"
    //             }]
    //         },
    //         options: {
    //             scales: {
    //             yAxes: [{
    //                 ticks: {
    //                     beginAtZero: true,
    //                     callback: function(value) {if (value % 1 === 0) {return value;}}
    //                 }
    //             }],
    //             xAxes: [{
    //                 gridLines: {
    //                     display: false
    //                 },
    //                 ticks: {
    //                     autoSkip: true,
    //                     callback: (t, i) => i % 2 ? '' : labels[i]
    //                 }
    //             }]
    //             }
    //         }
    //     });
    // }

    jQuery(document).ready(function() {

        // $("#filterfrm").validate({
        //     rules: {
        //         start_date: "required",
        //         end_date: "required"
        //     },
        //     messages: {
        //         start_date: "Please enter start date",
        //         end_date: "Please enter end date"
        //     },
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

        var urlParams           =   new URLSearchParams(window.location.search);
        var page_ids            =   urlParams.get('page_id');
        var start_date          =   urlParams.get('start_date');
        var end_date            =   urlParams.get('end_date');
        var start_date_time     =   new Date(start_date).getTime();
        var end_date_time       =   new Date(end_date).getTime();

        const loading_text                          =   'Loading...';
        const no_data_text                          =   'No details found';
        const google_ads_counts                     =   getKeyName('google_ads_counts');
        const google_ads_line_chart                 =   getKeyName('google_ads_line_chart');

        initialDataLoadings();

        // Intial loading
        function initialDataLoadings() {
            loadDataTable();
            getLineChartData();
        }

        $(document).on('click','.google_ads_tabs',function(){
            var click_type = $(this).data('type');
            var selected_tab = $('#ads_tab_container').find('.active');
            var selected_count_card = $('#clickable_count_container').find('.clickable_active_count');
            var selected_count_card_type = selected_count_card.find('.clickable_count_card').data('type');
            if(selected_tab.data('type') != click_type){
                $(selected_tab).removeClass('active');
                $(this).addClass('active');
                loadDataTable($(this).data('type'));
                var count_cards = $('#clickable_count_container').children();
                changeLayout(count_cards,click_type);

            }
        })
        function  changeLayout(cards,type){
            if(type == 'gadsConversion'){
                $(cards).each(function(){
                    if($(this).data('count-category') == 'conversions'){
                        $(this).attr('class', 'col-lg-6 col-md-6 col-12');
                        $(this).css('display', '');
                    }else{
                        $(this).css('display', 'none');
                    }
                });
                $('#gads_line_graph_title').text('Conversion');
            }else{
                $(cards).each(function(){
                    $(this).attr('class', 'col-lg-3 col-md-3 col-12');
                    $(this).css('display', '');
                });
                $('#gads_line_graph_title').text('Clicks');
            }
        }

        // $(document).on('click','.clickable_count_card',function(){
        //     var click_count_type = $(this).data('count-type');
        //     var selected_count_card = getSelectedCountCard();
        //     var selected_count_card_type = selected_count_card.find('.clickable_count_card').data('count-type');
        //     var selected_tab_type = getSelectedTabType();

        //     if(selected_count_card_type != click_count_type){
        //         $(selected_count_card).removeClass('clickable_active_count');
        //         $(this).parent('.card').addClass('clickable_active_count');

        //         $('#gads_line_graph_title,#gads_bar_graph_title').text($(this).siblings('.card-header').text());
        //         // updateData(selected_tab_type,selected_count_card_type,start_date,end_date,page_ids);
        //     }
        // });

        // Display loading count
        function displayCountLoading(loading_text) {
            $("#count_viewThroughConv").html(loading_text);
            $("#count_averageCpc").html(loading_text);
            $("#count_clicks").html(loading_text);
            $("#count_conversionRate").html(loading_text);
            $("#count_conversions").html(loading_text);
            $("#count_costs").html(loading_text);
            $("#count_costPerConversion").html(loading_text);
            $("#count_impressions").html(loading_text);
        }

        // Change count values
        function setCountValues(count) {
            $("#count_viewThroughConv").html(count.viewThroughConv);
            $("#count_averageCpc").html(count.averageCPC);
            $("#count_clicks").html(count.clicks);
            $("#count_conversionRate").html(count.conversionRate);
            $("#count_conversions").html(count.conversion);
            $("#count_costs").html(count.cost);
            $("#count_costPerConversion").html(count.costPerConversion);
            $("#count_impressions").html(count.impressions);
        }

        // Load the DataTable as per the Selected Tab
        function loadDataTable(type = null){
            displayCountLoading(loading_text);
            displayLineChartLoading(loading_text);
            if(type == null) {
                var type = $('.google_ads_tabs.active').data('type');
            }

            let columns = [];
            let columnsDef = [];
            var orderableColumn = 3;
            var dataTableId = dataTableType = channelType = '';
            if(type == 'gadsConversion'){
                var dataTableId     =   'google_ads_conversion_table';
                var dataTableType   =   'gads_conversion_rows';
                var channelType     =   'Conversions';
                orderableColumn = 1;
                columns.push({data: 'conversion_name'} , { data: 'view-through-conv'}, {data: 'conversion'});
                columnsDef.push(
                    {targets : 0, title : 'Conversion Name', orderable : true},
                    {targets : 1, title : 'View-Through Conv.', orderable : true},
                    {targets : 2, title : 'Conversion', orderable : true},
                );
            }else{
                if(type == 'gadsCampaign'){
                    var dataTableId     =   'google_ads_campaign_table';
                    var dataTableType   =   'gads_campaigns_rows';
                    var channelType     =   "Campaign";
                    columns.push({ data: 'campaign'});
                    columnsDef.push({targets     :   0, title       :   'Campaign', orderable   :   true});
                }
                else if(type == 'gadsKeyword'){
                    var dataTableId     =   'google_ads_keyword_table';
                    var dataTableType   =   'gads_keywords_rows';
                    var channelType     =   'Keyword';
                    columns.push({ data: 'keyword'});
                    columnsDef.push({targets     :   0, title       :   'Keyword', orderable   :   true});
                }
                else if(type == 'gadsSearchTerms'){
                    var dataTableId     =   'google_ads_search_terms_table';
                    var dataTableType   =   'gads_search_terms_rows';
                    var channelType     =   'Search Terms';
                    columns.push({ data: 'search_term'});
                    columnsDef.push({targets     :   0, title       :   'Search Terms', orderable   :   true});
                }

                columns.push(
                    { data: 'view-through-conv' },
                    { data: 'averageCPC' },
                    { data: 'clicks' },
                    { data: 'conversion_rate' },
                    { data: 'conversion'},
                    { data: 'cost'},
                    { data: 'cost_per_conversion' },
                    { data: 'impressions' },
                );
                columnsDef.push(
                    {targets     :   1, title      :   'View-Through Conv.', orderable   :   true},
                    {targets     :   2,title       :   'Average CPC',orderable   :   true},
                    {targets     :   3,title       :   'Clicks',orderable   :   true},
                    {targets     :   4,title       :   'Conversion Rate',orderable   :   true},
                    {targets     :   5,title       :   'Conversion',orderable   :   true},
                    {targets     :   6,title       :   'Cost',orderable   :   true},
                    {targets     :   7,title       :   'Cost / Conversion',orderable   :   true},
                    {targets     :   8,title       :   'Impression',orderable   :   true},
                );
            }


            // Data Table
            $(".data-table-div").html('<table class="table table-bordered table-hover table-checkable" id="'+dataTableId+'" style="margin-top: 13px !important"></table>');
            var url =  "{{ route('analytics.google-ads.ajax', ['type' => 'dataTableType']) }}";
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
                        return dataSrc(response,type);
                    },
                    data    :   {
                        _token: '{{csrf_token()}}',
                        start_date,
                        end_date,
                        page_ids
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        $('#'+dataTableId).DataTable().clear().draw();
                        if(type == 'gadsCampaign'){
                            displayCountLoading(no_data_text);
                        }
                        else if(type == 'gadsConversion'){
                            setConversionCounts({ viewThroughConv:0 , conversion:0 })
                            displayLineChartLoading(no_data_text);
                        }
                    }
                },
                columns :   columns,
                columnDefs: columnsDef,
                order: [[orderableColumn, 'desc']]
            });
        }

        // Data table response
        function dataSrc(response,type) {
            var returnData = response.data.rows;

            if('counts' in response.data && type == 'gadsCampaign'){
                var counts = response.data.counts;
                setItem(google_ads_counts, counts);
                setCountValues(counts);
            }
            if(type != 'gadsCampaign'){
                if(type == 'gadsConversion'){
                    var counts = response.data.counts;
                    if(counts){
                        setConversionCounts(counts);
                    }else{
                        displayCountLoading(no_data_text);
                    }
                }else{
                    var countData = getItem(google_ads_counts);
                    if(countData){
                        setCountValues(countData);
                    }else{
                        displayCountLoading(no_data_text);
                    }
                }
            }
            updateLineChart(type);
            // if('counts' in response.data){
            //     if( type == 'gadsCampaign'){
            //         var counts = response.data.counts;
            //         setItem(google_ads_counts, counts);
            //         setCountValues(counts);
            //     }else{
            //         if(type == 'gadsConversion'){
            //             var counts = response.data.counts;
            //             if(counts){
            //                 setConversionCounts(counts);
            //             }else{
            //                 displayCountLoading(no_data_text);
            //             }
            //         }else{
            //             var countData = getItem(google_ads_counts);
            //             if(countData){
            //                 setCountValues(countData);
            //             }else{
            //                 displayCountLoading(no_data_text);
            //             }
            //         }

            //     }
            // }


            return returnData;
        }

        function getLineChartData() {
            var url =  "{{ route('analytics.google-ads.ajax', ['type' => 'gads_line_chart']) }}";

            $.ajax({
                type    :   'POST',
                url     :   url,
                data    :   {
                    _token: '{{csrf_token()}}',
                    start_date,
                    end_date,
                    page_ids
                },
                success: function(response){
                    var data = response.data;
                    if('clicks' in data){
                        var lables = $.map(data.clicks, function(element,index) {return element.date});
                        var values = $.map(data.clicks, function(element,index) {return element.value});

                        $('#gads_line_graph_loading').css('display', 'none');
                        $('#gads_line_graph_container').css('display', '');
                        setItem(google_ads_line_chart, data);
                        gads_line_graph(lables, values, 'gads_line_graph');
                    }else{
                        $('#gads_line_graph_loading').html(no_data_text);
                    }

                },
                error: function(jqXHR, textStatus, errorThrown){
                    $('#gads_line_graph_loading').html(no_data_text);
                }
            });
        }

        function displayLineChartLoading(loading_text = null){
            $('#gads_line_graph_loading').css('display', '');
            $('#gads_line_graph_container').css('display', 'none');
            $('#gads_line_graph_loading_text').text(loading_text);
        }

        function updateLineChart(type){
            // displayLineChartLoading();
            var lineChartData = getItem(google_ads_line_chart);
            if(type != 'gadsConversion'){
                if(lineChartData && 'clicks' in lineChartData){
                    var lables = $.map(lineChartData.clicks, function(element,index) {return element.date});
                    var values = $.map(lineChartData.clicks, function(element,index) {return element.value});
                    var graph_id = `gads_${type}_line_graph`;
                    var graph = `<canvas id="${graph_id}" height="90" class="d-flex justify-content-center"> </canvas>`;
                    $('#gads_line_graph_loading').css('display', 'none');
                    $('#gads_line_graph_container').css('display', '');
                    $('#gads_line_graph_container').empty();
                    $('#gads_line_graph_container').html(graph);
                    gads_line_graph(lables, values, graph_id);
                }else{
                    displayLineChartLoading(no_data_text);
                    // $('#gads_line_graph_loading').css('display', '');
                    // $('#gads_line_graph_container').css('display', 'none');
                }
            }else{
                if(lineChartData && 'conversions' in lineChartData){
                    var lables = $.map(lineChartData.conversions, function(element,index) {return element.date});
                    var values = $.map(lineChartData.conversions, function(element,index) {return element.value});
                    var graph_id = `gads_${type}_line_graph`;
                    var graph = `<canvas id="${graph_id}" height="90" class="d-flex justify-content-center"> </canvas>`;
                    $('#gads_line_graph_loading').css('display', 'none');
                    $('#gads_line_graph_container').css('display', '');
                    $('#gads_line_graph_container').empty();
                    $('#gads_line_graph_container').html(graph);
                    console.log(graph_id);
                    gads_line_graph(lables, values, graph_id);
                }else{
                    displayLineChartLoading(no_data_text);
                    $('#gads_line_graph_loading').css('display', '');
                    $('#gads_line_graph_container').css('display', 'none');
                }
            }
        }

        function setConversionCounts(count){
            $("#count_viewThroughConv").html(count.viewThroughConv);
            $("#count_conversions").html(count.conversion);
        }
        // get Selected Tab Type
        function getSelectedTabType(){
            return $('#ads_tab_container').find('.active').data('type');
        }

        // Get Selected Card element
        function getSelectedCountCard(){
            return $('#clickable_count_container').find('.clickable_active_count');
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

        var gads_line_graph =  (labels,values,graph_id=null) => {
            var graph_lable = getSelectedTabType() == 'gadsConversion' ? 'Conversion' :'Clicks'
            var ctx = document.getElementById(graph_id).getContext('2d');
            var lineGraph = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: graph_lable,
                        data: values,
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
    });
</script>
@endpush