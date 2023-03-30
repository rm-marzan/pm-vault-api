<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCardRequest extends FormRequest
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
            'cardholder_name' => 'string|max:255',
            'brand' => 'string|max:255',
            'number' => [
                'string',
                'max:500',
                Rule::unique('cards')->ignore($this->card),
            ],
            'exp_month' => 'string|max:20',
            'exp_year' => 'string|max:4',
            'cvv' => 'string|max:500',
        ];
    }
}
