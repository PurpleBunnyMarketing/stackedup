@php
    $isFacebookConnected = $connectedSocialMedia->where('media_id',1)->first();
@endphp
<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader">
    <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex  post-type">
                <a href="javascript:;" class="post-link btn font-size-h3
                    font-weight-bold my-2 mr-5" data-type="posts">Facebook - Engagement</a>
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
            <div class="card">
                <div class="card-header pb-0">
                    <h2 class="text-bold-700">Engagements</h2>
                </div>
                <div class="card-body">
                    <div class="facebook-graph facebook_engagement" style="display: none;">
                        <div id="facebook_engagement" class="d-flex justify-content-center"></div>
                    </div>
                    <div class="align-items-center justify-content-center no-data facebook_engagement_init">
                        <h2 class="text-bold-700 mt-1"> Loading data... </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h2 class="text-bold-700">Audience Engagement</h2>
                </div>
                <div class="card-body">
                    <div class="facebook-graph facebook_audience_engagement" style="display: none;">
                        <canvas id="facebook_audience_engagement" height="100"></canvas>
                    </div>
                    <div class="align-items-center justify-content-center no-data facebook_audience_engagement_init">
                        <h2 class="text-bold-700 mt-1"> Loading data... </h2>
                    </div>
                </div>
            </div>

        </div>
    </div> 
    @else
        <h3 style="color:rgb(231,127,1)">No Data Found</h3>
    @endif  
</div>

@push('extra-js-scripts')
<script>
/* Top Followers By Job Function */

let fbengageddonut = []
let facebook_like_age_arr = {
        labels : [],
        value : [],
    };

let facebook_engagement =  () => {
    const apexChart = "#facebook_engagement";
    var options = {
        series:fbengageddonut,
        chart: {
            width: 380,
            type: 'pie',
        },
        legend : {
            show: true,
            position:'bottom'
        },
        labels : ['Like','Other','Share','Comments'],
        responsive: [{
            // breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }],
        dataLabels: {
            enabled: true,
            style: {
            fontSize: "140px",
            fontFamily: "Helvetica, Arial, sans-serif",
            fontWeight: "bold"
            }
        },
        colors: ['#3755A9' , '#E77F01','#f44561','#39ac70']
    };

    var chart = new ApexCharts(document.querySelector(apexChart), options);
    chart.render();
}

let facebook_audience_engagement = () => { 

    let ctx = document.getElementById("facebook_audience_engagement").getContext('2d');;
    let twitterFollower = new Chart(ctx, {
    type: 'line',
    data: {
            labels: facebook_audience_engagement_arr.labels,
            datasets: [{
                label: 'Audience Engagement',
                data: facebook_audience_engagement_arr.value,
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
                    callback: (t, i) => i % 3 ? '' : facebook_audience_engagement_arr.labels[i]
                }
            }]
            }
        }
    });
}

jQuery(document).ready(function() {
    var urlParams = new URLSearchParams(window.location.search);    
    const start_date = urlParams.get('start_date');
    const end_date = urlParams.get('end_date');
    const page_id = urlParams.get('page_id');
    $.ajax({
        url: "{{ route('analytics.facebook.ajax', ['type' => 'facebook_engagement']) }}",
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

            // Engagement
            fbengageddonut = res.data.fbengageddonut;
            if(fbengageddonut[0] > 0 || fbengageddonut[1] > 0 || fbengageddonut[2] > 0 || fbengageddonut[3] > 0) {
                $('.facebook_engagement_init').hide();
                $('.facebook_engagement').show();
                facebook_engagement();
            } else {
                $('.facebook_engagement_init').html(`<h2 class="text-bold-700 mt-1"> No data found<h2>`);
            }   

            // facebook_audience_engagement
            facebook_audience_engagement_arr = res.data.facebook_audience_engagement_arr;
            if(facebook_audience_engagement_arr.labels || facebook_audience_engagement_arr.value) {
                $('.facebook_audience_engagement_init').hide();
                $('.facebook_audience_engagement').show();
                facebook_audience_engagement();
            } else {
                $('.facebook_audience_engagement_init').html(`<h2 class="text-bold-700 mt-1"> No data found<h2>`);
            }   
        },
        error: function(e){

        }
    })
})
</script>
@endpush