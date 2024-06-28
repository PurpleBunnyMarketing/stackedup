{{-- Custom CSS --}}
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/custom.css') }}" />


<!--begin::Fonts-->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
<!--end::Fonts-->
<!--begin::Page Vendors Styles(used by this page)-->
<link href="{{ asset('frontend/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css?v=7.0.5') }}" rel="stylesheet"
    type="text/css" />
<!--end::Page Vendors Styles-->
<!--begin::Global Theme Styles(used by all pages)-->
<link rel="stylesheet" href="{{ asset('frontend/assets/css/jquery.fancybox.css') }}" type="text/css">
<link href="{{ asset('frontend/assets/plugins/global/plugins.bundle.css?v=7.0.5') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('frontend/assets/plugins/custom/prismjs/prismjs.bundle.css?v=7.0.5') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('frontend/assets/css/style.bundle.css?v=7.0.5') }}" rel="stylesheet" type="text/css" />
<!--end::Global Theme Styles-->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />



<!--end::Layout Themes-->

<style>
    .custom-loader {
        width: 100px;
        height: 100px;
        display: grid;
        border: 4px solid #0e051b00;
        border-radius: 50%;
        border-right-color: #e77f01;
        animation: s5 1s infinite linear;
    }

    .custom-loader::before,
    .custom-loader::after {
        content: "";
        grid-area: 1/1;
        margin: 2px;
        border: inherit;
        border-radius: 50%;
        animation: s5 2s infinite;
    }

    .custom-loader::after {
        margin: 8px;
        animation-duration: 3s;
    }

    @keyframes s5 {
        100% {
            transform: rotate(1turn)
        }
    }


    /*Google Analytics*/
    .section-count {
        cursor: pointer;
    }

    .section-count-active {
        border: 2px solid rgb(231, 127, 1);
    }

    .location-section-count-active {
        border: 2px solid rgb(231, 127, 1);
    }

    .language-section-count-active {
        border: 2px solid rgb(231, 127, 1);
    }

    .browser-section-count-active {
        border: 2px solid rgb(231, 127, 1);
    }

    .age-section-count-active {
        border: 2px solid rgb(231, 127, 1);
    }

    .gender-section-count-active {
        border: 2px solid rgb(231, 127, 1);
    }

    .device-section-count-active {
        border: 2px solid rgb(231, 127, 1);
    }

    .location-section-count {
        cursor: pointer;
    }

    .language-section-count {
        cursor: pointer;
    }

    .browser-section-count {
        cursor: pointer;
    }

    .age-section-count {
        cursor: pointer;
    }

    .gender-section-count {
        cursor: pointer;
    }

    .device-section-count {
        cursor: pointer;
    }

    .soical-media-modal-main {
        overflow-y: hidden !important;
    }

    .soical-media-modal-main .modal-body {
        min-height: 250px;
        max-height: 600px;
        overflow-y: scroll;
    }

    @media only screen and (max-width: 767px) {
        .soical-media-modal-main .modal-body {
            max-height: 400px;
        }
    }

    /* .nav-ga.active, .main-nav-ga.active {
    border: solid 1px red;
} */
    /* .loc-link.active {
    border: solid 1px red;
} */
    /* .nav-ga-data.active{
    border: solid 1px red;
} */
</style>