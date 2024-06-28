@extends('auth.layouts.app')

@section('content')

<!--begin::Main-->
<div class="d-flex flex-column flex-root">
    <!--begin::Login-->
    <div class="login login-4 login-signin-on d-flex flex-row-fluid" id="kt_login">
        <div class="d-flex flex-center flex-row-fluid bgi-size-cover bgi-position-top bgi-no-repeat"
            style="background-image: url({{ asset('frontend/assets/media/bg/bg-3.jpg') }});">
            <div class="login-form custom-default-class text-center p-7 position-relative overflow-hidden">
                <!--begin::Login Header-->
                <div class="d-flex flex-center mb-15">
                    <a href="{{ route('login') }}">
                        <img src="{{ asset('frontend/assets/media/images/logo.png') }}" class="max-h-75px max-w-105px"
                            alt="" />
                    </a>
                </div>
                <!--end::Login Header-->
                <!--begin::Login Sign in form-->
                <div class="login-signin">
                    <ul class="custom-nav nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="email-tab" data-toggle="tab" href="#email-address" role="tab"
                                aria-controls="home" aria-selected="true">EMAIL ADDRESS</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="mobile-tab" data-toggle="tab" href="#mobile" role="tab"
                                aria-controls="mobile" aria-selected="false">MOBILE NUMBER</a>
                        </li>
                    </ul>
                    <div class="custom-tab tab-content" id="myTabContent">
                        <div class="tab-pane fade {{ ($errors->has('email') || $errors->has('password')) ? 'show active' : 'show active ' }}"
                            id="email-address" role="tabpanel" aria-labelledby="email-tab">
                            <div class="mb-20">
                                <h3>Login To Your Account</h3>
                                <div class="text-muted font-weight-bold">Enter your details to login to your account:
                                </div>
                            </div>
                            <form method="POST" id="frmLogin-email" action="{{ route('login') }}" autocomplete="off">
                                @csrf

                                <div class="form-group text-left mb-5">
                                    <input required
                                        class="form-control h-auto py-4 px-8 @error('email') is-invalid @enderror"
                                        type="text" value="{{ old('email') }}" placeholder="Email Address" name="email"
                                        id="email" autocomplete="off" />
                                    <span class="invalid-email"></span>
                                    @if ($errors->has('email'))
                                    <span class="invalid-feedback" id="email-error">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                {{-- <div class="form-group mb-5 text-left">
                                    <input class="form-control h-auto py-4 px-8 " @error('password') is-invalid
                                        @enderror type="password" placeholder="Password" name="password" id="password"
                                        value="{{ old('password') }}" />
                                </div> --}}
                                <div class="form-group mb-5 fv-plugins-icon-container text-left mb-5">
                                    <div class="input-group">
                                        <input type="password"
                                            class="form-control h-auto py-4 px-8 @error('password') is-invalid @enderror"
                                            id="password" name="password" placeholder="Password"
                                            value="{{ old('password') }}" data-error-container="#password-error" />
                                        <div class="input-group-append"><span class="input-group-text"><i
                                                    class="far fa-eye" id="togglePassword"></i></span></div>
                                        <span class="invalid-password"></span>
                                        @if ($errors->has('password'))
                                        <span class="invalid-feedback text-left" id="password-error">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <span id="password-error"></span>
                                </div>
                                <div class="form-group d-flex flex-wrap justify-content-between align-items-center">
                                    <div class="checkbox-inline">
                                        <label class="checkbox m-0 text-muted">
                                            <input type="checkbox" name="remember" />
                                            <span></span>Remember me</label>
                                    </div>
                                    <a href="{{ route('password.request') }}" id="kt_login_forgot"
                                        class="text-muted text-hover-primary">Forgot Password ?</a>
                                </div>
                                <button id="kt_login_signin_submit"
                                    class="btn common-btn font-weight-bold px-9 py-4 my-3 mx-4">SIGN IN</button>
                            </form>
                            <div class="mt-10">
                                <span class="opacity-70">Don't have an account yet?</span>
                                <a href="{{ route('register') }}" id="kt_login_signup"
                                    class="primary-color  font-weight-bold">SIGN UP!</a>
                            </div>
                        </div>
                        <!-- second tab start  -->
                        <div class="tab-pane fade {{ ($errors->has('mobile_no')) ? 'show active' : '' }}" id="mobile"
                            role="tabpanel" aria-labelledby="mobile-tab">
                            <div class="mb-20">
                                <h3>Login To Your Account</h3>
                                <div class="text-muted font-weight-bold">We will send you OTP by SMS to verify your
                                    Number:</div>
                            </div>
                            <form method="POST" id="frmLogin-mobile" action="{{ route('send-otp') }}"
                                autocomplete="off">
                                @csrf
                                <div class="input-group text-left mb-5 country-number">
                                    <select class="form-control selectpicker country-code" id="phone_code"
                                        name="phone_code" data-size="5" data-live-search="true">
                                        <option value="+91">+91</option>
                                        @foreach($countries as $country)
                                        @if ($country == '91') @continue @endif
                                        <option value="{{ '+'.$country}}">{{'+'. $country }}</option>
                                        @endforeach
                                    </select>
                                    <input
                                        class="form-control h-auto py-4 px-8 @error('mobile_no') is-invalid @enderror"
                                        type="number" placeholder="Mobile No" id="mobile_no" name="mobile_no" min="0"
                                        oninput="validity.valid||(value='');" />
                                    <span class="invalid-mobile_no"></span>
                                    @if ($errors->has('mobile_no'))
                                    <span class="invalid-feedback" id="mobile_no-error">
                                        <strong>{{ $errors->first('mobile_no') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <!-- <div class="form-group text-left mb-5">
                                    <input required class="form-control h-auto py-4 px-8 @error('email') is-invalid @enderror"
                                        type="text" value="{{ old('email') }}" placeholder="Email Address" name="email"
                                        id="email" autocomplete="off" />
                                    <span class="invalid-email"></span>
                                    @if ($errors->has('email'))
                                    <span class="invalid-feedback" id="email-error">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>  -->
                                <button id="kt_login_signin_submit"
                                    class="btn common-btn font-weight-bold px-9 py-4 my-3 mx-4">SIGN IN</button>
                            </form>
                            <div class="mt-10">
                                <span class="opacity-70">Don't have an account yet?</span>
                                <a href="{{ route('register') }}" id="kt_login_signup"
                                    class="primary-color  font-weight-bold">SIGN UP!</a>
                            </div>
                        </div>
                        <!-- second tab end  -->
                    </div>

                </div>
                <!--end::Login Sign in form-->

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
    $(document).ready(function() {
        $('#frmLogin-mobile').on('focus', 'input[type=number]', function (e) {
            $(this).on('wheel.disableScroll', function (e) {
            e.preventDefault();
            });
        });
        $('#frmLogin-mobile').on('blur', 'input[type=number]', function (e) {
            $(this).off('wheel.disableScroll')
        });
        $("#frmLogin-email").validate({
            rules: {
                email: {
                    required: true,
                    maxlength: 80,
                    email: true,
                    valid_email: true,
                    remote: {
                        url: "{{ route('exist.email') }}",
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
                    }

                },
                password: {
                    required: true,
                    minlength: 8,
                },
            },
            messages: {
                email: {
                    required: "@lang('validation.required', ['attribute' => 'email address'])",
                    maxlength:"@lang('validation.max.string', ['attribute' => 'email address', 'max' => 80])",
                    email:"@lang('validation.email', ['attribute' => 'email address'])",
                    valid_email:"@lang('validation.email', ['attribute' => 'email address'])",
                    remote:"Please enter valid registered email address.",

                },
                password: {
                    required:"@lang('validation.required', ['attribute' => 'password'])",
                    minlength:"@lang('validation.min.string', ['attribute' => 'password', 'min' => 8])",
                }
            },
            errorClass: 'invalid-feedback',
            errorElement: 'span',
            highlight: function (element) {

                if(element.id == 'email' || element.id == 'password'){
                    // console.log(element.id);
                    $('#mobile-tab').removeClass('active');
                    $('#email-tab').addClass('active');
                }
                $(element).addClass('is-invalid');
                $(element).siblings('label').addClass('text-danger'); // For Label
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
                $(element).siblings('label').removeClass('text-danger'); // For Label
            },
            errorPlacement: function (error, element) {
                if (element.attr("data-error-container")) {
                    error.appendTo(element.attr("data-error-container"));
                } else {
                    error.insertAfter(element);
                }
            }
        });

        $('#frmLogin-email').submit(function(e) {
            if ($(this).valid()) {
                $("input[type=submit], input[type=button], button[type=submit]").prop("disabled",
                    "disabled");
                return true;
            } else {
                return false;
            }

        });

        $("#frmLogin-mobile").validate({
            rules: {
                mobile_no: {
                    required: true,
                    not_empty: true,
                    maxlength: 10,
                    minlength: 6,
                    pattern: /^(\d+)(?: ?\d+)*$/,
                    remote: {
                        url: "{{ route('exist.mobile_no') }}",
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
            },
            messages: {
                mobile_no: {
                    required:"@lang('validation.required',['attribute'=>'mobile number'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'mobile number'])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'mobile number','max'=>10])",
                    minlength:"@lang('validation.min.string',['attribute'=>'mobile number','min'=>6])",
                    pattern:"@lang('validation.numeric',['attribute'=>'mobile number'])",
                    remote:"Mobile number doesn't exists. Please enter valid mobile number.",
                },
                phone_code: {
                    required: "@lang('validation.required',['attribute'=>'phone code'])",
                },
            },
            errorClass: 'invalid-feedback',
            errorElement: 'span',
            highlight: function (element) {
                if(element.id == 'mobile_no'){
                    $('#email-tab').removeClass('active');
                    $('#mobile-tab').addClass('active');
                }
                $(element).addClass('is-invalid');
                $(element).siblings('label').addClass('text-danger'); // For Label
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
                $(element).siblings('label').removeClass('text-danger'); // For Label
            },
            errorPlacement: function (error, element) {
                if (element.attr("data-error-container")) {
                    error.appendTo(element.attr("data-error-container"));
                } else {
                    error.insertAfter(element);
                }
            }
        });

        $('#frmLogin-mobile').submit(function(e) {
            if ($(this).valid()) {
                $("input[type=submit], input[type=button], button[type=submit]").prop("disabled",
                    "disabled");
                return true;
            } else {
                return false;
            }

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
    });
</script>
@endpush
