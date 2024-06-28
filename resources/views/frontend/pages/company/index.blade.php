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
                    <h2 class="font-weight-bold my-2 mr-5">Company Details</h2>
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
    <div class=" container">
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Card-->
                <div class="card card-custom gutter-b example example-compact">
                    <!--begin::Form-->
                    <form class="form">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group row">
                                        <div class="col-sm-6 col-lg-6">
                                            <label class="font-size-h6 font-weight-lighter">Compnay name:</label>
                                            <p class="font-size-h5 font-weight-bolder">{{
                                                $user->company_name ?? "" }}</p>
                                        </div>
                                        <div class="col-sm-6 col-lg-6">
                                            <label class="font-size-h6 font-weight-lighter">ABN :</label>
                                            <p class="font-size-h5 font-weight-bolder">{{
                                                $user->abn}}</p>
                                        </div>
                                        <div class="col-sm-6 col-lg-6 mt-6">
                                            <label class="font-size-h6 font-weight-lighter">Compnay Address:</label>
                                            <p class="font-size-h5 font-weight-bolder">{{ $user->company_address
                                                ?? ""}}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--begin::Button-->
                            <a href="{{ route('company.details.edit') }}"
                                class="btn common-btn secondary-color d-inline-block font-weight-bold py-3 px-6 mr-2 mb-8">
                                Edit Company Details
                            </a>
                            <!--end::Button-->
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