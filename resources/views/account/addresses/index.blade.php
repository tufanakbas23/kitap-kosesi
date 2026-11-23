@extends('layouts.account')

@section('title', 'Adreslerim')

@section('account')
    <div class="flex-between mb-2">
        <h1 class="mb-0">Adreslerim</h1>
        <a href="{{ route('account.addresses.create') }}" class="btn btn-primary">+ Yeni Adres Ekle</a>
    </div>

    @if ($addresses->isEmpty())
        <div class="card empty-state">
            <div class="icon">📍</div>
            <h3>Kayıtlı adresiniz bulunmuyor</h3>
            <p>Siparişlerinizi hızlıca tamamlamak için bir adres ekleyin.</p>
        </div>
    @else
        <div class="address-cards">
            @foreach ($addresses as $address)
                <div class="address-card {{ $address->is_default ? 'default' : '' }}">
                    <div class="head">
                        <strong>{{ $address->title }}</strong>
                        @if ($address->is_default)
                            <span class="badge badge-primary">Varsayılan</span>
                        @endif
                    </div>
                    <span>{{ $address->full_name }}</span>
                    <span class="text-muted">{{ $address->full_address }}</span>
                    <span class="text-muted">📞 {{ $address->phone }}</span>
                    <div class="foot">
                        <a href="{{ route('account.addresses.edit', $address) }}" class="btn-link">Düzenle</a>
                        <form action="{{ route('account.addresses.destroy', $address) }}" method="POST" data-confirm="Bu adresi silmek istediğinize emin misiniz?">
                            @csrf @method('DELETE')
                            <button class="btn-link danger">Sil</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
