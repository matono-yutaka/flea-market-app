<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>mock</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css')}}">
  <link rel="stylesheet" href="{{ asset('css/common.css')}}">
  <link rel="stylesheet" href="{{ asset('css/profile/mypage_trading_chat.css')}}">
</head>

@php
use Illuminate\Support\Str;
@endphp

<body>
<div class="app">
    <header class="header">
        <a href="/" class="img-content"><img src="{{ asset('storage/images/logo.svg') }}" alt=""></a>
    </header>
<div class="all-contents">
    <div class="left-contents">
        <p class="other-trading">その他の取引</p>
        @foreach($purchases as $purchaseItem)
            @if ($purchaseItem->id !== $purchase->id)
                <a class="trading-items" href="{{ url('/chat/' . $purchaseItem->id) }}">{{$purchaseItem->item->name}}</a>
            @endif
        @endforeach
    </div>
    <div class="right-contents">
        <div class="upper-contents">
            <div class="image-box">
                <img src="{{ isset($purchase->item->user->profile) && $purchase->item->user->profile->image ? asset('storage/images/national_flag/' . $purchase->item->user->profile->image) : asset('storage/images/20200501_noimage.jpg') }}" alt="" class="profile-image">
            <h1 class="user-name">「{{$purchase->item->user->name}}」さんとの取引画面</h1>
            </div>
            <a class="trade-complete-btn" href="#modal1">取引を完了する</a>
        </div>
        <div class="divider"></div>
        <div class="image-box">
            <img src="{{ Str::startsWith($purchase->item->image, 'http') ? $purchase->item->image : asset('storage/images/' . $purchase->item->image) }}" alt="{{ $purchase->item->name }}" class="item-image">
            <div class="image-text">
                <h2 class="item-name">{{$purchase->item->name}}</h2>
                <p class="price"><span class="price-logo">¥ </span>{{$purchase->item->price}}</p>
            </div>
        </div>
        <div class="divider"></div>
        <div class="chat-area">
            <div class="chat-persons">
                @foreach($messages as $message)
                @if($message->user_id !== auth()->id())
                <div class="chat-box1">
                    <div class="chat-person1">
                        <img src="{{ isset($purchase->item->user->profile) && $purchase->item->user->profile->image ? asset('storage/images/national_flag/' . $purchase->item->user->profile->image) : asset('storage/images/20200501_noimage.jpg') }}" alt="" class="chat-image">
                        <p class="chat-name">{{$purchase->item->user->name}}</p>
                    </div>
                    <div class="chat-comment-image">
                        <div class="chat-comment1">
                        {{$message->comment}}
                        </div>
                    @if ($message->image)
                        <img src="{{ asset('storage/' . $message->image) }}" class="chat-image">
                    @endif
                    </div>
                </div>
                @endif
                @if($message->user_id === auth()->id())
                <div class="chat-box2">
                    <div class="chat-person2">
                        <p class="chat-name">{{$purchase->user->name}}</p>
                        <img src="{{ isset($purchase->user->profile) && $purchase->user->profile->image ? asset('storage/images/national_flag/' . $purchase->user->profile->image) : asset('storage/images/20200501_noimage.jpg') }}" alt="" class="chat-image">
                    </div>
                    <div class="chat-comment2">

    {{-- 編集フォーム --}}
    <form class="chat-edit-form" action="{{ url('/chat/edit/' .$message->id) }}" method="POST" id="edit-form-{{ $message->id }}" >
        @csrf
        @if ($message->image)
        <img src="{{ asset('storage/' . $message->image) }}" class="chat-image2">
        @endif

        <input type="text" class="chat-comment2-input" name="comment" value="{{ old('comment', $message->comment) }}" placeholder="{{$message->comment}}">
    </form>

    {{-- ボタン並べる用の div --}}
    <div class="chat-btn-group">

        {{-- 編集ボタンは編集フォームを submit --}}
        <button class="chat-btn"
                type="submit"
                form="edit-form-{{ $message->id }}">
            編集
        </button>

        {{-- 削除フォーム --}}
        <form action="/chat/delete/{{$message->id}}" method="POST" onsubmit="return confirm('本当に削除しますか？')">
            @csrf
            @method('DELETE')
            <input class="chat-btn" type="submit" value="削除">
        </form>
    </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            <form  class="chat-form" action="{{ url('/chat/' . $purchase->id . '/message') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="chat-message">
                    <p class="chat-form__error-message">
                        @error('comment')
                        {{ $message }}
                        @enderror
                    </p>
                    <p class="image-form__error-message">
                        @error('image')
                        {{ $message }}
                        @enderror
                    </p>
                    <input type="text" id="chatInput" class="chat-form-input" value="{{old('comment')}}" placeholder="取引メッセージを記入してください"  name="comment">
                </div>
                <label class="file-label" for="imageInput">画像を追加</label>
                <input type="file" name="image" class="image-btn" id="imageInput">
                <button id="sendBtn" class="chat-form-button"><img src="/storage/images/紙飛行機アイコン.svg" alt="チャットアイコン" class="chat-form-icon"></button>
            </form>
        </div>
    </div>

    <div class="modal" id="modal1">
        <a href="#!" class="modal-overlay"></a>
        <div class="modal__inner">
            <div class="modal__content">
            <p class="modal-logo">取引が完了しました。</p>
            <div class="modal-divider"></div>
            <P class="star-text">今回の取引相手はどうでしたか？</P>
            <form class="modal-form" action="/trade/complete/store/{{$purchase->id}}#modal1" method="post">
                @csrf
                <div class="modal-star">
                    <button class="star-btn"><img src="/storage/images/シャープな星の無料アイコン.png" alt="" class="star"></button>
                    <button class="star-btn"><img src="/storage/images/シャープな星の無料アイコン.png" alt="" class="star"></button>
                    <button class="star-btn"><img src="/storage/images/シャープな星の無料アイコン.png" alt="" class="star"></button>
                    <button class="star-btn"><img src="/storage/images/シャープな星の無料アイコン.png" alt="" class="star"></button>
                    <button class="star-btn"><img src="/storage/images/シャープな星の無料アイコン.png" alt="" class="star"></button>
                    <input type="hidden" name="rating" id="rating-value">
                </div>
                <div class="modal-divider"></div>
                <div class="modal-btn-area">
                    <input class="modal-form__btn" type="submit" value="送信する">
                </div>
            </form>
            </div>
        </div>
    </div>
</div>

</div>
</body>

<script>
    const chatArea = document.querySelector('.chat-area');
    chatArea.scrollTop = chatArea.scrollHeight;

document.querySelectorAll('.star-btn').forEach((btn, index) => {
    btn.addEventListener('click', function (e) {
        e.preventDefault(); // ボタンの送信を止める
        document.getElementById('rating-value').value = index + 1;

        // 見た目を塗りつぶす（任意）
        document.querySelectorAll('.star').forEach((star, i) => {
            star.classList.toggle('active', i <= index);
        });
    });
});

// 入力のたびに保存
document.getElementById('chatInput').addEventListener('input', function () {
    localStorage.setItem('chat_draft', this.value);
});

document.addEventListener('DOMContentLoaded', function () {
    const saved = localStorage.getItem('chat_draft');
    if (saved !== null) {
        document.getElementById('chatInput').value = saved;
    }
});

document.getElementById('sendBtn').addEventListener('click', function () {
    localStorage.removeItem('chat_draft');
});
</script>

</html>