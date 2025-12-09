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

    // バリデーション前の処理、VD前にセッションにパスを入れないとVD後再表示されない(コントローラーはVD後の処理のためこれをコントローラーに書いても再表示されない)
    protected function prepareForValidation()
    {
        // 新しく画像がアップロードされた場合
        if ($this->hasFile('image')) {
            // 一時保存場所へのパス
            $tempPath = $this->file('image')->store('temp', 'public');
            // バリデーションエラー後のプレビュー保持用にパスをセッションへ保存
            $this->session()->put('temp_item_image_path', $tempPath);
        }
    }

    public function rules()
{
    return [
        'image' => [
            'required_without:temp_item_image_path',
            'mimes:png,jpg,jpeg',
        ],
        'temp_item_image_path' => 'nullable',
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
            'image.required_without' => '商品画像をアップロードしてください',
            'image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
            'categories.required' => '商品のカテゴリーを選択してください',
            'condition.required' => '商品の状態を選択してください',
            'price.required' => '販売価格を入力してください',
            'price.integer' => '数値で入力してください',
            'price.min' => '0円以上で入力してください',
        ];
    }
}
