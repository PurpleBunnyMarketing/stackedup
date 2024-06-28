@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('users_show', $user->id) !!}
@endpush
@section('content')
<div class="container">
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="{{ $icon }} text-primary"></i>
                </span>
                <h3 class="card-label text-uppercase">View {{ $custom_title }}</h3>
            </div>
        </div>

        <!--begin::Form-->
      
            <div class="card-body">
                <div class="form-group clearfix">
                    <div class="row">
                        <label class="control-label col-md-3"><strong>Full Name : </strong></label>
                        <div class="col-md-9">{{ $user->full_name ?? '--' }}</div>
                    </div>
                </div>
                <div class="form-group clearfix">
                    <div class="row">
                        <label class="control-label col-md-3"><strong>Email : </strong></label>
                        <div class="col-md-9">{{ $user->email?? '--' }}</div>
                    </div>
                </div>
                <div class="form-group clearfix">
                    <div class="row">
                        <label class="control-label col-md-3"><strong>Mobile Number : </strong></label>
                        <div class="col-md-9">{{ $user->phone_code.''.$user->mobile_no ?? '--' }}</div>
                    </div>
                </div>
                <div class="form-group clearfix">
                    <div class="row">
                        <label class="control-label col-md-3"><strong>Profile Photo : </strong></label>
                        <div class="col-md-9">
                            @if(Storage::exists($user->profile_photo) ) 
                                <img src="{{ Storage::url($user->profile_photo) }}" alt="" style="width:auto; height:auto; max-width: 350px; max-height: 250px" class="img-responsive">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back</a>
            </div>
       
        <!--end::Form-->
    </div>
</div>
@endsection


