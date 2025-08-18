@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/product/sell.css')}}">
@endsection

@section('content')

<div class="sell-form">
  <h2 class="sell-form__heading">商品の出品</h2>
  <div class="sell-form__inner">
    <form class="sell-form__form" action="/exhibit" method="post" enctype="multipart/form-data">
      @csrf
      <P class="product-image_text">商品画像</p>
      <p class="sell-form__error-message">
          @error('image')
          {{ $message }}
          @enderror
        </p>

      <div class="image-box {{ session('temp_item_image_path') ? 'has-image' : '' }}" id="imageBox">
        <img id="imagePreview" src="{{session('temp_item_image_path') ? asset('storage/' . session('temp_item_image_path')) : ''}}" alt="" class="product-image">
      @if (session('temp_item_image_path'))
        {{-- hiddenでセッションのパスを送る --}}
            <input type="hidden" name="temp_item_image_path" value="{{ session('temp_item_image_path') }}">
      @endif
            <label for="imageInput" class="file-label">画像を選択する</label>
                <input type="file" name="image" id="imageInput" class="image-select__btn">
      </div>

      <p class=product-detail>商品の詳細</p>
      <div class="divider"></div>
      <p class="category">カテゴリー</p>
      <p class="sell-form__error-message">
          @error('categories')
          {{ $message }}
          @enderror
      </p>
      @foreach ($categories as $category)
      <input type="checkbox" name="categories[]" value="{{ $category->id }}" id="category{{ $category->id }}" class="checkbox" {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
      <label for="category{{ $category->id }}" class="category-label">{{ $category->name }}</label>
      @endforeach

      <p class="product-status">商品の状態</p>
      <p class="sell-form__error-message2">
          @error('condition')
          {{ $message }}
          @enderror
        </p>
        <select name="condition" class="select">
          <option value="" hidden>選択してください</option>
          <option value="良好" {{ old('condition') == '良好' ? 'selected' : '' }}>良好</option>
          <option value="目立った傷や汚れなし" {{ old('condition') == '目立った傷や汚れなし' ? 'selected' : '' }}>目立った傷や汚れなし</option>
          <option value="やや傷や汚れあり" {{ old('condition') == 'やや傷や汚れあり' ? 'selected' : '' }}>やや傷や汚れあり</option>
          <option value="状態が悪い" {{ old('condition') == '状態が悪い' ? 'selected' : '' }}>状態が悪い</option>
        </select>

        <p class=product-detail>商品名と説明</p>
        <div class="divider"></div>
      <div class="sell-form__group">
        <label class="sell-form__label" for="name">商品名</label>
        <input class="sell-form__input" type="text" name="name" id="name" value="{{ old('name') }}">
        <p class="sell-form__error-message">
          @error('name')
          {{ $message }}
          @enderror
        </p>
      </div>
      <div class="sell-form__group">
        <label class="sell-form__label" for="brand_name">ブランド名</label>
        <input class="sell-form__input" type="text" name="brand_name" id="brand_name" value="{{ old('brand_name') }}">
        <p class="sell-form__error-message">
          @error('brand_name')
          {{ $message }}
          @enderror
        </p>
      </div>
      <div class="sell-form__group">
        <label class="sell-form__label" for="description">商品の説明</label>
        <textarea cols="30" rows="9" name="description" class="product-description" id="description">{{ old('description') }}</textarea>
        <p class="sell-form__error-message">
          @error('description')
          {{ $message }}
          @enderror
        </p>
      </div>
        <div class="sell-form__group">
        <label class="sell-form__label" for="price">販売価格</label>
        <input class="sell-form__input" type="text" name="price" id="price" placeholder="¥" value="{{ old('price') }}">
        <p class="sell-form__error-message">
          @error('price')
          {{ $message }}
          @enderror
        </p>
        </div>
      <input class="sell-form__btn" type="submit" value="出品する">
    </form>
  </div>
</div>

<script>
    const previews = [
        { inputId: 'imageInput', previewId: 'imagePreview' },
    ];

    previews.forEach(pair => {
        const input = document.getElementById(pair.inputId);
        const preview = document.getElementById(pair.previewId);

        if (input && preview) {
            input.addEventListener('change', e => {
                const file = e.target.files[0];
                if (file) {
                    preview.src = URL.createObjectURL(file);
                    preview.style.display = 'block';
                }
            });
        }
    });

    document.getElementById('imageInput').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('imagePreview');
    const container = document.getElementById('imageBox');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            container.classList.add('has-image');
        };
        reader.readAsDataURL(file);
    }
});

</script>

@endsection