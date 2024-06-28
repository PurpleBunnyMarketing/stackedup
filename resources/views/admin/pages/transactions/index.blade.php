@extends('admin.layouts.app')
@push('breadcrumb')
{!! Breadcrumbs::render('transaction_list') !!}
@endpush

@push('extra-css-styles')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" />
@endpush

@section('content')
<div class="container">
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="{{$icon}} text-primary"></i>
                </span>
                <h3 class="card-label text-uppercase">{{ $custom_title }}</h3>
            </div>


            <div class="card-toolbar">
                @if (in_array('delete', $permissions))
                <a href="{{ route('admin.packages.destroy', 0) }}" name="del_select" id="del_select"
                    class="btn btn-sm btn-light-danger font-weight-bolder text-uppercase mr-2 delete_all_link">
                    <i class="far fa-trash-alt"></i> Delete Selected
                </a>
                @endif
                @if (in_array('add', $permissions))
                <a href="{{ route('admin.packages.create') }}"
                    class="btn btn-sm btn-primary font-weight-bolder text-uppercase">
                    <i class="fas fa-plus"></i>
                    Add {{ str_singular($custom_title) }}
                </a>
                @endif
            </div>

        </div>
        <div class="card-body">
            {{-- Datatable Start --}}
            <table class="table table-bordered table-hover table-checkable" id="transaction_table"
                style="margin-top: 13px !important"></table>
            {{-- Datatable End --}}
        </div>
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
    $(document).ready(function() {
        // datatable
        oTable = $('#transaction_table').DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.transactions.listing') }}",
                data: {
                    columnsDef: ['subscription_id','user_name','email', 'type','date','amount','status'],
                },
            },
            columns: [  
                // { data: 'checkbox' },          
                {
                    data: 'subscription_id'
                },
                {
                    data: 'user_name'
                },
                {
                    data: 'email'
                },
                {
                    data: 'type'
                },
                {
                    data: 'date_time'
                },
                {
                    data: 'amount'
                },
                {
                    data: 'status'
                },
                // {
                //     data: 'action',
                //     responsivePriority: -1
                // },
            ],
            columnDefs: [
                 // { targets: 0, title: "<center><input type='checkbox' class='all_select'></center>", orderable: false },
                {
                    targets: 0,
                    title: 'Subscription ID',
                    orderable: false
                },
                {
                    targets: 1,
                    title: 'User Name',
                    orderable: false
                },
                {
                    targets: 2,
                    title: 'Email',
                    orderable: false
                },
                {
                    targets: 3,
                    title: 'Subscription Type',
                    orderable: false
                },
                {
                    targets: 4,
                    title: 'Transaction Date & Time',
                    orderable: false
                },
                {
                    targets: 5,
                    title: 'Amount',
                    orderable: false
                },
                {
                    targets: 6,
                    title: 'Status',
                    orderable: false
                },
            ],
            order: [
               
            ],
            lengthMenu: [
                [10, 20, 50, 100],
                [10, 20, 50, 100]
            ],
            pageLength: 10,
        });
    });
</script>
@endpush