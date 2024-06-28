<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\StaffDetailResource;
use App\Http\Resources\v1\StaffListResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    private $version = "v.1.0";
    public function getVersion()
    {
        return $this->version;
    }

    /* FAQ*/
    public function staffList(Request $request)
    {
        $user = $request->user();
        try {
            $staff = User::where('parent_id', $user->id)->get();
            return (new StaffListResource($staff))
                ->additional([
                    'meta' => [
                        'message' => trans('api.list', ['entity' => 'Staff List']),
                        'count'    => $staff->count() ?? "",
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
    public function staffDetail(Request $request)
    {
        $rules = [
            'staff_id'       => 'required|exists:users,custom_id',
        ];
        if ($this->apiValidator($request->all(), $rules)) {
            try {

                $staff = User::with(['posts' => function ($q) {
                    $q->orderBy('created_at', 'DESC');
                }])->where('custom_id', $request->staff_id)->first();
                return (new StaffDetailResource($staff))
                    ->additional([
                        'meta' => [
                            'message' => trans('api.list', ['entity' => 'Staff list']),
                        ]
                    ]);
            } catch (\Exception $ex) {
                $this->status = $this->statusArr['something_wrong'];
                $this->response['message'] = trans('api.error');
            }
            $this->response['meta']['api'] = $this->version;
            $this->response['meta']['url'] = url()->current();
        }
        return $this->return_response();
    }
    public function staffDestroy(Request $request)
    {

        $rules = [
            'staff_id' => 'required|string|exists:users,custom_id'
        ];
        if ($this->apiValidator($request->all(), $rules)) {
            try {
                $id = $request->staff_id ?? "";
                $staff = User::where('custom_id', $id)->with('company')->firstOrFail();
                if ($staff->company->id === Auth::id()) {
                    if ($staff->profile_photo) {
                        if (Storage::exists($staff->profile_photo)) {
                            Storage::delete($staff->profile_photo);
                        }
                    }
                    $staff->delete();
                    $this->response['data']  = null;
                    $this->status = $this->statusArr['success'];
                    $this->response['meta']['message']  =  trans('api.delete', ['entity' => trans('Staff')]);
                } else {
                    $this->response['meta']['message']  =  trans('api.unauthenticated_staff_delete');
                    $this->status = $this->statusArr['something_wrong'];
                }
            } catch (\Throwable $th) {
                $this->status = $this->statusArr['something_wrong'];
                $this->response['meta']['message'] = trans('api.error');
            }
            $this->response['meta']['api'] = $this->version;
            $this->response['meta']['url'] = url()->current();
        }
        return $this->return_response();
    }
}
