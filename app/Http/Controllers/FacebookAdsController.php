<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\MediaPage;
use App\Models\SocialMediaDetail;
use Carbon\Carbon;
use Exception;
use Facebook\Facebook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FacebookAdsController extends Controller
{

    protected $fb;
    protected $pages = [];
    public function __construct(Facebook $facebook)
    {
        $this->fb = $facebook;
    }

    public function redirectToFacebookAds()
    {
        return Socialite::driver('facebook')->with(['redirect_uri' => url('auth/facebook_ads/callback')])->scopes(["email", "business_management", "ads_read", "ads_management"])->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleFaceBookAdsCallback()
    {
        DB::beginTransaction();
        try {
            $user = Socialite::driver('facebook')->redirectUrl(url('auth/facebook_ads/callback'))->user();
            $facebook = SocialMediaDetail::where('id', $user->id)->first();
            $is_page_relink = false;
            if (!$facebook) {
                $media = Media::where('name', 'Facebook Ads')->first();

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
                        'token_expiry' => Carbon::parse(now())->addDays(60)->toDateString(),
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
                        'token_expiry' => Carbon::parse(now())->addDays(60)->toDateString(),
                    ]);
                }
                if (!$social_media_detail) throw new Exception();

                $accounts = $this->getFacebookAdsAccount($user);

                if ($accounts) {
                    foreach ($accounts as $account) {
                        $mediaPages = MediaPage::where(['media_id' => $media->id, 'user_id' => auth()->user()->id, 'page_id' => $account['id'], 'page_name' => $account['name']])->first();
                        // $profilePictureRequest = $this->fb->get('/' . $account['id'] . '/picture?redirect=0&type=normal', $user->token);
                        // $profilePictureRespone = $profilePictureRequest->getGraphNode();

                        // if (!$mediaPages) {
                        //     // put image in folder from url
                        //     $file_name = $profilePictureRespone['url'];
                        //     $parseUrl = parse_url($file_name);
                        //     $ext = pathinfo($parseUrl['path'], PATHINFO_EXTENSION);
                        //     $fileName = rand(111111, 999999) . ".$ext";
                        //     Storage::putFileAs('social_images', $file_name, $fileName);
                        //     $imageContent = Storage::url('social_images/' . $fileName . '');
                        // } else {
                        //     $imageContent = $mediaPages->image_url;
                        // }

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
                DB::commit();
            }
            flash("Facebook Ads Link successfully!")->success();
            return redirect()->route('social-media-list', ['socialMedia' => 'Facebook Ads']);
        } catch (Exception $e) {
            DB::rollBack();
            flash(trans('flash_message.social_media_error', ['entity' => 'Facebook Ads']))->error();
            return redirect()->route('social-media-list');
        }
    }

    public function getFacebookAdsAccount($user, $url = null): array
    {
        $params = 'name';
        $url = $url  ?? "https://graph.facebook.com/" . config('utility.DEFAULT_FACEBOOK_API_VERSION') . "/me/adaccounts?fields=id,name&access_token={$user->token}";
        // $response = $this->fb->get('/' . $user->id . '/accounts?fields=' . $params, $user->token, null, config('utility.DEFAULT_FACEBOOK_API_VERSION'));
        $response = Http::get($url);
        if ($response->failed()) $response->throw();
        $data = $response->json();

        $this->pages = isset($data['data']) ? array_merge($this->pages, array_values($data['data'])) : [];

        if (isset($data['paging'], $data['paging']['next'])) {
            $this->getFacebookAdsAccount($user, $data['paging']['next']);
        }
        return $this->pages;
        // $ads_account_request = $this->fb->get('/me/adaccounts?fields=name', $user->token, null, config('utility.DEFAULT_FACEBOOK_API_VERSION'));
        // $statuscode = $ads_account_request->getHttpStatusCode();
        // $response = ($statuscode == 200) ?  $ads_account_request->getGraphEdge()->asArray()  : [];
        // return $response ?? [];
    }
}
