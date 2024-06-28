<?php

namespace App\Http\Controllers;

use App\Admin;
use App\Models\CmsPage;
use App\Models\MediaPage;
use App\Models\SocialMediaDetail;
use App\Models\Subscriber;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UtilityController extends Controller
{
    public function updateProfilePicture(Request $request)
    {
        $user = Auth::user();

        if ($user->profile) {
            Storage::delete($user->profile);
        }
        $path = $request->file('profile_pic')->store('profiles');
        $user->profile = $path;

        if ($user->save()) {
            flash('Profile picture updated successfully!')->success();
        } else {
            flash('Unable to update profile picture. Try again later')->error();
        }

        return redirect()->back();
    }

    public static function generateUrl($path)
    {
        $url = "";
        if (!empty($path) && Storage::exists($path))
            $url = Storage::url($path);
        return $url;
    }

    // Common For All
    public function checkOldPassword(Request $request)
    {
        if (Hash::check($request->old_password, Auth::user()->password)) {
            return 'true';
        } else {
            return 'false';
        }
    }

    // For Unique email check of user & admin
    public function checkEmail(Request $request)
    {

        $id = $request->id ?? 0;
        if ($request->type == 'user') {
            $user = User::query();
        } elseif ($request->type == 'admin') {
            $user = Admin::query();
        }
        $user =    $user->where([
            ['id', '!=', $id],
            'email' => $request->email,
        ])->count();

        if ($user == 0) {
            return "true";
        } else {
            return "false";
        }
    } // For Unique email check of user & admin

    public function checkSubscriber(Request $request)
    {
        if (!Subscriber::where('email', $request->email)->exists()) {
            return "true";
        } else {
            return "false";
        }
    }

    public function checkMobileNo(Request $request)
    {
        $id = $request->id ?? 0;
        if ($request->type == 'user') {
            $user = User::query();
        } elseif ($request->type == 'admin') {
            $user = Admin::query();
        }
        $user = $user->where([
            ['id', '!=', $id]

        ]);
        $user = $user->whereRaw('concat(users.phone_code , "" , users.mobile_no) = ?', [$request->phone_code . '' . $request->mobile_no]);
        $user = $user->count();
        if ($user == 0) {
            return "true";
        } else {
            return "false";
        }
    }
    //exist mobile number
    public function checkContact(Request $request)
    {
        $id = $request->id ?? 0;
        if ($request->type == 'user') {
            $user = User::where([
                // ['id', '<>', $id],
                'phone_code' => $request->phone_code,
                'mobile_no' => $request->mobile_no,
            ])->count();
        } elseif ($request->type == 'admin') {
            $user = Admin::where([
                'contact_no' => $request->contact_no,
            ])->count();
        }
        if ($user == 0) {
            return "false";
        } else {
            return "true";
        }
    }

    public function noScript()
    {
        return view('errors.no-script');
    }

    public function noCookie()
    {
        return view('errors.no-cookie');
    }

    public function home()
    {
        return view('welcome');
    }

    public function adminHome()
    {
        $users = [];
        $users[] = Auth::user();
        $users[] = Auth::guard()->user();
        $users[] = Auth::guard('admin')->user();

        return redirect(route('admin.dashboard.index'));
    }

    // For Admin Panel Usage
    public function checkTitle(Request $request)
    {
        $id = $request->id ?? 0;
        if ($request->type == 'cms') {
            $data = CmsPage::query();
        }

        $data = $data->where([
            ['id', '<>', $id],
            'title' => $request->title,
        ])->count();


        if ($data == 0) {
            return "true";
        } else {
            return "false";
        }
    }

    public function profileCheckPassword(Request $request)
    {
        if (Hash::check($request->current_password, Auth::user()->password)) {
            return "true";
        } else {
            return "false";
        }
    }
    // For Unique email check of user & admin
    public function checkEmailExist(Request $request)
    {

        $id = $request->id ?? 0;
        if ($request->type == 'user') {
            $user = User::query();
        } elseif ($request->type == 'admin') {
            $user = Admin::query();
        }
        $user =    $user->where([
            ['id', '!=', $id],
            'email' => $request->email,
        ])->count();
        if ($user == 0) {
            return "false";
        } else {
            return "true";
        }
    }
    public function checkAbnExists(Request $request)
    {
        $existsAbnRecord = User::where('abn', $request->abn)->first();
        if ($existsAbnRecord) {
            return "false";
        }
        return "true";
    }

    public function checkMediaTokenExpiry(Request $request)
    {
        $userId = auth()->user()->parent_id ?? auth()->id();
        $media_page = MediaPage::where('id', $request->media_page_id)->with('media')->first();
        $content = array('status' => false, 'message' => 'Not expired');
        if ($media_page->media->name != 'X(Twitter)') {
            // $mediaTokenExpiryDate = SocialMediaDetail::where(['user_id' => $userId, 'media_id' => $media->id])->first();
            $mediaTokenExpiryDate = SocialMediaDetail::where('id', $media_page->social_media_detail_id)->first();
            // $is_expire = $mediaTokenExpiryDate->token_expiry ? Carbon::parse($mediaTokenExpiryDate->token_expiry)->diffInDays(now()->toDateString()) : '';
            if ($mediaTokenExpiryDate->token_expiry < Carbon::parse(now())->toDateString())
                $content =  array('status' => true, 'message' => "Your {$media_page->media->name} Token is expired, Please Relink your {$media_page->media->name} Account.");
        }
        return response()->json($content);
    }

    public function checkInstagramMediaPage(Request $request): bool
    {
        $media_page_ids = $request->media_page_ids ?? '';
        return MediaPage::whereIn('id', $media_page_ids)->where('media_id', 4)->exists();
    }
}
