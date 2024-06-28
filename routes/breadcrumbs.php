<?php

use Illuminate\Support\Facades\Auth;
use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;

// Dashboard ---------------------------------------------------------------------------------------------------------------------------------------------------
Breadcrumbs::register('dashboard', function ($breadcrumbs) {
    $breadcrumbs->push('Dashboard', route(Auth::getDefaultDriver() . '.dashboard.index'));
});

// Users -------------------------------------------------------------------------------------------------------------------------------------------------------
Breadcrumbs::register('users_list', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('Users', route(Auth::getDefaultDriver() . '.users.index'));
});

// Quick Links
Breadcrumbs::register('quick_link', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(__('Mange Quick Link'), route('admin.quickLink'));
});
// Profile
Breadcrumbs::register('my_profile', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(__('Manage Account'), route('admin.profile-view'));
});

Breadcrumbs::register('users_create', function ($breadcrumbs) {
    $breadcrumbs->parent('users_list');
    $breadcrumbs->push('Add New User', route(Auth::getDefaultDriver() . '.users.create'));
});

Breadcrumbs::register('users_update', function ($breadcrumbs, $id) {
    $breadcrumbs->parent('users_list');
    $breadcrumbs->push('Edit User', route(Auth::getDefaultDriver() . '.users.edit', $id));
});
Breadcrumbs::register('users_show', function ($breadcrumbs, $id) {
    $breadcrumbs->parent('users_list');
    $breadcrumbs->push(__('View User'), route(Auth::getDefaultDriver() . '.users.show', $id));
});

// Role Management -------------------------------------------------------------------------------------------------------------------------------------------------------
Breadcrumbs::register('roles_list', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('Roles', route(Auth::getDefaultDriver() . '.roles.index'));
});
Breadcrumbs::register('roles_create', function ($breadcrumbs) {
    $breadcrumbs->parent('roles_list');
    $breadcrumbs->push('Add New Role', route(Auth::getDefaultDriver() . '.roles.create'));
});
Breadcrumbs::register('roles_update', function ($breadcrumbs, $id) {
    $breadcrumbs->parent('roles_list');
    $breadcrumbs->push(__('Edit Role'), route('admin.roles.edit', $id));
});

// faqs -------------------------------------------------------------------------------------------------------------------------------------------------------
Breadcrumbs::register('faqs_list', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('faqs', route(Auth::getDefaultDriver() . '.faqs.index'));
});
Breadcrumbs::register('faqs_create', function ($breadcrumbs) {
    $breadcrumbs->parent('faqs_list');
    $breadcrumbs->push('Add New Faqs', route(Auth::getDefaultDriver() . '.faqs.create'));
});

Breadcrumbs::register('faqs_update', function ($breadcrumbs, $id) {
    $breadcrumbs->parent('faqs_list');
    $breadcrumbs->push('Edit Faqs', route(Auth::getDefaultDriver() . '.faqs.edit', $id));
});

// countries -------------------------------------------------------------------------------------------------------------------------------------------------------
Breadcrumbs::register('countries_list', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('Countries', route(Auth::getDefaultDriver() . '.countries.index'));
});
Breadcrumbs::register('countries_create', function ($breadcrumbs) {
    $breadcrumbs->parent('countries_list');
    $breadcrumbs->push('Add New Country', route(Auth::getDefaultDriver() . '.countries.create'));
});

Breadcrumbs::register('countries_update', function ($breadcrumbs, $id) {
    $breadcrumbs->parent('countries_list');
    $breadcrumbs->push('Edit Country', route(Auth::getDefaultDriver() . '.countries.edit', $id));
});

// states -------------------------------------------------------------------------------------------------------------------------------------------------------
Breadcrumbs::register('states_list', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('States', route(Auth::getDefaultDriver() . '.states.index'));
});
Breadcrumbs::register('states_create', function ($breadcrumbs) {
    $breadcrumbs->parent('states_list');
    $breadcrumbs->push('Add New State', route(Auth::getDefaultDriver() . '.states.create'));
});

