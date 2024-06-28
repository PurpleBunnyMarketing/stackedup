<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqsRequest;
use App\Http\Requests\Admin\PackagesRequest;
use App\Models\Faqs;
use App\Models\Package;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('admin.pages.packages.index')->with(['custom_title' => 'Manage Packages']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pages.packages.create')->with(['custom_title' => 'Packages']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PackagesRequest $request)
    {
        DB::beginTransaction();
        try {
            $interval = '';
            $stripeProductId = config('utility.STRIPE_PRODUCT')
                ?? "";
            if ($request->package_type) $interval = ($request->package_type == 'monthly') ? 'month' : '';

            $percentageOffAnnual = $request->percentageOff ?? "";
            $yearlyPrice = ($request->amount) ? ($request->amount * 12) : '';
            $discountPriceAnnual = ($percentageOffAnnual) ? $yearlyPrice * $percentageOffAnnual / 100 : '';
            $yearlyDiscountedPrice = ($yearlyPrice && $discountPriceAnnual) ? $yearlyPrice - $discountPriceAnnual : $yearlyPrice;
            $stripe = new \Stripe\StripeClient(
                config('utility.STRIPE_SECRET')
            );
            // creating new price monthly product
            $productPrice = $stripe->prices->create([
                'unit_amount' => $request->amount * 100,
                'currency'    => 'AUD',
                'recurring'   => ['interval' => $interval],
                'product'     => $stripeProductId,
            ]);
            $productPriceYearly = $stripe->prices->create([
                'unit_amount' => $yearlyPrice * 100,
                'currency'    => 'AUD',
                'recurring'   => ['interval' => 'year'],
                'product'     => $stripeProductId,
            ]);
            if ($productPrice && $productPriceYearly) {

                $type = [$request->package_type, 'yearly'];
                $packageExits = Package::whereIn('package_type', $type)->delete();
                $packagesInsert = [];
                $packagesInsert[] = [
                    'custom_id'     => getUniqueString('packages'),
                    'product_id'    => $stripeProductId,
                    'price_id'      => $productPrice->id,
                    'package_type'  => $request->package_type,
                    'amount'        => $request->amount,
                    'actual_yearly_amount' => '',
                    'yearly_off_amount' => '',
                    'created_at'    => Carbon::now(),
                ];
                $packagesInsert[] = [
                    'custom_id'     => getUniqueString('packages'),
                    'product_id'    => $stripeProductId,
                    'price_id'      => $productPriceYearly->id,
                    'package_type'  => 'yearly',
                    'amount'        => $yearlyDiscountedPrice,
                    'actual_yearly_amount' => $yearlyPrice,
                    'yearly_off_amount' => $percentageOffAnnual,
                    'created_at'    => Carbon::now()
                ];
                $packages = Package::insert($packagesInsert);
                if ($packagesInsert) {
                    DB::commit();
                    flash('Packages created successfully!')->success();
                } else {
                    throw new Exception('Unable to save Packages. Please try again later.');
                }
            } else {
                throw new Exception('Unable to save Packages. Please try again later.');
            }
        } catch (Exception $e) {
            DB::rollBack();
            flash($e->getMessage())->error();
        }
        return redirect(route('admin.packages.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show(Package $package)
    {
        return view('admin.pages.packages.show', compact('package'))->with(['custom_title' => 'Package']);
    }


    public function edit(Package $package)
    {
        return view('admin.pages.packages.edit', compact('package'))->with(['custom_title' => 'Package']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PackagesRequest $request, Package $package)
    {

        if (!empty($request->action) && $request->action == 'change_status') {
            $content = ['status' => 204, 'message' => "something went wrong"];
            if ($package) {
                $package->is_active = $request->value;
                if ($package->save()) {
                    $content['status'] = 200;
                    $content['message'] = "Status updated successfully.";
                }
            }
            return response()->json($content);
        } else {
            $package->fill($request->all());
            if ($package->save()) {
                flash('Package updated successfully!')->success();
            } else {
                flash('Unable to update user. Try again later')->error();
            }
            return redirect(route('admin.packages.index'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (!empty($request->action) && $request->action == 'delete_all') {
            $content = ['status' => 204, 'message' => "something went wrong"];
            Package::whereIn('id', explode(',', $request->ids))->delete();
            $content['status'] = 200;
            $content['message'] = "Packages deleted successfully.";
            $content['count'] = Package::all()->count();
            return response()->json($content);
        } else {
            $faqs = Package::where('id', $id)->firstOrFail();
            $faqs->delete();
            if (request()->ajax()) {
                $content = array('status' => 200, 'message' => "Packages deleted successfully.", 'count' => Faqs::all()->count());
                return response()->json($content);
            } else {
                flash('Packages deleted successfully.')->success();
                return redirect()->route('admin.packages.index');
            }
        }
    }



    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $packages = Package::orderBy($sort_column, $sort_order);

        if ($search != '') {
            $packages->where(function ($query) use ($search) {
                $query->where('amount', 'like', "%{$search}%")->orWhere('package_type', 'like', "%{$search}%");
            });
        }

        $count = $packages->count();

        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $packages = $packages->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);

        $packages = $packages->get();
        foreach ($packages as $package) {
            $message = ($package->package_type == 'yearly') ? 'Note:- Your Yearly Price is $' . $package->amount . 'and your Annual Discount Off is ' . $package->yearly_off_amount . '%' : '';
            $params = [
                'checked' => ($package->is_active == 'y' ? 'checked' : ''),
                'getaction' => $package->is_active,
                'class' => '',
                'id' => $package->id,
            ];

            $records['data'][] = [
                'checkbox' => view('admin.layouts.includes.checkbox')->with('id', $package->id)->render(),
                'id' => $package->id,
                'package_type' => $package->package_type,
                'amount' => $package->amount . ' ' . $message,
                'active' => view('admin.layouts.includes.switch', compact('params'))->render(),
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'packages', 'id' => $package->id], $package)->render(),
            ];
        }

        return $records;
    }
    //
}
