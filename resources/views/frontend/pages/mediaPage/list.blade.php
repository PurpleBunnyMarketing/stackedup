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
            <form method="POST" action="{{route('checkout')}}" id="checkoutForm">
                @csrf
                @method('post')
                <div class="row">
                    <div class="col-md-6">
                        <div class="media-social-card bg-light">
                            <div class="row mb-5">
                                <div class="col-sm-6 pricing-amount-common pricing-amount-left">
                                    <p class="font-size-h6 text-dark-75 font-weight-bolder social-title">#channels</p>
                                    {{-- Facebook --}}
                                    <div class="channel-content-wrap">
                                        <div class="icon">
                                            <img src="{{ asset('frontend/assets/media/images/facebook.svg') }}">
                                        </div>
                                        <input type="text" class="focus-label media-count channel_count" value="0"
                                            id="facebook_pageCount" name="facebook_pageCount" readonly />
                                        <div class="icon-group-wrap">
                                            <button class="count-up" type="button"><i
                                                    class="fas fa-chevron-up"></i></button>
                                            <button class="count-down" type="button" id="facebook_down"><i
                                                    class="fas fa-chevron-down"></i></button>
                                        </div>
                                    </div>
                                    {{-- Linkedin --}}
                                    <div class="channel-content-wrap ">
                                        <div class="icon">
                                            <img src="{{ asset('frontend/assets/media/images/linkedin.svg') }}">
                                        </div>
                                        <input type="text" class="focus-label media-count channel_count" value="0"
                                            id="linkedin_pageCount" name="linkedin_pageCount" readonly />
                                        <div class="icon-group-wrap">
                                            <button class="count-up" type="button"><i
                                                    class="fas fa-chevron-up"></i></button>
                                            <button class="count-down" type="button" id="linkedin_down"><i
                                                    class="fas fa-chevron-down"></i></button>
                                        </div>
                                    </div>
                                    {{-- X(Twitter) --}}
                                    <div class="channel-content-wrap ">
                                        <div class="icon">
                                            <img src="{{ asset('frontend/assets/media/images/twitter.svg') }}">
                                        </div>
                                        <input type="text" class="focus-label media-count channel_count" value="0"
                                            id="twitter_pageCount" name="twitter_pageCount" readonly />
                                        <div class="icon-group-wrap">
                                            <button class="count-up" type="button"><i
                                                    class="fas fa-chevron-up"></i></button>
                                            <button class="count-down" type="button" id="twitter_down"><i
                                                    class="fas fa-chevron-down"></i></button>
                                        </div>
                                    </div>
                                    {{-- Instagram --}}
                                    <div class="channel-content-wrap ">
                                        <div class="icon">
                                            <img src="{{ asset('frontend/assets/media/images/instagram.svg') }}">
                                        </div>
                                        <input type="text" class="focus-label media-count channel_count" value="0"
                                            id="instagram_pageCount" name="instagram_pageCount" readonly />
                                        <div class="icon-group-wrap">
                                            <button class="count-up" type="button" type="button"><i
                                                    class="fas fa-chevron-up"></i></button>
                                            <button class="count-down" id="instagram_down" type="button"><i
                                                    class="fas fa-chevron-down"></i></button>
                                        </div>
                                    </div>
                                    {{-- 12-06 Add Google Icon for Business,Analytics,Ads --}}
                                    <div class="channel-content-wrap ">
                                        <div class="icon">
                                            <img src="{{ asset('frontend/assets/media/images/google-logo.svg') }}">
                                        </div>
                                        <input type="text" class="focus-label media-count channel_count" value="0"
                                            id="google_pageCount" name="google_pageCount" readonly />
                                        <div class="icon-group-wrap">
                                            <button class="count-up" type="button" type="button"><i
                                                    class="fas fa-chevron-up"></i></button>
                                            <button class="count-down" id="google_down" type="button"><i
                                                    class="fas fa-chevron-down"></i></button>
                                        </div>
                                    </div>
                                    {{-- 20-04 Add Google My Business --}}
                                    {{-- <div class="channel-content-wrap ">
                                        <div class="icon">
                                            <img
                                                src="{{ asset('frontend/assets/media/images/google-my-business.svg') }}">
                                        </div>
                                        <input type="text" class="focus-label media-count channel_count" value="0"
                                            id="google_pageCount" name="google_pageCount" readonly />
                                        <div class="icon-group-wrap">
                                            <button class="count-up" type="button" type="button"><i
                                                    class="fas fa-chevron-up"></i></button>
                                            <button class="count-down" id="google_down" type="button"><i
                                                    class="fas fa-chevron-down"></i></button>
                                        </div>
                                    </div> --}}
                                    {{-- 26-05 Google Analytics --}}
                                    {{-- <div class="channel-content-wrap ">
                                        <div class="icon">
                                            <img
                                                src="{{ asset('frontend/assets/media/images/google-analytics-icon.svg') }}">
                                        </div>
                                        <input type="text" class="focus-label media-count channel_count" value="0"
                                            id="google_analytics_pageCount" name="google_analytics_pageCount"
                                            readonly />
                                        <div class="icon-group-wrap">
                                            <button class="count-up" type="button" type="button"><i
                                                    class="fas fa-chevron-up"></i></button>
                                            <button class="count-down" id="google_analytics_down" type="button"><i
                                                    class="fas fa-chevron-down"></i></button>
                                        </div>
                                    </div> --}}
                                    {{-- 26-05 Google Ads --}}
                                    {{-- <div class="channel-content-wrap ">
                                        <div class="icon">
                                            <img src="{{ asset('frontend/assets/media/images/googleads-icon.svg') }}">
                                        </div>
                                        <input type="text" class="focus-label media-count channel_count" value="0"
                                            id="google_ads_pageCount" name="google_ads_pageCount" readonly />
                                        <div class="icon-group-wrap">
                                            <button class="count-up" type="button" type="button"><i
                                                    class="fas fa-chevron-up"></i></button>
                                            <button class="count-down" id="google_ads_down" type="button"><i
                                                    class="fas fa-chevron-down"></i></button>
                                        </div>
                                    </div> --}}

                                </div>
                                <div class="col-sm-6 pricing-amount-common pricing-amount">
                                    <p class="font-size-h6 text-dark-75 font-weight-bolder social-title">Amount</p>
                                    <input type="text" class="focus-label media-count" value="$0" id="f_page_price"
                                        name="f_page_price" readonly />
                                    <input type="text" class="focus-label media-count" value="$0" id="l_page_price"
                                        name="l_page_price" readonly />
                                    <input type="text" class="focus-label media-count" value="$0" id="t_page_price"
                                        name="t_page_price" readonly />
                                    <input type="text" class="focus-label media-count" value="$0" id="i_page_price"
                                        name="i_page_price" readonly />
                                    {{-- 12/06 -> Google for Business,Analytics,Ads --}}
                                    <input type="text" class="focus-label media-count" value="$0" id="g_page_price"
                                        name="g_page_price" readonly />
                                    {{-- 20-04 -> Google My Business --}}
                                    {{-- <input type="text" class="focus-label media-count" value="$0" id="g_page_price"
                                        name="g_page_price" readonly /> --}}
                                    {{-- 26-05 -> Google Analytics--}}
                                    {{-- <input type="text" class="focus-label media-count" value="$0"
                                        id="google_analytics_page_price" name="google_analytics_page_price" readonly />
                                    --}}
                                    {{-- 26-05 -> Google Ads --}}
                                    {{-- <input type="text" class="focus-label media-count" value="$0"
                                        id="google_ads_page_price" name="google_ads_page_price" readonly /> --}}
                                </div>
                            </div>
                            <div class="row mt-5">
                                <div class="col-md-12 col-12">
                                    <div class=""><b>*Additional Information: </b></div>
                                    <div class="">One Google Subscription purchase grants you access to Google Ads, Google Analytics and Google Business Profile Reporting and scheduling. Additional One Facebook Subscription will grant you access to link both a Facebook Page and Facebook Ad Account. </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="media-card media-social-card bg-light">
                            <div class="checkout-footer mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0 mr-2">Total Channels</p>
                                    <span type="text" class="focus-label" id="totalchannel">0</span>
                                    <input type="hidden" name="totalchannelCount" id="totalchannelCount" />
                                </div>
                            </div>
                            @if ($payment && $payment->type == 'monthly')
                            <div class="checkout-footer mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0 mr-2">Monthly
                                        Subscriptions</p>
                                    <input type="text" class="focus-label" id="price_monthly" name="price_monthly"
                                        readonly value="0" />
                                </div>
                            </div>
                            @elseif ($payment && $payment->type == 'yearly')
                            <div class="checkout-footer mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0 mr-2">Annual
                                        Subscriptions</p>
                                    <input type="text" class="focus-label" id="price_annual" name="price_annual"
                                        readonly value="0" />
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
                            @else
                            <div class="checkout-footer mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0 mr-2">Monthly
                                        Subscriptions</p>
                                    <input type="text" class="focus-label" id="price_monthly" name="price_monthly"
                                        readonly value="0" />
                                </div>
                            </div>
                            <div class="checkout-footer mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0 mr-2">Annual
                                        Subscriptions</p>
                                    <input type="text" class="focus-label" id="price_annual" name="price_annual"
                                        readonly value="0" />
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
                            {{-- {{$package ? ($package->package_type=='monthly' ? '1' : $package->yearly_off_amount ) :
                            $packageYearly->yearly_off_amount ?? '1' }} --}}
                            {{-- <p class="font-size-h6 font-weight-bolder mb-2 secondary-color">Save
                                {{!$package ? $packageYearly->yearly_off_amount : $package->yearly_off_amount }}%
                                on annual subscription</p> --}}
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
                                @else
                                <div class="d-flex align-items-center">
                                    <input type="radio" id="monthly" name="radioPlan" value="monthly" @if($payment &&
                                        $payment->type == 'monthly') checked @endif />
                                    <label for="monthly">Monthly</label>
                                </div>
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
                            <div class="checkout-footer mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0 mr-2"
                                        @if($is_couponCode=='y' )data-toggle="tooltip" data-theme="dark"
                                        title="Disabled" style="color:grey !important" disabled @endif>Coupon
                                        Code</p>
                                    <input type="text" class="focus-label discount-code cursor-block" id="discountCode"
                                        value="" name="discountCode" @if($is_couponCode=='y' ) style="border-color:grey"
                                        disabled @endif />
                                </div>
                                @if ($is_couponCode=='y')
                                <span class="font-size-h6 text-dark-50 font-weight-bolder mb-0 mr-2">You have already
                                    used Coupon Code once.</span>
                                @endif
                                <div class="d-flex align-items-center justify-content-between">
                                    <div id="coupon-error">
                                        <b><span style="color: red;"></span></b>
                                    </div>
                                    <div id="remove-coupon">
                                        <a class="btn btn-link" id="remove_coupon_button" href="#" data-toggle="tooltip"
                                            data-theme="dark" title="Remove Coupon Code"><i
                                                class="far fa-times-circle text-danger"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="checkout-footer mb-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0 mr-2">Total Subscription
                                    </p>
                                    <input type="text" class="focus-label" name="subTotal" id="subTotal" value="0"
                                        readonly>
                                </div>
                            </div>
                            <div class="checkout-footer mb-0">
                                <div class="d-flex align-items-center justify-content-end">
                                    <button type="submit" class="font-weight-bold  custom-link focus-label link-btn"
                                        id="purchase" style="border-radius: 26px !important;">PURCHASE</button>
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
$("#purchase").prop("disabled", true);
$('#remove-coupon').css('display', 'none');

