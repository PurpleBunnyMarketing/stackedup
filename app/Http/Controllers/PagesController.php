<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\General\ChangePassword;
use App\Http\Requests\Admin\General\ProfileUpdate;
use App\Mail\ContactUsConfirmationMail;
use App\Mail\ContactUsMail;
use App\Models\CmsPage;
use App\Models\ContactUs;
use App\Models\Faqs;
use App\Models\Media;
use App\Models\Post;
use App\Models\Package;
use App\Models\Payment;
use App\Models\QuickLink;
use App\Models\Role;
use App\Models\Setting;
use App\Models\SocialMediaDetail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Illuminate\Support\Facades\DB;
use App\Models\MediaPagePayments;
use App\Models\MediaPage;
use App\Models\UserMedia;

class PagesController extends Controller
{

    public function dashboard()
    {
        $user = Auth::user();
        $parent_ids = null;
        $schedulePosts = collect([]);
        $posts = collect([]);

        $parent_id = $user->parent_id ?? "";
        if (!empty($parent_id)) {
            $parent_user = User::where('id', $parent_id)->first();
            $parent_ids = $parent_user->parents->pluck('id')->toArray();
            $parent_ids[] = $parent_user->id;
        } else {
            $parent_ids = $user->parents->pluck('id')->toArray();
            $parent_ids[] = $user->id;;
        }
        $dateOfLast30Day = \Carbon\Carbon::today()->subDays(30);
        $postsCount = Post::whereIn('user_id', $parent_ids)->whereNull('schedule_date_time')->where('created_at', '>=', $dateOfLast30Day)->count();
        $scheduleCount = Post::whereIn('user_id', $parent_ids)->whereNotNull('schedule_date_time')->count();
        $posts = Post::whereIn('user_id', $parent_ids)->whereNull('schedule_date_time')->latest()->with(['images', 'postMedia'])->limit(4)->get();
        $schedulePosts = Post::whereIn('user_id', $parent_ids)->whereNotNull('schedule_date_time')->orderBy('schedule_date_time', 'desc')->with(['images', 'postMedia'])->limit(4)->get();
        return view('frontend.pages.general.dashboard', compact('schedulePosts', 'posts', 'postsCount', 'scheduleCount'))->with(['title' => __('Dashboard')]);
    }
    public function filterPosts(Request $request)
    {
        $user = Auth::user();
        $parent_ids = null;
        $schedulePosts = collect([]);
        $posts = collect([]);
        $startDate = date("Y-m-d ", strtotime($request->startDate));
        $endDate = date("Y-m-d ", strtotime($request->endDate));
        $parent_id = $user->parent_id ?? "";
        if (!empty($parent_id)) {
            $parent_user = User::where('id', $parent_id)->first();
            $parent_ids = $parent_user->parents->pluck('id')->toArray();
            $parent_ids[] = $parent_user->id;
        } else {
            $parent_ids = $user->parents->pluck('id')->toArray();
            $parent_ids[] = $user->id;;
        }
        $posts = Post::whereIn('user_id', $parent_ids)->whereNull('schedule_date_time')->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->orderBy('created_at', 'desc')->get();
        $schedulePosts = Post::whereIn('user_id', $parent_ids)->whereNotNull('schedule_date_time')->whereBetween('schedule_date', [$startDate, $endDate])->orderBy('schedule_date_time', 'desc')->get();


        $post['dataPost'] = view('frontend.pages.general.filterdata')->with(['posts' => $posts, 'type' => 'Posts'])->render();
        $post['dataSchedule'] = view('frontend.pages.general.filterdata')->with(['posts' => $schedulePosts, 'type' => 'Schedule Posts'])->render();
        return response()->json($post);
    }
    public function home()
    {
        $user = Auth::user();
        $parent_ids = null;
        $schedulePosts = collect([]);
        $posts = collect([]);

        $parent_id = $user->parent_id ?? "";
        if (!empty($parent_id)) {
            $parent_user = User::where('id', $parent_id)->first();
            $parent_ids = $parent_user->parents->pluck('id')->toArray();
            $parent_ids[] = $parent_user->id;
        } else {
            $parent_ids = $user->parents->pluck('id')->toArray();
            $parent_ids[] = $user->id;;
        }
        $posts = Post::whereIn('user_id', $parent_ids)->whereNull('schedule_date_time')->orderBy('created_at', 'desc')->limit(4)->get();
        $schedulePosts = Post::whereIn('user_id', $parent_ids)->whereNotNull('schedule_date_time')->orderBy('created_at', 'desc')->limit(4)->get();
        // if($user->is_subscribe == 'n' && $user->type == 'company'){
        //     return redirect()->route('package-list');
        // }else{
        // if($user->type == 'company'){
        //     $data = SocialMediaDetail::where('user_id',$user->id)->get();
        //     if($data->count() == 4){
        //         return view('frontend.pages.general.dashboard', compact('schedulePosts', 'posts'))->with(['title' => __('Dashboard')]);
        //     }else{

        //         return redirect()->route('social-media-list');
        //     }
        // }else{
        return view('frontend.pages.general.dashboard', compact('schedulePosts', 'posts'))->with(['title' => __('Dashboard')]);
        // }

        // }


    }
    public function profile()
    {
        $user = Auth::user();
        // dd($user);
        return view('admin.pages.general.profile', compact('user'))->with(['custom_title' => __('Edit Profile')]);
    }

