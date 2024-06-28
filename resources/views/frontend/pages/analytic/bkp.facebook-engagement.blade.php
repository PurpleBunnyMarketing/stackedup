@php
//engagment
$engagementstype = [
    'other' => 0,
    'likes' => 0,
    'share' => 0,
    'comment' => 0,
];

$audienceengagement = [];

$facebook_engaged_analytic = $analytic->facebook_engaged_analytic ?? '{}';
$facebook_engaged_analytic = json_decode($facebook_engaged_analytic,true);

foreach($facebook_engaged_analytic as $fla){
    //engagment donut
    if(isset($fla['page_enaged_all'][0]['values'])){
        $engagementstype['likes'] = isset($fla['page_enaged_all'][0]['values']) ? array_sum(array_column(array_column($fla['page_enaged_all'][0]['values'],'value'),'like')) : $engagementstype['likes'] ;
        $engagementstype['other'] = isset($fla['page_enaged_all'][0]['values']) ? array_sum(array_column(array_column($fla['page_enaged_all'][0]['values'],'value'),'other')) : $engagementstype['other'] ;
        $engagementstype['share'] = isset($fla['page_enaged_all'][0]['values']) ? array_sum(array_column(array_column($fla['page_enaged_all'][0]['values'],'value'),'link')) : $engagementstype['share'] ;
        $engagementstype['comment'] = isset($fla['page_enaged_all'][0]['values']) ? array_sum(array_column(array_column($fla['page_enaged_all'][0]['values'],'value'),'comment')) : $engagementstype['comment'] ;
    }

    //engaged chart

    if(isset($fla['page_enaged_all'][0]['values'])){
        foreach($fla['page_enaged_all'][0]['values'] as $key => $value){
            $date = \Carbon\Carbon::parse($value['end_time']['date'])->subDay()->format('d M');
            $audienceengagement[$date] = isset($audienceengagement[$date]) ? ($audienceengagement[$date] + array_sum($value['value'])) : (count($value['value']) > 0 ? array_sum($value['value']) : 0);
        }
    }

}
$fbengageddonut = [
    $engagementstype['likes'],
    $engagementstype['other'],
    $engagementstype['share'],
    $engagementstype['comment']
];

$fb6key = array_keys($audienceengagement);
$fb6value = array_values($audienceengagement);

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
                <div class="card-header border-0 pb-0">
                    {{-- <div class="avatar bg-rgba-warning p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-users text-primary font-medium-5"></i>
                        </div>
                    </div> --}}
                    <h2 class="text-bold-700">Engagements</h2>
                </div>
                <div class="card-body">
                    @if(array_sum($fbengageddonut) > 0)
                    <div class="facebook-graph">
                        <div id="facebook_5" class="d-flex justify-content-center"></div>
                    </div>
                    @else
                    <div class="d-flex align-items-center justify-content-center no-data">
                        <h2 class="text-bold-700 "> No data found </h2>
                    </div>
                    {{-- <h2 class="text-bold-700 mt-1 py-12 h-100 d-flex align-items-center justify-content-center"> No
                        data
                        found </h2> --}}
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-12">
            <div class="card h-100">
                <div class="card-header border-0 pb-0">
                    {{-- <div class="avatar bg-rgba-warning p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-users text-primary font-medium-5"></i>
                        </div>
                    </div> --}}
                    <h2 class="text-bold-700">Audience Engagement</h2>
                </div>
                <div class="card-body">
                    @if($audienceengagement)
                    <canvas id="facebook_6" height="145"></canvas>
                    @else
                    <div class="d-flex align-items-center justify-content-center no-data">
                        <h2 class="text-bold-700"> No data found </h2>
                    </div>
                    {{-- <h2 class="text-bold-700 mt-1 py-12 h-100 d-flex align-items-center justify-content-center"> No
                        data
                        found </h2> --}}
                    @endif
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
    const fbengageddonut = @php echo json_encode($fbengageddonut) @endphp;

    var facebookchart5 =  () => {
		const apexChart = "#facebook_5";
		var options = {
			series:fbengageddonut ,
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

    var facebookchart6 = () => { 

        var labels = @php echo json_encode($fb6key); @endphp;
        var value =  @php  echo json_encode($fb6value); @endphp;
        var ctx = document.getElementById("facebook_6").getContext('2d');;
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

    jQuery(document).ready(function() {      
        facebookchart5();

        @if($audienceengagement)
            facebookchart6();
        @endif
    });
</script>
@endpush