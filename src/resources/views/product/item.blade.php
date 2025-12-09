@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/product/item.css')}}">
@endsection

@section('content')

<div class="all-contents">
  <div class="image-box">
    <img src="{{ Str::startsWith($item->image, 'http') ? $item->image : asset('storage/images/' . $item->image) }}" alt="{{ $item->name }}" class="item-image">
  </div>
  <div class="right-content">
    <h1 class="item-name">{{$item->name}}</h1>
    <p class="brand-name">{{$item->brand_name}}</p>
    <p class="price"><span class="price-text">¥</span>{{number_format($item->price)}}<span class="price-text">（税込）</span></p>
    <div class="logos">
      <div class="good-group">
      @if(auth()->id() === ($item->user_id))
      <button disabled class="good-non"> <img src="/storage/images/星アイコン8.svg" alt="" class=""></button>
      @else
      @auth
        @if(auth()->user()->goods->contains($item->id))
        <form action="{{ url('/good/' . $item->id) }}" method="POST">
        @csrf
        <button class="good-yes"><img src="/storage/images/星アイコン6.svg" alt="" class=""></button>
        </form>
        @else
        <form action="{{ url('/good/' . $item->id) }}" method="POST">
        @csrf
        <button class="good-no"> <img src="/storage/images/星アイコン8.svg" alt="" class=""></button>
        </form>
        @endif
      @else
        <button disabled class="good-non"> <img src="/storage/images/星アイコン8.svg" alt="" class=""></button>
      @endauth
      @endif
        <p class="number">{{ $item->likedUsers->count() }}</p>
      </div>
      <div class="comment-group2">
        <img src="/storage/images/message.svg" alt="コメントアイコン" class="comment-icon">
        <p class="number">{{ $comments->count() }}</p>
      </div>
    </div>
    @if(auth()->id() !== ($item->user_id))
    <form class="purchase-form" action="{{ url('/purchase/' . $item->id) }}" method="get">
      <input class="purchase-btn" type="submit" value="購入手続きへ">
      @else
      <input class="purchase-btn_disabled" type="submit" value="購入手続きへ" disabled>
    @endif
    <h2 class="item-description">商品説明</h2>
    <p class="description">{{$item->description}}</p>
    <h2 class="item-information">商品の情報</h2>
    <div class="categories">
      <h3 class="category">カテゴリー</h3>
      @foreach ($item->categories as $category)
      <p class="category-label">{{$category->name}}</p>
      @endforeach
    </div>
    <div class="item-conditions">
      <h3 class="item-condition_text">商品の状態</h3>
      <p class="item-condition">{{$item->condition}}</p>
    </div>
    </form>
    @foreach($comments as $comment)
    <h2 class="comment-heading">コメント({{ $loop->iteration }})</h2>
    <div class="comment-group">
      @if($comment->user->profile && $comment->user->profile->image)
      <img src="{{ asset('storage/images/national_flag/' . $comment->user->profile->image) }}" alt="no-image" class="comment-image">
      @else
      <img src="{{ asset('storage/images/20200501_noimage.jpg') }}" alt="" class="comment-image">
      @endif
      <p class="comment-name">{{$comment->user->name}}</p>
    </div>
      <p class="comment-comment">{{$comment->comment}}</p>
    @endforeach
    @if(auth()->id() !== ($item->user_id))
    <form class="comment-form" action="{{ url('/comment/' . $item->id) }}"  method="post">
      @csrf
        <h3 class="product-comment">商品へのコメント</h3>
        <textarea cols="30" rows="9" name="comment" class="comment" id="description">{{ old('comment') }}</textarea>
        <p class="comment-form__error-message">
          @error('comment')
          {{ $message }}
          @enderror
        </p>
        <input type="submit" class="comment-btn" value="コメントを送信する">
        @else
        <h3 class="product-comment">商品へのコメント</h3>
        <textarea cols="30" rows="9" name="comment" class="comment" id="description">{{ old('comment') }}</textarea>
        <input type="submit" class="comment-btn_disabled" value="コメントを送信する" disabled>
    </form>
    @endif

  </div>

</div>

@endsection

