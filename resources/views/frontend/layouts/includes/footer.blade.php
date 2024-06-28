<!--begin::Footer-->
<div class="footer bg-white py-4 d-flex flex-lg-column" id="kt_footer">
    <!--begin::Container-->
    <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between">
        <!--begin::Copyright-->
        <div class="text-dark order-2 order-md-1">
            <span class="text-muted font-weight-bold mr-2">2022©</span>
            <a href="http://keenthemes.com/metronic" target="_blank"
                class="text-dark-75 text-hover-primary">Stackedup</a>
        </div>
        <!--end::Copyright-->
        <!--begin::Nav-->
        <div class="nav nav-dark order-1 order-md-2 justify-content-center">
            <a href="{{ route('about-us') }}"  class="nav-link pr-3 pl-0 {{ Route::is('about-us') ? 'active' : '' }}">About
                Us</a>
            <a href="{{ route('contact') }}" class="nav-link px-3 {{ Route::is('contact') ? 'active' : '' }}">Contact
                Us</a>
            <a href="{{ route('terms-conditions') }}"  class="nav-link pl-3 pr-0 {{ Route::is('terms-conditions') ? 'active' : '' }}">Terms
                &amp; Conditions</a>
            <a href="{{ route('privacy-policy') }}" class="nav-link pl-3 pr-0 {{ Route::is('privacy-policy') ? 'active' : '' }}">Privacy Policy</a>
            <a href="{{ route('faqs') }}"  class="nav-link pl-3 pr-0 {{ Route::is('faqs') ? 'active' : '' }}">FAQs</a>
        </div>
        <!--end::Nav-->
    </div>
    <!--end::Container-->
</div>
<!--end::Footer-->