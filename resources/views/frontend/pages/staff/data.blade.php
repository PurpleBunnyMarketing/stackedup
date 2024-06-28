@if($staff->count() > 0)
@foreach($staff as $value)

<div class="col-sm-6 col-md-6 col-lg-4 col-xl-3 mb-5" id="card-{{$value->id}}">
    <!--begin::Iconbox-->
    <div class="card card-custom mb-8 mb-lg-0 staff-user-card">
        <div class="card-body">
            <div class="mb-4 mr-6">
                <div class="staff-image">
                    <img src="{{ Storage::exists($value->profile_photo) ? Storage::url($value->profile_photo) : asset('frontend/images/default_profile.jpg')}}"
                        alt="staff-user">
                </div>
            </div>
            <div class="d-flex flex-column">
                <div style="height: 120px;">
                    <p class="font-italic text-dark-75 font-size-h3 mb-10">{{$value->full_name ?? "" }}</p>
                    <p class="text-dark font-weight-bold font-size-h5 mb-4">Last LoggedIn:
                        {{ $value->last_loggedIn == null ? 'Not Yet' :
                        \Carbon\Carbon::parse($value->last_loggedIn)->format('M d, Y \a\t h:i A')}}
                    </p>
                    {{-- <p class="text-dark font-weight-bold font-size-h5 mb-4">{{ $value->last_loggedIn }}</p> --}}
                </div>

                <div class="d-flex align-items-center flex-wrap" style="justify-content: space-evenly;gap:10px;">
                    <a href="{{  route('staff.show',$value->id)}}" class="btn common-btn font-weight-bold ">View</a>
                    <a href="{{  route('staff.edit',$value->id)}}"
                        class="btn edit-btn common-btn font-weight-bold ">Edit</a>
                    <button data-id="{{$value->id}}"
                        class="btn  common-btn btn-danger font-weight-bold btn-delete">Delete</button>
                    <div id="status_button_container">
                        @if( auth()->user()->type == 'company' || auth()->user()->type == 'admin')
                            @if ($value->is_active == 'y')
                            <button data-user_id="{{$value->id}}" data-status_value="n"
                                class="btn common-btn btn-primary font-weight-bold btn_change_status">Deactivate</button>
                            @else
                            <button data-user_id="{{$value->id}}" data-status_value="y"
                                class="btn common-btn btn-success font-weight-bold btn_change_status">Activate</button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Iconbox-->
</div>

@endforeach
@else
<div class="text-center not-found">
    {{-- <img src="{{ asset('frontend/images/not-found.png') }} " alt="not-found"> --}}
    <h3>No Staff Found</h3>
</div>
@endif
