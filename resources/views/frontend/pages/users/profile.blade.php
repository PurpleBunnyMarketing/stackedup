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
                    <h2 class="font-weight-bold my-2 mr-5">MY PROFILE</h2>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Heading-->
            </div>
            @if ($expiry_date !== '')
            <div class="d-flex align-items-center">
                <h3 class="mb-0">Subscription Plan:</h3>
                <p class="mb-0 ml-2 font-size-h5" style="color:#E77F01 ">{{$expiry_date}}
                </p>
            </div>
            @endif

            <!--end::Info-->
        </div>
    </div>
    <!--end::Subheader-->
    <div class=" container">
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Card-->
                <div class="card card-custom gutter-b example example-compact">
                    <!--begin::Form-->
                    <form class="form">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="profile-image">
                                        <img src="{{ Storage::exists($user->profile_photo) ? Storage::url($user->profile_photo) : asset('frontend/images/default_profile.jpg')}}"
                                            alt="profile-image">
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group row">
                                        <div class="col-sm-6 col-lg-6">
                                            <label>Full Name:</label>
                                            <p class="font-size-h5 font-weight-bolder">{{
                                                $user->full_name ?? "" }}</p>
                                        </div>
                                        <div class="col-sm-6 col-lg-6">
                                            <label>Phone Number:</label>
                                            <p class="font-size-h5 font-weight-bolder">{{
                                                $user->phone_code.''.$user->mobile_no }}</p>
                                        </div>
                                        <div class="col-sm-6 col-lg-6">
                                            <label>Email Address:</label>
                                            <p class="font-size-h5 font-weight-bolder">{{ $user->email
                                                ?? ""}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--begin::Button-->
                            <a href="{{ route('edit-profile') }}"
                                class="btn common-btn secondary-color d-inline-block font-weight-bold py-3 px-6 mr-2 mb-8">
                                Edit Profile
                            </a>
                            <!--end::Button-->

                            <div class="my-profile-tab">
                                <div class="card-toolbar">
                                    <ul class="nav nav-tabs nav-bold nav-tabs-line">
                                        @foreach($data as $media)
                                        @if(count($media->mediaPages) > 0)
                                        <li class="nav-item">
                                            <a class="nav-link @if($loop->first) active @endif" data-toggle="tab"
                                                href="#media-{{ $media->id ?? "" }}" style="width: min-content">
                                                {{-- <span class="nav-icon"><i class="flaticon2-chat-1"></i></span> --}}
                                                <img src="{{$media->website_image_url}}" height="15" width="15">
                                                <span class="nav-text" style="text-wrap: nowrap;">{{ $media->name ??
                                                    ""}}</span>
                                            </a>
                                        </li>
                                        @endif
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="profile-card-body">
                                    <div class="tab-content">
                                        @foreach($data as $media)
                                        <div class="tab-pane fade @if($loop->first) show active @endif "
                                            id="media-{{ $media->id ?? "" }}" role="tabpanel"
                                            aria-labelledby="media-{{ $media->id ?? "" }}" style="width:fit-content">
                                            <div
                                                class="profil-link d-flex justify-content-center align-items-start flex-column flex-wrap">
                                                @foreach($media->mediaPages as $page)
                                                <div class="d-flex align-items-center">
                                                    <div class="social-icon">
                                                        <img src="{{ $page->image_url ?? asset('frontend/images/default_profile.jpg') }}"
                                                            alt="social-icon">
                                                    </div>
                                                    <a href="javascript:;" id="media_page"
                                                        class="btn social-link font-weight-bold font-size-h3 mr-2 mb-2"
                                                        data-page_id={{$page->custom_id}}>
                                                        {{-- <i class="flaticon2-bell-4"></i> --}}
                                                        {{ $page->page_name ?? ""}}
                                                        <i class="ml-2 flaticon-delete text-danger"></i>
                                                    </a>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- --}}
                        </div>
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Card-->
            </div>
        </div>
    </div>
</div>
<!--end::Content-->
@endsection

@push('extra-js-scripts')

<script>
    $(document).ready(function() {
        // $(document).on('click','',function(e){
        //     console.log($(this).data('page_id'));
        // });
        $(document).on("click", "#media_page", function (e) {
            e.preventDefault();
            var action = "{{route('user_media.delete')}}";
            var page_id = $(this).data('page_id');
            var element = $(this);
            Swal.fire({
                title: "Are you sure?",
                text: "You won't to delete this Media!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: action,
                        type: "DELETE",
                        data:{
                            _token:"{{csrf_token()}}",
                            page_id:page_id,
                        },
                        dataType: "json",
                        success: function (success) {
                            // console.log(success);
                            element.remove();
                        },
                    });
                    Swal.fire({
                        title: "Deleted!",
                        icon: "success",
                        text: "Media Access Revoked.",
                        showConfirmButton: false,
                        timer: 1500,
                    });
                }
            });
        });
    });
</script>

@endpush