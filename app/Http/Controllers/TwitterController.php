<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\MediaPage;
use App\Models\SocialMediaDetail;
use App\Models\UserMedia;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;


class TwitterController extends Controller
{

    public function redirectToTwitter()
    {
        return Socialite::driver('twitter')->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleTwitterCallback()
    {
        DB::beginTransaction();
        try {
            $user = Socialite::driver('twitter')->user();
            $is_page_relink = false;
            $twitter = SocialMediaDetail::where('id', $user->id)->first();
            if (!$twitter) {
                $media = Media::where('name', 'X(Twitter)')->first();
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
                        'token_secret' => $user->tokenSecret ?? ""
                    ]);

                    $social_media_detail = SocialMediaDetail::where([
                        'user_id' => Auth::user()->id,
                        'media_id' => $media->id,
                        'social_id' => $user->id
                    ])->first();
                } else {

                    $social_media_detail = SocialMediaDetail::create([
                        'custom_id' => getUniqueString('social_media_details'),
                        'user_id' => Auth::user()->id,
                        'media_id' => $media->id ?? "",
                        'social_id' => $user->id ?? "",
                        'token' => $user->token ?? "",
                        'token_secret' => $user->tokenSecret ?? ""
                    ]);
                }

                if (!$social_media_detail) throw new Exception();

                $mediaPages = MediaPage::where([
                    'media_id' => $media->id,
                    'user_id' => Auth::user()->id,
                    'page_id' => $user->id
                ])->exists();

                if (!$mediaPages) {
                    $media_page = MediaPage::create([
                        'custom_id' => getUniqueString('media_pages'),
                        'media_id'  => $media->id ?? null,
                        'user_id'   => Auth::user()->id ?? "",
                        'page_id'   => $user->id ?? null,
                        'page_name' => isset($user->name) ? $user->name . '(Home)' :  'X(Twitter) - Home',
                        'is_old'    => 'n',
                        'social_media_detail_id' => $social_media_detail->id,
                        'image_url' => $user->avatar ?? null,
                    ]);
                } else {
                    $existMedia_page = MediaPage::where([
                        'media_id' => $media->id ?? null,
                        'user_id'  => Auth::user()->id ?? "",
                        'page_id'  => $user->id ?? null
                    ])->first();
                    if ($existMedia_page->page_id != $user->id) {
                        $media_page = MediaPage::where(
                            [
                                'media_id' => $media->id ?? null,
                                'user_id'  => Auth::user()->id ?? "",
                                'page_id'  => $user->id ?? null,
                                'is_old'   => 'n'
                            ]
                        )->update([
                            'page_name' => isset($user->name) ? $user->name . '(Home)' :  'X(Twitter) - Home',
                            'is_old'    => 'y',
                            'social_media_detail_id' => $social_media_detail->id,
                            'image_url' => $user->avatar ?? null,
                        ]);
                    } elseif ($existMedia_page->page_id == $user->id) {

                        $media_page = MediaPage::where(
                            [
                                'media_id' => $media->id ?? null,
                                'user_id'  => Auth::user()->id ?? "",
                                'page_id'  => $user->id ?? null,
                                'is_old'   => 'y'
                            ]
                        )->update([
                            'page_name' => isset($user->name) ? $user->name . '(Home)' :  'X(Twitter) - Home',
                            'is_old'    => 'n',
                            'social_media_detail_id' => $social_media_detail->id,
                            'image_url' => $user->avatar ?? null,
                        ]);
                    }
                }

                // $media_page = MediaPage::updateOrCreate(
                //     [
                //         'media_id'  => $media->id ?? null,
                //         'user_id'   => auth()->user()->id ?? "",
                //         'page_id'   => $user->id
                //     ],
                //     [
                //         'custom_id'             => getUniqueString('media_pages'),
                //         'page_name'             => 'Twitter - Home' ?? null,
                //     ]
                // );

                if (!$media_page) throw new Exception();

                // $user_media = UserMedia::create(
                //     [
                //         'custom_id'     => getUniqueString('user_media'),
                //         'user_id'       => auth()->user()->id ?? '',
                //         'media_id'      => $media->id  ?? '',
                //         'media_page_id' => $media_page->id
                //     ]
                // );
                // if (!$user_media) throw new Exception();
            }
            DB::commit();
            flash("X(Twitter) Link successfully!")->success();
            return redirect()->route('social-media-list', ['socialMedia' => 'X(Twitter)', 'is_page_is_relink' => $is_page_relink]);
        } catch (Exception $e) {
            DB::rollback();
            flash(trans('flash_message.social_media_error', ['entity' => 'X(Twitter)']))->error();
            return redirect()->route('social-media-list');
        }
    }
}
