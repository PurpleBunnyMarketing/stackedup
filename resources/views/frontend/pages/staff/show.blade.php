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
                    <h2 class="font-weight-bold my-2 mr-5">Staff Member Details</h2>
                    <!--end::Title-->
                    <!--begin::Breadcrumb-->
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Heading-->
            </div>
            <!--end::Info-->
        </div>
    </div>

    <!--end::Subheader-->
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Card-->
                <div class="card card-custom gutter-b example example-compact">
                    <!--begin::Form-->
                    <form class="form">
                        <div class="card-body">


                            <div class="form-group row">
                                <div class="col-md-3">
                                    <div class="profile-image">
                                        <img src="{{ $staff->profile_photo ? Storage::url($staff->profile_photo) : asset('frontend/images/default_profile.jpg')}}"
                                            alt="No Profile picture available" class="img img-responsive" width="150px"
                                            height="150px">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6 col-lg-6">
                                    <label>Full Name:</label>
                                    <p class="font-size-h5 font-weight-bolder">{{ $staff->full_name ?? "" }}</p>
                                </div>
                                <div class="col-md-6 col-lg-6">
                                    <label>Contact Number:</label>
                                    <p class="font-size-h5 font-weight-bolder">{{
                                        ($staff->phone_code.''.$staff->mobile_no) ?? ""}}</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6 col-lg-6">
                                    <label>Email:</label>
                                    <p class="font-size-h5 font-weight-bolder">{{ $staff->email ?? "" }}</p>
                                </div>
                            </div>
                            <div class="my-profile-tab">
                                <div class="card-toolbar">
                                    <ul class="nav nav-tabs nav-bold nav-tabs-line">
                                        @foreach($data as $media)
                                        <li class="nav-item">
                                            <a class="nav-link @if($loop->first) active @endif" data-toggle="tab"
                                                href="#media-{{ $media->id ?? "" }}">
                                                {{-- <span class="nav-icon"><i class="flaticon2-chat-1"></i></span> --}}
                                                {{-- <span class="nav-text">{{ $media->name ?? ""}}</span> --}}
                                                <img src="{{$media->website_image_url}}" height="15" width="15">
                                                <span class="nav-text">{{ $media->name ?? ""}}</span>
                                            </a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="profile-card-body">
                                    <div class="tab-content">
                                        @foreach($data as $media)
                                        <div class="tab-pane fade @if($loop->first) show active @endif"
                                            id="media-{{ $media->id ?? "" }}" role="tabpanel"
                                            aria-labelledby="media-{{ $media->id ?? "" }}">
                                            <div class="profil-link">
                                                @foreach($media->mediaPages as $page)
                                                <div class="d-flex align-items-center">
                                                    <div class="social-icon">
                                                        <img src="{{ $page->image_url ?? asset('frontend/images/default_profile.jpg') }}"
                                                            alt="social-icon">
                                                    </div>
                                                    <a href="javascript:void(0);"
                                                        class="btn social-link font-weight-bold font-size-h3 mr-2 mb-2">
                                                        {{ $page->page_name ?? ""}}
                                                    </a>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <a href="{{  route('staff.edit',$staff->id)}}"
                                class="btn edit-btn common-btn font-weight-bold py-3 px-6 mr-2 mr-4">Edit</a>
                            <a href="{{ route('staff.index') }}"
                                class="btn common-btn font-weight-bold py-3 px-6 mr-2 ">Back</a>
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
@endpush