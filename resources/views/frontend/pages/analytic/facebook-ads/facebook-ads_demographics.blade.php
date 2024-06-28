<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader">

</div>
<div class="container">

    <div class="row post-cards">
        {{-- Organic VS Paid Likes --}}
        <div class="col-lg-6 col-md-6 col-12">
            <div class="card">
                <div class="card-header  pb-0">
                    <h2 class="text-bold-700">Gender</h2>
                </div>
                <div class="card-body">
                    <div id="facebook_ads_gender_graph_container" style="display: none">
                        <div id="facebook_ads_gender_pie_chart" class="d-flex justify-content-center"></div>
                    </div>
                    <div class="align-items-center justify-content-center no-data"
                        id="facebook_ads_gender_graph_loading_text">
                        <h2 class="d-flex justify-content-center loading-text">Loading...</h2>
                    </div>
                </div>
            </div>
        </div>
        {{-- Total Likes --}}
        <div class="col-lg-6 col-md-6 mt-md-0 mt-6 col-12">
            <div class="card">
                <div class="card-header  pb-0">
                    <h2 class="text-bold-700">Age</h2>
                </div>
                <div class="card-body">
                    <div id="facebook_ads_age_graph_container" style="display: none;">
                        <div id="facebook_ads_age_graph_pie_chart" class="d-flex justify-content-center"></div>
                    </div>
                    <div class="align-items-center justify-content-center no-data"
                        id="facebook_ads_age_graph_loading_text">
                        <h2 class="d-flex justify-content-center loading-text">Loading...</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- @else
    <h3 style="color:rgb(231,127,1)">No Data Found</h3>
    @endif
    <div class="custom-pagination d-flex justify-content-center flex-wrap py-2 mr-3 pagination slider-pagination">
    </div> --}}
</div>

@push('extra-js-scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.min.js" type="text/javascript"></script>
<script>
    var facebook_ads_gender_pie_chart =  (lables,values) => {
		const apexChart = "#facebook_ads_gender_pie_chart";
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
            labels : lables,
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
			colors: ['#8CD3FF','#2A4B9B','#F8B478']
		};

		var chart = new ApexCharts(document.querySelector(apexChart), options);
		chart.render();
	}

    var facebook_ads_age_pie_chart =  (lables,values) => {

		const apexChart = "#facebook_ads_age_graph_pie_chart";
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
			colors: ['#8CD3FF','#2A4B9B','#F8B478','#368FFB','#c9742a','#2b64a5']

		};

		var chart = new ApexCharts(document.querySelector(apexChart), options);
		chart.render();
	}



    jQuery(document).ready(function() {

        var urlParams           =   new URLSearchParams(window.location.search);
        var page_ids            =   urlParams.get('page_id');
        var start_date          =   urlParams.get('start_date');
        var end_date            =   urlParams.get('end_date');

        const loading_text                          =   'Loading...';
        const no_data_text                          =   'No details found';

        initialLoading();

        function initialLoading(){
            getFacebookAdsGenderData();
            getFacebookAdsAgeData();
        }

        function getFacebookAdsGenderData(){
            $.ajax({
                url: "{{ route('analytics.facebook_ads.ajax', ['type' => 'demographics_gender_pie_chart']) }}",
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

                    // setItem(ls_acquisition_channels_pie_charts, res.data);

                    $('#facebook_ads_gender_graph_container').css('display', '');
                    $('#facebook_ads_gender_graph_loading_text').css('display', 'none');
                    facebook_ads_gender_pie_chart(lables,values);
                },
                error: function(jqXHR, error, errorThrown) {
                    $('#facebook_ads_gender_graph_loading_text').html(no_data_text);
                }
            });
        }
        function getFacebookAdsAgeData(){
            $.ajax({
                url: "{{ route('analytics.facebook_ads.ajax', ['type' => 'demographics_age_pie_chart']) }}",
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
                    // setItem(ls_acquisition_channels_pie_charts, res.data);

                    $('#facebook_ads_age_graph_container').css('display', 'block');
                    $('#facebook_ads_age_graph_loading_text').css('display', 'none');

                    facebook_ads_age_pie_chart(lables,values);
                },
                error: function(jqXHR, error, errorThrown) {
                    $('#facebook_ads_age_graph_loading_text').html(no_data_text);
                }
            });
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