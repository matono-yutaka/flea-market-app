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

    // この商品をいいねしたユーザー一覧
    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'goods')->withTimestamps();
    }
}