    public function updateProfile(ProfileUpdate $request)
    {
        $user = Auth::user();

        if ($request->hasFile('profile_avatar')) {
            Storage::delete($user->profile);
            $user->profile = $request->file('profile_avatar')->store('profileImage');
        }

        $old_email = $user->email;

        $user->full_name = $request->full_name;
        $user->email = $request->email;
        $user->contact_no = $request->contact_no;
        if ($user->save()) {
            if ($old_email != $user->email) {
                Auth::guard('admin')->logout();
                return redirect()->guest(route('admin.login'));
                die;
            }
        }
        flash(trans('flash_message.update', ['entity' => 'Profile']))->success();
        return redirect()->route('admin.profile-view');
    }

    public function updatePassword(ChangePassword $request)
    {
        // dd($request->all());
        if (Hash::check($request->current_password, Auth::user()->password)) {
            $user = Auth::user();
            $user->password = Hash::make($request->password);
            flash(trans('flash_message.password_change'))->success();
            $user->save();
        } else {
            flash(trans('flash_message.password_not_match'))->error();
        }
        return redirect()->route('admin.profile-view');
    }

    public function quickLink()
    {
        $roles = Role::where('is_display', 'y')->with(['quickLinks'])->get();
        return view('admin.pages.general.quickLink', compact('roles'))->with(['custom_title' => __('Quick Link')]);
    }

    public function updateQuickLink(Request $request)
    {
        $user = Auth::user();
        if ($request->has('roles')) {
            //getting all requested roles id of quickLinks
            $requestedRoleIds = array_keys($request->roles);
            //getting admins all roles id of quickLinks
            $quickLinksRoleIds =  $user->quickLinks->pluck('role_id')->toArray();
            //getting diffQuickLinks data
            $diffQuickLinks = array_diff($quickLinksRoleIds, $requestedRoleIds);
            //getting role id values of diffQuickLinks
            $values = array_values($diffQuickLinks);
            if (count($values) > 0) {
                QuickLink::where('admin_id', $user->id)->whereIn('role_id', $values)->delete();
            }
            foreach ($request->roles as $key => $role) {
                if (!empty($role['permissions'])) {
                    $quickLink = QuickLink::updateOrCreate(
                        ['role_id' => $key, 'admin_id' => $user->id],
                        ['link_type' => implode(',', $role['permissions'])]
                    );
                }
            }
        } else {
            //quick links request empty then delete all quick links
            $user->quickLinks()->delete();
        }
        return redirect()->back();
    }

    public function showSetting()
    {
        $settings = Setting::where('editable', '=', 'y')->get();
        return view('admin.pages.general.settings', compact('settings'))->with(['custom_title' => 'Site Setting']);
    }

    public function changeSetting(Request $request)
    {
        $array = array();
        $flag = false;
        $cb_key = $cb_secret = '';
        foreach ($request->input() as $key => $value) {
            $setting = Setting::find($key);
            if ($setting) {
                if ($value != "" || $setting->required == "n") {
                    $setting->value = $value;
                    $setting->save();
                    $array[$setting->constant] = $value;

                    //Config::set('settings.' . $setting->constant, $setting->value);
                }
            }
        }

        //Image Uploading
        foreach ($request->file() as $key => $value) {
            $setting = Setting::find($key);
            if ($request->hasFile($key)) {
                $file_pre = ($setting->constant == 'site_logo') ? 'logo.' : time() . '.';
                $filename = $file_pre . $request->file($key)->getClientOriginalExtension();
                $request->file($key)->move(public_path('frontend/images'), $filename);
                $setting->value = 'frontend/images/' . $filename;
                $setting->save();
                $array[$setting->constant] = 'frontend/images/' . $filename;
            }
        }

        //Non editable value
        $rem_settings = Setting::where('editable', '=', 'n')->get();
        foreach ($rem_settings as $key => $single) {
            $array[$single->constant] = $single->value;
        }
        flash(trans('flash_message.update', ['entity' => 'Settings']))->success();
        return redirect()->route('admin.settings.index');
    }

