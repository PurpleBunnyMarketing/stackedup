@extends('frontend.layouts.app')

@section('content')

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Heading-->
                <div class="d-flex  ">
                    <!--begin::Title-->
                    <h2 class="font-weight-bold my-2 mr-5">Staff</h2>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Heading-->
            </div>
            <!--end::Info-->
            <!--begin::Toolbar-->
            <div class="d-flex align-items-center">
                <!--begin::Button-->
                <a href="{{ route('staff.create') }}" class="btn common-btn font-weight-bold py-3 px-6 mr-2">ADD
                    Staff</a>
                <!--end::Button-->
            </div>
            <!--end::Toolbar-->
        </div>
    </div>
    <!--end::Subheader-->
    <div class="container">
        <!-- staff-user cards  -->
        <div class="row staff-list">

        </div>
        <div class="custom-pagination d-flex justify-content-center flex-wrap py-2 mr-3 pagination slider-pagination">
        </div>
    </div>
</div>


@endsection

@push('extra-js-scripts')
<script type="text/javascript">
    var page = 1;
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        page = $(this).attr('href').split('page=')[1];
        // page = $(this).attr('href').substr(($(this).attr('href').indexOf('?=')));
        loadData()
    });
	$(document).ready(function() {

        loadData();
    });
	$(document).on('click','.btn-delete',function(){
		var id = $(this).data('id');
        var route = "{{ route('staff.destroy', [':id']) }}";
        route = route.replace(':id', id);
        Swal.fire({
            title: "Are you sure?",
            text: "You won't to delete this Staff Member!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    type: 'DELETE',
                    url: route,
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',
                        page: page || 1,
                        id:id
                    },
                    success: function(response) {
                        $('#card-'+id).remove();
                        var length = $('.staff-list .card').length;
                        if(length == 0){
                            page -= 1;
                        }
                        loadData(type);
                        showMessage(200,"Staff deleted successfully.");
                    },
                });
                Swal.fire({
                    title: "Deleted!",
                    icon: "success",
                    text: "Post was deleted.",
                    showConfirmButton: false,
                    timer: 1500,
                });
            }
        });
	});
    $(document).on('click','.btn_change_status',function(){
		var staff_member_id = $(this).data('user_id');
		var status_value = $(this).data('status_value');
        console.log(staff_member_id,status_value);
        var element = $(this);
        var route = "{{ route('change.staff.status') }}";
        Swal.fire({
            title: "Are you sure?",
            text: `You want to ${status_value == 'y' ? 'Activate' : 'Deactivate'} this Staff Member!`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: `Yes, ${status_value == 'y' ? 'Activate' : 'Deactivate'} it!`,
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    type: 'POST',
                    url: route,
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_id : staff_member_id,
                        status_value : status_value,
                    },
                    success: function(response) {
                        // $(element).parent().empty();
                        var html = `<button data-user_id="${staff_member_id}" data-status_value="n"
                                class="btn common-btn btn-primary font-weight-bold btn_change_status">Deactivate</button>`;
                        if (status_value == 'n'){
                            html = `<button data-user_id="${staff_member_id}" data-status_value="y"
                                class="btn common-btn btn-success font-weight-bold btn_change_status">Activate</button>`;
                        }
                        $(element).parent().html(html);
                        Swal.fire({
                            title: "Deleted!",
                            icon: "success",
                            text: "Status change successfully.",
                            showConfirmButton: false,
                            timer: 1500,
                        });
                    },
                    error:function(jqXHR, error) {
                        var response = JSON.parse(jqXHR.responseText);
                        Swal.fire({
                            title: "Error",
                            icon: "error",
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500,
                        });
                    },
                });
            }
        });
	});
    function loadData() {
	   // console.log('page',page);
        var url = "{{ route('paginate.staff') }}";
        $.ajax({
            type: 'POST',
            url: url,
            dataType: 'json',
            beforeSend:addOverlay,
            data: {
                _token: '{{ csrf_token() }}',
                page: page || 1,
            },
            success: function(response) {
                $('.staff-list').html(response.data);
                $('.slider-pagination').html(response.pages);
                removeOverlay();
            },
        });
    }
</script>

@endpush