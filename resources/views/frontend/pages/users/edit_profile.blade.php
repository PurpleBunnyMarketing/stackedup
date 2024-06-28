@extends('frontend.layouts.app')

@section('content')

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
<!--begin::Subheader-->
<div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
	<div
		class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
		<!--begin::Info-->
		<div class="d-flex align-items-center flex-wrap mr-1">
			<!--begin::Heading-->
			<div class="d-flex  ">
				<!--begin::Title-->
				<h2 class="font-weight-bold my-2 mr-5">EDIT PROFILE</h2> 
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
	<div class="row">
		<div class="col-lg-12">
			<!--begin::Card-->
			<div class="card card-custom gutter-b example example-compact"> 
				<!--begin::Form--> 
				<form id="frmEditProfile" class="form" name="frmEditProfile" action="{{ route('upadte-profile') }}" method="POST" enctype="multipart/form-data">
                        @csrf
					<div class="card-body">
						<div class="row">
							<div class="col-md-3">
								{{-- <div class="profile-image">
									<img src="{{ Storage::exists($user->profile_photo) ? Storage::url($user->profile_photo) : asset('frontend/images/default_profile.jpg')}}" alt="profile-image">
								</div> --}}
								
								<div class="image-input image-input-outline" id="kt_profile_avatar" style="background-image: url({{ Storage::exists($user->profile_photo) ? Storage::url($user->profile_photo) : asset('frontend/images/default_profile.jpg')}})">
									<div class="image-input-wrapper" id="kt_profile_avatar" style="background-image: url({{ Storage::exists($user->profile_photo) ? Storage::url($user->profile_photo) : asset('frontend/images/default_profile.jpg')}})"></div>
								<label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">
										<i class="fa fa-pen icon-sm text-muted"></i>
										<input type="file" name="profile_photo" id="profile_photo" value="123" accept=".png, .jpg, .jpeg" />
										<input type="hidden" name="profile_avatar_remove" />
									</label>
									<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Cancel avatar">
										<i class="ki ki-bold-close icon-xs text-muted"></i>
									</span>
									<span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="Remove avatar">
										<i class="ki ki-bold-close icon-xs text-muted"></i>
									</span>
								</div> 
							</div>

							<div class="col-md-9">
								<div class="form-group row">
									<div class="col-sm-6 col-lg-6 mb-6">
										<label>Full Name:</label>
										<input type="text" class="form-control" value="{{ $user->full_name ?? "" }}" placeholder="Enter full name" name="full_name" id="full_name" />
										
									</div>
									 <div class="col-sm-6 col-lg-6 mb-6">
										<label>Phone Number:</label>
										<div class="input-group mb-5">                          
											<select class="form-control selectpicker country-code" id="phone_code" name="phone_code" disabled>
												<option selected> {{ $user->phone_code }} </option>
											</select>
											<input class="form-control h-auto px-8" type="number" placeholder="Mobile No" id="mobile_no" name="mobile_no" min="0" value="{{$user->mobile_no}}" disabled />
										</div> 
									 {{-- <input class="form-control h-auto py-4 px-8 @error('email') is-invalid @enderror" id="mobile_no" name="mobile_no" type="tel" value="{{ $user->phone_code.''.$user->mobile_no }}" placeholder="Enter Contact Number"  disabled/>
										@if ($errors->has('mobile_no'))
			                                <span class="invalid-feedback">
			                                    <strong>{{ $errors->first('mobile_no') }}</strong>
			                                </span>
			                            @endif		 --}}
									</div> 
									{{-- <div class="input-group">                          
										<select class="form-control  selectpicker country-code col-sm-3 col-lg-2 mb-1" id="phone_code" name="phone_code" disabled >
												<option selected>{{ $user->phone_code }}</option>                                  
										</select>
										<input class="form-control h-auto col-sm-1 col-lg-12 mb-1 @error('email') is-invalid @enderror" id="mobile_no" name="mobile_no" type="tel" value="{{ $user->mobile_no }}" placeholder="Enter Contact Number"  disabled/>
									</div>  --}}
									<div class="col-sm-6 col-lg-6 mb-6">
										<label>Email:</label>
										<input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ $user->email ?? "" }}" placeholder="Enter Email" disabled/>
										@if ($errors->has('email'))
			                                <span class="invalid-feedback">
			                                    <strong>{{ $errors->first('email') }}</strong>
			                                </span>
			                            @endif
									</div> 
								</div>
							</div>
						</div>

						<!--begin::Button--> 
						<button type="submit" class="btn common-btn secondary-color d-inline-block font-weight-bold py-3 px-6 mr-2 mb-8"  >
							Update Profile
						</button>
						<!--end::Button--> 
					
					</div> 
				</form>
				<!--end::Form-->
			</div>
			<!--end::Card--> 
		</div>
	</div>
</div>
</div>
<!--end::Content-->

@endsection

@push('extra-js-scripts')
<script type="text/javascript">
	$(document).ready(function() {
        $("#frmEditProfile").validate({
            rules: {
                ignore: [],
                full_name:{
                    required:true,
                    not_empty:true,
                    maxlength:30
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
	            phone_code:{
                    required:true,
                    not_empty:true,
                    maxlength:30
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
	                    }
	                },
	            },
                profile_photo:{
                    extension:'jpg|png|jpeg'
                },
            },
            messages: {
                full_name:{
                    required:"@lang('validation.required',['attribute'=>'full name'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'full name'])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'full name','max'=>30])"
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
                profile_photo:{
                    extension:"@lang('validation.mimetypes',['attribute'=>'profile photo','value'=>'jpg|png|jpeg'])"
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
        
        $('#frmEditProfile').submit(function(){
            if( $(this).valid() ){
                addOverlay();
                return true;
            }
            else{
                removeOverlay();
                return false;
            }
        });

		$(".btn-xs").click(function(){
			var remove = $(this).attr("data-action");
			if(remove == 'remove')
			{
				// console.log('hello');
				$("#kt_profile_avatar").removeAttr( "style" );
				$('#kt_profile_avatar').css('background-image', 'url("frontend/images/default_profile.jpg")');
				
			}
		});
    });
   
    
</script>

@endpush