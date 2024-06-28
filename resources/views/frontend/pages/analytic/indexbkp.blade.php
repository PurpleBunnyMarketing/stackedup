@extends('frontend.layouts.app')
@section('content')
@php
    $request = request()->query();
@endphp

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-6 py-lg-12 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">               
                <div class="d-flex  post-type">
                    <select class="form-control" id="social-media-type" >
                        <option value="" >Select Any Type</option>
                        <option value="{{ route('analytics.filter-analytic') }}?type=facebook" {{ (isset($request['type']) && $request['type'] =='facebook') ? 'selected' : ''  }}>Facebook</option>
                        <option value="{{ route('analytics.filter-analytic') }}?type=linkedin" {{ (isset($request['type']) && $request['type'] =='linkedin') ? 'selected' : ''  }}>Linkedin</option>
                        <option value="{{ route('analytics.filter-analytic') }}?type=twitter" {{ (isset($request['type']) && $request['type'] =='twitter') ? 'selected' : ''  }}>Twitter</option>
                    </select>
                </div>
            </div>
            <div class="d-flex align-items-center">            
            </div>
        </div>
    </div>

    {{-- @if((isset($request['type']) && $request['type'] == 'facebook') || !isset($request['type']))
        @include('frontend.pages.analytic.facebook-likes')
    @endif  --}}

    @if(isset($request['type']))
        @if(isset($request['type']) && $request['type'] == 'twitter')
            @include('frontend.pages.analytic.twiter-insight')
            @include('frontend.pages.analytic.twitterfeed')  
        @endif

        @if(isset($request['type']) && $request['type'] == 'linkedin' || !isset($request['type']))        
            @include('frontend.pages.analytic.linkedin-engagment')
        @endif

        @if(isset($request['type']) && $request['type'] == 'facebook' || !isset($request['type']))        
            @include('frontend.pages.analytic.facebook-likes')
            @include('frontend.pages.analytic.facebook-engagement')
            @include('frontend.pages.analytic.facebook-reach')
            {{-- @include('frontend.pages.analytic.facebook_post') --}}
        @endif
    @endif

</div>
@endsection
@push('extra-js-scripts')
<script src="{{ asset('admin/plugins/chart/apexcharts.js') }}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.4/Chart.min.js" type="text/javascript"></script>

<script>
jQuery(document).ready(function() {
    removeOverlay();
})

jQuery(document).on('change','#social-media-type',function(){
    addOverlay();
    window.location.href = $(this).val();
})

</script>
@endpush

