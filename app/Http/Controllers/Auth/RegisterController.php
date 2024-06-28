<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Media;
use App\Models\MediaPage;
use App\Models\UserMedia;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Stripe;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */


    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->stripe = new \Stripe\StripeClient(
            config('utility.STRIPE_SECRET')
        );
    }
    public function showRegistrationForm()
    {
        $countries = Country::pluck('phonecode')->toArray();
        return view('auth.register', compact('countries'))->withTitle('Register');
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        return Validator::make($data, [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // 'phone_code' => ['required'],
            'mobile_no' => ['required'],
            'company_name' => ['required', 'min:5'],
            'abn' => ['required', 'unique:users,abn'],
            'company_address' => ['required', 'min:5'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {

        $user =  User::create([
            'custom_id' => getUniqueString('users'),
            'full_name' => $data['full_name'] ?? "",
            'email' => $data['email'] ?? "",
            'phone_code' => $data['phone_code'] ?? "",
            'mobile_no' => $data['mobile_no'] ?? "",
            'password' => Hash::make($data['password']) ?? "",
            'company_name' => $data['company_name'] ?? "",
            'abn' => $data['abn'] ?? "",
            'company_address' => $data['company_address'] ?? "",
        ]);

        // $media_pages = MediaPage::get();
        // foreach($media_pages as $page){
        //     $user_media =  UserMedia::create([
        //         'custom_id' => getUniqueString('user_media'),
        //         'user_id' => $user->id ?? "",
        //         'media_id' => $page->media_id ?? "",
        //         'media_page_id' => $page->id ?? "",
        //     ]);
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
        return $data;
    }
}