Breadcrumbs::register('states_update', function ($breadcrumbs, $id) {
    $breadcrumbs->parent('states_list');
    $breadcrumbs->push('Edit State', route(Auth::getDefaultDriver() . '.states.edit', $id));
});

// cities -------------------------------------------------------------------------------------------------------------------------------------------------------
Breadcrumbs::register('cities_list', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('Cities', route(Auth::getDefaultDriver() . '.cities.index'));
});
Breadcrumbs::register('cities_create', function ($breadcrumbs) {
    $breadcrumbs->parent('cities_list');
    $breadcrumbs->push('Add New City', route(Auth::getDefaultDriver() . '.cities.create'));
});

Breadcrumbs::register('cities_update', function ($breadcrumbs, $id) {
    $breadcrumbs->parent('cities_list');
    $breadcrumbs->push('Edit City', route(Auth::getDefaultDriver() . '.cities.edit', $id));
});

// CMS Pages ---------------------------------------------------------------------------------------------------------------------------------------------------
Breadcrumbs::register('cms_list', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(__('CMS Pages'), route('admin.pages.index'));
});
Breadcrumbs::register('cms_update', function ($breadcrumbs, $id) {
    $breadcrumbs->parent('cms_list');
    $breadcrumbs->push(__('Edit CMS Page'), route('admin.pages.edit', $id));
});
//site configuartion
Breadcrumbs::register('site_setting', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push(__('Site Configuration'), route('admin.settings.index'));
});


// Packages -------------------------------------------------------------------------------------------------------------------------------------------------------
Breadcrumbs::register('packages_list', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('Packages', route(Auth::getDefaultDriver() . '.packages.index'));
});
Breadcrumbs::register('packages_create', function ($breadcrumbs) {
    $breadcrumbs->parent('packages_list');
    $breadcrumbs->push('Add New Packages', route(Auth::getDefaultDriver() . '.packages.index'));
});
Breadcrumbs::register('packages_update', function ($breadcrumbs, $id) {
    $breadcrumbs->parent('packages_list');
    $breadcrumbs->push('Edit Package', route(Auth::getDefaultDriver() . '.packages.edit', $id));
});
Breadcrumbs::register('packages_show', function ($breadcrumbs, $id) {
    $breadcrumbs->parent('packages_list');
    $breadcrumbs->push(__('View Package'), route(Auth::getDefaultDriver() . '.packages.show', $id));
});

// Transaction -------------------------------------------------------------------------------------------------------------------------------------------------------
Breadcrumbs::register('transaction_list', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('Transactions', route(Auth::getDefaultDriver() . '.transactions.index'));
});

// Coupons  -------------------------------------------------------------------------------------------------------------------------------------------------------

Breadcrumbs::register('coupons_list', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('Coupons', route(Auth::getDefaultDriver() . '.coupons.index'));
});
Breadcrumbs::register('coupons_create', function ($breadcrumbs) {
    $breadcrumbs->parent('coupons_list');
    $breadcrumbs->push('Add New Coupons', route(Auth::getDefaultDriver() . '.coupons.index'));
});
Breadcrumbs::register('coupons_update', function ($breadcrumbs, $id) {
    $breadcrumbs->parent('coupons_list');
    $breadcrumbs->push('Edit Coupon', route(Auth::getDefaultDriver() . '.coupons.edit', $id));
});
Breadcrumbs::register('coupons_show', function ($breadcrumbs, $id) {
    $breadcrumbs->parent('coupons_list');
    $breadcrumbs->push(__('View Coupon'), route(Auth::getDefaultDriver() . '.coupons.show', $id));
});
// Subscribers  -------------------------------------------------------------------------------------------------------------------------------------------------------

Breadcrumbs::register('subscribers_list', function ($breadcrumbs) {
    $breadcrumbs->parent('dashboard');
    $breadcrumbs->push('Subscribers', route(Auth::getDefaultDriver() . '.subscribers.index'));
});
