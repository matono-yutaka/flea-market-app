<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mock</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css')}}">
  <link rel="stylesheet" href="{{ asset('css/auth/verify-email.css')}}">
</head>

<body>
    <header class="header">
    <img src="{{ asset('storage/images/logo.svg') }}" alt="" class="img-content" />
    </header>
    @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif
<div class="verify-content">
    <h2 class="verify-heading">登録していただいたメールアドレスに認証メールを送付しました。<br>メール認証を完了してください。</h2>
</div>
<div class="verify__link">
    <a href="http://localhost:8025/#" class="verify-btn">認証はこちらから</a>
    <form method="post" action="{{ route('verification.send') }}" >
    @csrf
        <button type="submit" class="reverify-link">認証メールを再送する</button>
    </form>
</div>
</body>

</html>