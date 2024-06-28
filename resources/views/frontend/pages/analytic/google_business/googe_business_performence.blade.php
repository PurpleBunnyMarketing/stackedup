@php
$isGoogleMyBusinessConnected = $connectedSocialMedia->where('media_id',5)->first();
@endphp

<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader"></div>
<div class="container">

    <div class="row post-cards">
        {{-- Plateform & Device --}}
        <div class="col-lg-6 col-md-6 col-12">
            <div class="card">
                <div class="card-header  pb-0">
                    <h2 class="text-bold-700">Platform & Device</h2>
                </div>
                <div class="card-body">
                    <div class="plateform_device_graph_container">
                        <div id="plateform_device_graph" class="d-flex justify-content-center"></div>
                    </div>
                    <div class="align-items-center justify-content-center no-data plateform_device_loading">
                        <div id="" class="d-flex justify-content-center">Loading...</div>
                    </div>
                </div>
            </div>
            {{-- Search VS Maps --}}
            <div class="card mt-6">
                <div class="card-header  pb-0">
                    <h2 class="text-bold-700">Search Vs Maps</h2>
                </div>
                <div class="card-body">
                    <div class="search_vs_maps_graph_container">
                        <div id="search_vs_maps_graph" class="d-flex justify-content-center"></div>
                    </div>
                    <div class="align-items-center justify-content-center no-data plateform_device_loading">
                        <div id="" class="d-flex justify-content-center">Loading...</div>
                    </div>
                </div>
            </div>
            {{-- Audience Age --}}
            <div class="card mt-6">
                <div class="card-header pb-0">
                    <h2 class="text-bold-700">Business Profile Impression</h2>
                </div>
                <div class="card-body">
                    <div class="mt-auto" id="impression_graph_container" style="display: none">
                        <canvas id="impression_graph" height="100"></canvas>
                    </div>
                    <div class="align-items-center justify-content-center no-data impression_graph_loading">
                        <div id="" class="d-flex justify-content-center">Loading...</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Total Likes --}}
        <div class="col-lg-6 col-md-6 mt-md-0 mt-6 col-12">
            <div class="facebook-likes">
                <div class="card">
                    <div class="card-header  pb-0">
                        <h2 class="text-bold-700">Desktop Vs Mobile</h2>
                    </div>
                    <div class="card-body">
                        <div class="desktop_vs_mobile_graph_container">
                            <div id="desktop_vs_mobile_graph" class="d-flex justify-content-center"></div>
                        </div>
                        <div class="align-items-center justify-content-center no-data plateform_device_loading">
                            <h2 class="text-bold-700 mt-1"> Loading... </h2>
                        </div>
                    </div>
                </div>

                <div class="card mt-6">
                    <div class="card-header  pb-0">
                        <h2 class="text-bold-700">Business Profile Interactions</h2>
                    </div>
                    <div class="card-body">
                        <div class="mt-auto" id="interactions_graph_container" style="display: none">
                            <canvas id="interactions_graph" height="100"></canvas>
                        </div>
                        <div class="align-items-center justify-content-center no-data interactions_graph_loading">
                            <div id="" class="d-flex justify-content-center">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row post-cards mt-6">
        {{-- Plateform & Device --}}
        <div class="col-lg-6 col-md-6 col-12">
            {{-- Average rating --}}
            <div class="card">
                <div class="card-header pb-0 ">
                    <h2 class="text-bold-700">Average Rating</h2>
                </div>
                <div class="card-body" id="average_rating_container">
                    <h2 class="text-bold-700 text-center" id="average_rating" style="font-size:3.5rem;color:#E77F01">
                        Loading...
                    </h2>
                </div>
            </div>
            {{-- Calls --}}
            <div class="card mt-6">
                <div class="card-header pb-0">
                    <h2 class="text-bold-700">Calls</h2>
                </div>
                <div class="card-body">
                    <div class="mt-auto calls_clicks_graph_container" style="display: none;">
                        <canvas id="calls_clicks_graph" height="130"></canvas>
                    </div>
                    <div class="align-items-center justify-content-center no-data click-default ">
                        <h2 class="text-bold-700 mt-1 calls_clicks_loading"> Loading data... </h2>
                    </div>
                </div>
            </div>
            {{-- Directions --}}
            <div class="card mt-6">
                <div class="card-header pb-0">
                    <h2 class="text-bold-700">Directions</h2>
                </div>
                <div class="card-body">
                    <div class="mt-auto direction_clicks_graph_container" style="display: none;">
                        <canvas id="direction_clicks_graph" height="130"></canvas>
                    </div>
                    <div class="align-items-center justify-content-center no-data click-default ">
                        <h2 class="text-bold-700 mt-1 direction_clicks_loading"> Loading data... </h2>
                    </div>
                </div>
            </div>
            {{-- Bookings --}}
            <div class="card mt-6">
                <div class="card-header pb-0">
                    <h2 class="text-bold-700">Bookings</h2>
                </div>
                <div class="card-body">
                    <div class="mt-auto bookings_graph_container" style="display: none;">
                        <canvas id="booking_graph" height="130"></canvas>
                    </div>
                    <div class="align-items-center justify-content-center no-data click-default">
                        <h2 class="text-bold-700 mt-1 bookings_loading"> Loading data... </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 mt-md-0 mt-6 col-12">
            <div class="facebook-likes">
                {{-- Reviews --}}
                <div class="card">
                    <div class="card-header pb-0 ">
                        <h2 class="text-bold-700">Reviews</h2>
                    </div>
                    <div class="card-body" id="reviews_count_container">
                        <h2 class="text-bold-700 text-center" id="reviews_count" style="font-size:3.5rem;color:#E77F01">
                            Loading...</h2>
                    </div>
                </div>

                <div class="card mt-6">
                    <div class="card-header pb-0">
                        <h2 class="text-bold-700">Website Clicks</h2>
                    </div>
                    <div class="card-body">
                        <div class="mt-auto website_clicks_graph_container" style="display: none;">
                            <canvas id="website_clicks_graph" height="130"></canvas>
                        </div>
                        <div class="align-items-center justify-content-center no-data click-default ">
                            <h2 class="text-bold-700 mt-1 website_clicks_loading"> Loading data... </h2>
                        </div>
                    </div>
                </div>

                <div class="card mt-6">
                    <div class="card-header pb-0">
                        <h2 class="text-bold-700">Messages</h2>
                    </div>
                    <div class="card-body">
                        <div class="mt-auto messages_graph_container" style="display: none;">
                            <canvas id="messages_graph" height="130"></canvas>
                        </div>
                        <div class="align-items-center justify-content-center no-data click-default">
                            <h2 class="text-bold-700 mt-1 messages_loading"> Loading data... </h2>
                        </div>
                    </div>
                </div>
                <div class="card mt-6">
                    <div class="card-header pb-0">
                        <h2 class="text-bold-700">Food Orders</h2>
                    </div>
                    <div class="card-body">
                        <div class="mt-auto food_orders_graph_container" style="display: none;">
                            <canvas id="food_orders_graph" height="130"></canvas>
                        </div>
                        <div class="align-items-center justify-content-center no-data click-default">
                            <h2 class="text-bold-700 mt-1 food_orders_loading"> Loading data... </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('extra-js-scripts')
