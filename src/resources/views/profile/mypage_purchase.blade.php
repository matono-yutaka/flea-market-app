@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/mypage_purchase.css')}}">
@endsection

@section('content')

<div class="upper-contents">
    <div class="image-box">
    <img src="{{ isset($profile) && $profile->image ? asset('storage/images/national_flag/' . $profile->image) : asset('storage/images/20200501_noimage.jpg') }}"alt="" class="profile-image">
    <h2 class="user-name">{{isset($profile) && $profile->name ? $profile->name : ''}}</h2>
    </div>
    <a class="edit-link" href="/mypage/profile">プロフィールを編集</a>
</div>
<div class="top-list">
        <a class="exhibit-list" href="{{ route('mypage', ['page' => 'sell']) }}">出品した商品</a>
        <p class="purchase-list">購入した商品</p>
</div>
    <div class="divider"></div>
    <div class="product-list">
            @foreach($purchases as $purchase)
                <a href="{{ url('/item/' . $purchase->item->id) }}" class="product-card-link">
                <div class="product-card">
                    <img src="{{ Str::startsWith($purchase->item->image, 'http') ? $purchase->item->image : asset('storage/images/' . $purchase->item->image) }}" alt="{{ $purchase->item->name }}">
                    <div class="card-info">
                        <p class="card-name">{{ $purchase->item->name }}</p>
                    </div>
                </div>
                </a>
            @endforeach
    </div>

@endsection