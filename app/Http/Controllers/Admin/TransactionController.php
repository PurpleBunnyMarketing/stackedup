<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.transactions.index')->with('custom_title', 'Transactions');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
    public function destroy($id)
    {
        //
    }

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $transactions = Payment::orderBy($sort_column, $sort_order);

        if ($search != '') {
            $transactions->whereHas('user', function ($query) use ($search) {
                $query->where('full_name', 'like', "%{$search}%");
            });
        }

        $count = $transactions->count();

        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $transactions = $transactions->offset($offset)->limit($limit)->orderBy('created_at', 'desc');

        $transactions = $transactions->get();
        foreach ($transactions as $transaction) {

            $records['data'][] = [
                // 'checkbox' => view('admin.layouts.includes.checkbox')->with('id', $transaction->custom_id)->render(),
                'subscription_id' => $transaction->subscription_id ?? '',
                'user_name' => $transaction->user->full_name ?? '',
                'email' => $transaction->user->email ?? '',
                'type' => $transaction->type ?? '',
                'amount'  => $transaction->amount,
                'date_time' => $transaction->created_at ? Carbon::parse($transaction->created_at)->format('d M, Y H:i: a') : '',
                'status' => $transaction->payment_status === 'completed' ? '<span class="label label-lg font-weight-bolder label-light-success label-inline">Recieved</span>' : '<span class="label font-weight-bolder label-lg label-light-primary label-inline">Pending</span>',
                // 'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'Transactions', 'id' => $transaction->custom_id], $transaction)->render(),
            ];
        }
        return $records;
    }
}
