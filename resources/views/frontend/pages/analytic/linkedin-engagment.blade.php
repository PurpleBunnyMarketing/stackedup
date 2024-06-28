@php
    /**
    * Get Orgnization ids
    */

    $orgIds = \App\Http\Controllers\AnalyticsController::getLinkedinOrgIds();

    /* Follwers */
    $linkedin_follower = json_decode($analytic->linkedin_follower ?? '{}',true);

    $isLinkedinConnected = $connectedSocialMedia->where('media_id',2)->first();
    $current_request = request()->getRequestUri();

@endphp
@if(!empty($orgIds))

<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader">
    <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex  post-type">
                <a href="javascript:;" class="post-link btn font-size-h3
                    font-weight-bold my-2 mr-5" data-type="posts">Linkedin - Engagement</a>
            </div>
        </div>
    </div>
</div>
<div class="container">
    @if(isset($analytic->id) && isset($isLinkedinConnected))
    <div class="row post-cards">
        {{-- Followers --}}
        <div class="col-lg-6 col-md-6 col-12 ">
            <div class="card">
                <div class="card-header pb-0">
                    <h2 class="text-bold-700">Followers</h2>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-center no-data">
                        <h2 class="text-bold-700 mt-1" id="total_counts" style="font-size:3.5rem;color:#E77F01">Loading data..</h2>
                    </div>
                </div>
            </div>
            {{-- Organic vs Paid New Followers --}}
            <div class="card mt-6">
                <div class="card-header  pb-0">
                    <h2 class="text-bold-700">Organic vs Paid New Followers</h2>
                </div>
                <div class="card-body">
                    <div class="linkedin-graph linkedin_1" style="display: none;">
                        <div id="linkedin_1" class="d-flex justify-content-center"></div>
                    </div>
                    <div class=" align-items-center justify-content-center no-data linkedin_1_init">
                        <h2 class="text-bold-700 mt-1"> Loading data.. </h2>
                    </div>
                </div>
            </div>
            {{-- Impression --}}
            <div class="card mt-6">
                <div class="card-header pb-0">
                    <h2 class="text-bold-700">Impressions</h2>
                </div>
                <div class="card-body">
                    <div class="mt-auto impression-graph" style="display: none;">
                        <canvas id="linkedin_4" height="130"></canvas>
                    </div>
                    <div class="align-items-center justify-content-center no-data impression-default">
                        <h2 class="text-bold-700 mt-1"> Loading data... </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-12  mt-lg-0 mt-4 ">
            {{-- Net Followers --}}
            <div class="card">
                <div class="card-header pb-0">
                    <h2 class="text-bold-700">Net Followers</h2>
                </div>
                <div class="card-body">
                    <div class="mt-auto new-follower-graph" style="display: none;">
                        <canvas id="linkedin_2" height="130"></canvas>
                    </div>
                    <div class="align-items-center justify-content-center no-data new-follower-default">
                        <h2 class="text-bold-700 mt-1"> Loading data... </h2>
                    </div>
                </div>
            </div>
            {{-- Clicks --}}
            <div class="card mt-6">
                <div class="card-header pb-0">
                    <h2 class="text-bold-700">Clicks</h2>
                </div>
                <div class="card-body">
                    <div class="mt-auto click-graph" style="display: none;">
                        <canvas id="linkedin_3" height="130"></canvas>
                    </div>
                    <div class="align-items-center justify-content-center no-data click-default">
                        <h2 class="text-bold-700 mt-1"> Loading data... </h2>
                    </div>
                </div>
            </div>
            {{-- Social Actions --}}
            <div class="card mt-6">
                <div class="card-header pb-0">
                    <h2 class="text-bold-700">Social Actions</h2>
                </div>
                <div class="card-body">
                    <div class="mt-auto social-count-graph" style="display: none;">
                        <canvas id="social_action_chart" height="130"></canvas>
                    </div>
                    <div class="align-items-center justify-content-center no-data social-count-default">
                        <h2 class="text-bold-700 mt-1"> Loading data... </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <h3 style="color:rgb(231,127,1)">No Data Found</h3>
    @endif
    <div class="custom-pagination d-flex justify-content-center flex-wrap py-2 mr-3 pagination slider-pagination">
    </div>
</div>

<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader">
    <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex  post-type">
                <a href="javascript:;" class="post-link btn font-size-h3
                    font-weight-bold my-2 mr-5" data-type="posts">Linkedin - Demographics</a>
            </div>
        </div>
    </div>
