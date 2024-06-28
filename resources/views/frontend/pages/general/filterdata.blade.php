@forelse($posts as $post)
<div class="col-xl-12 col-lg-12 col-md-6 col-sm-6" id="card-{{$post->id}}">
    <!--begin::Stats Widget 4-->
    <div class="card card-custom card-stretch gutter-b" style="height: 225px">
        <!--begin::Body-->
        <div class="card-body d-flex align-items-center py-0 p-8">
            <div class="d-flex flex-column flex-grow-1">
                <p class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-6">
                    {{$post->caption ?? "" }}</p>
                <p class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-6 ">

                    @if(!empty($post->schedule_date) )
                    @if(\Carbon\Carbon::parse($post->schedule_date)->format('Y-m-d H:i:s') >= \Carbon\Carbon::now())
                    {{ \Carbon\Carbon::parse($post->schedule_date_time)->format('M d, Y').' at
                    '.\Carbon\Carbon::parse($post->schedule_date_time)->format('h:iA') }}
                    @else
                    {{ \Carbon\Carbon::parse($post->created_at)->format('M d, Y').' at
                    '.\Carbon\Carbon::parse($post->created_at)->format('h:iA') }}
                    @endif
                    @else
                    {{ \Carbon\Carbon::parse($post->created_at)->format('M d, Y').' at
                    '.\Carbon\Carbon::parse($post->created_at)->format('h:iA') }}
                    @endif
                </p>
                <p class=" font-italic text-dark-75 font-size-h3 ">
                    {{ $post->user->full_name ?? "" }}</p>
            </div>
            <div class="post-img d-flex align-self-center">
                @if(Storage::exists($post->upload_file))
                @if(\File::extension(generateURL($post->upload_file ?? "")) == 'mp4' )
                <img src="{{ !empty($post->thumbnail) ? storage::url($post->thumbnail) : ""}}" class="align-self-center"
                    alt="post-image">
                @else
                <img src="{{ !empty($post->upload_file) ? storage::url($post->upload_file) : ""}}"
                    class="align-self-center" alt="post-image">
                @endif
                @else
                <img src="{{ asset('frontend/images/no_image_available.jpeg') }}" alt="post-image">
                @endif
            </div>
        </div>
    </div>
    <!--end::Stats Widget 4-->
</div>
@empty
<div class="col-xl-12 col-lg-12 col-md-6 col-sm-6">
    <div class="text-center not-found py-5">
        {{-- <img src="{{ asset('frontend/images/not-found.png') }} " alt="not-found"> --}}
        <h3>No {{$type}} Found</h3>
    </div>
</div>
@endforelse




{{-- @if($schedulePosts)
@dump('schedule')
@forelse($schedulePosts as $schedulePost)
<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6" id="card-{{$schedulePost->id}}">
    <!--begin::Stats Widget 4-->
    <div class="card card-custom card-stretch gutter-b">
        <!--begin::Body-->
        <div class="card-body d-flex align-items-center py-0 p-8">
            <div class="d-flex flex-column flex-grow-1">
                <p class="card-title font-weight-bolder text-dark-75 font-size-h2 mb-6">
                    {{$schedulePost->caption ?? "" }}</p>
                <p class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-6 ">

                    @if(!empty($schedulePost->schedule_date) )
                    @if(\Carbon\Carbon::parse($schedulePost->schedule_date)->format('Y-m-d H:i:s') >=
                    \Carbon\Carbon::now())
                    {{ \Carbon\Carbon::parse($schedulePost->schedule_date_time)->format('M d, Y').' at
                    '.\Carbon\Carbon::parse($schedulePost->schedule_date_time)->format('h:iA') }}
                    @else
                    {{ \Carbon\Carbon::parse($schedulePost->created_at)->format('M d, Y').' at
                    '.\Carbon\Carbon::parse($schedulePost->created_at)->format('h:iA') }}
                    @endif
                    @else
                    {{ \Carbon\Carbon::parse($schedulePost->created_at)->format('M d, Y').' at
                    '.\Carbon\Carbon::parse($schedulePost->created_at)->format('h:iA') }}
                    @endif
                </p>
                <p class=" font-italic text-dark-75 font-size-h3 ">
                    {{ $schedulePost->user->full_name ?? "" }}</p>
            </div>
            <div class="post-img d-flex align-self-center">
                @if(Storage::exists($schedulePost->upload_file))
                @if(\File::extension(generateURL($schedulePost->upload_file ?? "")) == 'mp4' )
                <img src="{{ !empty($schedulePost->thumbnail) ? storage::url($schedulePost->thumbnail) : ""}}"
                    class="align-self-center" alt="post-image">
                @else
                <img src="{{ !empty($schedulePost->upload_file) ? storage::url($schedulePost->upload_file) : ""}}"
                    class="align-self-center" alt="post-image">
                @endif
                @else
                <img src="{{ asset('frontend/images/no_image_available.jpeg') }}" alt="post-image">
                @endif

            </div>
        </div>
    </div>
    <!--end::Stats Widget 4-->
</div>
@else
<div class="text-center not-found">

    <h3>No Posts Found</h3>
</div>
@endforelse
@endif --}}