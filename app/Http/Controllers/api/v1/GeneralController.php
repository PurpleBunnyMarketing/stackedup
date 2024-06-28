<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\CMSListResource;
use App\Http\Resources\v1\FAQsListResource;
use App\Http\Resources\v1\MediaListResource;
use App\Http\Resources\v1\PackageListResource;
use App\Http\Resources\v1\UserProfile;
use App\Mail\ContactUsMail;
use App\Models\CmsPage;
use App\Models\ContactUs;
use App\Models\Faqs;
use App\Models\Media;
use App\Models\Package;
use App\Models\Setting;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class GeneralController extends Controller
{
    private $version = "v.1.0";
    public function getVersion()
    {
        return $this->version;
    }
    public function contactUs(Request $request)
    {
        $rules = [
            'full_name'         =>      'required|max:150',
            'email_address'     =>      'required|email|max:150',
            'message'           =>      'required',
        ];
        if ($this->apiValidator($request->all(), $rules)) {
            try {
                $request['custom_id'] = getUniqueString('contact_us');
                $contact = ContactUs::create($request->all());
                $setting = Setting::where('constant', 'support_email')->first();

                Mail::to($setting->value)->send(new ContactUsMail($contact));
                $this->status = $this->statusArr['success'];
                $this->response['meta']['api'] = $this->version;
                $this->response['meta']['url'] = url()->current();
                $this->response['meta']['message']  =  trans('api.add', ['entity' => 'Contact Us']);
            } catch (\Exception $ex) {
                $this->status = $this->statusArr['something_wrong'];
                $this->response['meta']['message'] = trans('api.error');
            }
        }
        return $this->return_response();
    }
    /* FAQ*/
    public function faqs(Request $request)
    {
        try {
            $faqs = Faqs::get();
            return (new FAQsListResource($faqs))
                ->additional([
                    'meta' => [
                        'message' => trans('api.list', ['entity' => 'FAQs List']),
                        'count'    => $faqs->count() ?? "",
                    ]
                ]);
        } catch (\Exception $e) {
            $this->status = $this->statusArr['something_wrong'];
            $this->response['meta']['message'] = trans('api.error');
        }
        $this->response['meta']['api'] = $this->version;
        $this->response['meta']['url'] = url()->current();
        return $this->return_response();
    }
    public function mediaList(Request $request)
    {
        try {
            $user = $request->user();
            $medias = Media::whereHas('mediaPages', function ($q) use ($user) {
                $q->whereHas('userMediaPages', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                    $q->where('is_deleted', 'n');
                });
            })->with(['mediaPages' => function ($q) use ($user) {
                $q->whereHas('userMediaPages', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                    $q->where('is_deleted', 'n');
                });
            }])->get();
            // dd($medias);
            return (new MediaListResource($medias))
                ->additional([
                    'meta' => [
                        'message' => trans('api.list', ['entity' => 'Media List']),
                        'count'    => $medias->count() ?? "",
                    ]
                ]);
        } catch (\Exception $ex) {
            $this->status = $this->statusArr['something_wrong'];
            $this->response['meta']['message'] = trans('api.error');
        }
        $this->response['meta']['api'] = $this->version;
        $this->response['meta']['url'] = url()->current();
        return $this->return_response();
    }

    public function packageList(Request $request)
    {
        try {
            $packages = Package::get();

            return (new PackageListResource($packages))
                ->additional([
                    'meta' => [
                        'message' => trans('api.list', ['entity' => 'Package List']),
                        'count'    => $packages->count() ?? "",
                    ]
                ]);
        } catch (\Exception $ex) {

            $this->status = $this->statusArr['something_wrong'];
            $this->response['meta']['message'] = trans('api.error');
        }
        $this->response['meta']['api'] = $this->version;
        $this->response['meta']['url'] = url()->current();
        return $this->return_response();
    }
    public function cmsPage(Request $request)
    {
        try {
            $pages = CmsPage::get();

            return (new CMSListResource($pages))
                ->additional([
                    'meta' => [
                        'message' => trans('api.list', ['entity' => 'CMS List']),
                        'count'    => $pages->count() ?? "",
                    ]
                ]);
        } catch (\Exception $ex) {

            $this->status = $this->statusArr['something_wrong'];
            $this->response['meta']['message'] = trans('api.error');
        }
        $this->response['meta']['api'] = $this->version;
        $this->response['meta']['url'] = url()->current();
        return $this->return_response();
    }

    public function contactUsDetail()
    {
        // try {
        $settings = Setting::pluck('value', 'constant')->toArray();
        if ($settings) {
            $this->status = $this->statusArr['success'];
            $this->response['data']['support_email'] = $settings['support_email'];
            // $supportContact = $settings['support_contact'];
            $this->response['data']['support_contact'] = $settings['support_contact'];
            $this->response['data']['address'] = $settings['address'];
            $this->response['meta']['api'] = $this->version;
            $this->response['meta']['url'] = url()->current();
            $this->response['meta']['message']  =  trans('ContactUS Detail');
            return response()->json($this->response, $this->status);
        } else {
            $this->status = $this->statusArr['something_wrong'];
            $this->response['meta']['message'] = trans('api.error');
        }
        // }
        // catch (\Exception $ex) {
        //     $this->status = $this->statusArr['something_wrong'];
        //     $this->response['meta']['message'] = trans('api.error');
        // }

        $this->response['meta']['api'] = $this->version;
        $this->response['meta']['url'] = url()->current();
        return $this->return_response();
    }
}
