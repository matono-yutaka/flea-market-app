@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css')}}">
@endsection

@section('content')

<div class="all-content">
    <div class="top-list">
        <P class="recommend">おすすめ</p>
        <a class="my-list" href="{{ route('index', ['tab' => 'mylist', 'keyword' => request('keyword')]) }}">マイリスト</a>
    </div>
    <div class="divider"></div>
    <div class="item-list">
        @foreach($items as $item)
            <a href="{{ url('/item/' . $item->id) }}" class="item-card-link">
                <div class="item-card">
                    <img src="{{ Str::startsWith($item->image, 'http') ? $item->image : asset('storage/images/' . $item->image) }}" alt="{{ $item->name }}">
                    <div class="card-info">
                        <p class="card-name">{{ $item->name }}</p>
                        @if(in_array($item->id, $purchasedItemIds))
                        <span class="sold">Sold</span>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection