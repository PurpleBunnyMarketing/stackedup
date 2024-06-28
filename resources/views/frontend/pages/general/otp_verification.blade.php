@extends('auth.layouts.app')

@section('content')
<!--begin::Main-->
<div class="d-flex flex-column flex-root">
    <!--begin::Login-->
    <div class="login login-4 login-signin-on d-flex flex-row-fluid" id="kt_login">
        <div class="d-flex flex-center flex-row-fluid bgi-size-cover bgi-position-top bgi-no-repeat"
            style="background-image: url({{ asset('frontend/assets/media/bg/bg-3.jpg') }});">
            <div class="login-form custom-default-class text-center p-7 position-relative overflow-hidden">
                <!--begin::Login Header-->
                <div class="d-flex flex-center mb-15">
                    <a href="{{ route('login') }}">
                        <img src="{{ asset('frontend/assets/media/images/logo.png') }}" class="max-h-75px" alt="" />
                    </a>
                </div>
                <!--begin::Login forgot password form-->
                <div class="login-forgot">
                    <div class="mb-20">
                        <h3>otp verification</h3>
                        <!-- <div class="text-muted font-weight-bold">Please enter your registered Email Id to receive reset
									password link</div> -->
                    </div>
                    <form class="form" id="login_otp_form" action="{{ route('verify-otp') }}" method="POST">
                        @csrf
                        <div class="form-group mb-10">
                            <div class="d-flex flex-row mt-5">
                                <input type="text" maxlength="1" class="form-control mr-4" autofocus="" id="digit-1"
                                    name="digit-1" oninput="this.value=this.value.replace(/[^0-9]/g,'');" />
                                <input type="text" maxlength="1" class="form-control mr-4" id="digit-2" name="digit-2"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'');" />
                                <input type="text" maxlength="1" class="form-control mr-4" id="digit-3" name="digit-3"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'');" />
                                <input type="text" maxlength="1" class="form-control" id="digit-4" name="digit-4"
                                    oninput="this.value=this.value.replace(/[^0-9]/g,'');" />

                                <input type="hidden" class="form-control" id="phoneCode" name="phoneCode"
                                    value="{{$phoneCode}}" />
                                <input type="hidden" class="form-control" id="mobileNo" name="mobileNo"
                                    value="{{$mobileNo}}" />
                            </div>
                            <div class="d-flex justify-content-between mt-5" id="resend-otp-timer">
                                <!-- <span class="d-block mobile-text resend-otp disabled" id="resend">Resend OTP in </span>
										<center><span class="d-block mobile-text" id="countdown"></span></center> -->
                                <a class="btn btn-link" style="display:inline;pointer-events: none" disabled
                                    id="withTimer">
                                    {{ __('Resend OTP in ') }}
                                    <span id='countdown'></span>
                                </a>
                                <p class="btn btn-link" style="display:none;" id="withoutTimer">
                                    {{ __('Resend OTP') }}
                                    <span id='countdown'></span>
                                </p>
                            </div>
                            <!-- <div>
									  	<a class="btn btn-link" style="display:inline;pointer-events: none" disabled id="withTimer">
                                        {{ __('Resend OTP in ') }}
                                    <span id='timer'></span>
                                    </a>
                                    <a class="btn btn-link" href="{{ route('send-otp') }}" style="display:none;" id="withoutTimer">
                                        {{ __('Resend OTP') }}
                                    <span id='timer'></span>
                                    </a>
									  </div> -->
                        </div>
                        <div class="form-group d-flex flex-wrap flex-center mt-10">
                            <button id="kt_login_forgot_submit"
                                class="btn common-btn font-weight-bold px-9 py-4 my-3 mx-2" disabled>Verify</button>
                        </div>
                    </form>
                </div>
                <!--end::Login forgot password form-->
            </div>
        </div>
    </div>
    <!--end::Login-->
</div>
@endsection
@push('extra-js-scripts')
<script>
    var HOST_URL = "https://keenthemes.com/metronic/tools/preview";
</script>
<!--begin::Global Config(global config for global JS scripts)-->
<script>
    var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1200 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#6993FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#F3F6F9", "dark": "#212121" }, "light": { "white": "#ffffff", "primary": "#E1E9FF", "secondary": "#ECF0F3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#212121", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#ECF0F3", "gray-300": "#E5EAEE", "gray-400": "#D6D6E0", "gray-500": "#B5B5C3", "gray-600": "#80808F", "gray-700": "#464E5F", "gray-800": "#1B283F", "gray-900": "#212121" } }, "font-family": "Poppins" };
</script>
<!--end::Global Config-->
<!--begin::Global Theme Bundle(used by all pages)-->
<script src="assets/plugins/global/plugins.bundle.js?v=7.0.5"></script>
<script src="assets/plugins/custom/prismjs/prismjs.bundle.js?v=7.0.5"></script>
<script src="assets/js/scripts.bundle.js?v=7.0.5"></script>
<!--end::Global Theme Bundle-->
<!--begin::Page Vendors(used by this page)-->
<script src="assets/plugins/custom/fullcalendar/fullcalendar.bundle.js?v=7.0.5"></script>
<!--end::Page Vendors-->
<!--begin::Page Scripts(used by this page)-->
<script src="assets/js/pages/widgets.js?v=7.0.5"></script>
<!--end::Page Scripts-->
<script type="text/javascript">
    function timer(params) {
        var time = 119; // 01:59 time
        var saved_countdown = localStorage.getItem('saved_countdown');

        if(saved_countdown == null) {
            // Set the time we're counting down to using the time allowed
            var new_countdown = new Date().getTime() + (time + 2) * 1000;

            time = new_countdown;
            localStorage.setItem('saved_countdown', new_countdown);
        } else {
            time = saved_countdown;
        }

        // Update the count down every 1 second
        var x = setInterval(() => {

            // Get today's date and time
            var now = new Date().getTime();

            // Find the distance between now and the allowed time
            var distance = time - now;

            // Time counter
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

            // Output the result in an element with id="demo"
            document.getElementById("countdown").innerHTML = minutes + ":" + seconds;

            // If the count down is over, write some text
            if (minutes == 0 && seconds == 0) {
                clearInterval(x);
                localStorage.removeItem('saved_countdown');
                toastr.error('Oppss your OTP is expiry!!');
                document.getElementById("withTimer").style.display = 'none';
                document.getElementById("withoutTimer").style.display = 'block';
            }
        }, 1590);
    }



$(document).ready(function() {
    timer();
	$('#kt_login_forgot_submit').prop('disabled', true);
    $('input[type="text"]').keyup(function() {
        if($(this).val() != '') {
            $('#kt_login_forgot_submit').prop('disabled', false);
        }
    });
    $('#digit-1').keyup(function(e) {
        var key = e.keyCode || e.charCode;
        if($(this).val().length == 1){
            $('#digit-2').focus();
        }
    });
    $('#digit-2').keyup(function(e) {
        var key = e.keyCode || e.charCode;
        if($(this).val().length == 1){
            $('#digit-3').focus();
        }
        if(key == 8 && $(this).val().length == 0){
            $('#digit-1').focus();
        }
    });
    $('#digit-3').keyup(function(e) {
        var key = e.keyCode || e.charCode;
        if($(this).val().length == 1){
            $('#digit-4').focus();
        }
        if(key == 8 && $(this).val().length == 0){
            $('#digit-2').focus();
        }
    });
    $('#digit-4').keyup(function(e) {
        var key = e.keyCode || e.charCode;
        if(key == 8 && $(this).val().length == 0){
            $('#digit-3').focus();
        }
    });

    $('#resend-otp-timer').on('click','#withoutTimer',(e)=>{
        var phoneCode = '{{$phoneCode}}';
        var mobileNumber = '{{$mobileNo}}';

        $.ajax({
            type : 'POST',
            url : "{{ route('resend-otp')}}" ,
            data : {
                _token : "{{csrf_token()}}",
                phoneCode : phoneCode,
                mobileNo : mobileNumber,
            },
            success:(response) => {
                if(response.status == 200){
                    toastr.success('OTP Send Succesfully on register Mobile Number');
                    $('#withoutTimer').css('display', 'none');
                    $('#withTimer').css('display', 'block');
                    timer();
                }
            },
            error: (error) => {
                // console.log('error' + error);
            }
        });
    });
});

</script>
@endpush
