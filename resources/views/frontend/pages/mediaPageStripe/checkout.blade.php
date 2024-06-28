@extends('frontend.layouts.app')
@push('extra-css-styles')
<link rel="stylesheet" href="{{ asset('assets/css/checkout.css') }}" />
@endpush
@section('content')
<div class="content d-flex flex-column flex-column-fluid add-post-page" id="kt_content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="subheader py-2 py-lg-12 subheader-transparent" id="kt_subheader">
                    <!--begin::Title-->
                    <h2 class="font-weight-bold my-2 mr-5">Checkout</h2>
                    <!--end::Title-->
                </div>
                <div class="add-post-form">
                    <form id="payment-form" method="POST">
                        <div id="payment-element">
                            <!--Stripe.js injects the Payment Element-->
                        </div>
                        <button id="submit">
                            <div class="spinner hidden" id="spinner"></div>
                            <span id="button-text">Pay now</span>
                        </button>
                        <div id="payment-message" class="hidden"></div>
                    </form>
                </div>
            </div>
            <!--  <div class="col-md-6 post-preview">
                <div class="preview-list"></div>
            </div>  -->
        </div>
    </div>
</div>

@endsection

@push('extra-js-scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    window.location.hash="no-back-button";
    window.location.hash="Again-No-back-button";//again because google chrome don't insert first hash into history
    window.onhashchange=function(){window.location.hash="no-back-button";}
    // This is your test publishable API key.
    const stripe = Stripe("{{config('utility.STRIPE_KEY')}}");
    var qty ='{!! $channelCount !!}';
    var plan ='{!! $plan !!}';
    let elements;

    initialize();
    checkStatus();

    document
        .querySelector("#payment-form")
        .addEventListener("submit", handleSubmit);

    // Fetches a payment intent and captures the client secret
    async function initialize() {
        const { clientSecret } = await fetch("{{ route('media-page.payment-success') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({qty},{plan}),
        }).then((r) => r.json());
        elements = stripe.elements({
            clientSecret
        });

        // const paymentElement = elements.create("payment");
        const paymentElement = elements.create('payment', {
            fields: {
              billingDetails: {
                address: {
                  country: "never"
                }
              }
            }
          });
        paymentElement.mount("#payment-element");
    }

    async function handleSubmit(e) {
        e.preventDefault();
        setLoading(true);

        const {
            error
        } = await stripe.confirmPayment({
            elements,
            confirmParams: {
                // Make sure to change this to your payment completion page
                return_url: "{{ route('home') }}",
                payment_method_data: {
                    billing_details: {
                        address: {
                            country: "US"
                        }
                    }
                }
            },
        });
        if (error.type === "card_error" || error.type === "validation_error") {
            showMessage(error.message);
        } else {
            showMessage("An unexpected error occured.");
        }

        setLoading(false);
    }

    // Fetches the payment intent status after payment submission
    async function checkStatus() {
        const clientSecret = new URLSearchParams(window.location.search).get(
            "payment_intent_client_secret"
        );

        if (!clientSecret) {
            return;
        }

        const {
            paymentIntent
        } = await stripe.retrievePaymentIntent(clientSecret);

        switch (paymentIntent.status) {
            case "succeeded":
                showMessage("Payment succeeded!");
                break;
            case "processing":
                showMessage("Your payment is processing.");
                break;
            case "requires_payment_method":
                showMessage("Your payment was not successful, please try again.");
                break;
            default:
                showMessage("Something went wrong.");
                break;
        }
    }

    // ------- UI helpers -------

    function showMessage(messageText) {
        const messageContainer = document.querySelector("#payment-message");

        messageContainer.classList.remove("hidden");
        messageContainer.textContent = messageText;

        setTimeout(function() {
            messageContainer.classList.add("hidden");
            messageText.textContent = "";
        }, 4000);
    }

    // Show a spinner on payment submission
    function setLoading(isLoading) {
        if (isLoading) {
            // Disable the button and show a spinner
            document.querySelector("#submit").disabled = true;
            document.querySelector("#spinner").classList.remove("hidden");
            document.querySelector("#button-text").classList.add("hidden");
        } else {
            document.querySelector("#submit").disabled = false;
            document.querySelector("#spinner").classList.add("hidden");
            document.querySelector("#button-text").classList.remove("hidden");
        }
    }
</script>
@endpush
