@extends('frontend.layouts.app')

@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content"> 
	<!--begin::Entry-->
	<div class="d-flex flex-column-fluid">
		<!--begin::Container-->
		<div class="container"> 
			<!--begin::Dashboard--> 
			<!-- enter code here  -->
				<!--begin::Card-->
				<div class="card card-custom gutter-b mt-10">
					<div class="card-header">
						<div class="card-title">
							<span class="card-icon">
								<i class="flaticon2-chart text-primary"></i>
							</span>
							<h3 class="card-label">SUBSCRIPTION</h3>
						</div>
					</div>
					<div class="card-body">
						<div class="row justify-content-center my-20 subscription-cards">
							<!--begin: Pricing-->
							@foreach($packages as $package)
							<div class="col-sm-6 col-md-5 col-xxl-4">
								<div class="bg-gray-300 pt-25 pt-md-20 pb-15 px-5 h-100 text-center">
									<!--begin::Icon-->
									<div class="d-flex flex-center position-relative mb-6">
										<h2>{{ $package->package_type == 'monthly' ? "Monthly" : "Yearly"}}</h2>
									</div>
									<!--end::Icon-->
									<!--begin::Content-->
									<span class="font-size-h1 d-block d-block font-weight-boldest text-dark-75 py-2">${{ $package->amount ?? "" }}</span>
									<h4 class="font-size-h6 d-block d-block font-weight-bold mb-7 text-dark-50">Duration: {{ $package->package_type == 'monthly' ? "1 Month" : "1 Year"}}</h4>
									<p class="mb-15 d-flex flex-column"> {!! $package->description !!}</p>
									<a href="{{route('buy-now',$package->id)}}" class="btn common-btn text-uppercase font-weight-bolder px-15 py-3">BUY NOW</a>
									<!--end::Content-->
								</div>
							</div>
							@endforeach
							<!--end: Pricing-->
							
						</div>
					</div>
				</div>
				<!--end::Card--> 


			<!-- end code  -->
			<!--end::Dashboard-->
		</div>
		<!--end::Container-->
	</div>
	<!--end::Entry-->
</div>
<!--end::Content--> 

@endsection
