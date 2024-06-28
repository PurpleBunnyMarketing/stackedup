@extends('frontend.layouts.app')

@section('content')

<style>
    .dropzone .dz-preview .dz-image img {
        height: 100px !important;
        width: 100px !important;
        border-radius: 15px !important;
        object-fit: fill !important;
        object-position: center !important;
    }
</style>
<!--begin::Content-->
{{-- @dd($post->postMediaList->pluck('media_page_id')->toArray()) --}}
<div class="content d-flex flex-column flex-column-fluid add-post-page" id="kt_content">
    <div class="container">
        <div class="row  ">
            <div class="col-md-6">
                <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
                    <!--begin::Title-->
                    <h2 class="font-weight-bold my-2 mr-5">Edit Post</h2>
                    <!--end::Title-->
                </div>
                <div class="add-post-form">
                    <form id="frmEditPost" method="POST" action="{{ route('posts.update', $post->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <input type="hidden" name="images" id="images">
                        <input type="hidden" name="all_images" id="all_images">
                        <input type="hidden" name="thumbnail_image" id="thumbnail_image">
                        <input type="hidden" name="remove_upload_image" id="remove_upload_image">
                        <input type="hidden" name="remove_thumbnail_image" id="remove_thumbnail_image">
                        <input type="hidden" name="time_zone" id="time_zone" value="">

                        <!-- select start  -->
                        <div class="form-group row">
                            <div class="col-sm-12 ">
                                {{-- @dd($media,$post->postMediaList->pluck('media_page_id')->toArray()) --}}
                                <select class="form-control kt-bootstrap-select media_page_id" id="media_page_id"
                                    name="media_page_id[]" multiple name="select"
                                    data-error-container="#media-page-id-error">
                                    @foreach($media as $value)
                                    <optgroup label="{{ $value->name }}" id="optgroup-{{str_slug($value->name)}}">
                                        @foreach($value->mediaPages as $page)
                                        <option value="{{$page->id}}" data-media-id="{{ $value->id ?? '' }}" {{
                                            in_array($page->id,$post->postMediaList->pluck('media_page_id')->toArray())
                                            ? 'selected' : ''}}>{{ $page->page_name }}</option>
                                        @endforeach
                                        @endforeach

                                </select>
                                @if ($errors->has('media_page_id'))
                                <span class="invalid-feedback">
                                    <strong class="form-text text-danger">{{ $errors->first('media_page_id') }}</strong>
                                </span>
                                @endif
                                <span id="media-page-id-error"></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Upload File</label>
                            <div class="dropzone dropzone-default dz-clickable upload_file" id="upload_file"
                                accept=".jpg,.jpeg,.png" data-error-container="#dropzone-error" name="upload_file">
                                <div class="dropzone-msg dz-message needsclick">
                                    <div class="dropzone-image">
                                        <img src="{{ asset('frontend/assets/media/images/dropzone.svg') }}"
                                            alt="dropzone-image">
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="drop-image-group">
                                @if ($post->upload_file !== null)
                                <div class="drop-image upload-drop-image">
                                    <button class="close-upload-btn close-btn btn-danger"
                                        data-id="{{$post->upload_file}}">
                                        <i class="flaticon2-cross"></i>
                                    </button>
                                    @if(\File::extension($post->upload_file) == 'mp4')
                                    <a data-fancybox="gallery" href="{{ Storage::url($post->upload_file) ?? "" }}">
                                        <video>
                                            <source src="{{ Storage::url($post->upload_file) ?? "" }}" type="video/mp4">
                                        </video>
                                    </a>
                                    @else
                                    <a data-fancybox="gallery" href="{{ Storage::url($post->upload_file) ?? "" }}">
                                        <img src="{{ Storage::url($post->upload_file) ?? "" }}" alt="image">
                                    </a>
                                    @endif
                                </div>


                                @else
                                @foreach ($post->images->pluck('upload_image_file')->toArray() as $image)
                                <div class="drop-image-group">
                                    <div class="drop-image upload-drop-image">
                                        <button class="close-upload-btn close-btn btn-danger" data-id="{{$image}}">
                                            <i class="flaticon2-cross"></i>
                                        </button>
                                        <a data-fancybox="gallery" href="{{ Storage::url($image) ?? "" }}">
                                            <img src="{{ Storage::url($image) ?? "" }}" alt="image">
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                                @endif
                            </div> --}}
                        </div>
                        <span id="dropzone-error"></span>

                        {{-- <div class="form-group">
                            <label>Thumbnail Image</label>
                            <div class="dropzone dropzone-default dz-clickable thumbnail" id="thumbnail"
                                accept=".jpg,.jpeg,.png" data-error-container="#thumbnail-error" name="thumbnail">
                                <div class="dropzone-msg dz-message needsclick">
                                    <div class="dropzone-image">
                                        <img src="{{ asset(" frontend/assets/media/images/dropzone.svg") }}"
                                            alt="dropzone-image">
                                    </div>
                                </div>
                            </div>
                            <div class="drop-image-group">
                                <div class="drop-image thumbnail-drop-image">
                                    <button class="close-thumbnail-btn close-btn btn-danger"
                                        data-id="{{$post->thumbnail}}">
                                        <i class="flaticon2-cross"></i>
                                    </button>
                                    @if(\File::extension($post->thumbnail) == 'mp4')
                                    <a data-fancybox="gallery1" href="{{ Storage::url($post->thumbnail) ?? "" }}">
                                        <video width="100" height="100" controls>
                                            <source src="{{ Storage::url($post->thumbnail) ?? "" }}" type="video/mp4">
                                        </video>
                                    </a>
                                    @else
                                    <a data-fancybox="gallery1" href="{{ Storage::url($post->thumbnail) ?? "" }}">
                                        <img src="{{ Storage::url($post->thumbnail) ?? "" }}" alt="image">
                                    </a>
                                    @endif
                                </div>

                            </div>
                        </div>
                        <span id="thumbnail-error"></span> --}}
                        {{-- <input class="form-control min-h-80px py-4 px-8 mt-6" name="caption" id="caption"
                            type="text" placeholder="Caption" value="{{ old('caption',$post->caption) }}"
                            onkeyup="changeCaptionName()" /> --}}
                        <textarea name="caption" id="caption" placeholder="Caption" cols="30" rows="4"
                            onkeyup="changeCaptionName()"
                            class="form-control min-h-80px py-4  mt-6 @error('caption') is-invalid @enderror">{{ old('caption',$post->caption) }}</textarea>
                        @if ($errors->has('caption'))
                        <span class="invalid-feedback">
                            <strong class="form-text text-danger">{{ $errors->first('caption') }}</strong>
                        </span>
                        @endif
                        <input class="form-control h-auto py-4  mt-6" name="hashtag" id="hashtag"
                            value="{{ old('hashtag',$post->hashtag) }}" type="text" placeholder="Hashtag"
                            onkeyup="changeHashtagName()" />
                        @if ($errors->has('hashtag'))
                        <span class="invalid-feedback">
                            <strong class="form-text text-danger">{{ $errors->first('hashtag') }}</strong>
                        </span>
                        @endif
                        <div class="" id="google_fields">
                            <div class="form-group mt-6">
                                <label for="#action_type">Call To Action Button</label>
                                <select name="action_type" id="action_type"
                                    class="form-control kt-bootstrap-select @error('action_type') is-invalid @enderror"
                                    data-error-container="#action_type_error_container" {{
                                    in_array(5,$post->postMediaList->pluck('media_id')->toArray()) ? : 'disabled' }}>
                                    <option value="ACTION_TYPE_UNSPECIFIED" {{$post->call_to_action_type ==
                                        'ACTION_TYPE_UNSPECIFIED'? 'selected' : ''}}>NONE</option>
                                    <option value="BOOK" {{$post->call_to_action_type ==
                                        'BOOK'? 'selected' : ''}}>Book</option>
                                    <option value="ORDER" {{$post->call_to_action_type ==
                                        'ORDER'? 'selected' : ''}}>Order Online</option>
                                    <option value="SHOP" {{$post->call_to_action_type ==
                                        'SHOP'? 'selected' : ''}}>Buy</option>
                                    <option value="LEARN_MORE" {{$post->call_to_action_type ==
                                        'LEARN_MORE'? 'selected' : ''}}>Learn More</option>
                                    <option value="CALL" {{$post->call_to_action_type ==
                                        'CALL'? 'selected' : ''}}>Call Now</option>
                                </select>
                                <span id="action_type_error_container"></span>
                                @if ($errors->has('action_type'))
                                <span class="invalid-feedback">
                                    <strong class="form-text text-danger">{{ $errors->first('action_type') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group {{ $post->call_to_action_type === 'CALL' || $post->call_to_action_type === 'ACTION_TYPE_UNSPECIFIED' || $post->call_to_action_type === null ? 'd-none': ''}}"
                                id="action_link_container">
                                <input type="url" name="action_link" id="action_link"
                                    class="form-control h-auto  mt-6 @error('action_link') is-invalid @enderror"
                                    placeholder="Link For your Button"
                                    value="{{old('action_link') ?? $post->action_link}}">
                                @if ($errors->has('action_link'))
                                <span class="invalid-feedback">
                                    <strong class="form-text text-danger">{{ $errors->first('action_link') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="input-group mt-6">
                            <input type="text" class="form-control @error('schedule_date') is-invalid @enderror"
                                name="schedule_date" placeholder="Select date"
                                value="{{ old('schedule_date', \Carbon\Carbon::parse($post->schedule_date_time)->format('m/d/Y')) }}"
                                id="schedule_date" data-error-container="#schedule-date-error"
                                onkeyup="changeScheduleDate()" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                            @if ($errors->has('schedule_date'))
                            <span class="invalid-feedback">
                                <strong class="form-text text-danger">{{ $errors->first('schedule_date') }}</strong>
                            </span>
                            @endif
                        </div>


                        <span id="schedule-date-error"></span>
                        <div class="input-group timepicker my-6">
                            <input class="form-control @error('schedule_time') is-invalid @enderror"
                                name="schedule_time" id="schedule_time" placeholder="Select time" type="text"
                                value="{{ old('schedule_time',\Carbon\Carbon::parse($post->schedule_date_time)->format('g:i A')) }}"
                                onkeyup="changeScheduleTime()" readonly />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-clock-o"></i>
                                </span>
                            </div>
                            @if ($errors->has('schedule_time'))
                            <span class="invalid-feedback">
                                <strong class="form-text text-danger">{{ $errors->first('schedule_time') }}</strong>
                            </span>
                            @endif
                        </div>

                        <span id="error-time"></span>

                        <!-- select end  -->
                        <div class="d-flex align-items-center">
                            <!--begin::Button-->
                            <button type="submit"
                                class="btn common-btn secondary-color font-weight-bold py-3 px-6 mr-2 ">Submit</button>
                            <a href="{{ route('posts.index')}}" class="btn btn-secondary post-now">Cancel</a>

                            <!--end::Button-->
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-6 post-preview">
                <div class="preview-list"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('extra-js-scripts')
<script src="{{ asset('frontend/assets/js/pages/crud/file-upload/dropzonejs.js') }}"></script>
<script src="{{ asset('frontend/assets/js/jquery.fancybox.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#time_zone').val(Intl.DateTimeFormat().resolvedOptions().timeZone)
        $('#action_type').selectpicker();
        let page_ids =[];
        var selected_media_ids_of_page_id = {{ $post->postMediaList->pluck('media_id') }};

        let file_paths = JSON.parse(`{!! $post->thumbnail ? collect($post->thumbnail) : ($post->upload_file ? collect($post->upload_file) : $post->images->pluck('upload_image_file'))!!}`);

        // var max_upload_file_size = "{{ $max_file_size }}";
        var max_upload_file_size = 10000;

        // For validate the file size of the video of each social media
        var social_media_video_size =JSON.parse(`{!! json_encode($social_media_video_size) !!}`);

        var thumbnail_image = null;
        $.validator.addMethod("hashtag_check", function (value, element) {
            // console.log(this.optional(element) || /^#[\w-]+(?:\s#[\w-]+)*$/mg.test(value));
            return this.optional(element) || /^#[\w-]+(?:\s#[\w-]+)*$/mg.test(value);
        });
        $('[data-fancybox="gallery"]').fancybox({
            });
        $('[data-fancybox="gallery1"]').fancybox({
            });

        $('body').on('change', '#media_page_id', function(){
            $('#media_page_id').valid();
        });

        $('#caption').on('change',function(){
            changeCaptionName();

        });
        $('#hashtag').on('change',function(){
            changeHashtagName();

        });
        $('#schedule_date').on('change',function(){
            changeScheduleDate();

        });
        $('#schedule_time').on('change', function() {
            changeScheduleTime();
        });
        $('#schedule_time').timepicker({
           minuteStep: 1,
           defaultTime: '',
           showSeconds: false,
           showMeridian: true,
           snapToStep: true,
           minTime: 0,
        }).on('change', function(){
                var time = $(this).val();
                var date = $('#schedule_date').val();
                var hours = Number(time.match(/^(\d+)/)[1]);
                var minutes = Number(time.match(/:(\d+)/)[1]);
                var AMPM = time.match(/\s(.*)$/)[1];
                if (AMPM == "PM" && hours < 12) hours = hours + 12;
                if (AMPM == "AM" && hours == 12) hours = hours - 12;
                var sHours = hours.toString();
                var sMinutes = minutes.toString();
                if (hours < 10) sHours = "0" + sHours;
                if (minutes < 10) sMinutes = "0" + sMinutes;
                var time1 = sHours + ":" + sMinutes;
                var from = Date.parse(date + " " + time1);
                var close = Date.parse(new Date());

                if(close > from) {
                    $('#schedule_time').addClass('is-invalid');
                    $('#error-time').css('display','block');
                     $('#error-time').text('The time must be greater then now');
                     $('#error-time').addClass('invalid-feedback');
                    $("input[type=submit], input[type=button], button[type=submit]").prop("disabled",
                    "disabled");
                    return false;
                }else{
                    $('#schedule_time').removeClass('is-invalid');
                    $('#error-time').removeClass('invalid-feedback');
                    $("input[type=submit], input[type=button], button[type=submit]").prop("disabled",
                    false);
                      $('#error-time').html('');
                    return true;
                }

            });

        $('#schedule_date').datepicker({
            todayHighlight: true,
            startDate:new Date(),
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>',
            },
        }).on('change', function(){
            var date = $(this).val();
            var time = $('#schedule_time').val();
            var hours = Number(time.match(/^(\d+)/)[1]);
            var minutes = Number(time.match(/:(\d+)/)[1]);
            var AMPM = time.match(/\s(.*)$/)[1];
            if (AMPM == "PM" && hours < 12) hours = hours + 12;
            if (AMPM == "AM" && hours == 12) hours = hours - 12;
            var sHours = hours.toString();
            var sMinutes = minutes.toString();
            if (hours < 10) sHours = "0" + sHours;
            if (minutes < 10) sMinutes = "0" + sMinutes;
            var time1 = sHours + ":" + sMinutes;
            var from = Date.parse(date + " " + time1);
            var close = Date.parse(new Date());
            if(close > from) {
                $('#schedule_time').addClass('is-invalid');
                $('#error-time').css('display','block');
                $('#error-time').text('The time must be greater then now');
                $('#error-time').addClass('invalid-feedback');
                $("input[type=submit], input[type=button], button[type=submit]").prop("disabled",
                "disabled");
                return false;
            }else{
                $('#schedule_time').removeClass('is-invalid');
                $('#error-time').removeClass('invalid-feedback');
                $("input[type=submit], input[type=button], button[type=submit]").prop("disabled",
                false);
                    $('#error-time').html('');
                return true;
            }
        });
        var images = JSON.parse(`{!! $post->upload_file ? collect($post->upload_file) : $post->images->pluck('upload_image_file')!!}`);
        // console.log('initial image',images);
        var new_uploaded_images = [];
        var remove_upload_image = [];
        var remove_thumbnail_image = [];
        var thumbnail_image;

        if(images.length > 0){
            $('.dropzone-image').hide();
        }

        mediaPreview();

        if ($('#upload_file').length) {
            $('#upload_file').dropzone({
                // paramName: "file", // The name that will be used to transfer the file
                maxFiles: 10,
                maxfilesexceeded: function(file) {
                    $(".dropzone").addClass("is-invalid");
                    $("#dropzone-error").text('You have exceeded the File Upload Limit, You can not add more files.');
                    $("#dropzone-error").show();
                    // this.removeAllFiles();
                    this.addFile(file);
                    // this.removeFile(file);
                },
                maxFilesize: max_upload_file_size, // MB
                timeout: null,
                addRemoveLinks: true,
                acceptedFiles: "image/*,.mp4,image/webp",
                dictMaxFilesExceeded: "You can not upload any more files.",
                dictRemoveFile: "Remove file",
                url: "{{ route('posts.storeimage') }}",
                clickable: true,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                params: {
                    'type' : "upload_file",
                },
                accept:function(file,done){
                    const file_size = file ? parseFloat(file.size / 1000000).toFixed(1) : undefined;
                    if(file != null && (file.type == 'video/mp4' || file.type == 'video/mov')){
                        var isVideoCorrect = isVideoSizeIsCorrect(file);
                        // console.log('isVideoCorrect',isVideoCorrect);
                        if(!isVideoCorrect.status){
                            $(".dropzone").addClass("is-invalid");
                            $("#dropzone-error").text(`File is too big for ${isVideoCorrect.mediaName}, It must be less then ${isVideoCorrect.mediaSize} mb.`);
                            $("#dropzone-error").show();
                        }else{
                            $("#dropzone-error").hide();
                            $(".dropzone").removeClass("is-invalid");
                        }
                    }
                    done();
                },
                init:function(){
                    var myDropzone = this;
                    // this.on("sending", function(file, xhr, data) {
                    //     data.append("media_page_id", page_ids);
                    // });
                    $.ajax({
                        url: "{{ route('posts.getimages') }}",
                        type: 'GET',
                        data: {
                            request: 'fetch',
                            post_id: '{{ $post->custom_id }}',
                        },
                        dataType: 'json',
                        success: function(response){
                            if(response.length > 0){
                                $.each(response, function(key,value) {
                                    // console.log(value);
                                    var file = { name: value.name, size: value.size};
                                    myDropzone.options.addedfile.call(myDropzone, file);
                                    myDropzone.options.thumbnail.call(myDropzone, file, value.path);
                                    myDropzone.emit("complete", file);

                                    $(file.previewElement).attr("data-path", value.name);
                                    if( (value.type == 'mp4' || value.type=='mov')){
                                        $(file.previewElement ).attr("data-thumbnail-image", value.path);
                                    }

                                });
                            } else{
                                $('.dropzone-image').show();
                            }
                        }
                    });
                },
                queuecomplete:function(file,done){
                    var count_video_files = 0;
                    var count_image_files = 0;
                    // console.log('queue images',images);
                    $.each(images,function(key,value){
                        let fileExtension = value.split('.').pop();
                        if(fileExtension == 'mov' || fileExtension == 'mp4'){
                            count_video_files++;
                        }else if(fileExtension == 'jpg' || fileExtension == 'jpeg' ||fileExtension == 'webp' ||fileExtension == 'png' ){
                            count_image_files++;
                        }
                    });
                    // console.log(count_image_files,count_video_files);
                    if(count_video_files >= 1 && count_image_files >= 1){
                        $(".dropzone").addClass("is-invalid");
                        $("#dropzone-error").text('You can not upload video when you select one or more images, You need to Remove Videos Or select only One Video and remove all the images').addClass("invalid-feedback");
                        $("#dropzone-error").show();
                    }else if(count_video_files > 1){
                        $(".dropzone").addClass("is-invalid");
                        $("#dropzone-error").text('You can Upload only one Video at a time').addClass("invalid-feedback");
                        $("#dropzone-error").show();
                    }else if(count_video_files + count_image_files > 10){
                        $(".dropzone").addClass("is-invalid");
                        $("#dropzone-error").text('You have exceeded the File Upload Limit, You can not add more files.');
                        $("#dropzone-error").show();
                    }else{
                        $("#dropzone-error").hide();
                        $(".dropzone").removeClass("is-invalid");
                    }
                    if(count_image_files > 4){
                        var selected = false;
                        var twitter_options = $("#optgroup-twitter").children();
                        $(twitter_options).each(function(){
                            if($(this)[0].selected == true){
                                selected = true;
                                // document.getElementById('media_page_id').options[$(this)[0].index].selected = false;
                            }
                        });
                        if(selected){
                            $(".dropzone").addClass("is-invalid");
                            $("#dropzone-error").text('X(Twitter) does not allowed More then 4 Images. Please remove Files and select option').addClass("invalid-feedback");
                            $("#dropzone-error").show();
                        }
                    }
                    if(count_image_files > 8){
                        var selected = false;
                        var linkedin_options = $("#optgroup-linkedin").children();
                        $(linkedin_options).each(function(){
                            if($(this)[0].selected == true){
                                selected = true;
                                // document.getElementById('media_page_id').options[$(this)[0].index].selected = false;
                            }
                        });
                        if(selected){
                            $(".dropzone").addClass("is-invalid");
                            $("#dropzone-error").text('Linkedin does not allowed More then 8 Images,Please remove Files and select option.').addClass("invalid-feedback");
                            $("#dropzone-error").show();
                        }
                    }
                    if(count_image_files > 10){
                        var selected = false;
                        var facebook_options = $("#optgroup-facebook").children();
                        $(facebook_options).each(function(){
                            if($(this)[0].selected == true){
                                selected = true;
                                // document.getElementById('media_page_id').options[$(this)[0].index].selected = false;
                            }
                        });
                        if(selected){
                            $(".dropzone").addClass("is-invalid");
                            $("#dropzone-error").text('Facebook & Instagram does not allowed More then 10 Images,Please remove Files and select option.').addClass("invalid-feedback");
                            $("#dropzone-error").show();
                        }
                    }
                    if(this.files.length > 1 && jQuery.inArray(5,selected_media_ids_of_page_id) !== -1){
                        $(".dropzone").addClass("is-invalid");
                        $("#dropzone-error").text('You can select only 1 image, If you Select Google My Business Social Media.').addClass("invalid-feedback");
                        $("#dropzone-error").show();
                    }
                    // chanegImageAfterUpload(file_paths,thumbnail_image);
                },
                success: function (file, response) {
                    var file_type = file.type;
                    file_paths.push(response.created_thumbnail !== '' ? response.created_thumbnail : response.path);
                    var imagePath = response.path;
                    file.previewElement.classList.add("dz-success");
                    if(jQuery.inArray(4,selected_media_ids_of_page_id) !== -1){
                        // if(!videoCheck(file)){
                            videoCheck(file)
                        // }
                    }
                    var element = file.previewElement;
                    $(element).attr('data-path', response.path);
                    if(response.created_thumbnail !== ''){
                        $(element).attr('data-thumbnail-image', response.created_thumbnail);
                        chanegImageAfterUpload(file_paths, response.created_thumbnail);
                        $(element).children('div.dz-image').children('img').attr('src', response.created_thumbnail);
                    }
                    $("#dropzone-error").hide();
                    // file_paths.push(response.path);
                    chanegImageAfterUpload(file_paths,thumbnail_image);
                    $(".dropzone").removeClass("is-invalid");
                    new_uploaded_images.push(response.path);
                    // console.log(response.created_thumbnail);

                    images.push(response.path);
                    thumbnail_image = response.created_thumbnail
                    // display custom thumbnail image field if the uploaded file is the video
                    if(file_type == 'video/mp4' || file_type == 'video/mov'){
                        $('#custom_thumbnail_container').removeClass('d-none');
                    }
                    var isVideoCorrect = isVideoSizeIsCorrect(file);
                    if(!isVideoCorrect.status){
                        $(".dropzone").addClass("is-invalid");
                        $("#dropzone-error").text(`File is too big for ${isVideoCorrect.mediaName}, It must be less then ${isVideoCorrect.mediaSize} mb.`);
                        $("#dropzone-error").show();
                    }else{
                        $("#dropzone-error").hide();
                        $(".dropzone").removeClass("is-invalid");
                    }
                },
                removedfile: function(file) {
                    var app_url = "{{ env('APP_URL').'/storage/' }}";
                    let files = this.files;
                    var rem_elem = file.previewElement;
                    let path = $(rem_elem).attr('data-path');
                    let thumbanil_image = $(rem_elem).attr('data-thumbnail-image');
                    $.ajax({
                        url: "{{ route('posts.deleteimage') }}",
                        type: 'POST',
                        beforeSend:addOverlay,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            path : path,
                            type : "upload_file",
                        },
                        success:function(response){
                            if(response.success == 'false'){
                                images = images.filter((image)=> image !== path);
                                remove_upload_image.push(response.path);
                                if(thumbanil_image !== null && thumbanil_image !== undefined)
                                {
                                    var abc = thumbanil_image.substring(thumbanil_image.lastIndexOf('/') + 1);
                                    path = `thumbnail/${abc}`;
                                    remove_thumbnail_image.push(path);
                                }

                                file_paths = file_paths.filter((image)=> image !== path)
                                chanegImageAfterUpload(file_paths,thumbnail_image);
                                rem_elem.remove();
                            } else{
                                images=images.filter((image)=> image !== path);
                                // console.log('path',path);

                                // console.log(file_paths,images);
                                var file_extension = path.substring(path.lastIndexOf('.') + 1);
                                if(file_extension == 'mp4' || file_extension == 'mov'){
                                    var filename = path.substring(path.lastIndexOf('/') + 1);
                                    filename = filename.slice(0,filename.lastIndexOf('.'));
                                    path = `{{ \Storage::url('thumbnail/${filename}.jpg') }}`;
                                }
                                file_paths=file_paths.filter((image)=> image !== path);
                                new_uploaded_images=new_uploaded_images.filter((image)=> image !== path);
                                // if(thumbanil_image !== null && thumbanil_image !== undefined){
                                //     path = thumbanil_image
                                // }

                                removeOverlay();
                                chanegImageAfterUpload(file_paths,thumbnail_image);
                                rem_elem.remove();
                                var count_video_files =0;
                                var count_image_files =0;
                                $.each(images,function(key,value){
                                    let fileExtension = value.split('.').pop();
                                    if(fileExtension == 'mov' || fileExtension == 'mp4'){
                                        count_video_files++;
                                    }else if(fileExtension == 'jpg' || fileExtension == 'jpeg' ||fileExtension == 'webp' ||fileExtension == 'png' ){
                                        count_image_files++;
                                    }
                                });
                                if(count_video_files >= 1 && count_image_files >= 1){
                                    $(".dropzone").addClass("is-invalid");
                                    $("#dropzone-error").text('You can not upload video when you select one or more images, You need to Remove Videos Or select only One Video and remove all the images').addClass("invalid-feedback");
                                    $("#dropzone-error").show();
                                }else if(count_video_files > 1){
                                    $(".dropzone").addClass("is-invalid");
                                    $("#dropzone-error").text('You can Upload only one Video at a time').addClass("invalid-feedback");
                                    $("#dropzone-error").show();
                                }else if(count_video_files + count_image_files > 10){
                                    $(".dropzone").addClass("is-invalid");
                                    $("#dropzone-error").text('You have exceeded the File Upload Limit, You can not add more files.').addClass("invalid-feedback");
                                    $("#dropzone-error").show();
                                }else{
                                    $("#dropzone-error").hide();
                                    $(".dropzone").removeClass("is-invalid");
                                }
                                if(count_image_files > 4){
                                    var selected = false;
                                    var twitter_options = $("#optgroup-twitter").children();
                                    $(twitter_options).each(function(){
                                        if($(this)[0].selected == true){
                                            selected = true;
                                            // document.getElementById('media_page_id').options[$(this)[0].index].selected = false;
                                        }
                                    });
                                    if(selected){
                                        $(".dropzone").addClass("is-invalid");
                                        $("#dropzone-error").text('X(Twitter) does not allowed More then 4 Images. Please remove Files and select option').addClass("invalid-feedback");
                                        $("#dropzone-error").show();
                                    }
                                }
                                if(count_image_files > 8){
                                    var selected = false;
                                    var linkedin_options = $("#optgroup-linkedin").children();
                                    $(linkedin_options).each(function(){
                                        if($(this)[0].selected == true){
                                            selected = true;
                                            // document.getElementById('media_page_id').options[$(this)[0].index].selected = false;
                                        }
                                    });
                                    if(selected){
                                        $(".dropzone").addClass("is-invalid");
                                        $("#dropzone-error").text('Linkedin does not allowed More then 8 Images,Please remove Files and select option.').addClass("invalid-feedback");
                                        $("#dropzone-error").show();
                                    }
                                }
                                if(count_image_files > 10){
                                    var selected = false;
                                    var facebook_options = $("#optgroup-facebook").children();
                                    $(facebook_options).each(function(){
                                        if($(this)[0].selected == true){
                                            selected = true;
                                            // document.getElementById('media_page_id').options[$(this)[0].index].selected = false;
                                        }
                                    });
                                    if(selected){
                                        $(".dropzone").addClass("is-invalid");
                                        $("#dropzone-error").text('Facebook & Instagram does not allowed More then 10 Images,Please remove Files and select option.').addClass("invalid-feedback");
                                        $("#dropzone-error").show();
                                    }
                                }
                                if(file_paths.length > 1 && jQuery.inArray(5,selected_media_ids_of_page_id) !== -1){
                                    // console.log('in google');
                                    $(".dropzone").addClass("is-invalid");
                                    $("#dropzone-error").text('You can select only 1 image, If you Select Google My Business Social Media.').addClass("invalid-feedback");
                                    $("#dropzone-error").show();
                                }
                            }
                        },
                        complete:removeOverlay
                    });
                    if($(".dropzone").hasClass('is-invalid')){
                        isVideoSizeIsCorrect();
                    }
                },
                error: function (file, response) {
                    let errmsg = ''
                    var status = file.xhr.status;

                    if(status == 413){
                        $(".dropzone").addClass("is-invalid");
                        $("#dropzone-error").text(`This file is to Large for upload,You can\'t upload more than ${max_upload_file_size} MB size of single file. Please try to upload the file with lower size.`);
                        $("#dropzone-error").show();
                    }else{
                        if(response.errorType == 'instagram_image'){
                            // errmsg = response.message
                            $(file.previewElement).find('.dz-image').css({"border" : "2px solid #d90101"});
                            $(file.previewElement).addClass("dz-error").find('.dz-error-message').text(response.message_title);
                            $(".dropzone").addClass('is-invalid');

                            $("#dropzone-error").text(response.message);
                            $("#dropzone-error").show();
                        }else{
                            if(response == 'error'){
                                errmsg = 'Something went wrong please upload another file.Allowed file types are image/*,.mp4,image/webp,.mov'
                            }else{
                                errmsg = response;
                            }
                            $(".dropzone").addClass("is-invalid");
                            $("#dropzone-error").text(errmsg);
                            $("#dropzone-error").show();
                            this.removeFile(file);
                            // file.previewElement.classList.add("dz-error");
                        }
                    }
                }
            });

            var sortable = new Sortable(document.getElementById('upload_file'), {
                animation: 150, // You can adjust the animation duration
                handle: '.dz-preview', // Element used as the handle for dragging
                onEnd: function (event) {
                    if($('.dz-preview').length > 1){
                        images = [];
                        $("[id='imageContainer']").html('');

                        $('.dz-preview').each(function (index) {

                            if($(this).data('thumbnail-image') != undefined){
                                images.push($(this).data('thumbnail-image'));
                            }else{
                                images.push($(this).data('path'));
                            }
                        });
                        console.log('images',images);
                        chanegImageAfterUpload(images);
                    }
                },
            });
        }
        $(document).on('change', '#action_type', function() {
            var action_type = $(this).val();
            if(action_type == 'ACTION_TYPE_UNSPECIFIED' || action_type == 'CALL'){
                $('#action_link_container').addClass('d-none')
            }else{
                $('#action_link').val(null);
                $('#action_link_container').removeClass('d-none')
            }

        });
        $('#media_page_id').selectpicker();
        $(document).on('changed.bs.select', '#media_page_id', function(e,clickedIndex) {
            var media_page_id = $('#media_page_id').val();
            page_ids = media_page_id;

            var media_page_id_token = document.getElementById('media_page_id').options[clickedIndex];

            if(selected_media_ids_of_page_id[clicked_index_array.indexOf(clickedIndex)] !== undefined){ selected_media_ids_of_page_id.splice(clicked_index_array.indexOf(clickedIndex),1); }
            else {selected_media_ids_of_page_id.push($(media_page_id_token).data('media-id')); }

            if(jQuery.inArray(clickedIndex,clicked_index_array) !== -1){ clicked_index_array = clicked_index_array.filter((index) => index !== clickedIndex);}
            else { clicked_index_array.push(clickedIndex); }
            // console.log(selected_media_ids_of_page_id,'selected_media_ids_of_page_id');
            if (jQuery.inArray(3,selected_media_ids_of_page_id) !== -1)
            {
                var hashtagLength = $('#hashtag').val().length;
                var captionLength = $('#caption').val().length;
                if(captionLength + hashtagLength > 280){
                    $('#caption-error').text('You can not add more then 280 character Because X(Twitter) does not allowed.');
                    $('#caption').addClass('is-invalid');
                    // if(hashtagLength > 0){
                        $('#hashtag').addClass('is-invalid');
                    // }
                }else{
                    $('#caption-error').text('');
                    $('#caption').removeClass('is-invalid');
                    $('#hashtag').removeClass('is-invalid');
                }
            }
            if(jQuery.inArray(5,selected_media_ids_of_page_id) !== -1){
                $('#action_type').removeAttr('disabled');
                $('#action_type').selectpicker('refresh');
            }
            if(media_page_id_token.selected){
                var is_expired = checkForTokenExpiry(media_page_id_token.value);
                if(is_expired.status){
                    document.getElementById('media_page_id').options[clickedIndex].selected = false;
                    showMessage(412, is_expired.message);
                    return;
                }
            }
            mediaPreview();
        });

        function videoCheck(file){
            if(!file.type.match('image/*')){
                var video = document.createElement('video');
                video.preload = 'metadata';
                var validAcceptRatio = [

                ];
                video.onloadedmetadata = function() {
                    window.URL.revokeObjectURL(video.src);
                    var duration = parseInt(video.duration);
                    var aspectRatio = video.videoWidth / video.videoHeight;

                    if(duration > 60){
                        $(".dropzone").addClass("is-invalid");
                        $("#dropzone-error").text('Instagram does not allow to upload the video duration more then 60 Sec.');
                        $("#dropzone-error").show();
                        $(file.previewElement).find('.dz-image').css({"border" : "2px solid #d90101"});
                        $(file.previewElement).addClass("dz-error").find('.dz-error-message').text('Invalid Video Duration');
                    }else if((aspectRatio < 0.8 || aspectRatio > 1.8) || video.videoWidth > 1920){
                        $(".dropzone").addClass("is-invalid");
                        $("#dropzone-error").text('Instagram does only allows to upload the video of aspect Ratio between 4/5 to 16/9.');
                        $("#dropzone-error").show();
                        $(file.previewElement).find('.dz-image').css({"border" : "2px solid #d90101"});
                        $(file.previewElement).addClass("dz-error").find('.dz-error-message').text('Invalid Accept Ration');
                    }else if(bitrate){

                    }
                }
                video.src = URL.createObjectURL(file);
            }else{
                return true;
            }
        }

        function isVideoSizeIsCorrect(file = null){
            const file_size = file ? parseFloat(file.size / 1000000).toFixed(1) : undefined;

            var returnResponse = {
                status : true,
                mediaName : '',
                mediaSize : ''
            };
            if(file != null){
                if(jQuery.inArray(1,selected_media_ids_of_page_id) !== -1 && parseInt(file_size) >= parseInt(social_media_video_size.facebook)){
                    returnResponse.status = false;
                    returnResponse.mediaName = 'Facebook';
                    returnResponse.status = social_media_video_size.facebook;
                }else if(jQuery.inArray(2,selected_media_ids_of_page_id) !== -1 && parseInt(file_size) >= parseInt(social_media_video_size.linkedin)){
                    returnResponse.status = false;
                    returnResponse.mediaName = 'LinkedIn';
                    returnResponse.mediaSize = social_media_video_size.linkedin;

                }else if(jQuery.inArray(3,selected_media_ids_of_page_id) !== -1 && parseInt(file_size) >= parseInt(social_media_video_size.twitter)){
                    returnResponse.status = false;
                    returnResponse.mediaName = 'Twitter';
                    returnResponse.mediaSize = social_media_video_size.twitter;

                }else if(jQuery.inArray(4,selected_media_ids_of_page_id) !== -1 && parseInt(file_size) >= parseInt(social_media_video_size.instagram)){
                    returnResponse.status = false;
                    returnResponse.mediaName = 'Instagram';
                    returnResponse.mediaSize = social_media_video_size.instagram;
                }
            }
            return returnResponse;
        }

        $("#frmEditPost").validate({
            ignore: [],
            rules: {
                caption:{
                    required:true,
                    not_empty:true,
                },
                hashtag:{
                    required:false,
                    not_empty:false,
                    maxlength:150,
                    hashtag_check: true,
                },
                schedule_date:{
                    required:function(){
                        var schedule_time = $('#schedule_time').val();
                        if(schedule_time != ''){
                            return true;
                        }else{
                            return false;
                        }
                    },
                },
                schedule_time:{
                    required:function(){
                        var schedule_date = $('#schedule_date').val();
                        if(schedule_date != ''){
                            return true;
                        }else{
                            return false;
                        }
                    },
                },
                // upload_file:{
                //     required:false,
                //     extension: "jpg|jpeg|png|mp4",
                // },
                'media_page_id[]':{
                    required:true,
                },
                action_link:{
                    required:function(){
                        return $('#action_type').val() !== 'ACTION_TYPE_UNSPECIFIED' && $('#action_type').val() !== 'CALL';
                    },
                    url:true
                },
            },
            messages: {
                caption:{
                    required:"@lang('validation.required',['attribute'=>'caption'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'caption'])",
                },
                hashtag:{
                    required:"@lang('validation.required',['attribute'=>'hashtag'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'hashtag'])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'hashtag','max'=>150])",
                    hashtag_check: "Enter Valid hashtag with '#' sign and space saparator.",
                },
                schedule_date:{
                    required:"@lang('validation.required',['attribute'=>'schedule date'])",
                },
                schedule_time:{
                    required:"@lang('validation.required',['attribute'=>'schedule time'])",
                },
                // upload_file:{
                //     required:"@lang('validation.required',['attribute'=>'upload file'])",
                //     extension:"@lang('validation.mimetypes',['attribute'=>'upload file','value'=>'jpg|png|jpeg'])",
                // },

                "media_page_id[]":{
                    required:"@lang('validation.required',['attribute'=>'media page'])"
                },
                action_link:{
                    required: "@lang('validation.required', ['attribute' => 'Call to Action Link'])",
                    url: "@lang('validation.url', ['attribute' => 'Call to Action Link'])"
                },
            },
            errorClass: 'invalid-feedback',
            errorElement: 'span',
            highlight: function (element) {
                $(element).addClass('is-invalid');
                $(element).siblings('label').addClass('text-danger'); // For Label
                $(element).parent().closest('.bootstrap-select').addClass('select-validation');

            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
                $(element).siblings('label').removeClass('text-danger'); // For Label
                $(element).parent().closest('.bootstrap-select').removeClass('select-validation');
            },
            errorPlacement: function (error, element) {
                if (element.attr("data-error-container")) {
                    error.appendTo(element.attr("data-error-container"));
                } else {
                    error.insertAfter(element);
                }
            }
        });
        $('#frmEditPost').submit(function () {
            if (jQuery.inArray(3,selected_media_ids_of_page_id) !== -1)
            {
                var hashtagLength = $('#hashtag').val().length;
                var captionLength = $('#caption').val().length;
                if(captionLength + hashtagLength > 280){
                    $('#caption-error').text('You can not add more then 280 character Because X(Twitter) does not allowed.');
                    $('#caption').addClass('is-invalid');
                    $('#hashtag').addClass('is-invalid');
                    return false;
                }else{
                    $('#caption-error').text('');
                    $('#caption').removeClass('is-invalid');
                    $('#hashtag').removeClass('is-invalid');
                }
            }
            if(jQuery.inArray(4,selected_media_ids_of_page_id) !== -1 && images.length < 0){
                $(".dropzone").addClass("is-invalid");
                $("#dropzone-error").text('You Must have to select At least one image, If you Selected Instagram Social Media.');
                $("#dropzone-error").show();
            }
            if ($(this).valid() && !$('#upload_file').hasClass('is-invalid') && checkCurrentTimeForSubmit()) {
                $("#images").val(new_uploaded_images);
                $("#all_images").val(images);
                $("#thumbnail_image").val(thumbnail_image);
                $("#remove_upload_image").val(remove_upload_image);
                $("#remove_thumbnail_image").val(remove_thumbnail_image);

                addOverlay();
                $("input[type=submit], input[type=button], button[type=submit]").prop("disabled", "disabled");
                return true;
            } else {
                return false;
            }
        });
        $(".close-upload-btn").click(function(e){
            e.preventDefault();
            var thisElement = $(this);
            var path = $(this).attr("data-id");
            remove_upload_image.push(path);
            images = images.filter((image)=> image !== path);
            file_paths = file_paths.filter((image)=> image !== path)
            $(this).parents(".drop-image").remove();
            chanegImageAfterUpload(file_paths,thumbnail_image);
            var count_video_files = 0;
            var count_image_files = 0;
            $.each(images,function(key,value){
                let fileExtension = value.split('.').pop();
                if(fileExtension == 'mov' || fileExtension == 'mp4'){
                    count_video_files++;
                }else if(fileExtension == '.jpg' || fileExtension == 'jpeg' ||fileExtension == 'webp' ||fileExtension == 'png' ){
                    count_image_files++;
                }
            });
            if(count_video_files >= 1 && count_image_files >= 1){
                $(".dropzone").addClass("is-invalid");
                $("#dropzone-error").text('You can not upload video when you select one or more images, You need to Remove Videos Or select only One Video and remove all the images').addClass("invalid-feedback");
                $("#dropzone-error").show();
            }else if(count_video_files > 1){
                $(".dropzone").addClass("is-invalid");
                $("#dropzone-error").text('You can Upload only one Video at a time').addClass("invalid-feedback");
                $("#dropzone-error").show();
            }else if(count_video_files + count_image_files > 10){
                $(".dropzone").addClass("is-invalid");
                $("#dropzone-error").text('You have exceeded the File Upload Limit, You can not add more files.');
                $("#dropzone-error").show();
            }else{
                $("#dropzone-error").hide();
                $(".dropzone").removeClass("is-invalid");
            }
            if(count_image_files > 4){
                var selected = false;
                var twitter_options = $("#optgroup-twitter").children();
                $(twitter_options).each(function(){
                    if($(this)[0].selected == true){
                        selected = true;
                        document.getElementById('media_page_id').options[$(this)[0].index].selected = false;
                    }
                });
                if(selected){
                    showMessage(412,'X(Twitter) does not allowed More then 4 Images. Please remove Files and select option')
                    $("#media_page_id").selectpicker('refresh');
                    mediaPreview();
                }
            }
            if(count_image_files > 8){
                var selected = false;
                var linkedin_options = $("#optgroup-linkedin").children();
                $(linkedin_options).each(function(){
                    if($(this)[0].selected == true){
                        selected = true;
                        document.getElementById('media_page_id').options[$(this)[0].index].selected = false;
                    }
                });
                if(selected){
                    showMessage(412,'Linkedin does not allowed More then 8 Images,Please remove Files and select option.')
                    $("#media_page_id").selectpicker('refresh');
                    mediaPreview();
                }
            }
            if(count_image_files > 10){
                var selected = false;
                var facebook_options = $("#optgroup-facebook").children();
                $(facebook_options).each(function(){
                    if($(this)[0].selected == true){
                        selected = true;
                        document.getElementById('media_page_id').options[$(this)[0].index].selected = false;
                    }
                });
                if(selected){
                    showMessage(412,'Facebook  does not allowed More then 10 Images,Please remove Files and select option')
                    $("#media_page_id").selectpicker('refresh');
                    mediaPreview();
                }
            }
            if(jQuery.inArray(4,selected_media_ids_of_page_id) !== -1 && images.length < 0){
                $(".dropzone").addClass("is-invalid");
                $("#dropzone-error").text('You Must have to select At least one image, If you Selected Instagram Social Media.');
                $("#dropzone-error").show();
            }
        });

    });
    // function editImagePreview(filepath, myDropzone) {
    //     var data = filepath.split(',');
    //     // callback and crossOrigin are optional
    //         // console.log("filepath",filepath);
    //     data.forEach(function(element) {
    //         let url = '{{ Storage::url(":path") }}';
    //         url = url.replace(':path',element);
    //         let mockFile = {
    //             type: 'video/mp4',
    //             filepath:url,
    //             accepted: true
    //         };
    //         // myDropzone.displayExistingFile(mockFile,url);
    //         myDropzone.emit("addedfile", mockFile);
    //         myDropzone.emit("thumbnail", mockFile,url);
    //         myDropzone.emit("complete", mockFile);
    //     });
    // }

    function mediaPreview(){
        var upload_file_count = ($('.upload_file .dz-preview.dz-complete').length)  + ($('.upload-drop-image').length);
        var thumbnail_image_count = ($('.thumbnail .dz-preview.dz-complete').length)  + ($('.thumbnail-drop-image').length);

        var media_page_id = $('#media_page_id').val();
        var caption = $('#caption').val();
        var hashtag = $('#hashtag').val();
        var upload_file = upload_file_count;
        var thumbnail = thumbnail_image_count;
         var schedule_date = $('#schedule_date').val();
         var schedule_time = $('#schedule_time').val();

        // if(caption == ""){

        //     showMessage(412,'The caption field is required.');
        //     $("#media_page_id option:selected").prop("selected", false);
        //     $('#media_page_id').selectpicker('refresh');

        //     // $('#media_page_id').selectpicker('deselectAll');
        // }else if (hashtag == "") {
        //     $("#media_page_id option:selected").prop("selected", false);
        //     $('#media_page_id').selectpicker('refresh');
             // $('#media_page_id').selectpicker('deselectAll');
            //  showMessage(412,'The hashtag field is required.');
        // }else if (upload_file <= 0) {
            // $("#media_page_id option:selected").prop("selected", false);
            // $('#media_page_id').selectpicker('refresh');
             // $('#media_page_id').selectpicker('deselectAll');
            // showMessage(412,'The upload file field is required.');

        // }else if (thumbnail <= 0) {
        //     $("#media_page_id option:selected").prop("selected", false);
        //     $('#media_page_id').selectpicker('refresh');
        //      // $('#media_page_id').selectpicker('deselectAll');
        //     showMessage(412,'The thumbnail image field is required.');
        // }else{
            var upload_file = `{!!$post->upload_file ? collect($post->upload_file) : $post->images->pluck('upload_image_file')!!}`;
            var url = "{{ route('posts.postPreview') }}";

            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                beforeSend:addOverlay,
                data: {
                    _token: '{{ csrf_token() }}',
                    media_page_id: media_page_id ?? "",
                    caption:caption,
                    hashtag:hashtag,
                    upload_file:JSON.parse(upload_file),
                    id:"{{ $post->id ?? ""}}",
                    schedule_date:schedule_date,
                    schedule_time:schedule_time,
                    thumbnail:"{{ Storage::url($post->thumbnail) ?? ""}}"
                },
                success: function(response) {
                    $('.preview-list').html(response.data);

                    removeOverlay();

                },
            });
        // }
    }
    function checkForTokenExpiry(media_page_id){
        var return_response = null;
        var media_page_id = media_page_id;
        var url = "{{ route('check.token_expiry') }}";
        $.ajax({
            type: 'POST',
            url: url,
            dataType:'json',
            async: false, // Please do not Implement this line in future.
            data: {
                _token: '{{ csrf_token() }}',
                media_page_id: media_page_id ?? "",
            },
            success: function(response) {
                return_response = response;
            },
        });
        return return_response;
    }

    function changeCaptionName(){
        // $('.caption-text').text($('#caption').val());
        $('.caption-text').html($('#caption').val().replaceAll("\n", "<br />"));
    }
    function changeHashtagName(){
        $('.hashtag-text').text($('#hashtag').val());
    }
    function changeScheduleDate(){
        var date = new Date($('#schedule_date').val());
        var date_formate = date.getDate()+' '+date.toLocaleString('en-US', { month: 'short' })+", "+date.getFullYear() ;
        $('.schedule-date-text').text(date_formate);
    }
    function changeScheduleTime(){
        var time = $('#schedule_time').val();
        // var date_formate = date.getDate()+' '+date.toLocaleString('en-US', { month: 'short' })+", "+date.getFullYear() ;
        $('.schedule-time-text').text(time);
    }
    function chanegImageAfterUpload(files){
        // console.log('into function');
        // console.log(files);
        let imagesForAppend ='';
        if(files.length > 0){
            $('.dropzone-image').hide();
            $.each(files,function(key, value){
                let is_video = value.includes('https://');
                if(is_video){
                    path = value;
                }else{
                    path = `{{ \Storage::url('${value}') }}`;
                }
                imagesForAppend += `<img src="${path}" alt="social-icon" class="thumbnail_image my-4" style="height: 120px;width:120px;">`
            });
            $("[id='imageContainer']").css('display', 'block');
            $("[id='imageContainer']").html(imagesForAppend);
        }else{
            $('.dropzone-image').show();
            $("[id='imageContainer']").html('');
            $("[id='imageContainer']").css('display', 'none');
        }
    }

    function checkCurrentTimeForSubmit(){
        var date = $('#schedule_date').val();
        var time = $('#schedule_time').val();
        if(date !== '' && time !== ''){
            var hours = Number(time.match(/^(\d+)/)[1]);
            var minutes = Number(time.match(/:(\d+)/)[1]);
            var AMPM = time.match(/\s(.*)$/)[1];
            if (AMPM == "PM" && hours < 12) hours = hours + 12;
            if (AMPM == "AM" && hours == 12) hours = hours - 12;
            var sHours = hours.toString();
            var sMinutes = minutes.toString();
            if (hours < 10) sHours = "0" + sHours;
            if (minutes < 10) sMinutes = "0" + sMinutes;
            var time1 = sHours + ":" + sMinutes;
            var from = Date.parse(date + " " + time1);
            var close = Date.parse(new Date());
            if(close > from) {
                $('#schedule_time').addClass('is-invalid');
                $('#error-time').css('display','block');
                $('#error-time').text('The time must be greater then now');
                $('#error-time').addClass('invalid-feedback');
                $("input[type=submit], input[type=button], button[type=submit]").prop("disabled",
                "disabled");
                return false;
            }else{
                $('#schedule_time').removeClass('is-invalid');
                $('#error-time').removeClass('invalid-feedback');
                $("input[type=submit], input[type=button], button[type=submit]").prop("disabled",
                false);
                    $('#error-time').html('');
                return true;
            }
        }
        return true;
    }

    // function checkImageValidation(){
    //     var count_video_files = 0;
    //     var count_image_files = 0;
    //     $.each(images,function(key,value){
    //         let fileExtension = value.split('.').pop();
    //         if(fileExtension == 'mov' || fileExtension == 'mp4'){
    //             count_video_files++;
    //         }else if(fileExtension == 'jpg' || fileExtension == 'jpeg' ||fileExtension == 'webp' ||fileExtension == 'png' ){
    //             count_image_files++;
    //         }
    //     });
    //     if(count_video_files >= 1 && count_image_files >= 1){
    //         $(".dropzone").addClass("is-invalid");
    //         $("#dropzone-error").text('You can not upload video when you select one or more images, You need to Remove Videos Or select only One Video and remove all the images').addClass("invalid-feedback");
    //         $("#dropzone-error").show();
    //         return false;
    //     }else if(count_video_files > 1){
    //         $(".dropzone").addClass("is-invalid");
    //         $("#dropzone-error").text('You can Upload only one Video at a time').addClass("invalid-feedback");
    //         $("#dropzone-error").show();
    //         return false;
    //     }else if(count_video_files + count_image_files > 10){
    //         $(".dropzone").addClass("is-invalid");
    //         $("#dropzone-error").text('You have exceeded the File Upload Limit, You can not add more files.');
    //         $("#dropzone-error").show();
    //         return false;
    //     }else{
    //         $("#dropzone-error").hide();
    //         $(".dropzone").removeClass("is-invalid");
    //         return true;
    //     }
    //     if(count_image_files > 4){
    //         var selected = false;
    //         var twitter_options = $("#optgroup-twitter").children();
    //         $(twitter_options).each(function(){
    //             if($(this)[0].selected == true){
    //                 selected = true;
    //                 // document.getElementById('media_page_id').options[$(this)[0].index].selected = false;
    //             }
    //         });
    //         if(selected){
    //             $(".dropzone").addClass("is-invalid");
    //             $("#dropzone-error").text('Twitter does not allowed More then 4 Images. Please remove Files and select option').addClass("invalid-feedback");
    //             $("#dropzone-error").show();
    //             return false;
    //         }
    //     }
    //     if(count_image_files > 8){
    //         var selected = false;
    //         var linkedin_options = $("#optgroup-linkedin").children();
    //         $(linkedin_options).each(function(){
    //             if($(this)[0].selected == true){
    //                 selected = true;
    //                 // document.getElementById('media_page_id').options[$(this)[0].index].selected = false;
    //             }
    //         });
    //         if(selected){
    //             $(".dropzone").addClass("is-invalid");
    //             $("#dropzone-error").text('Linkedin does not allowed More then 8 Images,Please remove Files and select option.').addClass("invalid-feedback");
    //             $("#dropzone-error").show();
    //             return false;
    //         }
    //     }
    //     if(count_image_files > 10){
    //         var selected = false;
    //         var facebook_options = $("#optgroup-facebook").children();
    //         $(facebook_options).each(function(){
    //             if($(this)[0].selected == true){
    //                 selected = true;
    //                 // document.getElementById('media_page_id').options[$(this)[0].index].selected = false;
    //             }
    //         });
    //         if(selected){
    //             $(".dropzone").addClass("is-invalid");
    //             $("#dropzone-error").text('Facebook & Instagram does not allowed More then 10 Images,Please remove Files and select option.').addClass("invalid-feedback");
    //             $("#dropzone-error").show();
    //             return false;
    //         }
    //     }
    //     return true;
    // }
</script>

@endpush
