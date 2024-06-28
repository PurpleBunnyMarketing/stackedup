@php

$facebook_react_analytic = $analytic->facebook_react_analytic ?? '{}';
$facebook_react_analytic = json_decode($facebook_react_analytic,true);


$videoviewpaid = 0;
$videovieworganic = 0;
$page_Organic_reach = 0;
$page_paid_reach = 0;


$agereacharr = [
    '13-17' => 0,
    '18-24' => 0,
    '25-34' => 0,
    '35-44' => 0,
    '45-54' => 0,
    '55-64' => 0,
    '65+' => 0,
];

$agereacharr1 = [
    'Male' => 0,
    'Female' => 0,
    'Unspecified' => 0,
];
$totlareachArr = [];
$countryreacharr = [];
$citiesreacharr = [];

foreach($facebook_react_analytic as $fla){
    if(isset($fla['page_video_views_organic'][0]['values'][1]['value'])){
        // $videovieworganic = $fla['page_video_views_organic'][2]['values'][1]['value'] + $videovieworganic;
        $videovieworganic += array_sum(array_column($fla['page_video_views_organic'][0]['values'],'value'));
    }
    if(isset($fla['page_organic_reach'][0]['values'])){
        // $videovieworganic = $fla['page_video_views_organic'][2]['values'][1]['value'] + $videovieworganic;
        $page_Organic_reach += array_sum(array_column($fla['page_organic_reach'][0]['values'],'value'));
    }
    if(isset($fla['page_paid_reach'][0]['values'])){
        // $videovieworganic = $fla['page_video_views_organic'][2]['values'][1]['value'] + $videovieworganic;
        $page_paid_reach += array_sum(array_column($fla['page_paid_reach'][0]['values'],'value'));
    }
    if(isset($fla['page_video_views_paid'][0]['values'][1]['value'])){
        // $videoviewpaid = $fla['page_video_views_paid'][2]['values'][1]['value'] + $videoviewpaid;
        $videoviewpaid += array_sum(array_column($fla['page_video_views_paid'][0]['values'],'value'));
    }

    //page react // gender // age
    if(isset($fla['page_reach_by_gender'][0]['values'])){
        foreach($fla['page_reach_by_gender'][0]['values'] as $key => $value){
            if(isset($value['value'])){
                foreach($value['value'] as $key1 => $value1){
                    if(array_key_exists(substr($key1, 2), $agereacharr)){
                    $agereacharr[substr($key1, 2)] = $agereacharr[substr($key1, 2)] + $value1;
                        if(str_contains($key1,'M.')){
                            $agereacharr1['Male'] += $value1;
                        }
                        if(str_contains($key1,'F.')){
                            $agereacharr1['Female'] += $value1;
                        }
                        if(str_contains($key1,'U.')){
                            $agereacharr1['Unspecified'] += $value1;
                        }
                    }
                }
            }
            if(isset($fla['page_reach'][0]['values'])){
                foreach($fla['page_reach'][0]['values'] as $key => $value){
                    $date = \Carbon\Carbon::parse($value['end_time']['date'])->subDay()->format('d M');
                    $totlareachArr[$date] = $value['value'] ?? 0;
                }
            }
        }
    }
    //country
    $data = [];
    if(isset($fla['page_content_activity_by_country_unique'][0]['values'][1]['value'])){

        $data = array_map(function($value){
            return array_keys($value['value']);
        },$fla['page_content_activity_by_country_unique'][0]['values']);

        $data = array_unique(array_flatten($data));
        foreach ($fla['page_content_activity_by_country_unique'][0]['values'] as $key => $value) {
            foreach ($value['value'] as $key1 => $countryData) {
                if(in_array($key1, $data) && isset($countryreacharr[$key1])){
                $countryreacharr[$key1] += $countryData;
                }else{
                $countryreacharr[$key1] = $countryData;
                }
            }
        }
    }
    // Cities
    if(isset($fla['page_content_activity_by_city_unique'][0]['values'])){
        foreach($fla['page_content_activity_by_city_unique'][0]['values'] as $key => $cities){
            foreach ($cities['value'] as $key => $city) {
                if(isset($citiesreacharr[$key])) $citiesreacharr[$key] += $city;
                else $citiesreacharr[$key] = $city;
            }
        }
    }
}

$videoview = [$videovieworganic,$videoviewpaid];

$trkey = array_keys($totlareachArr);
$trvalue = array_values($totlareachArr);

