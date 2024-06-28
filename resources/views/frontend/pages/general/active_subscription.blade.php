@extends('frontend.layouts.app')

@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container  mt-10">
            <!--begin::Dashboard-->
            <!--begin::Row-->
            <div class="head-title-wrap">
                <h1 class="secondary-color">How many Social Media channels do you need?</h1>
                <p class="text-dark font-size-h5">Don't worry if you do not get it exact, you can add more later!</p>
            </div>
            <form id="cancelForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="media-social-card bg-light">
                            <div class="row">
                                <div class="col-sm-6">
                                    <p
                                        class="font-size-h6 text-dark-75 font-weight-bolder social-title social-title-copy">
                                        #channels</p>
                                        {{-- Facebook --}}
                                    <div class="channel-content-wrap">
                                        <div class="icon">
                                            <img src="{{ asset('frontend/assets/media/images/facebook.svg') }}">
                                        </div>
                                        <input type="text" class="focus-label media-count channel_count"
                                            value="{{Count($facebookMediaPage) ?? 0}}" id="facebook_pageCount"
                                            name="facebook_pageCount" readonly />

                                    </div>
                                    {{-- Linekdin --}}
                                    <div class="channel-content-wrap ">
                                        <div class="icon">
                                            <img src="{{ asset('frontend/assets/media/images/linkedin.svg') }}">
                                        </div>
                                        <input type="text" class="focus-label media-count channel_count"
                                            value="{{Count($linkedInMediaPage) ?? 0}}" id="linkedin_pageCount"
                                            name="linkedin_pageCount" readonly />

                                    </div>
                                    {{-- Twitter --}}
                                    <div class="channel-content-wrap ">
                                        <div class="icon">
                                            <img src="{{ asset('frontend/assets/media/images/twitter.svg') }}">
                                        </div>
                                        <input type="text" class="focus-label media-count channel_count"
                                            value="{{Count($twitterMediaPage) ?? 0}}" id="twitter_pageCount"
                                            name="twitter_pageCount" readonly />

                                    </div>
                                    {{-- Instagram --}}
                                    <div class="channel-content-wrap ">
                                        <div class="icon">
                                            <img src="{{ asset('frontend/assets/media/images/instagram.svg') }}">
                                        </div>
                                        <input type="text" class="focus-label media-count channel_count"
                                            value="{{Count($instagramMediaPage) ?? 0}}" id="instagram_pageCount"
                                            name="instagram_pageCount" readonly />

                                    </div>

                                    {{-- Google My Business --}}
                                    <div class="channel-content-wrap ">
                                        <div class="icon">
                                            <img
                                                src="{{ asset('frontend/assets/media/images/google-logo.svg') }}">
                                        </div>
                                        <input type="text" class="focus-label media-count channel_count"
                                            value="{{Count($googleMediaPage) ?? 0}}" id="google_pageCount"
                                            name="google_pageCount" readonly />
                                    </div>
                                    {{-- Google Analytixs --}}
                                    {{-- <div class="channel-content-wrap ">
                                        <div class="icon">
                                            <img
                                                src="{{ asset('frontend/assets/media/images/google-analytics-icon.svg') }}">
                                        </div>
                                        <input type="text" class="focus-label media-count channel_count"
                                            value="{{Count($googleAnalyticsMediaPage) ?? 0}}"
                                            id="google_analytics_pageCount" name="google_analytics_pageCount"
                                            readonly />

                                    </div> --}}
                                    {{-- Google Ads --}}
                                    {{-- <div class="channel-content-wrap ">
                                        <div class="icon">
                                            <img src="{{ asset('frontend/assets/media/images/googleads-icon.svg') }}">
                                        </div>
                                        <input type="text" class="focus-label media-count channel_count"
                                            value="{{Count($googleAdsMediaPage) ?? 0}}" id="google_ads_pageCount"
                                            name="google_ads_pageCount" readonly />

                                    </div> --}}

                                </div>
                                <div class="col-sm-6 pricing-amount">
                                    <p class="font-size-h6 text-dark-75 font-weight-bolder social-title">Amount</p>
                                    <input type="text" class="focus-label media-count"
                                        value="${{Count($facebookMediaPage) * ($annualPrice == 0  ? $monthlyPrice : $annualPrice) }}" id="f_page_price"
                                        name="f_page_price" readonly />
                                    <input type="text" class="focus-label media-count"
                                        value="${{Count($linkedInMediaPage) * ($annualPrice == 0  ? $monthlyPrice : $annualPrice)}}" id="l_page_price"
                                        name="l_page_price" readonly />
                                    <input type="text" class="focus-label media-count"
                                        value="${{Count($twitterMediaPage) * ($annualPrice == 0  ? $monthlyPrice : $annualPrice)}}" id="t_page_price"
                                        name="t_page_price" readonly />
                                    <input type="text" class="focus-label media-count"
                                        value="${{Count($instagramMediaPage) * ($annualPrice == 0  ? $monthlyPrice : $annualPrice)}}" id="i_page_price"
                                        name="i_page_price" readonly />
                                    <input type="text" class="focus-label media-count"
                                        value="${{Count($googleMediaPage) * ($annualPrice == 0  ? $monthlyPrice : $annualPrice)}}" id="g_page_price"
                                        name="g_page_price" readonly />
                                    {{-- <input type="text" class="focus-label media-count"
                                        value="${{Count($googleAnalyticsMediaPage) * ($annualPrice == 0  ? $monthlyPrice : $annualPrice)}}"
                                        id="google_analytics_page_price" name="google_analytics_page_price" readonly />
                                    <input type="text" class="focus-label media-count"
                                        value="${{Count($googleAdsMediaPage) * ($annualPrice == 0  ? $monthlyPrice : $annualPrice)}}"
                                        id="google_ads_page_price" name="google_ads_page_price" readonly /> --}}
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-center my-4">
                            <button type="button"
                                class="font-weight-bold text-uppercase custom-link focus-label link-btn buy-more"
                                id="morepages" style="border-radius:26px;width:55%;">Buy More Pages</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="media-card media-social-card bg-light">
                            <div class="checkout-footer mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0 mr-2">Total Channels</p>

                                    <span type="text" class="focus-label" id="totalchannel">{{$totalChannel ??
                                        0}}</span>
                                    <input type="hidden" name="totalchannelCount" id="totalchannelCount" />
                                </div>
                            </div>
                            @if($payment && $payment->type == 'monthly')
                            <div class="checkout-footer mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0 mr-2">Monthly
                                        Subscriptions</p>
                                    <input type="text" class="focus-label" id="price_monthly" name="price_monthly"
                                        readonly value="${{$totalChannel * $monthlyPrice ?? 0}}" />
                                </div>
                            </div>
                            @elseif($payment && $payment->type == 'yearly')
                            <div class="checkout-footer mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0 mr-2">Annual
                                        Subscriptions</p>
                                    <input type="text" class="focus-label" id="price_annual" name="price_annual"
                                        readonly value="${{$totalChannel * $annualPrice ?? 0}}" />
                                </div>
                            </div>
                            @if ($package && $package->yearly_off_amount !== null)
                            <p class="font-size-h6 font-weight-bolder mb-2 secondary-color">Save
                                {{ $package->yearly_off_amount}}%
                                on annual subscription</p>
                            @elseif (!$package && $packageYearly->yearly_off_amount !== null)
                            <p class="font-size-h6 font-weight-bolder mb-2 secondary-color">Save
                                {{ $packageYearly->yearly_off_amount}}%
                                on annual subscription</p>
                            @endif
                            {{-- <p class="font-size-h6 font-weight-bolder mb-2 secondary-color">Save
                                {{$package->yearly_off_amount}}% on annual subscription</p> --}}
                            @endif

                            <div class="checkout-footer subscription-time mb-2">
                                @if ($payment && $payment->type == 'monthly')
                                <div class="d-flex align-items-center">
                                    <input type="radio" id="monthly" name="radioPlan" value="monthly" @if($payment &&
                                        $payment->type == 'monthly') checked @endif />
                                    <label for="monthly">Monthly</label>
                                </div>
                                @elseif ($payment && $payment->type == 'yearly')
                                <div class="d-flex align-items-center">
                                    <input type="radio" id="annual" name="radioPlan" value="yearly" @if($payment &&
                                        $payment->type == 'yearly') checked @endif>
                                    <label for="annual">Annual</label>
                                </div>
                                @endif
                            </div>
                            @if($errors->any())
                            <div class="radio-error" id="radio_error"><span>{{$errors->first()}}</span></div>
                            @endif

                            <div class="checkout-footer mb-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0 mr-2">Total Subscription
                                    </p>
                                    <input type="text" class="focus-label" name="subTotal" id="subTotal"
                                        value="${{$payment->amount}}" readonly>
                                </div>
                            </div>
                            <div class="checkout-footer mb-0">
                                <div class="d-flex align-items-center justify-content-between">
                                    <button type="submit"
                                        class="font-weight-bold  text-uppercase custom-link focus-label link-btn"
                                        id="cancel-subscription"
                                        style="border-radius: 26px !important;width:55%;">Cancel
                                        Subscription</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <!--end::Row-->

            <!--end::Dashboard-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->

