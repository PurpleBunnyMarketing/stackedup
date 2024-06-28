<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Payment;
use App\User;
use Exception;
use Illuminate\Auth\Access\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class StripeController extends Controller
{

    public function buyNow(Request $request, $id)
    {
        $package = Package::where('id', $id)->firstOrFail();
        return view('frontend.pages.stripe.checkout', compact('package'))->with(['title' => __('Checkout')]);
    }
    public function paymentSuccess(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = User::where('id', Auth::id())->first();
            $stripe = new \Stripe\StripeClient(
                config('utility.STRIPE_SECRET')
            );
            $package = Package::where('id', $request->package_id)->first();


            $attched =  $stripe->customers->createSource(
                Auth::user()->customer_id ?? "",
                ['source' => $request->payMentId]
            );

            $subscription = $stripe->subscriptions->create([
                'customer' =>  Auth::user()->customer_id ?? "",

                'items' => [
                    ['price' => $package->price_id],
                ],
            ]);

            $user->is_subscribe = 'y';
            $user->save();

            $payment = new Payment;
            $payment->custom_id = getUniqueString('payments');
            $payment->user_id = Auth::id() ?? "";
            $payment->payment_id = $subscription->latest_invoice ?? "";
            $payment->subscription_id = $subscription->id ?? "";
            $payment->price_id = $subscription->plan->id ?? "";
            $payment->type = $package->package_type ?? "";
            $payment->amount = $package->amount ?? "";
            $payment->start_date = date('Y-m-d', $subscription->current_period_start) ?? "";
            $payment->end_date = date('Y-m-d', $subscription->current_period_end) ?? "";
            $payment->save();


            flash("Payment successfully.")->success();
            DB::commit();
            return redirect()->route('social-media-list');
        } catch (Exception $e) {
            DB::rollBack();
            // flash($e->getMessage())->error();
            flash(__('flash_message.something'))->error();
            // dd($e->getMessage());
            return redirect()->back();
        }
    }

    public function createSubscription(Request $request)
    {
        try {
            $customer_id = $request['data']['object']['customer'] ?? "";
            $user = User::where('customer_id', $customer_id)->first();
            $data = Payment::where('payment_id', $request['data']['object']['latest_invoice'])->first();
            if (empty($data)) {
                $latest_payment = Payment::where('user_id', $user->customer_id)->latest()->first();
                $package = Package::where('price_id', $latest_payment->price_id)->first();
                $payment = new Payment;
                $payment->custom_id = getUniqueString('payments');
                $payment->user_id = $user->id ?? "";
                $payment->payment_id = $request['data']['object']['latest_invoice'] ?? "";
                $payment->subscription_id = $latest_payment->subscription_id ?? "";
                $payment->price_id = $latest_payment->price_id ?? "";
                $payment->type = $package->package_type ?? "";
                $payment->start_date = date('Y-m-d', $latest_payment->current_period_end) ?? "";
                if ($package->package_type == 'monthly') {

                    $dt = strtotime($latest_payment->current_period_end);
                    $payment->end_date = date('Y-m-d', strtotime("+1 month", $dt)) ?? "";
                } else {
                    $dt = strtotime($latest_payment->current_period_end);
                    $payment->end_date = date('Y-m-d', strtotime("+1 year", $dt)) ?? "";
                }
                $payment->payment_status = "completed";
                $payment->save();
            } else {
                $data->payment_status = "completed";
                $data->save();
            }
            $orderLog = new Logger('subscriptions');
            $orderLog->pushHandler(new StreamHandler(storage_path('logs/subscriptions.log')), Logger::INFO);
            $orderLog->info('RequestData', (array)($request->all()));
        } catch (\Exception $e) {
            $response['customer_id'] = $customer_id ?? "";
            $response['user'] = $user->id ?? "";
            $response['data'] = $data->id ?? "";
            $response['invoice'] = $request['data']['object']['latest_invoice'] ?? "";
            $response['error '] = $e->getMessage() ?? "";
            $orderLog = new Logger('error_subscriptions');
            $orderLog->pushHandler(new StreamHandler(storage_path('logs/error_subscriptions.log')), Logger::INFO);
            $orderLog->info('RequestData', (array)($response));
        }
        return response()->json($orderLog, 200, [], JSON_NUMERIC_CHECK);
    }

    public function checkSubscription()
    {
        $is_subscribed =  User::where('id', Auth::user()->id)->where('is_subscribe', 'y')->first();
        if ($is_subscribed != null)
            return response()->json(1);
        else
            return response()->json(0);
    }
}
