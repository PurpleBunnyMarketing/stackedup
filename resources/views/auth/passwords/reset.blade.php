@extends('auth.layouts.app')

@push('extra-css-styles')
<style type="text/css">
.select-validation      { border :1px solid #F64E60; }

</style>
@endpush
@section('content')
<div class="d-flex flex-column flex-root">
    <!--begin::Login-->
    <div class="login login-4 login-signin-on d-flex flex-row-fluid" id="kt_login">
        <div class="d-flex flex-center flex-row-fluid bgi-size-cover bgi-position-top bgi-no-repeat" style="background-image: url({{ asset('frontend/assets/media/bg/bg-3.jpg')}});">
            <div class="login-form custom-default-class text-center p-7 position-relative overflow-hidden">
                <!--begin::Login Header-->
                <div class="d-flex flex-center mb-15">
                    <?php $url = "password/reset/".$token."?email=".$email;?>
                    <a href="{{ url($url) }}">
                        <img src="{{ asset('frontend/assets/media/images/logo.png') }}" class="max-h-75px" alt="" />
                    </a>
                </div> 
                <!--begin::Login forgot password form-->
                <div class="login-forgot">
                    <div class="mb-20">
                        <h3>Reset Password</h3>
                        <div class="text-muted font-weight-bold">Please enter the credentials to reset your password</div>
                    </div>
                     <form  name="frmreset" id="frmreset" action="{{ route('password.update') }}" method="POST">
                        @csrf
                         <input type="hidden" name="token" value="{{ $token }}">
                         @if ($errors->has('email'))
                             <div class="error-block input-error" style="margin-bottom:15px;">
                                   <strong><span class="x-small  error" style="color: #F64E60">{{ $errors->first('email') ?? "" }}</span></strong>
                            </div>
                        @endif
                        <div class="form-group mb-10">
                            <input class="form-control h-auto py-4 px-8" type="email" name="email" id="email" placeholder="Email Address" value="{{old('email',$email ?? "") }}" readonly />
                        </div> 
                        <div class="form-group mb-10 text-left mb-5">
                            {{-- <input class="form-control h-auto py-4 px-8" type="password" placeholder="New Password" name="password" id="password" autocomplete="off" /> --}}
                            <div class="input-group">
                                <input type="password"
                                    class="form-control h-auto py-4 px-8 @error('password') is-invalid @enderror"
                                    id="password" name="password" placeholder="New Password" data-error-container="#password-error"/>
                                    <div class="input-group-append"><span class="input-group-text" ><i class="far fa-eye" id="togglePassword" ></i></span></div>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback text-left">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                             </div>
                             <span id="password-error"></span>
                        </div>
                        <div class="form-group mb-10 text-left mb-5">
                            {{-- <input class="form-control h-auto py-4 px-8" type="password" placeholder="Re-Enter New Password" name="password_confirmation" id="password_confirmation" autocomplete="off" /> --}}
                            <div class="input-group">
                                <input type="password"
                                    class="form-control h-auto py-4 px-8 @error('password') is-invalid @enderror"
                                    id="password_confirmation" name="password_confirmation" placeholder="Re-Enter New Password" data-error-container="#confirm-password-error"/>
                                    <div class="input-group-append"><span class="input-group-text" ><i class="far fa-eye" id="togglePasswordConfirmation" ></i></span></div>

                                @if ($errors->has('password_confirmation'))
                                    <span class="invalid-feedback text-left">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                             </div>
                             <span id="confirm-password-error"></span>
                        </div>
                        <div class="form-group d-flex flex-wrap flex-center mt-10">
                            <button id="kt_login_forgot_submit" class="btn common-btn font-weight-bold px-9 py-4 my-3 mx-2">Submit</button> 
                        </div>
                    </form>
                </div>
                <!--end::Login forgot password form-->
            </div>
        </div>
    </div>
    <!--end::Login-->
</div>
@endsection
@push('extra-js-scripts')
<script src="{{ asset('admin/js/custom_validations.js') }}"></script>
<script type="text/javascript">
   $(document).ready(function() {
        $("#frmreset").validate({
            rules: {
                email:{
                    required: true,
                    not_empty: true,
                    valid_email: true,
                    minlength: 6,
                    maxlength: 150,
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
                email:{
                    required:"@lang('validation.required',['attribute'=>'email address'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'email address'])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'email address','max'=>150])",
                    valid_email:"@lang('validation.email',['attribute'=>'email address'])",
                },
                password: {
                    required:"@lang('validation.required',['attribute'=>'password'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'password'])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'password','max'=>16])",
                    minlength:"@lang('validation.min.string',['attribute'=>'password','min'=>8])",
                },
                password_confirmation: {
                    required:"@lang('validation.required',['attribute'=>'password confirmation'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'password confirmation'])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'password confirmation','max'=>16])",
                    minlength:"@lang('validation.min.string',['attribute'=>'password confirmation','min'=>8])",
                    equalTo: "password and confirm password does not match.",
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

        $('#frmreset').submit(function(){
            if( $(this).valid() ){
                addOverlay();
                return true;
            }
            else{
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
            const passwordConfirmation = document.querySelector('#password_confirmation');
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
 

