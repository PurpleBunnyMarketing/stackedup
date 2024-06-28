@extends('auth.layouts.app')

@section('content')
<!--begin::Main-->
<div class="d-flex flex-column flex-root">
    <!--begin::Login-->
    <div class="login login-4 login-signin-on d-flex flex-row-fluid" id="kt_login">
        <div class="d-flex flex-center flex-row-fluid bgi-size-cover bgi-position-top bgi-no-repeat" style="background-image: url({{ asset('frontend/assets/media/bg/bg-3.jpg')}});">
            <div class="login-form custom-default-class text-center p-7 position-relative overflow-hidden">
                <!--begin::Login Header-->
                <div class="d-flex flex-center mb-15">
                    <a href="{{ route('password.request') }}">
                        <img src="{{ asset('frontend/assets/media/images/logo.png') }}" class="max-h-75px" alt="" />
                    </a>
                </div> 
                <!--begin::Login forgot password form-->
                <div class="login-forgot">
                    <div class="mb-20">
                        <h3>FORGET PASSWORD</h3>
                        <div class="text-muted font-weight-bold">We will send you link to reset password on registered Email Id</div>
                    </div>
                     <form  name="frmforgot" id="frmforgot" action="{{ route('password.email') }}" method="POST">
                            @csrf
                        @if (session('status'))
                            <div class="alert alert-success alert-important">
                                {{ session('status')}}
                            </div>
                        @endif
                        <div class="form-group mb-10 text-left mb-5">
                            <input class="form-control h-auto py-4 px-8" type="text" placeholder="Email ID" name="email" id="email" autocomplete="off" />
                            @if ($errors->has('email'))
                                <div class="error-block input-error">
                                       <span class="error" style="color: #F64E60">{{ $errors->first('email') ?? "" }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="form-group d-flex flex-wrap flex-center mt-10">
                            <button id="kt_login_forgot_submit" class="btn common-btn font-weight-bold px-9 py-4 my-3 mx-2">SUBMIT</button> 
                            <a href="{{route('login')}}" id="kt_login_forgot_cancel" class="btn common-btn cancel-btn  font-weight-bold px-9 py-4 my-3 mx-2">CANCEL</a>
                        </div>
                    </form>
                </div>
                <!--end::Login forgot password form-->
            </div>
        </div>
    </div>
    <!--end::Login-->
</div>
<!--end::Main-->
@endsection

@push('extra-js-scripts')
<script src="{{ asset('admin/js/custom_validations.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#frmforgot").validate({
            rules: {
                email:{
                    required:true,
                    // not_empty:true,
                    maxlength:100,
                    valid_email: true,
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
        setTimeout(function() {
        $(".alert").alert('close');
        // $('.error').fadeOut('fast');
    }, 5000);
   
   });
</script>
@endpush
