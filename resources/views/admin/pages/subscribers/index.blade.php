@extends('admin.layouts.app')

@push('breadcrumb')
{!! Breadcrumbs::render('subscribers_list') !!}
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
                    <i class="fas fa-users text-primary"></i>
                </span>
                <h3 class="card-label text-uppercase">{{ $custom_title }}</h3>
            </div>

            <div class="card-toolbar">
                @if (in_array('delete', $permissions))
                <a href="{{ route('admin.users.destroy', 0) }}" name="del_select" id="del_select"
                    class="btn btn-sm btn-light-danger font-weight-bolder text-uppercase mr-2 delete_all_link">
                    <i class="far fa-trash-alt"></i> Delete Selected
                </a>
                @endif
                @if (in_array('add', $permissions))
                <a href="{{ route('admin.users.create') }}"
                    class="btn btn-sm btn-primary font-weight-bolder text-uppercase">
                    <i class="fas fa-plus"></i>
                    Add {{ $custom_title }}
                </a>
                @endif
            </div>
        </div>
        <div class="card-body">
            {{-- Datatable Start --}}
            <table class="table table-bordered table-hover table-checkable" id="subscribers_table"
                style="margin-top: 13px !important"></table>
            {{-- Datatable End --}}
        </div>
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
    $(document).ready(function () {
        // datatable
        oTable = $('#subscribers_table').DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.subscribers.listing') }}",
                data: {
                    columnsDef: ['email','created_at'],
                },
            },
            columns: [
                { data: 'email' },
                { data: 'created_at' },
            ],
            columnDefs: [
                // Specify columns titles here...
                { targets: 0, title: 'Subscribers Email', orderable: true },
                { targets: 1, title: 'Subscribe at', orderable: true },
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