@forelse($followerCountsByGeoCountry as $key => $fgc)
{{-- <div class="bar-one">
    <span class="year">{{ $linkedin_geo_data[$key] ?? '' }}</span>
    <span class="year1">{{ $fgc }}</span>
    <div class="progress">
        <div class="progress-bar" role="progressbar" style="width: {{ 100*$fgc/$tfc }}%"
            aria-valuenow="{{ $fgc }}" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
</div> --}}
<div class="bar-one my-4">
    <div class="d-flex justify-content-between pr-2">
        <div class="">{{ $linkedin_geo_data[$key] ?? '' }}</div>
        <div class="" style="font-weight: 900;">{{ $fgc }}</div>
    </div>
    <div class="progress mt-0">
        <div class="progress-bar" role="progressbar" style="width: {{ 100*$fgc/$tfc }}%"
            aria-valuenow="{{ $fgc }}" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
</div>

@empty
<div class="d-flex align-items-center justify-content-center no-data">
    <h2 class="text-bold-700 mt-1"> No data found </h2>
</div>
{{-- <h2
    class="text-bold-700 mt-1 py-12 h-100 d-flex align-items-center justify-content-center"> No
    data found </h2> --}}
@endforelse