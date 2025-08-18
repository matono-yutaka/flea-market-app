<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mock</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css')}}">
  <link rel="stylesheet" href="{{ asset('css/auth/register.css')}}">
</head>

<body>
    <header class="header">
    <img src="{{ asset('storage/images/logo.svg') }}" alt="" class="img-content" />
    </header>

<div class="register-form">
  <h2 class="register-form__heading">会員登録</h2>
  <div class="register-form__inner">
    <form class="register-form__form" action="/register" method="post">
      @csrf
      <div class="register-form__group">
        <label class="register-form__label" for="name">ユーザー名</label>
        <input class="register-form__input" type="text" name="name" id="name" value="{{ old('name') }}">
        <p class="register-form__error-message">
          @error('name')
          {{ $message }}
          @enderror
        </p>
      </div>
      <div class="register-form__group">
        <label class="register-form__label" for="email">メールアドレス</label>
        <input class="register-form__input" type="mail" name="email" id="email" value="{{ old('email') }}">
        <p class="register-form__error-message">
          @error('email')
          {{ $message }}
          @enderror
        </p>
      </div>
      <div class="register-form__group">
        <label class="register-form__label" for="password">パスワード</label>
        <input class="register-form__input" type="password" name="password" id="password">
        <p class="register-form__error-message">
          @error('password')
            @if ($message !== 'パスワードと一致しません')
            <div class="register-form__error-message">{{ $message }}</div>
            @endif
          @enderror
        </p>
      </div>
      <div class="register-form__group">
        <label class="register-form__label" for="password_confirmation">確認用パスワード</label>
        <input class="register-form__input" type="password" name="password_confirmation" id="password_confirmation">
        <p>
        @php
        $passwordError = $errors->first('password');
        $passwordConfirmationError = $errors->first('password_confirmation');
        @endphp

        @if ($passwordConfirmationError)
        <div class="register-form__error-message">{{ $passwordConfirmationError }}</div>
        @elseif ($passwordError === 'パスワードと一致しません')
        <div class="register-form__error-message">{{ $passwordError }}</div>
        @endif
        </p>
      </div>
      <input class="register-form__btn" type="submit" value="登録する">
      <div class="login__link">
        <a class="login__button-submit" href="/login">ログインはこちら</a>
      </div>
    </form>
  </div>
</div>

</body>

</html>



