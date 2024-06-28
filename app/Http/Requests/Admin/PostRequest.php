<?php

namespace App\Http\Requests\Admin;

use App\Rules\CheckSchedulePostTimeRule;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        switch ($this->method()) {
            case 'POST':
                $unless = "change_status";
                $id = (!empty(Route::current()->parameters()['post']->id) ? Route::current()->parameters()['post']->id : NULL);
                return [
                    'caption'           =>  'required_unless:action,' . $unless,
                    // 'hashtag'           =>  'required_unless:action,' . $unless,
                    'hashtag'           =>  'nullable|sometimes',
                    'schedule_date'     =>  'sometimes|nullable|date|date_format:m/d/Y',
                    'schedule_time'     =>  ['required_with:schedule_date', 'nullable', 'date_format:g:i A', new CheckSchedulePostTimeRule()],
                    // 'schedule_time'     =>  ['required_with:schedule_date', 'nullable', 'date_format:g:i A',],
                    'media_page_id.*'   =>  'required_unless:action,' . $unless,
                    // 'upload_file'       => 'required_unless:action,'.$unless.'|mimes:jpeg,png,jpg,mp4',
                    'action_type'       => 'nullable|sometimes',
                    // 'action_link'       => 'required_unless:action_type,ACTION_TYPE_UNSPECIFIED,CALL'
                ];
                break;
            case 'PUT':
                $unless = "change_status";
                $id = (!empty(Route::current()->parameters()['post']->id) ? Route::current()->parameters()['post']->id : NULL);
                return [
                    'caption'           =>  'required_unless:action,' . $unless,
                    'hashtag'           =>  'nullable|sometimes',
                    'schedule_date'     =>  'sometimes|nullable|date|date_format:m/d/Y',
                    'schedule_time'     =>  ['required_with:schedule_date', 'nullable', 'date_format:g:i A', new CheckSchedulePostTimeRule()],
                    // 'schedule_time'     =>  ['required_with:schedule_date', 'nullable', 'date_format:g:i A',],
                    'media_page_id.*'   =>  'required_unless:action,' . $unless,
                    // 'upload_file'       => 'required_unless:action,'.$unless.'|mimes:jpeg,png,jpg,mp4',
                    'action_type'       => 'nullable|sometimes',
                    // 'action_link'       => 'required_unless:action_type,ACTION_TYPE_UNSPECIFIED,CALL'
                ];
                break;
        }
    }

    public function messages()
    {
        return [
            'caption.required_unless' => 'Caption field is required',
            'hashtag.required_unless' => 'Hashtag field is required',
            'schedule_date.date_formate' => 'Schedule Date field must be in the formate of mm/dd/yyyy format',
            'action_link.required_if' => 'Please Enter the Action Link field , if your select above option except  NONE or the CALL'
        ];
    }
}
