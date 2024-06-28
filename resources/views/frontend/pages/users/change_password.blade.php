@extends('frontend.layouts.app')

@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-12 subheader-transparent mb-6" id="kt_subheader">
        <div
            class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Heading-->
                <div class="d-flex  ">
                    <!--begin::Title-->
                    <h2 href="#" class="font-weight-bold my-2 mr-5">CHANGE PASSWORD</h2> 
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
    <div class="container">
      <div class="change-password row mb-12">
            <div class="flex-row-fluid">
                <!--begin::Card-->
                <div class="card card-custom"> 
                    <!--begin::Form-->
                    <form id="frmChangePassword" class="form" name="frmChangePassword" action="{{ route('updatePassword') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label text-alert">Current Password</label>
                                <div class="col-lg-9 col-xl-6">
                                    {{-- <input type="password" class="form-control form-control-lg mb-2" name="old_password" id="old_password"  placeholder="Current password" />  --}}
                                    <div class="input-group">
                                        <input type="password"
                                            class="form-control h-auto py-4 px-8 @error('old_password') is-invalid @enderror"
                                            id="old_password" name="old_password" placeholder="Current password" data-error-container="#old-password-error"/>
                                            <div class="input-group-append"><span class="input-group-text" ><i class="far fa-eye" id="toggleOldPassword" ></i></span></div>
   
                                        @if ($errors->has('old_password'))
                                            <span class="invalid-feedback text-left">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                     </div>
                                     <span id="old-password-error"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label text-alert">New Password</label>
                                <div class="col-lg-9 col-xl-6">
                                    {{-- <input type="password" name="password" id="password" class="form-control form-control-lg" value="" placeholder="New password" /> --}}
                                    <div class="input-group">
                                        <input type="password"
                                            class="form-control h-auto py-4 px-8 @error('password') is-invalid @enderror"
                                            id="password" name="password" placeholder="New password" data-error-container="#password-error"/>
                                            <div class="input-group-append"><span class="input-group-text" ><i class="far fa-eye" id="togglePassword" ></i></span></div>
    
                                        @if ($errors->has('password'))
                                            <span class="invalid-feedback text-left">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                     </div>
                                     <span id="password-error"></span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xl-3 col-lg-3 col-form-label text-alert">Verify Password</label>
                                <div class="col-lg-9 col-xl-6">
                                    {{-- <input type="password" id="password_confirmation" name="password_confirmation" class="form-control form-control-lg" value="" placeholder="Verify password" /> --}}
                                    <div class="input-group">
                                        <input type="password"
                                            class="form-control h-auto py-4 px-8 @error('password_confirmation') is-invalid @enderror"
                                            id="password_confirmation" name="password_confirmation" placeholder="Verify password" data-error-container="#confirm-password-error"/>
                                            <div class="input-group-append"><span class="input-group-text" ><i class="far fa-eye" id="togglePasswordConfirmation" ></i></span></div>
    
                                        @if ($errors->has('password_confirmation'))
                                            <span class="invalid-feedback text-left">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                     </div>
                                     <span id="confirm-password-error"></span>
                                </div>
                            </div> 
                            <div class="card-toolbar mt-14">
                                <button type="submit" class="btn common-btn mr-2">SAVE</button>
                               
                                <a href="{{ route('dashboard') }}" class="btn btn-secondary ">Cancel</a>
                            </div>
                        </div>
                    </form>
                    <!--end::Form-->
                </div>
            </div>
        </div> 
    </div>
</div>
<!--end::Content-->

@endsection

@push('extra-js-scripts')
<script type="text/javascript">
	$(document).ready(function() {

        $("#frmChangePassword").validate({
            ignore: [],
            rules: {
                old_password:{
                    required:true,
                    not_empty: true,
                    minlength: 8,
                    maxlength: 16,
                },
                password: {
                    required:true,
                    not_empty: true,
                    minlength: 8,
                    maxlength: 16,
                    not_equal: '#old_password'
                },
                password_confirmation: {
                    required:true,
                    not_empty: true,
                    minlength: 8,
                    maxlength: 16,
                    equalTo: '#password'
                },
            },
            messages: {
                old_password: {
                    required:"@lang('validation.required',['attribute'=>'current password'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'current password'])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'current password','max'=>16])",
                    minlength:"@lang('validation.min.string',['attribute'=>'current password','min'=>8])",
                },
                password: {
                    required:"@lang('validation.required',['attribute'=>'password'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'password'])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'password','max'=>16])",
                    minlength:"@lang('validation.min.string',['attribute'=>'password','min'=>8])",
                    not_equal: "@lang('validation.not_equal',['attribute'=>'new password','other'=>"current password"])",
                    
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
                $(element).parent().closest('.bootstrap-select').addClass('select-validation');
               
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
                $(element).siblings('label').removeClass('text-danger'); // For Label
                $(element).parent().closest('.bootstrap-select').removeClass('select-validation');
            },
            errorPlacement: function (error, element) {
                if (element.attr("data-error-container")) {
                    error.appendTo(element.attr("data-error-container"));
                } else {
                    error.insertAfter(element);
                }
            }
        });
        
        $('#frmChangePassword').submit(function(){
            if( $(this).valid() ){
                addOverlay();
                return true;
            }
            else{
                removeOverlay();
                return false;
            }
        });
            const toggleOldPassword = document.querySelector('#toggleOldPassword');
            const oldPassword = document.querySelector('#old_password');
            toggleOldPassword.addEventListener('click', function (e) {
                // toggle the type attribute
                const type = oldPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                oldPassword.setAttribute('type', type);
                // toggle the eye slash icon
                this.classList.toggle('fa-eye-slash');
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
