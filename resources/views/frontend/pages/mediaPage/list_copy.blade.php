@extends('frontend.layouts.app')

@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <!--begin::Dashboard-->
            <!--begin::Row-->
            <div class="row justify-content-center mt-10">
                <div class="col-xl-5 col-lg-6 col-md-6 col-sm-8 col-11 bg-light rounded">
                    <!--begin::Tiles Widget 1-->
                    <div class="card card-custom gutter-b card-stretch bg-transparent shadow-none">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <div class="card-title">
                                <div class="card-label">
                                    <div class="font-weight-bolder">SOCIAL MEDIA PAGES</div>
                                </div>
                            </div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body d-flex flex-column px-0">
                            <!--begin::Items-->
                            <div class="flex-grow-1 card-spacer-x">
                            <input type="hidden" name="expired" id="expired" value="{{$is_expiry}}"/>
                                <!--begin::Item-->
                                @if(!empty($mediaPage))
                                @foreach($mediaPage as $value)

                                <div class="d-flex align-items-center justify-content-between mb-10">
                                    <div class="d-flex align-items-center mr-2 pages">
                                        @php $isDeleted = 'n'; @endphp
                                        @foreach($linkedPage as $linked)
                                            @if($linked->media_page_id ==  $value->id)
                                                 @php $isDeleted = $linked->is_deleted; @endphp
                                                @break
                                            @endif
                                        @endforeach
                                        <div>
                                            <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0">{{$value->page_name ?? "" }}</p>
                                            <!-- <div class="d-flex align-items-center">
                                                <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0 mr-2">Price:</p>
                                                <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0">$50</p> -->
                                                @if($isDeleted == 'y')
                                                <input type="hidden" name="price" id="price_{{ $value->id }}" value=0>
                                                @elseif($isDeleted == 'n')
                                                <input type="hidden" name="price" id="price_{{ $value->id }}" value=21>
                                                @endif
                                            <!-- </div> -->
                                        </div>
                                    </div>
                                     <!-- <div class="pages-price">
                                            <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0">$50</p>
                                        </div> -->

                                    <input type="hidden" name="media_id" value="">

                                        @php $flag=0; @endphp
                                        @foreach($linkedPage as $linked)
                                            @if($linked->media_page_id ==  $value->id && $linked->is_deleted == 'n')
                                                 @php $flag=1; @endphp
                                                @break
                                            @endif
                                        @endforeach

                                        @if($flag == 1)
                                        <a class="font-weight-bold font-size-h5 cart-link"
                                            data-social-name="{{ $value->page_name}}" href="javascript:;" data-page-id="{{ $value->page_id}}" data-id="{{ $value->id }}" data-media-id="{{$value->media_id}}" id="addPage{{$value->id}}" disabled="true"><i class='fa fa-check' aria-hidden='true'></i></a>
                                        @elseif($flag == 0)
                                        <a class="font-weight-bold font-size-h5 cart-link" href="javascript:;"
                                            data-social-name="{{ $value->page_name}}" data-page-id="{{ $value->page_id}}" data-id="{{ $value->id }}" data-media-id="{{$value->media_id}}" id="addPage{{$value->id}}">Add</a>
                                        @endif
                                       @php $flag=0; @endphp

                                        <!-- @if($linkedPage->isEmpty())
                                         <button class="font-weight-bold  custom-link font-size-h5 add-btn"
                                            data-social-name="{{ $value->page_name}}" href="javascript:;" data-page-id="{{ $value->page_id}}" data-id="{{ $value->id }}" data-media-id="{{$value->media_id}}" id="addPage{{$value->id}}">Add</button>
                                        @endif -->
                                </div>

                                @endforeach
                                @if($value ?? '')
                                <hr>
                                <form method="POST" action="{{route('checkout')}}" id="checkoutForm">
                                    @csrf
                                    @method('post')
                                <input type="hidden" name="media_id" id="media_id" value="">
                                <input type="hidden" name="mediapage_id" id="mediapage_id" value="">
                                <!-- <div class="d-flex align-items-center justify-content-between checkout-footer">
                                    <div class="d-flex align-items-center  ">
                                        <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0 mr-2">SubTotal:</p>
                                        <input type="text" class="subtotal font-size-h6 text-dark-75 font-weight-bolder mb-0" id="subtotal" name="subtotal" value="" readonly />
                                    </div>
                                    <button type="submit" class="font-weight-bold  custom-link  font-size-h5 link-btn" id="checkout"
                                        data-social-name="{{ $value->page_name}}" {{ $value->page_id}} disabled>Checkout</button>
                                </div> -->
                                <div class="checkout-footer mb-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0 mr-2">Total Channelsss</p>
                                        <span type="text" class="focus-label" id="totalchannel">0</span>
                                        <input type="hidden" name="totalchannelCount" id="totalchannelCount" />
                                    </div>
                                    <!-- <button type="submit" class="font-weight-bold  custom-link  font-size-h5 link-btn" id="checkout"
                                        data-social-name="{{ $value->page_name}}" {{ $value->page_id}} disabled>Checkout</button> -->
                                </div>
                                <div class="checkout-footer mb-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0 mr-2">Monthly Subscriptions</p>
                                        <input type="text" class="focus-label" id="price_monthly" name="price_monthly" readonly/>
                                    </div>
                                </div>
                                <div class="checkout-footer mb-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0 mr-2">Annual Subscriptions</p>
                                        <input type="text" class="focus-label" id="price_annual" name="price_annual" readonly/>
                                    </div>
                                </div>
                                <p class="font-size-h6 font-weight-bolder mb-2 secondary-color">Save 24% on annual subscription</p>
                                <div class="checkout-footer subscription-time mb-2">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" id="monthly" name="radioPlan" value="monthly">
                                        <label for="monthly">Monthly</label>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="radio" id="annual" name="radioPlan" value="yearly">
                                        <label for="annual">Annual</label>
                                    </div>
                                </div>
                                <div class="checkout-footer mb-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0 mr-2">Discount Code</p>
                                        <input type="text" class="focus-label discount-code" id="discountCode"/>
                                    </div>
                                </div>
                                <div class="checkout-footer mb-4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0 mr-2">Totla Subscription</p>
                                        <input type="text" class="focus-label" name="subTotal" id="subTotal"></span>
                                    </div>
                                </div>
                                <div class="checkout-footer mb-0">
                                    <div class="d-flex align-items-center justify-content-end">
                                    <button type="submit" class="font-weight-bold  custom-link focus-label link-btn" id="purchase"
                                        data-social-name="{{ $value->page_name}}" {{ $value->page_id}} >PURCHASE</button>
                                    </div>
                                </div>
                            </form>
                            @else
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center  ">
                                        <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0 mr-2">Please Linked to Social Media to get Page's</p>
                                    </div>
                                </div>
                            @endif
                            @else
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center  ">
                                        <p class="font-size-h6 text-dark-75 font-weight-bolder mb-0 mr-2">Please Linked to Social Media to get Page's</p>
                                    </div>
                                </div>
                            @endif
                            </div>
                            <!--end::Items-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Tiles Widget 1-->
                </div>
            </div>
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
<script>
</script>
<script type="text/javascript">
    function finalTotalAmount(Total,Discount,CodeDiscount){
        let discountAmount = (Total * Discount)/100;
        return (Total - discountAmount - CodeDiscount)
   }