var DiscounteAmount = 0;

$("#discountCode").on("focusout", function() {
    $("#purchase").prop("disabled", true);
    var facebookCount = parseInt($('#facebook_pageCount').val());
    var linkedinCount = parseInt($('#linkedin_pageCount').val());
    var twitterCount = parseInt($('#twitter_pageCount').val());
    var instagramCount = parseInt($('#instagram_pageCount').val());
    var googleCount = parseInt($('#google_pageCount').val());
    // var googleAnalyticsCount = parseInt($('#google_analytics_pageCount').val());
    // var googleAdsCount = parseInt($('#google_ads_pageCount').val());
    // if(facebookCount == 0 && linkedinCount == 0 && twitterCount == 0 && instagramCount ==0 && googleCount == 0 && googleAnalyticsCount == 0 && googleAdsCount == 0){
    if(facebookCount == 0 && linkedinCount == 0 && twitterCount == 0 && instagramCount ==0 && googleCount == 0){
        $('#discountCode').val(null);
        showMessage(412, 'Please Select Media pages first.');
        $("#purchase").prop("disabled", true);
    }else{
        var coupon_Code = $("#discountCode").val();
        applyCouponCode(coupon_Code);
    }
});

function applyCouponCode(coupon_Code){
    $.ajax({
            url: "{{route('exist.coupon')}}",
            type: "GET",
            data: {couponCode : coupon_Code},
            dataType  : 'json',
            success: function (response) {
                if(response.status == 200){
                    var FinalTotal = 0;
                    $("#coupon-error span").text(response.message).css('color','green');
                    $("#purchase").prop("disabled", false);
                    var subtotal = $('#subTotal').val();
                    subtotal = subtotal.substring(1);
                    if(response.type == '%'){
                        DiscounteAmount = parseInt(subtotal * response.off_amount/100);
                        FinalTotal = subtotal - DiscounteAmount;
                    }else{
                        DiscounteAmount = response.off_amount;
                        FinalTotal = parseInt(subtotal - response.off_amount);
                    }
                    FinalTotal = FinalTotal > 0 ? FinalTotal : 0;
                    $('#subTotal').val('$'+Math.round(FinalTotal));
                    $('#discountCode').attr('readonly', true);
                    $('#remove-coupon').css('display', 'block');
                }else{
                    $("#coupon-error span").text(response.message);
                }
                // toastr.success('Page Added Successfully as your subscription is going on!');

            },
            error: function(jqXHR, textStatus, errorThrown) {
                $("#coupon-error span").text("Coupon code is not valid");
            }
        });
}

