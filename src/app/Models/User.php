<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function comment()
    {
        return $this->hasMany(Comment::class);
    }

    // いいねした商品の一覧を取得するリレーション定義
    public function goods()
    {
        // User と Item は多対多の関係にある
        // 中間テーブル（第2引数）として 'goods' テーブルを使用する
        // withTimestamps() は中間テーブルの created_at / updated_at を自動で更新する設定
        return $this->belongsToMany(Item::class, 'goods')->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

}