$(document).ready(function(){
   $pageCount = 0;
   $total_price = [];
   $mediaPage_id = [];
    $("#purchase").prop("disabled", true);
    $(".cart-link").on("click", function(){
        $pageCount += 1;
        $is_expiry = $("#expired").val();
        // if($is_expiry == 'n'){
            $("#purchase").prop("disabled", true);
            $page_id = $(this).attr("data-page-id");
            $id = $(this).attr("data-id");
            $media_id = $(this).attr("data-media-id");
            $price = parseFloat($("#price_"+$id).val());
            $total_price.push($price);
            $(this).off('click');
            // $(this).prop("disabled", true);
            $(this).html("<i class='fa fa-check' aria-hidden='true'></i>");
            $mediaPage_id.push($id);
            $("#mediapage_id").val($mediaPage_id);
            $("#media_id").val($media_id);
            $sum = 0;
            $annualPrice =  '';
            $("#totalchannelCount").val($pageCount);
            // $('#subtotal').val(sum);
            // $("#checkout").prop("disabled", false);
            $.ajax({
                url: "{{route('check-pages')}}",
                type: "GET",
                data: {mediapage_id : $id,page_id: $page_id, media_id: $media_id, price: $price},
                dataType  : 'json',
                success: function (response) {
                    // console.log(response['data']);
                    if(response['data'] === '' || response['data'] === 'null'){
                       $.each($total_price,function(){$sum+=parseFloat(this) || 0;});
                       $("#totalchannel").text($pageCount);
                       $('#price_monthly').val('$'+$sum);
                       // monthly price * 12 months
                       $annualPrice = $sum * 12;
                       //finalTotalAmount(Total(Number),Discount(Percentage),CodeDiscount(Number));
                       let finalTotal = finalTotalAmount($annualPrice,24,0);
                       $('#price_annual').val('$'+Math.round(finalTotal));
                       $('#subTotal').val('$'+finalTotal);
                       $("#purchase").prop("disabled", false);
                    }else{
                    if($total_price == 0) $("#purchase").prop("disabled", true);
                    else $("#purchase").prop("disabled", false);
                       toastr.success('Page Added Successfully as your subscription is going on!');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                //    console.log(textStatus, errorThrown);
                }
            });
        // }
        // else{
        //     toastr.error('Please Purchase Subscription First');
        // }


    });
    $("#checkoutForm").validate({
        rules: {
            "radioPlan" :
            {
                required:true,
            }
        },
        messages: {
                "type":
                {
                    required:"@lang('validation.required',['attribute'=>'role type'])"
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
            removeOverlay();
            $("input[type=submit], input[type=button], button[type=submit]").prop("disabled",false);
            if (element.attr("data-error-container")) {
                error.appendTo(element.attr("data-error-container"));
            } else {
                error.insertAfter(element);
            }
        }
    });
});
</script>

@endpush