$('#remove_coupon_button').click(function(){
    $("#coupon-error span").text("");
    $("#coupon-error span").css("color",'red');
    $('#discountCode').val(null);
    $('#discountCode').prop("readonly", false);
    showMessage(200, 'Coupon removed Successfully.');
    var subtotal = $('#subTotal').val();
    if(subtotal == '$0'){
        if ($("#monthly").is(":checked")) {
            subtotal = $('#price_monthly').val();
        }
        else if($("#annual").is(":checked")){
            subtotal = $('#price_annual').val();
        }
        $('#subTotal').val(subtotal);
    } else{
        subtotal = subtotal.substring(1);
        var DiscountAddedAmount = parseInt(subtotal) + parseInt(DiscounteAmount);
        $('#subTotal').val('$'+Math.round(DiscountAddedAmount));
    }

    $('#remove-coupon').css('display', 'none');
});

var perPagePrice = Math.round("{{!$package ? $packageMonthly->amount : $package->amount }}");
var totalChannel = 0;
let finalTotal = 0;
let monthlyPrice = 0;
var decreaseMonthlyPrice = 0;
var annualPrice = 0;
var yearlyDefaultDiscount = parseInt("{{$package ? ($package->package_type === 'monthly' ? '1' : $package->yearly_off_amount ?? 1) : $packageYearly->yearly_off_amount ?? 1}}");
var isPackageExists = "{{ $package ? true : false}}";
var packageType = "{{ $package ? $package->package_type : ''}}"

function finalTotalAmount(Total,Discount,CodeDiscount){
    let discountAmount = 0;
    if(Discount !== 1){
         discountAmount = (Total * Discount)/100;
    }
    return (Total - discountAmount - CodeDiscount)
}
// counter
//initialising a variable name data
$(document).on('click','.count-up',function(){

    $("#purchase").prop("disabled", false);
    var counter = $(this).parents('.channel-content-wrap').find('input').val();
    var updateed = parseInt(counter) + 1;

    $(this).parents('.channel-content-wrap').find('input').val(updateed);

    var facebookCount = parseInt($('#facebook_pageCount').val());
    var linkedinCount = parseInt($('#linkedin_pageCount').val());
    var twitterCount = parseInt($('#twitter_pageCount').val());
    var instagramCount = parseInt($('#instagram_pageCount').val());
    var googleCount = parseInt($('#google_pageCount').val());
    // var googleAnalyticsCount = parseInt($('#google_analytics_pageCount').val());
    // var googleAdsCount = parseInt($('#google_ads_pageCount').val());

    // totalChannel = facebookCount + linkedinCount + twitterCount + instagramCount + googleCount + googleAdsCount + googleAnalyticsCount;
    totalChannel = facebookCount + linkedinCount + twitterCount + instagramCount + googleCount;
    $("#totalchannel").text(totalChannel);
    $("#totalchannelCount").val(totalChannel);
    $("#f_page_price").val('$'+facebookCount * parseInt(perPagePrice));
    $("#l_page_price").val('$'+linkedinCount * parseInt(perPagePrice));
    $("#t_page_price").val('$'+twitterCount * parseInt(perPagePrice));
    $("#i_page_price").val('$'+instagramCount * parseInt(perPagePrice));
    $("#g_page_price").val('$'+googleCount * parseInt(perPagePrice));
    // $("#google_analytics_page_price").val('$'+googleAnalyticsCount * parseInt(perPagePrice));
    // $("#google_ads_page_price").val('$'+googleAdsCount * parseInt(perPagePrice));


    var fpages_price = $("#f_page_price").val();
    var lpages_price = $("#l_page_price").val();
    var tpages_price = $("#t_page_price").val();
    var ipages_price = $("#i_page_price").val();
    var gpages_price = $("#g_page_price").val();
    // var google_analytics_pages_price = $("#google_analytics_page_price").val();
    // var google_ads_pages_price = $("#google_ads_page_price").val();

    // remove $ string from value
    var facebookPrice = fpages_price.replace(/[^a-z0-9,. ]/gi, '');
    var linkedinPrice = lpages_price.replace(/[^a-z0-9,. ]/gi, '');
    var twitterPrice  =  tpages_price.replace(/[^a-z0-9,. ]/gi, '');
    var instagramPrice = ipages_price.replace(/[^a-z0-9,. ]/gi, '');
    var googlePrice = gpages_price.replace(/[^a-z0-9,. ]/gi, '');
    // var googleAnalyticsPrice = google_analytics_pages_price.replace(/[^a-z0-9,. ]/gi, '');
    // var googleAdsPrice = google_ads_pages_price.replace(/[^a-z0-9,. ]/gi, '');

    // monthlyPrice = parseInt(facebookPrice) + parseInt(linkedinPrice) + parseInt(twitterPrice) + parseInt(instagramPrice) + parseInt(googlePrice) + parseInt(googleAnalyticsPrice) + parseInt(googleAdsPrice);
    monthlyPrice = parseInt(facebookPrice) + parseInt(linkedinPrice) + parseInt(twitterPrice) + parseInt(instagramPrice) + parseInt(googlePrice);
    $('#price_monthly').val('$'+monthlyPrice);
    // console.log(monthlyPrice);
    if(isPackageExists == 1){
        annualPrice = monthlyPrice;
    }else{
        annualPrice = monthlyPrice * 12;
    }
    finalTotal = finalTotalAmount(annualPrice,yearlyDefaultDiscount,0);

    $('#price_annual').val('$'+Math.round(finalTotal));

    if($("#monthly").is(":checked")) {
        $('#subTotal').val('$'+Math.round(monthlyPrice));
    }
    else if($("#annual").is(":checked")) {
        $('#subTotal').val('$'+Math.round(finalTotal));
    }

    var coupon_Code = $("#discountCode").val();
    if(coupon_Code != null && coupon_Code != '') {
        applyCouponCode(coupon_Code);
    }
});


