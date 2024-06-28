@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('packages_show', $package->id) !!}
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
                        <label class="control-label col-md-3"><strong>Package Type : </strong></label>
                        <div class="col-md-9">{{ $package->package_type ?? '--' }}</div>
                    </div>
                </div>
                <div class="form-group clearfix">
                    <div class="row">
                        <label class="control-label col-md-3"><strong>Amount : </strong></label>
                        <div class="col-md-9">{{ $package->amount?? '--' }}</div>
                    </div>
                </div>
                <div class="form-group clearfix">
                    <div class="row">
                        <label class="control-label col-md-3"><strong>description : </strong></label>
                        <div class="col-md-9">{!! $package->description ?? '--' !!}</div>
                    </div>
                </div>
               
            <div class="card-footer">
                <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary">Back</a>
            </div>
       
        <!--end::Form-->
    </div>
</div>
@endsection


