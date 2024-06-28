<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.subscribers.index')->with('custom_title', 'Subscribers');
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
        $subscribers = Subscriber::orderBy($sort_column, $sort_order);

        if ($search != '') {
            $subscribers->where(function ($query) use ($search) {
                $query->where('email', 'like', "%{$search}%");
                $query->orWhere('created_at', 'like', "%{$search}%");
            });
        }

        $count = $subscribers->count();

        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $subscribers = $subscribers->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);

        $subscribers = $subscribers->get();

        foreach ($subscribers as $subscriber) {
            if ($subscriber->custom_id == '' || $subscriber->custom_id == null) continue;

            $records['data'][] = [
                'id' => $subscriber->id,
                'email' => $subscriber->email,
                'created_at' => Carbon::parse($subscriber->created_at)->format('d-m-Y h:i A'),
            ];
        }

        return $records;
    }
}
