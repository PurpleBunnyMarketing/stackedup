@php
$facebook_post = $analytic->facebook_post ?? '{}';
$facebook_post = json_decode($facebook_post,true);
$facebook_post = call_user_func_array('array_merge', $facebook_post);
$total_reach_data = array_map(function($post){
return isset($post['postreach']) ? $post['postreach'][0]['values'][0]['value'] : '';
},$facebook_post);
array_multisort($total_reach_data, SORT_DESC, $facebook_post);

$paginattweet = [];
$limit = 10;


// $facebook_review = $analytic->facebook_review ?? '{}';
// $facebook_review = json_decode($facebook_review,true);
// $facebook_review = call_user_func_array('array_merge', $facebook_review);

@endphp

<div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader">
    <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <div class="d-flex align-items-center flex-wrap mr-1">
            <div class="d-flex  post-type">
                <a href="javascript:;" class="post-link btn font-size-h3
                        font-weight-bold my-2 mr-5" data-type="posts">Facebook - Posts</a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="card">
        <div class="card-body">
            <table class="table table-hover table-bordered" id="facebook_posts_table">
                <thead>
                    <tr>
                        <th>DATE</th>
                        <th>POST</th>
                        <th>REACH</th>
                        <th>LIKES</th>
                        <th>SHARES</th>
                        <th>CLICKS</th>
                        <th>POSTS ACTIVITY UNIQUE</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($facebook_post as $feed)
                    <tr>
                        <td style="width: fit-content">
                            {{
                            \Carbon\Carbon::parse($feed['created_time']['date'])->format('j M, Y') ?? ''}}
                        </td>
                        <td>
                            <div class="d-flex justify-content-between">
                                @if(isset($feed['attachments']['image']['src']))
                                <div class="d-flex align-self-center">
                                    <img src="{{ $feed['attachments']['image']['src'] }}" class="align-self-center"
                                        style="object-fit: cover; width: 60px; aspect-ratio: 1; object-position: center; "
                                        alt="post-image">
                                </div>
                                @endif
                                <div class="ml-3 flex-grow-1">
                                    {{ isset($feed['message']) ? \Illuminate\Support\Str::limit($feed['message'], 60,
                                    $end='...') :
                                    ''}}
                                </div>
                            </div>
                        </td>
                        <td>
                            {{$feed['postreach'][0]['values'][0]['value'] ?? 0}}
                        </td>
                        <td>
                            {{$feed['reactions'] ?? 0}}
                        </td>
                        <td>
                            {{$feed['post_share_count'] ?? 0}}
                        </td>
                        <td>
                            {{$feed['insights'][0]['values'][0]['value'] ?? 0}}
                        </td>
                        <td>
                            {{$feed['post_activity_unique'][0]['values'][0]['value'] ?? 0 }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <h3 style="color:rgb(231,127,1)">No Posts found</h3>
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>
    {{-- <div class="row post-cards">

        @forelse($linkedin_posts as $key => $feed)
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 twt-feed page_{{ $key }}" id="card-68">
            <div class=" card card-custom card-stretch gutter-b">
                <div class="card-body d-flex align-items-center py-0 p-8">
                    <div class="d-flex flex-column flex-grow-1">
                        <a href="{{$feed['url']}}" class="text-muted">
                            <p class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-10"> {{
                                isset($feed['content']) ? \Illuminate\Support\Str::limit($feed['content'],
                                60, $end='...') :
                                ''}}</p>
                        </a>
                        <p class="card-title font-weight-bolder text-dark-75 font-size-h6 mb-10"> {{
                            $feed['hash_tags'] ?? ''}}</p>
                        <p class="card-title font-weight-bolder  text-dark-75 font-size-h5 mb-10 ">{{
                            $feed['created_date']}}</p>
                    </div>

                    @if(isset($feed['image_url']))

                    <div class="post-img d-flex align-self-center">
                        <img src="{{ $feed['image_url'] }}" class="align-self-center" alt="post-image">
                    </div>
                    @endif
                </div>
                <div class="button-group px-10 my-6 my-lg-6 ">
                    <a href="javascript:" type="button" class="btn btn-dark">{{$feed['comment_count']}} Comments</a>
                    <a href="javascript:" type="button" class="btn btn-primary">{{$feed['likes_count']}} Like</a>
                </div>

            </div>
        </div>
        @empty
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6" id="card-68">
            <h3 style="color:rgb(231,127,1)">No Feed found</h3>
        </div>
        @endforelse
    </div> --}}
