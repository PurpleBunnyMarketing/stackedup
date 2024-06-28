<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader">
    <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex  post-type">
                <a href="javascript:;" class="post-link btn font-size-h3
                    font-weight-bold my-2 mr-5" data-type="posts">Instagram - Audiance</a>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="row post-cards">
        {{-- Organic VS Paid Likes --}}
        <div class="col-lg-8 col-md-8 col-12">
            <div class="card">
                <div class="card-header  pb-0">
                    <h2 class="text-bold-700">Age</h2>
                </div>
                <div class="card-body">
                    <div id="instagram_age_bar_chart_container" style="display: none">
                        <canvas id="instagram_age_bar_chart" height="108"></canvas>
                    </div>
                    <div class="align-items-center justify-content-center no-data"
                        id="instagram_age_bar_chart_loading_text_container">
                        <h2 id="instagram_age_bar_chart_loading_text"
                            class="d-flex justify-content-center loading-text">
                            Loading...</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-12 ">
            <div class="card mt-6 mt-lg-0 mt-md-0">
                <div class="card-header  pb-0">
                    <h2 class="text-bold-700">Gender</h2>
                </div>
                <div class="card-body">
                    <div id="instagram_gender_pie_chart_container" style="display: none;">
                        <div id="instagram_gender_pie_chart" class="d-flex justify-content-center"></div>
                    </div>
                    <div class="align-items-center justify-content-center no-data"
                        id="instagram_gender_pie_chart_loading_text_container">
                        <h2 class="d-flex justify-content-center loading-text"
                            id="instagram_gender_pie_chart_loading_text">
                            Loading...</h2>
                    </div>
                </div>
            </div>

        </div>
        {{-- Total Likes --}}

    </div>
    <div class="row post-cards mt-6">
        {{-- Organic VS Paid Likes --}}
        <div class="col-lg-6 col-md-6 col-12">
            <div class="card">
                <div class="card-header  pb-0">
                    <h2 class="text-bold-700">By Countires</h2>
                </div>
                <div class="card-body">
                    <div style="max-height: 400px;overflow-y: scroll;display: none;"
                        id="instagram_country_table_data_container" class="table-responsive">
                        <table class="table table-bordered" id="instagram_country_table_data">
                        </table>
                    </div>
                    <div class="align-items-center justify-content-center no-data"
                        id="instagram_country_table_data_loading_text_container">
                        <h2 class="d-flex justify-content-center loading-text"
                            id="instagram_country_table_data_loading_text">
                            Loading...</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-12">
            <div class="card mt-6 mt-lg-0">
                <div class="card-header  pb-0">
                    <h2 class="text-bold-700">By Cities</h2>
                </div>
                <div class="card-body">
                    <div style="max-height: 400px;overflow-y: scroll;display:none;"
                        id="instagram_city_table_data_container" class="table-responsive">
                        <table class="table table-bordered" id="instagram_city_table_data">
                        </table>
                    </div>
                    <div class="align-items-center justify-content-center no-data"
                        id="instagram_city_table_data_loading_text_container">
                        <h2 class="d-flex justify-content-center loading-text"
                            id="instagram_city_table_data_loading_text">
                            Loading...</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('extra-js-scripts')
<script>
    var instagram_age_bar_chart = (labels,values) => {

        let ctx = document.getElementById("instagram_age_bar_chart").getContext('2d');
        Chart.defaults.global.legend.display = false;
        let facebookLikeAge = new Chart(ctx, {
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
    var instagram_gender_pie_chart =  (lables,values) => {

		const apexChart = "#instagram_gender_pie_chart";
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

    $(function(){
        var urlParams           =   new URLSearchParams(window.location.search);
        var page_ids            =   urlParams.get('page_id');
        var start_date          =   urlParams.get('start_date');
        var end_date            =   urlParams.get('end_date');

        const loading_text                          =   'Loading...';
        const no_data_text                          =   'No details found';

        initialInstagramAudianceDataLoading();

        function initialInstagramAudianceDataLoading(){
            getInstagramAgeBarChartData();
            getInstagramGenderPieChartData();
            getInstagramCountryTableData();
            getInstagramCityTableData();
        }

        function getInstagramAgeBarChartData(){
            $.ajax({
                url: "{{ route('analytics.instagram.ajax', ['type' => 'audiance_age_bar_chart']) }}",
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
                    if('age' in data){
                        $('#instagram_age_bar_chart_container').css('display', '');
                        $('#instagram_age_bar_chart_loading_text_container').css('display', 'none');
                        instagram_age_bar_chart(data.age.key_array,data.age.values_array);
                    }else{
                        $('#instagram_website_clicks_line_chart_loading_text').text(no_data_text);
                    }
                },
                error: function(jqXHR, error, errorThrown) {
                    $('#instagram_age_bar_chart_loading_text').text(no_data_text);


                }
            });
        }
        function getInstagramGenderPieChartData(){
            $.ajax({
                url: "{{ route('analytics.instagram.ajax', ['type' => 'audiance_gender_pie_chart']) }}",
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
                    if('gender' in data){
                        $('#instagram_gender_pie_chart_container').css('display', '');
                        $('#instagram_gender_pie_chart_loading_text_container').css('display', 'none');
                        instagram_gender_pie_chart(data.gender.key_array,data.gender.values_array);
                    }else{
                        $('#instagram_gender_pie_chart_loading_text').text(no_data_text);
                    }

                },
                error: function(jqXHR, error, errorThrown) {
                    $('#instagram_gender_pie_chart_loading_text').text(no_data_text);
                }
            });
        }
        function getInstagramCountryTableData(){
            $.ajax({
                url: "{{ route('analytics.instagram.ajax', ['type' => 'audiance_country_table_data']) }}",
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
                    if('country' in data){
                        $('#instagram_country_table_data_container').css('display', '');
                        $('#instagram_country_table_data_loading_text_container').css('display', 'none');
                        setDataInTable('instagram_country_table_data',data.country.values);
                    }else{
                        $('#instagram_country_table_data_loading_text').text(no_data_text);
                    }

                },
                error: function(jqXHR, error, errorThrown) {
                    $('#instagram_country_table_data_loading_text').text(no_data_text);
                }
            });
        }
        function getInstagramCityTableData(){
            $.ajax({
                url: "{{ route('analytics.instagram.ajax', ['type' => 'audiance_city_table_data']) }}",
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
                    if('city' in data){
                        $('#instagram_city_table_data_container').css('display', '');
                        $('#instagram_city_table_data_loading_text_container').css('display', 'none');
                        setDataInTable('instagram_city_table_data',data.city.values);
                    }else{
                        $('#instagram_city_table_data_loading_text').text(no_data_text);
                    }

                },
                error: function(jqXHR, error, errorThrown) {
                    $('#instagram_city_table_data_loading_text').text(no_data_text);
                }
            });
        }

        function setDataInTable(element,data){

            var html = '';
            $(data).each(function(key,value){

                html += `<tr>
                    <td>${value.location_name}</td>
                    <td>${value.value}</td>
                    </tr>`;
            });
            $(`#${element}`).append(html);
        }

    });

</script>
@endpush