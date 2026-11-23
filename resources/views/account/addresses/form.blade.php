@extends('layouts.account')

@section('title', $address->exists ? 'Adresi Düzenle' : 'Yeni Adres')

@section('account')
    <div class="card">
        <div class="card-header"><h3>{{ $address->exists ? 'Adresi Düzenle' : 'Yeni Adres Ekle' }}</h3></div>
        <div class="card-body">
            <form action="{{ $address->exists ? route('account.addresses.update', $address) : route('account.addresses.store') }}" method="POST" novalidate>
                @csrf
                @if ($address->exists) @method('PUT') @endif
                <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">

                <div class="form-row">
                    <x-field name="title" label="Adres Başlığı" :value="$address->title" placeholder="Örn: Ev, İş" required />
                    <x-field name="full_name" label="Ad Soyad" :value="$address->full_name" required />
                </div>
                <div class="form-row">
                    <x-field name="phone" label="Telefon" type="tel" :value="$address->phone" placeholder="05XX XXX XX XX" required />
                    <x-field name="city" label="İl" type="select" :value="$address->city" placeholder="İl seçiniz" required
                             :options="array_combine(config('shop.cities'), config('shop.cities'))" />
                </div>
                <div class="form-row">
                    <x-field name="district" label="İlçe" :value="$address->district" required />
                    <x-field name="neighborhood" label="Mahalle" :value="$address->neighborhood" />
                </div>
                <x-field name="address_line" label="Açık Adres" type="textarea" :value="$address->address_line" placeholder="Cadde, sokak, bina no, daire no" rows="3" required />
                <div class="form-row">
                    <x-field name="postal_code" label="Posta Kodu" :value="$address->postal_code" maxlength="5" />
                </div>
                <label class="checkbox mb-2">
                    <input type="checkbox" name="is_default" value="1" @checked(old('is_default', $address->is_default))>
                    Varsayılan teslimat adresim olarak kaydet
                </label>

                <div class="form-actions">
                    <a href="{{ request('redirect_to') === 'checkout' ? route('checkout.index') : route('account.addresses.index') }}" class="btn btn-outline">Vazgeç</a>
                    <button class="btn btn-primary">Adresi Kaydet</button>
                </div>
            </form>
        </div>
    </div>
@endsection
