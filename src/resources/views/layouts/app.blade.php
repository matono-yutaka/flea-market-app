<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mock</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css')}}">
  <link rel="stylesheet" href="{{ asset('css/common.css')}}">
  @yield('css')
</head>

@php
use Illuminate\Support\Str;
@endphp

<body>
  <div class="app">
    <header class="header">
        <img src="{{ asset('storage/images/logo.svg') }}" alt="" class="img-content" />

        <form action="/" method="get">
        <input class="header__text" type="text"  placeholder="なにをお探しですか？" name="keyword" value="{{ request('keyword') }}">
        <input type="hidden" name="tab" value="{{ $tab ?? 'recommend' }}">
        </form>

        <div class="app__link">
        @if (Auth::check())
        <form action="/logout" method="post">
        @csrf
        <input class="header__btn" type="submit"  value="ログアウト">
        </form>
        @endif
        @guest
        <a href="/login" class="header__btn">ログイン</a>
        @endguest
        <a class="mypage__btn" href="{{ route('mypage', ['page' => 'sell']) }}">マイページ</a>
        <a class="sell__btn" href="/sell">出品</a>
        </div>
    </header>

    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif

    <div class="content">
      @yield('content')
    </div>
  </div>
</body>

</html>