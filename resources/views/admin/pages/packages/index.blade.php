@extends('admin.layouts.app')
@push('breadcrumb')
{!! Breadcrumbs::render('packages_list') !!}
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
            <table class="table table-bordered table-hover table-checkable" id="packages_table"
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
        oTable = $('#packages_table').DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.packages.listing') }}",
                data: {
                    columnsDef: ['package_type','amount', 'action'],
                },
            },
            columns: [
                // { data: 'checkbox' },
                {
                    data: 'package_type'
                },
                {
                    data: 'amount'
                },
                {
                    data: 'action',
                    responsivePriority: -1
                },
            ],
            columnDefs: [
                 // { targets: 0, title: "<center><input type='checkbox' class='all_select'></center>", orderable: false },
                {
                    targets: 0,
                    title: 'Package Type',
                    orderable: true
                },
                {
                    targets: 1,
                    title: 'Amount',
                    orderable: true
                },

                {
                    targets: -1,
                    title: 'Action',
                    orderable: false
                },
            ],
            order: [
                [0, 'asc']
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
