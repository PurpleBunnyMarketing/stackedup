@extends('auth.layouts.app')

@section('content')
<!--begin::Main-->
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
                    <!--begin::Forgot-->
                    <div class="login-form">
                        <div class="text-center mb-10 mb-lg-20">
                            <h3 class="">Forgotten Password ?</h3>
                            <p class="text-muted font-weight-bold">Enter your email to reset your password</p>
                        </div>
                        <!--begin::Form-->
                       <form  name="frmforgot" id="frmforgot" action="{{ route('password.email') }}" method="POST">
                            @csrf
                             @if (session('status'))
                                        <div class="alert alert-success alert-important">
                                            {{ session('status')}}
                                        </div>
                                    @endif
                            <div class="form-group py-3 border-bottom mb-10">
                                <input class="form-control" type="email" placeholder="Email Address" name="email" autocomplete="off" id="email" />
                            @if ($errors->has('email'))
                                <div class="error-block input-error">
                                       <span class="error" style="color: #F64E60">{{ $errors->first('email') ?? "" }}</span>
                                </div>
                            @endif
                            </div>
                            <div class="form-group d-flex flex-wrap flex-center">
                                <button id="kt_login_forgot_submit" class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-2">Submit</button>
                                <a href="{{route('login')}}" id="kt_login_forgot_cancel" class="btn btn-light-primary font-weight-bold px-9 py-4 my-3 mx-2">Cancel</a>
                            </div>
                        </form>
                        <!--end::Form-->
                    </div>
                    <!--end::Forgot-->>
                    
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
<!--end::Main-->
@endsection

@push('extra-js-scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $("#frmforgot").validate({
            rules: {
                email:{
                    required:true,
                    // not_empty:true,
                    maxlength:100,
                    // valid_email: true,
                },
            },
            messages: {
                email:{
                    required:"@lang('validation.required',['attribute'=>'email address'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'email address'])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'email address','max'=>100])",
                    valid_email:"@lang('validation.email',['attribute'=>'email address'])",
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

        $('#frmforgot').submit(function(){
            if( $(this).valid() ){

                return true;
            }
            else{
                return false;
            }
        });
    });
</script>
@endpush
