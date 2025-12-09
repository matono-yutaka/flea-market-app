@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/mypage_trading.css')}}">
@endsection

@section('content')

<div class="upper-contents">
    <div class="image-box">
    <img src="{{ isset($profile) && $profile->image ? asset('storage/images/national_flag/' . $profile->image) : asset('storage/images/20200501_noimage.jpg') }}" alt="" class="profile-image">
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
        <a class="purchase-list" href="{{ route('mypage', ['page' => 'buy']) }}">購入した商品</a>
        <div class="trading-messages">
            <p class="trading-list" >取引中の商品</p>
            @if($totalUnread > 0)<span class="total-unread-badge">{{ $totalUnread }}</span>
            @endif
        </div>
</div>
    <div class="divider"></div>
    <div class="product-list">
        @foreach($tradingItems as $tradingItem)
            {{-- 購入者側 --}}
            @if ($tradingItem->user_id === auth()->id() && $tradingItem->rating_buyer === null )
                <a href="{{ url('/chat/' . $tradingItem->id) }}" class="product-card-link">
                <div class="product-card">
                    @if($tradingItem->unread_count > 0)
                    <span class="unread-badge">{{ $tradingItem->unread_count }}</span>
                    @endif
                    <img src="{{ Str::startsWith($tradingItem->item->image, 'http') ? $tradingItem->item->image : asset('storage/images/' . $tradingItem->item->image) }}" alt="{{ $tradingItem->item->name }}">
                    <div class="card-info">
                        <p class="card-name">{{ $tradingItem->item->name }}</p>
                    </div>
                </div>
                </a>
            @endif
            {{-- 出品者側 --}}
            @if ($tradingItem->item->user_id === auth()->id() && $tradingItem->rating_seller === null)
                <a href="{{ url('/chat/' . $tradingItem->id) }}" class="product-card-link">
                <div class="product-card">
                    @if($tradingItem->unread_count > 0)
                    <span class="unread-badge">{{ $tradingItem->unread_count }}</span>
                    @endif
                    <img src="{{ Str::startsWith($tradingItem->item->image, 'http') ? $tradingItem->item->image : asset('storage/images/' . $tradingItem->item->image) }}" alt="{{ $tradingItem->item->name }}">
                    <div class="card-info">
                        <p class="card-name">{{ $tradingItem->item->name }}</p>
                    </div>
                </div>
                </a>
            @endif
        @endforeach
    </div>

@endsection