<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIdentityRequest extends FormRequest
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
            'item_id' => 'required',
            'title' => 'string|max:255',
            'username' => 'string|max:255',
            'first_name' => 'string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'string|max:255',
            'address' => 'string|max:255',
            'phone' => 'string|max:255',
            'email' => 'email|max:255',
            'security_number' => 'string|max:255',
            'license_number' => 'string|max:255',
        ];
    }
}
