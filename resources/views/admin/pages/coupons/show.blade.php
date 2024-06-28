@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('coupons_show', $coupon->id) !!}
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
                        <label class="control-label col-md-3"><strong>Coupon Name : </strong></label>
                        <div class="col-md-9">{{ $coupon->name ?? '--' }}</div>
                    </div>
                </div>
                <div class="form-group clearfix">
                    <div class="row">
                        <label class="control-label col-md-3"><strong>Amount Off : </strong></label>
                        <div class="col-md-9">{{ $coupon->amountOff ?? '--' }}</div>
                    </div>
                </div>
                <div class="form-group clearfix">
                    <div class="row">
                        <label class="control-label col-md-3"><strong>Percentage Off : </strong></label>
                        <div class="col-md-9">{!! $coupon->percentageOff ?? '--' !!}</div>
                    </div>
                </div>
               
            <div class="card-footer">
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Back</a>
            </div>
       
        <!--end::Form-->
    </div>
</div>
@endsection


