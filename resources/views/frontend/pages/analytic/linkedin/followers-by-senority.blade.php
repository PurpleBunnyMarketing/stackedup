@forelse($followerCountsBySeniority as $key => $fgc)
<div class="bar-one my-4">
    <div class="d-flex justify-content-between pr-2">
        <span class="">{{ $linkedin_seniority_data[$key] ?? '' }}</span>
        <span class="" style="font-weight:900;">{{ $fgc }}</span>
    </div>
    <div class="progress mt-0">
        <div class="progress-bar" role="progressbar" style="width: {{ 100*$fgc/$tfcl }}%"
            aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
</div>
@empty
<div class="d-flex align-items-center justify-content-center no-data">
    <h2 class="text-bold-700 mt-1"> No data found </h2>
</div>
@endforelse