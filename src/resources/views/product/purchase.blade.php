@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/product/purchase.css')}}">
@endsection

@section('content')

<div class="all-contents">
<form id="paymentForm" class="purchase-form" action="{{ url('/checkout/' . $item->id) }}" method="post">
    @csrf
    <div class="left-contents">
        <div class="image-box">
            <img src="{{ Str::startsWith($item->image, 'http') ? $item->image : asset('storage/images/' . $item->image) }}" alt="{{ $item->name }}" class="item-image">
            <div class="image-text">
                <h2 class="item-name">{{$item->name}}</h2>
                <p class="price"><span class="price-logo">¥ </span>{{number_format($item->price)}}</p>
            </div>
        </div>
        <div class="divider"></div>
        <h3 class="payment">支払い方法</h3>
        <select class="select" name="select" id="paymentSelect">
            <option value="" hidden>選択してください</option>
            <option value="コンビニ払い">コンビニ払い</option>
            <option value="カード払い">カード払い</option>
        </select>
        <div class="divider"></div>
        <div class="address-contents">
            <h3 class="shipping-address">配送先</h3>
            <a class="change-address" href="{{ url('/purchase/address/'. $item->id) }} " name="address">変更する</a>
        </div>
        @if (!empty($shippingAddress->address))
        <p class="address">〒{{$shippingAddress->post_code}}<br>
        {{$shippingAddress->address}}　{{$shippingAddress->building}}</p>
        @else
        <p class="address">〒 {{$profile->post_code ?? '' }}<br>{{$profile->address ?? '未登録' }}　{{$profile->building ?? '' }}</p>
        @endif
        <div class="divider"></div>
    </div>
    <div class="right-contents">
        <div class="payment-confirmation">
            <div class="upper">
                <p class="item-price">商品代金</p>
                <p class="payment-amount"><span class="payment-logo">¥</span>{{number_format($item->price)}}</p>
            </div>
            <div class="under">
                <p class="payment2">支払い方法</p>
                <p class="payment-select" id="selectedPayment">選択してください</p>
            </div>
        </div>
        <input class="purchase-form__btn" type="submit" value="購入する" id="submitButton" disabled>
        <p id="warningText" class="warning-message" style="display: none;">
        🚫 支払い方法を選択してください
        </p>
    </div>
</form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('paymentSelect');
    const display = document.getElementById('selectedPayment');

    select.addEventListener('change', function () {
        // 選択された支払い方法を右側に反映
        if (select.value) {
            display.textContent = select.value;
        } else {
            display.textContent = '支払い方法を選択してください';
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('paymentSelect');
    const display = document.getElementById('selectedPayment');
    const form = document.getElementById('paymentForm');
    const itemId = "{{ $item->id }}";
    const submitBtn = document.getElementById('submitButton');
    const warningText = document.getElementById('warningText');

    const disableButton = () => {
        submitBtn.disabled = true;
        warningText.style.display = 'block';  // 表示する
    };

    const enableButton = () => {
        submitBtn.disabled = false;
        warningText.style.display = 'none';  // 非表示にする
    };
    // 初期状態（未選択なら無効）
    if (!select.value) {
        disableButton();
    }

    select.addEventListener('change', function () {
        const value = select.value;
        display.textContent = value ? value : '支払い方法を選択してください';
        if (value === 'カード払い') {
            form.action = `/checkout/${itemId}`;
            enableButton();
        } else if (value === 'コンビニ払い') {
            form.action = `/konbini/${itemId}`;
            enableButton();
        } else {
            form.action = '#';
            disableButton();
        }
    });
});

</script>
@endsection