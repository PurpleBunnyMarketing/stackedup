<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppUpdateSetting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppUpdateSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        $settings = AppUpdateSetting::get();
        // dd($settings);
        return view('admin.pages.app-update.index', compact('settings'))->with(['custom_title' => 'App Update Settings']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            AppUpdateSetting::updateOrCreate(
                ['slug' => 'android'],
                [
                    'build_version' => $request->build_version['android'],
                    'app_version' => $request->app_version['android'],
                    'is_force_update' => isset($request->is_force_update['android']) && !empty($request->is_force_update['android']) ? '1' : '0',
                ]
            );
            AppUpdateSetting::updateOrCreate(
                ['slug' => 'ios'],
                [
                    'build_version' => $request->build_version['ios'],
                    'app_version' => $request->app_version['ios'],
                    'is_force_update' => isset($request->is_force_update['ios']) && !empty($request->is_force_update['ios']) ? '1' : '0',
                ]
            );
            flash('App Version Settings Updated Successfully')->success();
            DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
            flash('Something went Wrong, please Try Again..!')->error();
        }
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