</div>

{{-- <div class="container">
    <div class="row post-cards">

        @forelse($facebook_post as $key => $feed)
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 twt-feed page_{{ $key }}" id="card-68"
            style="display: {{ $key >= $limit ? 'none':'block' }}">
            <div class="card card-custom card-stretch gutter-b">
                <div class="card-body d-flex align-items-center py-0 p-8">
                    <div class="d-flex flex-column flex-grow-1">
                        <p class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-10"> {{
                            isset($feed['message']) ? \Illuminate\Support\Str::limit($feed['message'], 60, $end='...') :
                            ''}}</p>
                        <p class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-10 ">{{
                            \Carbon\Carbon::parse($feed['created_time']['date'])->format('j M, Y') ?? ''}}</p>
                    </div>

                    @if(isset($feed['attachments']['image']['src']))

                    <div class="post-img d-flex align-self-center">
                        <img src="{{ $feed['attachments']['image']['src'] }}" class="align-self-center"
                            alt="post-image">
                    </div>
                    @endif
                </div>
                <div class="button-group px-10 my-6 my-lg-6 ">
                    <a href="javascript:" type="button"
                        class="btn btn-dark">{{$feed['postreach'][0]['values'][0]['value']}} Reach</a>
                    <a href="javascript:" type="button" class="btn btn-primary">{{$feed['reactions']}} Like</a>
                    <a href="javascript:" type="button"
                        class="btn btn-secondary post-now">{{$feed['insights'][0]['values'][0]['value']}} Clicks</a>
                </div>

            </div>
        </div>
        @empty
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6" id="card-68">
            <h3 style="color:rgb(231,127,1)">No Feed found</h3>
        </div>
        @endforelse
    </div>

    @if(count($facebook_post) > $limit)
    <div class="custom-pagination d-flex justify-content-center flex-wrap py-2 mr-3 pagination slider-pagination">
        <a class="btn btn-primary disabled prev-feed">Prev</a>&nbsp;&nbsp;&nbsp;
        <a class="btn btn-primary next-feed">Next</a>
    </div>
    @endif
</div> --}}

@push('extra-js-scripts')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
    $("#facebook_posts_table").dataTable({
        searching: false,
        paging: false,
        info: false,
        ordering: false
    });
    // var offset = 0;
    // var limit = {{ $limit }};

    // $(function(){
        
    //     $('.next-feed').click(function(){

    //         offset += {{ $limit }}
    //         $(".twt-feed").hide()
    //         $(".prev-feed").removeClass('disabled')
            
    //         for( var i=offset; i < limit+offset; i++ ){
    //             $('.page_'+i).show();                
    //         }
            
    //         if( limit+offset >= {{ count($facebook_post) }} ){
    //             $(".next-feed").addClass('disabled')
    //         }
            
            
    //     })
    //     $('.prev-feed').click(function(){
    //         $(".next-feed").removeClass('disabled')
    //         offset -= {{ $limit }}
    //         $(".twt-feed").hide()
    //         for( var i=offset; i < limit+offset; i++ ){
    //             $('.page_'+i).show();                
    //         }
    //         if(offset <=0 ){
    //             $(".prev-feed").addClass('disabled')
    //         }
    //     })
        
    // })

  
</script>
@endpush