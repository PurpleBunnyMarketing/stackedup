@extends('frontend.layouts.app')
@section('content')

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader">
        <div class="container">
            <div class="d-flex align-items-end justify-content-between flex-wrap flex-sm-nowrap">
                <!--begin::Info-->
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <!--begin::Heading-->
                    {{-- <div class="d-flex  post-type"> --}}
                        <!--begin::Title-->
                        <a href="javascript:;" class="{{ $type == 'posts' ? " active" : "" }} post-link btn
                            font-weight-bold py-3 px-6 mr-2 mt-4 common-btn text-nowrap" data-type=" posts">POSTS</a>
                        <a href="javascript:;" class="{{ $type == 'scheduled_posts' ? " active" : "" }} post-link btn
                            font-weight-bold py-3 px-6 mr-2 mt-4 common-btn text-nowrap"
                            data-type=" scheduled_posts">SCHEDULED
                            POSTS</a>
                        {{-- <a href="javascript:;" class="{{ $type == 'posts' ? " active" : "" }} post-link btn
                            font-size-h3 font-weight-bold my-2 mr-5 " data-type=" posts">POSTS</a>
                        <a href="javascript:;" class="{{ $type == 'scheduled_posts' ? " active" : "" }} post-link btn
                            font-size-h3 font-weight-bold btn-sm-md my-2 mr-5 text-nowrap"
                            data-type=" scheduled_posts">SCHEDULED
                            POSTS</a> --}}
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <!--end::Breadcrumb-->
                        {{--
                    </div> --}}
                    <!--end::Heading-->
                </div>
                <!--end::Info-->

                <!--begin::Toolbar-->
                <div class="d-flex align-items-center">
                    <!--begin::Button-->

                    <a href="{{ route('posts.create') }}"
                        class="btn font-weight-bold py-3 px-6 mr-2 mt-4 common-btn text-nowrap">ADD
                        POST</a>
                    <!--end::Button-->
                </div>
                <!--end::Toolbar-->
            </div>
            <div class="custom-filter-block">
                <div class="d-flex align-items-start flex-wrap gap-3 ">
                    <div class="head-select pr-3">
                        <select class="form-control media_page_id" id="media_page_id" name="media_page_id[]" multiple
                            data-error-container="#media-page-id-error">
                            @foreach ($media as $value)
                            <optgroup label="{{ $value->name }}">
                                @foreach ($value->mediaPages as $page)
                                <option value="{{ $page->id }}" data-media-id="{{ $value->id ?? '' }}">
                                    {{$page->page_name }}</option>
                                @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <form class="form d-flex " id="filterPost" method="post">
                        <div class="mr-6">
                            <div class="form-group mb-0">
                                <label class="col-form-label mr-4">Start Date</label>
                                {{-- <div class=""> --}}
                                    <input type="text" class="form-control" name="post_start_date"
                                        placeholder="Select date"
                                        value="{{ old('post_start_date') ?? \Carbon\Carbon::now()->startOfMonth()->format('m/d/Y') }}"
                                        id="post_start_date" data-error-container="#start-date-error"
                                        onkeyup="changePostStartDate()" />
                                    {{--
                                </div> --}}
                            </div>
                            <span id="post_start_date_error" class="post_filter_error"></span>
                        </div>
                        <div>
                            <div class="form-group mb-0">
                                <label class="col-form-label mr-4">End Date</label>
                                {{-- <div class=""> --}}
                                    <input type=" text" class="form-control" name="post_end_date"
                                        placeholder="Select date"
                                        value="{{ old('post_end_date') ?? \Carbon\Carbon::now()->endOfMonth()->format('m/d/Y') }}"
                                        id="post_end_date" data-error-container="#start-date-error"
                                        onkeyup="changePostEndDate()" />
                                    {{--
                                </div> --}}
                            </div>
                            <span id="post_end_date_error" class="post_filter_error"></span>
                        </div>
                    </form>
                    <a href="#" class="btn font-weight-bold px-6 mr-2 common-btn filter-btn ml-4" id="applyFilter">Apply
                    </a>
                </div>

                <div class="container">
                    <div class="row filter-post-cards">

                    </div>
                </div>
                <div class="container">
                    <div class="row filter-schedule-post-cards">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Subheader-->
    <div class="container">
        <div class="row post-cards">
        </div>
        <div class="custom-pagination d-flex justify-content-center flex-wrap py-2 mr-3 pagination slider-pagination">
        </div>
        {{-- <nav aria-label="reviews pagination" class="slider-pagination" style="width: 100%"></nav> --}}
    </div>
</div>

@endsection