<script>
    var plateform_and_device_graph =  (graph_lables,values) => {
		const apexChart = "#plateform_device_graph";
		var options = {
            plotOptions:{
                pie:{
                    dataLabels:{
                        offset: -18,
                        minAngleToShowLabel: 5
                    },
                },
            },
			series: values,
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
            labels : graph_lables,
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
			colors: ['#3755A9' , '#E77F01','#ffba81','#22d0ff']
		};

		var chart = new ApexCharts(document.querySelector(apexChart), options);
		chart.render();
	}
    var desktop_vs_mobile_graph =  (graph_lables,values) => {

		const apexChart = "#desktop_vs_mobile_graph";
		var options = {
            plotOptions:{
                pie:{
                    dataLabels:{
                        offset: -18,
                        minAngleToShowLabel: 5,
                    },
                },
            },
			series: values,
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
            labels : graph_lables,
			responsive: [{
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
    var search_vs_maps_graph =  (graph_lables,values) => {

		const apexChart = "#search_vs_maps_graph";
		var options = {
            plotOptions:{
                pie:{
                    dataLabels:{
                        offset: -18,
                        minAngleToShowLabel: 5
                    },
                },
            },
			series: values,
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
            labels : graph_lables,
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
    var calls_clicks_graph = (labels, value) => {
        var ctx = document.getElementById("calls_clicks_graph").getContext('2d');
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
    var website_clicks_graph = (labels, value) => {
        var ctx = document.getElementById("website_clicks_graph").getContext('2d');
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
    var direction_clicks_graph = (labels, value) => {
        var ctx = document.getElementById("direction_clicks_graph").getContext('2d');
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
    var messages_graph = (labels, value) => {

        var ctx = document.getElementById("messages_graph").getContext('2d');
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
    var booking_graph = (labels, value) => {
        var ctx = document.getElementById("booking_graph").getContext('2d');
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
    var food_orders_graph = (labels, value) => {
        var ctx = document.getElementById("food_orders_graph").getContext('2d');
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
    var impression_graph = (labels, value) => {

        var ctx = document.getElementById("impression_graph").getContext('2d');
        Chart.defaults.global.legend.display = false;
        var twitterFollower = new Chart(ctx, {
        type: 'bar',
        data: {
                labels: labels,
                datasets: [{
                    label: 'Impressions',
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
    var interactions_graph = (labels, value) => {

        var ctx = document.getElementById("interactions_graph").getContext('2d');
        Chart.defaults.global.legend.display = false;
        var twitterFollower = new Chart(ctx, {
        type: 'bar',
        data: {
                labels: labels,
                datasets: [{
                    label: 'Interactions',
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

        const page_ids = urlParams.get('page_id');
        const start_date = urlParams.get('start_date');
        const end_date = urlParams.get('end_date');
        getCounts();
        var callData = getCallWebsiteCliksData();
        var MessageData = getMessageBookingsFoodOrderData();

        if(callData && MessageData){
            getInteractionData();
        }
        /* Total followers count  */
        function getCounts(){
            $.ajax({
                url: "{{ route('analytics.google-business.ajax', ['type' => 'platform_device']) }}",
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

                    const {plateform_device,desktop_vs_mobile,search_vs_maps,impressions} = res.data
                    if(res.status == '200'){
                        $('.plateform_device_loading').hide();
                        $('.impression_graph_loading').hide();
                        plateform_and_device_graph(plateform_device['lables'], plateform_device['values']);
                        desktop_vs_mobile_graph(desktop_vs_mobile['lables'],desktop_vs_mobile['values']);
                        search_vs_maps_graph(search_vs_maps['lables'],search_vs_maps['values']);
                        $('#impression_graph_container').show();
                        impression_graph(impressions['lables'],impressions['values']);
                    }else{
                        $('.plateform_device_loading').text(res.data);
                        $('.impression_graph_loading').text(res.data);
                    }
                },
                error: function(e){

                }
            })
        }
        /*  Calls,Website,Direction Clicks Data */
        function getCallWebsiteCliksData(){
            var isGetCallWebsiteCliksDataCompleted = false;
                $.ajax({
                url: "{{ route('analytics.google-business.ajax', ['type' => 'calls_website_direction']) }}",
                type: 'POST',
                data: {
                    _token: '{{csrf_token()}}',
                    page_ids,
                    start_date,
                    end_date,
                },
                async: false,
                cache: false,
                success: function(res){

                    const {calls_clicks_keys,calls_clicks_values,website_clicks_keys,website_clicks_values,direction_clicks_keys,direction_clicks_values} = res.data
                    if(res.status == '200'){
                        $('.calls_clicks_loading').hide();
                        $('.website_clicks_loading').hide();
                        $('.direction_clicks_loading').hide();
                        $('.calls_clicks_graph_container').show();
                        $('.website_clicks_graph_container').show();
                        $('.direction_clicks_graph_container').show();
                        calls_clicks_graph(calls_clicks_keys,calls_clicks_values)
                        website_clicks_graph(website_clicks_keys,website_clicks_values);
                        direction_clicks_graph(direction_clicks_keys,direction_clicks_values)
                    }else{
                        $('.calls_clicks_loading').text(res.data);
                        $('.website_clicks_loading').text(res.data);
                        $('.direction_clicks_loading').text(res.data);
                    }
                    isGetCallWebsiteCliksDataCompleted = true;
                },
                error: function(e){

                }
            });
            return isGetCallWebsiteCliksDataCompleted;
        }

        function getMessageBookingsFoodOrderData(){
            var isGetMessageBookingsFoodOrderDataCompleted = false;
            /* Message, Bookings,Food Order Clicks Data */
            $.ajax({
                url: "{{ route('analytics.google-business.ajax', ['type' => 'message_booking_food']) }}",
                type: 'POST',
                data: {
                    _token: '{{csrf_token()}}',
                    page_ids,
                    start_date,
                    end_date,
                },
                async: false,
                cache: false,
                success: function(res){
                    const {messages_keys,messages_values,bookings_keys,bookings_values,food_orders_keys,food_orders_values} = res.data
                    if(res.status == '200'){
                        $('.messages_loading').hide();
                        $('.bookings_loading').hide();
                        $('.food_orders_loading').hide();
                        $('.messages_graph_container').show();
                        $('.bookings_graph_container').show();
                        $('.food_orders_graph_container').show();
                        messages_graph(messages_keys,messages_values)
                        booking_graph(bookings_keys,bookings_values);
                        food_orders_graph(food_orders_keys,food_orders_values)
                    }else{
                        $('.messages_loading').text(res.data);
                        $('.bookings_loading').text(res.data);
                        $('.food_orders_loading').text(res.data);
                    }
                    isGetMessageBookingsFoodOrderDataCompleted = true;
                },
                error: function(e){

                }
            });
            return isGetMessageBookingsFoodOrderDataCompleted;
        }
        function getInteractionData(){
            $.ajax({
                url: "{{ route('analytics.google-business.ajax', ['type' => 'interactions']) }}",
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

                    const {interactions_graph_labels,interactions_graph_values} = res.data
                    if(res.status == '200'){
                        $('.interactions_graph_loading').hide();
                        $('#interactions_graph_container').show();
                        interactions_graph(interactions_graph_labels,interactions_graph_values)
                    }else{
                        $('.messages_loading').text(res.data);
                    }
                },
                error: function(e){

                }
            });
        }
    });
</script>
@endpush