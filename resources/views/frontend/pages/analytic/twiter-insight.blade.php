@php
    $twitter_analytic = $analytic->twitter_analytic ?? '{}';
    $twitter_analytic = json_decode($twitter_analytic,true);

    $twitter_follower_analytic = $analytic->twitter_follower_analytic ?? '{}';
    $twitter_follower_analytic = json_decode($twitter_follower_analytic,true);

    $tuser = [];
    if(isset($twitter_follower_analytic)){
        foreach($twitter_follower_analytic as $val){
            if(isset($val['users'])){
                foreach($val['users'] as $val1){
                    $date = \Carbon\Carbon::parse( $val1['created_at'])->format('M d');
                    $tuser[$date] = ($tuser[$date] ?? 0) + 1;
                }
            }

        }
    }
    $tuserkey = array_keys($tuser);
    $tuservelue = array_values($tuser);

    $isTwitterinConnected = $connectedSocialMedia->where('media_id',3)->first();
@endphp
<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex  post-type">
                    <a href="javascript:;" class="post-link btn font-size-h3
                        font-weight-bold my-2 mr-5" data-type="posts">X(Twitter) - Insights</a>
                </div>
            </div>
            {{-- <div class="d-flex align-items-center">
                <a href="{{ route('analytics.refresh-twitter') }}" class="btn font-weight-bold py-3 px-6 mr-2 common-btn"> Refresh</a>

            </div> --}}
        </div>
    </div>
    <div class="container">
        @if(isset($analytic->id) && $isTwitterinConnected)
            <div class="row post-cards">
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="card h-100">
                            <div class="card-header h-100 d-flex flex-column">
                                <h2 class="text-bold-700 mt-1">Followers</h2>
                                <h2 class="text-bold-700 mt-1 h-100 d-flex align-items-center justify-content-center" style="font-size:3.5rem;color:#E77F01">
                                    {{$twitter_analytic['followers_count'] ?? 0}}
                                </h2>
                            </div>
                        </div>
                    </div>

                    {{-- <div class="col-lg-8 col-md-6 col-12  mt-lg-0 mt-4">
                        <div class="card h-100">
                            <div class="card-header h-100 d-flex flex-column pb-0">
                                <h2 class="text-bold-700 mt-1 mb-12">Followers</h2>

                                @if($tuser)
                                    <div class="mt-auto">
                                        <canvas id="twitterFollower" height="100"></canvas>
                                    </div>
                                @else
                                    <h2 class="text-bold-700 mt-1 py-12 h-100 d-flex align-items-center justify-content-center"> No data found </h2>
                                @endif

                            </div>
                        </div>
                    </div> --}}
                    <div class="col-lg-4 col-md-6 col-12 mt-4 mt-lg-0">
                        <div class="card h-100">
                            <div class="card-header h-100 d-flex flex-column">
                                <h2 class="text-bold-700 mt-1">Likes</h2>
                                <h2 class="text-bold-700 mt-1 pb-10 text-center" style="font-size:3.5rem;color:#E77F01">{{$twitter_analytic['like_count'] ?? 0}}</h2>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-lg-4 col-md-6 col-12 mt-4">
                        <div class="card h-100">
                            <div class="card-header h-100 d-flex flex-column">
                                <h2 class="text-bold-700 mt-1">Retweets</h2>
                                <h2 class="text-bold-700 mt-1 pb-10 text-center" style="font-size:3.5rem;color:#E77F01">{{$twitter_analytic['retweet_count'] ?? 0}}</h2>
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-lg-4 col-md-6 col-12 mt-4 mt-lg-0">
                        <div class="card h-100">
                            <div class="card-header h-100 d-flex flex-column">
                                <h2 class="text-bold-700 mt-1">Tweets</h2>
                                <h2 class="text-bold-700 mt-1 pb-10 text-center" style="font-size:3.5rem;color:#E77F01">{{$twitter_analytic['tweet_count'] ?? 0}}</h2>
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
@if($tuser)
jQuery(document).ready(function() {
    var labels = @php echo json_encode($tuserkey); @endphp;
    var value =  @php  echo json_encode($tuservelue); @endphp;
    var ctx = document.getElementById("twitterFollower").getContext('2d');;
    var twitterFollower = new Chart(ctx, {
    type: 'bar',
    data: {
            labels: labels,
            datasets: [{
                label: 'Follower',
                data: value,
                backgroundColor: '#E47D03'
            }]
        },
        options: {
            scales: {
            yAxes: [{
                ticks: {
                beginAtZero: true,
                callback: function(value) {if (value % 1 === 0) {return value;}}
                }
            }]
            }
        }
    });
});
@endif
</script>
@endpush
