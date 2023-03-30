<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
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
            'name' => 'string|max:255',
            'type' => 'integer|in:1,2,3,4',
            'notes' => 'nullable|string|max:500',
            'favorite' => 'nullable|boolean',
            'user_id' => 'nullable',
            'folder_id' => 'nullable',
            'organization_id' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'name.max' => 'Please Try To Use Shorter Name',
            'type.min' => 'Type is not valid',
            'type.max' => 'Type is not valid',
            'notes.max' => 'Text is too long',
        ];
    }
}
