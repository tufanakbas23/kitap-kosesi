@extends('layouts.account')

@section('title', 'Hesap Özeti')

@section('account')
    <h1>Merhaba, {{ $user->name }} 👋</h1>
    <p class="text-muted">Hesabınızla ilgili tüm işlemleri buradan yönetebilirsiniz.</p>

    <div class="stat-boxes">
        <a href="{{ route('account.orders.index') }}" class="stat-box"><span class="ico">📦</span><div><strong>{{ $orderCount }}</strong><span>Sipariş</span></div></a>
        <a href="{{ route('account.favorites') }}" class="stat-box"><span class="ico">♡</span><div><strong>{{ $favoriteCount }}</strong><span>Favori</span></div></a>
        <a href="{{ route('account.addresses.index') }}" class="stat-box"><span class="ico">📍</span><div><strong>{{ $addressCount }}</strong><span>Adres</span></div></a>
        <a href="{{ route('account.returns.index') }}" class="stat-box"><span class="ico">↩️</span><div><strong>{{ $returnCount }}</strong><span>İade/Değişim</span></div></a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Son Siparişlerim</h3>
            <a href="{{ route('account.orders.index') }}" class="text-primary">Tümünü Gör ›</a>
        </div>
        @forelse ($recentOrders as $order)
            @include('account.orders._card', ['order' => $order])
        @empty
            <div class="empty-state">
                <div class="icon">📦</div>
                <h3>Henüz siparişiniz yok</h3>
                <a href="{{ route('home') }}" class="btn btn-primary">Alışverişe Başla</a>
            </div>
        @endforelse
    </div>
@endsection
