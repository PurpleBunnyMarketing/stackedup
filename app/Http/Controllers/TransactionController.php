<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\PageTransactions;
use App\Models\UserMedia;
use App\Models\MediaPage;
use App\User;
use App\Models\Package;
use App\Models\Payment;
use App\Models\MediaPagePayments;
use App\Models\Coupon;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function checkout(Request $request)
    {
        try {
            if ($request['radioPlan'] == '') {
                return redirect()->back()->withErrors(['plan' => 'Select any plan']);
            }
            $plan               = $request['radioPlan'] ?? '';
            $channelCount       = $request['totalchannelCount'] ?? '';
            $priceMonthly       = $request['price_monthly'] ?? '';
            $priceAnnual        = $request['price_annual'] ?? '';
            $subTotal           = $request['subTotal'] ?? '';
            $facePageCount      = $request['facebook_pageCount']   ?? 0;
            $linkedPageCount    = $request['linkedin_pageCount'] ?? 0;
            $twitterPageCount   = $request['twitter_pageCount'] ?? 0;
            $instaPageCount     = $request['instagram_pageCount'] ?? 0;
            $googlePageCount    = $request['google_pageCount'] ?? 0;
            // $googleAnalyticsPageCount    = $request['google_analytics_pageCount'] ?? 0;
            // $googleAdsPageCount    = $request['google_ads_pageCount'] ?? 0;
            $couponCode         = $request['discountCode'] ?? '';
            $message = '';
            if (isset($couponCode)) {
                $coupon = Coupon::whereName($couponCode)->first();
                if (isset($coupon->id) && ($coupon->percentageOff == 100  || (substr($subTotal, 1) - ($coupon->amountOff ?? 0)) == 0)) {
                    $message = 'You have used 100% off coupon code so you will get first interval free and from next interval your card will charge as usual.';
                }
            }
            return view('frontend.pages.stripe.checkout', compact('channelCount', 'plan', 'priceMonthly', 'priceAnnual', 'subTotal', 'facePageCount', 'linkedPageCount', 'twitterPageCount', 'instaPageCount', 'googlePageCount', 'couponCode', 'message'))->with(['title' => __('Checkout')]);
            // return view('frontend.pages.mediaPageStripe.checkout', compact('channelCount' , 'plan'));
        } catch (Exception $e) {
            // dd($e->getMessage(), $e->getLine());
            flash(__('flash_message.something'))->error();
            return redirect()->back();
        }
        // return view('frontend.pages.mediaPageStripe.checkout', compact('subTotal'));
    }

    public function paymentSuccess(Request $request)
    {
        DB::beginTransaction();
        try {
            $auth = Auth::user();
            $user = User::where('id', $auth->id)->first();
            $stripe = new \Stripe\StripeClient(
                config('utility.STRIPE_SECRET')
            );
            $couponName = $request->couponCode ?? "";
            $package = Package::where('package_type', $request->plan)->orderBy('created_at', 'desc')->first();
            $checkPaymentexist = Payment::where(['user_id' => $auth->id])->first();
            $packageCreatedDate = ($package) ? $package->created_at : '';
            $userExpiryDate = ($checkPaymentexist) ? $checkPaymentexist->end_date : '';
            $coupon = Coupon::whereName($couponName)->first() ?? '';
            // checking new package date and user expiry date
            if ($packageCreatedDate > $userExpiryDate && $checkPaymentexist) {
                $package = Package::where('package_type', $request->plan)->where('price_id', $checkPaymentexist->price_id)->first();
            }

            $attched =  $stripe->customers->createSource(
                Auth::user()->customer_id ?? "",
                ['source' => $request->payMentId]
            );
            // fetching last purchased page count of user
            $getPageQty = 0;
            $getPageQty = MediaPagePayments::where('user_id', $auth->id)->count();
            $totalPageQty = $getPageQty + $request->qty;



            if (!$checkPaymentexist) {
                $subscription = $stripe->subscriptions->create([
                    'customer' =>  $auth->customer_id ?? "",

                    'items' => [
                        ['price' => $package->price_id, 'quantity' => $totalPageQty],
                    ],
                    'coupon' => $coupon->coupon_id ?? "",

                ]);
                $user->is_subscribe = 'y';
                $user->save();

                $payment = new Payment;
                $payment->custom_id             = getUniqueString('payments');
                $payment->user_id               = $auth->id ?? "";
                $payment->payment_id            = $subscription->latest_invoice ?? "";
                $payment->subscription_id       = $subscription->id ?? "";
                $payment->price_id              = $subscription->plan->id ?? "";
                $payment->type                  = $package->package_type ?? "";
                $payment->amount                = ($package->amount * $request->qty) ?? "";
                $payment->start_date            = date('Y-m-d', $subscription->current_period_start) ?? "";
                $payment->end_date              = date('Y-m-d', $subscription->current_period_end) ?? "";
                $payment->coupon_used           = ($request->couponCode) ? 'y' : '';
                $payment->used_coupon_name      = ($request->couponCode) ? $couponName : '';
                $payment->discount_amount       = $request->couponCode ?  ($coupon->percentageOff !== '' ? ($package->amount * $request->qty) * $coupon->percentageOff / 100  : $coupon->amountOff) : '';
                $payment->save();
            } elseif ($checkPaymentexist && $checkPaymentexist->type == $request->plan) {

                $retrieveSubscription = $stripe->subscriptions->retrieve($checkPaymentexist->subscription_id);

                // if($checkPaymentexist->price_id == $package->price_id){
                $subscription = $stripe->subscriptions->update(
                    $retrieveSubscription->id,
                    [
                        'proration_behavior' => 'always_invoice',
                        'items' => [
                            [
                                'id' => $retrieveSubscription->items->data[0]->id,
                                'price' => $package->price_id, 'quantity' => $totalPageQty
                            ],
                        ],
                        'coupon' => $coupon->coupon_id ?? "",
                    ]
                );

                $updatePayment = payment::where(['user_id' => $auth->id, 'subscription_id' => $subscription->id])
                    ->update([
                        'price_id' => $subscription->plan->id ?? "",
                        'type'     => $package->package_type ?? "",
                        'amount'   => ($package->amount * $totalPageQty) ?? "",
                        'coupon_used' => ($request->couponCode) ? 'y' : "",
                        'used_coupon_name' => ($request->couponCode) ? $request->couponCode : "",
                        'discount_amount' => ($request->couponCode) ?  $coupon->percentageOff !== '' ? ($package->amount * $request->qty) * $coupon->percentageOff / 100  : $coupon->amountOff : '',
                    ]);
                // }
                // else{

                // }
                $payment = payment::where(['user_id' => $auth->id, 'subscription_id' => $subscription->id])->first();
            }
            $mediaPages = [];
            if ($request['faceCount'] > 0) {
                for ($i = 1; $i <= $request['faceCount']; $i++) {
                    $mediaPages[] = [
                        'custom_id'     => getUniqueString('media_page_payments'),
                        'user_id'       => $auth->id ?? '',
                        'media_id'      => 1,
                        'payment_id'    => $payment->id,
                        'is_used'       => 'n',
                        'is_expiry'     => 'n',
                        'created_at'    => \Carbon\Carbon::now(),
                        'updated_at'    => \Carbon\Carbon::now(),
                    ];
                    // Facebook Ads
                    $mediaPages[] = [
                        'custom_id'     => getUniqueString('media_page_payments'),
                        'user_id'       => $auth->id ?? '',
                        'media_id'      => 8,
                        'payment_id'    => $payment->id,
                        'is_used'       => 'n',
                        'is_expiry'     => 'n',
                        'created_at'    => \Carbon\Carbon::now(),
                        'updated_at'    => \Carbon\Carbon::now(),
                    ];
                }
            }
            if ($request['linkedCount'] > 0) {
                for ($j = 1; $j <= $request['linkedCount']; $j++) {
                    $mediaPages[] = [
                        'custom_id'     => getUniqueString('media_page_payments'),
                        'user_id'       => $auth->id ?? '',
                        'media_id'      => 2,
                        'payment_id'    => $payment->id,
                        'is_used'       => 'n',
                        'is_expiry'     => 'n',
                        'created_at'    => \Carbon\Carbon::now(),
                        'updated_at'    => \Carbon\Carbon::now(),
                    ];
                }
            }
            if ($request['twitCount'] > 0) {
                for ($k = 1; $k <= $request['twitCount']; $k++) {
                    $mediaPages[] = [
                        'custom_id'     => getUniqueString('media_page_payments'),
                        'user_id'       => $auth->id ?? '',
                        'media_id'      => 3,
                        'payment_id'    => $payment->id,
                        'is_used'       => 'n',
                        'is_expiry'     => 'n',
                        'created_at'    => \Carbon\Carbon::now(),
                        'updated_at'    => \Carbon\Carbon::now(),
                    ];
                }
            }
            if ($request['instaCount'] > 0) {
                for ($z = 1; $z <= $request['instaCount']; $z++) {
                    $mediaPages[] = [
                        'custom_id'     => getUniqueString('media_page_payments'),
                        'user_id'       => $auth->id ?? '',
                        'media_id'      => 4,
                        'payment_id'    => $payment->id,
                        'is_used'       => 'n',
                        'is_expiry'     => 'n',
                        'created_at'    => \Carbon\Carbon::now(),
                        'updated_at'    => \Carbon\Carbon::now(),
                    ];
                }
            }
            if ($request['googleCount'] > 0) {
                for ($z = 1; $z <= $request['googleCount']; $z++) {
                    // Google My Business
                    $mediaPages[] = [
                        'custom_id'     => getUniqueString('media_page_payments'),
                        'user_id'       => $auth->id ?? '',
                        'media_id'      => 5,
                        'payment_id'    => $payment->id,
                        'is_used'       => 'n',
                        'is_expiry'     => 'n',
                        'created_at'    => \Carbon\Carbon::now(),
                        'updated_at'    => \Carbon\Carbon::now(),
                    ];
                    // Google Analytics
                    $mediaPages[] = [
                        'custom_id'     => getUniqueString('media_page_payments'),
                        'user_id'       => $auth->id ?? '',
                        'media_id'      => 6,
                        'payment_id'    => $payment->id,
                        'is_used'       => 'n',
                        'is_expiry'     => 'n',
                        'created_at'    => \Carbon\Carbon::now(),
                        'updated_at'    => \Carbon\Carbon::now(),
                    ];
                    // Google Ads
                    $mediaPages[] = [
                        'custom_id'     => getUniqueString('media_page_payments'),
                        'user_id'       => $auth->id ?? '',
                        'media_id'      => 7,
                        'payment_id'    => $payment->id,
                        'is_used'       => 'n',
                        'is_expiry'     => 'n',
                        'created_at'    => \Carbon\Carbon::now(),
                        'updated_at'    => \Carbon\Carbon::now(),
                    ];
                }
            }
            // Google Analytics
            // if ($request['googleAnalyticsCount'] > 0) {
            //     for ($z = 1; $z <= $request['googleAnalyticsCount']; $z++) {
            //         $mediaPages[] = [
            //             'custom_id'     => getUniqueString('media_page_payments'),
            //             'user_id'       => $auth->id ?? '',
            //             'media_id'      => 6,
            //             'payment_id'    => $payment->id,
            //             'is_used'       => 'n',
            //             'is_expiry'     => 'n',
            //             'created_at'    => \Carbon\Carbon::now(),
            //             'updated_at'    => \Carbon\Carbon::now(),
            //         ];
            //     }
            // }
            // Google Ads
            // if ($request['googleAdsCount'] > 0) {
            //     for ($z = 1; $z <= $request['googleAdsCount']; $z++) {
            //         $mediaPages[] = [
            //             'custom_id'     => getUniqueString('media_page_payments'),
            //             'user_id'       => $auth->id ?? '',
            //             'media_id'      => 7,
            //             'payment_id'    => $payment->id,
            //             'is_used'       => 'n',
            //             'is_expiry'     => 'n',
            //             'created_at'    => \Carbon\Carbon::now(),
            //             'updated_at'    => \Carbon\Carbon::now(),
            //         ];
            //     }
            // }
            MediaPagePayments::insert($mediaPages);
            flash("Payment Added Successfully! You can start adding channels.")->success();
            DB::commit();
        } catch (Exception $e) {
            Log::info($e->getMessage());
            // dump($e->getMessage());
            DB::rollBack();
            flash(__('flash_message.something'))->error();
        }
        return redirect()->route('social-media-list');
    }
}
