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
                    <h2 class="font-weight-bold my-2 mr-5">Contact Us</h2>
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
    <!--begin::Container-->
    <div class="container">
        <!--begin::Card-->
        <div class="card card-custom gutter-b">
            <div class="card-header">
                <div class="card-title">
                    <h5 class="card-label">Send us your enquiries</h5>
                </div>
            </div>
            <!--begin::Form-->
            <form class="contact-form" action="{{ route('contact') }}" id="frmContactForm" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-5">
                            <div class="mb-12">
                                <p class="font-size-h6">Email:</p>
                                <p class="font-size-h6 font-weight-bolder d-flex align-items-center">
                                    <i class="flaticon2-new-email text-dark mr-4"></i>{{$settings['support_email'] ??
                                    ""}}

                                </p>
                            </div>
                            <div class="mb-12">
                                <p class="font-size-h6">Conatct No:</p>
                                <p class="font-size-h6 font-weight-bolder d-flex align-items-center">
                                    <i class="fas fa-phone-alt text-dark mr-4"></i>{{$settings['support_contact'] ??
                                    ""}}
                                </p>
                            </div>
                            <div class="mb-12">
                                <p class="font-size-h6">Adress:</p>
                                <p class="font-size-h6 font-weight-bolder d-flex align-items-center">
                                    <i class="fa fa-map-marker-alt text-dark mr-4"></i> {{$settings['address'] ?? "" }}
                                </p>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <!--begin::Input-->
                            <div class="form-group">
                                <label>Full Name{!! $mend_sign !!}:</label>
                                <input type="text"
                                    class="form-control form-control-lg @error('full_name') is-invalid @enderror"
                                    placeholder="Enter your full name" name="full_name" id="full_name"
                                    value="{{ old('full_name') }}" />
                                @if ($errors->has('full_name'))
                                <span class="help-block">
                                    <strong class="form-text">{{ $errors->first('full_name') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!--end::Input-->

                            <!--begin::Input-->
                            <div class="form-group">
                                <label>Email address{!! $mend_sign !!}</label>
                                <input type="email" id="email_address" name="email_address"
                                    class="form-control form-control-lg @error('email_address') is-invalid @enderror"
                                    placeholder="Enter email" value="{{old('email_address')}}" />
                                @if ($errors->has('email_address'))
                                <span class="help-block">
                                    <strong class="form-text">{{ $errors->first('email_address') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!--end::Input-->
                            <!--begin::Input-->
                            <div class="form-group">
                                <label for="exampleTextarea">Message{!! $mend_sign !!}</label>
                                <textarea class="form-control form-control-lg @error('message') is-invalid @enderror"
                                    id="message" name="message" placeholder="Enter Message"
                                    rows="3">{{old('message')}}</textarea>
                                @if ($errors->has('message'))
                                <span class="help-block">
                                    <strong class="form-text">{{ $errors->first('message') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!--end::Input-->
                            <!--begin::File Input Screen Shot-->
                            <div class="form-group">
                                <label for="exampleTextarea">Upload Image: </label>
                                <input type="file" id="file" name="file"
                                    class="form-control form-control-lg @error('file') is-invalid @enderror"
                                    accept=".png,.jpg,.jpeg" />
                                <span class="text-small text-muted">If you find any issue, Please attach the
                                    screen
                                    shot of the issue.</span>
                                <div class="text-small text-muted">Format Only PNG,JPG,JPEG.</div>
                                @if ($errors->has('file'))
                                <span class="help-block">
                                    <strong class="form-text">{{ $errors->first('file') }}</strong>
                                </span>
                                @endif
                            </div>
                            <!--end::Input-->
                        </div>
                        <div class="col-xl-1"></div>
                    </div>
                </div>
                <!--begin::Actions-->
                <div class="card-footer">
                    <div class="row">
                        <div class="col-xl-5"></div>
                        <div class="col-xl-6">
                            <button type="submit" class="btn btn-primary font-weight-bold mr-2">Submit</button>
                            {{-- <button type="reset" class="btn btn-clean font-weight-bold">Cancel</button> --}}
                        </div>
                        <div class="col-xl-1"></div>
                    </div>
                </div>
                <!--end::Actions-->
            </form>
            <!--end::Form-->
        </div>
        <!--end::Card-->
    </div>
    <!--end::Container-->
</div>
<!--end::Content-->
@endsection

@push('extra-js-scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $("#frmContactForm").validate({
            rules: {
                email_address:{
                    required: true,
                    not_empty: true,
                    maxlength: 150,
                    email: true,
                    valid_email: true,
                },
                full_name: {
                    required: true,
                    maxlength:40,
                    minlength:2,
                    not_empty: true,
                    lettersonly:true,
                },
                message: {
                    required: true,
                    not_empty: true,
                    minlength: 10,
                },
                file:{
                    required: false,
                    extension:'jpg|png|jpeg'
                }
            },
            messages: {
                email_address:{
                    required:"@lang('validation.required',['attribute'=> 'Email Address'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=> 'Email Address'])",
                    maxlength:"@lang('validation.max.string',['attribute'=> 'Email Address','max'=>150])",
                    valid_email:"@lang('validation.email',['attribute'=> 'Email Address'])",
                    email:"@lang('validation.email',['attribute'=> 'Email Address'])",
                },
                full_name:{
                    required:"@lang('validation.required',['attribute'=>'Full Name'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=>'Full Name'])",
                    maxlength:"@lang('validation.max.string',['attribute'=>'Full Name','max'=>40])",
                    minlength:"@lang('validation.min.string',['attribute'=>'Full Name','min'=>2])",
                    lettersonly:"@lang('validation.alpha',['attribute'=>'Full Name'])"
                },
                message: {
                    required:"@lang('validation.required',['attribute'=>'message'])",
                    not_empty:"@lang('validation.not_empty',['attribute'=> 'message'])",
                    minlength:"@lang('validation.min.string',['attribute'=> 'message','min'=>10])",
                },
                file:{
                    extension:"@lang('validation.mimes',['attribute'=> 'File','values'=>'.png,.jpg,.jpeg'])"
                }
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
        
        $('#frmContactForm').submit(function(){
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