$(document).on('click','#facebook_down',function(){
    var counter = $(this).parents('.channel-content-wrap').find('input').val();
    if(counter == 0) $("#purchase").prop("disabled", true);
    if( parseInt(counter) >= 1 ){
        $("#purchase").prop("disabled", false);
        var updateed = parseInt(counter) - 1;
        $(this).parents('.channel-content-wrap').find('input').val(updateed);
        totalChannel = parseInt(totalChannel) - 1;
        $("#totalchannel").text(totalChannel);

        var fPagePrice = $("#f_page_price").val();

        // remove $ string from value
        var facebookPrice = fPagePrice.replace(/[^a-z0-9,. ]/gi, '');
        if(facebookPrice != 0) var fDecreasePrice = facebookPrice - perPagePrice;
        if(facebookPrice != 0) $('#f_page_price').val('$'+fDecreasePrice);


        if(packageType == 'monthly'){
            monthlyPrice = $('#price_monthly').val().replace(/[^a-z0-9,. ]/gi, '');
        }else if(packageType == 'yearly'){
            var facebookCount = parseInt($('#facebook_pageCount').val());
            var linkedinCount = parseInt($('#linkedin_pageCount').val());
            var twitterCount = parseInt($('#twitter_pageCount').val());
            var instagramCount = parseInt($('#instagram_pageCount').val());
            var googleCount = parseInt($('#google_pageCount').val());
            $("#f_page_price").val('$'+facebookCount * parseInt(perPagePrice));
            $("#l_page_price").val('$'+linkedinCount * parseInt(perPagePrice));
            $("#t_page_price").val('$'+twitterCount * parseInt(perPagePrice));
            $("#i_page_price").val('$'+instagramCount * parseInt(perPagePrice));
            $("#g_page_price").val('$'+googleCount * parseInt(perPagePrice));


            var fpages_price = $("#f_page_price").val();
            var lpages_price = $("#l_page_price").val();
            var tpages_price = $("#t_page_price").val();
            var ipages_price = $("#i_page_price").val();
            var gpages_price = $("#g_page_price").val();

            // remove $ string from value
            var facebookPrice = fpages_price.replace(/[^a-z0-9,. ]/gi, '');
            var linkedinPrice = lpages_price.replace(/[^a-z0-9,. ]/gi, '');
            var twitterPrice  =  tpages_price.replace(/[^a-z0-9,. ]/gi, '');
            var instagramPrice = ipages_price.replace(/[^a-z0-9,. ]/gi, '');
            var googlePrice = gpages_price.replace(/[^a-z0-9,. ]/gi, '');
            annualPrice = monthlyPrice = parseInt(facebookPrice) + parseInt(linkedinPrice) + parseInt(twitterPrice) + parseInt(instagramPrice) + parseInt(googlePrice);
        }else{
            monthlyPrice = $('#price_monthly').val().replace(/[^a-z0-9,. ]/gi, '');
            annualPrice = (parseInt(monthlyPrice) - perPagePrice) * 12;
        }
        decreaseMonthlyPrice = parseInt(monthlyPrice) - perPagePrice;
        $('#price_monthly').val('$'+decreaseMonthlyPrice);

        // monthly price * 12 months
        finalTotal = finalTotalAmount(annualPrice,yearlyDefaultDiscount,0);

        $('#price_annual').val('$'+Math.round(finalTotal));
        if ($("#monthly").is(":checked")) {
            $('#subTotal').val('$'+Math.round(decreaseMonthlyPrice));
        }
        else if($("#annual").is(":checked")) {
            $('#subTotal').val('$'+Math.round(finalTotal));
        }

    }
    var coupon_Code = $("#discountCode").val();
    if(coupon_Code != null && coupon_Code != '') {
        applyCouponCode(coupon_Code);
    }
});



