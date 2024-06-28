<?php

use App\Mail\StaffAddMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//landing page
Route::get('/', function () {
    return view('frontend.pages.general.index');
})->name('landing-page');

Route::get('/payment', 'PagesController@payment');
Route::post('payment/create-subscription', 'StripeController@createSubscription');
Route::get('/login', function () {
    // dd(getUniqueString('user_media'));
    return redirect()->route('login');
});
/* If javascript is disable */
Route::get('no-script', function () {
    return view('errors.no-script');
})->name('no-script');

/* If cookie is disable */
Route::get('no-cookie', function () {
    return view('errors.no-cookie');
})->name('no-cookie');

Auth::routes();
Route::post('send-otp', 'UserController@sendOTP')->name('send-otp');
Route::post('resend-otp', 'UserController@resendOtp')->name('resend-otp');
Route::get('otp/{phone_code}/{mobile_no}', 'UserController@otpScreen')->name('otp');
Route::get('otp-expiry', 'UserController@expiryOtp')->name('otp-expiry');
Route::post('verify-otp', 'UserController@verifyOTP')->name('verify-otp');
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
// Route::get('/', 'HomeController@index')->name('welcome');

Route::group(['prefix' => 'admin'], function () {
    Route::get('login', 'AdminAuth\LoginController@showLoginForm')->name('admin.login');
    Route::post('login', 'AdminAuth\LoginController@login');
    Route::get('logout', 'AdminAuth\LoginController@logout')->name('admin.logout');

    Route::post('/password/email', 'AdminAuth\ForgotPasswordController@sendResetLinkEmail')->name('admin.password.request');
    Route::post('/password/reset', 'AdminAuth\ResetPasswordController@reset')->name('admin.password.email');
    Route::get('/password/reset', 'AdminAuth\ForgotPasswordController@showLinkRequestForm')->name('admin.password.reset');
    Route::get('/password/reset/{token}/{email?}', 'AdminAuth\ResetPasswordController@showResetForm');
});

Route::post('check-email', 'UtilityController@checkEmail')->name('check.email');
Route::post('check-subscriberEmail', 'UtilityController@checkSubscriber')->name('check.subscriber.email');
Route::post('exist-email', 'UtilityController@checkEmailExist')->name('exist.email');

Route::post('check-mobile-no', 'UtilityController@checkMobileNo')->name('check.mobile_no');
Route::post('exist-mobile-no', 'UtilityController@checkContact')->name('exist.mobile_no');
Route::post('check-abn', 'UtilityController@checkAbnExists')->name('check.abn');

Route::get('terms-conditions', 'PagesController@termsConditions')->name('terms-conditions');
Route::get('privacy-policy', 'PagesController@privacyPolicy')->name('privacy-policy');
Route::get('about-us', 'PagesController@aboutUs')->name('about-us');
Route::get('faqs', 'PagesController@faqs')->name('faqs');
Route::post('chkuser', 'UserController@checkUser')->name('checkUser');
Route::post('/subscribe', 'SubscriberController@subscribe')->name('subscribe');

// Contact Us
Route::get('contact', 'PagesController@contact')->name('contact');

