@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/save.css')}}">
@endsection

@section('content')

<div class="profile-form">
  <h2 class="profile-form__heading">プロフィール設定</h2>
  <div class="profile-form__inner">
    <form class="profile-form__form" action="/profile/save" method="post" enctype="multipart/form-data">
      @csrf
      <div class="image-box">
          <img id="editImagePreview" src="{{session('temp_profile_image_path') ? asset('storage/' . session('temp_profile_image_path')) :  ($profile->image ? asset('storage/images/national_flag/' . $profile->image) : asset('storage/images/20200501_noimage.jpg')) }}" alt="" class="profile-image">
          @if (session('temp_profile_image_path'))
          {{-- hiddenでセッションのパスを送る --}}
            <input type="hidden" name="temp_profile_image_path" value="{{ session('temp_profile_image_path') }}">
          @endif
          <div class="image-text">
              <label for="editImageInput" class="file-label">画像を選択する</label>
              <input type="file" name="image" id="editImageInput" class="image-select__btn" value="{{old('image', $profile->image)}}">
              <p class="profile-form__error-message">
              @error('image')
              {{ $message }}
              @enderror
              </p>
          </div>
      </div>
      <div class="profile-form__group">
        <label class="profile-form__label" for="name">ユーザー名</label>
        <input class="profile-form__input" type="text" name="name" id="name" value="{{old('name', $profile->name ?? $user->name)}}">
        <p class="profile-form__error-message">
          @error('name')
          {{ $message }}
          @enderror
        </p>
      </div>
      <div class="profile-form__group">
        <label class="profile-form__label" for="post_code">郵便番号</label>
        <input class="profile-form__input" type="text" name="post_code" id="post_code" value="{{old('post_code', $profile->post_code)}}">
        <p class="profile-form__error-message">
          @error('post_code')
          {{ $message }}
          @enderror
        </p>
      </div>
      <div class="profile-form__group">
        <label class="profile-form__label" for="address">住所</label>
        <input class="profile-form__input" type="text" name="address" id="address" value="{{old('address', $profile->address)}}">
        <p class="profile-form__error-message">
          @error('address')
          {{ $message }}
          @enderror
        </p>
      </div>
        <div class="profile-form__group">
        <label class="profile-form__label" for="building">建物名</label>
        <input class="profile-form__input" type="text" name="building" id="building" value="{{old('building', $profile->building ?? '')}}">
        <p class="profile-form__error-message">
          @error('building')
          {{ $message }}
          @enderror
        </p>
        </div>
      <input class="profile-form__btn" type="submit" value="更新する">
    </form>
  </div>
</div>

<script>
    const previews = [
        { inputId: 'editImageInput', previewId: 'editImagePreview' },
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

    document.getElementById('editImageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('editImagePreview');
    if (file) {
        preview.src = URL.createObjectURL(file);
    }
    });

</script>

@endsection