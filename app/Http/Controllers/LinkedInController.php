<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Media;
use App\Models\MediaPage;
use App\Models\SocialMediaDetail;
use App\Models\UserMedia;
use Carbon\Carbon;

class LinkedInController extends Controller
{

    public function linkedinRedirect()
    {
        return Socialite::driver('linkedin')->scopes(['r_liteprofile', 'w_member_social', 'w_organization_social', 'r_basicprofile', 'rw_organization_admin', 'r_organization_social'])->redirect();
    }

    public function linkedinCallback()
    {
        try {
            $user = Socialite::driver('linkedin')->user();
            $linkedinUser = SocialMediaDetail::where('id', $user->id)->first();
            $is_page_relink = false;
            if (!$linkedinUser) {

                $media = Media::where('name', 'Linkedin')->first();

                $existingMediaPages = MediaPage::where(['media_id' => $media->id, 'user_id' => Auth::user()->id])->get();
                if ($existingMediaPages && $existingMediaPages->count() > 0) {
                    foreach ($existingMediaPages as $page) {
                        $page->update(['is_old' => 'y']);
                    }
                }
                // MediaPage::where(['media_id' => $media->id, 'user_id' => Auth::user()->id])->update(['is_old' => 'y']);
                $mediaDetails = SocialMediaDetail::where(['user_id' => Auth::user()->id, 'media_id' => $media->id, 'social_id' => $user->id])->exists();
                if ($mediaDetails) {
                    SocialMediaDetail::where(
                        [
                            'user_id' => Auth::user()->id,
                            'media_id' => $media->id ?? "",
                            'social_id' => $user->id ?? "",
                        ]
                    )->update([
                        'token'         => $user->token ?? "",
                        'token_expiry' => $user->expiresIn ? Carbon::parse(now())->addSeconds($user->expiresIn)->toDateString() : '',
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
                        'token_expiry' => $user->expiresIn ? Carbon::parse(now())->addSeconds($user->expiresIn)->toDateString() : '',
                    ]);
                }

                if (!$social_media_detail) throw new Exception();
                // MediaPage::where(['media_id' => $media->id, 'user_id' => auth()->user()->id])->delete();
                // $media_page = MediaPage::updateOrCreate(
                //     [
                //         'media_id'  => $media->id ?? null,
                //         'user_id'   => auth()->user()->id ?? "",
                //         'page_id'   => $user->id
                //     ],
                //     [
                //         'custom_id'             => getUniqueString('media_pages'),
                //         'page_name'             => 'Linkedin - Home' ?? null,
                //     ]
                // );

                //is_old update and set enum for page
                $mediaPages = MediaPage::where([
                    'media_id' => $media->id,
                    'user_id' => auth()->user()->id,
                    'page_id' => $user->id
                ])->exists();

                if (!$mediaPages) {
                    $media_page = MediaPage::create([
                        'custom_id' => getUniqueString('media_pages'),
                        'media_id'  => $media->id ?? null,
                        'user_id'   => Auth::user()->id ?? "",
                        'page_id'   => $user->id ?? null,
                        'page_name' =>  isset($user->name) ? $user->name . '(Home)' :  'Linkedin - Home',
                        'is_old'    => 'n',
                        'social_media_detail_id' => $social_media_detail->id
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
                            'page_name' => 'Linkedin - Home' ?? null,
                            'is_old'    => 'y',
                            'social_media_detail_id' => $social_media_detail->id
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
                            'page_name' => 'Linkedin - Home' ?? null,
                            'is_old'    => 'n',
                            'social_media_detail_id' => $social_media_detail->id
                        ]);
                    }
                }

                if (!$media_page) throw new Exception();
                // $user_media = UserMedia::updateOrCreate(
                //     [
                //         'user_id'       => auth()->user()->id ?? '',
                //         'media_id'      => $media->id  ?? '',
                //         'media_page_id' => $media_page->id
                //     ],
                //     [
                //         'custom_id'     => getUniqueString('user_media')
                //     ]
                // );
                // if (!$user_media) throw new Exception();
                $this->getOrganizationID($user->token, $media->id, $social_media_detail->id);
            }
            DB::commit();
            flash("Linkedin Link successfully!")->success();
            return redirect()->route('social-media-list', ['socialMedia' => 'Linkedin', 'is_page_is_relink' => $is_page_relink]);
        } catch (Exception $e) {
            // dd($e->getMessage());
            DB::rollback();
            flash(trans('flash_message.social_media_error', ['entity' => 'Linkedin']))->error();
        }
        return redirect()->route('social-media-list');
    }

    public function getOrganizationID($token, $mediaID, $social_media_detail_id)
    {
        $url = 'https://api.linkedin.com/v2/organizationAcls?q=roleAssignee';
        $type = 'GET';
        $headers = array(
            'Authorization: Bearer ' . $token . '',
            'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=11:x=1:i=1664445440:t=1664453856:v=2:sig=AQFjPaItq05VsgdPzJxozvfVeQYoLymL"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"'
        );

        $response = fireCURL($url, $type, $headers);
        $organizationID = [];

        foreach ($response['elements'] as $values) {
            $organizationID[] = $values['organization'];
        }

        if ($organizationID) $this->getOrganizationName($token, $organizationID, $mediaID, $social_media_detail_id);
        else return flash('Please try again for OrganizationID')->error();
    }

    public function getOrganizationName($token, $organizationID, $mediaID, $social_media_detail_id)
    {
        $orgID = [];
        foreach ($organizationID as $value) {
            $id = explode(":", $value);
            $orgID[] = $id[3];
        }
        foreach ($orgID as $ID) {
            // $url = 'https://api.linkedin.com/v2/organizations/' . $ID . '';
            $url = 'https://api.linkedin.com/v2/organizationsLookup?ids[0]=' . $ID . '&projection=(results*(localizedName,id,name,logoV2(original~:playableStreams)))';
            $type = 'GET';
            $headers = array(
                'Authorization: Bearer ' . $token . '',
                'Cookie: lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=11:x=1:i=1664447541:t=1664453856:v=2:sig=AQGQ3hz4lT0TAbWyTjtfyP3e5iiIvflF"; lidc="b=VB39:s=V:r=V:a=V:p=V:g=3491:u=11:x=1:i=1664447483:t=1664453856:v=2:sig=AQFXVJ6SdHMJVYqb6CrGTtx1Z7Zzl0LQ"; bcookie="v=2&85655da9-8de9-46d8-81df-59920214fb38"'
            );

            $response = fireCURL($url, $type, $headers);
            $media_page = MediaPage::updateOrCreate(
                [
                    'media_id'   => $mediaID ?? null,
                    'user_id'    => auth()->user()->id ?? "",
                    'page_id'    => $ID
                ],
                [
                    'custom_id'  => getUniqueString('media_pages'),
                    // 'page_name'  => $response['localizedName'] ?? null,
                    'page_name'  => isset($response['results'][$ID]['localizedName']) ? $response['results'][$ID]['localizedName'] : 'Linkedin',
                    'page_id'    => $ID,
                    'is_old'   => 'n',
                    'social_media_detail_id'   => $social_media_detail_id,
                    'image_url' => isset($response['results'][$ID]['logoV2']) ? $response['results'][$ID]['logoV2']['original~']['elements'][1]['identifiers'][0]['identifier'] : '',
                ]
            );
            if (!$media_page) throw new Exception();

            // $user_media = UserMedia::updateOrCreate(
            //     [
            //         'user_id'       => auth()->user()->id ?? '',
            //         'media_id'      => $mediaID ?? '',
            //         'media_page_id' => $media_page->id
            //     ],
            //     [
            //         'custom_id'     => getUniqueString('user_media'),
            //     ]
            // );
            // if (!$user_media) throw new Exception();
        }
        return true;
    }
}
