<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubscribeRequest extends FormRequest
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
        return [
            'email' => 'required|unique:subscribers,email',
        ];
    }
    public function messages()
    {
        return [
            'email.required' => 'Email is required',
            'email.unique' => 'This email is already subscribed.',
        ];
    }
    // public function failedValidation(Validator $validator)
    // {
    //     throw new HttpResponseException(response()->json([
    //         'meta' => [
    //             'message'   =>  $validator->errors()->first(),
    //         ]
    //     ], 412));
    // }
}
