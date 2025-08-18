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
        'image' => [
            function ($attribute, $value, $fail) {
                // 新規アップロードもhiddenも無くて、DBに画像が無ければエラーにしたい場合はここで判定
                // もしDBの既存画像を確認できるなら $this->route('user') などでモデル取得して確認する
                // 例：既存プロフィールの画像名を取得
                $profile = auth()->user()->profile;

                if (
                    !$this->hasFile('image') &&
                    !$this->filled('temp_profile_image_path') &&
                    (!$profile || !$profile->image)
                ) {
                    // 画像は必須じゃないならエラーは出さずに return だけ
                    return;
                    // 画像必須ならここで $fail('画像をアップロードしてください');
                }
            },
            function ($attribute, $value, $fail) {
                if ($this->hasFile('image')) {
                    $file = $this->file('image');
                    $allowed = ['png', 'jpeg', 'jpg'];
                    if (!in_array($file->extension(), $allowed)) {
                        $fail('「.png」または「.jpeg」形式でアップロードしてください');
                    }
                }
            },
        ],
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