    public function contact()
    {
        $settings = Setting::pluck('value', 'constant')->toArray();

        return view('frontend.pages.general.contact-us', compact('settings'))->withTitle('Contact Us');
    }
    public function contactStore(Request $request)
    {
        $rules = [
            'full_name'         =>      'required|max:150',
            'email_address'     =>      'required|email',
            'message'           =>      'required|min:10',
            'file'              =>      'nullable|mimes:png,jpg,jpeg'
        ];
        $this->ValidateForm($request->all(), $rules);
        try {
            $request['custom_id'] = getUniqueString('contact_us');
            if ($request->has('file')) {
                $request['image'] = $request->file->store('screen_shots');
            }
            $contact = ContactUs::create($request->all());
            $setting = Setting::where('constant', 'support_email')->first();
            Mail::to($setting->value)->send(new ContactUsMail($contact));
            Mail::to($contact->email_address)->send(new ContactUsConfirmationMail($contact));
            flash('Thank you! Your message has been sent successfully.')->success();
        } catch (\Throwable $th) {
            // dd($th->getMessage());
            flash('Something Went Wrong')->error();
        }
        return redirect()->back();
    }

    public function termsConditions()
    {

        $terms_conditions = CmsPage::where('slug', 'terms-and-conditions')->first();
        return view('frontend.pages.general.terms-conditions', compact('terms_conditions'))->withTitle('Terms & conditions');
    }
    public function privacyPolicy()
    {
        $privacy_policy = CmsPage::where('slug', 'privacy')->first();
        return view('frontend.pages.general.privacy-policy', compact('privacy_policy'))->withTitle('Privacy Policy');
    }
    public function aboutUs()
    {
        $view = 'about-us-without-login';
        if (auth()->check() && auth()->user())
            $view = 'about-us';
        $about_us = CmsPage::where('slug', 'about-us')->first();
        return view('frontend.pages.general.' . $view, compact('about_us'))->withTitle('About Us');
    }
    public function faqs()
    {
        $faqs = Faqs::get();
        return view('frontend.pages.general.faqs', compact('faqs'))->withTitle('FAQs');
    }

