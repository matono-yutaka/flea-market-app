<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'price', 'brand_name', 'description', 'image', 'condition','user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // コントローラーで使ってない、残念・・・
    public function scopeKeywordSearch($query, $keyword)
    {
        if (!empty($keyword)) {
        $query->where('name', 'like', '%' . $keyword . '%');
        }
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // この商品をいいねしたユーザー一覧を取得するリレーション定義
    public function likedUsers()
    {
        // Item（商品）と User（ユーザー）は多対多の関係にある
        // 中間テーブル 'goods' を介して関連付ける
        // withTimestamps() は中間テーブルの created_at / updated_at を自動的に管理
        return $this->belongsToMany(User::class, 'goods')->withTimestamps();
    }
}
