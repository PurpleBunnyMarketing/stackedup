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
                <h1 class="secondary-color">Link your Social Media channels and start stacking those posts!</h1> 
            </div> 
            <!-- for second page start  -->
            <div class="row stacking-post-block">
                <div class="col-sm-8 col-lg-6">
                    <div class="stacking-posts">
                        <div class="stacking-header">
                            <div class="stack-post-icon">
                                icon
                            </div>
                            <div class="stack-post-count">
                                <span>3</span>/<span>4</span>
                            </div>
                            <button class="custom-link">Link Facebook</button>
                        </div>
                        <div class="stack-page-link">
                            <h2>Facebook Page Name 1</h2>
                            <button class="custom-link">Unlink</button>
                        </div>
                        <div class="stack-page-link">
                            <h2>Facebook Page Name 1</h2>
                            <button class="custom-link">Unlink</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- for second page end  -->
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
    // counter 
    
//initialising a variable name data


$(document).on('click','.count-up',function(){
    var counter = $(this).parents('.channel-content-wrap').find('input').val();
    var updateed = parseInt(counter) + 1;

    $(this).parents('.channel-content-wrap').find('input').val(updateed);

})


$(document).on('click','.count-down',function(){
    var counter = $(this).parents('.channel-content-wrap').find('input').val();
    if( parseInt(counter) >= 1 ){
        
         var updateed = parseInt(counter) - 1;
         $(this).parents('.channel-content-wrap').find('input').val(updateed);
    }
})
  


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
                    console.log(response['data']);
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
                   console.log(textStatus, errorThrown);
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