</div>
<div class="container">
    @if(isset($analytic->id) && isset($isLinkedinConnected))
    <div class="card">
        <div class="card-header text-center">
            <h2 class="mb-0">Top Followers</h2>
        </div>
    </div>
    <div class="row post-cards linkedin-demographics">
        {{-- Top Followers By Country --}}
        <div class="col-lg-4 col-md-6 col-12 mt-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h2 class="text-bold-700">By Country</h2>
                </div>
                <div class="card-body">
                    <section class="bar-graph bar-graph-horizontal bar-graph-one followers-country">
                       <div class="d-flex align-items-center justify-content-center no-data followers-country-default">
                            <h2 class="text-bold-700 mt-1"> Loading data... </h2>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        {{-- Top Followers By Company Size --}}
        <div class="col-lg-4 col-md-6 col-12 mt-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h2 class="text-bold-700">By Company Size</h2>
                </div>
                <div class="card-body">
                    <section class="bar-graph bar-graph-horizontal bar-graph-one followers-company">
                        <div class="d-flex align-items-center justify-content-center no-data followers-by-company-default">
                            <h2 class="text-bold-700 mt-1"> Loading data... </h2>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        {{-- Top Followers By Senority Level --}}
        <div class="col-lg-4 col-md-6 col-12 mt-6">
            <div class="card">
                <div class="card-header pb-0">
                    <h2 class="text-bold-700">By Seniority Level</h2>
                </div>
                <div class="card-body">
                    <section class="bar-graph bar-graph-horizontal bar-graph-one followers-senority">
                        <div class="d-flex align-items-center justify-content-center no-data followers-senority-default">
                            <h2 class="text-bold-700 mt-1"> Loading data... </h2>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        {{-- Top Followers By Job Function --}}
        <div class="col-12 col-md-12 mt-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h2 class="text-bold-700">Followers by Job Function</h2>
                </div>
                <div class="card-body">
                    <div class="mt-auto follower-job-function-graph" style="display: none;">
                        <canvas id="linkedin_6" height="100"></canvas>
                    </div>
                    <div class="align-items-center justify-content-center no-data follower-job-function-default">
                        <h2 class="text-bold-700 mt-1"> Loading data... </h2>
                    </div>
                </div>
            </div>
        </div>
        {{-- Followers by Industry --}}
        <div class="col-12 col-md-12 mt-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h2 class="text-bold-700">Followers by Industry</h2>
                </div>
                <div class="card-body">
                    <div class="mt-auto follower-industry-graph" style="display: none;">
                        <canvas id="linkedin_7" height="100"></canvas>
                    </div>
                    <div class="align-items-center justify-content-center no-data follower-industry-default">
                        <h2 class="text-bold-700 mt-1"> Loading data... </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <h3 style="color:rgb(231,127,1)">No Data Found</h3>
    @endif
    <div class="custom-pagination d-flex justify-content-center flex-wrap py-2 mr-3 pagination slider-pagination">
    </div>
</div>


