@extends('layouts.app')

@section('title', 'Giriş Yap')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/account.css') }}">
@endpush

@section('content')
    <div class="auth-wrap">
        <div class="auth-form">
            <div class="auth-tabs">
                <a href="{{ route('login') }}" class="active">Giriş Yap</a>
                <a href="{{ route('register') }}">Üye Ol</a>
            </div>

            <form action="{{ route('login') }}" method="POST" novalidate>
                @csrf
                <x-field name="email" label="E-posta Adresi" type="email" placeholder="ornek@mail.com" autocomplete="email" autofocus required />

                <div class="form-group">
                    <label for="password" class="form-label">Şifre <span class="req">*</span></label>
                    <div class="password-field">
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="current-password" required>
                        <button type="button" class="toggle-pass">Göster</button>
                    </div>
                    @error('password') <small class="form-error">{{ $message }}</small> @enderror
                </div>

                <div class="flex-between mb-2">
                    <label class="checkbox"><input type="checkbox" name="remember" value="1"> Beni hatırla</label>
                </div>

                <button class="btn btn-primary btn-lg btn-block">Giriş Yap</button>
            </form>

            <p class="text-center mt-2">Hesabınız yok mu? <a href="{{ route('register') }}" class="text-primary fw-bold">Hemen üye olun</a></p>

            <div class="demo-accounts">
                <strong>Demo hesaplar (sunum için):</strong><br>
                Müşteri: test@kitapkosesi.test / 123456<br>
                Yönetici: admin@kitapkosesi.test / admin123
            </div>
        </div>

        <div class="auth-side">
            <h2>KitapKöşesi'ne hoş geldiniz</h2>
            <ul>
                <li>📦 Siparişlerinizi kolayca takip edin</li>
                <li>♡ Favori kitaplarınızı kaydedin</li>
                <li>🏷 Üyelere özel kampanyalardan yararlanın</li>
                <li>↩️ İade ve değişim taleplerinizi online oluşturun</li>
            </ul>
        </div>
    </div>
@endsection
