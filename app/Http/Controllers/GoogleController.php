<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\MediaPage;
use App\Models\SocialMediaDetail;
use Carbon\Carbon;
use Exception;
use Google\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Google\Service\MyBusinessAccountManagement;
use Google\Service\MyBusinessBusinessInformation;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->with(["prompt" => "consent select_account", "access_type" => "offline"])->scopes(['openid', 'https://www.googleapis.com/auth/userinfo.profile', 'https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/business.manage', 'https://www.googleapis.com/auth/plus.business.manage'])->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleGoogleCallback()
    {
        DB::beginTransaction();
        try {
            $user = Socialite::driver('google')->user();

            $google = SocialMediaDetail::where('id', $user->id)->first();
            $tokenExpiryDays = config('utility.GOOGLE_ENV') == 'testing' ? 7 : 60;
            if (!$google) {

                // Get all the Associated Analytics Account
                /* $response = $client->get("GET https://mybusinessbusinessinformation.googleapis.com/v1/accout_id/locations
                ", [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $user->token . '',
                        'Accept' => 'application/json',
                    ],
                ]); */

                $media = Media::where('name', 'Google My Business')->first();

                $existingMediaPages = MediaPage::where(['media_id' => $media->id, 'user_id' => Auth::user()->id])->get();
                if ($existingMediaPages && $existingMediaPages->count() > 0) {
                    foreach ($existingMediaPages as $page) {
                        $page->update(['is_old' => 'y']);
                    }
                }

                $mediaDetails = SocialMediaDetail::where(['user_id' => Auth::user()->id, 'media_id' => $media->id, 'social_id' => $user->id])->exists();
                if ($mediaDetails) {
                    SocialMediaDetail::where(
                        [
                            'user_id' => Auth::user()->id,
                            'media_id' => $media->id ?? "",
                            'social_id' => $user->id ?? "",
                        ]
                    )->update([
                        'token'     => $user->token ?? "",
                        'token_secret' => $user->tokenSecret ?? "",
                        'token_expiry' => Carbon::parse(now())->addDays($tokenExpiryDays)->toDateString(),
                        'refresh_token' => $user->refreshToken ?? '',
                    ]);
                    $social_media_detail = SocialMediaDetail::where([
                        'user_id' => Auth::user()->id,
                        'media_id' => $media->id,
                        'social_id' => $user->id,
                    ])->first();
                } else {
                    $social_media_detail = SocialMediaDetail::create([
                        'custom_id' => getUniqueString('social_media_details'),
                        'user_id' => Auth::user()->id,
                        'media_id' => $media->id ?? "",
                        'social_id' => $user->id ?? "",
                        'token' => $user->token ?? "",
                        'token_secret' => $user->tokenSecret ?? "",
                        'token_expiry' => Carbon::parse(now())->addDays($tokenExpiryDays)->toDateString(),
                        'refresh_token' => $user->refreshToken ?? '',
                    ]);
                }
                if (!$social_media_detail) throw new Exception();

                $client = new Client();
                $client->setAccessToken($user->token);

                $personal_account = $this->getUserPersonalAccount($client);

                $locations = $this->getAllLocations($client, $personal_account->name);

                if (isset($locations)) {
                    foreach ($locations as $account) {
                        $imageContent = asset('frontend/images/default_profile.jpg');
                        // $account->name -> containes the value  'account/id' ex. 'account/123456798'
                        $mediaPages = MediaPage::where(['media_id' => $media->id, 'user_id' => auth()->user()->id, 'page_id' => $account['name'], 'page_name' => $account['title']])->first();

                        if (!$mediaPages) {
                            $media_page = MediaPage::create([
                                'custom_id' => getUniqueString('media_pages'),
                                'media_id'  => $media->id ?? null,
                                'user_id'   => Auth::user()->id ?? "",
                                'page_id'   => $account['name'] ?? null,
                                'page_name' => $account['title'] ?? null,
                                'is_old'    => 'n',
                                'social_media_detail_id' => $social_media_detail->id,
                                'image_url' => $imageContent ?? null,
                                'google_mobile_number' => $account->phoneNumbers->primaryPhone ?? null,
                            ]);
                        } else {
                            $existMedia_page = MediaPage::where([
                                'media_id' => $media->id ?? null,
                                'user_id'  => Auth::user()->id ?? "",
                                'page_id'  => $account['name'] ?? null
                            ])->first();
                            if ($existMedia_page->page_id != $account['name']) {
                                $media_page = MediaPage::where(
                                    [
                                        'media_id' => $media->id ?? null,
                                        'user_id'  => Auth::user()->id ?? "",
                                        'page_id'  => $account['name'] ?? null,
                                        'is_old'   => 'n'
                                    ]
                                )->update([
                                    'page_name' => $account['title'] ?? null,
                                    'is_old'    => 'y',
                                    'social_media_detail_id' => $social_media_detail->id,
                                    'image_url' => $imageContent ?? null,
                                    'google_mobile_number' => $account->phoneNumbers->primaryPhone ?? null,
                                ]);
                            } elseif ($existMedia_page->page_id == $account['name']) {
                                $media_page = MediaPage::where(
                                    [
                                        'media_id' => $media->id ?? null,
                                        'user_id'  => Auth::user()->id ?? "",
                                        'page_id'  => $account['name'] ?? null,
                                        'is_old'   => 'y'
                                    ]
                                )->update([
                                    'page_name' => $account['title'] ?? null,
                                    'is_old'    => 'n',
                                    'social_media_detail_id' => $social_media_detail->id,
                                    'image_url' => $imageContent ?? null,
                                    'google_mobile_number' => $account->phoneNumbers->primaryPhone ?? null,
                                ]);
                            }
                        }
                    }
                }
            }
            DB::commit();
            flash("Google Business Link successfully!")->success();
            return redirect()->route('social-media-list', ['socialMedia' => 'Google My Business']);
        } catch (Exception $e) {
            // dd($e->getMessage(), $e->getLine());
            DB::rollback();
            flash('Something went wrong.')->error();
            return redirect()->route('social-media-list');
        }
    }
    // Get All the Locations Of users
    public function getAllLocations($client, $account_id): array
    {
        $service = new MyBusinessBusinessInformation($client);

        $parameter['readMask'] = 'name,title,websiteUri,phoneNumbers';
        $parameter['pageSize'] = '100';
        $locationsResponse = $service->accounts_locations->listAccountsLocations($account_id, $parameter);

        return $locationsResponse->getLocations();
    }


    public function getUserPersonalAccount($client): object
    {
        $myBusiness = new MyBusinessAccountManagement($client);
        $accounts = $myBusiness->accounts->listAccounts();
        $accounts = $accounts->getAccounts();

        // Get the Personal account from the Multiple Account
        $personalAccount = array_filter($accounts, fn ($account) => $account->type == 'PERSONAL')[0];

        return $personalAccount;
    }
}
