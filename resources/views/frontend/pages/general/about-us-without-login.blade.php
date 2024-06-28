<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <title>{{env('APP_NAME')}}</title>
    <!-- favicon  -->
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('frontend/images/favicon.ico/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('frontend/images/favicon.ico/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('frontend/images/favicon.ico/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('frontend/images/favicon.ico/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114"
        href="{{ asset('frontend/images/favicon.ico/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120"
        href="{{ asset('frontend/images/favicon.ico/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144"
        href="{{ asset('frontend/images/favicon.ico/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152"
        href="{{ asset('frontend/images/favicon.ico/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180"
        href="{{ asset('frontend/images/favicon.ico/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192"
        href="{{ asset('frontend/images/favicon.ico/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('frontend/images/favicon.ico/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('frontend/images/favicon.ico/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('frontend/images/favicon.ico/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('frontend/images/favicon.ico/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- css  -->
    <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/index-style.css') }}?version=2">

</head>

<body>
    <a id="button" class="back-to-top-btn"><img src="{{ asset('frontend/images/top-arrow.svg') }}" alt="icon"></a>
    <div class="banner-gradient">
        <header class="header">
            <nav class="navbar navbar-expand-md">
                <div class="container">
                    <a class="navbar-brand" href="#">
                        <img src="{{ asset('frontend/images/logo.png') }}" alt="">
                    </a>
                    <div class="mobile-menu">
                        <a href="{{route('login')}}" class="common-btn small-btn" type="submit">Sign in</a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"><img src="{{ asset('frontend/images/bars-solid.svg') }}"
                                    alt="icon"></span>
                        </button>
                    </div>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('landing-page')}}">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="javascript:void(0)">About Us</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{url('/#pirce-section')}}">Pricing</a>
                            </li>
                        </ul>
                    </div>
                    <div class="desktop-btn">
                        <a href="{{route('login')}}" class="common-btn small-btn" type="submit">Sign in</a>
                    </div>
                </div>
            </nav>
        </header>
        <!-- banner section  -->

        <section class="banner-without-login" id="home">
            <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                <!--begin::Subheader-->
                <div class="subheader py-2  subheader-transparent" id="kt_subheader">
                    <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                        <!--begin::Info-->
                        <div class="d-flex align-items-center flex-wrap mr-1">
                            <!--begin::Heading-->
                            <div class="d-flex">
                                <!--begin::Title-->
                                <h2 class="font-weight-bold my-2 mr-5">{!!$about_us->title ?? "About Us"!!}</h2>
                                <!--end::Title-->
                                <!--begin::Breadcrumb-->
                                <!--end::Breadcrumb-->
                            </div>
                            <!--end::Heading-->
                        </div>
                        <!--end::Info-->
                    </div>
                </div>
                <!--end::Subheader-->
                <div class="container about-us">
                    <div class="bg-white p-10 pt-18">
                        {!!$about_us->description!!}
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="gradiant-bg">
        <div class="footer-primary">
            <div class="container">
                <div class="company-detail">
                    <img src="{{ asset('frontend/images/footer-logo.png') }}" alt="image" class="footer-logo">
                    <div class="footer-des">
                        <p class="footer-title">About Us</p>
                        <p class="p-small">We don’t have a choice on whether we DO social media, the question is how
                            well we DO it.</p>
                    </div>
                    <div class="footer-contact">
                        <p class="footer-title">Contact Us</p>
                        <a href="tel:+910735215048"><img src="{{ asset('frontend/images/call-icon.svg') }}"
                                alt="icon">{{$sitesetting['support_contact']}}</a>
                        <a href="mailto:support@stackedup.com.au"><img
                                src="{{ asset('frontend/images/mail-icon.svg') }}"
                                alt="icon">{{$sitesetting['support_email']}}</a>
                    </div>
                </div>
                <div class="footer-pages">
                    <p class="footer-title">Information</p>
                    <ul class="footer-page-list">
                        <li><a href="{{route('about-us')}}">About Us</a></li>
                        <li><a href="{{ route('social-media-list') }}">Social Media</a></li>
                        <li><a href="#">Stack Up</a></li>
                        <li><a href="{{ route('posts.index') }}">Your Posts</a></li>
                        <li><a href="{{url('/#pirce-section')}}">Pricing</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <p class="footer-title">Helpful Links</p>
                    <ul class="footer-page-list">
                        <li><a href="{{ route('terms-conditions') }}">Terms & Condition</a></li>
                        <li><a href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-subscribe">
                    <p class="footer-title"> Subscribe More Info</p>
                    <input type="text" class="form-control" placeholder="Enter your mail">
                    <button class="common-btn small-btn">Submit</button>
                </div>
            </div>
        </div>
        <footer>
            <div class="container">
                @php
                $string="{{YEAR}}";
                @endphp
                <p> {{ \Str::replace($string,\Carbon\Carbon::now()->format('Y'),$sitesetting['footer_text']) }}</p>
                <ul class="social-links">
                    @if ($sitesetting['facebook'] !== null)
                    <li><a href={{$sitesetting['facebook']}} target="_blank"><img
                                src="{{ asset('frontend/images/facebook-icon.svg') }}" alt="icon"></a></li>
                    @endif
                    @if ($sitesetting['twitter'] !== null)
                    <li><a href={{$sitesetting['twitter']}} target="_blank"><img
                                src="{{ asset('frontend/images/twitter-icon.svg') }}" alt="icon"></a></li>
                    @endif
                    @if ($sitesetting['instagram'] !== null)
                    <li><a href={{$sitesetting['instagram']}} target="_blank"><img
                                src="{{ asset('frontend/images/instagram-icon.svg') }}" alt="icon"></a></li>
                    @endif
                    @if ($sitesetting['linkedin'] !== null)
                    <li><a href={{$sitesetting['linkedin']}} target="_blank"><img
                                src="{{ asset('frontend/images/linkedin-icon.svg') }}" alt="icon"></a></li>
                    @endif
                    @if ($sitesetting['pinterest'] !== null)
                    <li><a href={{$sitesetting['pinterest']}} target="_blank"><img
                                src="{{ asset('frontend/images/pinterest-icon.svg') }}" alt="icon"></a></li>
                    @endif
                </ul>
            </div>
        </footer>
    </div>
    <!-- js  -->
    <script src="{{ asset('frontend/js/lib/jquery.min.js') }}"></script>
    <script src="{{ asset('frontend/js/bootstrap.min.js') }}"></script>
    <!-- js  -->
    <script src="{{ asset('frontend/js/index.js') }}"></script>
</body>

</html>