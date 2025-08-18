<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'select' => 'required',
            'address' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'select.required' => '支払い方法を選択してください',
            'address.required' => '住所を入力してください'
        ];

    }
}
