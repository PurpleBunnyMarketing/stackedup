<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeRequest;
use App\Mail\SubscribeMail;
use App\Models\Subscriber;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SubscriberController extends Controller
{
    public function subscribe(SubscribeRequest $request)
    {
        try {
            $request['custom_id'] = getUniqueString('subscribers');
            $subscribe = Subscriber::create($request->all());
            // Mail::to($request->email)->send(new SubscribeMail());
            if (!$subscribe) throw new Exception();
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong'], 412);
        }
        return response()->json(['status' => 'success', 'message' => 'Subscribed successfully',], 200);
    }
}
