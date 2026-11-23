@extends('layouts.app')

@section('title', 'Üye Ol')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/account.css') }}">
@endpush

@section('content')
    <div class="auth-wrap">
        <div class="auth-form">
            <div class="auth-tabs">
                <a href="{{ route('login') }}">Giriş Yap</a>
                <a href="{{ route('register') }}" class="active">Üye Ol</a>
            </div>

            <form action="{{ route('register') }}" method="POST" novalidate>
                @csrf
                <div class="form-row">
                    <x-field name="name" label="Ad" autocomplete="given-name" required />
                    <x-field name="surname" label="Soyad" autocomplete="family-name" required />
                </div>
                <x-field name="email" label="E-posta Adresi" type="email" placeholder="ornek@mail.com" autocomplete="email" required />
                <x-field name="phone" label="Cep Telefonu" type="tel" placeholder="05XX XXX XX XX" hint="İsteğe bağlı — kargo bilgilendirmesi için" />

                <div class="form-row">
                    <div class="form-group">
                        <label for="password" class="form-label">Şifre <span class="req">*</span></label>
                        <div class="password-field">
                            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" required>
                            <button type="button" class="toggle-pass">Göster</button>
                        </div>
                        <small class="form-hint">En az 6 karakter, harf ve rakam içermeli</small>
                        @error('password') <small class="form-error">{{ $message }}</small> @enderror
                    </div>
                    <x-field name="password_confirmation" label="Şifre Tekrarı" type="password" autocomplete="new-password" required />
                </div>

                <div class="form-group">
                    <label class="checkbox">
                        <input type="checkbox" name="terms" value="1" @checked(old('terms'))>
                        <span><a href="{{ route('help') }}#uyelik-kosullari" class="text-primary" target="_blank" rel="noopener">Demo üyelik koşullarını</a> ve <a href="{{ route('help') }}#veri-kullanimi" class="text-primary" target="_blank" rel="noopener">veri kullanımı açıklamasını</a> okudum.</span>
                    </label>
                    @error('terms') <small class="form-error">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label class="checkbox">
                        <input type="checkbox" id="e-bulten" name="newsletter" value="1" @checked(old('newsletter'))>
                        <span>Kampanya ve duyurulardan e-posta ile haberdar olmak istiyorum.</span>
                    </label>
                </div>

                <button class="btn btn-primary btn-lg btn-block">Üye Ol</button>
            </form>

            <p class="text-center mt-2">Zaten üye misiniz? <a href="{{ route('login') }}" class="text-primary fw-bold">Giriş yapın</a></p>
        </div>

        <div class="auth-side">
            <h2>Üyelere özel ayrıcalıklar</h2>
            <ul>
                <li>🎁 İlk siparişe özel %10 indirim (HOSGELDIN10)</li>
                <li>🚚 {{ number_format(config('shop.free_shipping_limit'), 0, ',', '.') }} TL üzeri ücretsiz kargo</li>
                <li>💳 9 aya varan taksit seçenekleri</li>
                <li>⭐ Okuduğunuz kitapları değerlendirin</li>
            </ul>
        </div>
    </div>
@endsection
