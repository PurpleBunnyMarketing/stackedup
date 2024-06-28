<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\MediaPage;
use App\Models\SocialMediaDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InstagramController extends Controller
{

    protected $fb;
    protected $pages = [];
    public function __construct(Facebook $facebook)
    {
        $this->fb = $facebook;
    }

    public function redirectToInstagram()
    {
        return Socialite::driver('facebook')
            ->with(['redirect_uri' => url('auth/instagram/callback')])
            ->scopes(['email', 'instagram_basic', 'pages_show_list', 'instagram_content_publish', 'pages_read_engagement', 'instagram_manage_insights','business_management'])->redirect();
    }

    public function handleInstagramCallback()
    {
        try {
            $user = Socialite::driver('facebook')->redirectUrl(url('auth/instagram/callback'))->user();

            $instagram = SocialMediaDetail::where('id', $user->id)->first();
            $is_page_relink = false;

            if (!$instagram) {
                $media = Media::where('name', 'Instagram')->first();
                $existingMediaPages = MediaPage::where(['media_id' => $media->id, 'user_id' => Auth::user()->id])->get();
                if ($existingMediaPages && $existingMediaPages->count() > 0) {
                    foreach ($existingMediaPages as $page) {
                        $page->update(['is_old' => 'y']);
                    }
                }
                // MediaPage::where(['media_id' => $media->id, 'user_id' => Auth::user()->id])->update(['is_old' => 'y']);
                // $authUserId = auth()->id();
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

                // $response = $this->fb->get('/me/accounts', $user->token);
                // $pages = $response->getGraphEdge()->asArray();

                $pages = $this->getFacebookPages($user);
                if ($pages && count($pages) > 0 && isset($media->id)) {
                    foreach ($pages as $page) {
                        $instareq = $this->fb->get('/' . $page['id'] . '?fields=instagram_business_account', $page['access_token']);
                        $instares = $instareq->getGraphNode()->asArray();



                        if (isset($instares['instagram_business_account']['id'])) {
                            $profilePictureRequest = $this->fb->get('/' . $page['id'] . '/picture?redirect=0&type=normal', $user->token);
                            $profilePictureRespone = $profilePictureRequest->getGraphNode();

                            //Check if page sync or not.
                            $media_page = MediaPage::where([
                                'user_id' => auth()->user()->id,
                                'page_id' => $instares['instagram_business_account']['id'],
                            ])->first();

                            // if (!$media_page) {
                            // put image in folder from url
                            $file_name = $profilePictureRespone['url'];
                            $parseUrl = parse_url($file_name);
                            $extension = pathinfo($parseUrl['path'], PATHINFO_EXTENSION);
                            $fileName = implode('.', [\Illuminate\Support\Str::random(20), $extension]);
                            Storage::putFileAs('social_images', $file_name, $fileName);
                            $imageContent = Storage::url('social_images/' . $fileName . '');
                            // } else {
                            //     $imageContent = $media_page->image_url;
                            // }


                            $insta_id = $instares['instagram_business_account']['id'];
                            $instaprofreq = $this->fb->get('/' . $insta_id . '?fields=username', $page['access_token']);
                            $instaprofres = $instaprofreq->getGraphNode()->asArray();



                            if (isset($media_page->id)) {
                                $existMedia_page = MediaPage::where([
                                    'media_id' => $media->id ?? null,
                                    'user_id'  => auth()->user()->id ?? "",
                                    'page_id'  => $instares['instagram_business_account']['id'] ?? null
                                ])->first();

                                if ($existMedia_page->page_id != $instares['instagram_business_account']['id']) {
                                    $media_page = MediaPage::where(
                                        [
                                            'media_id' => $media->id ?? null,
                                            'user_id'  => Auth::user()->id ?? "",
                                            'page_id'  => $instares['instagram_business_account']['id'] ?? null,
                                            'is_old'   => 'n'
                                        ]
                                    )->update([
                                        'page_name' => $instaprofres['username'] ?? 'Instagram - Home',
                                        'is_old'    => 'y',
                                        'image_url' => $imageContent,
                                    ]);
                                } elseif ($existMedia_page->page_id == $instares['instagram_business_account']['id']) {
                                    $media_page = MediaPage::where(
                                        [
                                            'media_id' => $media->id ?? null,
                                            'user_id'  => Auth::user()->id ?? "",
                                            'page_id'  => $instares['instagram_business_account']['id'] ?? null,
                                            'is_old'   => 'y'
                                        ]
                                    )->update([
                                        'page_name' => $instaprofres['username'] ?? 'Instagram - Home',
                                        'is_old'    => 'n',
                                        'social_media_detail_id' => $social_media_detail->id,
                                        'image_url' => $imageContent,
                                    ]);
                                }
                                // MediaPage::where('id',$media_page->id)->update([
                                //     'page_name' => $instaprofres['username'] ?? 'Instagram - Home'
                                // ]);
                            } else {

                                MediaPage::create([
                                    'custom_id' => getUniqueString('media_pages'),
                                    'media_id'  => $media->id ?? null,
                                    'user_id'   => auth()->user()->id,
                                    'page_id'   => $instares['instagram_business_account']['id'],
                                    'page_name' => $instaprofres['username'] ?? 'Instagram - Home',
                                    'is_old'    => 'n',
                                    'social_media_detail_id' => $social_media_detail->id,
                                    'image_url' => $imageContent,
                                ]);
                            }
                        }
                    }
                    flash("Instagram Link successfully.")->success();
                } else {
                    flash("No instagram business profile found.")->success();
                }

                DB::commit();
            }
            return redirect()->route('social-media-list', ['socialMedia' => 'Instagram', 'is_page_is_relink' => $is_page_relink]);
        } catch (Exception $e) {
            DB::rollBack();
            flash(trans('flash_message.social_media_error', ['entity' => 'Instagram']))->error();
            return redirect()->route('social-media-list');
        }
    }
    public function getFacebookPages($user, $url = null)
    {
        $params = 'name,access_token';
        $url = $url  ?? "https://graph.facebook.com/" . config('utility.DEFAULT_FACEBOOK_API_VERSION') . "/{$user->id}/accounts?access_token={$user->token}";
        // $response = $this->fb->get('/' . $user->id . '/accounts?fields=' . $params, $user->token, null, config('utility.DEFAULT_FACEBOOK_API_VERSION'));
        $response = Http::get($url);
        if ($response->failed()) $response->throw();
        $data = $response->json();

        $this->pages = isset($data['data']) ? array_merge($this->pages, array_values($data['data'])) : [];

        if (isset($data['paging'], $data['paging']['next'])) {
            $this->getFacebookPages($user, $data['paging']['next']);
        }
        return $this->pages;
    }
}
