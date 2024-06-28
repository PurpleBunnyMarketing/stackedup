@forelse($posts as $post)

<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6" id="card-{{$post->id}}">
    <!--begin::Stats Widget 4-->
    @php
    $is_error = $post->postMedia->pluck('is_error')->toArray();
    @endphp
    <div class="card card-custom card-stretch gutter-b">
        <!--begin::Body-->
        <div class="card-body d-flex align-items-center py-0 p-8">
            <div class="d-flex flex-column flex-grow-1">
                <p class="card-title font-size-h6 mb-10">
                    {{-- {{ $post->caption ?? "" }}</p> --}}
                {!! nl2br(\Str::limit($post->caption,200)) ?? "" !!}</p>
                <p class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-10 ">Posted on:
                    @if(!empty($post->schedule_date))
                    {{-- @if(\Carbon\Carbon::parse($post->schedule_date)->format('Y-m-d H:i:s') >=
                    \Carbon\Carbon::now()) --}}
                    {{ \Carbon\Carbon::parse($post->schedule_date_time)->format('M d, Y').' at
                    '.\Carbon\Carbon::parse($post->schedule_date_time)->format('h:iA') }}
                    {{-- @else
                    {{ \Carbon\Carbon::parse($post->created_at)->format('M d, Y').' at
                    '.\Carbon\Carbon::parse($post->created_at)->format('h:iA') }} --}}
                    {{-- @endif --}}
                    @else
                    {{ convertUTCtoLocalDiffrentReturn($post->created_at, isset($_COOKIE['timeZone']) ?
                    $_COOKIE['timeZone'] : "Australia/Brisbane")}}
                    {{-- {{ \Carbon\Carbon::parse($post->created_at)->format('M d, Y').' at
                    '.\Carbon\Carbon::parse($post->created_at)->format('h:iA') }} --}}
                    {{-- {{$dt->format('M d, Y'). " at ".$dt->format('h:iA') }} --}}
                    @endif
                </p>
                <span class="text-dark-75 font-size-h3">
                    Posted By:
                    <span class="font-italic">
                        {{ $post->user->full_name ?? "" }}</span>
                </span>

            </div>
            @php
                $post_image_count = count($post->images);
            @endphp
            <div class="{{ ($post->upload_file == '' && $post_image_count > 1) ? 'image-sliderbox' : 'post-img' }}  ">
                @if ($post->upload_file == '')
                    @if ($post_image_count > 1)
                        <div class="sliderImage">
                            @foreach ($post->images->pluck('upload_image_file')->toArray() as $key
                            => $image)
                                <div>
                                    <div class="slider-image">
                                        <img class="img-fluid" src="{{\Storage::url($image)}}" alt="{{$key}}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif($post_image_count == 1)
                        <img src="{{ !empty($post->images[0]->upload_image_file) ? Storage::url($post->images[0]->upload_image_file) : ""}}"
                            class="align-self-center" alt="post-image" style="height: 165px; width: 165px;">
                    @else
                        <img src="{{ asset('frontend/images/no_image_available.jpeg') }}" alt="post-image">
                    @endif
                @else
                    @if(Storage::exists($post->upload_file))
                        @if(\File::extension(generateURL($post->upload_file ?? "")) == 'mp4' )
                            <img src="{{ !empty($post->thumbnail) ? Storage::url($post->thumbnail) : ""}}" class="align-self-center"
                                alt="post-image">
                        @else
                            <img src="{{ !empty($post->upload_file) ? Storage::url($post->upload_file) : ""}}"
                                class="align-self-center" alt="post-image">
                        @endif
                    @else
                        <img src="{{ asset('frontend/images/no_image_available.jpeg') }}" alt="post-image">
                    @endif
                @endif

            </div>
        </div>
        <div class="post-card-footer">
            <div class="button-group px-10 my-6 my-lg-6">
                <a href="{{ route('posts.show',$post->id)}}" type="button" class="btn btn-dark">View</a>
                @if($type == 'scheduled_posts')
                <a href="{{ route('posts.edit',$post->id)}}" type="button" class="btn btn-primary">Edit</a>
                <button data-id="{{$post->id}}" type="button" class="btn btn-danger btn-delete">Delete</button>
                {{-- @endif
                @if($type == 'scheduled_posts') --}}
                <button type="button" class="btn btn-secondary post-now" data-id="{{ $post->id }}">Post Now</button>
                @endif
            </div>
            <div class="social-icon-group d-flex align-items-center pr-8">
                @foreach ($post->postMedia->unique('media_id'); as $postMedia)
                <img src="{{ $postMedia->media->image_url ?? "" }}" alt="{{$postMedia->media->name}}"
                    style="max-width: 100%;width:28px;height:28px;" class="mr-2">
                @endforeach
            </div>
        </div>
    </div>
    <!--end::Stats Widget 4-->
</div>

@empty
<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
    <div class="not-found">
        {{-- <img src="{{ asset('frontend/images/not-found.png') }} " alt="not-found"> --}}
        <h3>No Posts Found</h3>
    </div>
</div>
@endforelse
