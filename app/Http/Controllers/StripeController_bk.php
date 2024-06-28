<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\General\ChangePassword;
use App\Http\Requests\Admin\General\ProfileUpdate;
use App\Mail\ContactUsMail;
use App\Models\CmsPage;
use App\Models\ContactUs;
use App\Models\Faqs;
use App\Models\Media;
use App\Models\Package;
use App\Models\QuickLink;
use App\Models\Role;
use App\Models\Setting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Stripe\Stripe;

class StripeController_bk extends Controller
{

    public function buyNow(Request $request,$id)
    {
        $package = Package::where('id',$id)->firstOrFail();
        // return view('stripe')->with(['totalprice' => $request->price]);
        return view('frontend.pages.stripe.checkout',compact('package'))->with(['title' => __('Checkout')]);
    }
    public function paymentSuccess(){
        // dd('paymentSuccess');
    }

    public function payment(Request $request){
      
         try{
            Stripe::setApiKey('sk_test_51LT3kRJWudNQ8ReCH0ExhOHKL7w0s2H8XRZiQMQyFSXNdcMTlrZXeDPfNJDRa6wy0l1rV3kYcpGsx1azxp2MzvRe00nYCarpn9');
 $stripe = new \Stripe\StripeClient(
            'sk_test_51LT3kRJWudNQ8ReCH0ExhOHKL7w0s2H8XRZiQMQyFSXNdcMTlrZXeDPfNJDRa6wy0l1rV3kYcpGsx1azxp2MzvRe00nYCarpn9'
        );
            // // dd('sdfds');
            // $customer = \Stripe\Customer::create();
            $customer = \Stripe\Customer::create(array(
                'email' => 'test@gmail.com',
                // 'source'  => $request->stripeToken
            ));
            $paymentIntent = $stripe->subscriptions->create([
              'customer' => $customer->id,
              
              'items' => [
                ['price' => 'price_1LT3mGJWudNQ8ReC2SqlZq8N'],
              ],
              // 'backdate_start_date' => strtotime("5-8-2022 5:00"),
              // 'cancel_at' => strtotime("25-8-2022 15:00"),
        ]);
            // $paymentIntent = \Stripe\PaymentIntent::create([
            //         'customer' => $customer->id,
            //         'setup_future_usage' => 'off_session',
            //         'amount' => 1 * 100,
            //         'currency' => 'USD',
            //         'automatic_payment_methods' => [  'enabled' => true ],
            //         'receipt_email' => 'abhinavparshar29@gmail.com'
            // ]);
            //  $paymentIntent = \Stripe\PaymentIntent::create([
            //     'customer' => $customer->id,
            //     'setup_future_usage' => 'off_session',
            //     'amount' => 1 * 100,
            //     'currency' => 'USD',
            //     // 'payment_method_types'=>['card'],
            //     'automatic_payment_methods' => [
            //         'enabled' => true,
            //     ],
            // ]);
            // print_r($paymentIntent);exit;
            $output = [
                'clientSecret' => $paymentIntent->client_secret,
            ];  
        }
        catch(\Exception $e){
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        return response()->json($output);
    }
}