$(document).on('click','#linkedin_down',function(){
    var counter = $(this).parents('.channel-content-wrap').find('input').val();
    if(counter == 0) $("#purchase").prop("disabled", true);
    if( parseInt(counter) >= 1 ){
        $("#purchase").prop("disabled", false);
        var updateed = parseInt(counter) - 1;
        $(this).parents('.channel-content-wrap').find('input').val(updateed);
        totalChannel = parseInt(totalChannel) - 1;
        $("#totalchannel").text(totalChannel);

        var lPagePrice = $("#l_page_price").val();

        // remove $ string from value
        var linkedinPrice = lPagePrice.replace(/[^a-z0-9,. ]/gi, '');

        if(linkedinPrice != 0) var lDecreasePrice = linkedinPrice - perPagePrice;

        if(linkedinPrice != 0) $("#l_page_price").val('$'+lDecreasePrice);

        if(packageType == 'monthly'){
            monthlyPrice = $('#price_monthly').val().replace(/[^a-z0-9,. ]/gi, '');
        }else if(packageType == 'yearly'){
            var facebookCount = parseInt($('#facebook_pageCount').val());
            var linkedinCount = parseInt($('#linkedin_pageCount').val());
            var twitterCount = parseInt($('#twitter_pageCount').val());
            var instagramCount = parseInt($('#instagram_pageCount').val());
            var googleCount = parseInt($('#google_pageCount').val());
            $("#f_page_price").val('$'+facebookCount * parseInt(perPagePrice));
            $("#l_page_price").val('$'+linkedinCount * parseInt(perPagePrice));
            $("#t_page_price").val('$'+twitterCount * parseInt(perPagePrice));
            $("#i_page_price").val('$'+instagramCount * parseInt(perPagePrice));
            $("#g_page_price").val('$'+googleCount * parseInt(perPagePrice));


            var fpages_price = $("#f_page_price").val();
            var lpages_price = $("#l_page_price").val();
            var tpages_price = $("#t_page_price").val();
            var ipages_price = $("#i_page_price").val();
            var gpages_price = $("#g_page_price").val();

            // remove $ string from value
            var facebookPrice = fpages_price.replace(/[^a-z0-9,. ]/gi, '');
            var linkedinPrice = lpages_price.replace(/[^a-z0-9,. ]/gi, '');
            var twitterPrice  =  tpages_price.replace(/[^a-z0-9,. ]/gi, '');
            var instagramPrice = ipages_price.replace(/[^a-z0-9,. ]/gi, '');
            var googlePrice = gpages_price.replace(/[^a-z0-9,. ]/gi, '');
            annualPrice = monthlyPrice = parseInt(facebookPrice) + parseInt(linkedinPrice) + parseInt(twitterPrice) + parseInt(instagramPrice) + parseInt(googlePrice);
        }else{
            monthlyPrice = $('#price_monthly').val().replace(/[^a-z0-9,. ]/gi, '');
            annualPrice = (parseInt(monthlyPrice) - perPagePrice) * 12;
        }
        decreaseMonthlyPrice = parseInt(monthlyPrice) - perPagePrice;
        $('#price_monthly').val('$'+decreaseMonthlyPrice);

        // monthly price * 12 months
        finalTotal = finalTotalAmount(annualPrice,yearlyDefaultDiscount,0);

        $('#price_annual').val('$'+Math.round(finalTotal));
        if ($("#monthly").is(":checked")) {
           $('#subTotal').val('$'+Math.round(decreaseMonthlyPrice));
        }
        else if($("#annual").is(":checked")) {
            $('#subTotal').val('$'+Math.round(finalTotal));
        }
    }
    var coupon_Code = $("#discountCode").val();
    if(coupon_Code != null && coupon_Code != '') {
        applyCouponCode(coupon_Code);
    }
});


$(document).on('click','#twitter_down',function(){
    var counter = $(this).parents('.channel-content-wrap').find('input').val();
    if(counter == 0) $("#purchase").prop("disabled", true);
    if( parseInt(counter) >= 1 ){
        $("#purchase").prop("disabled", false);
        var updateed = parseInt(counter) - 1;
        $(this).parents('.channel-content-wrap').find('input').val(updateed);
        totalChannel = parseInt(totalChannel) - 1;
        $("#totalchannel").text(totalChannel);


        var tPagePrice = $("#t_page_price").val();

        // remove $ string from value
        var twitterPrice  = tPagePrice.replace(/[^a-z0-9,. ]/gi, '');

        if(twitterPrice != 0)  var tDecreasePrice = twitterPrice - perPagePrice;

        if(twitterPrice != 0)  $("#t_page_price").val('$'+tDecreasePrice);

        if(packageType == 'monthly'){
            monthlyPrice = $('#price_monthly').val().replace(/[^a-z0-9,. ]/gi, '');
        }else if(packageType == 'yearly'){
            var facebookCount = parseInt($('#facebook_pageCount').val());
            var linkedinCount = parseInt($('#linkedin_pageCount').val());
            var twitterCount = parseInt($('#twitter_pageCount').val());
            var instagramCount = parseInt($('#instagram_pageCount').val());
            var googleCount = parseInt($('#google_pageCount').val());
            $("#f_page_price").val('$'+facebookCount * parseInt(perPagePrice));
            $("#l_page_price").val('$'+linkedinCount * parseInt(perPagePrice));
            $("#t_page_price").val('$'+twitterCount * parseInt(perPagePrice));
            $("#i_page_price").val('$'+instagramCount * parseInt(perPagePrice));
            $("#g_page_price").val('$'+googleCount * parseInt(perPagePrice));


            var fpages_price = $("#f_page_price").val();
            var lpages_price = $("#l_page_price").val();
            var tpages_price = $("#t_page_price").val();
            var ipages_price = $("#i_page_price").val();
            var gpages_price = $("#g_page_price").val();

            // remove $ string from value
            var facebookPrice = fpages_price.replace(/[^a-z0-9,. ]/gi, '');
            var linkedinPrice = lpages_price.replace(/[^a-z0-9,. ]/gi, '');
            var twitterPrice  =  tpages_price.replace(/[^a-z0-9,. ]/gi, '');
            var instagramPrice = ipages_price.replace(/[^a-z0-9,. ]/gi, '');
            var googlePrice = gpages_price.replace(/[^a-z0-9,. ]/gi, '');
            annualPrice = monthlyPrice = parseInt(facebookPrice) + parseInt(linkedinPrice) + parseInt(twitterPrice) + parseInt(instagramPrice) + parseInt(googlePrice);
        }else{
            monthlyPrice = $('#price_monthly').val().replace(/[^a-z0-9,. ]/gi, '');
            annualPrice = (parseInt(monthlyPrice) - perPagePrice) * 12;
        }
        decreaseMonthlyPrice = parseInt(monthlyPrice) - perPagePrice;
        $('#price_monthly').val('$'+decreaseMonthlyPrice);

        // monthly price * 12 months
        finalTotal = finalTotalAmount(annualPrice,yearlyDefaultDiscount,0);

        $('#price_annual').val('$'+Math.round(finalTotal));
        if ($("#monthly").is(":checked")) {
            $('#subTotal').val('$'+Math.round(decreaseMonthlyPrice));
        }
        else if($("#annual").is(":checked")) {
            $('#subTotal').val('$'+Math.round(finalTotal));
        }
    }
    var coupon_Code = $("#discountCode").val();
    if(coupon_Code != null && coupon_Code != '') {
        applyCouponCode(coupon_Code);
    }
});


