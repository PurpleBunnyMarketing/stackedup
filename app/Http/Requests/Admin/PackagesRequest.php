<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class PackagesRequest extends FormRequest
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
        $unless = "change_status";
        $id = (!empty(Route::current()->parameters()['package']->id) ? ',' . Route::current()->parameters()['package']->id : '');
        switch ($this->method()) {
            case 'POST':
                return [
                    'package_type' => 'required',
                    'amount'       => 'required',
                    'percentageOff' => 'required',
                ];
                break;
            case 'PUT':
                return [
                    'package_type'   =>  'required_unless:action,' . $unless,
                    'amount'         =>  'required_unless:action,' . $unless,
                    'percentageOff'         =>  'required_unless:action,' . $unless,
                    // 'current_points' => 'required_unless:action,' . $unless . '|numeric|min:0',
                    // 'current_cookies' => 'required_unless:action,' . $unless . '|numeric|min:0',
                    // 'profile_photo' => 'nullable:action,' . $unless . '|mimes:jpeg,jpg,png',
                ];
                break;
        }
    }
}