@push('extra-js-scripts')
<script>
    var linkedinchart1 = function (arr) {
		const apexChart = "#linkedin_1";
		var options = {
			series:arr ,
			chart: {
				width: 380,
				type: 'pie',
			},
            dataLabels: {
                enabled: true,
            },
            legend : {
                show: true,
                position:'bottom'
            },
            labels : ['Organic','Paid'],
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
			colors: ['#3755A9' , '#E77F01']
		};

		var chart = new ApexCharts(document.querySelector(apexChart), options);
		chart.render();
	}

    var linkedinChart2 = (labels, value) => {

        var ctx = document.getElementById("linkedin_2").getContext('2d');
        Chart.defaults.global.legend.display = false;
        var twitterFollower = new Chart(ctx, {
        type: 'bar',
        data: {
                labels: labels,
                datasets: [{
                    label: 'Net Follower',
                    data: value,
                    backgroundColor: "#E77F01"
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
                        callback: (t, i) => i % 2 ? '' : labels[i]
                    }
                }]
                }
            }
        });
    }

    var linkedinChart3 = (labels, value) => {

        var ctx = document.getElementById("linkedin_3").getContext('2d');
        Chart.defaults.global.legend.display = false;
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

    var linkedinChart4 = (labels, value) => {

        var ctx = document.getElementById("linkedin_4").getContext('2d');;
        var twitterFollower = new Chart(ctx, {
        type: 'line',
        data: {
                labels: labels,
                datasets: [{
                    label: 'Audience Engagement',
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

    var linkedinChart6 = (labels, value) => {

        var ctx = document.getElementById("linkedin_6").getContext('2d');
        Chart.defaults.global.legend.display = false;
        var twitterFollower = new Chart(ctx, {
        type: 'bar',
        data: {
                labels: labels,
                datasets: [{
                    label: '',
                    data: value,
                    backgroundColor: ["#3755A9", "#ffddb3", "#6581cd", "#fe981b", "#b2c0e6", "#e77f01","#2c4487"]
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

    var linkedinChart7 = (labels, value) => {

        var ctx = document.getElementById("linkedin_7").getContext('2d');
        Chart.defaults.global.legend.display = false;
        var twitterFollower = new Chart(ctx, {
        type: 'bar',
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
        },
        data: {
                labels: labels,
                datasets: [{
                    label: '',
                    data: value,
                    backgroundColor: ["#3755A9", "#ffddb3", "#6581cd", "#fe981b", "#b2c0e6", "#e77f01","#2c4487"]
                }]
            },

        });
    }

    var social_actions_chart = (labels, likes_count, comment_count, share_count) => {

        var ctx = document.getElementById("social_action_chart").getContext('2d');
        Chart.defaults.global.legend.display = true;
        var social_action_chart = new Chart(ctx, {
        type: 'bar',
        data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Likes',
                        data: likes_count,
                        backgroundColor: "#E77F01"
                    },
                    {
                        label: 'Comments',
                        data: comment_count,
                        backgroundColor: "#CCC5A8"
                    },
                    {
                        label: 'Shares',
                        data: share_count,
                        backgroundColor: "#52BACC"
                    },
                ]
            },
            options: {
                legend : {
                        show: true,
                        position:'bottom'
                },
                scales: {
                    yAxes: [{
                        stacked: true,
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) {if (value % 1 === 0) {return value;}}
                        }
                    }],
                    xAxes: [{
                        stacked: true,
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            autoSkip: true,
                            callback: (t, i) => i % 2 ? '' : labels[i]
                        }
                    }]
                }
            }
        });
    }


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

    jQuery(document).ready(function() {
        var urlParams = new URLSearchParams(window.location.search);

        const start_date = urlParams.get('start_date');
        const end_date = urlParams.get('end_date');

        /* Total followers count  */
        $.ajax({
            url: "{{ route('analytics.linkedin.ajax', ['type' => 'total_counts']) }}",
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                'orgIds': @php echo json_encode($orgIds) @endphp,
                'page_update': '{{ $page_update }}'
            },
            async: true,
            cache: false,
            success: function(r){
                const res = JSON.parse(r);
                // console.log(res);
                if(res.status === 200) {
                    $('#total_counts').html(res.data)
                } else {
                    $('#total_counts').html("Something went wrong!")
                }
            },
            error: function(e){

            }
        })

        /* new, organic and paid followers */
        $.ajax({
            url: "{{ route('analytics.linkedin.ajax', ['type' => 'organic_paid_new_followers']) }}",
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                start_date,
                end_date,
                'orgIds': @php echo json_encode($orgIds) @endphp,
                'update_data': '{{ $update_data }}'
            },
            async: true,
            cache: false,
            success: function(r){
                const res = JSON.parse(r);
                // console.log(res);
               if(res.status === 200) {
                    const organicFollowerCount = res.data.organicFollowerCount;
                    const paidFollowerCount = res.data.paidFollowerCount;
                    if(organicFollowerCount > 0 || paidFollowerCount > 0) {
                        $('.linkedin_1_init').hide();
                        $('.linkedin_1').show();
                        linkedinchart1([organicFollowerCount, paidFollowerCount]);
                    } else {
                        $('.linkedin_1_init').html('No data found');
                    }

                    /* new followers */
                    const new_followers = res.data.new_followers
                    const new_followers_keys = res.data.new_followers_keys
                    const new_followers_values = res.data.new_followers_values
                    if (new_followers > 0) {
                        $('.new-follower-default').hide();
                        $('.new-follower-graph').show();
                        linkedinChart2(new_followers_keys, new_followers_values);
                    } else {
                        $('.new-follower-default').html('No data found');
                    }

                } else {
                    $('.linkedin_1_init').html(res.data)
                }
            },
            error: function(e){

            }
        })

        /* Viewers and clicks graph */
        $.ajax({
            url: "{{ route('analytics.linkedin.ajax', ['type' => 'viewers']) }}",
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                start_date,
                end_date,
                'orgIds': @php echo json_encode($orgIds) @endphp,
                'update_data': '{{ $update_data }}'
            },
            async: true,
            cache: false,
            success: function(r){
                const res = JSON.parse(r);
                // console.log(res);
                if(res.status === 200) {
                    const viewers = res.data.viewers
                    const viewers_keys = res.data.viewers_keys
                    const viewers_values = res.data.viewers_values
                    if (viewers > 0) {
                        $('.impression-default').hide();
                        $('.impression-graph').show();
                        linkedinChart4(viewers_keys, viewers_values);
                    } else {
                        $('.impression-default').html('No data found');
                    }

                    const clicks = res.data.clicks
                    const click_keys = res.data.click_keys
                    const click_values = res.data.click_values
                    if (clicks > 0) {
                        $('.click-default').hide();
                        $('.click-graph').show();
                        linkedinChart3(click_keys, click_values);
                    } else {
                         $('.click-default').html('No data found');
                    }
                } else {
                    $('.impression-default').html(res.data)
                    $('.click-default').html(res.data)
                }
            },
            error: function(e){

            }
        })

        /* Social counts */
        $.ajax({
            url: "{{ route('analytics.linkedin.ajax', ['type' => 'social_count']) }}",
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                start_date,
                end_date,
                'orgIds': @php echo json_encode($orgIds) @endphp,
                'update_data': '{{ $update_data }}'
            },
            async: true,
            cache: false,
            success: function(r){
                const res = JSON.parse(r);
                // console.log(res);
                if(res.status === 200) {
                    const {social_count, social_actions_keys, likes_count, comment_count, share_count} = res.data;

                    if (social_count > 0) {
                        $('.social-count-default').hide();
                        $('.social-count-graph').show();
                        social_actions_chart(social_actions_keys, likes_count, comment_count, share_count);
                    } else {
                        $('.social-count-default').html('No data found');
                    }
                } else {
                    $('.social-count-default').html(res.data)
                }
            },
            error: function(e){

            }
        })

        /* Top Followers By Country */
        $.ajax({
            url: "{{ route('analytics.linkedin.ajax', ['type' => 'follower-by-country']) }}",
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                analytic_id: {{ $analytic->id }},
                'page_update': '{{ $page_update }}'
            },
            async: true,
            cache: false,
            success: function(r){
                const res = JSON.parse(r);
                // console.log(res);
                if(res.status === 200) {
                    $('.followers-country').html(res.data);
                } else {
                    $('.followers-country-default').html(res.data)
                }
            },
            error: function(e){

            }
        })


        /* Top Followers By Company */
        $.ajax({
            url: "{{ route('analytics.linkedin.ajax', ['type' => 'follower-by-company']) }}",
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                analytic_id: {{ $analytic->id }},
                'page_update': '{{ $page_update }}'
            },
            async: true,
            cache: false,
            success: function(r){
                const res = JSON.parse(r);
                // console.log(res);
                if(res.status === 200) {
                    $('.followers-company').html(res.data);
                } else {
                    $('.followers-company-default').html(res.data)
                }
            },
            error: function(e){

            }
        })

        /* Top Followers By Senority */
        $.ajax({
            url: "{{ route('analytics.linkedin.ajax', ['type' => 'follower-by-senority']) }}",
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                analytic_id: {{ $analytic->id }},
                'page_update': '{{ $page_update }}'
            },
            async: true,
            cache: false,
            success: function(r){
                const res = JSON.parse(r);
                // console.log(res);
                if(res.status === 200) {
                    $('.followers-senority').html(res.data);
                } else {
                    $('.followers-senority-default').html(res.data)
                }
            },
            error: function(e){

            }
        })

        /* Top Followers By Job Function */
        $.ajax({
            url: "{{ route('analytics.linkedin.ajax', ['type' => 'follower-by-job-function']) }}",
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                analytic_id: {{ $analytic->id }},
                'page_update': '{{ $page_update }}'
            },
            async: true,
            cache: false,
            success: function(r){
                const res = JSON.parse(r);
                // console.log(res);
                if(res.status === 200) {
                    const {function_count, function_keys, function_values} = res.data;
                    if (function_count > 0) {
                        $('.follower-job-function-default').hide();
                        $('.follower-job-function-graph').show();
                        linkedinChart6(function_keys, function_values);
                    } else {
                        $('.follower-job-function-default').html('No data found');
                    }
                } else {
                    $('.follower-job-function-default').html(res.data)
                }
            },
            error: function(e){

            }
        })

        /* Top Followers By Job Function */
        $.ajax({
            url: "{{ route('analytics.linkedin.ajax', ['type' => 'follower-by-industry']) }}",
            type: 'POST',
            data: {
                _token: '{{csrf_token()}}',
                analytic_id: {{ $analytic->id }},
                'page_update': '{{ $page_update }}'
            },
            async: true,
            cache: false,
            success: function(r){
                const res = JSON.parse(r);
                // console.log(res);
                if(res.status === 200) {
                    const {industry_count, industry_keys, industry_values} = res.data;
                    if (industry_count > 0) {
                        $('.follower-industry-default').hide();
                        $('.follower-industry-graph').show();
                        linkedinChart7(industry_keys, industry_values);
                    } else {
                        $('.follower-industry-default').html('No data found');
                    }
                } else {
                    $('.follower-industry-default').html(res.data)
                }
            },
            error: function(e){

            }
        })

    });
</script>
@endpush
@else
<p>Please contact Admin</p>
@endif
