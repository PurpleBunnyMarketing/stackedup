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
                            <h3 class="">Reset Password</h3>
                           
                        </div>
                        <!--begin::Form-->
                        <form  name="frmreset" id="frmreset" action="{{ route('password.update') }}" method="POST">
                            @csrf
                             <input type="hidden" name="token" value="{{ $token }}">
                             @if ($errors->has('email'))
                                 <div class="error-block input-error">
                                       <strong><span class="x-small  error" style="color: #F64E60">{{ $errors->first('email') ?? "" }}</span></strong>
                                </div>
                            @endif
                            
                            <div class="form-group py-3 border-top m-0">
                                <input class="form-control" type="email" placeholder="Email" id="email" name="email" autocomplete="off" />
                            </div>
                           
                            <div class="form-group py-3 border-top m-0">
                                <input type="password" name="password" id="password" class="form-control" placeholder="New Password">
                            </div>
                            <div class="form-group py-3 border-top m-0">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm Password">
                            </div>
                            
                            <div class="form-group d-flex flex-wrap flex-center">
                                <button id="kt_login_signup_submit" class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-2">Submit</button>
                                {{-- <button id="kt_login_signup_cancel" class="btn btn-outline-primary font-weight-bold px-9 py-4 my-3 mx-2">Cancel</button> --}}
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
                    equalTo: "@lang('validation.equal_to',['attribute'=>'password confirmation','other'=>"password"])",
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
    });
</script>
@endpush
 

