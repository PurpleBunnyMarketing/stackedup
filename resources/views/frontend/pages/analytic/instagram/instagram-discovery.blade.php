<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader">
    <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex  post-type">
                <a href="javascript:;" class="post-link btn font-size-h3
                    font-weight-bold my-2 mr-5" data-type="posts">Instagram - Discovery</a>
            </div>
        </div>
    </div>
</div>
<div class="container">

    <div class="row post-cards">
        <div class="col-lg-3 col-md-3 col-12">
            {{-- Audience Age --}}
            <div class="card">
                <div class="card-header pb-0 ">
                    <h2 class="text-bold-700">Followers As At Today</h2>
                </div>
                <div class="card-body align-items-center">
                    <div id="followers_count_container" style="display: none;">
                        <div class="text-bold-700 text-center loading-text" id="followers_count"
                            style="color:#E77F01;font-size:40px;">
                        </div>
                    </div>
                    <div id="followers_count_loading_text_container">
                        <div class="text-bold-700 text-center loading-text" id="followers_count_loading_text"
                            style="color:#E77F01;font-size:18px;">
                            Loading...
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-9 col-md-9 col-12">
            <div class="card mt-6 mt-lg-0 mt-md-0">
                <div class="card-header  pb-0">
                    <h2 class="text-bold-700">Followers Gained <span id="follower_gained_count">- 0</span></h2>
                    <p class="text-danger" id="follower_gain_error_message"></p>
                </div>
                <div class="card-body">
                    <div id="instagram_new_followers_bar_chart_container" style="display: none;">
                        <canvas id="instagram_new_followers_bar_chart" height="70"></canvas>
                    </div>
                    <div class="align-items-center justify-content-center no-data"
                        id="instagram_new_followers_bar_chart_loading_text_container">
                        <h2 id="instagram_new_followers_bar_chart_loading_text"
                            class="d-flex justify-content-center loading-text">
                            Loading...</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row post-cards mt-6">
        <div class="col-lg-6 col-md-12 col-12">
            {{-- Audience Age --}}
            <div class="card">
                <div class="card-header  pb-0">
                    <h2 class="text-bold-700">Reach</h2>
                </div>
                <div class="card-body">

                    <div id="instagram_reach_line_graph_container" style="display: none;">
                        <canvas id="instagram_reach_line_graph" height="140"></canvas>
                    </div>
                    <div class="align-items-center justify-content-center no-data"
                        id="instagram_reach_line_loading_text_container">
                        <h2 id="instagram_reach_line_loading_text" class="d-flex justify-content-center loading-text">
                            Loading...</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 col-12">
            <div class="card mt-6 mt-lg-0">
                <div class="card-header  pb-0">
                    <h2 class="text-bold-700">Impression</h2>
                </div>
                <div class="card-body">

                    <div id="instagram_impression_line_graph_container" style="display: none;">
                        <canvas id="instagram_impression_line_graph" height="140"></canvas>
                    </div>
                    <div class="align-items-center justify-content-center no-data"
                        id="instagram_impression_line_graph_loading_text_container">
                        <h2 id="instagram_impression_line_graph_loading_text"
                            class="d-flex justify-content-center loading-text">
                            Loading...</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('extra-js-scripts')
<script>
    var instagram_reach_line_chart =  (labels,values) => {

        var ctx = document.getElementById("instagram_reach_line_graph").getContext('2d');
        var twitterFollower = new Chart(ctx, {
        type: 'line',
        data: {
                labels: labels,
                datasets: [{
                    label: 'Reach',
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
    var instagram_impression_line_chart =  (labels,values) => {

        var ctx = document.getElementById("instagram_impression_line_graph").getContext('2d');
        var twitterFollower = new Chart(ctx, {
        type: 'line',
        data: {
                labels: labels,
                datasets: [{
                    label: 'Impression',
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
    var instagram_new_followers_bar_chart = (labels,values) => {

        let ctx = document.getElementById("instagram_new_followers_bar_chart").getContext('2d');
        Chart.defaults.global.legend.display = false;
        let instagram_new_followers_bar_chart = new Chart(ctx, {
        type: 'bar',
        data: {
                labels: labels,
                datasets: [{
                    label: '',
                    data: values,
                    backgroundColor: "#E77F01"
                }]
            },
            options: {
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false
                        },
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) {if (value % 1 === 0) {return value;}}
                        }
                    }]
                }
            }
        });
    }

    $(function(){

        var urlParams           =   new URLSearchParams(window.location.search);
        var page_ids            =   urlParams.get('page_id');
        var start_date          =   urlParams.get('start_date');
        var end_date            =   urlParams.get('end_date');

        const loading_text                          =   'Loading...';
        const no_data_text                          =   'No details found';

        initialInstagramDataLoading();

        function initialInstagramDataLoading(){
            getNewFollowerBarChartAndFollowersCountData();
            getImpressionAndReachData();
        }

        function getImpressionAndReachData(){
            $.ajax({
                url: "{{ route('analytics.instagram.ajax', ['type' => 'dicovery_reach_impression_line_chart']) }}",
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
                    var data = res.data;

                    if('reach' in data){
                        $('#instagram_reach_line_graph_container').css('display', '');
                        $('#instagram_reach_line_loading_text_container').css('display', 'none');
                        instagram_reach_line_chart(data.reach.key_array,data.reach.values_array);
                    }else{
                        $('#instagram_reach_line_loading_text').text(no_data_text);
                    }

                    if('impressions' in data){
                        $('#instagram_impression_line_graph_container').css('display', '');
                        $('#instagram_impression_line_graph_loading_text_container').css('display', 'none');
                        instagram_impression_line_chart(data.impressions.key_array,data.impressions.values_array);
                    }else{
                        $('#instagram_impression_line_graph_loading_text').text(no_data_text);
                    }
                },
                error: function(jqXHR, error, errorThrown) {
                    $('#instagram_reach_line_loading_text').text(no_data_text);
                    $('#instagram_impression_line_graph_loading_text').text(no_data_text);
                }
            });
        }

        function getNewFollowerBarChartAndFollowersCountData(){
            $.ajax({
                url: "{{ route('analytics.instagram.ajax', ['type' => 'dicovery_new_followers_bar_chart_and_count']) }}",
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
                    var data = res.data;
                    if('follower_gain_count' in data){
                        $('#follower_gained_count').text('- '+data.follower_gain_count);
                    }
                    if('error_message' in data){
                        $('#follower_gain_error_message').text(data.error_message.follower_gained);
                    }
                    if('follower_count' in data){
                        $('#instagram_new_followers_bar_chart_container').css('display', '');
                        $('#instagram_new_followers_bar_chart_loading_text_container').css('display', 'none');
                        instagram_new_followers_bar_chart(data.follower_count.key_array,data.follower_count.values_array);
                    }else{
                        $('#instagram_new_followers_bar_chart_loading_text').text(no_data_text);
                    }
                    if('counts' in data){
                        $('#followers_count_container').css('display', '');
                        $('#followers_count_loading_text_container').css('display', 'none');
                        $('#followers_count').text(data.counts.follower_count);
                    }else{
                        $('#followers_count_loading_text').text(no_data_text);
                    }
                },
                error: function(jqXHR, error, errorThrown) {
                    $('#instagram_new_followers_bar_chart_loading_text').text(no_data_text);
                    $('#followers_count_loading_text').text(no_data_text);
                }
            });
        }


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