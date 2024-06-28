@php
    $isFacebookConnected = $connectedSocialMedia->where('media_id',1)->first();
@endphp
<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader">
    <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex  post-type">
                <a href="javascript:;" class="post-link btn font-size-h3
                    font-weight-bold my-2 mr-5" data-type="posts">Facebook - Likes</a>
            </div>
        </div>
    </div>
</div>
<div class="container">
    @if(isset($analytic->id) && isset($isFacebookConnected))
    <div class="row post-cards">        
        <div class="col-lg-6 col-md-6 col-12">
            {{-- Organic VS Paid Likes --}}
            <div class="card mt-6">
                <div class="card-header  pb-0">
                    <h2 class="text-bold-700">Organic vs Paid Likes</h2>
                </div>
                <div class="card-body">
                    <div class="facebook-graph facebook_like_paid_organic" style="display: none;">
                        <div id="facebook_like_paid_organic" class="d-flex justify-content-center"></div>
                    </div>
                    <div class="align-items-center justify-content-center no-data facebook_like_paid_organic_init">
                        <h2 class="text-bold-700 mt-1"> Loading data.. </h2>
                    </div>
                </div>
            </div>

            {{-- Audience Age --}}
            <div class="card mt-6">
                <div class="card-header  pb-0">
                    <h2 class="text-bold-700">Audience Age</h2>
                </div>
                <div class="card-body">
                    <div class="facebook-graph facebook_like_age" style="display: none;">
                        <canvas id="facebook_like_age" height="100"></canvas>
                    </div>
                    <div class="align-items-center justify-content-center no-data facebook_like_age_init">
                        <h2 class="text-bold-700 mt-1"> Loading data.. </h2>
                    </div>
                </div>
            </div>
            
            {{-- Top Countries --}}

            <div class="card mt-6">
                <div class="card-header  pb-0">
                    <h2 class="text-bold-700">Top Countries</h2>
                </div>
                <div class="card-body">
                    <div class="countries table-responsive" style="display: none;">
                        
                    </div>
                    <div class="align-items-center justify-content-center no-data countries_init">
                        <h2 class="text-bold-700 mt-1"> Loading data.. </h2>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 col-md-6 mt-md-0 mt-6 col-12">
            <div class="facebook-likes">
                {{-- Total Likes --}}
                <div class="card mt-6">
                    <div class="card-header  pb-0">
                        <h2 class="text-bold-700">Total Likes</h2>
                    </div>
                    <div class="card-body">
                        <div class="facebook-graph total_likes" style="display: none;">
                            <h2  class="text-bold-700 mt-1" id="total_likes" style="font-size:3.5rem;color:#E77F01"></h2>
                        </div>
                        <div class="align-items-center justify-content-center no-data total_likes_init">
                            <h2 class="text-bold-700 mt-1"> Loading data.. </h2>
                        </div>
                    </div>
                </div>
                                
                <div class="card mt-6">
                    <div class="card-header  pb-0">
                        <h2 class="text-bold-700">Audience Growth</h2>
                    </div>
                    <div class="card-body">
                        <div class="facebook-graph facebook_like_audience_growth" style="display: none;">
                            <canvas id="facebook_like_audience_growth" height="100"></canvas>
                        </div>
                        <div class="align-items-center justify-content-center no-data facebook_like_audience_growth_init">
                            <h2 class="text-bold-700 mt-1"> Loading data.. </h2>
                        </div>
                    </div>
                </div>
                {{-- Audience Gender --}}
                <div class="card mt-6">
                    <div class="card-header  pb-0">
                        <h2 class="text-bold-700">Audience Gender</h2>
                    </div>
                    <div class="card-body">
                        <div class="facebook-graph facebook_like_gender" style="display: none;">
                            <div id="facebook_like_gender" class="d-flex justify-content-center"></div>
                        </div>
                        <div class="align-items-center justify-content-center no-data facebook_like_gender_init">
                            <h2 class="text-bold-700 mt-1"> Loading data.. </h2>
                        </div>
                    </div>
                </div>  
                <div class="card mt-6">
                    <div class="card-header  pb-0">
                        <h2 class="text-bold-700">Top Cities</h2>
                    </div>
                    <div class="card-body">
                        <div class="cities table-responsive" style="display: none;">
                            
                        </div>
                        <div class="align-items-center justify-content-center no-data cities_init">
                            <h2 class="text-bold-700 mt-1"> Loading data.. </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
        <h3 style="color:rgb(231,127,1)">No Data Found</h3>
    @endif
    <div class="custom-pagination d-flex justify-content-center flex-wrap py-2 mr-3 pagination slider-pagination"></div>
