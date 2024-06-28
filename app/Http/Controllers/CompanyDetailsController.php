<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyDetailsRequest;
use Illuminate\Support\Facades\DB;

class CompanyDetailsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('frontend.pages.company.index', compact('user'))->withTitle('Company Details');;
    }

    public function edit()
    {
        $user = auth()->user();
        return view('frontend.pages.company.update', compact('user'))->withTitle('Edit Company Details');;
    }

    public function update(CompanyDetailsRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $user->fill($request->validated());
            $user->save();
            flash('Company Details Updated Succesfully !')->success();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            flash('Something Went Wrong, Try after sometimes')->error();
            return redirect()->back();
        }
        return redirect()->route('company.details');
    }
}
