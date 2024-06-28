@extends('frontend.layouts.app')

@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Heading-->
                <div class="d-flex  ">

                </div>
                <!--end::Heading-->
            </div>
            <div class="container">

                <div class="row m-0 mb-2">
                    <div class="col bg-light-warning px-6 py-8 rounded-xl mr-7 mb-7">
                        <h2 class="text-warning d-block my-2">
                            {{ $postsCount ?? 0 }}
                        </h2>
                        <a href="#" class="text-warning font-weight-bold font-size-h6">Number of Post in Last 30
                            Days</a>
                    </div>
                    <div class="col bg-light-primary px-6 py-8 rounded-xl mb-7">
                        <h2 class="text-primary d-block my-2">
                            {{ $scheduleCount ?? 0 }}
                        </h2>
                        <a href="#" class="text-primary font-weight-bold font-size-h6 mt-2">Scheduled Posts</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row filter-post-cards">

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row filter-schedule-post-cards">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h2 class="mb-0">Posts</h2>
                            <a href="{{ url('posts') }}" class="btn font-weight-bold py-3 px-6 mr-2 common-btn">View
                                All</a>
                        </div>
                        <div class="row post-cards">
                            @if ($posts->count() > 0)
                            @foreach ($posts as $post)
                            <div class="col-xl-12 col-lg-12 col-md-6 col-sm-6" id="card-{{ $post->id }}">
                                <!--begin::Stats Widget 4-->
                                <div class="card card-custom card-stretch gutter-b">
                                    <!--begin::Body-->
                                    <div class="card-body d-flex align-items-center py-0 p-8">
                                        <div class="d-flex flex-column flex-grow-1">
                                            <p class="card-title font-size-h6 mb-6">
                                                {!! nl2br(\Str::limit($post->caption,200)) ?? "" !!}</p>
                                            </p>
                                            <p class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-6">

                                                @if (!empty($post->start_date))
                                                @if (\Carbon\Carbon::parse($post->start_date)->format('Y-m-d H:i:s') >=
                                                \Carbon\Carbon::now())
                                                {{-- {{ \Carbon\Carbon::parse($post->start_date_time)->format('M d, Y')
                                                .
                                                ' at
                                                ' .
                                                \Carbon\Carbon::parse($post->start_date_time)->format('h:iA') }} --}}

                                                {{ convertUTCtoLocalDiffrentReturn($post->start_date_time,
                                                isset($_COOKIE['timeZone']) ? $_COOKIE['timeZone'] :
                                                "Australia/Brisbane")}}
                                                @else
                                                {{-- {{ \Carbon\Carbon::parse($post->created_at)->format('M d, Y') .
                                                ' at
                                                ' .
                                                \Carbon\Carbon::parse($post->created_at)->format('h:iA') }} --}}
                                                {{ convertUTCtoLocalDiffrentReturn($post->created_at,
                                                isset($_COOKIE['timeZone']) ? $_COOKIE['timeZone'] :
                                                "Australia/Brisbane")}}

                                                @endif
                                                @else
                                                {{ convertUTCtoLocalDiffrentReturn($post->created_at,
                                                isset($_COOKIE['timeZone']) ? $_COOKIE['timeZone'] :
                                                "Australia/Brisbane")}}

                                                @endif
                                            </p>
                                            <p class=" font-italic text-dark-75 font-size-h3 ">
                                                {{ $post->user->full_name ?? '' }}</p>
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
                                    <div class="post-card-footer justify-content-end">
                                        <div class="button-group px-5 my-6 my-lg-6">
                                            <div class="social-icon-group d-flex align-items-center">
                                                @foreach ($post->postMedia->unique('media_id'); as $postMedia)
                                                <img src="{{ $postMedia->media->image_url ?? "" }}"
                                                    alt="{{$postMedia->media->name}}"
                                                    style="max-width: 100%;width:28px;height:28px;" class="mr-2">
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <!--end::Stats Widget 4-->
                            </div>
                            @endforeach
                            @else
                            <div class="col-xl-12 col-lg-12 col-md-6 col-sm-6">
                                <div class="text-center not-found py-6 w-100">
                                    {{-- <img src="{{ asset('frontend/images/not-found.png') }} " alt="not-found"> --}}
                                    <h3 class="text-center">No Posts Found</h3>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h2 class="mb-0">Scheduled Posts</h2>
                            <a href="{{ url('posts') }}" class="btn font-weight-bold py-3 px-6 mr-2 common-btn">View
                                All</a>
                        </div>
                        <div class="row post-cards">
                            @forelse ($schedulePosts as $schedulepost)
                            <div class="col-xl-12 col-lg-12 col-md-6 col-sm-6" id="card-{{ $schedulepost->id }}">
                                <!--begin::Stats Widget 4-->
                                <div class="card card-custom card-stretch gutter-b">
                                    <!--begin::Body-->
                                    <div class="card-body d-flex align-items-center py-0 p-8">
                                        <div class="d-flex flex-column flex-grow-1">
                                            <p class="card-title font-size-h6 mb-6">
                                                {!! nl2br(\Str::limit($schedulepost->caption,200)) ?? "" !!}</p>
                                            </p>
                                            <p class="card-title font-weight-bolder text-dark-75 font-size-h5 mb-6">
                                                @if ($schedulepost->schedule_date)
                                                @if (\Carbon\Carbon::parse($schedulepost->schedule_date)->format('Y-m-d
                                                H:i:s') >= \Carbon\Carbon::now())
                                                {{ \Carbon\Carbon::parse($schedulepost->schedule_date_time)->format('M
                                                d, Y') . ' at ' .
                                                \Carbon\Carbon::parse($schedulepost->schedule_date_time)->format('h:iA')
                                                }}
                                                @else
                                                {{ convertUTCtoLocalDiffrentReturn($schedulepost->created_at,
                                                isset($_COOKIE['timeZone']) ? $_COOKIE['timeZone'] :
                                                "Australia/Brisbane")}}
                                                @endif
                                                @else
                                                {{ convertUTCtoLocalDiffrentReturn($schedulepost->created_at,
                                                isset($_COOKIE['timeZone']) ? $_COOKIE['timeZone'] :
                                                "Australia/Brisbane")}}
                                                @endif
                                            </p>
                                            <p class=" font-italic text-dark-75 font-size-h3 ">{{
                                                $schedulepost->user->full_name ?? '' }}</p>
                                        </div>
                                        @php
                                            $schedule_post_image_count = count($schedulepost->images);
                                        @endphp
                                        <div class="{{ $schedulepost->upload_file == '' && $schedule_post_image_count > 1 ? 'image-sliderbox' : 'post-img' }}  ">
                                            @if ($schedulepost->upload_file == '')
                                                @if ($schedule_post_image_count > 1)
                                                    <div class="sliderImage">
                                                        @foreach ($schedulepost->images->pluck('upload_image_file')->toArray() as $key => $image)
                                                            <div>
                                                                <div class="slider-image">
                                                                    <img class="img-fluid" src="{{ \Storage::url($image) }}" alt="{{ $key }}">
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @elseif($schedule_post_image_count == 1)
                                                    <img src="{{ !empty($schedulepost->images[0]->upload_image_file) ? Storage::url($schedulepost->images[0]->upload_image_file) : '' }}"
                                                        class="align-self-center" alt="post-image" style="height: 165px; width: 165px;">
                                                @else
                                                    <img src="{{ asset('frontend/images/no_image_available.jpeg') }}" alt="post-image">
                                                @endif
                                            @else
                                                @if (Storage::exists($schedulepost->upload_file))
                                                    @if (\File::extension(generateURL($schedulepost->upload_file ?? '')) == 'mp4')
                                                        <img src="{{ !empty($schedulepost->thumbnail) ? storage::url($schedulepost->thumbnail) : '' }}"
                                                            class="align-self-center" alt="post-image">
                                                    @else
                                                        <img src="{{ !empty($schedulepost->upload_file) ? storage::url($schedulepost->upload_file) : '' }}"
                                                            class="align-self-center" alt="post-image">
                                                    @endif
                                                @else
                                                    <img src="{{ asset('frontend/images/no_image_available.jpeg') }}" alt="post-image">
                                                @endif
                                            @endif

                                        </div>

                                    </div>
                                    <div class="post-card-footer justify-content-end">
                                        <div class="button-group px-5 my-6 my-lg-6">
                                            <div class="social-icon-group d-flex align-items-center">
                                                @foreach ($schedulepost->postMedia->unique('media_id'); as $postMedia)
                                                <img src="{{ $postMedia->media->image_url ?? "" }}"
                                                    alt="{{$postMedia->media->name}}"
                                                    style="max-width: 100%;width:28px;height:28px;" class="mr-2">
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Stats Widget 4-->
                            </div>
                            @empty
                            <div class="col-xl-12 col-lg-12 col-md-6 col-sm-6">
                                <div class="text-center not-found py-6 w-100">
                                    <h3 class="text-center">No Posts Found</h3>
                                </div>
                            </div>
                            @endforelse

                        </div>
                    </div>
                </div>


                <div
                    class="custom-pagination d-flex justify-content-center flex-wrap py-2 mr-3 pagination slider-pagination">
                </div>
            </div>
            <!--end::Info-->
            <!-- Button trigger modal -->
            {{-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
                Launch demo modal
            </button> --}}

            <!-- Modal -->
            <div class="modal fade price-modal" id="exampleModalCenter" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Manage Subscription</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                {{-- <span aria-hidden="true">&times;</span> --}}
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="price-card-grroup">
                                <div class="price-card">
                                    <div class="custom-card-header">
                                        <div class="price-content">
                                            <div class="price-icon">
                                                <img src="{{ asset('frontend/images/price-1.png') }}" alt="icon">
                                            </div>
                                            <div class="price-title-wrap">
                                                <h5 class="primary-color">Billed Monthly</h5>
                                                <h6 class="theme-color">Per Social Media Channel </h6>
                                                <h6 class="primary-color">Save upto 24% when paid annually</h6>
                                            </div>
                                        </div>
                                        <h4>$21</h4>
                                    </div>
                                    <ul class="price-list">
                                        <li>Unlimited Users</li>
                                        <li>Unlimited Posts</li>
                                        <li>Scheduled &amp; Plan Posts</li>
                                        <li>Organic Content Analytics</li>
                                        <li>Paid Advertising Analytics</li>
                                        <li>Desktop &amp; Mobile App Access</li>
                                        <li>Email &amp; Chat Support</li>

                                    </ul>
                                    <a class="common-btn large-btn" href="{{ route('media-page-list') }}">SUBSCRIBE</a>
                                    {{-- <div class="custom-modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary">Subscribe</button>
                                    </div> --}}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!--end::Content-->

@endsection
@push('extra-js-scripts')
<script type="text/javascript">
    $(document).ready(function() {
        let timeZoneNew = Intl.DateTimeFormat().resolvedOptions().timeZone;
        document.cookie = "timeZone="+timeZoneNew;
        $('.sliderImage').slick({
            infinite: false,
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: false,
            autoplaySpeed: 5000,
            arrows: true,
            dots: true,
        });
            $('#applyFilter').on('click', function(e) {
                e.preventDefault();
                if ($('#start_date').val() == '') {
                    $('#post_start_date_error').text('Please select the start date');
                } else if ($('#end_date').val() == '') {
                    $('#post_end_date_error').text('Please select the end date');
                } else {
                    loadData();
                }
            });
            $('#start_date').datepicker({
                todayHighlight: true,
                startDate: '01/01/2000',
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>',
                },
            }).on('change', function() {
                $('#post_start_date_error').text('');
                changeStartDate();
                var startDate = $(this).val();
                var endDate = $('#end_date').val();
                if(endDate.length !== 0 && endDate < startDate){
                    $('#post_start_date_error').text('you can\'t select start date after the end date');
                    $('#applyFilter').addClass('disabled');
                }else{
                    $('#applyFilter').removeClass('disabled');
                }
            });
            $('#end_date').datepicker({
                todayHighlight: true,
                startDate: '01/01/2000',
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>',
                },
            }).on('change', function() {
                $('#post_end_date_error').text('');
                changeEndDate();
                var startDate = $('#start_date').val();
                var endDate = $(this).val();
                if(startDate.length !== 0 && endDate <  startDate){
                    $('#post_end_date_error').text('you can\'t select end date before the start date');
                    $('#applyFilter').addClass('disabled');
                }else{
                    $('#applyFilter').removeClass('disabled');
                }
            });
            function changeStartDate() {
                var date = new Date($('#start_date').val());
                var date_formate = date.getDate() + ' ' + date.toLocaleString('en-US', {
                    month: 'short'
                }) + ", " + date.getFullYear();
                $('.start-date-text').text(date_formate);
            }

            function changeEndDate() {
                var date = new Date($('#end_date').val());
                var date_formate = date.getDate() + ' ' + date.toLocaleString('en-US', {
                    month: 'short'
                }) + ", " + date.getFullYear();
                $('.end-date-text').text(date_formate);
            }
            function loadData() {
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('filter.posts') }}",
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',
                        startDate: startDate,
                        endDate: endDate
                    },
                    success: function(response) {
                        $('.filter-post-cards').html(response['dataPost']);
                        $('.filter-schedule-post-cards').html(response['dataSchedule']);
                        // removeOverlay();
                    },
                });
            }
			$(window).on('load', function() {
				$.ajax({
                    type: 'POST',
                    url: "{{ route('user.subscribed') }}",
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
						if(response == 0)
						{
							$('#exampleModalCenter').modal('show');
						}
                    },
                });
            });
        });
</script>
@endpush
