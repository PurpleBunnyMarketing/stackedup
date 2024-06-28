@extends('frontend.layouts.app')

@section('content')
<style>
    .tooltip {
        font-size: 13px !important;
    }
</style>
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Heading-->
                <div class="d-flex  ">
                    <!--begin::Title-->
                    <h2 class="font-weight-bold my-2 mr-5"> {{$post->schedule_date ? 'Schedule Post Detail' : 'Post
                        Detail'}}</h2>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Heading-->
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Subheader-->
    <div class="container">
        <div class="row scheduled-post-detail-card">
            <div class="col-lg-12">
                <!--begin::Card-->
                <div class="card card-custom example example-compact post-detail-card">
                    <div class="row align-items-center">
                        <div class="col-md-4 ">

                            <div class="post-detail-image {{ $post->upload_file !== null ? 'singleImage' : '' }}">
                                @if ($post->upload_file == '')
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
                                @else
                                @if(Storage::exists($post->upload_file))
                                @if(\File::extension(generateURL($post->upload_file ?? "")) == 'mp4' )
                                <video width="100%" height="100%" controls>
                                    <source src="{{generateURL($post->upload_file ?? "") }}" type="video/mp4">
                                </video>
                                @else
                                <img src="{{ generateURL($post->upload_file ?? "") }}" alt="image" width="100%"
                                    height="100%">
                                @endif
                                @else
                                <img src="{{ asset('frontend/images/no_image_available.jpeg') }}" alt="post-image">
                                @endif
                                @endif
                                {{-- <img src="./assets/media/images/post-image-1.png" alt="post-image"> --}}
                            </div>
                        </div>
                        <div class="col-md-8">
                            <!--begin::Form-->
                            <form class="form">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Caption</label>
                                            <p class="font-size-h5 font-weight-bolder">{{ $post->caption ?? ""}}</p>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Hashtag</label>
                                            <p class="font-size-h5 font-weight-bolder">{{ $post->hashtag ?? "" }}</p>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            <label>Date &amp; Time</label>
                                            <p class="font-size-h5 font-weight-bolder">
                                                {{-- {{ \Carbon\Carbon::parse($post->created_at)->format('M d, Y').' at
                                                '.\Carbon\Carbon::parse($post->created_at)->format('h:iA') }} --}}
                                                @if(!empty($post->schedule_date))
                                                {{-- @if(\Carbon\Carbon::parse($post->schedule_date)->format('Y-m-d
                                                H:i:s')
                                                >= \Carbon\Carbon::now())
                                                {{ \Carbon\Carbon::parse($post->schedule_date_time)->format('M d, Y').'
                                                at '.\Carbon\Carbon::parse($post->schedule_date_time)->format('h:iA') }}
                                                @else --}}
                                                {{ \Carbon\Carbon::parse($post->schedule_date_time)->format('M d, Y').'
                                                at
                                                '.\Carbon\Carbon::parse($post->schedule_date_time)->format('h:iA') }}
                                                {{-- @endif --}}
                                                @else
                                                {{-- {{ \Carbon\Carbon::parse($post->created_at)->format('M d, Y').' at
                                                '.\Carbon\Carbon::parse($post->created_at)->format('h:iA') }} --}}
                                                {{ convertUTCtoLocalDiffrentReturn($post->created_at,
                                                isset($_COOKIE['timeZone']) ? $_COOKIE['timeZone'] :
                                                "Australia/Brisbane")}}
                                                @endif

                                            </p>
                                        </div>
                                        <div class="col-sm-6">
                                            <label for="exampleInputPassword1">Added By</label>
                                            <p class="font-size-h5 font-weight-bolder">{{ $post->user->full_name ?? ""
                                                }}</p>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-lg-4">
                                            <label class="d-block">Social Media</label>
                                            @foreach($post->postMedia as $postMedia)
                                            <div
                                                class="symbol symbol-50 d-flex symbol-light mr-3 flex-shrink-0 add-post-symbol">
                                                <div class="symbol-content-wrap">
                                                    <a href="javascript::void(0)" class="symbol-label">
                                                        <img src="{{ asset($postMedia->media->website_image_url)}}"
                                                            alt="" class="h-50" />
                                                    </a>
                                                    <div class="d-flex">
                                                        <label for="">{{$postMedia->mediaPage->page_name}}</label>
                                                        @if ($postMedia->is_error == 'y')
                                                        <span class="ml-3" data-toggle="tooltip" data-placement="top"
                                                            title="{{$postMedia->error_message}}"
                                                            style="cursor: pointer;">
                                                            <i class="text-danger fas fa-exclamation-circle"
                                                                style="font-size: 20px;"></i>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach

                                        </div>
                                    </div>
                                    <a href="{{ route('posts.index') }}"
                                        class="btn common-btn font-weight-bold py-3 px-6 mr-2 ">Back</a>
                                </div>
                            </form>
                            <!--end::Form-->
                        </div>
                    </div>
                </div>
                <!--end::Card-->
            </div>
        </div>
    </div>
</div>
@endsection
@push('extra-js-scripts')
<script>
    $(document).ready(function(){
        $('.sliderImage').slick({
            infinite: false,
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: false,
            autoplaySpeed: 5000,
            arrows: true,
            dots: true,
        });
    })
</script>
@endpush