@extends('admin.layouts.app')

@push('breadcrumb')
    {!! Breadcrumbs::render('coupons_create') !!}
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
            <form id="frmAddCoupon" method="POST" action="{{ route('admin.coupons.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    {{-- Coupon Name --}}
                    <div class="form-group">
                        <label for="coupon_name">{!! $mend_sign !!}Coupon Name:</label><br>
                        <input class="form-control @error('coupon_name') is-invalid @enderror" type="text"
                            id="coupon_name" name="coupon_name">
                        @if ($errors->has('coupon_name'))
                            <span class="help-block">
                                <strong class="form-text">{{ $errors->first('coupon_name') }}</strong>
                            </span>
                        @endif
                    </div>

                    {{-- percentage_off --}}
                    <div class="form-group">
                        <label for="percentage_off">Percentage Off:</label>
                        <input type="number" class="form-control @error('percentage_off') is-invalid @enderror"
                            id="percentage_off" name="percentage_off" value="{{ old('percentage_off') }}"
                            placeholder="Enter percentage off" autocomplete="percentage_off" tabindex="0" autofocus
                            min="0" oninput="validity.valid||(value='');" />
                        @if ($errors->has('percentage_off'))
                            <span class="help-block">
                                <strong class="form-text">{{ $errors->first('percentage_off') }}</strong>
                            </span>
                        @endif
                    </div>

                    {{-- amount_off --}}
                    <div class="form-group">
                        <label for="amount_off">Amount Off:</label>
                        <input type="number" class="form-control @error('amount_off') is-invalid @enderror" id="amount_off"
                            name="amount_off" value="{{ old('amount_off') }}" placeholder="Enter amount off"
                            autocomplete="amount_off" tabindex="0" autofocus min="0"
                            oninput="validity.valid||(value='');" />
                        @if ($errors->has('amount_off'))
                            <span class="help-block">
                                <strong class="form-text">{{ $errors->first('amount_off') }}</strong>
                            </span>
                        @endif
                    </div>

                    <span id="percentage_off-error" class="text-danger" style="display: none;">Either Amount Off or Percentage Off is required.</span>

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
        $(document).ready(function() {
            var percentage = $('#percentage_off');
            var amount_off = $('#amount_off');

            percentage.on('change', function() {
                amount_off.val(null);
            });

            amount_off.on('change', function() {
                percentage.val(null);
            });

            function check_amount_percentage() {
                if (percentage.val() == '' || amount_off.val() == '') {
                    if (percentage.val() == '' && amount_off.val() == '') {
                        $('#percentage_off-error').show();
                        $('#amount_off-error').show();
                        return false;
                    }
                    return true;
                } else {
                    $('#percentage_off-error').hide();
                    $('#amount_off-error').hide();
                    return true;
                }
            }

            $("#frmAddCoupon").validate({
                rules: {
                    coupon_name: {
                        required: true,
                        not_empty: true,
                    },
                },
                messages: {
                    coupon_name: {
                        required: "@lang('validation.required', ['attribute' => 'coupon name'])",
                        not_empty: "@lang('validation.not_empty', ['attribute' => 'coupon name'])",
                    },
                },
                errorClass: 'invalid-feedback',
                errorElement: 'span',
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                    $(element).siblings('label').addClass('text-danger'); // For Label
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                    $(element).siblings('label').removeClass('text-danger'); // For Label
                },
                errorPlacement: function(error, element) {
                    if (element.attr("data-error-container")) {
                        error.appendTo(element.attr("data-error-container"));
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
            $('#frmAddCoupon').submit(function() {
                if ($(this).valid() && check_amount_percentage()) {
                    addOverlay();
                    $("input[type=submit], input[type=button], button[type=submit]").prop("disabled",
                        "disabled");
                    return true;
                } else {
                    return false;
                }
            });
        });
    </script>
@endpush
