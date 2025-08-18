@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/mypage.css')}}">
@endsection

@section('content')

<div class="upper-contents">
    <div class="image-box">
    <img src="{{ isset($profile) && $profile->image ? asset('storage/images/national_flag/' . $profile->image) : asset('storage/images/20200501_noimage.jpg') }}" alt="" class="profile-image">
    <h2 class="user-name">{{$profile->name}}</h2>
    </div>
    <a class="edit-link" href="/mypage/profile">プロフィールを編集</a>
</div>
<div class="top-list">
        <P class="exhibit-list">出品した商品</p>
        <a class="purchase-list" href="{{ route('mypage', ['page' => 'buy']) }}">購入した商品</a>
    </div>
    <div class="divider"></div>
    <div class="product-list">
            @foreach($items as $item)
                <a href="{{ url('/item/' . $item->id) }}" class="product-card-link">
                <div class="product-card">
                    <img src="{{ Str::startsWith($item->image, 'http') ? $item->image : asset('storage/images/' . $item->image) }}" alt="{{ $item->name }}">
                    <div class="card-info">
                        <p class="card-name">{{ $item->name }}</p>
                        @if(in_array($item->id, $purchasedItemIds))
                        <span class="sold">sold</span>
                        @endif
                    </div>
                </div>
                </a>
            @endforeach
    </div>

@endsection