<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['revalidate']], function () {
    Route::get('/home', function () {
        return redirect(route('admin.dashboard.index'));
    })->name('home');

    // Profile
    Route::get('profile/', 'Admin\PagesController@profile')->name('profile-view');
    Route::post('profile/update', 'Admin\PagesController@updateProfile')->name('profile.update');
    Route::put('change/password', 'Admin\PagesController@updatePassword')->name('update-password');

    // Quick Link
    Route::get('quickLink', 'Admin\PagesController@quickLink')->name('quickLink');
    Route::post('link/update', 'Admin\PagesController@updateQuickLink')->name('update-quickLink');

    Route::get('/permission', function () {
        return serialize(getPermissions('admin'));
    });
});

Route::group(['namespace' => 'Admin', 'middleware' => ['check_permit', 'revalidate']], function () {

    /* Dashboard */
    Route::get('/', 'PagesController@dashboard')->name('dashboard.index');
    Route::get('/dashboard', 'PagesController@dashboard')->name('dashboard.index');

    /* User */
    Route::get('users/listing', 'UsersController@listing')->name('users.listing');
    Route::resource('users', 'UsersController');


    /* Role Management */
    Route::get('roles/listing', 'AdminController@listing')->name('roles.listing');
    Route::resource('roles', 'AdminController');



    /* Country Management*/
    Route::get('countries/listing', 'CountryController@listing')->name('countries.listing');
    Route::resource('countries', 'CountryController');

    /* State Management*/
    Route::get('states/listing', 'StateController@listing')->name('states.listing');
    Route::resource('states', 'StateController');

    /* City Management*/
    Route::get('cities/listing', 'CityController@listing')->name('cities.listing');
    Route::resource('cities', 'CityController');

    /* CMS Management*/
    Route::get('pages/listing', 'CmsPagesController@listing')->name('pages.listing');
    Route::resource('pages', 'CmsPagesController');

    /* Site Configuration */
    Route::get('settings', 'PagesController@showSetting')->name('settings.index');
    Route::post('change-setting', 'PagesController@changeSetting')->name('settings.change-setting');

    /* app update settings */
    Route::get('update-settings', 'AppUpdateSettingController@index')->name('update-settings.index');
    Route::post('app-change-setting', 'AppUpdateSettingController@store')->name('update-settings.change-setting');

    /*  FAQs */
    Route::get('faqs/listing', 'FAQsController@listing')->name('faqs.listing');
    Route::resource('faqs', 'FAQsController');

    /*  Manage Packages */
    Route::get('packages/listing', 'PackageController@listing')->name('packages.listing');
    Route::resource('packages', 'PackageController');

    /* Transaction Management */
    Route::get('transactions/listing', 'TransactionController@listing')->name('transactions.listing');
    Route::resource('transactions', 'TransactionController');

    /*  Manage Coupons */
    Route::get('coupons/listing', 'CouponController@listing')->name('coupons.listing');
    Route::resource('coupons', 'CouponController');

    Route::get('subscribers/listing', 'SubscriberController@listing')->name('subscribers.listing');
    Route::resource('subscribers', 'SubscriberController')->only(['index', 'destroy']);
});

//User Exception
Route::get('users-error-listing', 'Admin\ErrorController@listing')->name('error.listing');
//Chart routes
Route::get('register-users-chart', 'Admin\ChartController@getRegisterUser')->name('users.registerchart');
Route::get('active-deactive-users-chart', 'Admin\ChartController@getActiveDeactiveUser')->name('users.activeDeactiveChart');

Route::post('check-email', 'UtilityController@checkEmail')->name('check.email');
Route::post('check-contact', 'UtilityController@checkContact')->name('check.contact');

Route::post('summernote-image-upload', 'Admin\SummernoteController@imageUpload')->name('summernote.imageUpload');
Route::post('summernote-media-image', 'Admin\SummernoteController@mediaDelete')->name('summernote.mediaDelete');

Route::post('check-title', 'UtilityController@checkTitle')->name('check.title');
Route::post('profile/check-password', 'UtilityController@profilecheckpassword')->name('profile.check-password');
