@php
    $twitter_tweet_analytic = $analytic->twitter_tweet_analytic ?? '{}';
    $twitter_tweet_analytic = json_decode($twitter_tweet_analytic,true);
    $paginattweet = [];
    $limit = 10;
@endphp


<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex  post-type">
                    <a href="javascript:;" class="post-link btn font-size-h3
                        font-weight-bold my-2 mr-5" data-type="posts">Twitter - Feed</a>
                </div>
            </div>
        </div>
    </div>

<div class="container">
    <div class="row post-cards">

        @forelse($twitter_tweet_analytic as $key => $feed)
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 twt-feed page_{{ $key }}" id="card-68" style="display: {{ $key >= $limit ? 'none':'block' }}">
                <div class="card card-custom card-stretch gutter-b">
                    <div class="card-body d-flex align-items-center py-0 p-8">
                        <div class="d-flex flex-column flex-grow-1">
                            <p class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-10"> {{ $feed['data']['text'] ?? ''}}</p>
                                <p class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-10 ">{{ \Carbon\Carbon::parse($feed['data']['created_at'])->format('Y-m-d') ?? ''}}</p>
                            <p class=" font-italic text-dark-75 font-size-h3 ">{{ auth()->user()->full_name }}</p>
                        </div>

                        @if(isset($feed['includes']) && isset($feed['includes']['media']) && isset($feed['includes']['media'][0]['url']))
                            
                            <div class="post-img d-flex align-self-center">
                                <img src="{{ $feed['includes']['media'][0]['url'] }}" class="align-self-center" alt="post-image">                                            
                            </div>
                        @endif
                    </div>
                    <div class="button-group px-10 my-6 my-lg-6 ">
                        <a href="javascript:" type="button" class="btn btn-dark">{{ $feed['data']['public_metrics']['retweet_count'] ?? 0 }} Retweets</a>
                        <a href="javascript:" type="button" class="btn btn-primary">{{ $feed['data']['public_metrics']['like_count'] ?? 0 }} Like</a>
                        <a href="javascript:" type="button" class="btn btn-secondary post-now">{{ $feed['data']['public_metrics']['reply_count'] ?? 0 }} Reply</a>
                    
                    </div>

                </div>
            </div>
        @empty
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6" id="card-68"><h3 style="color:rgb(231,127,1)">No Feed found</h3></div>
        @endforelse
    </div>
    
    @if(count($twitter_tweet_analytic) > $limit)
    <div class="custom-pagination d-flex justify-content-center flex-wrap py-2 mr-3 pagination slider-pagination">
        <a class="btn btn-primary disabled prev-feed">Prev</a>&nbsp;&nbsp;&nbsp;
        <a class="btn btn-primary next-feed">Next</a>
    </div>
    @endif
</div>

@push('extra-js-scripts')
<script>

    var offset = 0;
    var limit = {{ $limit }};

    $(function(){
        
        $('.next-feed').click(function(){

            offset += {{ $limit }}
            $(".twt-feed").hide()
            $(".prev-feed").removeClass('disabled')
            
            for( var i=offset; i < limit+offset; i++ ){
                $('.page_'+i).show();                
            }
            
            if( limit+offset >= {{ count($twitter_tweet_analytic) }} ){
                $(".next-feed").addClass('disabled')
            }
            
            
        })
        $('.prev-feed').click(function(){
            $(".next-feed").removeClass('disabled')
            offset -= {{ $limit }}
            $(".twt-feed").hide()
            for( var i=offset; i < limit+offset; i++ ){
                $('.page_'+i).show();                
            }
            if(offset <=0 ){
                $(".prev-feed").addClass('disabled')
            }
        })
        
    })

  
</script>
@endpush
   