$(document).on('click','#instagram_down',function(){
    var counter = $(this).parents('.channel-content-wrap').find('input').val();
    if(counter == 0) $("#purchase").prop("disabled", true);
    if( parseInt(counter) >= 1 ){
        $("#purchase").prop("disabled", false);
        var updateed = parseInt(counter) - 1;
        $(this).parents('.channel-content-wrap').find('input').val(updateed);
        totalChannel = parseInt(totalChannel) - 1;
        $("#totalchannel").text(totalChannel);


        var iPagePrice = $("#i_page_price").val();

        // remove $ string from value
        var instagramPrice = iPagePrice.replace(/[^a-z0-9,. ]/gi, '');


        if(instagramPrice != 0) var iDecreasePrice = instagramPrice - perPagePrice;

        if(instagramPrice != 0) $("#i_page_price").val('$'+iDecreasePrice);

        if(packageType == 'monthly'){
            monthlyPrice = $('#price_monthly').val().replace(/[^a-z0-9,. ]/gi, '');
        }else if(packageType == 'yearly'){
            var facebookCount = parseInt($('#facebook_pageCount').val());
            var linkedinCount = parseInt($('#linkedin_pageCount').val());
            var twitterCount = parseInt($('#twitter_pageCount').val());
            var instagramCount = parseInt($('#instagram_pageCount').val());
            var googleCount = parseInt($('#google_pageCount').val());
            $("#f_page_price").val('$'+facebookCount * parseInt(perPagePrice));
            $("#l_page_price").val('$'+linkedinCount * parseInt(perPagePrice));
            $("#t_page_price").val('$'+twitterCount * parseInt(perPagePrice));
            $("#i_page_price").val('$'+instagramCount * parseInt(perPagePrice));
            $("#g_page_price").val('$'+googleCount * parseInt(perPagePrice));


            var fpages_price = $("#f_page_price").val();
            var lpages_price = $("#l_page_price").val();
            var tpages_price = $("#t_page_price").val();
            var ipages_price = $("#i_page_price").val();
            var gpages_price = $("#g_page_price").val();

            // remove $ string from value
            var facebookPrice = fpages_price.replace(/[^a-z0-9,. ]/gi, '');
            var linkedinPrice = lpages_price.replace(/[^a-z0-9,. ]/gi, '');
            var twitterPrice  =  tpages_price.replace(/[^a-z0-9,. ]/gi, '');
            var instagramPrice = ipages_price.replace(/[^a-z0-9,. ]/gi, '');
            var googlePrice = gpages_price.replace(/[^a-z0-9,. ]/gi, '');
            annualPrice = monthlyPrice = parseInt(facebookPrice) + parseInt(linkedinPrice) + parseInt(twitterPrice) + parseInt(instagramPrice) + parseInt(googlePrice);
        }else{
            monthlyPrice = $('#price_monthly').val().replace(/[^a-z0-9,. ]/gi, '');
            annualPrice = (parseInt(monthlyPrice) - perPagePrice) * 12;
        }
        decreaseMonthlyPrice = parseInt(monthlyPrice) - perPagePrice;
        $('#price_monthly').val('$'+decreaseMonthlyPrice);

        // monthly price * 12 months
        finalTotal = finalTotalAmount(annualPrice,yearlyDefaultDiscount,0);

        $('#price_annual').val('$'+Math.round(finalTotal));
        if ($("#monthly").is(":checked")) {
            $('#subTotal').val('$'+Math.round(decreaseMonthlyPrice));
        }
        else if($("#annual").is(":checked")) {
            $('#subTotal').val('$'+Math.round(finalTotal));
        }
    }
    var coupon_Code = $("#discountCode").val();
    if(coupon_Code != null && coupon_Code != '') {
        applyCouponCode(coupon_Code);
    }
});

