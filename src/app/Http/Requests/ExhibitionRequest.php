<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            $this->session()->put('temp_item_image_path', $tempPath);
        }
    }

    public function rules()
{
    return [
        'image' => [
            function ($attribute, $value, $fail) {
                // アップロードファイルなし＆セッション or hidden の temp_item_image_path も空ならエラー
                if (!$this->hasFile('image') && !$this->filled('temp_item_image_path')) {
                    $fail('商品画像をアップロードしてください');
                }
            },
            // ファイルがある場合のみmimesチェックを
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
        // 他のバリデーションルール
        'name' => 'required',
        'description' => 'required|max:255',
        'categories' => 'required',
        'condition' => 'required',
        'price' => 'required|integer|min:0',
        'brand_name' => 'nullable',
    ];
}



    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '255文字以内で入力してください',
            'image.required' => '商品画像をアップロードしてください',
            'image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
            'categories.required' => '商品のカテゴリーを選択してください',
            'condition.required' => '商品の状態を選択してください',
            'price.required' => '販売価格を入力してください',
            'price.integer' => '数値で入力してください',
            'price.min' => '0円以上で入力してください',
        ];
    }
}
