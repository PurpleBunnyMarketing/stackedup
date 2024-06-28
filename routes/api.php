<?php

use App\Http\Controllers\AnalyticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['namespace' => 'v1', 'prefix' => 'v1'], function () {

    /* User Register API */
    Route::post('register', 'UserController@register')->name('register');
    /* User Login API */
    Route::post('login', 'UserController@login')->name('login');
    /* Send OTP API */
    Route::post('send-otp', 'UserController@sendOTP')->name('send-otp');
    /* Verify OTP */
    Route::post('verify-otp', 'UserController@verifyOTP')->name('verify-otp');
    /* Forgot Password API */
    Route::post('forgot-password', 'UserController@forgotPassword')->name('forgot-password');
    /* Contact Us */
    Route::post('contact-us', 'GeneralController@contactUs');
    /* FAQ*/
    Route::get('faqs', 'GeneralController@faqs');
    /* FAQ*/
    Route::get('package-list', 'GeneralController@packageList');
    /* CMS pages*/
    Route::get('cms-pages', 'GeneralController@cmsPage')->name('cms-pages');

    /* is app update */
    Route::post('is-app-update', 'UserController@isAppUpdate')->name('is-app-update');
});

Route::group(['namespace' => 'v1', 'prefix' => 'v1', 'middleware' => ['auth:api', 'user_validate']], function () {
    /* User Logout APfI */
    Route::post('logout', 'UserController@logout')->name('logout');

    /* User home API */
    Route::post('home', 'HomeController@dashboard')->name('home');

    /* get profile Detail API*/
    Route::get('get-profile', 'UserController@getProfile')->name('get-profile');

    /* edit profile API*/
    Route::post('edit-profile', 'UserController@editProfile')->name('edit-profile');

    /*Edit Profile Photo API */
    Route::post('edit-profile-photo', 'UserController@editProfilePhoto')->name('edit-profile-photo');

    /* Change Password API */
    Route::post('change-password', 'UserController@changePassword')->name('change-password');

    /* Staff List*/
    Route::post('staff-list', 'StaffController@staffList')->name('staff-list');

    /* Staff Delte*/
    Route::post('staff/delete', 'StaffController@staffDestroy')->name('staff-destroy');


    /* Add Post*/
    Route::post('add-post', 'PostController@addPost')->name('add-post');


    /* Media List*/
    Route::get('media-list', 'GeneralController@mediaList')->name('media-list');

    /* Post List*/
    Route::post('post-list', 'PostController@postList')->name('post-list');

    /* Post Now*/
    Route::post('post-now', 'PostController@postNow')->name('post-now');

    /* Edit Post*/
    Route::post('edit-post', 'PostController@editPost')->name('edit-post');

    /* Delete Post*/
    Route::post('delete-post', 'PostController@deletePost')->name('delete-post');

    /* Staff Detail */
    Route::post('staff-detail', 'StaffController@staffDetail')->name('staff-detail');

    /* Delete Media Page from Profiele */
    Route::post('page-delete', 'UserController@deleteMediaPage')->name('page-delete');

    /* Get Analytic */
    Route::post('get-analytic', 'AnalyticController@getAnalytic')->name('get-analytic');
    Route::post('refresh-analytic', 'AnalyticController@refreshAnalytic')->name('refresh-analytic');

    // Get Linkedin Analytics Data
    // Route::post('linkedin/data/{type}', [AnalyticsController::class, 'LinkedinAnalytics'])->name('analytics.linkedin.ajax');

    // Get Linkedin Analytics Data
    Route::post('google-business/data', 'AnalyticController@googleBusinessAnalytics')->name('analytics.google-business.ajax');

    // Get Google Analytics Data
    Route::post('google-analytics/data', 'AnalyticController@googleAnalytics')->name('analytics.google-analytics');

    // Get Google Analytics Data
    Route::post('facebook_ads/data', 'AnalyticController@facebookAdsReporting')->name('analytics.facebook_ads');

    // Get Instagram Analytics Data
    Route::post('instagram/data', 'AnalyticController@instagramAnalytics')->name('analytics.instagram');

    // Get Google Ads Analytics Data
    Route::post('google_ads/data', 'AnalyticController@googleAdsAnalytics')->name('analytics.google_ads');

    //Subscription Plan
    Route::get('subscriptionPlan', 'UserController@currentSubscription')->name('subscriptionPlan');

    // contactUsDetail
    Route::get('contactUsDetail', 'GeneralController@contactUsDetail')->name('contactUsDetail');

    // deleteAccount
    Route::get('deleteAccount', 'UserController@deleteAccount')->name('deleteAccount');

    // check Subscription
    Route::get('checkIsSubscribed', 'UserController@checkIsSubscribed')->name('checkIsSubscribed');
});
// total_counts,organic_paid_new_followers,viewers,social_count,posts,follower-by-country,follower-by-company,follower-by-senority,follower-by-job-function,follower-by-industry
