<?php

namespace App\Http\Controllers;

use App\Mail\StaffAddMail;
use App\Models\Country;
use App\Models\Media;
use App\Models\MediaPage;
use App\Models\Post;
use App\Models\StaffTempImage;
use App\Models\UserMedia;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class StaffController extends Controller
{
    public function __construct()
    {

        $this->stripe = new \Stripe\StripeClient(
            config('utility.STRIPE_SECRET')
        );
    }

    public function index(Request $request)
    {
        return view('frontend.pages.staff.list')->withTitle('Manage Staff');
    }

    public function create()
    {
        $temps = StaffTempImage::where('user_id', Auth::user()->id)->get();

        foreach ($temps as $temp) {
            if ($temp->profile_photo) {
                if (Storage::exists($temp->profile_photo)) {
                    Storage::delete($temp->profile_photo);
                }
            }
            $temp->delete();
        }
        $countries = Country::pluck('phonecode')->toArray();
        $user = Auth::user();
        // old yatri replace with below query 14-12-22
        // $media = Media::whereHas('socialMediaDetails', function ($q) use ($user) {
        //     $q->where('user_id', $user->id);
        // })->with('mediaPages', function ($q) use ($user) {
        //     $q->whereHas('userMediaPages', function ($q) use ($user) {
        //         $q->where('user_id', $user->id);
        //         $q->where('is_deleted', 'n');
        //     });
        // })->get();
        // new query
        $media = Media::whereHas('mediaPages', function ($q) use ($user) {
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

        return view('frontend.pages.staff.create', compact('countries', 'media'))->withTitle('Create Staff');
    }
    public function store(Request $request)
    {
        Db::beginTransaction();
        try {
            $user = Auth::user();
            // $profile_photo = '';
            // if ($request->has('images')) {
            //     $profile_photo = $request->file('images')->store('profile_photo');
            // }
            $parentID = ($request['type'] == 'company') ? Null : $user->id;
            $checkFristStaff = User::whereparent_id($user->id)->count();
            $type = ($checkFristStaff > 0) ? $request['type'] : 'admin';
            $user =  User::create([
                'custom_id'     => getUniqueString('users'),
                'parent_id'     => $parentID,
                'full_name'     => $request['full_name'] ?? "",
                'email'         => $request['email'] ?? "",
                'mobile_no'     => $request['mobile_no'] ?? "",
                'phone_code'    => $request['phone_code'] ?? "",
                'profile_photo' => $request->images ?? "",
                'password'      => Hash::make($request['password']) ?? "",
                'type'          => $type,
                'is_active'     => 'y'
            ]);

            if (!empty($request->media_page_id)) {
                $media_page_ids = $request->media_page_id;

                // $media_page_ids = explode(',',$request->media_page_id);
                if (!empty($media_page_ids)) {
                    foreach ($media_page_ids as $media_page) {
                        $media_id = MediaPage::where('id', $media_page)->first();
                        $user_media = UserMedia::create(['custom_id' => getUniqueString('user_media'), 'user_id' => $user->id, 'media_id' => $media_id->media_id, 'media_page_id' => $media_page]);
                        if (!$user_media) throw new Exception();
                    }
                }
            }
            $temps = StaffTempImage::where('profile_photo', $request->images)->first();
            if ($temps) $temps->delete();


            if ($user->customer_id == null) {
                $customer = $this->stripe->customers->create(
                    [
                        'email'     =>      $user->email ?? "",
                        'metadata'  =>  [
                            'Full name' =>  $user->full_name ?? "",
                        ],
                    ]
                );
                $user->customer_id = $customer->id;
                $user->save();
            }

            $data =  [
                'email' => $request->email ?? '',
                'password' => $request->password ?? '',
                'full_name' => $user->full_name ?? '',
                'company' => $user->company->full_name ?? '',
            ];
            // Send mail to New Staff member
            Mail::to($user->email)->send(new StaffAddMail($data));
            flash('Staff created successfully!')->success();
            DB::commit();
            return redirect(route('staff.index'));
        } catch (Exception $th) {
            DB::rollBack();
            flash($th->getMessage(), $th->getLine());
            return redirect()->back();
        }
    }
    public function edit($id)
    {
        $temps = StaffTempImage::where('user_id', Auth::user()->id)->get();

        foreach ($temps as $temp) {
            if ($temp->profile_photo) {
                if (Storage::exists($temp->profile_photo)) {
                    Storage::delete($temp->profile_photo);
                }
            }

            $temp->delete();
        }
        $countries = Country::pluck('phonecode')->toArray();
        $staff = User::where('id', $id)->with('media')->first();
        $user = User::where('id', Auth::user()->id)->first();
        // $media = Media::whereHas('user_media', function ($q) use ($user) {
        //     $q->where('user_id', $user->id);
        // })->with(['mediaPages' => function ($q) use ($user) {
        //     $q->whereHas('userMediaPages', function ($q) use ($user) {
        //         $q->where('user_id', $user->id);
        //     });
        // }])->get();
        $media = Media::whereHas('socialMediaDetails', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with('mediaPages', function ($q) use ($user) {
            $q->whereHas('userMediaPages', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        })->get();
        return view('frontend.pages.staff.edit', compact('staff', 'media', 'countries', 'user'))->withTitle('Edit Staff');
    }
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            //code...
            // dd($request->all());
            if (!empty($request->images)) {
                if (Storage::exists($request->remove_image)) {
                    Storage::delete($request->remove_image);
                }
                $request['profile_photo'] = $request->images ?? "";
                StaffTempImage::where('profile_photo', $request->images)->delete();
            }
            $user = User::where('id', $id)->first();
            $user = $user->fill($request->all());
            $user->save();

            // if(!empty($request->media_page_id)){
            //     $media_page_ids = $request->media_page_id;
            //     // $media_page_ids = explode(',',$request->media_page_id);
            //     if(!empty($media_page_ids)){
            //         foreach($media_page_ids as $media_page){
            //            $media_id = MediaPage::where('id',$media_page)->first();
            //            $user_media = UserMedia::create(['custom_id'=> getUniqueString('user_media'),'user_id'=>$user->id,'media_id' => $media_id->media_id,'media_page_id' => $media_page ]);
            //         }
            //     }
            // }
            if (!empty($request->media_page_id)) {
                $media_page_ids = $request->media_page_id;

                if (!empty($media_page_ids)) {
                    foreach ($media_page_ids as $media_page) {
                        $media_page_id = MediaPage::where('id', $media_page)->first();
                        $post_media = UserMedia::updateOrCreate(
                            [
                                'user_id'   => $user->id ?? "",
                                'media_page_id' => $media_page_id->id ?? '',
                                'media_id'      => $media_page_id->media_id,
                            ],
                            [
                                'custom_id' => getUniqueString('user_media'),
                                'user_id'   => $user->id ?? "",
                                'media_page_id' => $media_page_id->id ?? '',
                                'media_id'      => $media_page_id->media_id,
                            ]
                        );
                        $not_delete_post_media[] = $post_media->id;
                    }
                    UserMedia::where('user_id', $user->id)->whereNotIn('id', $not_delete_post_media)->delete();
                }
            }
            DB::commit();
            flash('Staff updated successfully!')->success();
            return redirect(route('staff.index'));
        } catch (\Throwable $th) {
            DB::rollBack();
            flash('Please Try Again')->error();
            return redirect()->back();
        }
    }
    public function show($id)
    {
        $staff = User::where('id', $id)->first();
        $data = Media::whereHas('user_media', function ($q) use ($staff) {
            $q->where('user_id', $staff->id);
        })->with(['mediaPages' => function ($q) use ($staff) {
            $q->whereHas('userMediaPages', function ($q) use ($staff) {
                $q->where('user_id', $staff->id);
            });
        }])->get();
        return view('frontend.pages.staff.show', compact('staff', 'data'))->withTitle('View Staff');
    }

    public function paginateStaff(Request $request)
    {

        $id = Auth::user()->id ?? "";
        // $staff = User::where('parent_id', $id)->where('is_active', 'y');
        $staff = User::where('parent_id', $id);
        $staff = $staff->paginate('9');
        $post['data'] = view('frontend.pages.staff.data', compact('staff'))->render();
        $post['pages'] = view('frontend.pages.staff.page', compact('staff'))->render();
        return response()->json($post);
    }

    public function destroy(Request $request)
    {
        $id = $request->id ?? "";
        $staff = User::where('id', $id)->first();
        if ($staff->profile_photo) {
            if (Storage::exists($staff->profile_photo)) {
                Storage::delete($staff->profile_photo);
            }
        }
        $staff->delete();
        $content = array('status' => 200, 'message' => trans('flash_message.delete', ['entity' => 'Staff']));
        return response()->json($content);
    }

    public function storeimage(Request $request)
    {
        $user = Auth::user();
        try {
            if ($request->file('file')) {
                $path = $request->file('file')->store('profile_photo');
                $data[] = array('profile_photo' => $path, 'user_id' => $user->id ?? "", 'custom_id' => getUniqueString('staff_temp_images'));
                $strore = StaffTempImage::insert($data);


                if ($path) {
                    return response()->json(['path' => $path], 200);
                } else {
                    return response()->json('error', 400);
                }
            } else {
                return response()->json('error', 400);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    public function deleteimage(Request $request)
    {

        $content = ['status' => 204, 'message' => "something went wrong"];

        if (!empty($request->path)) {
            $data = StaffTempImage::where('profile_photo', $request->path)->first();
            if (!empty($data)) {
                if (Storage::exists($data->profile_photo)) {
                    Storage::delete($data->profile_photo);
                }
                StaffTempImage::where('profile_photo', $request->path)->delete();

                $content['path'] = $data->profile_photo;
            }
        }


        $content['status'] = 200;

        return response()->json($content);
    }

    public function changeStatus(Request $request)
    {
        $content = ['status' => 502, 'message' => "something went wrong"];

        DB::beginTransaction();
        try {
            $user_id = $request->user_id;
            $status_value = $request->status_value;
            $user_status_changed = User::find($user_id)->update(['is_active' => $status_value]);

            if (!$user_status_changed) throw new Exception();

            $content = array('status' => 200, 'message' => 'Status changed successfully');
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
        return response()->json($content, $content['status']);
    }
}
