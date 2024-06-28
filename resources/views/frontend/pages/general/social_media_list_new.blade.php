@extends('frontend.layouts.app')

@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<!--begin::Entry-->
	<div class="d-flex flex-column-fluid">
		<!--begin::Container-->
		<div class="container">
			<!--begin::Dashboard-->
			<!--begin::Row-->
			<div class="row justify-content-center mt-10">
				<div class="col-xl-5 col-lg-6 col-md-6 col-sm-8 col-11 bg-light rounded">
					<!--begin::Tiles Widget 1-->
					<div class="card card-custom gutter-b card-stretch bg-transparent shadow-none">
						<!--begin::Header-->
						<div class="card-header border-0 pt-5">
							<div class="card-title">
								<div class="card-label">
									<div class="font-weight-bolder">SOCIAL MEDIA</div>
								</div>
							</div>
						</div>
						<!--end::Header-->
						<!--begin::Body-->
						<div class="card-body d-flex flex-column px-0">
							<!--begin::Items-->
							<div class="flex-grow-1 card-spacer-x">
								<!--begin::Item-->
								@foreach($media as $value)
								<div class="d-flex align-items-center justify-content-between mb-10">
									<div class="d-flex align-items-center mr-2">
										<div class="symbol symbol-50 symbol-light mr-3 flex-shrink-0">
											<div class="symbol-label">
												<img src="{{ $value->website_image_url ?? "" }}" alt="" class="h-50" />
											</div>
										</div>
										<div>
											<p class="font-size-h6 text-dark-75 font-weight-bolder mb-0">{{$value->name
												?? "" }}</p>
										</div>
									</div>

									<a class="font-weight-bold  custom-link font-size-h5 link-btn"
										data-social-name="{{ $value->name}}" href="javascript:;" {{
										in_array($value->id,$social_media_detail) ? "disabled=disabled" : "" }}
										>Link</a>
								</div>
								@endforeach

							</div>
							<!--end::Items-->
						</div>
						<!--end::Body-->
					</div>
					<!--end::Tiles Widget 1-->
				</div>
			</div>
			<!--end::Row-->

			<!--end::Dashboard-->
		</div>
		<!--end::Container-->
	</div>
	<!--end::Entry-->
</div>
<!--end::Content-->

@endsection

@push('extra-js-scripts')
<script type="text/javascript">
	$(document).ready(function() {
       $(document).on('click','.link-btn',function(){
       		// var is_subscribe = "{{ $user->is_subscribe ?? "" }}";
       		// if(is_subscribe == 'n'){
       		// 	 var route = "{{ route('package-list') }}";
       		// 	  window.location.href = route;
       		// }else{
       			if($(this).is('[disabled=disabled]')){
       				showMessage(200,"Already Link with social media");
       			 
       			}else if($(this).attr('data-social-name') == 'Twitter'){
       				window.location = "{{ route('auth.twitter') }}";
       			}else if($(this).attr('data-social-name') == 'Facebook'){
       				window.location = "{{ route('auth.facebook') }}";
       			}else if($(this).attr('data-social-name') == 'Google My Business'){
       				toastr.error('We are working on it!');
       				// window.location = "{{ route('auth.google') }}";
       			}
       			else if($(this).attr('data-social-name') == 'Instagram'){
       				toastr.error('We are working on it!');
       				// window.location = "{{ route('auth.google') }}";
       			}
       			else if($(this).attr('data-social-name') == 'Linkedin'){
       				window.location = "{{ route('auth.linkedin') }}";
       			}
       		// }
       });
    });
   
    
</script>

@endpush