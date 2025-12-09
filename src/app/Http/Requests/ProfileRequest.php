<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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

    protected function prepareForValidation()
    {
        if ($this->hasFile('image')) {
            $tempPath = $this->file('image')->store('temp', 'public');
            $this->session()->put('temp_profile_image_path', $tempPath);
        }
    }

    public function rules()
{
    return [
        'image' => 'nullable|mimes:png,jpg,jpeg',
        'name' => 'required|max:20',
        'post_code' => 'required|size:8',
        'address' => 'required',
        'building' => 'nullable',
    ];
}


    Public function messages()
    {
        return [
            'image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
            'name.required' => 'お名前を入力してください',
            'name.max' => '20文字以内で入力してください',
            'post_code.required' => '郵便番号を入力してください',
            'post_code.size' => 'ハイフンありの8文字で入力してください',
            'address.required' => '住所を入力してください',
        ];
    }
}
