<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqsRequest;
use App\Models\Faqs;
use Illuminate\Http\Request;

class FAQsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        return view('admin.pages.faqs.index')->with(['custom_title' => 'Faqs']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pages.faqs.create')->with(['custom_title' => 'Faqs']);
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FaqsRequest $request)
    {
        $request['custom_id'] = getUniqueString('faqs');
        $faqs = Faqs::create($request->all());
        if ($faqs) {
            flash('Faqs created successfully!')->success();
        } else {
            flash('Unable to save Faqs. Please try again later.')->error();
        }
        return redirect(route('admin.faqs.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Faqs $faq)
    {
        return view('admin.pages.faqs.edit', compact('faq'))->with(['custom_title' => 'Faqs']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FaqsRequest $request, Faqs $faq)
    {
        if (!empty($request->action) && $request->action == 'change_status') {
            $content = ['status' => 204, 'message' => "something went wrong"];
            if ($faq) {
                $faq->is_active = $request->value;
                if ($faq->save()) {
                    $content['status'] = 200;
                    $content['message'] = "Status updated successfully.";
                }
            }
            return response()->json($content);
        } else {
            $faq->fill($request->all());
            if ($faq->save()) {
                flash('Faqs updated successfully!')->success();
            } else {
                flash('Unable to update user. Try again later')->error();
            }
            return redirect(route('admin.faqs.index'));
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
            Faqs::whereIn('id', explode(',', $request->ids))->delete();
            $content['status'] = 200;
            $content['message'] = "Faqs deleted successfully.";
            $content['count'] = Faqs::all()->count();
            return response()->json($content);
        } else {
            $faqs = Faqs::where('id', $id)->firstOrFail();
            $faqs->delete();
            if (request()->ajax()) {
                $content = array('status' => 200, 'message' => "Faqs deleted successfully.", 'count' => Faqs::all()->count());
                return response()->json($content);
            } else {
                flash('Faqs deleted successfully.')->success();
                return redirect()->route('admin.faqs.index');
            }
        }
    }



    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $faqs = Faqs::orderBy($sort_column, $sort_order);

        if ($search != '') {
            $faqs->where(function ($query) use ($search) {
                $query->where('question', 'like', "%{$search}%");
            });
        }

        $count = $faqs->count();

        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $faqs = $faqs->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order);

        $faqs = $faqs->get();
        foreach ($faqs as $faq) {

            $params = [
                'checked' => ($faq->is_active == 'y' ? 'checked' : ''),
                'getaction' => $faq->is_active,
                'class' => '',
                'id' => $faq->id,
            ];

            $records['data'][] = [
                'checkbox' => view('admin.layouts.includes.checkbox')->with('id', $faq->id)->render(),
                'id' => $faq->id,
                'question' => $faq->question,
                'active' => view('admin.layouts.includes.switch', compact('params'))->render(),
                'action' => view('admin.layouts.includes.actions')->with(['custom_title' => 'faqs', 'id' => $faq->id], $faq)->render(),
            ];

        }

        return $records;
    }
    //
}
