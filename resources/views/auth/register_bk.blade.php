@extends('auth.layouts.app')

@push('extra-css-styles')
<style type="text/css">
.select-validation      { border :1px solid #F64E60; }

</style>
@endpush
@section('content')
<div class="d-flex flex-column flex-root">        
    <!--begin::Login-->
    <div class="login login-2 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid bg-white" id="kt_login">
        <!--begin::Aside-->
        <div class="login-aside order-2 order-lg-1 d-flex flex-column-fluid flex-lg-row-auto bgi-size-cover bgi-no-repeat p-7 p-lg-10">
            <!--begin: Aside Container-->
            <div class="d-flex flex-row-fluid flex-column justify-content-between">
                <!--begin::Aside body-->
                <div class="d-flex flex-column-fluid flex-column flex-center mt-5 mt-lg-0">
                    <a href="#" class="mb-15 text-center">
                        {{-- <img src="/metronic/theme/html/demo1/dist/assets/media/logos/logo-letter-1.png" class="max-h-75px" alt="" /> --}}
                    </a>
                    <!--begin::Signup-->
                    <div class="login-form">
                        <div class="text-center mb-10 mb-lg-20">
                            <h3 class="">Sign Up</h3>
                            <p class="text-muted font-weight-bold">Enter your details to create your account</p>
                        </div>
                        <!--begin::Form-->
                       <form class="signup-form" id="frmRegister" name="frmRegister" action="{{ route('register') }}" method="POST" class="form-inline">
                            @csrf
                            <div class="form-group py-3 m-0">
                                 <input type="text" name="full_name" id="full_name" class="form-control " placeholder="Full Name" autocomplete="off">
                                
                            </div>
                            <div class="form-group py-3 border-top m-0">
                                <input class="form-control" type="email" placeholder="Email" id="email" name="email" autocomplete="off" />
                            </div>
                             <div class="form-group py-3 border-top m-0">
                                <select class="form-control select2 @error('phone_code') is-invalid @enderror" id="phone_code" name="phone_code" data-error-container="#error-phone_code">
                                    <option value="">Select package type</option>
                                    
                                    @foreach($countries as $country)
                                        <option value="{{'+'.$country }}" >{{'+'.$country}}</option>
                                    @endforeach
                                    
                                </select>
                                <span id="error-phone_code"></span>
                                @if ($errors->has('phone_code'))
                                    <span class="text-danger">
                                        <strong class="form-text">{{ $errors->first('phone_code') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group py-3 border-top m-0">
                                <input class="form-control" type="text" placeholder="Mobile No" id="mobile_no" name="mobile_no" autocomplete="off" />
                            </div>
                            <div class="form-group py-3 border-top m-0">
                                <input class="form-control" type="password" placeholder="Password" name="password" autocomplete="off" id="password" />
                            </div>
                            <div class="form-group py-3 border-top m-0">
                                <input class="form-control" type="password" placeholder="Confirm password" 
                                name="password_confirmation" id="password_confirmation" autocomplete="off" />
                            </div>
                          
                            <div class="form-group d-flex flex-wrap flex-center">
                                <button id="kt_login_signup_submit" class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-2">Submit</button>
                               
                            </div>
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Signup-->
                    
                </div>
                <!--end::Aside body-->
                <!--begin: Aside footer for desktop-->
                <div class="d-flex flex-column-auto justify-content-between mt-15">
                    <div class="text-dark-50 font-weight-bold order-2 order-sm-1 my-2">© 2022 Metronic</div>
                    
                </div>
                <!--end: Aside footer for desktop-->
            </div>
            <!--end: Aside Container-->
        </div>
        <!--begin::Aside-->
        <!--begin::Content-->
        <div class="order-1 order-lg-2 flex-column-auto flex-lg-row-fluid d-flex flex-column p-7" style="background-image: url({{asset('frontend/images/bg-4.jpeg')}});">
            <!--begin::Content body-->
            <div class="d-flex flex-column-fluid flex-lg-center">
                <div class="d-flex flex-column justify-content-center">
                    <h3 class="display-3 font-weight-bold my-7 text-white">Welcome to Metronic!</h3>
                    <p class="font-weight-bold font-size-lg text-white opacity-80">The ultimate Bootstrap, Angular 8, React &amp; VueJS admin theme 
                    <br />framework for next generation web apps.</p>
                </div>
            </div>
            <!--end::Content body-->
        </div>
        <!--end::Content-->
    </div>
</div>
@endsection
@push('extra-js-scripts')
<script src="{{ asset('admin/js/custom_validations.js') }}"></script>
<script>
$(document).ready(function () {
     $('#phone_code').select2({
        placeholder: "Select phone code"
    });
    $('body').on('change', '#phone_code', function(){
        $('#phone_code').valid();
    });

    $("#frmRegister").validate({
        rules: {
        // ignore: [],
            full_name:{
                required:true,
                not_empty:true,
                maxlength:100
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
                        type: "user",
                        field: "mobile_no",
                        phone_code: function(){
                           return $('#phone_code').val();
                        },
                    }
                },
            },
            phone_code: {
                required: true,
            },
            password: {
                required: true,
                not_empty: true,
                minlength: 8,
                maxlength: 16,
            },
            password_confirmation: {
                required: true,
                not_empty: true,
                minlength: 8,
                maxlength: 16,
                equalTo: '#password'
            },
        },
        messages: {
            full_name:{
                required:"@lang('validation.required',['attribute'=>'full name'])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'full name'])",
                maxlength:"@lang('validation.max.string',['attribute'=>'full name','max'=>100])"
            },
            email:{
                required:"@lang('validation.required',['attribute'=>'email'])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'email'])",
                maxlength:"@lang('validation.max.string',['attribute'=>'email','max'=>150])",
                valid_email:"@lang('validation.email',['attribute'=>'email'])",
                remote:"@lang('validation.unique',['attribute'=>'email'])",
             
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
            password: {
                required:"@lang('validation.required',['attribute'=>'password'])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'password'])",
                maxlength:"@lang('validation.max.string',['attribute'=>'password','max'=>16])",
                minlength:"@lang('validation.min.string',['attribute'=>'password','min'=>8])",
            },
            password_confirmation: {
                required:"@lang('validation.required',['attribute'=>'confirm password'])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'confirm password'])",
                maxlength:"@lang('validation.max.string',['attribute'=>'confirm password','max'=>16])",
                minlength:"@lang('validation.min.string',['attribute'=>'confirm password','min'=>8])",
                equalTo: "@lang('validation.equal_to',['attribute'=>'confirm password','other'=>"password"])",
            },
        },
        errorClass: 'invalid-feedback',
        errorElement: 'span',
        highlight: function (element) {
            $(element).addClass('is-invalid');
            $(element).siblings('label').addClass('text-danger'); // For Label
             // For Label
            $(element).siblings('.select2-container').addClass('select-validation');  
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
            $(element).siblings('label').removeClass('text-danger'); // For Label
             $(element).siblings('.select2-container').removeClass('select-validation');
        },
        errorPlacement: function (error, element) {
            if (element.attr("data-error-container")) {
                error.appendTo(element.attr("data-error-container"));
            } else {
                error.insertAfter(element);
            }
        }
    });
    
    $('#frmRegister').submit(function(){
        if( $(this).valid() ){
            // addOverlay();
            return true;
        }
        else{
            // removeOverlay();
            return false;
        }
    });

});
 function myFunction() {
     
        $('.error-msg').html('');
    }
</script>
@endpush