// 20-04 -> Google
// 12/06 Add Google for Business,analytics,ads
$(document).on('click','#google_down',function(){
    var counter = $(this).parents('.channel-content-wrap').find('input').val();
    if(counter == 0) $("#purchase").prop("disabled", true);
    if( parseInt(counter) >= 1 ){
        $("#purchase").prop("disabled", false);
        var updateed = parseInt(counter) - 1;
        $(this).parents('.channel-content-wrap').find('input').val(updateed);
        totalChannel = parseInt(totalChannel) - 1;
        $("#totalchannel").text(totalChannel);


        var gPagePrice = $("#g_page_price").val();

        // remove $ string from value
        var googlePrice = gPagePrice.replace(/[^a-z0-9,. ]/gi, '');


        if(googlePrice != 0) var gDecreasePrice = googlePrice - perPagePrice;

        if(googlePrice != 0) $("#g_page_price").val('$'+gDecreasePrice);

        if(packageType == 'monthly'){
            monthlyPrice = $('#price_monthly').val().replace(/[^a-z0-9,. ]/gi, '');
        }else if(packageType == 'yearly'){
            var facebookCount = parseInt($('#facebook_pageCount').val());
            var linkedinCount = parseInt($('#linkedin_pageCount').val());
            var twitterCount = parseInt($('#twitter_pageCount').val());
            var instagramCount = parseInt($('#instagram_pageCount').val());
            var googleCount = parseInt($('#google_pageCount').val());
            $("#f_page_price").val('$'+facebookCount * parseInt(perPagePrice));
            $("#l_page_price").val('$'+linkedinCount * parseInt(perPagePrice));
            $("#t_page_price").val('$'+twitterCount * parseInt(perPagePrice));
            $("#i_page_price").val('$'+instagramCount * parseInt(perPagePrice));
            $("#g_page_price").val('$'+googleCount * parseInt(perPagePrice));


            var fpages_price = $("#f_page_price").val();
            var lpages_price = $("#l_page_price").val();
            var tpages_price = $("#t_page_price").val();
            var ipages_price = $("#i_page_price").val();
            var gpages_price = $("#g_page_price").val();

            // remove $ string from value
            var facebookPrice = fpages_price.replace(/[^a-z0-9,. ]/gi, '');
            var linkedinPrice = lpages_price.replace(/[^a-z0-9,. ]/gi, '');
            var twitterPrice  =  tpages_price.replace(/[^a-z0-9,. ]/gi, '');
            var instagramPrice = ipages_price.replace(/[^a-z0-9,. ]/gi, '');
            var googlePrice = gpages_price.replace(/[^a-z0-9,. ]/gi, '');
            annualPrice = monthlyPrice = parseInt(facebookPrice) + parseInt(linkedinPrice) + parseInt(twitterPrice) + parseInt(instagramPrice) + parseInt(googlePrice);
        }else{
            monthlyPrice = $('#price_monthly').val().replace(/[^a-z0-9,. ]/gi, '');
            annualPrice = (parseInt(monthlyPrice) - perPagePrice) * 12;
        }
        decreaseMonthlyPrice = parseInt(monthlyPrice) - perPagePrice;
        $('#price_monthly').val('$'+decreaseMonthlyPrice);

        // monthly price * 12 months
        finalTotal = finalTotalAmount(annualPrice,yearlyDefaultDiscount,0);
        $('#price_annual').val('$'+Math.round(finalTotal));
        if ($("#monthly").is(":checked")) {
            $('#subTotal').val('$'+Math.round(decreaseMonthlyPrice));
        }
        else if($("#annual").is(":checked")) {
            $('#subTotal').val('$'+Math.round(finalTotal));
        }
    }
    var coupon_Code = $("#discountCode").val();
    if(coupon_Code != null && coupon_Code != '') {
        applyCouponCode(coupon_Code);
    }
});
// 26-05 -> Google Analytics
// $(document).on('click','#google_analytics_down',function(){
//     var counter = $(this).parents('.channel-content-wrap').find('input').val();
//     if(counter == 0) $("#purchase").prop("disabled", true);
//     if( parseInt(counter) >= 1 ){
//         $("#purchase").prop("disabled", false);
//         var updateed = parseInt(counter) - 1;
//         $(this).parents('.channel-content-wrap').find('input').val(updateed);
//         totalChannel = parseInt(totalChannel) - 1;
//         $("#totalchannel").text(totalChannel);


//         var googleAnalyticsPagePrice = $("#google_analytics_page_price").val();

//         // remove $ string from value
//         var googleAnalyticsPrice = googleAnalyticsPagePrice.replace(/[^a-z0-9,. ]/gi, '');


//         if(googleAnalyticsPrice != 0) var googleAnalyticsDecreasePrice = googleAnalyticsPrice - perPagePrice;

//         if(googleAnalyticsPrice != 0) $("#google_analytics_page_price").val('$'+googleAnalyticsDecreasePrice);

//         if(packageType == 'monthly'){
//             monthlyPrice = $('#price_monthly').val().replace(/[^a-z0-9,. ]/gi, '');
//         }else if(packageType == 'yearly'){
//             var facebookCount = parseInt($('#facebook_pageCount').val());
//             var linkedinCount = parseInt($('#linkedin_pageCount').val());
//             var twitterCount = parseInt($('#twitter_pageCount').val());
//             var instagramCount = parseInt($('#instagram_pageCount').val());
//             var googleCount = parseInt($('#google_pageCount').val());
//             var googleAnalyticsCount = parseInt($('#google_analytics_pageCount').val());
//             var googleAdsCount = parseInt($('#google_ads_pageCount').val());

//             $("#f_page_price").val('$'+facebookCount * parseInt(perPagePrice));
//             $("#l_page_price").val('$'+linkedinCount * parseInt(perPagePrice));
//             $("#t_page_price").val('$'+twitterCount * parseInt(perPagePrice));
//             $("#i_page_price").val('$'+instagramCount * parseInt(perPagePrice));
//             $("#g_page_price").val('$'+googleCount * parseInt(perPagePrice));
//             $("#google_analytics_page_price").val('$'+googleAnalyticsCount * parseInt(perPagePrice));
//             $("#google_ads_page_price").val('$'+googleAdsCount * parseInt(perPagePrice));


//             var fpages_price = $("#f_page_price").val();
//             var lpages_price = $("#l_page_price").val();
//             var tpages_price = $("#t_page_price").val();
//             var ipages_price = $("#i_page_price").val();
//             var gpages_price = $("#g_page_price").val();
//             var google_analytics_pages_price = $("#google_analytics_page_price").val();
//             var google_ads_pages_price = $("#google_ads_page_price").val();

//             // remove $ string from value
//             var facebookPrice = fpages_price.replace(/[^a-z0-9,. ]/gi, '');
//             var linkedinPrice = lpages_price.replace(/[^a-z0-9,. ]/gi, '');
//             var twitterPrice  =  tpages_price.replace(/[^a-z0-9,. ]/gi, '');
//             var instagramPrice = ipages_price.replace(/[^a-z0-9,. ]/gi, '');
//             var googlePrice = gpages_price.replace(/[^a-z0-9,. ]/gi, '');
//             var googleAnalyticsPrice = google_analytics_pages_price.replace(/[^a-z0-9,. ]/gi, '');
//             var googleAdsPrice = google_ads_pages_price.replace(/[^a-z0-9,. ]/gi, '');
//             annualPrice = monthlyPrice = parseInt(facebookPrice) + parseInt(linkedinPrice) + parseInt(twitterPrice) + parseInt(instagramPrice) + parseInt(googlePrice) + parseInt(googleAnalyticsPrice) + parseInt(googleAdsPrice);
//         }else{
//             monthlyPrice = $('#price_monthly').val().replace(/[^a-z0-9,. ]/gi, '');
//             annualPrice = (parseInt(monthlyPrice) - perPagePrice) * 12;
//         }
//         decreaseMonthlyPrice = parseInt(monthlyPrice) - perPagePrice;
//         $('#price_monthly').val('$'+decreaseMonthlyPrice);

