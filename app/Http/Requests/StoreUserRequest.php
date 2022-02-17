<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'fname' => ['required', 'string'],
            //'lname' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string']
           
        ];
    }
    public function messages()
    {
        return [
            'fname.required' => 'A user first name is required',
            //'lname.required' => 'A user last name is required',
            'email.required' => 'A user email is required',
            'password.required' => 'A user password is required'
            
        ];
    }
}