$organicVsReach = [$page_Organic_reach,$page_paid_reach];
$fbreachbararr = [ $agereacharr1['Male'], $agereacharr1['Female'], $agereacharr1['Unspecified']];

$fbreachkey = array_keys($agereacharr);
$fbreachvalue = array_values($agereacharr);

$isFacebookConnected = $connectedSocialMedia->where('media_id',1)->first();

arsort($countryreacharr);
$countryreacharr = array_slice($countryreacharr,0,10);

arsort($citiesreacharr);
$citiesreacharr = array_slice($citiesreacharr,0,10);
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
        {{-- Total Reach --}}
        <div class="col-lg-6 col-md-6 col-12">
            <div class="card">
                <div class="card-header pb-0">
                    {{-- <div class="avatar bg-rgba-warning p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-users text-primary font-medium-5"></i>
                        </div>
                    </div> --}}
                    <h2 class="text-bold-700">Total Reach</h2>
                </div>
                <div class="card-body">
                    @if($totlareachArr)
                    <canvas id="totalreachline" height="100"></canvas>
                    @else
                    <div class="d-flex align-items-center justify-content-center no-data">
                        <h2 class="text-bold-700"> No data found </h2>
                    </div>
                    {{-- <h2 class="text-bold-700 mt-1 h-100 d-flex align-items-center justify-content-center no-data">
                        No
                        data found </h2> --}}
                    @endif
                </div>
            </div>
            <div class="card mt-6">
                <div class="card-header pb-0">
                    {{-- <div class="avatar bg-rgba-warning p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-users text-primary font-medium-5"></i>
                        </div>
                    </div> --}}
                    <h2 class="text-bold-700 ">Reach By Gender</h2>
                </div>
                <div class="card-body">
                    @if(array_sum($fbreachbararr) > 0)
                    <div class="facebook-graph">
                        <div id="fbreachbar" class="d-flex justify-content-center"></div>
                    </div>
                    @else
                    <div class="d-flex align-items-center justify-content-center no-data">
                        <h2 class="text-bold-700"> No data found </h2>
                    </div>
                    {{-- <h2 class="text-bold-700 mt-1 h-100 d-flex align-items-center justify-content-center no-data">
                        No
                        data found </h2> --}}
                    @endif
                </div>
            </div>
            <div class="card mt-6">
                <div class="card-header pb-0">
                    {{-- <div class="avatar bg-rgba-warning p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-users text-primary font-medium-5"></i>
                        </div>
                    </div> --}}
                    <h2 class="text-bold-700 ">Organic VS Paid</h2>
                </div>
                <div class="card-body">
                    @if(array_sum($organicVsReach) > 0)
                    <div class="facebook-graph">
                        <div id="fbreachbarReach" class="d-flex justify-content-center"></div>
                    </div>
                    @else
                    <div class="d-flex align-items-center justify-content-center no-data">
                        <h2 class="text-bold-700"> No data found </h2>
                    </div>
                    {{-- <h2 class="text-bold-700 mt-1 h-100 d-flex align-items-center justify-content-center no-data">
                        No
                        data found </h2> --}}
                    @endif
                </div>
            </div>
            <div class="card my-6">
                <div class="card-header pb-0">
                    {{-- <div class="avatar bg-rgba-warning p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-users text-primary font-medium-5"></i>
                        </div>
                    </div> --}}
                    <h2 class="text-bold-700 ">Top Countries</h2>
                </div>
                <div class="card-body">
                    <div style="{{ $countryreacharr ? 'max-height: 400px;overflow-y: scroll;' : '' }}"
                        class="table-responsive">
                        <table class="table {{ $countryreacharr ? 'table-bordered' : '' }}">
                            @forelse(getCountriesForAnalytic($countryreacharr) as $key => $value)
                            <tr style="color:{{ $loop->iteration > 3 ? '#3755A9 ': '#E77F01'}}">
                                <td>{{ $key }}</td>
                                <td>{{ $value }}</td>
                            </tr>
                            @empty
                            <tr>
                                <!-- <td colspan="2">No data found</td> -->
                                <td colspan="2" class="border-0 p-0">
                                    <div class="d-flex align-items-center justify-content-center no-data">
                                        <h2 class="text-bold-700"> No data found </h2>
                                    </div>
                                    {{-- <h2
                                        class="text-bold-700 mt-1 h-100 d-flex align-items-center justify-content-center no-data">
                                        No data found </h2> --}}
                                </td>
                            </tr>
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- Video View --}}
        <div class="col-lg-6 col-md-6 col-12">
            <div class="card ">
                <div class="card-header pb-0">
                    {{-- <div class="avatar bg-rgba-warning p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-users text-primary font-medium-5"></i>
                        </div>
                    </div> --}}
                    <h2 class="text-bold-700">Video View</h2>
                </div>
                <div class="card-body">
                    @if(array_sum($videoview) > 0)
                    <div class="facebook-graph">
                        <div id="videoviewdonut" class="d-flex justify-content-center"></div>
                    </div>
                    @else
                    <div class="d-flex align-items-center justify-content-center no-data">
                        <h2 class="text-bold-700"> No data found </h2>
                    </div>
                    {{-- <h2 class="text-bold-700 mt-1 h-100 d-flex align-items-center justify-content-center no-data">
                        No
                        data found </h2> --}}
                    @endif
                </div>
            </div>
            <div class="card mt-6">
                <div class="card-header pb-0">
                    {{-- <div class="avatar bg-rgba-warning p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-users text-primary font-medium-5"></i>
                        </div>
                    </div> --}}
                    <h2 class="text-bold-700 ">Engagement By Age</h2>
                </div>
                <div class="card-body">
                    @if(array_sum($fbreachvalue) > 0)
                    <div class="mt-auto">
                        <canvas id="fbreachage" height="100"></canvas>
                    </div>
                    @else
                    <div class="d-flex align-items-center justify-content-center no-data">
                        <h2 class="text-bold-700"> No data found </h2>
                    </div>
                    {{-- <h2 class="text-bold-700 mt-1 h-100 d-flex align-items-center justify-content-center no-data">
                        No
                        data found </h2> --}}
                    @endif
                </div>
            </div>
            <div class="card mt-6">
                <div class="card-header pb-0">
                    {{-- <div class="avatar bg-rgba-warning p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-users text-primary font-medium-5"></i>
                        </div>
                    </div> --}}
                    <h2 class="text-bold-700 ">Top Cities</h2>
                </div>
                <div class="card-body">
                    <div style="{{ $citiesreacharr ? 'max-height: 400px;overflow-y: scroll;' : '' }}"
                        class="table-responsive">
                        <table class="table {{ $citiesreacharr ? 'table-bordered' : '' }}">
                            @forelse($citiesreacharr as $key => $value)
                            <tr style="color:{{ $loop->iteration > 3 ? '#3755A9 ': '#E77F01'}}">
                                <td>{{ $key }}</td>
                                <td>{{ $value }}</td>
                            </tr>
                            @empty
                            <tr>
                                <!-- <td colspan="2">No data found</td> -->
                                <td colspan="2" class="border-0 p-0">
                                    <div class="d-flex align-items-center justify-content-center no-data">
                                        <h2 class="text-bold-700"> No data found </h2>
                                    </div>
                                    {{-- <h2
                                        class="text-bold-700 mt-1 h-100 d-flex align-items-center justify-content-center no-data">
                                        No data found </h2> --}}
                                </td>
                            </tr>
                            @endforelse
                        </table>
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
    const videoview = @php echo json_encode($videoview) @endphp;

    var videoviewdonut =  () => {
		const apexChart = "#videoviewdonut";
		var options = {
			series:videoview ,
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

    var totalreachline = () => {
        
        var labels = @php echo json_encode($trkey); @endphp;
        var value =  @php  echo json_encode($trvalue); @endphp;
        var ctx = document.getElementById("totalreachline").getContext('2d');;
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
    /* Gender Bar */
    const fbreachbararr = @php echo json_encode($fbreachbararr) @endphp;

    var fbreachdonut =  () => {
		const apexChart = "#fbreachbar";
		var options = {
			series:fbreachbararr ,
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
    
    const organicvspaidReach = @php echo json_encode($organicVsReach) @endphp;
    var fbreachdonutReach =  () => {
		const apexChart = "#fbreachbarReach";
		var options = {
			series:organicvspaidReach ,
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

    var fbreachage = () => {

        var labels = @php echo json_encode($fbreachkey); @endphp;
        var value =  @php  echo json_encode($fbreachvalue); @endphp;
        var ctx = document.getElementById("fbreachage").getContext('2d');
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

    jQuery(document).ready(function() {
        videoviewdonut();

        @if($totlareachArr)
            totalreachline(); 
        @endif      
        fbreachdonut();       
        fbreachdonutReach();       
        fbreachage();       
    });
</script>
@endpush