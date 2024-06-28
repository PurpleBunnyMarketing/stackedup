@extends('admin.layouts.app')

@push('breadcrumb')
{!! Breadcrumbs::render('users_update', $user->id) !!}
@endpush

@section('content')
<div class="container">
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="fas fa-user-edit text-primary"></i>
                </span>
                <h3 class="card-label text-uppercase">Edit {{ $custom_title }}</h3>
            </div>
        </div>

        <!--begin::Form-->
        <form id="frmEditUser" method="POST" action="{{ route('admin.users.update', $user->custom_id) }}"
            enctype="multipart/form-data">
            @csrf
            @method('put')
            <div class="card-body">

                {{-- First Name --}}
                <div class="form-group">
                    <label for="full_name">{!!$mend_sign!!}Full Name:</label>
                    <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name"
                        name="full_name" value="{{ old('full_name') != null ? old('full_name') : $user->full_name }}"
                        placeholder="Enter Full Name" autocomplete="full_name" spellcheck="false"
                        autocapitalize="sentences" tabindex="0" autofocus />
                    @if ($errors->has('full_name'))
                    <span class="help-block">
                        <strong class="form-text">{{ $errors->first('full_name') }}</strong>
                    </span>
                    @endif
                </div>

                {{-- Email --}}
                <div class="form-group">
                    <label for="email">{!!$mend_sign!!}Email:</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                        name="email" value="{{ old('email') != null ? old('email') : $user->email }}"
                        placeholder="Enter Email" autocomplete="email" spellcheck="false" tabindex="0" />
                    @if ($errors->has('email'))
                    <span class="text-danger">
                        <strong class="form-text">{{ $errors->first('email') }}</strong>
                    </span>
                    @endif
                </div>
                {{-- Mobile Number --}}
                <div class="form-group">
                    <label for="phone_code">Phone Code</label>
                    <select class="form-control select2 @error('phone_code') is-invalid @enderror" id="phone_code"
                        name="phone_code" data-error-container="#error-phone_code">
                        {{-- <option value="">Select package type</option> --}}
                        @foreach($countries as $country)
                        <option value="{{'+'.$country }}" {{ $country==$user->phone_code ? 'selected' :
                            ""}}>{{'+'.$country}}</option>
                        @endforeach

                    </select>
                    <span id="error-phone_code"></span>
                    @if ($errors->has('phone_code'))
                    <span class="text-danger">
                        <strong class="form-text">{{ $errors->first('phone_code') }}</strong>
                    </span>
                    @endif
                </div>
                {{-- Mobile Number --}}
                <div class="form-group">
                    <label for="mobile_no">Mobile Number</label>
                    <input type="number" class="form-control @error('mobile_no') is-invalid @enderror" id="mobile_no"
                        name="mobile_no" value="{{ old('mobile_no') != null ? old('mobile_no') : $user->mobile_no }}"
                        placeholder="Enter Mobile Number" oninput="validity.valid||(value='');" />
                    @if ($errors->has('mobile_no'))
                    <span class="text-danger">
                        <strong class="form-text">{{ $errors->first('mobile_no') }}</strong>
                    </span>
                    @endif
                </div>

                {{-- Profile Photo --}}
                <div class="form-group">
                    <label for="profile_photo">Profile Photo</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="profile_photo" name="profile_photo"
                            tabindex="0" />
                        <label class="custom-file-label @error('profile_photo') is-invalid @enderror"
                            for="customFile">Choose file</label>
                        @if ($errors->has('profile_photo'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('profile_photo') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                @if ($user->profile_photo)
                <div class="symbol symbol-120 mr-5">
                    <div class="symbol-label" style="background-image:url({{ asset('storage/'.$user->profile_photo)}})">
                        {{-- Custom css added .symbol div a --}}
                        {{-- <a href="#" class="btn btn-icon btn-light btn-hover-danger remove-img"
                            id="kt_quick_user_close" style="width: 18px; height: 18px;">
                            <i class="ki ki-close icon-xs text-muted"></i>
                        </a> --}}
                    </div>
                </div>
                @endif


            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2">Update {{ $custom_title }}</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
        <!--end::Form-->
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script>
    $(document).ready(function () {
    $('#phone_code').select2({
        placeholder: "Select phone code"
    });
    // $('body').on('change', '#phone_code', function(){
    //     $('#phone_code').valid();
    // });
    $("#frmEditUser").validate({
        rules: {
            full_name: {
                required: true,
                not_empty: true,
                minlength: 3,
            },
            
            email: {
                required: true,
                maxlength: 150,
                email: true,
                valid_email: true,
                remote: {
                    url: "{{ route('admin.check.email') }}",
                    type: "post",
                    data: {
                        _token: function() {
                            return "{{csrf_token()}}"
                        },
                        type: "user",
                        id: "{{ $user->id ?? '' }}"
                    }
                },
            },
            phone_code: {
                required: true,
            },
            mobile_no: {
                required: true,
                not_empty: true,
                digits:true,
                maxlength: 10,
                minlength: 9,
                pattern: /^(\d+)(?: ?\d+)*$/,
            },
            profile_photo:{
                extension: "jpg|jpeg|png",
            },
        },
        messages: {
            full_name: {
                required: "@lang('validation.required',['attribute'=>'full name'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'full name'])",
                minlength:"@lang('validation.min.string',['attribute'=>'full name','min'=>3])",
            },
            email: {
                required: "@lang('validation.required',['attribute'=>'email'])",
                maxlength:"@lang('validation.max.string',['attribute'=>'email','max'=>80])",
                email:"@lang('validation.email',['attribute'=>'email'])",
                valid_email:"@lang('validation.email',['attribute'=>'email'])",
                remote:"@lang('validation.unique',['attribute'=>'email'])",
            },
            phone_code: {
                required: "@lang('validation.required',['attribute'=>'phone code'])",
            },
            mobile_no: {
                required:"@lang('validation.required',['attribute'=>'mobile number'])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'mobile number'])",
                maxlength:"@lang('validation.max.string',['attribute'=>'mobile number','max'=>10])",
                minlength:"@lang('validation.min.string',['attribute'=>'mobile number','min'=>9])",
                pattern:"@lang('validation.numeric',['attribute'=>'mobile number'])",
                digits:"@lang('validation.numeric',['attribute'=>'mobile number'])",
            },
            profile_photo: {
                extension:"@lang('validation.mimetypes',['attribute'=>'profile photo','value'=>'jpg|png|jpeg'])",
            },
        },
        errorClass: 'invalid-feedback',
        errorElement: 'span',
        highlight: function (element) {
            $(element).addClass('is-invalid');
            $(element).siblings('label').addClass('text-danger'); // For Label
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
            $(element).siblings('label').removeClass('text-danger');
        },
        errorPlacement: function (error, element) {
            if (element.attr("data-error-container")) {
                error.appendTo(element.attr("data-error-container"));
            } else {
                error.insertAfter(element);
            }
        }
    });
    $('#frmEditUser').submit(function () {
        if ($(this).valid()) {
            addOverlay();
            $("input[type=submit], input[type=button], button[type=submit]").prop("disabled", "disabled");
            return true;
        } else {
            return false;
        }
    });

    //remove the imaegs
    // $(".remove-img").on('click',function(e){
    //     e.preventDefault();
    //     $(this).parents(".symbol").remove();
    //     $('#frmEditUser').append('<input type="hidden" name="remove_profie_photo" id="remove_image" value="removed">');
    // });
});
</script>
@endpush