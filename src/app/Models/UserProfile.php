<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'image', 'name', 'post_code', 'address', 'building',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

}
