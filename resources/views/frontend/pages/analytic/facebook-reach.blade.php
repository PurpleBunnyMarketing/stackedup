@php
    $isFacebookConnected = $connectedSocialMedia->where('media_id',1)->first();
@endphp
<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader">
    <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex  post-type">
                <a href="javascript:;" class="post-link btn font-size-h3
                    font-weight-bold my-2 mr-5" data-type="posts">Facebook - Reach</a>
            </div>
        </div>
        <div class="d-flex align-items-center">
        </div>
    </div>
</div>
<div class="container">
    @if(isset($analytic->id) && isset($isFacebookConnected))
        <div class="row post-cards">
            <div class="col-lg-6 col-md-6 mb-6 mb-md-0 col-12">
                {{-- Total Reach --}}
                <div class="card mt-6">
                    <div class="card-header pb-0">
                        <h2 class="text-bold-700">Total Reach</h2>
                    </div>
                    <div class="card-body">
                        <div class="facebook-graph facebook_total_reach" style="display: none;">
                            <canvas id="facebook_total_reach" height="100"></canvas>
                        </div>
                        <div class="align-items-center justify-content-center no-data facebook_total_reach_init">
                            <h2 class="text-bold-700 mt-1"> Loading data... </h2>
                        </div>
                    </div>
                </div>
                {{-- Reach By Gender --}}
                <div class="card mt-6">
                    <div class="card-header pb-0">
                        <h2 class="text-bold-700">Reach By Gender</h2>
                    </div>
                    <div class="card-body">
                        <div class="facebook-graph facebook_gender_reach" style="display: none;">
                            <div id="facebook_gender_reach" class="d-flex justify-content-center"></div>
                        </div>
                        <div class="align-items-center justify-content-center no-data facebook_gender_reach_init">
                            <h2 class="text-bold-700 mt-1"> Loading data... </h2>
                        </div>
                    </div>
                </div>
                {{-- Organic VS Paid --}}
                <div class="card mt-6">
                    <div class="card-header pb-0">
                        <h2 class="text-bold-700">Organic VS Paid</h2>
                    </div>
                    <div class="card-body">
                        <div class="facebook-graph facebook_organic_vs_paid_reach" style="display: none;">
                            <div id="facebook_organic_vs_paid_reach" class="d-flex justify-content-center"></div>
                        </div>
                        <div class="align-items-center justify-content-center no-data facebook_organic_vs_paid_reach_init">
                            <h2 class="text-bold-700 mt-1"> Loading data... </h2>
                        </div>
                    </div>
                </div>

                {{-- Top Countries --}}
                <div class="card mt-6">
                    <div class="card-header  pb-0">
                        <h2 class="text-bold-700">Top Countries</h2>
                    </div>
                    <div class="card-body">
                        <div class="reach_countries table-responsive" style="display: none;">
                            
                        </div>
                        <div class="align-items-center justify-content-center no-data reach_countries_init">
                            <h2 class="text-bold-700 mt-1"> Loading data.. </h2>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-6 col-md-6 mb-6 mb-md-0 col-12">
                {{-- Video View --}}
                <div class="card mt-6">
                    <div class="card-header pb-0">
                        <h2 class="text-bold-700">Video View</h2>
                    </div>
                    <div class="card-body">
                        <div class="facebook-graph facebook_reach_video_view" style="display: none;">
                            <div id="facebook_reach_video_view" class="d-flex justify-content-center"></div>
                        </div>
                        <div class="align-items-center justify-content-center no-data facebook_reach_video_view_init">
                            <h2 class="text-bold-700 mt-1"> Loading data... </h2>
                        </div>
                    </div>
                </div>
                {{-- Engagement By Age --}}
                <div class="card mt-6">
                    <div class="card-header pb-0">
                        <h2 class="text-bold-700">Engagement By Age</h2>
                    </div>
                    <div class="card-body">
                        <div class="facebook-graph facebook_reach_engagement_age" style="display: none;">
                            <canvas id="facebook_reach_engagement_age" height="100"></canvas>
                        </div>
                        <div class="align-items-center justify-content-center no-data facebook_reach_engagement_age_init">
                            <h2 class="text-bold-700 mt-1"> Loading data... </h2>
                        </div>
                    </div>
                </div>
                {{-- Top Cities --}}
                <div class="card mt-6">
                    <div class="card-header  pb-0">
                        <h2 class="text-bold-700">Top Cities</h2>
                    </div>
                    <div class="card-body">
                        <div class="reach_cities table-responsive" style="display: none;">
                            
                        </div>
                        <div class="align-items-center justify-content-center no-data reach_cities_init">
                            <h2 class="text-bold-700 mt-1"> Loading data.. </h2>
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
let facebook_total_reach_arr = {
    labels : [],
    value : [],
};
let facebook_reach_engagement_age_arr = {
    labels : [],
    value : [],
};
let facebook_gender_reach_arr = [];
let facebook_organic_vs_paid_reach_arr = [];
let facebook_reach_video_view_arr = [];
let reachCountryObj = {};
let reachCityObj = {};

