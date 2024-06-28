<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader">
    <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex  post-type">
                <a href="javascript:;" class="post-link btn font-size-h3
                    font-weight-bold my-2 mr-5" data-type="posts">Instagram - Interaction</a>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row post-cards">
        <div class="col-lg-6 col-md-6 col-12">
            <div class="card">
                <div class="card-header  pb-0">
                    <h2 class="text-bold-700">Profile Visits</h2>
                </div>
                <div class="card-body">
                    <div id="instagram_profile_visits_line_chart_container" style="display: none">
                        <canvas id="instagram_profile_visits_line_chart" height="140"></canvas>
                    </div>
                    <div class="align-items-center justify-content-center no-data"
                        id="instagram_profile_visits_line_chart_loading_text_container">
                        <h2 id="instagram_profile_visits_line_chart_loading_text"
                            class="d-flex justify-content-center loading-text">
                            Loading...</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-12">
            <div class="card mt-6 mt-lg-0 mt-md-0">
                <div class="card-header  pb-0">
                    <h2 class="text-bold-700">Website Clicks</h2>
                </div>
                <div class="card-body">
                    <div id="instagram_website_clicks_line_chart_container" style="display: none">
                        <canvas id="instagram_website_clicks_line_chart" height="140"></canvas>
                    </div>
                    <div class="align-items-center justify-content-center no-data"
                        id="instagram_website_clicks_line_chart_loading_text_container">
                        <h2 id="instagram_website_clicks_line_chart_loading_text"
                            class="d-flex justify-content-center loading-text">
                            Loading...</h2>
                    </div>
                </div>
            </div>

        </div>
    </div>
    {{-- Counts --}}
    <div class="row post-cards mt-6">
        <div class="col-lg-3 col-md-16 col-12">
            <div class="card mt-lg-0 ">
                <div class="card-header pb-0 ">
                    <h2 class="text-bold-700">Emails</h2>
                </div>
                <div class="card-body">
                    <h2 class="text-bold-700 text-center loading-text" id="emails_count" style="color:#E77F01">
                        Loading...
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-16 col-12">
            <div class="card mt-6 mt-lg-0 ">
                <div class="card-header pb-0 ">
                    <h2 class="text-bold-700">Get Direction</h2>
                </div>
                <div class="card-body">
                    <h2 class="text-bold-700 text-center loading-text" id="get_direction_count" style="color:#E77F01">
                        Loading...
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-16 col-12">
            <div class="card mt-6 mt-lg-0 ">
                <div class="card-header pb-0 ">
                    <h2 class="text-bold-700">Calls</h2>
                </div>
                <div class="card-body">
                    <h2 class="text-bold-700 text-center loading-text" id="calls_count" style="color:#E77F01">
                        Loading...
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-16 col-12">
            <div class="card mt-6 mt-lg-0 ">
                <div class="card-header pb-0 ">
                    <h2 class="text-bold-700">Message Clicks</h2>
                </div>
                <div class="card-body">
                    <h2 class="text-bold-700 text-center loading-text" id="message_clicks_count" style="color:#E77F01">
                        Loading...
                    </h2>
                </div>
            </div>
        </div>
    </div>

</div>

@push('extra-js-scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.min.js" type="text/javascript"></script>
<script>
    var instagram_profile_visit =  (labels,values) => {
        var ctx = document.getElementById("instagram_profile_visits_line_chart").getContext('2d');;
        var twitterFollower = new Chart(ctx, {
        type: 'line',
        data: {
                labels: labels,
                datasets: [{
                    label: 'Profile Visit',
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

    var instagram_website_click = (labels,values) => {
        var ctx = document.getElementById("instagram_website_clicks_line_chart").getContext('2d');;
        var twitterFollower = new Chart(ctx, {
        type: 'line',
        data: {
                labels: labels,
                datasets: [{
                    label: 'Profile Visit',
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

    $(function(){

        var urlParams           =   new URLSearchParams(window.location.search);
        var page_ids            =   urlParams.get('page_id');
        var start_date          =   urlParams.get('start_date');
        var end_date            =   urlParams.get('end_date');

        const loading_text                          =   'Loading...';
        const no_data_text                          =   'No details found';

        initialInstagramInteractionDataLoading();

        function initialInstagramInteractionDataLoading(){
            getInstagramInteractionData();
        }

        function getInstagramInteractionData(){
            $.ajax({
                url: "{{ route('analytics.instagram.ajax', ['type' => 'interaction_line_chart_and_count']) }}",
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
                    if('website_clicks' in data){
                        $('#instagram_website_clicks_line_chart_container').css('display', '');
                        $('#instagram_website_clicks_line_chart_loading_text_container').css('display', 'none');
                        instagram_website_click(data.website_clicks.key_array,data.website_clicks.values_array);
                    }else{
                        $('#instagram_website_clicks_line_chart_loading_text').text(no_data_text);
                    }

                    if('profile_views' in data){
                        $('#instagram_profile_visits_line_chart_container').css('display', '');
                        $('#instagram_profile_visits_line_chart_loading_text_container').css('display', 'none');
                        instagram_profile_visit(data.profile_views.key_array,data.profile_views.values_array);
                    }else{
                        $('#instagram_profile_visits_line_chart_loading_text').text(no_data_text);
                    }

                    setDisplayCounts(data?.counts);

                },
                error: function(jqXHR, error, errorThrown) {
                    $('#instagram_website_clicks_line_chart_loading_text').text(no_data_text);
                    $('#instagram_profile_visits_line_chart_loading_text').text(no_data_text);
                    setDisplayCounts(null);
                }
            });
        }

        function setDisplayCounts(counts=null)
        {
            $('#emails_count').html(counts?.email_contacts_count ?? no_data_text);
            $('#get_direction_count').html(counts?.get_directions_clicks_count ?? no_data_text);
            $('#calls_count').html(counts?.phone_call_clicks_count ?? no_data_text);
            $('#message_clicks_count').html(counts?.text_message_clicks_count ?? no_data_text);
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