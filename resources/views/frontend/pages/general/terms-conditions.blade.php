@extends('frontend.layouts.app')

@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<!--begin::Subheader-->
	<div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
		<div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
			<!--begin::Info-->
			<div class="d-flex align-items-center flex-wrap mr-1">
				<!--begin::Heading-->
				<div class="d-flex  ">
					<!--begin::Title-->
					<h2 class="font-weight-bold my-2 mr-5">{!!$terms_conditions->title ?? "About Us"!!}</h2>
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
	<div class="container about-us">
		<div class="bg-white p-10">
			{!!$terms_conditions->description!!}
		</div>
	</div>
</div>
<!--end::Content-->

@endsection