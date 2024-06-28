@extends('auth.layouts.app')

@push('extra-css-styles')
<style type="text/css">
    .select-validation {
        border: 1px solid #F64E60;
    }
</style>
@endpush
@section('content')
<div class="d-flex flex-column flex-root">
    <!--begin::Login-->
    <div class="login login-4 login-signin-on d-flex flex-row-fluid" id="kt_login">
        <div class="d-flex flex-center flex-row-fluid bgi-size-cover bgi-position-top bgi-no-repeat"
            style="background-image: url({{ asset('frontend/assets/media/bg/bg-3.jpg')}});">
            <div class="login-form custom-default-class text-center p-7 position-relative overflow-hidden">
                <!--begin::Login Header-->
                <div class="d-flex flex-center mb-15">
                    <a href="{{ route('register') }}">
                        <img src="{{ asset('frontend/assets/media/images/logo.png') }}" class="max-h-75px max-w-105px"
                            alt="" />
                    </a>
                </div>
                <!--end::Login Header-->
                <!--begin::Login Sign in form-->
                <div class="login-signup">
                    <div class="mb-20">

                        <h3>REGISTER</h3>
                        <div class="text-muted font-weight-bold">Enter your details to create your account</div>
                    </div>
                    <form class="signup-form" id="frmRegister" name="frmRegister" action="{{ route('register') }}"
                        method="POST" class="form-inline" autocomplete="off">
                        @csrf
                        <div class="form-group text-left mb-5">
                            <input class="form-control h-auto py-4 px-8" type="text" placeholder="Full Name"
                                name="full_name" id="full_name" autocomplete="off" />
                            @if ($errors->has('full_name'))
                            <span class="text-danger">
                                <strong class="form-text">{{ $errors->first('full_name') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group text-left mb-5">
                            <input class="form-control h-auto py-4 px-8" type="text" placeholder="Email Address"
                                name="email" id="email" autocomplete="off" />
                            @if ($errors->has('email'))
                            <span class="text-danger">
                                <strong class="form-text">{{ $errors->first('email') }}</strong>
                            </span>
                            @endif
                        </div>

                        <div class="input-group text-left mb-5 country-number">
                            <select class="form-control selectpicker country-code" id="phone_code" name="phone_code"
                                data-size="5" data-live-search="true">
                                <option value="+91">+91</option>
                                @foreach($countries as $country)
                                @if ($country == '91') @continue @endif
                                <option value="{{ '+'.$country}}">{{'+'. $country }}</option>
                                @endforeach
                            </select>
                            <input class="form-control h-auto py-4 px-8" type="number" placeholder="Mobile No"
                                id="mobile_no" name="mobile_no" min="0" oninput="validity.valid||(value='');"
                                autocomplete="off" />
                        </div>

                        @if ($errors->has('phone_code'))
                        <span class="text-danger">
                            <strong class="form-text">{{ $errors->first('phone_code') }}</strong>
                        </span>
                        @endif

                        <div class="form-group mb-5 fv-plugins-icon-container text-left mb-5">
                            <div class="input-group">
                                <input class="form-control h-auto py-4 px-8" type="password" placeholder="Password"
                                    name="password" id="password" data-error-container="#password-error"
                                    autocomplete="off" />
                                <div class="input-group-append"><span class="input-group-text"><i class="far fa-eye"
                                            id="togglePassword"></i></span></div>
                                @if ($errors->has('password'))

                                <span class="text-danger">
                                    <strong class="form-text">{{ $errors->first('password') }}</strong>
                                </span>
                                @endif
                            </div>
                            <span id="password-error"></span>
                        </div>
                        <div class="form-group mb-5 fv-plugins-icon-container text-left mb-5">
                            <div class="input-group">
                                <input class="form-control h-auto py-4 px-8" type="password"
                                    name="password_confirmation" id="password_confirmation"
                                    placeholder="Confirm Password" data-error-container="#password_confirmation-error"
                                    autocomplete="off" />
                                <div class="input-group-append"><span class="input-group-text"><i class="far fa-eye"
                                            id="ctogglePassword"></i></span></div>
                                @if ($errors->has('password_confirmation'))

                                <span class="text-danger">
                                    <strong class="form-text">{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                                @endif
                            </div>
                            <span id="password_confirmation-error"></span>

                        </div>
                        <div class="form-group text-left mb-5">
                            <input class="form-control h-auto py-4 px-8" type="text" placeholder="Company Name"
                                name="company_name" id="company_name" autocomplete="off" />
                            @if ($errors->has('company_name'))
                            <span class="text-danger">
                                <strong class="form-text">{{ $errors->first('company_name') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group text-left mb-5">
                            <input class="form-control h-auto py-4 px-8" type="text" placeholder="Verified ABN."
                                name="abn" id="abn" autocomplete="off" />
                            @if ($errors->has('abn'))
                            <span class="text-danger">
                                <strong class="form-text">{{ $errors->first('abn') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group text-left mb-5">
                            <input class="form-control h-auto py-4 px-8" type="text" placeholder="Company Address"
                                name="company_address" id="company_address" autocomplete="off" />
                            @if ($errors->has('company_address'))
                            <span class="text-danger">
                                <strong class="form-text">{{ $errors->first('company_address') }}</strong>
                            </span>
                            @endif
                        </div>
                        <div class="form-group mb-5 text-left">
                            <div class="checkbox-inline">
                                <span></span>Already have an account?
                                <a href="{{ route('login') }}"
                                    class="primary-color font-weight-bold ml-1">Login</a>.</label>
                            </div>
                            <div class="form-text text-muted text-center"></div>
                        </div>
                        <div class="form-group d-flex flex-wrap flex-center mt-10">
                            <button type="submit" id="kt_login_signup_submit"
                                class="btn common-btn font-weight-bold px-9 py-4 my-3 mx-2">REGISTER</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Login-->
</div>
@endsection
@push('extra-js-scripts')
<script src="{{ asset('admin/js/custom_validations.js') }}"></script>



<script>
    $('#email').val(' '); 
    $('#password').val(' '); // one space - if this is empty/null it will autopopulate regardless of on load event
    window.addEventListener('load', () => {
      $('#email').val(''); // empty string
      $('#password').val(''); 
    });
    $(document).ready(function () {

    $('#frmRegister').on('focus', 'input[type=number]', function (e) {
      $(this).on('wheel.disableScroll', function (e) {
        e.preventDefault()
      })
    })
    $('#frmRegister').on('blur', 'input[type=number]', function (e) {
      $(this).off('wheel.disableScroll')
    })

    $('#mobile_no').keypress(function (e) {    

        var charCode = (e.which) ? e.which : event.keyCode    

        if (String.fromCharCode(charCode).match(/[^0-9]/g))    

            return false;                        

    });
    $('body').on('change', '#phone_code', function(){
        $('#phone_code').valid();
    });

    $("#frmRegister").validate({
        rules: {
        ignore: [],
            full_name:{
                required:true,
                not_empty:true,
                maxlength:40,
                minlength:2,
                pattern: /[A-Za-z0-9]/,
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
                        type:"user",
                    
                    }
                },
                
            },
            mobile_no: {
                required: true,
                not_empty: true,
                maxlength: 10,
                minlength: 6,
                pattern: /^(\d+)(?: ?\d+)*$/,
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
            company_name: {
                required: true,
                not_empty: true,
                minlength: 5,
            },
            abn: {
                required: true,
                not_empty: true,
                digits:true,
                minlength: 11,
                maxlength: 11,
                remote: {
                    url: "{{ route('check.abn') }}",
                    type: "post",
                    data: {
                        _token: function() {
                            return "{{csrf_token()}}"
                        },
                        abn: function(){
                           return $('#abn').val();
                        },
                    }
                },
            },
            company_address: {
                required: true,
                not_empty: true,
                minlength: 5,
            },
        },
        messages: {
            full_name:{
                required:"@lang('validation.required',['attribute'=>'full name'])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'full name'])",
                maxlength:"@lang('validation.max.string',['attribute'=>'full name','max'=>40])",
                minlength:"@lang('validation.min.string',['attribute'=>'full name','min'=>2])"
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
                maxlength:"@lang('validation.max.string',['attribute'=>'mobile number','max'=>10])",
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
            company_name : {
                required:"@lang('validation.required',['attribute'=>'Company Name'])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'Company Name'])",
                minlength:"@lang('validation.min.string',['attribute'=>'Company Name','min'=> 5])",
            },
            abn : {
                required:"@lang('validation.required',['attribute'=>'ABN'])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'ABN'])",
                maxlength:"@lang('validation.max.string',['attribute'=>'ABN','max'=>11])",
                minlength:"@lang('validation.min.string',['attribute'=>'ABN','min'=>11])",
                remote:"@lang('validation.unique_abn',['attribute'=>'ABN'])",
            },
            company_address : {
                required:"@lang('validation.required',['attribute'=>'Company Address'])",
                minlength:"@lang('validation.min.string',['attribute'=>'Company Address','min'=>5])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'Company Address'])",
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
const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#password');

togglePassword.addEventListener('click', function(e) {
    // toggle the type attribute
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    // toggle the eye slash icon
    this.classList.toggle('fa-eye-slash');
});

const ctogglePassword = document.querySelector('#ctogglePassword');
const cpassword = document.querySelector('#password_confirmation');

ctogglePassword.addEventListener('click', function(e) {
    // toggle the type attribute
    const type = cpassword.getAttribute('type') === 'password' ? 'text' : 'password';
    cpassword.setAttribute('type', type);
    // toggle the eye slash icon
    this.classList.toggle('fa-eye-slash');
});
function myFunction() {
    $('.error-msg').html('');
}
</script>
@endpush