@endsection
@push('extra-js-scripts')
<script type="text/javascript">
    $(document).ready(function(){
$('#cancel-subscription').on("click", function (e) {
        e.preventDefault();
        Swal.fire({
            title: "Are you sure you want Cancel Subscription?",
            text: "You will loose your pages and data with social media!",
            showCancelButton: true,
            confirmButtonText: "Confirm",
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: "{{ route('cancel-subscription') }}",
                    type: "DELETE",
                    data:{
                        _token:"{{csrf_token()}}",
                    },
                    dataType: "json",
                    success: function (success) {
                        window.location = "{{ route('dashboard') }}";
                    },
                });
                Swal.fire({
                    title: "Deleted!",
                    icon: "success",
                    text: "Subscription Access Revoked.",
                    showConfirmButton: false,
                    timer: 1500,
                });
            }
        });
    });
$('#morepages').on('click', function(){
     window.location = "{{ route('media-page-list') }}";
});
$('input[type=radio][name=radioPlan]').change(function() {
    if (this.value == 'monthly') {
       $('#subTotal').val('$'+monthlyPrice.toFixed(2));
    }
    else if (this.value == 'yearly') {
        $('#subTotal').val('$'+finalTotal.toFixed(2));
    }
});



if ($("#monthly").is(":checked")) {
    $('#annual').prop('disabled', true);
}
else if($("#annual").is(":checked")){
    $('#monthly').prop('disabled', true);
}
   

  
    
});
</script>

@endpush