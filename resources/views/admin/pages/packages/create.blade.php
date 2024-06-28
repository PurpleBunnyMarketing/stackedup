@extends('admin.layouts.app')

@push('breadcrumb')
{!! Breadcrumbs::render('packages_create') !!}
@endpush

@section('content')
<div class="container">
    <div class="card card-custom">
        <div class="card-header">
            <div class="card-title">
                <span class="card-icon">
                    <i class="fas fa-user-plus text-primary"></i>
                </span>
                <h3 class="card-label text-uppercase">ADD {{ $custom_title }}</h3>
            </div>
        </div>

        <!--begin::Form-->
        <form id="frmAddPackager" method="POST" action="{{ route('admin.packages.store') }}"
            enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                {{-- Package Type --}}
                <div class="form-group">
                    <!--  <label for="package_type">{!!$mend_sign!!}Package Type:</label><br>
                    <input type="radio" id="typepackage_month" name="package_type" value="monthly" checked> -->
                    <input type="hidden" name="package_type" value="monthly">
                    <!--  <input type="radio" id="typepackage_year" name="package_type" value="yearly">
                    <label for="">Yearly</label><br> -->
                    @if ($errors->has('package_type'))
                    <span class="help-block">
                        <strong class="form-text">{{ $errors->first('package_type') }}</strong>
                    </span>
                    @endif
                </div>

                {{-- amount --}}
                <div class="form-group">
                    <label for="">Monthly Per Page Price</label>&nbsp;&nbsp;&nbsp;
                    <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount"
                        name="amount" value="{{ old('amount') }}" placeholder="Enter amount" autocomplete="amount"
                        tabindex="0" autofocus min="0" oninput="validity.valid||(value='');" />
                    @if ($errors->has('amount'))
                    <span class="help-block">
                        <strong class="form-text">{{ $errors->first('amount') }}</strong>
                    </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="percentageOff">{!!$mend_sign!!}PercentageOff on Annual:</label>
                    <input type="number" class="form-control @error('percentageOff') is-invalid @enderror"
                        id="percentageOff" name="percentageOff" value="{{ old('percentageOff') }}"
                        placeholder="Enter percentageOff" autocomplete="percentageOff" tabindex="0" autofocus min="0"
                        oninput="validity.valid||(value='');" />
                    @if ($errors->has('percentageOff'))
                    <span class="help-block">
                        <strong class="form-text">{{ $errors->first('percentageOff') }}</strong>
                    </span>
                    @endif
                </div>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-2 text-uppercase"> Add {{ $custom_title }}</button>
                <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary text-uppercase">Cancel</a>
            </div>
        </form>
        <!--end::Form-->
    </div>
</div>
@endsection

@push('extra-js-scripts')
<script>
    $(document).ready(function () {
    $("#frmAddPackager").validate({
        rules: {
            package_type: {
                required: true,
                not_empty: true,
            },
            amount: {
                required: true,
                not_empty: true,
            },
            percentageOff:{
                required: true,
                not_empty: true,
            }
        },
        messages: {
            package_type: {
                required: "@lang('validation.required',['attribute'=>'package type'])",
                not_empty: "@lang('validation.not_empty',['attribute'=>'package type'])",
            },
            amount: {
                required: "@lang('validation.required',['attribute'=>'amount'])",
            },
            percentageOff: {
                required: "@lang('validation.required',['attribute'=>'Percentage Off on Annual'])",
            },
        },
        errorClass: 'invalid-feedback',
        errorElement: 'span',
        highlight: function (element) {
            $(element).addClass('is-invalid');
            $(element).siblings('label').addClass('text-danger'); // For Label
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
            $(element).siblings('label').removeClass('text-danger'); // For Label
        },
        errorPlacement: function (error, element) {
            if (element.attr("data-error-container")) {
                error.appendTo(element.attr("data-error-container"));
            } else {
                error.insertAfter(element);
            }
        }
    });
    $('#frmAddPackager').submit(function () {
        if ($(this).valid()) {
            addOverlay();
            $("input[type=submit], input[type=button], button[type=submit]").prop("disabled", "disabled");
            return true;
        } else {
            return false;
        }
    });
});
</script>
@endpush