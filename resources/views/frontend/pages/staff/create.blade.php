@extends('frontend.layouts.app')

@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid add-post-page" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Heading-->
                <div class="d-flex  ">
                    <!--begin::Title-->
                    <h2 class="font-weight-bold my-2 mr-5">Add Staff</h2>
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
    <div class="container add-staff">
        {{-- <form action="" class="form mb-8"> --}}
            <form id="frmAddStaff" class="form mb-8" method="POST" action="{{ route('staff.store') }}"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="images" id="images" value="">
                <!-- <div class="row"> -->
                <div class="row">
                    <div class="col-md-6 mb-6">
                        <div class="dropzone dropzone-default dz-clickable" id="profile_photo" name="profile_photo">
                            <div class="dropzone-msg dz-message needsclick">
                                <div class="dropzone-image">
                                    <img src="{{ asset('frontend/assets/media/images/dropzone.svg') }}"
                                        alt="dropzone-image">
                                </div>
                            </div>
                        </div>
                        <span class="invalid-feedback" id="dropzone-error"></span>
                    </div>
                    <div class="col-md-6">
                        <div class="col-12 mb-6 ">
                            <!-- select start  -->
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <p>Please select your role:</p>
                                    <input type="radio" id="typestaff_admin" name="type" value="admin"
                                        data-error-container="#type-error">
                                    <label for="company">Admin</label><br>
                                    <input type="radio" id="typestaff_staff" name="type" value="staff"
                                        data-error-container="#type-error">
                                    <label for="css">Standard</label><br>
                                    <span id="type-error"></span>
                                </div>
                            </div>
                            <!-- select end  -->
                        </div>
                        <div class="col-12 mb-6">
                            <input type="text" class="form-control" placeholder="Enter Full Name" name="full_name"
                                id="full_name" />
                        </div>
                        <!-- </div> -->
                        <!-- <div class="row mb-6"> -->
                        <div class="col-12 mb-6">
                            <input type="email" class="form-control" placeholder="Enter Email Address" id="email"
                                name="email" autocomplete="off" />
                        </div>
                        <div class="col-12 mb-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <select class="form-control selectpicker country-code" id="phone_code"
                                        name="phone_code" data-size="5" data-live-search="true"
                                        data-error-container="#error-phone_code">
                                        <option value="+91">+91</option>
                                        @foreach($countries as $country)
                                        @if ($country == '91') @continue @endif
                                        <option value="{{ '+'.$country}}">{{ '+'.$country }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input class="form-control h-auto py-4 px-8" type="number" placeholder="Mobile No"
                                    id="mobile_no" name="mobile_no" min="0" oninput="validity.valid||(value='');" />
                                {{-- <input class="form-control h-auto py-4 px-8" type="number" placeholder="Mobile No"
                                    id="mobile_no" name="mobile_no" data-error-container="#error-phone_code" /> --}}
                            </div>
                            <!-- <input class="form-control" type="tel" placeholder="Mobile Number" id="example-tel-input"/> -->
                            <span id="error-phone_code"></span>
                            @if ($errors->has('phone_code'))
                            <span class="text-danger">
                                <strong class="form-text">{{ $errors->first('phone_code') }}</strong>
                            </span>
                            @endif
                        </div>
                        <!-- </div> -->
                        <!-- <div class="row"> -->
                        <div class="col-12 mb-6">
                            <input class="form-control" type="password" name="password" id="password"
                                placeholder="Password" autocomplete="off" i />
                        </div>
                        <div class="col-12 mb-6">
                            <!-- select start  -->
                            <div class="form-group row">
                                <div class="col-sm-12">
                                    <select class="form-control kt-bootstrap-select media_page_id" id="media_page_id"
                                        name="media_page_id[]" multiple name="select"
                                        data-error-container="#media-page-id-error">
                                        @forelse($media as $value)
                                        <optgroup label="{{ $value->name }}">
                                            @foreach($value->mediaPages as $page)
                                            <option value="{{ $page->id}}" data-media-id="{{ $value->id ?? '' }}">{{
                                                $page->page_name }}</option>
                                            @endforeach
                                        </optgroup>
                                        @empty
                                        <option value="" disabled>Please Link at One least Social media</option>
                                        @endforelse
                                    </select>
                                    <span id="media-page-id-error"></span>
                                </div>
                            </div>
                            <!-- select end  -->
                        </div>
                    </div>
                </div>
                <!-- </div> -->
                <div class="d-flex align-items-center justify-content-center">
                    <!--begin::Button-->
                    <button type="submit" class="btn common-btn secondary-color font-weight-bold py-3 px-6 mr-2">
                        Submit
                    </button>
                    <a href="{{ route('staff.index')}}" class="btn btn-secondary post-now">Cancel</a>
                    <!--end::Button-->
                </div>
            </form>

    </div>
</div>

@endsection

@push('extra-js-scripts')
<script src="{{ asset('frontend/assets/js/pages/crud/file-upload/dropzonejs.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('input[type=radio][name=type]').change(function() {
            if (this.value == 'staff') {
                $("#media_page_id").attr("disabled",false);
            }
            else if (this.value == 'company') {
                $("#media_page_id").attr("disabled",true);
            }
        });
        $('body').on('change', '#media_page_id', function(){
            $('#media_page_id').valid();
        });
        $('#media_page_id').selectpicker();
        var images = "";
        $('#profile_photo').dropzone({
            // url: "https://keenthemes.com/scripts/void.php", // Set the url for your upload script location
            paramName: "file", // The name that will be used to transfer the file
            maxFiles: 1,
            maxFilesize: 10, // MB
            addRemoveLinks: true,
            acceptedFiles: "image/*",
            url: "{{ route('staff.storeimage') }}",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            init: function(){
                this.on("maxfilesexceeded", function(file){
                    alert("No more files please!");
                    this.removeFile(file)
                });
            },
            success: function (file, response) {
                // console.log("file + ",file);
                // console.log("response + ",response);
                var imagePath = response.path;
                file.previewElement.classList.add("dz-success");
                var element = file.previewElement;
                $(element).attr('data-path',response.path);
                images = response.path;
            },
            removedfile: function(file) {
            var rem_elem = file.previewElement;
            let path = $(rem_elem).attr('data-path');
                $.ajax({
                    url: "{{ route('staff.deleteimage') }}",
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
                        rem_elem.remove();
                        images = jQuery.grep(images, function(value) {
                            return value != response.path;
                        });
                        //showMessage(r.status,r.message);
                    },
                    complete:removeOverlay
                });
            },
            error: function (file, response) {
                file.previewElement.classList.add("dz-error");
            }
        });
        $("#frmAddStaff").validate({
            ignore: [],
            rules: {
                full_name:{
                    required:true,
                    not_empty:true,
                    minlength:2,
                    maxlength:40,
                    lettersonly:true,
                },
                email:{
                    required:true,
                    not_empty:true,
                    maxlength:150,
                    valid_email: true,
                    remote: {
                        url: "{{ route('check.email') }}",
                        type: "post",
                        async: false,
                        cache: false,
                        data: {
                            _token: function() {
                                return "{{csrf_token()}}"
                            },
                            field: "email",
                            type:"user"
                        }
                    },

                },
                password: {
                    required: true,
                    not_empty: true,
                    minlength: 8,
                    maxlength: 16,
                },
                mobile_no: {
                    required: true,
                    not_empty: true,
                    maxlength: 16,
                    minlength: 6,
                    // pattern: /^(\d+)(?: ?\d+)*$/,
                    remote: {
                        url: "{{ route('check.mobile_no') }}",
                        type: "post",
                        data: {
                            _token: function() {
                                return "{{csrf_token()}}"
                            },
                            phone_code: function() {
                                return $('#phone_code').val();
                            },
                            type: "user",
                            field: "mobile_no",
                        }
                    },
                },
                phone_code: {
                    required: true,
                },
                profile_photo:{
                    required:true,
                    extension: "jpg|jpeg|png",
                },
                thumbnail_image:{
                    required:true,
                    extension: "jpg|jpeg|png",
                },
                'media_page_id[]':{
                    required:true,
                },
                "type" :{
                    required:true,
                }
            },
            messages: {
                full_name:{
                    required:"@lang('validation.required',['attribute'=>'full name'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'full name'])",
                    minlength:"@lang('validation.min.string',['attribute'=>'full name','min'=>2])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'full name','max'=>40])",
                    lettersonly:"@lang('validation.lettersonly',['attribute'=>'full name'])",

                },
                email:{
                    required:"@lang('validation.required',['attribute'=>'email'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'email'])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'email','max'=>150])",
                    valid_email:"@lang('validation.email',['attribute'=>'email'])",
                    remote:"@lang('validation.unique',['attribute'=>'email'])",
                },
                password: {
                    required:"@lang('validation.required',['attribute'=>'password'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'password'])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'password','max'=>16])",
                    minlength:"@lang('validation.min.string',['attribute'=>'password','min'=>8])",
                },
                mobile_no: {
                    required:"@lang('validation.required',['attribute'=>'mobile number'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'mobile number'])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'mobile number','max'=>16])",
                    minlength:"@lang('validation.min.string',['attribute'=>'mobile number','min'=>6])",
                    pattern:"@lang('validation.numeric',['attribute'=>'mobile number'])",
                    remote:"@lang('validation.unique',['attribute'=>'mobile number'])",
                },
                phone_code: {
                    required: "@lang('validation.required',['attribute'=>'phone code'])",
                },
                profile_photo:{
                    required:"@lang('validation.required',['attribute'=>'profile photo'])",
                    extension:"@lang('validation.mimetypes',['attribute'=>'profile photo','value'=>'jpg|png|jpeg'])",
                },

                "media_page_id[]":{
                    required:"@lang('validation.required',['attribute'=>'media page'])"
                },
                "type":{
                    required:"@lang('validation.required',['attribute'=>'role type'])"
                }
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
                removeOverlay();
                $("input[type=submit], input[type=button], button[type=submit]").prop("disabled",false);
                if (element.attr("data-error-container")) {
                    error.appendTo(element.attr("data-error-container"));
                } else {
                    error.insertAfter(element);
                }
            }
        });

        $('#frmAddStaff').submit(function () {
            $("#images").val(images);

            // var selected_profile_photo  = $('.dz-preview.dz-processing').length;

            // if(selected_profile_photo <= 0){
            //     // showMessage('412','You have to upload at least one image.')
            //     $(".dropzone").addClass("is-invalid");
            //     $("#dropzone-error").text('You have to upload at least one image.');
            //     $("#dropzone-error").show();

            //     return false;
            // }
            if ($(this).valid()) {
                addOverlay();
                $("input[type=submit], input[type=button], button[type=submit]").prop("disabled", "disabled");
                return true;
            } else {
                return false;
            }
        });
    });
</script>

@endpush
