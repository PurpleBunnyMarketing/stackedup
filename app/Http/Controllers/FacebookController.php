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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class FacebookController extends Controller
{

    protected $fb;
    protected $pages = [];
    public function __construct(Facebook $facebook)
    {
        $this->fb = $facebook;
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->scopes(["email", "pages_manage_posts", "pages_show_list", "public_profile", "read_insights", "pages_read_engagement", 'ads_read','business_management'])->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleFacebookCallback()
    {
        DB::beginTransaction();
        try {
            $user = Socialite::driver('facebook')->user();
            $facebook = SocialMediaDetail::where('id', $user->id)->first();
            $is_page_relink = false;
            if (!$facebook) {
                $media = Media::where('name', 'Facebook')->first();

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

                // $params = 'name,access_token';
                // $response = $this->fb->get('/' . $user->id . '/accounts?fields=' . $params, $user->token, null, config('utility.DEFAULT_FACEBOOK_API_VERSION'));
                // $statuscode = $response->getHttpStatusCode();
                // $pages = ($statuscode == 200) ?  $response->getGraphEdge()->asArray()  : [];
                $pages = $this->getFacebookPages($user);
                if ($pages) {
                    foreach ($pages as $page) {
                        $mediaPages = MediaPage::where(['media_id' => $media->id, 'user_id' => auth()->user()->id, 'page_id' => $page['id'], 'page_name' => $page['name']])->first();
                        $profilePictureRequest = $this->fb->get('/' . $page['id'] . '/picture?redirect=0&type=normal', $user->token, null, config('utility.DEFAULT_FACEBOOK_API_VERSION'));
                        $profilePictureRespone = $profilePictureRequest->getGraphNode();

                        // if (!$mediaPages) {
                        // put image in folder from url
                        $file_name = $profilePictureRespone['url'];
                        $parseUrl = parse_url($file_name);
                        $ext = pathinfo($parseUrl['path'], PATHINFO_EXTENSION);
                        $fileName = rand(111111, 999999) . ".$ext";
                        Storage::putFileAs('social_images', $file_name, $fileName);
                        $imageContent = Storage::url('social_images/' . $fileName . '');
                        // } else {
                        //     $imageContent = $mediaPages->image_url;
                        // }

                        if (!$mediaPages) {
                            $media_page = MediaPage::create([
                                'custom_id' => getUniqueString('media_pages'),
                                'media_id'  => $media->id ?? null,
                                'user_id'   => Auth::user()->id ?? "",
                                'page_id'   => $page['id'] ?? null,
                                'page_name' => $page['name'] ?? null,
                                'is_old'    => 'n',
                                'social_media_detail_id' => $social_media_detail->id,
                                'image_url' => $imageContent ?? null
                            ]);
                        } else {
                            $existMedia_page = MediaPage::where([
                                'media_id' => $media->id ?? null,
                                'user_id'  => Auth::user()->id ?? "",
                                'page_id'  => $page['id'] ?? null
                            ])->first();
                            if ($existMedia_page->page_id != $page['id']) {
                                $media_page = MediaPage::where(
                                    [
                                        'media_id' => $media->id ?? null,
                                        'user_id'  => Auth::user()->id ?? "",
                                        'page_id'  => $page['id'] ?? null,
                                        'is_old'   => 'n'
                                    ]
                                )->update([
                                    'page_name' => $page['name'] ?? null,
                                    'is_old'    => 'y',
                                    'social_media_detail_id' => $social_media_detail->id,
                                    'image_url' => $imageContent ?? null
                                ]);
                            } elseif ($existMedia_page->page_id == $page['id']) {

                                $media_page = MediaPage::where(
                                    [
                                        'media_id' => $media->id ?? null,
                                        'user_id'  => Auth::user()->id ?? "",
                                        'page_id'  => $page['id'] ?? null,
                                        'is_old'   => 'y'
                                    ]
                                )->update([
                                    'page_name' => $page['name'] ?? null,
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
            flash("Facebook Link successfully!")->success();
            return redirect()->route('social-media-list', ['socialMedia' => 'Facebook', 'is_page_is_relink' => $is_page_relink]);
        } catch (Exception $e) {
            // dd($e->getMessage());
            DB::rollBack();
            flash(trans('flash_message.social_media_error', ['entity' => 'Facebook']))->error();
            return redirect()->route('social-media-list');
        }
    }

    public function getFacebookPages($user, $url = null)
    {
        $params = 'name,access_token';
        $url = $url  ?? "https://graph.facebook.com/" . config('utility.DEFAULT_FACEBOOK_API_VERSION') . "/{$user->id}/accounts?fields={$params}&access_token={$user->token}";
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
