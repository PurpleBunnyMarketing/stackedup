@php
$user = Auth::user();
@endphp
<!--begin::Header-->
<div id="kt_header" class="header header-fixed bg-dark">
    <!--begin::Container-->
    <div class="container d-flex align-items-stretch justify-content-between">
        <!--begin::Left-->
        <div class="d-flex align-items-stretch mr-3">
            <!--begin::Header Logo-->
            <div class="header-logo">
                <a href=" @if($user) {{ route('dashboard') }} @else {{ route('landing-page') }} @endif ">
                    <img alt="Logo" src="{{ asset('frontend/assets/media/images/logo.svg')}}"
                        class="logo-default max-h-40px" />
                </a>
            </div>
            <!--end::Header Logo-->
            <!--begin::Header Menu Wrapper-->
            <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
                <!--begin::Header Menu-->
                <div id="kt_header_menu"
                    class="header-menu header-menu-left header-menu-mobile header-menu-layout-default ">
                    <!--begin::Header Nav-->
                    <ul class="menu-nav">
                        @if( ($user) )
                        <li class="menu-item menu-item-submenu menu-item-rel {{ Route::is('dashboard') ? 'menu-item-open menu-item-here' : '' }}"
                            aria-haspopup="true">
                            <a href="{{ route('dashboard')}}" class="menu-link ">
                                <span class="menu-text">Home</span>
                                <i class="menu-arrow"></i>
                            </a>
                        </li>
                        <li class="menu-item menu-item-submenu menu-item-rel {{ Route::is('posts.*') ? 'menu-item-open menu-item-here' : '' }}"
                            aria-haspopup="true">
                            <a href="{{ route('posts.index')}}" class="menu-link ">
                                <span class="menu-text">Posts</span>
                                <span class="menu-desc"></span>
                                <i class="menu-arrow"></i>
                            </a>
                        </li>
                        <li class="menu-item menu-item-submenu menu-item-rel" aria-haspopup="true">
                            <a href="{{ route('analytics.index') }}"
                                class="menu-link analytic-link">
                                <span class="menu-text">Analytics</span>
                                <span class="menu-desc"></span>
                                <i class="menu-arrow"></i>
                            </a>
                        </li>

                        @if($user->type == 'company' || $user->type == 'admin')
                        <li class="menu-item menu-item-submenu menu-item-rel {{ Route::is('staff.*') ? 'menu-item-open menu-item-here' : '' }}"
                            aria-haspopup="true">
                            <a href="{{ route('staff.index') }}" class="menu-link ">
                                <span class="menu-text">Staff</span>
                                <span class="menu-desc"></span>
                                <i class="menu-arrow"></i>
                            </a>
                        </li>
                        @endif
                        @endif
                    </ul>
                    <!--end::Header Nav-->
                </div>
                <!--end::Header Menu-->
            </div>
            <!--end::Header Menu Wrapper-->
        </div>
        <!--end::Left-->
        <!--begin::Topbar-->
        <div class="topbar">
            <!--begin::User-->
            <div class="dropdown">
                <!--begin::Toggle-->
                @if ($user)
                <div class="topbar-item" data-toggle="dropdown" data-offset="0px,0px">
                    <div
                        class="btn btn-icon btn-hover-transparent-white d-flex align-items-center btn-lg px-md-2 w-md-auto">
                        <span class="opacity-70 font-weight-bold font-size-base d-none d-md-inline mr-1">Hi,</span>
                        <span class="opacity-90 font-weight-bolder font-size-base d-none d-md-inline mr-4">{{
                            ($user) ? $user->full_name : "" }}</span>
                        <span class="symbol symbol-35">
                            <span class="symbol-label text-white font-size-h5 font-weight-bold bg-white-o-30">

                                @if ($user->profile_photo)
                                <img src={{generateURL($user->profile_photo)}} height="35" width="35"
                                class="rounded" alt="profile image"
                                />
                                @else
                                <img src="{{ asset('assets/media/users/blank.png')}}" height="35" width="35"
                                    class="rounded" alt="profile image">
                                @endif

                            </span>
                        </span>
                    </div>
                </div>
                @else
                <div class="topbar-item" data-offset="0px,0px">
                    <div
                        class="btn btn-icon btn-hover-transparent-white d-flex align-items-center btn-lg px-md-2 w-md-auto">
                        <a href="{{route('login')}}"
                            class="menu-link text-white font-weight-bold font-size-base d-none d-md-inline mr-1">SignIn</a>
                    </div>
                </div>
                @endif
                <!--end::Toggle-->
                <!--begin::Dropdown-->
                @if ($user)
                <div class="dropdown-menu p-0 m-0 dropdown-menu-right dropdown-menu-anim-up dropdown-menu-lg p-0">
                    <!--begin::Nav-->
                    <div class="navi navi-spacer-x-0 pt-5">
                        <!--begin::Item-->
                        <a href="{{ route('profile') }}" class="navi-item px-8">
                            <div class="navi-link">
                                <div class="navi-icon mr-2">
                                    <i class="flaticon2-calendar-3 text-success"></i>
                                </div>
                                <div class="navi-text">
                                    <div class="font-weight-bold">My Profile</div>
                                </div>
                            </div>
                        </a>
                        @if ($user->type == 'company' && $user->is_active == 'y' ||
                        $user->type == 'admin')
                        {{-- @if ($user->type == 'company' && $user->is_active == 'y' && $user->is_subscribe == 'y'
                        || $user->type == 'admin' )
                        --}}
                        <a href="{{ route('company.details') }}" class="navi-item px-8">
                            <div class="navi-link">
                                <div class="navi-icon mr-2">
                                    <i class="far fa-building text-info"></i>
                                </div>
                                <div class="navi-text">
                                    <div class="font-weight-bold">Company Details</div>
                                </div>
                            </div>
                        </a>
                        @endif
                        @if ($user->type == 'company' && $user->is_active == 'y' || $user->type == 'admin')
                        <a href="{{ route('social-media-list') }}" class="navi-item px-8">
                            <div class="navi-link">
                                <div class="navi-icon mr-2">
                                    <i class="flaticon2-calendar-3 text-success"></i>
                                </div>
                                <div class="navi-text">
                                    <div class="font-weight-bold">Manage Social Media</div>
                                </div>
                            </div>
                        </a>
                        @endif
                        @if ($user->type == 'company' && $user->is_active == 'y' && $user->is_subscribe == 'y' ||
                        $user->type == 'admin')
                        <a href="{{ route('active-subscription') }}" class="navi-item px-8">
                            <div class="navi-link">
                                <div class="navi-icon mr-2">
                                    <i class="flaticon2-calendar-3 text-success"></i>
                                </div>
                                <div class="navi-text">
                                    <div class="font-weight-bold">Current Subscription</div>
                                </div>
                            </div>
                        </a>
                        @endif

                        @if ($user->type == 'company' && $user->is_active == 'y' && $user->is_subscribe == 'n' ||
                        $user->type == 'admin')
                        <a href="{{ route('media-page-list') }}" class="navi-item px-8">
                            <div class="navi-link">
                                <div class="navi-icon mr-2">
                                    <i class="flaticon2-calendar-3 text-success"></i>
                                </div>
                                <div class="navi-text">
                                    <div class="font-weight-bold">Purchase Subscription</div>
                                </div>
                            </div>
                        </a>
                        @endif
                        <!--end::Item-->
                        <!--begin::Item-->
                        <a href="{{ route('changePassword') }}" class="navi-item px-8">
                            <div class="navi-link">
                                <div class="navi-icon mr-2">
                                    <i class="fa la-key text-danger"></i>
                                </div>
                                <div class="navi-text">
                                    <div class="font-weight-bold">Change Password</div>
                                </div>
                            </div>
                        </a>
                        <!--end::Item-->
                        <!--begin::Item-->
                        {{-- <button data-target-href="{{ route('logout') }}"
                            class="btn btn-icon btn-light btn-hover-danger btn-sm action-logout" id="logout"
                            title="Logout">Logout</button> --}}

                        <a href="javascript:;" data-target-href="{{ route('logout') }}"
                            class="navi-item px-8 action-logout" id="logout">
                            <div class="navi-link">
                                <div class="navi-icon mr-2">
                                    <i class="fas fa-sign-out-alt text-warning"></i>
                                </div>
                                <div class="navi-text">

                                    <div class="font-weight-bold">Logout</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!--end::Nav-->
                </div>
                @endif
                <!--end::Dropdown-->
            </div>
            <!--end::User-->
        </div>
        <!--end::Topbar-->
    </div>
    <!--end::Container-->
</div>

<!--end::Header-->

@push('extra-js-scripts')
<script>
    $(document).ready(function() {
    $('.analytic-link').click(function(){
        addOverlay();
    })
});
</script>
@endpush