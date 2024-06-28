<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\api\v1\currentSubscription;
use App\Http\Resources\v1\SubscriptionPlanResource;
use App\Http\Resources\v1\UserProfile;
use App\Http\Resources\v1\UserProfileDetail;
use App\Models\AppUpdateSetting;
use App\Models\MediaPage;
use App\Models\MediaPagePayments;
use App\Models\Package;
use App\Models\Payment;
use App\Models\UserMedia;
use App\Rules\CheckUserPermittedMediaRule;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    use SendsPasswordResetEmails;
    private $version = "v.1.0";
    public function getVersion()
    {
        return $this->version;
    }
    public function __construct()
    {
        $this->stripe = new \Stripe\StripeClient(
            config('utility.STRIPE_SECRET')
        );
    }
    /* User Register API */
    public function register(Request $request)
    {

        $rules = [
            'full_name'     => 'required|max:150',
            'email'         => 'required|email|max:150|unique:users,email',
            'password'         => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8',
            'phone_code'  =>    'required|max:30',
            'mobile_no'     =>    'required|numeric|digits_between:6,16',
        ];
        if ($this->apiValidator($request->all(), $rules)) {
            try {
                $user = User::whereRaw('concat(users.phone_code , "" , users.mobile_no) = ?', [$request->phone_code . '' . $request->mobile_no])->first();

                if (empty($user)) {
                    $request['is_active'] = 'y';
                    $request['type'] = 'company';
                    $request['custom_id'] = getUniqueString('users');
                    $request['password'] = Hash::make($request->password);

                    $user = User::create($request->all());
                    // $media_pages = MediaPage::get();
                    // foreach ($media_pages as $page) {
                    // 	$user_media =  UserMedia::create([
                    // 		'custom_id' => getUniqueString('user_media'),
                    // 		'user_id' => $user->id ?? "",
                    // 		'media_id' => $page->media_id ?? "",
                    // 		'media_page_id' => $page->id ?? "",
                    // 	]);
                    // }
                    $data = User::findOrFail($user->id);
                    if ($data->customer_id == null) {
                        $customer = $this->stripe->customers->create(
                            [
                                'email'     =>      $data->email ?? "",
                                'metadata'  =>  [
                                    'Full name' =>  $data->full_name ?? "",
                                ],
                            ]
                        );
                        $data->customer_id = $customer->id;
                        $data->save();
                    }
                    return (new UserProfile($user))
                        ->additional([
                            'meta' => [
                                'message'     =>    trans('api.registered'),
                                'auth_token' => $user->createToken(config('utility.token'))->accessToken,
                            ]
                        ]);
                } else {
                    $this->status = $this->statusArr['unprocessable_entity'];
                    $this->response['meta']['url'] = url()->current();
                    $this->response['meta']['api'] = $this->version;
                    $this->response['meta']['message'] = "The mobile no has already been taken.";
                }
            } catch (\Exception $ex) {
                $this->status = $this->statusArr['something_wrong'];
                $this->response['meta']['message'] = trans('api.error');
            }
        }
        return $this->return_response();
    }

    //User Login
    public function login(Request $request)
    {
        $rules = [
            'email'         =>  'required|nullable|exists:users,email|email|string|max:150',
            'password'      =>  'required|nullable|string|min:8|max:16',
            // 'email'         =>  'required_without:mobile_no|nullable|email:rfc,dns|string|max:150',
            // 'password'      =>  'required_with:email|nullable|string|min:8|max:16',
            // 'phone_code'     =>	'required_with:mobile_no|nullable|max:30',
            // 'mobile_no'     =>	'required_without:email|nullable|numeric|digits_between:6,16',
        ];
        $message = [

            'email.exists' => "Please enter valid registered email address."
        ];
        if ($this->ApiValidator($request->all(), $rules, $this->getVersion(), $message)) {
            // $message = [

            // 	'email.required_without' => 'The email field is required.',
            // 	'password.required_with' => 'The password field is required.',
            // 	'mobile_no.required_without' => 'The mobile no field is required.',
            // 	'phone_code.required_with' => 'The phone code field is required.',
            // ];

            try {
                $attempt = [];
                if (!empty($request->email)) {
                    $attempt = ['email'    =>    $request->email, 'password' => $request->password];
                    if (Auth::attempt($attempt)) {
                        $user = Auth::user();
                        if ($user->is_active == 'y') {
                            return (new UserProfile($user))
                                ->additional([
                                    'meta' => [
                                        'message'        =>    trans('api.login'),
                                        'auth_token'     =>     $user->createToken(config('utility.token'))->accessToken,
                                    ]
                                ]);
                        } else {
                            $this->status = $this->statusArr['forbidden'];
                            $this->response['meta']['url'] = url()->current();
                            $this->response['meta']['api'] = $this->version;
                            $this->response['meta']['message'] = "Please contact admin to activate your account";
                        }
                    } else {
                        $this->status = $this->statusArr['forbidden'];
                        $this->response['meta']['url'] = url()->current();
                        $this->response['meta']['api'] = $this->version;
                        $this->response['meta']['message']  = trans('api.login_fail');
                    }
                }
            } catch (\Exception $ex) {

                $this->status = $this->statusArr['something_wrong'];
                $this->response['meta']['message'] = trans('api.error');
            }
        }
        return $this->return_response();
    }

    // Send OTP
    public function sendOTP(Request $request)
    {
        $rules = [
            'phone_code'    =>  'required|max:30',
            'mobile_no'     =>  'required|numeric|digits_between:6,16',
        ];
        if ($this->apiValidator($request->all(), $rules)) {
            try {

                $check = User::where(['phone_code' => $request->phone_code, 'mobile_no' => $request->mobile_no])->first();
                if (!empty($check)) {
                    $otp = mt_rand(1000, 9999);
                    $user = "matt@purplebunny.com.au";
                    $pass = "Purple12345678";
                    $to = $request->mobile_no;
                    $from = "Stackedup";

                    $message = urlencode("Dear Client,The One Time Password(OTP) for your account login is " . $otp . ". Please do not share your OTP with anyone. Team stackedup!");

                    $url = "http://api.smsbroadcast.com.au/api.php?" .
                        "username=$user&password=$pass&from=$from&to=$to&message=$message";
                    $result = file_get_contents($url);
                    if ($result == "Your message was sent.") {
                        $check->otp = $otp;
                        $check->save();
                        $this->status = $this->statusArr['success'];
                        $this->response['meta']['message'] = "OTP send successfully.";
                        return $this->return_response();
                    } else {
                        $this->status = $this->statusArr['something_wrong'];
                        $this->response['meta']['message'] = $result;
                        return $this->return_response();
                    }
                } else {
                    $this->status = $this->statusArr['forbidden'];
                    $this->response['meta']['message'] = "This mobile number is not registered with us.";
                    return $this->return_response();
                }
                $this->response['meta']['url'] = url()->current();
                $this->response['meta']['api'] = $this->version;
                return $this->return_response();
            } catch (\Exception $ex) {
                $this->status = $this->statusArr['something_wrong'];
                $this->response['meta']['message'] = trans('api.error');
                return $this->return_response();
            }
        }
    }

    // verify OTP

    public function verifyOTP(Request $request)
    {
        $rules = [
            'phone_code'    =>    'required|max:30',
            'mobile_no'     =>    'required|numeric|digits_between:6,16',
            'otp'             =>    'required|max:30',
        ];
        if ($this->apiValidator($request->all(), $rules, $this->version)) {
            try {
                $check = User::where(['phone_code' => $request->phone_code, 'mobile_no' => $request->mobile_no])->first();
                if (!empty($check)) {
                    $check_otp = User::where(['phone_code' => $request->phone_code, 'mobile_no' => $request->mobile_no, 'otp' => $request->otp])->first();
                    if (!empty($check_otp)) {
                        if ($check_otp->is_active == 'y') {
                            return (new UserProfile($check_otp))
                                ->additional([
                                    'meta' => [
                                        'message'        =>    trans('api.login'),
                                        'auth_token'     =>     $check_otp->createToken(config('utility.token'))->accessToken,
                                    ]
                                ]);
                        } else {
                            $this->status = $this->statusArr['forbidden'];
                            $this->response['meta']['url'] = url()->current();
                            $this->response['meta']['api'] = $this->version;
                            $this->response['meta']['message'] = "Please contact admin to activate your account";
                        }
                    } else {
                        $this->status = $this->statusArr['forbidden'];
                        $this->response['meta']['message'] = trans('api.validate_fail', ['entity' => 'otp']);
                    }
                } else {
                    $this->status = $this->statusArr['forbidden'];
                    $this->response['meta']['message'] = trans('api.not_exists', ['entity' => 'mobile no']);
                }
                $this->response['meta']['url'] = url()->current();
                $this->response['meta']['api'] = $this->version;
            } catch (\Exception $ex) {
                $this->status = $this->statusArr['something_wrong'];
                $this->response['meta']['message'] = trans('api.error');
            }
        }
        return $this->return_response();
    }

    // User get profile

    public function getProfile(Request $request)
    {
        try {
            $user = User::where('id', $request->user()->id)->with('media.mediaPage', 'media.media')->first();
            return (new UserProfileDetail($user))
                ->additional([
                    'meta' => [
                        'message' =>    trans('api.success'),
                    ]
                ]);
        } catch (\Exception $ex) {
            $this->status = $this->statusArr['something_wrong'];
            $this->response['message'] = trans('api.error');
        }

        return $this->return_response();
    }

    // edit profile

    public function editProfile(Request $request)
    {
        $user = $request->user();
        $rules = [
            'full_name'     => 'required|max:150',
            'email'         => 'required|email|max:150|unique:users,email,' . $user->id,
            'phone_code'      =>    'required|max:30',
            'mobile_no'     =>    'required|numeric|digits_between:6,16',
        ];
        if ($this->apiValidator($request->all(), $rules)) {
            try {
                $user->fill($request->except('profile_photo'));
                $user->save();
                return (new UserProfile($user))
                    ->additional([
                        'meta' => [
                            'message' => trans('api.update', ['entity' => 'Profile']),

                        ]
                    ]);
            } catch (\Exception $ex) {
                $this->status = $this->statusArr['something_wrong'];
                $this->response['meta']['message'] = trans('api.error');
            }
        }
        return $this->return_response();
    }

    public function editProfilePhoto(Request $request)
    {

        $user = $request->user();
        $rules = [
            'profile_photo' => 'sometimes|image|mimes:jpeg,png,jpg',
        ];
        if ($this->apiValidator($request->all(), $rules, $this->version)) {
            if ($request->has('profile_photo')) {
                if ($user->profile_photo) {
                    if (Storage::exists($user->profile_photo)) {
                        Storage::delete($user->profile_photo);
                    }
                }
                $path = $request->file('profile_photo')->store('profile_photo');
                $user->profile_photo = $path;
            }
            $user->save();
            return (new UserProfile($user))
                ->additional([
                    'meta' => [
                        'message' => trans('api.update', ['entity' => 'Profile']),
                    ]
                ]);
        }
        return $this->return_response();
    }

    public function changePassword(Request $request)
    {
        $rules = [
            'old_password'          =>      'required|min:8|max:16',
            'password'              =>      'required|min:8|max:16|confirmed|different:old_password',
            'password_confirmation' =>      'required|min:8|max:16',
        ];
        if ($this->apiValidator($request->all(), $rules)) {
            $user = $request->user();
            if (Hash::check($request->old_password, $user->password)) {
                $user->update([
                    'password' => Hash::make($request->password)
                ]);
                $user = User::findOrfail(Auth::id());

                $this->response['meta']['message']  = "Password changed succesfully";
                $this->response['meta']['url'] = url()->current();
                $this->response['meta']['api'] = $this->version;
                $this->status = $this->statusArr['success'];
            } else {
                $this->response['meta']['message']  =  trans('api.password_not_match');
                $this->response['meta']['url'] = url()->current();
                $this->response['meta']['api'] = $this->version;
                $this->status = $this->statusArr['something_wrong'];
            }
        }
        return $this->return_response();
    }
    /* User  API */
    public function logout(Request $request)
    {
        DB::table('oauth_access_tokens')->where('user_id', $request->user()->id)->delete();
        $this->status = $this->statusArr['success'];
        $this->response['meta']['api'] = $this->version;
        $this->response['meta']['url'] = url()->current();
        $this->response['meta']['message']  = trans('api.');
        return $this->return_response();
    }

    /* Forget Password API */
    public function forgotPassword(Request $request)
    {
        $rules = [
            'email' => 'required|email|exists:users,email'
        ];
        $message = [

            'email.exists' => "Please enter valid registered email address."
        ];
        if ($this->ApiValidator($request->all(), $rules, $this->getVersion(), $message)) {
            $user = User::where(['email' => $request->email])->first();
            if ($user != null) {
                $response = $this->broker()->sendResetLink($this->credentials($request));
                if ($response == Password::RESET_LINK_SENT) {
                    $this->status = $this->statusArr['success'];
                    $this->response['meta']['message']  =  trans('api.link_sent', ['entity' => 'Reset password']);
                } else {
                    $this->status = $this->statusArr['not_found'];
                    $this->response['meta']['message'] = "Please wait before retrying.";
                }
            } else {
                $this->status = $this->statusArr['not_found'];
                $this->response['meta']['message'] = "User does not exist!";
            }
        }
        $this->response['meta']['api'] = $this->getVersion();
        $this->response['meta']['url'] = url()->current();
        return $this->return_response();
    }
    public function broker() /* Used in dabbUserForgetPassword API */
    {
        return Password::broker('users');
    }


    public function deleteMediaPage(Request $request)
    {
        $rules = [
            'page_id' => ['required', 'string', new CheckUserPermittedMediaRule()],
        ];
        DB::beginTransaction();
        try {
            if ($this->apiValidator($request->only('page_id'), $rules)) {
                $pageMedia_id = MediaPage::where('custom_id', $request->page_id)->first()->id;
                $deletedUserMedia =   UserMedia::where('user_id', auth()->id())->where('media_page_id', $pageMedia_id)->update(['is_deleted' => 'y']);
                $deleteMediaPagePayments = MediaPagePayments::where(['user_id'  => auth()->user()->parent_id ?? auth()->id(), 'media_page_id' => $pageMedia_id, 'is_used' => 'y'])->update(['is_used' => 'n']);
                if (!$deletedUserMedia || !$deleteMediaPagePayments) throw new Exception();
                DB::commit();
                $this->response['meta']['message'] = 'Media Page deleted successfully.';
                $this->status = $this->statusArr['success'];
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->status = $this->statusArr['something_wrong'];
            $this->response['meta']['message'] = trans('api.error');
        }
        return $this->return_response();
    }


    public function currentSubscription()
    {
        try {
            $is_expiry = '';
            $user_id = auth()->user()->parent_id ?? auth()->id();
            $payment = Payment::where('user_id', $user_id)->first();
            if ($payment) {
                $facebookMediaPage = MediaPagePayments::where(['user_id' => $user_id, 'media_id' => 1])->get();
                $linkedInMediaPage = MediaPagePayments::where(['user_id' => $user_id, 'media_id' => 2])->get();
                $twitterMediaPage = MediaPagePayments::where(['user_id' => $user_id, 'media_id' => 3])->get();
                $instagramMediaPage = MediaPagePayments::where(['user_id' => $user_id, 'media_id' => 4])->get();
                $googlePage = MediaPagePayments::where(['user_id' => $user_id, 'media_id' => 5])->get();
                // $googleAnalyticsMediaPage = MediaPagePayments::where(['user_id' => $user_id, 'media_id' => 6])->get();
                // $googleAdsMediaPage = MediaPagePayments::where(['user_id' => $user_id, 'media_id' => 7])->get();
                // $facebookAdsMediaPage = MediaPagePayments::where(['user_id' => $user_id, 'media_id' => 8])->get();


                $totalChannel = Count($facebookMediaPage) + Count($linkedInMediaPage) + Count($twitterMediaPage) + Count($instagramMediaPage) + count($googlePage);
                // $totalChannel = Count($facebookMediaPage) + Count($linkedInMediaPage) + Count($twitterMediaPage) + Count($instagramMediaPage) + count($googlePage) + count($googleAdsMediaPage) + count($facebookAdsMediaPage) + count($googleAnalyticsMediaPage);

                $linkedPage = UserMedia::where('user_id', $user_id)->select('media_page_id', 'is_deleted')->get();
                if ($payment) $is_expiry = now()->format('d M, Y') > $payment->end_date ? 'y' : 'n';

                // $packages = ;
                $monthlyPrice = Package::withoutTrashed()->where('is_active', 'y')->where('package_type', 'monthly')->first();
                $yearlyPrice =  Package::withoutTrashed()->where('is_active', 'y')->where('package_type', 'yearly')->first();

                return (new SubscriptionPlanResource(['facebookPage' => $facebookMediaPage, 'linkedInPage' => $linkedInMediaPage, 'twitterPage' => $twitterMediaPage, 'instagramPage' => $instagramMediaPage, 'payment' => $payment, 'totalChannel' => $totalChannel, 'monthlyPrice' => $monthlyPrice, 'yearlyPrice' => $yearlyPrice, 'googlePage' => $googlePage]))
                    // return (new SubscriptionPlanResource(['facebookPage' => $facebookMediaPage, 'linkedInPage' => $linkedInMediaPage, 'twitterPage' => $twitterMediaPage, 'instagramPage' => $instagramMediaPage, 'payment' => $payment, 'totalChannel' => $totalChannel, 'monthlyPrice' => $monthlyPrice, 'yearlyPrice' => $yearlyPrice, 'googleMyBusinessPage' => $googlePage, 'googleAnalyticsPage' => $googleAnalyticsMediaPage, 'googleAdsPage' => $googleAdsMediaPage, 'facebookAdsPage' => $facebookAdsMediaPage]))
                    ->additional([
                        'meta' => [
                            'message' => trans('Your Current Subscription Plan'),
                        ]
                    ]);
            } else {
                $this->status = $this->statusArr['success'];
                $this->response['meta']['api'] = $this->version;
                $this->response['meta']['url'] = url()->current();
                $this->response['meta']['message']  =  trans('Please purchase Subscription First');
                return $this->return_response();
            }
        } catch (\Throwable $th) {
            $this->status = $this->statusArr['something_wrong'];
            $this->response['meta']['message'] = trans('api.error');
        }
        return $this->return_response();
    }

    public function deleteAccount(Request $request)
    {
        try {
            auth()->user()->forceDelete();
            DB::table('oauth_access_tokens')->where('user_id', Auth::id())->delete();
            $this->status = $this->statusArr['success'];
            $this->response['meta']['message']  =  trans('api.delete', ['entity' => 'Account']);
            return $this->return_response();
        } catch (\Exception $e) {
            $this->status = $this->statusArr['something_wrong'];
            $this->response['meta']['message'] = trans('api.error');
        }
        $this->response['meta']['api'] = $this->version;
        $this->response['meta']['url'] = url()->current();

        return $this->return_response();
    }

    public function checkIsSubscribed(Request $request)
    {
        $user = Auth::user();
        if ($user->is_active == 'y') {
            $this->status = $this->statusArr['success'];
            $this->response['data']['is_subscribed'] = $user->is_subscribe;
            $this->response['meta']['message']  =  trans('Check User Subscribed or not!');
            $this->response['meta']['api'] = $this->version;
            $this->response['meta']['url'] = url()->current();
            return $this->return_response();
        } else {
            $this->status = $this->statusArr['forbidden'];
            $this->response['meta']['url'] = url()->current();
            $this->response['meta']['api'] = $this->version;
            $this->response['meta']['message'] = "Please contact admin to activate your account";
        }
    }

    public function isAppUpdate(Request $request)
    {
        // check AppUpdateSetting  update is 1 or not


        // $is_force= AppUpdateSetting::where('slug', $request->platform)->where('is_force_update',1)->first();
        // if($is_force){
        //     $is_force_update = true;
        // }
        $is_force_update = false;
        $isUpdated = true;
        $versionDetails = AppUpdateSetting::where('slug', $request->platform)->first();
        if ($versionDetails != null) {
            $isUpdated = true;

            if ($versionDetails->build_version > $request->build_version) {
                $isUpdated = false;
            }

            if ($versionDetails->app_version != $request->app_version) {

                $request_app_version = explode('.', $request->app_version);
                $app_version = explode('.', $versionDetails->app_version);

                $isUpdated = false;

                foreach ($request_app_version as $key => $value) {
                    $isUpdated = false;
                    // check 3 argument if any one is greater than return true
                    if ($value > $app_version[$key]) {
                        $isUpdated = true;
                        break;
                    }
                }
            }

            if ($isUpdated == false) {
                if ($versionDetails->is_force_update == 1) {
                    $is_force_update = true;
                }
            }
        }
        // $is_force_update = false;

        // $versionDetails = AppUpdateSetting::where('slug', $request->platform)->first();

        // if ($versionDetails != null) {
        //     $isUpdated = version_compare($request->app_version, $versionDetails->app_version, '<') || $versionDetails->build_version > $request->build_version;

        //     if ($versionDetails->is_force_update == 1 && $isUpdated) {
        //         $is_force_update = true;
        //     }
        // }

        $this->response['data']['is_updated'] = $isUpdated;
        $this->response['data']['is_force_update'] = $is_force_update;
        $this->response['meta']['message'] = "Success";
        $this->status = Response::HTTP_OK;
        return $this->return_response();
    }
}
