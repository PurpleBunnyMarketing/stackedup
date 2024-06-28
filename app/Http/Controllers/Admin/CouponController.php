<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Exception;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.coupons.index')->with(['custom_title' => 'Manage Coupons']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pages.coupons.create')->with(['custom_title' => 'Coupons']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $stripeProductId = config('utility.STRIPE_PRODUCT') ?? "";
        $stripe = new \Stripe\StripeClient(
            config('utility.STRIPE_SECRET')
        );
        $stripeCoupons = [];
        if ($request->amount_off) {
            // creating new coupons
            $stripeCoupons = $stripe->coupons->create([
                'name'  => $request->coupon_name ?? "",
                'amount_off' => $request->amount_off * 100 ?? "",
                'currency'   =>  'AUD',
            'duration' => 'forever',
                'applies_to' => ["products" => [$stripeProductId]],
            ]);
        } elseif ($request->percentage_off) {
            // creating new coupons
            $stripeCoupons = $stripe->coupons->create([
                'name'  => $request->coupon_name ?? "",
                'percent_off' => $request->percentage_off ?? "",
                'duration' => 'forever',
                'applies_to' => ["products" => [$stripeProductId]],
            ]);
        }
        // duration' => 'forever' once,repeating,forever enum
        if (!empty($stripeCoupons)) {
            $coupons = Coupon::create([
                'custom_id'     => getUniqueString('coupons'),
                'coupon_id'     => $stripeCoupons->id,
                'name'          => $request->coupon_name ?? "",
                'percentageOff' => $request->percentage_off ?? "",
                'amountOff'     => $request->amount_off ?? "",
            ]);

            if ($coupons) {
                flash('Coupon created successfully!')->success();
            } else {
                flash('Unable to save Coupon. Please try again later.')->error();
            }
        } else {
            flash('Unable to save Coupon. Please try again later.')->error();
        }
        return redirect(route('admin.coupons.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Coupon $coupon)
    {
        return view('admin.pages.coupons.show', compact('coupon'))->with(['custom_title' => 'Coupon']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Coupon $coupon)
    {
        // return view('admin.pages.coupons.edit', compact('package'))->with(['custom_title' => 'Package']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coupon $coupon)
    {
        try {

            $stripe = new \Stripe\StripeClient(config('utility.STRIPE_SECRET'));
            $stripe_coupon = $stripe->coupons->retrieve($coupon->coupon_id);

            $stripe_coupon->delete() ? $coupon->delete() : '';

            $content = array('status' => 200, 'message' => "Coupon deleted successfully.", 'count' => Coupon::all()->count());
            return response()->json($content);

        } catch (\Exception $e) {
            $content = array('status' => 500, 'message' => 'Coupon deletion failed: ' . $e->getMessage(), 'count' => Coupon::all()->count());
            return response()->json($content);
        }
    }

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $coupons = Coupon::orderBy($sort_column, $sort_order);

        if ($search != '') {
            $coupons->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        }

        $count = $coupons->count();

        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $coupons = $coupons->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);

        $coupons = $coupons->get();
        foreach ($coupons as $coupon) {

            $params = [
                'checked' => ($coupon->is_active == 'y' ? 'checked' : ''),
                'getaction' => $coupon->is_active,
                'class' => '',
                'id' => $coupon->id,
            ];

            $records['data'][] = [
                'checkbox' => view('admin.layouts.includes.checkbox')->with('id', $coupon->id)->render(),
                'id' => $coupon->id,
                'name' => $coupon->name,
                'percentageOff' => $coupon->percentageOff,
                'amountOff' => $coupon->amountOff,
                'active' => view('admin.layouts.includes.switch', compact('params'))->render(),
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'coupons', 'id' => $coupon->id], $coupon)->render(),
            ];
        }

        return $records;
    }
}
