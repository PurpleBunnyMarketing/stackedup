<?php

namespace App\Console\Commands;

use App\Models\MediaPagePayments;
use App\Models\Package;
use App\Models\Payment;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RenewalNewPriceSubscriptionUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'new_price_renewal:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Renewal All old user with new price for subscription';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::beginTransaction();
        try {
            $users = User::whereIs_subscribe('y')->get();
            foreach ($users as $user) {
                $stripe = new \Stripe\StripeClient(
                    config('utility.STRIPE_SECRET')
                );
                $checkPaymentexist = Payment::where('user_id', $user->id)->first();

                $userExpiryDate = ($checkPaymentexist) ? $checkPaymentexist->end_date : '';

                $checkDate = ($userExpiryDate) ? $userExpiryDate->subDays(1) : '';
                $todayDate = Carbon::now()->format('Y-m-d');
                // checking new package date and user expiry date
                if ($checkDate == $todayDate) {

                    // if($packageCreatedDate < $userExpiryDate && $checkPaymentexist){
                    $package = Package::where('package_type', $checkPaymentexist->type)->orderBy('created_at', 'desc')->first();
                    if ($package->price_id != $checkPaymentexist->price_id) {
                        $getPageQty = MediaPagePayments::where('user_id', $user->id)->count();

                        $retrieveSubscription = $stripe->subscriptions->retrieve($checkPaymentexist->subscription_id);

                        $subscription = $stripe->subscriptions->update(
                            $retrieveSubscription->id,
                            [
                                'proration_behavior' => 'always_invoice',
                                'items' => [
                                    [
                                        'id' => $retrieveSubscription->items->data[0]->id,
                                        'price' => $package->price_id, 'quantity' => $getPageQty
                                    ],
                                ],
                                'coupon' => $request->couponCode ?? "",
                            ]
                        );

                        $updatePayment = payment::where(['user_id' => $user->id, 'subscription_id' => $subscription->id])
                            ->update([

                                'price_id' => $subscription->plan->id ?? "",
                                'type'     => $package->package_type ?? "",
                                'amount'   => ($package->amount * $getPageQty) ?? ""
                            ]);
                    }

                    Log::debug('Update Renewal with new product id');
                    // }
                }
                DB::commit();
            }
        } catch (Exception $e) {
            Log::debug($e->getMessage());
            DB::rollBack();
        }
    }
}
