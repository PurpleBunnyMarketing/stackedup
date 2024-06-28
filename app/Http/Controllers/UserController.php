<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\MediaPage;
use App\Models\Payment;
use App\Models\UserMedia;
use App\Models\MediaPagePayments;
use App\Services\SMSService;
use App\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        $data = Media::whereHas('user_media', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['mediaPages' => function ($q) use ($user) {
            $q->whereHas('userMediaPages', function ($q) use ($user) {
                $q->where('user_id', $user->id);
                $q->where('is_deleted', 'n');
            });
        }])->get();
        $expiry_date = '';
        if ($user->type == 'company') {
            $expiry_date = Payment::select('end_date')->where('user_id', $user->id)->orderBy('end_date', 'desc')->first();
            if ($user->is_subscribe == 'n')
                $expiry_date = 'Please Purchase Subscription First';
            else
                if ($expiry_date) $expiry_date = now() < date($expiry_date->end_date) ? 'Expire on ' . Carbon::parse($expiry_date->end_date)->format('d M, Y') : 'Expired';
        }

        return view('frontend.pages.users.profile', compact('user', 'data', 'expiry_date'))->withTitle('My Profile');
    }

    public function changePassword()
    {
        return view('frontend.pages.users.change_password')->withTitle('Change Password');
    }

    public function updatePassword(Request $request)
    {
        // dd($request->all());
        $rules = [
            'old_password'  => 'required|min:8|max:16',
            'password'      => 'required|min:8|max:16|confirmed',
            'password_confirmation' => 'required|min:8|max:16',
        ];
        $this->ValidateForm($request->all(), $rules);
        $user = Auth::user();
        // dd($user->password);
        if (Hash::check($request->old_password, $user->password)) {

            if (!empty($request->password)) {
                $request['password'] = Hash::make($request->password);
            }
            $user->fill($request->except('profile_photo'));
            if ($user->save()) {
                flash('Change password successfully!')->success();
            } else {
                flash('Enable to update profile!')->error();
            }
        } else {
            // dd('dsfds');
            flash("Current password doesn\'t match with our records.")->error();
        }
        return redirect()->back();
    }

    public function editProfile()
    {
        $user = Auth::user();
        return view('frontend.pages.users.edit_profile', compact('user'))->withTitle('Edit Profile');
    }
    public function updateProfile(Request $request)
    {
        $rules = [
            'full_name' => 'required|max:30',
            // 'mobile_no' => 'required',
            // 'phone_code' => 'required',
            // 'email' => 'required|email:rfc,dns',
        ];
        $this->ValidateForm($request->all(), $rules);
        $user = Auth::user();

        $user->fill($request->except('profile_photo'));
        if ($request->profile_avatar_remove == 1) {
            if ($user->profile_photo) {
                if (Storage::exists($user->profile_photo)) {
                    Storage::delete($user->profile_photo);
                }
            }
            $user->profile_photo = null;
        }
        if (!empty($request->profile_photo)) {
            if ($user->profile_photo) {
                if (Storage::exists($user->profile_photo)) {
                    Storage::delete($user->profile_photo);
                }
            }
            $path = $request->file('profile_photo')->store('profile_photo');
            $user->profile_photo = $path;
        }
        $user->save();
        flash('Profile updated successfully!')->success();
        return redirect()->route('profile');
    }

    public function deleteUserMedia(Request $request)
    {
        $pageMedia_id = MediaPage::where('custom_id', $request->page_id)->first()->id;
        $deletedUserMedia = DB::transaction(function () use ($pageMedia_id) {
            return  UserMedia::where('user_id', auth()->id())->where('media_page_id', $pageMedia_id)->update(['is_deleted' => 'y']);
        });
        $deleteMediaPagePayment = DB::transaction(function () use ($pageMedia_id) {
            return MediaPagePayments::where(['user_id'  => auth()->user()->parent_id ?? auth()->id(), 'media_page_id' => $pageMedia_id, 'is_used' => 'y'])->update(['is_used' => 'n']);
        });
        if ($deletedUserMedia && $deleteMediaPagePayment)
            return response()->json(array('status' => 200, 'message' => "Media Access Revoked"));
    }

    // Send OTP
    public function sendOTP(Request $request, SMSService $sms)
    {
        $rules = [
            'phone_code'    =>  'required|max:30',
            'mobile_no'     =>  'required|numeric|exists:users,mobile_no',
        ];
        $error =  $this->ValidateForm($request->all(), $rules);
        try {
            $check = User::where(['phone_code' => $request->phone_code, 'mobile_no' => $request->mobile_no])->first();
            if (!empty($check)) {

                $otp = mt_rand(1000, 9999);
                // $result = $sms->send($request->mobile_no, $otp);
                $user = "matt@purplebunny.com.au";
                $pass = "Purple12345678";
                $to = $request->mobile_no;
                $from = "Stackedup";

                $message = urlencode("Dear Client,The One Time Password(OTP) for your account login is " . $otp . ". Please do not share your OTP with anyone. Team stackedup!");

                $url = "http://api.smsbroadcast.com.au/api.php?" . "username=$user&password=$pass&from=$from&to=$to&message=$message";
                $result = file_get_contents($url);
                if ($result == "Your message was sent.") {
                    $check->otp = $otp;
                    $check->save();
                    flash('OTP Send successfully!')->success();

                    return redirect()->route('otp', [$request->phone_code, $request->mobile_no]);
                } else {
                    flash($result)->error();
                }
            } else {
                flash("This mobile number is not registered with us.")->error();
            }
            return redirect()->back();
        } catch (\Exception $ex) {
            // dd($ex->getMessage());
            flash($ex->getMessage())->error();
        }
        return redirect()->back();
    }

    public function otpScreen($phone_code, $mobile_no)
    {

        $phoneCode = $phone_code;
        $mobileNo = $mobile_no;

        return view('frontend.pages.general.otp_verification', compact('phoneCode', 'mobileNo'));
    }

    // verify OTP
    public function verifyOTP(Request $request)
    {
        try {
            $user_given_otp = $request->{'digit-1'} . $request->{'digit-2'} . $request->{'digit-3'} . $request->{'digit-4'};
            $check = User::where('mobile_no', $request['mobileNo'])->first();
            if (!empty($check)) {
                $check_otp = User::where(['phone_code' => $request->phoneCode, 'mobile_no' => $request->mobileNo, 'otp' => $user_given_otp])->first();
                if (!empty($check_otp)) {
                    if ($check_otp->is_active == 'y') {
                        Auth::login($check_otp);
                        if ($check_otp->is_subscribe !== 'y' && $check_otp->type == 'company') {
                            flash('Purchase Subscription First then you can access other resources')->error();
                            return redirect()->route('social-media-list');
                        }
                        return redirect()->route('home');
                    } else {
                        flash('Please contact admin to activate your account')->error();
                        return redirect()->back();
                    }
                } else {
                    flash('Oops! You enter wrong otp, please re-enter correct!')->error();
                    return redirect()->back();
                }
            } else {
                flash("This mobile number does not exists with us.")->error();
                return redirect()->back();
            }
        } catch (\Exception $ex) {
            flash($ex->getMessage())->error();
            return redirect()->back();
        }
        // return $this->return_response();
    }

    public function expiryOtp(Request $request)
    {
        try {
            if ($request->phoneCode && $request->mobileNo) {
                $expiryOTP =  User::where([
                    'phone_code' => $request->phoneCode,
                    'mobile_no' => $request->mobileNo
                ])->update([
                    'otp' => ''
                ]);
            }
            // dd($expiryOTP);
            if (request()->ajax()) {
                $content = array('status' => 200, 'message' => "success.", 'data' => $new);
                return response()->json($content);
            }
        } catch (\Exception $ex) {
            $content = array('status' => 400, 'message' => "error");
            return response()->json($content);
        }
    }

    public function resendOtp(Request $request, SMSService $sms)
    {
        // dd($request->all());
        try {
            $user = User::select('otp')->where(['phone_code' => $request->phoneCode, 'mobile_no' => $request->mobileNo])->first();
            if ($user) {
                // $result  = $sms->send($request->mobileNo, $user->otp);
                // $user = "matt@purplebunny.com.au";
                // $pass = "Purple12345678";
                // $to = $request->mobileNo;
                // $from = "Stackedup";

                // $message = urlencode("Dear Client,The One Time Password(OTP) for your account login is " .$otp. ". Please do not share your OTP with anyone. Team stackedup!");

                // $url = "http://api.smsbroadcast.com.au/api.php?".
                // "username=$user&password=$pass&from=$from&to=$to&message=$message";
                // $result = file_get_contents($url);
                // if ($result == "Your message was sent.") {
                $content = array('status' => 200, 'message' => "success.", 'data' => 'OTP ReSend Successfully');
                // } else throw new Exception();
            }
        } catch (Exception $e) {
            $content = array('status' => 400, 'message' => "error.", 'data' => ['error' => $e->getMessage, 'line' => $e->getLine()]);
        }
        return response()->json($content);
    }
}
