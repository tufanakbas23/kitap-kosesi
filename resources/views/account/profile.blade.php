@extends('layouts.account')

@section('title', 'Üyelik Bilgilerim')

@section('account')
    <div class="card mb-2">
        <div class="card-header"><h3>Üyelik Bilgilerim</h3></div>
        <div class="card-body">
            <form action="{{ route('account.profile.update') }}" method="POST" novalidate>
                @csrf @method('PUT')
                <div class="form-row">
                    <x-field name="name" label="Ad" :value="$user->name" required />
                    <x-field name="surname" label="Soyad" :value="$user->surname" required />
                </div>
                <div class="form-row">
                    <x-field name="email" label="E-posta" type="email" :value="$user->email" required />
                    <x-field name="phone" label="Cep Telefonu" type="tel" :value="$user->phone" placeholder="05XX XXX XX XX" />
                </div>
                <div class="form-row">
                    <x-field name="birth_date" label="Doğum Tarihi" type="date" :value="$user->birth_date?->format('Y-m-d')" />
                    <x-field name="gender" label="Cinsiyet" type="select" :value="$user->gender" required
                             :options="['kadin' => 'Kadın', 'erkek' => 'Erkek', 'belirtilmemis' => 'Belirtmek istemiyorum']" />
                </div>
                <label class="checkbox mb-2">
                    <input type="checkbox" id="e-bulten" name="newsletter" value="1" @checked(old('newsletter', $user->newsletter))>
                    Kampanya e-postaları almak istiyorum
                </label>
                <div class="form-actions"><button class="btn btn-primary">Bilgilerimi Güncelle</button></div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>Şifre Değiştir</h3></div>
        <div class="card-body">
            <form action="{{ route('account.password.update') }}" method="POST" novalidate>
                @csrf @method('PUT')
                <x-field name="current_password" label="Mevcut Şifre" type="password" autocomplete="current-password" required />
                <div class="form-row">
                    <x-field name="password" label="Yeni Şifre" type="password" autocomplete="new-password" hint="En az 6 karakter, harf ve rakam içermeli" required />
                    <x-field name="password_confirmation" label="Yeni Şifre (Tekrar)" type="password" autocomplete="new-password" required />
                </div>
                <div class="form-actions"><button class="btn btn-dark">Şifremi Değiştir</button></div>
            </form>
        </div>
    </div>
@endsection
