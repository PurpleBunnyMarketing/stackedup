<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\MediaPage;
use App\Models\SocialMediaDetail;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class GoogleAnalyticsController extends Controller
{
    public function redirectToGoogleAnalytics()
    {
        return Socialite::driver('google')->with(["prompt" => "consent select_account", "access_type" => "offline", 'redirect_uri' => url('auth/google_analytics/callback')])->scopes(['openid', 'https://www.googleapis.com/auth/userinfo.profile', 'https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/analytics', 'https://www.googleapis.com/auth/analytics.readonly'])->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleGoogleAnalyticsCallback()
    {
        DB::beginTransaction();
        try {
            $user = Socialite::driver('google')->redirectUrl(url('auth/google_analytics/callback'))->user();

            $google = SocialMediaDetail::where('id', $user->id)->first();
            $is_page_relink = false;
            if (!$google) {

                $media = Media::where('name', 'Google Analytics 4')->first();
                $tokenExpiryDays = config('utility.GOOGLE_ENV') == 'testing' ? 7 : 60;

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
                        'media_id' => $media->id ?? "",
                        'social_id' => $user->id ?? "",
                        'token' => $user->token ?? "",
                        'token_secret' => $user->tokenSecret ?? "",
                        'token_expiry' => Carbon::parse(now())->addDays($tokenExpiryDays)->toDateString(),
                        'refresh_token' => $user->refreshToken ?? '',
                    ]);
                }
                if (!$social_media_detail) throw new Exception();

                $httpClient = new Client();
                $googleAccounts = $this->getAnalyticsAccounts($httpClient, $user->token);

                if (isset($googleAccounts)) {
                    foreach ($googleAccounts as $account) {
                        $imageContent = asset('frontend/images/default_profile.jpg');
                        // $account->name -> containes the value  'account/id' ex. 'account/123456798'
                        $mediaPages = MediaPage::where(['media_id' => $media->id, 'user_id' => auth()->user()->id, 'page_id' => $account['account'], 'page_name' => $account['displayName']])->first();

                        if (!$mediaPages) {
                            $media_page = MediaPage::create([
                                'custom_id' => getUniqueString('media_pages'),
                                'media_id'  => $media->id ?? null,
                                'user_id'   => Auth::user()->id ?? "",
                                'page_id'   => $account['account'] ?? null,
                                'page_name' => $account['displayName'] ?? null,
                                'is_old'    => 'n',
                                'social_media_detail_id' => $social_media_detail->id,
                                'image_url' => $imageContent ?? null,
                                'account_properties' => json_encode($account, true)
                            ]);
                        } else {
                            $existMedia_page = MediaPage::where([
                                'media_id' => $media->id ?? null,
                                'user_id'  => Auth::user()->id ?? "",
                                'page_id'  => $account['account'] ?? null
                            ])->first();
                            if ($existMedia_page->page_id != $account['account']) {
                                $media_page = MediaPage::where(
                                    [
                                        'media_id' => $media->id ?? null,
                                        'user_id'  => Auth::user()->id ?? "",
                                        'page_id'  => $account['account'] ?? null,
                                        'is_old'   => 'n'
                                    ]
                                )->update([
                                    'page_name' => $account['displayName'] ?? null,
                                    'is_old'    => 'y',
                                    'social_media_detail_id' => $social_media_detail->id,
                                    'image_url' => $imageContent ?? null,
                                    'account_properties' => json_encode($account, true)
                                ]);
                            } elseif ($existMedia_page->page_id == $account['account']) {
                                $media_page = MediaPage::where(
                                    [
                                        'media_id' => $media->id ?? null,
                                        'user_id'  => Auth::user()->id ?? "",
                                        'page_id'  => $account['account'] ?? null,
                                        'is_old'   => 'y'
                                    ]
                                )->update([
                                    'page_name' => $account['displayName'] ?? null,
                                    'is_old'    => 'n',
                                    'social_media_detail_id' => $social_media_detail->id,
                                    'image_url' => $imageContent ?? null,
                                    'account_properties' => json_encode($account, true)
                                ]);
                            }
                        }
                    }
                }
            }
            DB::commit();
            flash("Google Analytics Account Link successfully!")->success();
            return redirect()->route('social-media-list', ['socialMedia' => 'Google Analytics 4', 'is_page_is_relink' => $is_page_relink]);
        } catch (Exception $e) {
            // dd($e->getMessage());
            DB::rollback();
            flash($e->getMessage())->error();
        }
        return redirect()->route('social-media-list');
    }


    /**
     * Get all the Analytics Account
     *
     * @param Google/Client $client
     * @param string $auth_user_token
     * @return array
     */
    public function getAnalyticsAccounts($client, $auth_user_token): array
    {

        // $response = $client->get("https://www.googleapis.com/analytics/v3/management/accounts/", [
        //     'headers' => [
        //         'Authorization' => 'Bearer ' . $auth_user_token . '',
        //         'Accept' => 'application/json',
        //     ],
        // ]);
        $response = $client->get("https://analyticsadmin.googleapis.com/v1alpha/accountSummaries", [
            'headers' => [
                'Authorization' => 'Bearer ' . $auth_user_token . '',
                'Accept' => 'application/json',
            ],
        ]);
        $data = json_decode($response->getBody(), true);

        return $data['accountSummaries'];
    }
}