Route::group(['middleware' => ['auth', 'user_validate']], function () {
    // Route::middleware('user_paid_or_not')->group(function () {
    // ------------------------X(Twitter)--------------------------------------------------
    Route::controller(TwitterController::class)->group(function () {
        Route::get('auth/twitter', 'redirectToTwitter')->name('auth.twitter');
        Route::get('auth/twitter/callback', 'handleTwitterCallback');
    });

    // ------------------------Facebook--------------------------------------------------
    Route::controller(FacebookController::class)->group(function () {
        Route::get('auth/facebook', 'redirectToFacebook')->name('auth.facebook');
        Route::get('auth/facebook/callback', 'handleFaceBookCallback');
    });
    // ------------------------Facebook Ads--------------------------------------------------
    Route::controller(FacebookAdsController::class)->group(function () {
        Route::get('auth/facebook_ads', 'redirectToFacebookAds')->name('auth.facebook_ads');
        Route::get('auth/facebook_ads/callback', 'handleFaceBookAdsCallback');
    });
    // ------------------------Google My Business----------------------------------------
    Route::controller(GoogleController::class)->group(function () {
        Route::get('auth/google', 'redirectToGoogle')->name('auth.google');
        Route::get('auth/google/callback', 'handleGoogleCallback');
    });
    // ------------------------Google Analytics----------------------------------------
    Route::controller(GoogleAnalyticsController::class)->group(function () {
        Route::get('auth/google_analytics', 'redirectToGoogleAnalytics')->name('auth.google.analytics');
        Route::get('auth/google_analytics/callback', 'handleGoogleAnalyticsCallback');
    });
    // ------------------------Google Ads----------------------------------------
    Route::controller(GoogleAdsController::class)->group(function () {
        Route::get('auth/google_ads', 'redirectToGoogleAds')->name('auth.google.ads');
        Route::get('auth/google_ads/callback', 'handleGoogleAdsCallback');
    });

    // ------------------------Linkedin--------------------------------------------------
    Route::controller(LinkedInController::class)->group(function () {
        Route::get('auth/linkedin', 'linkedinRedirect')->name('auth.linkedin');
        Route::get('auth/linkedin/callback', 'linkedinCallback');
    });
    // ------------------------Instagram--------------------------------------------------
    Route::controller(InstagramController::class)->group(function () {
        Route::get('auth/instagram', 'redirectToInstagram')->name('auth.instagram');
        Route::get('auth/instagram/callback', 'handleInstagramCallback');
    });

    //dashboard
    Route::get('home', 'PagesController@home')->name('home');
    Route::get('dashboard', 'PagesController@dashboard')->name('dashboard');
    Route::post('filter/posts', 'PagesController@filterPosts')->name('filter.posts');
    Route::post('user/subscribed', 'StripeController@checkSubscription')->name('user.subscribed');

    //post
    Route::post('paginate/posts', 'PostController@paginatePosts')->name('paginate.posts');
    Route::post('post-now', 'PostController@postNow')->name('post-now');
    Route::get('posts/getimages', 'PostController@getImages')->name('posts.getimages');
    Route::post('posts/storeimage', 'PostController@storeimage')->name('posts.storeimage');
    Route::post('posts/store_custom_thumbnail', 'PostController@storeCustomThumbnail')->name('posts.store.custom_thumbnail');
    Route::post('posts/deleteimage', 'PostController@deleteimage')->name('posts.deleteimage');
    Route::post('posts/preview', 'PostController@postPreview')->name('posts.postPreview');
    Route::resource('posts', 'PostController');

    //Staff
    Route::post('staff/storeimage', 'StaffController@storeimage')->name('staff.storeimage');
    Route::post('staff/deleteimage', 'StaffController@deleteimage')->name('staff.deleteimage');
    Route::post('paginate/staff', 'StaffController@paginateStaff')->name('paginate.staff');
    Route::post('change/staff/status', 'StaffController@changeStatus')->name('change.staff.status');
    Route::resource('staff', 'StaffController');

    // Change Password
    Route::get('change-password', 'UserController@changePassword')->name('changePassword');
    Route::post('change-password', 'UserController@updatePassword')->name('updatePassword');
    Route::delete('user/delete/user_media', 'UserController@deleteUserMedia')->name('user_media.delete');


    // Contact Us
    Route::get('contact', 'PagesController@contact')->name('contact');
    Route::post('contact', 'PagesController@contactStore');
    Route::get('media-page-list', 'MediaPagesController@list')->name('media-page-list')->middleware('revalidate');
    Route::get('check-pages', 'MediaPagesController@checkPages')->name('check-pages');
    Route::post('checkout', 'TransactionController@checkout')->name('checkout')->middleware('revalidate');
    Route::post('media-page/payment-success', 'TransactionController@paymentSuccess')->name('media-page.payment-success')->middleware('revalidate');

    //analytics
    Route::post('get-next-tweet-feed', 'AnalyticsController@nextTweetFeed')->name('analytics.get-next-tweet-feed');
    Route::get('refresh-linkedin-post', 'AnalyticsController@LinkedInPost')->name('analytics.refresh-linkedin-post');
    Route::get('refresh-linkedin', 'AnalyticsController@LinkedInEngagment')->name('analytics.refresh-linkedin');
    Route::get('refresh-twitter', 'AnalyticsController@TwitterAnalitics')->name('analytics.refresh-twitter');
    Route::get('refresh-twitter-feed', 'AnalyticsController@TwitterFeed')->name('analytics.refresh-twitter-feed');
    // Route::get('refresh-facebook/{type}', 'AnalyticsController@FacebookAnalitics')->name('analytics.refresh-facebook');
    // Route::get('refresh-facebook-post', 'AnalyticsController@FacebookPosts')->name('analytics.refresh-facebook-post');
    Route::post('get-media-page', 'AnalyticsController@getPages')->name('analytics.get-media-page');
    Route::resource('analytics', 'AnalyticsController');

    Route::get('media-page-list', 'MediaPagesController@list')->name('media-page-list')->middleware('revalidate');
    Route::get('check-pages', 'MediaPagesController@checkPages')->name('check-pages')->middleware('revalidate');
    Route::post('checkout', 'TransactionController@checkout')->name('checkout')->middleware('revalidate');
    Route::post('media-page/payment-success', 'TransactionController@paymentSuccess')->name('media-page.payment-success')->middleware('revalidate');

    //analytics
    Route::post('get-next-tweet-feed', 'AnalyticsController@nextTweetFeed')->name('analytics.get-next-tweet-feed');
    Route::get('refresh-linkedin-post', 'AnalyticsController@LinkedInPost')->name('analytics.refresh-linkedin-post');
    Route::get('refresh-linkedin', 'AnalyticsController@LinkedInEngagment')->name('analytics.refresh-linkedin');
    Route::get('refresh-twitter', 'AnalyticsController@TwitterAnalitics')->name('analytics.refresh-twitter');
    Route::get('refresh-twitter-feed', 'AnalyticsController@TwitterFeed')->name('analytics.refresh-twitter-feed');
    Route::get('filter-analytic', 'AnalyticsController@filterAnalytic')->name('analytics.filter-analytic');
    Route::resource('analytics', 'AnalyticsController');

    /* Facebook Analytic */
    Route::post('facebook/data/{type}', 'AnalyticsController@FacebookAnalitics')->name('analytics.facebook.ajax');

    /** Get Linkedin Anaylitics */
    Route::post('linkedin/data/{type}', 'AnalyticsController@LinkedinAnalytics')->name('analytics.linkedin.ajax');

    /** Get Google Business Anaylitics */
    Route::post('google-business/data/{type}', 'AnalyticsController@googleMyBusinessAnalyticsData')->name('analytics.google-business.ajax');
    Route::post('google-analytics/{type}', 'AnalyticsController@googleAnalytics')->name('analytics.google-analytics.ajax');

    // Facebook Ads Analytics
    Route::post('facebook_ads/{type}', 'AnalyticsController@facebookAdsReporting')->name('analytics.facebook_ads.ajax');

    // Instagram Analytics
    Route::post('instagram/{type}', 'AnalyticsController@instagramAnalytics')->name('analytics.instagram.ajax');

    /** Get Google Ads Anaylitics */
    Route::post('google-ads/data/{type}', 'AnalyticsController@getGoogleAdsReporting')->name('analytics.google-ads.ajax');

    //});
    // Cancel subscription
    Route::delete('cancel-subscription', 'MediaPagesController@cancelSubscription')->name('cancel-subscription');


    //Active Subscription
    Route::get('active-subscription', 'MediaPagesController@activeSubscription')->name('active-subscription');
    // Cancel subscription
    Route::delete('cancel-subscription', 'MediaPagesController@cancelSubscription')->name('cancel-subscription');
    // check coupon code
    Route::get('exist-couponCode', 'MediaPagesController@checkCouponCode')->name('exist.coupon');

    // User Edit Profile
    Route::get('profile', 'UserController@profile')->name('profile');
    Route::get('edit-profile', 'UserController@editProfile')->name('edit-profile');
    Route::post('edit-profile', 'UserController@updateProfile')->name('upadte-profile');

    Route::get('package-list', 'PagesController@packageList')->name('package-list');

    // Social Media List
    Route::get('social-media-list', 'PagesController@socialMediaList')->name('social-media-list');

    Route::get('buy-now/{id}', 'StripeController@buyNow')->name('buy-now')->middleware('revalidate');
    Route::post('payment', 'StripeController@payment')->name('payment')->middleware('revalidate');
    Route::post('payment-success', 'StripeController@paymentSuccess')->name('payment-success')->middleware('revalidate');

    // Company details
    Route::get('company/details', 'CompanyDetailsController@index')->name('company.details');
    Route::get('company/details/edit', 'CompanyDetailsController@edit')->name('company.details.edit');
    Route::post('company/details/update', 'CompanyDetailsController@update')->name('company.details.update');

    Route::post('/check/token-expiry', 'UtilityController@checkMediaTokenExpiry')->name('check.token_expiry');

    Route::post('/check_instagram_media_page', 'UtilityController@checkInstagramMediaPage')->name('check.instagram_media_page');
});


Route::get('/send-mail', function () {
    try {
        $data =  [
            'email'     => "abc@example.com",
            'password'  => "abc",
            'full_name' => "abc",
            'company'   => "abc",
        ];
        Mail::to('maulik.vamja@yudiz.com')->send(new StaffAddMail($data));
    } catch (Exception $e) {
        dd($e->getMessage(), 'error');
    }
    dd('mail send');
});
