<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    use HasFactory;

    protected $fillable = ['post_code', 'address', 'building', 'user_id', 'item_id'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
