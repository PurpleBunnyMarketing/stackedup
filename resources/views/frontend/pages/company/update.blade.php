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
                    <h2 class="font-weight-bold my-2 mr-5">EDIT COMPANY DETAILS</h2>
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
                    <form id="frmEditCompanyDetails" class="form" name="frmEditCompanyDetails"
                        action="{{ route('company.details.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group row">
                                        <div class="col-sm-6 col-lg-6 mb-6">
                                            <label>Company Name:</label>
                                            <input type="text"
                                                class="form-control @error('company_name') is-invalid @enderror"
                                                value="{{ old('company_name') ?? $user->company_name  }}"
                                                placeholder="Enter Company Name" name="company_name"
                                                id="company_name" />
                                            @if ($errors->has('company_name'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('company_name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="col-sm-6 col-lg-6 mb-6">
                                            <label>ABN:</label>

                                            <input class="form-control h-auto px-8 @error('abn') is-invalid @enderror"
                                                type="number" placeholder="Verified ABN" id="abn" name="abn"
                                                value="{{old('abn') ?? $user->abn }}" />

                                            @if ($errors->has('mobile_no'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('mobile_no') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="col-sm-6 col-lg-6 mb-6">
                                            <label>Address:</label>
                                            <textarea id="company_address" name="company_address"
                                                class="form-control @error('company_address') is-invalid @enderror"
                                                placeholder="Enter Company Address"> {{   old('company_address') ?? $user->company_address  }}"</textarea>
                                            @if ($errors->has('company_address'))
                                            <span class="invalid-feedback">
                                                <strong>{{ $errors->first('company_address') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--begin::Button-->
                            <button type="submit"
                                class="btn common-btn secondary-color d-inline-block font-weight-bold py-3 px-6 mr-2 mb-8">
                                Edit
                            </button>
                            <a href="{{route('company.details')}}"
                                class="btn btn-secondary  d-inline-block font-weight-bold py-3 px-6 mr-2 mb-8">Cancel</a>
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

@push('extra-js-scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $("#frmEditCompanyDetails").validate({
            rules: {
                ignore: [],
                company_name: {
                required: true,
                not_empty: true,
                minlength: 5,
                },
                abn: {
                    required: true,
                    not_empty: true,
                    digits:true,
                    minlength: 11,
                    maxlength: 11,
                    remote: {
                        url: "{{ route('check.abn') }}",
                        type: "post",
                        data: {
                            _token: function() {
                                return "{{csrf_token()}}"
                            },
                            abn: function(){
                                return $('#abn').val();
                            },
                        }
                    },
                },
                company_address: {
                    required: true,
                    not_empty: true,
                    minlength: 5,
                },
            },
            messages: {
                company_name : {
                required:"@lang('validation.required',['attribute'=>'Company Name'])",
                not_empty:"@lang('validation.not_empty',['attribute'=>'Company Name'])",
                minlength:"@lang('validation.min.string',['attribute'=>'Company Name','min'=> 5])",
                },
                abn : {
                    required:"@lang('validation.required',['attribute'=>'ABN'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'ABN'])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'ABN','max'=>11])",
                    minlength:"@lang('validation.min.string',['attribute'=>'ABN','min'=>11])",
                    remote:"@lang('validation.unique_abn',['attribute'=>'ABN'])",
                },
                company_address : {
                    required:"@lang('validation.required',['attribute'=>'Company Address'])",
                    minlength:"@lang('validation.min.string',['attribute'=>'Company Address','min'=>5])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'Company Address'])",
                },
            },
            errorClass: 'invalid-feedback',
            errorElement: 'span',
            highlight: function (element) {
                $(element).addClass('is-invalid');
                $(element).siblings('label').addClass('text-danger'); // For Label
                $(element).parent().closest('.bootstrap-select').addClass('select-validation');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
                $(element).siblings('label').removeClass('text-danger'); // For Label
                $(element).parent().closest('.bootstrap-select').removeClass('select-validation');
            },
            errorPlacement: function (error, element) {
                if (element.attr("data-error-container")) {
                    error.appendTo(element.attr("data-error-container"));
                } else {
                    error.insertAfter(element);
                }
            }
        });
        
        $('#frmEditProfile').submit(function(){
            if( $(this).valid() ){
                addOverlay();
                return true;
            }
            else{
                removeOverlay();
                return false;
            }
        });
    });
</script>

@endpush