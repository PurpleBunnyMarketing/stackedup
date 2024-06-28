@extends('frontend.layouts.app')
{{-- @push('breadcrumb')
{!! Breadcrumbs::render('publisher_payment_create') !!}
@endpush --}}
@push('extra-css-styles')
<style>
    .stripe-element-container {
        padding-top: .55rem;
        padding-bottom: .50rem;
    }

    /* Blue outline on focus */
    .StripeElement {
        display: block;
        width: 100%;
        height: calc(1.5em + 1.3rem + 2px);
        padding: 0.65rem 1rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #3F4254;
        background-color: #ffffff;
        background-clip: padding-box;
        border: 1px solid #E4E6EF;
        border-radius: 0.42rem;
        -webkit-box-shadow: none;
        box-shadow: none;
        -webkit-transition: border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
        transition: border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, -webkit-box-shadow
    }

    .StripeElement--focus {
        color: #3F4254;
        background-color: #ffffff;
        border-color: #69b3ff;
        outline: 0;
    }
</style>
@endpush

@section('content')
<div class="content d-flex flex-column flex-column-fluid add-post-page" id="kt_content">
    <div class="container ">
        <div class="row">
            <div class="col-md-6 mx-lg-auto mx-md-auto">
                <div class="subheader py-2 py-lg-12 subheader-transparent d-flex justify-content-center"
                    id="kt_subheader">
                    <!--begin::Title-->
                    <h2 class="font-weight-bold my-2">Checkout</h2>
                    <!--end::Title-->
                </div>
                <div class="add-post-form">
                    <form id="card-details-form" class="add-to-card-form" method="POST"
                        action="{{ route('media-page.payment-success') }}">
                        @csrf
                        <input type="hidden" name="package_id" id="package_id" value="{{ $package->id ?? "" }}">
                        <input type="hidden" name="qty" id="qty" value="{{ $channelCount ?? "" }}">
                        <input type="hidden" name="plan" id="plan" value="{{ $plan ?? "" }}">
                        <input type="hidden" name="couponCode" id="couponCode" value="{{ $couponCode ?? ""}}">
                        <input type="hidden" name="faceCount" id="faceCount" value="{{ $facePageCount ?? 0 }}">
                        <input type="hidden" name="linkedCount" id="linkedCount" value="{{ $linkedPageCount ?? 0 }}">
                        <input type="hidden" name="twitCount" id="twitCount" value="{{ $twitterPageCount ?? 0 }}">
                        <input type="hidden" name="instaCount" id="instaCount" value="{{ $instaPageCount ?? 0 }}">
                        <input type="hidden" name="googleCount" id="googleCount" value="{{ $googlePageCount ?? 0 }}">
                        {{-- <input type="hidden" name="googleAnalyticsCount" id="googleAnalyticsCount" value="{{ $googleAnalyticsPageCount ?? 0 }}">
                        <input type="hidden" name="googleAdsCount" id="googleAdsCount" value="{{ $googleAdsPageCount ?? 0 }}"> --}}

                        @if($message)
                        <b style="color: #E77F01; padding-bottom:10px">{{ $message }}</b>
                        @endif
                        <div class="cardDetail" style="margin-top : 15px;">
                            <div class="card">
                                <div class="card-body">
                                    <label for="cc-number" class="control-label">CARD
                                        NUMBER</label>
                                    <div class="form-group">
                                        <div id="card-number"></div>
                                        <div style="color: red" class="mt-2" id="card-number-errors"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="cc-exp" class="control-label">CARD
                                                EXPIRY</label>
                                            <div class="form-group">
                                                <div id="card-exp"> </div>
                                                <div style="color: red" class="mt-2" id="card-expiry-errors"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="cc-cvc" class="control-label">CARD
                                                CVC</label>
                                            <div class="form-group">
                                                <div id="card-cvc"> </div>
                                                <div style="color: red" class="mt-2" id="cvc-errors"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="custom_stripe_id"></span>
                                    <div style="color: red" id="card-errors"></div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-lg form-control"
                                            id="addCardButton">Checkout(PAY)
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@push('extra-js-scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    var rules, messages;
        const stripe = Stripe("{{config('utility.STRIPE_KEY')}}");

        var elements = stripe.elements();
        var style = {
            base: {
                iconColor: "#0000FF",
                color: "#32325d",
                fontSize: '16px'

            }
        };
        var card = elements.create('cardNumber', {
            'placeholder': 'Enter Card Number',
            style: style
        });
        // Card expiry
        var exp = elements.create('cardExpiry', {
            style: style
        });

        // CVC
        var cvc = elements.create('cardCvc', {
            'placeholder': 'CVC',
            style: style
        });
        card.mount('#card-number');
        exp.mount('#card-exp');
        cvc.mount('#card-cvc');
        card.on('change', function(event) {

            if (event.complete) {
                $('#card-number-errors').empty()

            } else if (event.error) {
                $("#addCardButton").removeAttr("disabled");
                showError('card-number-errors',event.error.message);
            }
        });
        cvc.on('change', function(event) {
            if (event.complete) {
                $('#cvc-errors').empty()

            } else if (event.error) {
                $("#addCardButton").removeAttr("disabled");
                showError('cvc-errors',event.error.message);
            }
            // validationError(event);
        });
        exp.on('change', function(event) {
            // validationError(event);

            if (event.complete) {
                $('#card-expiry-errors').empty()

            } else if (event.error) {
                $("#addCardButton").removeAttr("disabled");
                showError('card-expiry-errors',event.error.message);
            }
        });
        var validationError = function(event) {
            // console.log(event);
            if (event.complete) {
                $('#card-errors').empty()

            } else if (event.error) {
                $("#addCardButton").removeAttr("disabled");
                showError(event.error.message);
            }
        }
        var showError = function(id,errorMsgText) {
            let displayError = document.getElementById(id);
            displayError.textContent = errorMsgText;
        };
        $("#addCardButton").click(function(e) {
            $("#addCardButton").attr("disabled", "disabled");
            if( $('#card-expiry-errors').text() == ''  &&  $('#card-number-errors').text() == '' &&  $('#cvc-errors').text() == ''){
                stripe.createToken(card)
                .then(function(result) {
                    // console.log(result.token.id);
                    let stripePaymentMethodId = result.token.id;
                    if (stripePaymentMethodId) {
                        addOverlay();
                        $("#card-number").append("<input type='hidden' name='payMentId' value='" +stripePaymentMethodId +"'/>");
                        $("#card-number").append("<input type='hidden' name='payment_type' value='card' />");
                        $("#addCardButton").attr("disabled", "disabled");
                        let form = document.getElementById("card-details-form");
                        form.submit();
                    } else {
                        // console.log("someting is wrong" + result);
                    }
                });
            }
            else{
                $("#addCardButton").removeAttr("disabled");
            }
            removeOverlay();
            return false;
        });

        $(document).ready(function() {
            $(".cardDetail").show();

        });
</script>
@endpush