let facebook_total_reach = () => {
        
    var labels = facebook_total_reach_arr?.lables;
    var value = facebook_total_reach_arr?.value;
    var ctx = document.getElementById("facebook_total_reach").getContext('2d');;
    var twitterFollower = new Chart(ctx, {
        type: 'line',
        data: {
                labels: labels,
                datasets: [{
                    label: 'Total Reach',
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

let facebook_gender_reach =  () => {
    
    const apexChart = "#facebook_gender_reach";
    let options = {
        series:facebook_gender_reach_arr ,
        chart: {
            width: 380,
            type: 'pie',
        },
        legend : {
            show: true,
            position:'bottom'
        },
        labels : ['Male','Female','Unspecified'],
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
        colors: ['#3755A9' , '#E77F01','#39ac70']
    };

    var chart = new ApexCharts(document.querySelector(apexChart), options);
    chart.render();
}

let facebook_organic_vs_paid_reach =  () => {
    const apexChart = "#facebook_organic_vs_paid_reach";
    var options = {
        series:facebook_organic_vs_paid_reach_arr ,
        chart: {
            width: 380,
            type: 'pie',
        },
        legend : {
            show: true,
            position:'bottom'
        },
        labels : ['Organic','Paid'],
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

var facebook_reach_engagement_age = () => {
    
    var labels = facebook_reach_engagement_age_arr.labels;
    var value =  facebook_reach_engagement_age_arr.value;
    var ctx = document.getElementById("facebook_reach_engagement_age").getContext('2d');
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
                }]
            }
        }
    });
}

var facebook_reach_video_view =  () => {
    const apexChart = "#facebook_reach_video_view";
    var options = {
        series:facebook_reach_video_view_arr ,
        chart: {
            width: 380,
            type: 'pie',
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


jQuery(document).ready(function() {
    var urlParams = new URLSearchParams(window.location.search);    
    const start_date = urlParams.get('start_date');
    const end_date = urlParams.get('end_date');
    const page_id = urlParams.get('page_id');
    $.ajax({
        url: "{{ route('analytics.facebook.ajax', ['type' => 'facebook_reach']) }}",
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

            // facebook_total_reach
            facebook_total_reach_arr = res.data.facebook_total_reach_arr;
            if(facebook_total_reach_arr?.labels || facebook_total_reach_arr?.value) {
                $('.facebook_total_reach_init').hide();
                $('.facebook_total_reach').show();
                facebook_total_reach();
            } else {
                $('.facebook_total_reach_init').html(`<h2 class="text-bold-700 mt-1"> No data found<h2>`);
            }   

            // facebook_gender_reach
            facebook_gender_reach_arr = res.data.facebook_gender_reach_arr;
            if(facebook_gender_reach_arr[0] > 0 || facebook_gender_reach_arr[1] > 0 || facebook_gender_reach_arr[2] > 0) {
                $('.facebook_gender_reach_init').hide();
                $('.facebook_gender_reach').show();
                facebook_gender_reach();
            } else {
                $('.facebook_gender_reach_init').html(`<h2 class="text-bold-700 mt-1"> No data found<h2>`);
            }  
            
            // facebook_organic_vs_paid_reach
            facebook_organic_vs_paid_reach_arr = res.data.facebook_organic_vs_paid_reach_arr;
            if(facebook_organic_vs_paid_reach_arr[0] > 0 || facebook_organic_vs_paid_reach_arr[1] > 0) {
                $('.facebook_organic_vs_paid_reach_init').hide();
                $('.facebook_organic_vs_paid_reach').show();
                facebook_organic_vs_paid_reach();
            } else {
                $('.facebook_organic_vs_paid_reach_init').html(`<h2 class="text-bold-700 mt-1"> No data found<h2>`);
            }  

            //Countries
            reachCountryObj = res.data.reach_countries;
            if(Object.keys(reachCountryObj).length > 0) {
                $('.reach_countries_init').hide();
                $('.reach_countries').show();
                reachCountryHtml();
            } else {
                $('.reach_countries_init').html(`<h2 class="text-bold-700 mt-1"> No data found<h2>`);
            } 
            //cities
            reachCityObj = res.data.reach_cities;
            if(Object.keys(reachCityObj).length > 0) {
                $('.reach_cities_init').hide();
                $('.reach_cities').show();
                reachCityHtml();
            } else {
                $('.reach_cities_init').html(`<h2 class="text-bold-700 mt-1"> No data found<h2>`);
            }  

            // facebook_reach_video_view
            facebook_reach_video_view_arr = res.data.facebook_reach_video_view_arr;
            if(facebook_reach_video_view_arr[0] > 0 || facebook_reach_video_view_arr[1] > 0) {
                $('.facebook_reach_video_view_init').hide();
                $('.facebook_reach_video_view').show();
                facebook_reach_video_view();
            } else {
                $('.facebook_reach_video_view_init').html(`<h2 class="text-bold-700 mt-1"> No data found<h2>`);
            }  

            // facebook_reach_engagement_age
            facebook_reach_engagement_age_arr = res.data.facebook_reach_engagement_age_arr;
            if(facebook_reach_engagement_age_arr?.labels || facebook_reach_engagement_age_arr?.value) {
                $('.facebook_reach_engagement_age_init').hide();
                $('.facebook_reach_engagement_age').show();
                facebook_reach_engagement_age();
            } else {
                $('.facebook_reach_engagement_age_init').html(`<h2 class="text-bold-700 mt-1"> No data found<h2>`);
            }   
        },
        error: function(e){

        }
    })

    let reachCountryHtml = () => {       
        let cls = '';
        if(Object.keys(reachCountryObj).length > 0){
            $('.reach_countries').css({"max-height": "400px;","overflow-y":"scroll"})
            cls = "table-bordered";
        }
        let conHtml = `<table class="table ${cls}">`;
        for (const property in reachCountryObj) {
            conHtml += `<tr>
                    <td>${property}</td>
                    <td>${reachCountryObj[property]}</td>
                </tr>`;
        }

        conHtml+= `</table>`;
        $(".reach_countries").html(conHtml)
    }

    let reachCityHtml = () => {       
        let cls = '';
        if(Object.keys(reachCityObj).length > 0){
            $('.reach_cities').css({"max-height": "400px;","overflow-y":"scroll"})
            cls = "table-bordered";
        }
        let conHtml = `<table class="table ${cls}">`;
        for (const property in reachCityObj) {
            conHtml += `<tr>
                    <td>${property}</td>
                    <td>${reachCityObj[property]}</td>
                </tr>`;
        }

        conHtml+= `</table>`;
        $(".reach_cities").html(conHtml)
    }
})

</script>
@endpush