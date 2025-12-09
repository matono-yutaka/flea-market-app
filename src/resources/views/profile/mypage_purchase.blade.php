@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/mypage_purchase.css')}}">
@endsection

@section('content')

<div class="upper-contents">
    <div class="image-box">
    <img src="{{ isset($profile) && $profile->image ? asset('storage/images/national_flag/' . $profile->image) : asset('storage/images/20200501_noimage.jpg') }}"alt="" class="profile-image">
        <div class="user-info">
            <h2 class="user-name">{{$profile->name}}</h2>
            @if($roundedAvg)
            @for($i = 1; $i <= 5; $i++)
            <span class="{{ $i <= $roundedAvg ? 'star-on' : 'star-off' }}">★</span>
            @endfor
            @endif
        </div>
    </div>
    <a class="edit-link" href="/mypage/profile">プロフィールを編集</a>
</div>
<div class="top-list">
        <a class="exhibit-list" href="{{ route('mypage', ['page' => 'sell']) }}">出品した商品</a>
        <p class="purchase-list">購入した商品</p>
        <div class="trading-messages">
            <a class="trading-list" href="{{ route('mypage', ['page' => 'trading']) }}">取引中の商品</a>
            @if($totalUnread > 0)<span class="total-unread-badge">{{ $totalUnread }}</span>
            @endif
        </div>
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