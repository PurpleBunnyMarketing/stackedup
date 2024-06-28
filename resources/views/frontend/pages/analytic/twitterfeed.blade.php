@php
    $twitter_tweet_analytic = $analytic->twitter_tweet_analytic ?? '{}';
    $twitter_tweet_analytic = json_decode($twitter_tweet_analytic,true);
    $paginattweet = [];
    $limit = 10;

    $twt = [];
    $includeArr = [];
    if(is_array($twitter_tweet_analytic)){
        foreach($twitter_tweet_analytic as $tt){
            $twt = array_merge($twt,($tt['data'] ?? []));
            $includeArr = array_merge($includeArr,(isset($tt['includes']['media']) ? $tt['includes']['media'] : []));
        }
    }
@endphp


<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex  post-type">
                    <a href="javascript:;" class="post-link btn font-size-h3
                        font-weight-bold my-2 mr-5" data-type="posts">X(Twitter) - Feed</a>
                </div>
            </div>
        </div>
    </div>

<div class="container">
    <div class="row post-cards">
        @if(is_array($twt) && !empty($twt))
            @forelse($twt as $key => $feed)
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 twt-feed page_{{ $key }}" id="card-68" style="display: {{ $key >= $limit ? 'none':'block' }}">
                <div class="card card-custom card-stretch gutter-b">
                    <div class="card-body d-flex align-items-center py-0 p-8">
                        <div class="d-flex flex-column flex-grow-1">
                            <p class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-10"> {{ $feed['text'] ?? ''}}</p>
                                <p class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-10 ">{{ \Carbon\Carbon::parse($feed['created_at'])->format('Y-m-d') ?? ''}}</p>
                            <p class=" font-italic text-dark-75 font-size-h3 ">{{ auth()->user()->full_name }}</p>
                        </div>

                        @if(isset($feed['attachments']['media_keys'][0]) && getMediaUrl($feed['attachments']['media_keys'][0],$includeArr) )
                            <div class="post-img d-flex align-self-center">
                                <img src="{{ getMediaUrl($feed['attachments']['media_keys'][0],$includeArr) }}" class="align-self-center" alt="post-image">
                            </div>
                        @endif
                    </div>
                    <div class="button-group px-10 my-6 my-lg-6 ">
                        <a href="javascript:" type="button" class="btn btn-dark">{{ $feed['public_metrics']['retweet_count'] ?? 0 }} Retweets</a>
                        <a href="javascript:" type="button" class="btn btn-primary">{{ $feed['public_metrics']['like_count'] ?? 0 }} Like</a>
                        <a href="javascript:" type="button" class="btn btn-secondary post-now">{{ $feed['public_metrics']['reply_count'] ?? 0 }} Reply</a>

                    </div>

                </div>
            </div>
            @empty
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6" id="card-68"><h3 style="color:rgb(231,127,1)">No Feed found</h3></div>
            @endforelse
        @endif
    </div>


    @if(isset($twt) && count($twt) > $limit)
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

            if( limit+offset >= {{ count($twt) }} ){
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