    public function socialMediaList(Request $request)
    {
        $is_relink = $request->is_page_is_relink ?? false;
        // dump($is_relink);
        $media = Media::get();
        $user = Auth::user();
        $user_id = auth()->user()->parent_id ?? auth()->id();

        // Total Page Purchased Count
        $totalPagefacebook = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 1])->count();
        $totalPagefacebookAds = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 8])->count();
        $totalPagelinked = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 2])->count();
        $totalPagetwitter = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 3])->count();
        $totalPageinsta = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 4])->count();
        $totalPageGoogle = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 5])->count();
        $totalPageGoogleAnalytics = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 6])->count();
        $totalPageGoogleAds = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 7])->count();

        // Total Page Linked or Used Count
        $usedPagefacebook = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 1, 'is_used' => 'y'])->count();
        $usedPagefacebookAds = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 8, 'is_used' => 'y'])->count();
        $usedPagelinked = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 2, 'is_used' => 'y'])->count();
        $usedPagetwitter = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 3, 'is_used' => 'y'])->count();
        $usedPageinsta = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 4, 'is_used' => 'y'])->count();
        $usedPageGoogle = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 5, 'is_used' => 'y'])->count();
        $usedPageGoogleAnalytics = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 6, 'is_used' => 'y'])->count();
        $usedPageGoogleAds = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 7, 'is_used' => 'y'])->count();

        // Fetch the media pages
        $getFacebookPage = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 1, 'is_used' => 'y'])->with('mediaPage')->get();
        $getFacebookAdsPage = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 8, 'is_used' => 'y'])->with('mediaPage')->get();
        $getLinkedinPage = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 2, 'is_used' => 'y'])->with('mediaPage')->get();
        $getTwitterPage = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 3, 'is_used' => 'y'])->with('mediaPage')->get();
        $getInstagramPage = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 4, 'is_used' => 'y'])->with('mediaPage')->get();
        $getGooglePage = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 5, 'is_used' => 'y'])->with('mediaPage')->get();
        $getGoogleAnalyticsPage = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 6, 'is_used' => 'y'])->with('mediaPage')->get();
        $getGoogleAdsPage = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => 7, 'is_used' => 'y'])->with('mediaPage')->get();
        // $getFacebookPage = MediaPage::where(['user_id' =>  Auth::id(), 'media_id' => 1])->take($totalPagefacebook)->get();
        // $getLinkedinPage = MediaPage::where(['user_id' =>  Auth::id(), 'media_id' => 2])->take($totalPagelinked)->get();
        // $getTwitterPage = MediaPage::where(['user_id' =>  Auth::id(), 'media_id' => 3])->take($totalPagetwitter)->get();
        // $getInstagramPage = MediaPage::where(['user_id' =>  Auth::id(), 'media_id' => 4])->take($totalPageinsta)->get();

        $getAllMediaPages = [];
        $getMediaPagesCount = 0;
        $showModal = false;
        $mediaType = ($request->socialMedia) ? $request->socialMedia : '';
        // Fetch all media pages
        if ($request->socialMedia) {
            $showModal = true;
            $media_id = $this->getMediaId($request->socialMedia);
            $getAllMediaPages = MediaPage::where(['user_id' =>  Auth::id(), 'media_id' => $media_id, 'is_old' => 'n'])->get();
            $getMediaPagesCount = MediaPagePayments::where(['user_id' =>  Auth::id(), 'media_id' => $media_id, 'is_used' => 'n'])->count();
        }

        $social_media_detail = SocialMediaDetail::where('user_id', $user_id)->pluck('media_id')->toArray();

        $linkedPage = UserMedia::where('user_id', $user_id)->select('media_page_id', 'is_deleted')->get();
        $payment = Payment::where('user_id', $user_id)->first();

        if ($payment) $is_expiry = now()->format('d M, Y') > $payment->end_date ? 'y' : 'n';
        $socialMediaExpiry = [];

        // Find the Twitter is linked or not.
        $twitterUserMedia = UserMedia::where(['user_id' => $user_id, 'media_id' => 3, 'is_deleted' => 'n'])->first();
        if ($twitterUserMedia) $socialMediaExpiry['twitter'] = true;
        // if ($twitterTokenExpiryDate) $socialMediaExpiry['twitter'] = $twitterTokenExpiryDate->token_expiry ?  Carbon::parse($twitterTokenExpiryDate->token_expiry)->diffInDays(now()->toDateString()) : 'N/A';

        return view('frontend.pages.general.social_media_list', compact('media', 'user', 'social_media_detail', 'totalPagefacebook', 'totalPagelinked', 'totalPagetwitter', 'totalPageinsta', 'totalPageGoogle', 'usedPagefacebook', 'usedPagelinked', 'usedPagetwitter', 'usedPageinsta', 'usedPageGoogle', 'getFacebookPage', 'getLinkedinPage', 'getTwitterPage', 'getInstagramPage', 'getGooglePage', 'linkedPage', 'getAllMediaPages', 'showModal', 'mediaType', 'getMediaPagesCount', 'socialMediaExpiry', 'totalPageGoogleAnalytics', 'totalPageGoogleAds', 'usedPageGoogleAnalytics', 'usedPageGoogleAds', 'getGoogleAnalyticsPage', 'getGoogleAdsPage', 'totalPagefacebookAds', 'usedPagefacebookAds', 'getFacebookAdsPage', 'is_relink'))->withTitle('Social Media List');
    }
    public function packageList()
    {
        $packages = Package::get();
        return view('frontend.pages.general.package_list', compact('packages'))->withTitle('Package List');
    }
    public function payment()
    {
        $stripe = new \Stripe\StripeClient();
        // $token = $stripe->tokens->create([
        //     'card' => [
        //         'number' => '4242424242424242',
        //         'exp_month' => 2,
        //         'exp_year' => 2024,
        //         'cvc' => '314',
        //     ],
        // ]);
        // // // dump($token);
        // $sc =  $stripe->customers->createSource(
        //   'cus_MBkB8Z3i4uznUL',
        //   ['source' => $token->id]
        // );
        // dd($sc);
        // $customer = $stripe->customers->create(
        //     [
        //         'email'     =>      'sneh@mailinator.com' ,
        //         'metadata'  =>  [
        //                 'first name'    =>  'sneh',
        //                 'last name'     =>  'patel',
        //         ],
        //     ]
        // );
        // $paymentMethods =  $stripe->paymentMethods->create([

        $sub = $stripe->subscriptions->create([
            'customer' => 'cus_MBkB8Z3i4uznUL',

            'items' => [
                ['price' => 'price_1LT3mvJWudNQ8ReCtm2fdTyj'],
            ],
            'backdate_start_date' => strtotime("5-8-2022 5:00"),
            'cancel_at' => strtotime("25-8-2022 15:00"),
        ]);
        // dd($sub);
    }
}