@push('extra-js-scripts')
<script type="text/javascript">
    var page = 1;
	$(document).on('click', '.pagination a', function(e) {
        // console.log("DS");
        e.preventDefault();
        var type = $('.post-type').find('.active').data('type');
           page = $(this).attr('href').split('page=')[1];
        // page = $(this).attr('href').substr(($(this).attr('href').indexOf('?=')));
        loadData(type)
    });

	$(document).on('click','.btn-delete',function(){

		var type = $('.post-type').find('.active').data('type');
		var id = $(this).data('id');
        var route = "{{ route('posts.destroy', [':id']) }}";
        route = route.replace(':id', id);
        Swal.fire({
            title: "Are you sure?",
            text: "You won't to delete this Post!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    type: 'DELETE',
                    url: route,
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',
                        page: page || 1,
                        id:id
                    },
                    success: function(response) {
                        $('#card-'+id).remove();
                        var length = $('.post-cards .card').length;
                        if(length == 0){
                            page -= 1;
                        }
                        loadData(type);
                        // alert('successfully removed the post!');
                        type == 'posts' ? showMessage(200,"Post deleted successfully.") :showMessage(200,"Schedule post deleted successfully.");
                    },
                });
                Swal.fire({
                    title: "Deleted!",
                    icon: "success",
                    text: "Post was deleted.",
                    showConfirmButton: false,
                    timer: 1500,
                });
            }
        });
    });

    $(document).on('click','.post-now',function(){

        var type = $('.post-type').find('.active').data('type');


        var id = $(this).data('id');

        var url = "{{ route('post-now')}}";
        $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            beforeSend:addOverlay,
            data: {
                _token: '{{ csrf_token() }}',
                page: page || 1,
                id:id
            },
            success: function(response) {
                $('.post-link').removeClass('active');
                $('.post-link[data-type = "posts"]').addClass('active');
                $('#card-'+id).remove();
                var length = $('.post-cards .card').length;
                if(length == 0){
                    page -= 1;
                }
                loadData(type);

                showMessage(200,"Post now successfully.");
                removeOverlay();

            },
        });
    });

    $(document).on('click','.post-link',function(){
        var type = $(this).data('type');
        $('.post-link').removeClass('active');
        // $('#post_start_date').val('');
        // $('#post_end_date').val('');
        $(this).addClass('active');
        page = 1;
        loadData(type);

    });
    $(document).ready(function() {
        //set the timezone in cookies
        let timeZoneNew = Intl.DateTimeFormat().resolvedOptions().timeZone;
        document.cookie = "timeZone="+timeZoneNew;
        // console.log(timeZoneNew);
        $('#media_page_id').selectpicker().on('change',function(){
            loadData();
        });

        $('#applyFilter').on('click', function(e) {
            e.preventDefault();
            if($('#post_start_date').val() == ''){
                $('#post_start_date_error').text('Please select the start date');
            }else if($('#post_end_date').val() == ''){
                $('#post_end_date_error').text('Please select the end date');
            }else{
                loadData();
            }
        });

        $('#post_start_date').datepicker({
            todayHighlight: true,
            startDate: '01/01/2000',
            maxDate: new Date(),
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>',
            },
        }).on('change.datepicker', function(){
            $('#post_start_date_error').text('');
            changePostStartDate();
            var startDate = $(this).val();
            var endDate = $('#post_end_date').val();
            if(endDate.length !== 0 && endDate < startDate){
                $('#post_start_date_error').text('you can\'t select start date after the end date');
                $('#applyFilter').addClass('disabled');
            }else{
                $('#applyFilter').removeClass('disabled');
            }
        });
        $('#post_end_date').datepicker({
            todayHighlight: true,
            useCurrent: false,
            startDate: '01/01/2000',
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>',
            },
        }).on('change.datepicker', function(e){
            $('#post_end_date_error').text('');
            changePostEndDate();
            var startDate = $('#post_start_date').val();
            var endDate = $(this).val();
            if(startDate.length !== 0 && endDate <  startDate){
                $('#post_end_date_error').text('you can\'t select end date before the start date');
                $('#applyFilter').addClass('disabled');
            }else{
                $('#applyFilter').removeClass('disabled');
            }
        });
        function changePostStartDate() {
            var date = new Date($('#post_start_date').val());
            var date_formate = date.getDate() + ' ' + date.toLocaleString('en-US', {
                month: 'short'
            }) + ", " + date.getFullYear();
            $('.start-date-text').text(date_formate);
        }
        function changePostEndDate() {
            var date = new Date($('#post_end_date').val());
            var date_formate = date.getDate() + ' ' + date.toLocaleString('en-US', {
                month: 'short'
            }) + ", " + date.getFullYear();
            $('.end-date-text').text(date_formate);
        }
        loadData();

    });
    function loadData(type) {
        var startDate = $('#post_start_date').val();
        var endDate = $('#post_end_date').val();
        var mediaPages = $('#media_page_id').val();
        var type = $('.post-link.active').attr('data-type');
        var url = "{{ route('paginate.posts') }}";
        $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            beforeSend:addOverlay,
            data: {
                _token: '{{ csrf_token() }}',
                page: page || 1,
                type:type || "posts",
                startDate: startDate,
                endDate: endDate,
                mediaPagesId: mediaPages
            },
            success: function(response) {
                $('.post-cards').html(response.data);
                $('.slider-pagination').html(response.pages);
                removeOverlay();
                $('.sliderImage').slick({
                    infinite: false,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    autoplay: false,
                    autoplaySpeed: 5000,
                    arrows: true,
                    dots: true,
                });
            },
        });
    }


</script>

@endpush
