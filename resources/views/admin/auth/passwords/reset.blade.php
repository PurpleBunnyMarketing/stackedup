@extends('admin.auth.layouts.app')

@section('content')
<div class="d-flex flex-column flex-root">
    <div class="login login-4 login-signin-on d-flex flex-row-fluid">
        <div class="d-flex flex-center flex-row-fluid bgi-size-cover bgi-position-top bgi-no-repeat" style="background-image: url({{ asset('assets/media/bg/bg-3.jpg') }});">
            <div class="login-form text-center p-7 position-relative overflow-hidden" style="max-width: 450px;width:100%">
                <div class="d-flex flex-center mb-15">
                    <a href="#">
                    <img class="img-fluid" style="max-width: 240px; max-height: 60px;" src="{{ asset($sitesetting['site_logo']) }}" alt="{{ env('APP_NAME') }}" /></a>
                </div>
                <div class="login-signin">
                    <div class="mb-20">
                        <h3>Reset Password</h3>
                        <div class="text-muted font-weight-bold">Enter your email and new password.</div>
                    </div>
                    <form class="form-horizontal" id="resetpwdForm" role="form" method="POST" action="{{ url('/admin/password/reset') }}" class="form">
                        {{ csrf_field() }}


                        <input type="hidden" name="token" value="{{ $token }}">
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} text-left">
                                <input id="email" type="email" class="form-control h-auto py-4 px-8" name="email" value="{{ old('email', $email) }}" required autocomplete="email" placeholder="Email" autofocus>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback text-left">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                                @if ($errors->has('email'))
                                        <div class="error-block input-error" style="margin-bottom:15px;">
                                            <strong><span class="x-small  error" style="color: #F64E60">{{ $errors->first('email') ?? "" }}</span></strong>
                                    </div>
                                @endif
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} text-left">
                            {{-- <input id="password" type="password" class="form-control h-auto py-4 px-8" name="password" placeholder="Password"required autocomplete="new-password">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif --}}
                                <div class="input-group">
                                    <input type="password"
                                        class="form-control h-auto py-4 px-8 @error('password') is-invalid @enderror"
                                        id="password" name="password" placeholder="Password" data-error-container="#password-error" autocomplete="new-password"/>
                                        <div class="input-group-append"><span class="input-group-text" ><i class="far fa-eye" id="togglePassword" ></i></span></div>

                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback text-left">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                 </div>
                                 <span id="password-error"></span>
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }} text-left">
                                {{-- <input id="password-confirm" type="password" class="form-control h-auto py-4 px-8" placeholder="Confirm Password" name="password_confirmation" required autocomplete="new-password">
                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif --}}
                                <div class="input-group">
                                    <input type="password"
                                        class="form-control h-auto py-4 px-8 @error('password') is-invalid @enderror"
                                        id="password-confirm" name="password_confirmation" placeholder="Confirm Password" data-error-container="#confirm-password-error"/>
                                        <div class="input-group-append"><span class="input-group-text" ><i class="far fa-eye" id="togglePasswordConfirmation" ></i></span></div>

                                    @if ($errors->has('password_confirmation'))
                                        <span class="invalid-feedback text-left">
                                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                                        </span>
                                    @endif
                                 </div>
                                 <span id="confirm-password-error"></span>
                        </div>
                        <button type="submit" class="btn btn-pill btn-primary font-weight-bold px-9  my-3 mx-4 opacity-90 px-15 py-3">
                            Reset Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('extra-js-scripts')
<script src="{{ asset('admin/js/custom_validations.js') }}"></script>
<script>
$(document).ready(function () {
    $("#resetpwdForm").validate({
        rules: {
            email: {
                required: true,
                maxlength: 80,
                email: true,
                valid_email: true,
            },
            password: {
                required: true,
                minlength: 8,
            },
            password_confirmation: {
                required: true,
                minlength: 8,
                equalTo : "#password"
            },
        },
        messages: {
            email: {
                required: "@lang('validation.required',['attribute'=>'email address'])",
                maxlength:"@lang('validation.max.string',['attribute'=>'email address','max'=>80])",
                email:"@lang('validation.email',['attribute'=>'email address'])",
                valid_email:"@lang('validation.email',['attribute'=>'email address'])",
            },
            password: {
                required:"@lang('validation.required',['attribute'=>'password'])",
                minlength:"@lang('validation.min.string',['attribute'=>'password','min'=>8])",
            },
            password_confirmation: {
                required:"@lang('validation.required',['attribute'=>'password confirmation'])",
                minlength:"@lang('validation.min.string',['attribute'=>'password confirmation','min'=>8])",
                equalTo:"Please enter the same value as password",
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

    $('#resetpwdForm').submit(function () {
        if ($(this).valid()) { 
            $("input[type=submit], input[type=button], button[type=submit]").prop("disabled", "disabled");
            return true;
        } else {
            return false;
        }
    });
    const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');
            togglePassword.addEventListener('click', function (e) {
                // toggle the type attribute
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                // toggle the eye slash icon
                this.classList.toggle('fa-eye-slash');
            });
            const togglePasswordConfirmation = document.querySelector('#togglePasswordConfirmation');
            const passwordConfirmation = document.querySelector('#password-confirm');
            togglePasswordConfirmation.addEventListener('click', function (e) {
                // toggle the type attribute
                const type = passwordConfirmation.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordConfirmation.setAttribute('type', type);
                // toggle the eye slash icon
                this.classList.toggle('fa-eye-slash');
            });
});
</script>
@endpush