//         // monthly price * 12 months
//         finalTotal = finalTotalAmount(annualPrice,yearlyDefaultDiscount,0);

//         $('#price_annual').val('$'+Math.round(finalTotal));
//         if ($("#monthly").is(":checked")) {
//            $('#subTotal').val('$'+Math.round(decreaseMonthlyPrice));
//         }
//         else if($("#annual").is(":checked")) {
//             $('#subTotal').val('$'+Math.round(finalTotal));
//         }
//     }
// });
// 26-05 -> Google Ads
// $(document).on('click','#google_ads_down',function(){
//     var counter = $(this).parents('.channel-content-wrap').find('input').val();
//     if(counter == 0) $("#purchase").prop("disabled", true);
//     if( parseInt(counter) >= 1 ){
//         $("#purchase").prop("disabled", false);
//         var updateed = parseInt(counter) - 1;
//         $(this).parents('.channel-content-wrap').find('input').val(updateed);
//         totalChannel = parseInt(totalChannel) - 1;
//         $("#totalchannel").text(totalChannel);


//         var googleAdsPagePrice = $("#google_ads_page_price").val();

//         // remove $ string from value
//         var googleAdsPrice = googleAdsPagePrice.replace(/[^a-z0-9,. ]/gi, '');


//         if(googleAdsPrice != 0) var googleAdsDecreasePrice = googleAdsPrice - perPagePrice;

//         if(googleAdsPrice != 0) $("#google_ads_page_price").val('$'+googleAdsDecreasePrice);

//         if(packageType == 'monthly'){
//             monthlyPrice = $('#price_monthly').val().replace(/[^a-z0-9,. ]/gi, '');
//         }else if(packageType == 'yearly'){
//             var facebookCount = parseInt($('#facebook_pageCount').val());
//             var linkedinCount = parseInt($('#linkedin_pageCount').val());
//             var twitterCount = parseInt($('#twitter_pageCount').val());
//             var instagramCount = parseInt($('#instagram_pageCount').val());
//             var googleCount = parseInt($('#google_pageCount').val());
//             var googleAnalyticsCount = parseInt($('#google_analytics_pageCount').val());
//             var googleAdsCount = parseInt($('#google_ads_pageCount').val());

//             $("#f_page_price").val('$'+facebookCount * parseInt(perPagePrice));
//             $("#l_page_price").val('$'+linkedinCount * parseInt(perPagePrice));
//             $("#t_page_price").val('$'+twitterCount * parseInt(perPagePrice));
//             $("#i_page_price").val('$'+instagramCount * parseInt(perPagePrice));
//             $("#g_page_price").val('$'+googleCount * parseInt(perPagePrice));
//             $("#google_analytics_page_price").val('$'+googleAnalyticsCount * parseInt(perPagePrice));
//             $("#google_ads_page_price").val('$'+googleAdsCount * parseInt(perPagePrice));


//             var fpages_price = $("#f_page_price").val();
//             var lpages_price = $("#l_page_price").val();
//             var tpages_price = $("#t_page_price").val();
//             var ipages_price = $("#i_page_price").val();
//             var gpages_price = $("#g_page_price").val();
//             var google_analytics_pages_price = $("#google_analytics_page_price").val();
//             var google_ads_pages_price = $("#google_ads_page_price").val();

//             // remove $ string from value
//             var facebookPrice = fpages_price.replace(/[^a-z0-9,. ]/gi, '');
//             var linkedinPrice = lpages_price.replace(/[^a-z0-9,. ]/gi, '');
//             var twitterPrice  =  tpages_price.replace(/[^a-z0-9,. ]/gi, '');
//             var instagramPrice = ipages_price.replace(/[^a-z0-9,. ]/gi, '');
//             var googlePrice = gpages_price.replace(/[^a-z0-9,. ]/gi, '');
//             var googleAnalyticsPrice = google_analytics_pages_price.replace(/[^a-z0-9,. ]/gi, '');
//             var googleAdsPrice = google_ads_pages_price.replace(/[^a-z0-9,. ]/gi, '');
//             annualPrice = monthlyPrice = parseInt(facebookPrice) + parseInt(linkedinPrice) + parseInt(twitterPrice) + parseInt(instagramPrice) + parseInt(googlePrice) + parseInt(googleAnalyticsPrice) + parseInt(googleAdsPrice);
//         }else{
//             monthlyPrice = $('#price_monthly').val().replace(/[^a-z0-9,. ]/gi, '');
//             annualPrice = (parseInt(monthlyPrice) - perPagePrice) * 12;
//         }
//         decreaseMonthlyPrice = parseInt(monthlyPrice) - perPagePrice;
//         $('#price_monthly').val('$'+decreaseMonthlyPrice);

//         // monthly price * 12 months
//         finalTotal = finalTotalAmount(annualPrice,yearlyDefaultDiscount,0);

//         $('#price_annual').val('$'+Math.round(finalTotal));
//         if ($("#monthly").is(":checked")) {
//            $('#subTotal').val('$'+Math.round(decreaseMonthlyPrice));
//         }
//         else if($("#annual").is(":checked")) {
//             $('#subTotal').val('$'+Math.round(finalTotal));
//         }
//     }
// });

    $('input[type=radio][name=radioPlan]').change(function() {
        if (this.value == 'monthly') {
            $('#subTotal').val('$'+Math.round(monthlyPrice));
        }
        else if (this.value == 'yearly') {
            $('#subTotal').val('$'+Math.round(finalTotal));
        }
        chechPlanSelection();
        var coupon_Code = $("#discountCode").val();
        if(coupon_Code != null && coupon_Code != '') {
            applyCouponCode(coupon_Code);
        }
    });

    function chechPlanSelection(){
        if(!$("#monthly").is(":checked") && !$("#annual").is(":checked")){
            $('#discountCode').attr('disabled', true).addClass('bg-secondary');
        } else{
            $('#discountCode').attr('disabled', false).removeClass('bg-secondary');
        }
    }
    chechPlanSelection();


if ($("#monthly").is(":checked")) {
    $('#annual').prop('disabled', true);
}
else if($("#annual").is(":checked")){
    $('#monthly').prop('disabled', true);
}
});
</script>

@endpush
