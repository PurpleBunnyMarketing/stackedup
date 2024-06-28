<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\MediaPage;
use App\Models\MediaPagePayments;
use App\Models\Package;
use App\Models\Payment;
use App\Models\SocialMediaDetail;
use App\Models\UserMedia;
use App\User;
use Carbon\Carbon;
// use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MediaPagesController extends Controller
{
    public function activeSubscription()
    {
        $is_expiry = '';
        $monthlyPrice = 0;
        $annualPrice = 0;
        $user_id = auth()->user()->parent_id ?? auth()->id();
        $payment = Payment::where('user_id', $user_id)->first();
        $package = Package::withTrashed()->where('price_id', $payment->price_id)->first();
        if ($payment) {
            $facebookMediaPage = MediaPagePayments::where(['user_id' => $user_id, 'media_id' => 1])->get();
            $linkedInMediaPage = MediaPagePayments::where(['user_id' => $user_id, 'media_id' => 2])->get();
            $twitterMediaPage = MediaPagePayments::where(['user_id' => $user_id, 'media_id' => 3])->get();
            $instagramMediaPage = MediaPagePayments::where(['user_id' => $user_id, 'media_id' => 4])->get();
            $googleMediaPage = MediaPagePayments::where(['user_id' => $user_id, 'media_id' => 5])->get();
            // $googleAnalyticsMediaPage = MediaPagePayments::where(['user_id' => $user_id, 'media_id' => 6])->get();
            // $googleAdsMediaPage = MediaPagePayments::where(['user_id' => $user_id, 'media_id' => 7])->get();

            $totalChannel = Count($facebookMediaPage) + Count($linkedInMediaPage) + Count($twitterMediaPage) + Count($instagramMediaPage) + Count($googleMediaPage);

            $linkedPage = UserMedia::where('user_id', $user_id)->select('media_page_id', 'is_deleted')->get();
            if ($payment) $is_expiry = now()->format('d M, Y') > $payment->end_date ? 'y' : 'n';

            if ($package) {
                if ($package->package_type == 'monthly') $monthlyPrice = $package->amount;
                if ($package->package_type == 'yearly') {
                    $annualPrice = $package->amount;
                    $monthlyPrice = $package->actual_yearly_amount / 12;
                }
            }

            return view('frontend.pages.general.active_subscription', compact('facebookMediaPage', 'linkedInMediaPage', 'twitterMediaPage', 'instagramMediaPage', 'googleMediaPage', 'linkedPage', 'is_expiry', 'payment', 'totalChannel', 'monthlyPrice', 'annualPrice', 'package'))->withTitle('Active Subscription');
        } else {
            flash('Subscription not active! Please Active Your subscription!')->success();
            return redirect()->back();
        }
    }

    public function list()
    {
        $is_expiry = '';
        $is_couponCode = '';
        $user_id = auth()->user()->parent_id ?? auth()->id();
        $facebookMediaPage = MediaPage::where(['user_id' => $user_id, 'media_id' => 1])->count();
        $linkedInMediaPage = MediaPage::where(['user_id' => $user_id, 'media_id' => 2])->count();
        $twitterMediaPage = MediaPage::where(['user_id' => $user_id, 'media_id' => 3])->count();
        $instagramMediaPage = MediaPage::where(['user_id' => $user_id, 'media_id' => 4])->count();

        $linkedPage = UserMedia::where('user_id', $user_id)->select('media_page_id', 'is_deleted')->get();
        $payment = Payment::where('user_id', $user_id)->first();
        // dd($payment);
        // If New users try to access the media page
        $package = $packageMonthly = $packageYearly = '';

        if (!$payment) {
            $packageMonthly = Package::where('package_type', 'monthly')->first();
            $packageYearly = Package::where('package_type', 'yearly')->first();
        } else {
            $userPaymentExpiryDate = ($payment) ? $payment->end_date : '';

            $checkDate = ($userPaymentExpiryDate) ? $userPaymentExpiryDate->format('Y-m-d') : '';
            $todayDate = Carbon::now()->format('Y-m-d');
            $package = Package::where('price_id', $payment->price_id)->first();
            // dd($checkDate, $todayDate);
            if ($checkDate >= $todayDate) {
                $package = Package::where('price_id', $payment->price_id)->withTrashed()->first();
            }
            // dd($package);
            // if(!$package)
        }

        if ($payment) $is_expiry = now()->format('d M, Y') > $payment->end_date ? 'y' : 'n';
        if ($payment) $is_couponCode = $payment->coupon_used == 'y'  ? 'y'   : 'n';

        return view('frontend.pages.mediaPage.list', compact('facebookMediaPage', 'linkedInMediaPage', 'twitterMediaPage', 'instagramMediaPage', 'linkedPage', 'is_expiry', 'payment', 'is_couponCode', 'package', 'packageMonthly', 'packageYearly'))->withTitle('Media Page List');
    }

    public function checkPages(Request $request)
    {
        DB::beginTransaction();
        try {
            $new = '';
            $user_id = auth()->user()->parent_id ?? auth()->id();
            $userMediaPageCount = UserMedia::where(['user_id'  => $user_id, 'media_id' => $request['media_id']])->count();
            $totalPagePurchased = MediaPagePayments::where(['user_id' =>  $user_id, 'media_id' => $request['media_id']])->count();
            if ($request['is_delete'] == 'y') {
                $userPageBuy = UserMedia::where(['user_id'  => $user_id, 'media_id' => $request['media_id'], 'is_deleted' => 'n'])->first();
                if ($userPageBuy) {
                    $userPageBuy->update([
                        'media_page_id' => $request['mediapage_id'],
                        'is_deleted'    => 'y'
                    ]);
                    $new = 'n';
                }
            } elseif ($request['is_delete'] == 'n') {
                $userPageBuy = UserMedia::where(['user_id'  => $user_id, 'media_id' => $request['media_id'], 'is_deleted' => 'y'])->first();
                if ($userPageBuy) {
                    $userPageBuy->update([
                        'media_page_id' => $request['mediapage_id'],
                        'is_deleted'    => 'n'
                    ]);
                    $new = 'n';
                } else {
                    $userPageBuy = UserMedia::create([
                        'custom_id'     => getUniqueString('user_media'),
                        'user_id'       => $user_id,
                        'media_id'      => $request['media_id'],
                        'media_page_id' => $request['mediapage_id'],
                        'is_deleted'    => $request['is_delete']
                    ]);
                }
            } elseif ($totalPagePurchased != $userMediaPageCount) {
                $userPageBuy = UserMedia::create([
                    'custom_id'     => getUniqueString('user_media'),
                    'user_id'       => $user_id,
                    'media_id'      => $request['media_id'],
                    'media_page_id' => $request['mediapage_id'],
                    'is_deleted'    => $request['is_delete']
                ]);
            }

            if ($request['is_used'] == 'y') {
                $mediaPaymentPage = MediaPagePayments::where(['user_id'  => $user_id, 'media_id' => $request['media_id'], 'is_used' => 'n'])->first();
                if ($mediaPaymentPage) {
                    $mediaPaymentPage->update([
                        'media_page_id' => $request['mediapage_id'],
                        'is_used'    => 'y'
                    ]);
                }
            } elseif ($request['is_used'] == 'n') {
                $mediaPaymentPage = MediaPagePayments::where(['user_id'  => $user_id, 'media_id' => $request['media_id'], 'is_used' => 'y'])->first();
                if ($mediaPaymentPage) {
                    $mediaPaymentPage->update([
                        'media_page_id' => $request['mediapage_id'],
                        'is_used'    => 'n'
                    ]);
                }
            }
            if (request()->ajax()) {
                $content = array('status' => 200, 'message' => "success.", 'data' => $new);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            $content = array('status' => $th->getCode(), 'message' => $th->getMessage(), 'data' => '');
        }
        return response()->json($content);
    }


    public function cancelSubscription()
    {
        DB::beginTransaction();
        try {

            $user_id = auth()->user()->parent_id ?? auth()->id();

            $MediaPagePayment = MediaPagePayments::where(['user_id' => $user_id])->forceDelete();

            $linkedPage = UserMedia::where('user_id', $user_id)->forceDelete();
            $payment = Payment::where('user_id', $user_id)->forceDelete();

            $SocialMediaDetail = SocialMediaDetail::where('user_id', $user_id)->forceDelete();
            $MediaPage = MediaPage::where('user_id', $user_id)->forceDelete();
            $userPlanUpdate = User::where('id', $user_id)->update(['is_subscribe' => 'n']);

            DB::commit();
            return response()->json(array('status' => 200, 'message' => "Canceled Subscription successfully!"));
        } catch (\Throwable $th) {
            // dd($th->getMessage());
            DB::rollback();
        }
    }

    //Check of coupon code
    public function checkCouponCode(Request $request)
    {
        $user_id = auth()->user()->parent_id ?? auth()->id();
        $couponCode = $request->couponCode ?? "";
        $checkCoupon = Coupon::where('name', $couponCode)->first();
        $isCouponUsedByUser = Payment::where(['coupon_used' => $couponCode, 'user_id' => $user_id])->exists();
        if (!$isCouponUsedByUser) {
            if ($checkCoupon) {
                $off_amount = $checkCoupon->amountOff == '' ? $checkCoupon->percentageOff . '%' : $checkCoupon->amountOff . ' AUD';
                return response()->json(array('status' => 200, 'message' => "Code Applied, You got {$off_amount} off.", 'off_amount' => $checkCoupon->percentageOff == '' ? $checkCoupon->amountOff : $checkCoupon->percentageOff, 'type' => $checkCoupon->amountOff == '' ? '%' : 'AUD'));
            }
            return response()->json(array('status' => 404, 'message' => "Coupon code not valid"));
        } else
            return response()->json(array('status' => 412, 'message' => "You have already used this coupon once"));
    }
}
