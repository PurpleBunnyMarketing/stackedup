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
    <meta name="google-site-verification" content="3yJqpLDdXXAPh9xX8TMAIJSYFqGVHADkx4msrjOcYNY" />
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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">

    <link rel="manifest" href="{{ asset('frontend/images/favicon.ico/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- css  -->
    <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/index-style.css') }}?version=2">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-6KS12MMLPD"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());

gtag('config', 'G-6KS12MMLPD');
    </script>
</head>

<body>
    <a id="button" class="back-to-top-btn"><img src="{{ asset('frontend/images/top-arrow.svg') }}" alt="icon"></a>
    <div class="banner-gradient">
        <div class="banner-circle-group">
            <div class="circle-1"></div>
            <div class="circle-2"></div>
            <div class="circle-3"></div>
            <div class="circle-4"></div>
            <div class="circle-5"></div>
            <div class="circle-6"></div>
            <div class="banner-star"></div>
            <div class="gradient-star"></div>
            <div class="spyder"></div>
        </div>
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
                                <a class="nav-link active" aria-current="page" href="#home">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('about-us')}}">About Us</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#pirce-section">Pricing</a>
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
        <section class="banner" id="home">
            <div class="container">
                <div class="row banner-wrap">
                    <div class="col-md-6">
                        <div class="banner-content">
                            <h5 class="theme-color star-icon d-inline-block">Stacked Up, it’s social media simplified!
                            </h5>
                            <h1 class="tag-h2">HOW DOES YOUR SOCIAL MEDIA <span class="theme-color">STACK UP</span>?
                            </h1>
                            <p>Stacked Up is an all-in-one social media platform for marketers and business owners that
                                makes it effortless to plan, schedule and analyse your content. </p>
                            <a href="{{route('register')}}" class="common-btn large-btn rounded-arrow"
                                type="submit">Register Now</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="banner-img">
                            <img src="{{ asset('frontend/images/banner-img.png') }}" alt="image">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- post section  -->
        <section class="posts" id="about-us">
            <div class="container">
                <div class="row banner-wrap common-pad">
                    <div class="col-md-5">
                        <div class="banner-img">
                            <img src="{{ asset('frontend/images/post-img.png') }}" alt="image">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="banner-content rounded-arrow">
                            <h5 class="theme-color d-flex align-items-center">We mean effortlessly schedule your posts
                                <img src="{{ asset('frontend/images/smily.svg') }}" class="ms-2" alt="smaily-icon">
                            </h5>
                            <h1 class="tag-h2">STACK UP YOUR <span class="theme-color">POSTS!</span></h1>
                            <p>Stacked Up allows you to schedule your posts ahead of time and automatically publishes
                                everything for you. Simply set, forget, and get back to growing your business! </p>
                            <p>Schedule your posts across multiple platforms</p>
                        </div>
                    </div>
                </div>
                <div class="row banner-wrap common-pad">
                    <div class="col-md-6">
                        <div class="banner-img">
                            <img src="{{ asset('frontend/images/report.png') }}" alt="image">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="banner-content rounded-arrow">
                            <h5 class="theme-color star-icon d-inline-block">Measure your success with reporting</h5>
                            <h1 class="tag-h2 ">SEE HOW YOUR POSTS <span class="theme-color">STACK UP!</span></h1>
                            <p>Stacked Up saves you time and money by allowing you to manage all your social media
                                channels in one platform, with a simple monthly fee per social media channel you connect
                                and UNLIMITED users. </p>
                            <p>Our reporting makes it easier than ever to measure what’s working for your business and
                                what’s not, making you the king or queen of social media analytics.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- fatures section -->
        <div class="feature">
            <div class="container">
                <h5 class="theme-color star-icon d-inline-block text-center">Measure your success with reporting
                    features like: </h5>
                <div class="row">
                    <div class="col-md-3 col-sm-4 col-6">
                        <div class="feature-card border-gradient border-gradient-purple">
                            <div class="feature-img">
                                <img src="{{ asset('frontend/images/fature-card-1.png') }}" alt="image">
                            </div>
                            <h6>Audience Growth measurement</h6>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-4 col-6">
                        <div class="feature-card border-gradient border-gradient-purple">
                            <div class="feature-img">
                                <img src="{{ asset('frontend/images/fature-card-2.png') }}" alt="image">
                            </div>
                            <h6>Audience Engagement & Demographics</h6>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-4 col-6">
                        <div class="feature-card border-gradient border-gradient-purple">
                            <div class="feature-img">
                                <img src="{{ asset('frontend/images/fature-card-3.png') }}" alt="image">
                            </div>
                            <h6>Page reach</h6>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-4 col-6">
                        <div class="feature-card border-gradient border-gradient-purple">
                            <div class="feature-img">
                                <img src="{{ asset('frontend/images/fature-card-4.png') }}" alt="image">
                            </div>
                            <h6>Individual Posts Performance </h6>
                        </div>
                    </div>
                </div>
                <p>We also simplify your Facebook & Instagram Advertising Campaign reporting with easy to read
                    performance reports including Clicks, Leads, Impressions, CPC, CPL, Demographics and loads more.</p>
                <p>As a 100% Australian owned business, know that you’re supporting Aussies with a tool that is well
                    suited to make your social media simple.</p>
            </div>
        </div>
        {{-- Plateform Section --}}
        @php
        $medias = App\Models\Media::orderBy('order_sequence')->get();
        @endphp
        <div class="feature">
            <div class="container">
                <h5 class="theme-color star-icon d-inline-block text-center"> Available Platforms </h5>
                <div class="home-media-section-main">
                    @foreach ($medias as $media)
                    @if($media->name == 'Facebook Ads') @continue; @endif
                    <img src="{{$media->image_url}}" alt="{{$media->name}}">
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="gradiant-bg" id="pirce-section">
        <div class="footer-star-1"></div>
        <div class="footer-star-2"></div>
        <section class="price-plan common-pad">
            <div class="container">
                <div class="price-banner">
                    <div class="banner-content rounded-arrow">
                        <h5 class="theme-color">Price detailing</h5>
                        <h2 class="star-icon banner-title d-inline-block">READY TO SEE HOW YOUR SOCIALS STACK UP?
                        </h2>
                        <p>Stacked Up saves you time and money by allowing you to manage all your social media
                            channels
                            in one platform, with a simple monthly fee per social media channel you connect and
                            UNLIMITED users.</p>
                    </div>
                </div>

                <div class="row price-card-group">
                    <div class="col-md-6">
                        <div class="price-card">
                            <div class="custom-card-header">
                                <div class="price-content">
                                    <div class="price-icon">
                                        <img src="{{ asset('frontend/images/price-1.png') }}" alt="icon">
                                    </div>
                                    <div class="price-title-wrap">
                                        <h5 class="primary-color">Billed Monthly</h5>
                                        <h6 class="theme-color">Per Social Media Channel </h6>
                                        <h6 class="primary-color">{{$package['yearly_discount'] ? 'Save upto
                                            '.$package['yearly_discount'].'% when paid annually' : ''}}</h6>
                                    </div>
                                </div>
                                <h4>${{$package['monthly_price']}}</h4>
                            </div>
                            <ul class="price-list">
                                <li>Unlimited Users</li>
                                <li>Unlimited Posts</li>
                                <li>Schedule &amp; Plan Posts</li>
                                <li>Organic Content Analytics</li>
                                <li>Paid Advertising Analytics</li>
                                <li>Desktop &amp; Mobile App Access</li>
                                <li>Email &amp; Phone Support</li>

                            </ul>
                            <a href="{{route('register')}}" class="common-btn large-btn rounded-arrow"
                                type="submit">Register Now</a>
                            <!-- <button class="common-btn large-btn">REGISTER NOW</button> -->
                        </div>
                    </div>
                    <!--<div class="col-md-6">
                        <div class="price-card">
                            <div class="custom-card-header">
                                <div class="price-content">
                                    <div class="price-icon">
                                        <img src="{{ asset('frontend/images/price-2.png') }}" alt="icon">
                                    </div>
                                    <div class="price-title-wrap">
                                        <h5>Billed Yearly</h5>
                                        <h6>25% Discount</h6>
                                    </div>
                                </div>
                                <h4>$250</h4>
                            </div>
                            <ul class="price-list">
                                <li>Unlimited Users</li>
                                <li>Schedule Unlimited Posts</li>
                                <li>Organic Content Analytics</li>
                                <li>Paid Advertising Analytics </li>
                                <li>Email & Chat Support</li>
                            </ul>
                            <button class="common-btn large-btn">Start your plan</button>
                        </div>
                    </div>-->
                </div>
            </div>
        </section>
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
                        <li><a href="#pirce-section">Pricing</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <p class="footer-title">Helpful Links</p>
                    <ul class="footer-page-list">
                        <li><a href="{{ route('terms-conditions') }}">Terms & Condition</a></li>
                        <li><a href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('faqs') }}">FAQ</a></li>
                    </ul>
                </div>
                <div class="footer-subscribe">
                    <form action="{{ route('subscribe') }}" method="POST" id="subscriberFormSubmit">
                        @csrf
                        <p class="footer-title"> Subscribe More Info</p>
                        <input type="email" class="form-control" placeholder="Enter your mail" name="email" id="email"
                            required data-error-container="#email_error">
                        <span id="email_error"></span>
                        @if ($errors->has('email'))
                        <span class="invalid-feedback d-block">
                            <strong class="form-text text-danger">{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                        <button class="common-btn small-btn" type="submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>
        <footer>
            {{-- @dd($sitesetting) --}}
            <div class="container">
                <div class="footer-last-section-main">
                    @php
                    $string="{{YEAR}}";
                    @endphp
                    <p> {{ \Str::replace($string,\Carbon\Carbon::now()->format('Y'),$sitesetting['footer_text']) }}
                    </p>
                    <div class="image-main">
                        <a href="https://apps.apple.com/us/app/stacked-up/id1661067486" target="_blank"><img
                                src="{{asset('frontend/images/app-store.png')}}" alt=""></a>
                        <a href="https://play.google.com/store/apps/details?id=com.stackedup" target="_blank"><img
                                src="{{asset('frontend/images/play-store.png')}}" alt=""></a>
                    </div>

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
<script src="{{ asset('admin/plugins/jquery-validation/js/jquery.validate.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('admin/js/custom_validations.js') }}" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    $(document).ready(function(){
        $("#subscriberFormSubmit").validate({
            rules: {
                email: {
                    required: true,
                    not_empty: true,
                    email:true,
                    remote: {
                        url: "{{ route('check.subscriber.email') }}",
                        type: "post",
                        data: {
                            _token: function() {
                                return "{{csrf_token()}}"
                            },
                            email:function(){
                                return $('#email').val();
                            }
                        }
                    },
                },
            },
            messages: {
                email: {
                    required: "@lang('validation.required',['attribute'=>'Email'])",
                    not_empty: "@lang('validation.not_empty',['attribute'=>'Email'])",
                    email:"@lang('validation.email',['attribute'=>'Email'])",
                    remote:"@lang('validation.already_subscribed',['attribute'=>'Email'])"
                },
            },
            errorClass: 'invalid-feedback',
            errorElement: 'span',
            highlight: function (element) {
                $(element).addClass('is-invalid');
                $(element).siblings('label').addClass('text-danger'); // For Label
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
                $(element).siblings('label').removeClass('text-danger'); // For Label
            },
            errorPlacement: function (error, element) {
                if (element.attr("data-error-container")) {
                    error.appendTo(element.attr("data-error-container"));
                } else {
                    error.insertAfter(element);
                }
            }
        });
        $('#subscriberFormSubmit').submit(function () {
            if ($(this).valid()) {
                event.preventDefault();
                $("input[type=submit], input[type=button], button[type=submit]").prop("disabled", "disabled");
                $.ajax({
                    url: "{{route('subscribe')}}",
                    type: "POST",
                    dataType: "json",
                    data:{
                        "_token": "{{ csrf_token() }}",
                        email: $('#email').val(),
                    },
                    success: function (success) {
                        Swal.fire({
                            text: 'Thank you for Subscribing the Stackedup,',
                            icon: 'success',
                            timer: 2500,
                            showConfirmButton:false,
                        });
                        $('#email').val('');
                    },
                });
            } else {
                return false;
            }
        });
    });
</script>