</div>

@push('extra-js-scripts')
<script>

jQuery(document).ready(function() {
    let facebook_like_paid_organic_arr = [0,0];
    let facebook_like_age_arr = {
        labels : [],
        value : [],
    };
    let facebook_like_audience_growth_arr = {
        labels : [],
        value : [],
        audremovevalue : [],
    };
    let countryObj = {};
    let cityObj = {};
    let facebook_like_gender_arr = [];

    var urlParams = new URLSearchParams(window.location.search);    
    const start_date = urlParams.get('start_date');
    const end_date = urlParams.get('end_date');
    const page_id = urlParams.get('page_id');

    /* Total followers count  */
    $.ajax({
        url: "{{ route('analytics.facebook.ajax', ['type' => 'facebook_like_analytic']) }}",
        type: 'POST',
        data: {
            _token: '{{csrf_token()}}',
            start_date: start_date,
            end_date: end_date,
            page_id: page_id,
            'page_update': '{{ $page_update }}'
        },
        async: true,
        cache: false,
        success: function(r){
            const res = JSON.parse(r); 
            
            // Organic vs Paid Likes Graph
            facebook_like_paid_organic_arr = res.data.facebook_like_paid_organic_arr;
            if(facebook_like_paid_organic_arr[0] > 0 || facebook_like_paid_organic_arr[1] > 0) {
                $('.facebook_like_paid_organic_init').hide();
                $('.facebook_like_paid_organic').show();
                facebook_like_paid_organic();
            } else {
                $('.facebook_like_paid_organic_init').html(`<h2 class="text-bold-700 mt-1"> No data found<h2>`);
            }    

            // Audience Growth Graph
            facebook_like_audience_growth_arr = res.data.facebook_like_audience_growth_arr;
            if(facebook_like_audience_growth_arr.labels || facebook_like_audience_growth_arr.value || facebook_like_audience_growth_arr.audremovevalue) {
                $('.facebook_like_audience_growth_init').hide();
                $('.facebook_like_audience_growth').show();
                facebook_like_audience_growth();
            } else {
                $('.facebook_like_audience_growth_init').html(`<h2 class="text-bold-700 mt-1"> No data found<h2>`);
            }    

            //Audience Age
            facebook_like_age_arr = res.data.facebook_like_age;
            if(facebook_like_age_arr.labels.length>0 || facebook_like_age_arr.value.length>0) {            
                $('.facebook_like_age_init').hide();
                $('.facebook_like_age').show();
                facebook_like_age();
            } else {
                $('.facebook_like_age_init').html(`<h2 class="text-bold-700 mt-1"> No data found<h2>`);
            }  
            
            //Countries
            countryObj = res.data.countries;
            if(Object.keys(countryObj).length > 0) {
                $('.countries_init').hide();
                $('.countries').show();
                countryHtml();
            } else {
                $('.countries_init').html(`<h2 class="text-bold-700 mt-1"> No data found<h2>`);
            }  

            //cities
            cityObj = res.data.cities;
            if(Object.keys(cityObj).length > 0) {
                $('.cities_init').hide();
                $('.cities').show();
                cityHtml();
            } else {
                $('.cities_init').html(`<h2 class="text-bold-700 mt-1"> No data found<h2>`);
            } 

            // Organic vs Paid Likes Graph
            facebook_like_gender_arr = res.data.facebook_like_gender_arr;
            if(facebook_like_gender_arr) {
                $('.facebook_like_gender_init').hide();
                $('.facebook_like_gender').show();
                facebook_like_gender();
            } else {
                $('.facebook_like_gender_init').html(`<h2 class="text-bold-700 mt-1"> No data found<h2>`);
            } 
            
            // Total Likes
            total_likes = res.data.total_likes;
            if(total_likes) {
                $('.total_likes_init').hide();
                $('.total_likes').show();
                $('#total_likes').html(total_likes);
            } else {
                $('.total_likes_init').html(`<h2 class="text-bold-700 mt-1"> No data found<h2>`);
            }
            
        },
        error: function(e){

        }
    });

    let facebook_like_paid_organic =  () => {
		const apexChart = "#facebook_like_paid_organic";
		let options = {
			series:facebook_like_paid_organic_arr,
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

		let chart = new ApexCharts(document.querySelector(apexChart), options);
		chart.render();
	}

    let facebook_like_age = () => {
        
        let ctx = document.getElementById("facebook_like_age").getContext('2d');
        Chart.defaults.global.legend.display = false;
        let facebookLikeAge = new Chart(ctx, {
        type: 'bar',
        data: {
                labels: facebook_like_age_arr.labels,
                datasets: [{
                    label: '',
                    data: facebook_like_age_arr.value,
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

    let countryHtml = () => {       
        let cls = '';
        if(Object.keys(countryObj).length > 0){
            $('.countries').css({"max-height": "400px;","overflow-y":"scroll"})
            cls = "table-bordered";
        }
        let conHtml = `<table class="table ${cls}">`;
        for (const property in countryObj) {
            conHtml += `<tr>
                    <td>${property}</td>
                    <td>${countryObj[property]}</td>
                </tr>`;
        }

        conHtml+= `</table>`;
        $(".countries").html(conHtml)
    }

    let cityHtml = () => {       
        let cls = '';
        if(Object.keys(cityObj).length > 0){
            $('.cities').css({"max-height": "400px;","overflow-y":"scroll"})
            cls = "table-bordered";
        }
        let conHtml = `<table class="table ${cls}">`;
        for (const property in cityObj) {
            conHtml += `<tr>
                    <td>${property}</td>
                    <td>${cityObj[property]}</td>
                </tr>`;
        }

        conHtml+= `</table>`;
        $(".cities").html(conHtml)
    }

    var facebook_like_audience_growth = () => {
        var ctx = document.getElementById("facebook_like_audience_growth").getContext('2d');
        var flagc = new Chart(ctx, {
        type: 'bar',
        data: {
                labels: facebook_like_audience_growth_arr.labels,
                datasets: [{
                    label: 'Gained Followers',
                    data: facebook_like_audience_growth_arr.value,
                    backgroundColor: "#3755A9"
                },
                {
                    label: 'Lost Followers',
                    data: facebook_like_audience_growth_arr.audremovevalue,
                    backgroundColor: "#E77F01"
                }
                ]
            },
            options: {
                legend : {
                    show: true,
                    position:'bottom'
                },
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
                            callback: (t, i) => i % 3 ? '' : facebook_like_audience_growth_arr.labels[i]
                        }
                    }]
                }
            }
        });
    }

    var facebook_like_gender =  () => {
		const apexChart = "#facebook_like_gender";
		var options = {
			series:facebook_like_gender_arr ,
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
                    "fontSize": 14,
                },
            },
            labels : ['Male','Female','Unspecified'],
			responsive: [{
				// breakpoint: 480,
				options: {
					chart: {
						width: 200
					},
					legend: {
						position: 'bottom'
					},
				}
			}],
			colors: ['#3755A9' , '#E77F01','#39ac70']
		};

		var chart = new ApexCharts(document.querySelector(apexChart), options);
		chart.render();
	}


});
</script>
@endpush