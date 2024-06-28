<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\MediaPage;
use App\Models\SocialMediaDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class GoogleAdsController extends Controller
{
    private $accounts = [];
    public function redirectToGoogleAds()
    {
        return Socialite::driver('google')->with(["prompt" => "consent select_account", "access_type" => "offline", 'redirect_uri' => url('auth/google_ads/callback')])->scopes(['openid', 'https://www.googleapis.com/auth/userinfo.profile', 'https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/adwords'])->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleGoogleAdsCallback()
    {
        DB::beginTransaction();
        try {
            $user = Socialite::driver('google')->redirectUrl(url('auth/google_ads/callback'))->user();

            $google = SocialMediaDetail::where('id', $user->id)->first();
            $tokenExpiryDays = config('utility.GOOGLE_ENV') == 'testing' ? 7 : 60;

            if (!$google) {

                $media = Media::where('name', 'Google Ads')->first();

                $existingMediaPages = MediaPage::where(['media_id' => $media->id, 'user_id' => Auth::user()->id])->get();
                if ($existingMediaPages && $existingMediaPages->count() > 0) {
                    foreach ($existingMediaPages as $page) {
                        $page->update(['is_old' => 'y']);
                    }
                }
                // MediaPage::where([
                //     'media_id' => $media->id,
                //     'user_id' => Auth::user()->id
                // ])->update(['is_old' => 'y']);

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
                        'media_id' => $media->id ?? null,
                        'social_id' => $user->id ?? null,
                        'token' => $user->token ?? null,
                        'token_secret' => $user->tokenSecret ?? null,
                        'token_expiry' => Carbon::parse(now())->addDays($tokenExpiryDays)->toDateString(),
                        'refresh_token' => $user->refreshToken ?? null,
                    ]);
                }
                if (!$social_media_detail) throw new Exception();

                $googleAdsAccounts = $this->getAdsAccounts($user);

                if (isset($googleAdsAccounts)) {
                    foreach ($googleAdsAccounts as $account) {
                        $imageContent = asset('frontend/images/default_profile.jpg');

                        $mediaPages = MediaPage::where(['media_id' => $media->id, 'user_id' => auth()->user()->id, 'page_id' => $account['id'], 'page_name' => $account['name']])->first();

                        if (!$mediaPages) {
                            $media_page = MediaPage::create([
                                'custom_id' => getUniqueString('media_pages'),
                                'media_id'  => $media->id ?? null,
                                'user_id'   => Auth::user()->id ?? "",
                                'page_id'   => $account['id'] ?? null,
                                'page_name' => $account['name'] ?? null,
                                'is_old'    => 'n',
                                'social_media_detail_id' => $social_media_detail->id,
                                'image_url' => $imageContent ?? null
                            ]);
                        } else {
                            $existMedia_page = MediaPage::where([
                                'media_id' => $media->id ?? null,
                                'user_id'  => Auth::user()->id ?? "",
                                'page_id'  => $account['id'] ?? null
                            ])->first();
                            if ($existMedia_page->page_id != $account['id']) {
                                $media_page = MediaPage::where(
                                    [
                                        'media_id' => $media->id ?? null,
                                        'user_id'  => Auth::user()->id ?? "",
                                        'page_id'  => $account['id'] ?? null,
                                        'is_old'   => 'n'
                                    ]
                                )->update([
                                    'page_name' => $account['name'] ?? null,
                                    'is_old'    => 'y',
                                    'social_media_detail_id' => $social_media_detail->id,
                                    'image_url' => $imageContent ?? null
                                ]);
                            } elseif ($existMedia_page->page_id == $account['id']) {

                                $media_page = MediaPage::where(
                                    [
                                        'media_id' => $media->id ?? null,
                                        'user_id'  => Auth::user()->id ?? "",
                                        'page_id'  => $account['id'] ?? null,
                                        'is_old'   => 'y'
                                    ]
                                )->update([
                                    'page_name' => $account['name'] ?? null,
                                    'is_old'    => 'n',
                                    'social_media_detail_id' => $social_media_detail->id,
                                    'image_url' => $imageContent ?? null
                                ]);
                            }
                        }
                    }
                }
            }
            DB::commit();
            flash("Google Ads Account Link successfully!")->success();
        } catch (Exception $e) {
            DB::rollback();
            // dd($e->getMessage(), 'error');
            flash($e->getMessage())->error();
        }
        return redirect()->route('social-media-list', ['socialMedia' => 'Google Ads']);
    }

    public function getAdsAccounts($auth_user)
    {
        try {
            $accountListRequest = Http::withToken($auth_user->token)->withHeaders(['developer-token' => config('utility.GOOGLE_ADS_DEVELOPER_TOKEN')])->acceptJson()->get('https://googleads.googleapis.com/v14/customers:listAccessibleCustomers');
            if ($accountListRequest->failed()) $accountListRequest->throw();

            $accountListResponse = $accountListRequest->collect()->toArray();
            if (!isset($accountListResponse['resourceNames'])) throw new Exception();
            foreach ($accountListRequest['resourceNames'] as $account_id) {
                $account = $this->getAccountInformation($account_id, $auth_user->token);
                if (!$account['manager']) $this->accounts[] = $account;
            }
        } catch (Exception $th) {
            return $this->accounts;
        }
        return $this->accounts;
    }

    public function getAccountInformation($customer_id, $auth_token): array
    {
        $payload = [
            'query' => "SELECT customer.descriptive_name,customer.manager FROM customer",
        ];
        $url = "https://googleads.googleapis.com/v14/{$customer_id}/googleAds:search";
        $accountInfoRequest = Http::withToken($auth_token)->withHeaders(['developer-token' => config('utility.GOOGLE_ADS_DEVELOPER_TOKEN')])->acceptJson()->post($url, $payload);

        if ($accountInfoRequest->failed()) $accountInfoRequest->throw();

        $accountInfoResponse = $accountInfoRequest->collect()->toArray();
        // if ($accountInfoResponse['results'][0]['customer']['manager']);
        return [
            'id' => $customer_id,
            'name' => $accountInfoResponse['results'][0]['customer']['descriptiveName']  ?? null,
            'manager' => $accountInfoResponse['results'][0]['customer']['manager'] ?? null,
        ];
    }
}
