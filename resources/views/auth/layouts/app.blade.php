<html lang="en">
	<!--begin::Head-->
	<head><base href="">
		<meta charset="utf-8" />
		
		 <title>{{ !empty($title) ? $title : '' }} | {{ config('app.name', 'Stackedup ') }} </title>
		<meta name="description" content="Updates and statistics" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
		
		<meta name="msapplication-TileColor" content="#ffffff">
		<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
		<meta name="theme-color" content="#ffffff">
		@include('auth.layouts.includes.css')
	    @stack('extra-css-styles')

	    @include('auth.layouts.includes.favicon')
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="quick-panel-right demo-panel-right offcanvas-right header-fixed subheader-enabled page-loading">
		@yield('content')
		{{-- Include Falsh Message --}}
        @include('flash::message')
		
		@include('auth.layouts.includes.js')
		<!--end::Page Scripts-->

	<script src="{{ asset('admin/js/custom.js') }}" type="text/javascript"></script>
	{{-- <script src="{{ asset('admin/js/custom_validations.js') }}" type="text/javascript"></script> --}}
			@stack('extra-js-scripts')
	</body>
	<!--end::Body-->
</html>