{{-- "Hesabım" sayfalarının ortak düzeni: sol menü + içerik --}}
@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/account.css') }}">
@endpush

@section('content')
    @include('shop.partials.breadcrumb', ['items' => [['Hesabım', route('account.dashboard')], [$__env->yieldContent('title'), null]]])

    @php
        $accountMenu = [
            ['account.dashboard', 'account.dashboard', '🏠', 'Hesap Özeti'],
            ['account.orders.index', 'account.orders.*', '📦', 'Siparişlerim'],
            ['account.returns.index', 'account.returns.*', '↩️', 'İade / Değişim Taleplerim'],
            ['account.favorites', 'account.favorites', '♡', 'Favorilerim'],
            ['account.reviews', 'account.reviews', '⭐', 'Yorumlarım'],
            ['account.addresses.index', 'account.addresses.*', '📍', 'Adreslerim'],
            ['account.profile', 'account.profile', '👤', 'Üyelik Bilgilerim'],
        ];
    @endphp

    <div class="account-layout">
        <aside class="account-sidebar">
            <div class="account-user">
                <div class="avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}{{ mb_substr(auth()->user()->surname, 0, 1) }}</div>
                <strong>{{ auth()->user()->full_name }}</strong>
                <span>{{ auth()->user()->email }}</span>
            </div>
            <nav class="account-menu">
                @foreach ($accountMenu as [$mRoute, $mPattern, $mIcon, $mLabel])
                    <a href="{{ route($mRoute) }}" class="{{ request()->routeIs($mPattern) ? 'active' : '' }}">{{ $mIcon }} {{ $mLabel }}</a>
                @endforeach
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">🚪 Çıkış Yap</button>
                </form>
            </nav>
        </aside>

        <section>
            @yield('account')
        </section>
    </div>
@endsection
