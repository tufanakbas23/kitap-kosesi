<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel') | {{ config('shop.name') }} Yönetim</title>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
@php
    $pendingOrders  = \App\Models\Order::where('status', 'beklemede')->count();
    $pendingReturns = \App\Models\ReturnRequest::where('status', 'beklemede')->count();
    $pendingReviews = \App\Models\Review::where('is_approved', false)->count();

    $adminMenu = [
        'Genel' => [
            ['admin.dashboard', 'admin.dashboard', '📊', 'Gösterge Paneli', null],
        ],
        'Katalog' => [
            ['admin.books.index', 'admin.books.*', '📚', 'Kitaplar', null],
            ['admin.categories.index', 'admin.categories.*', '🗂️', 'Kategoriler', null],
            ['admin.authors.index', 'admin.authors.*', '✍️', 'Yazarlar', null],
            ['admin.publishers.index', 'admin.publishers.*', '🏢', 'Yayınevleri', null],
        ],
        'Satış' => [
            ['admin.orders.index', 'admin.orders.*', '📦', 'Siparişler', $pendingOrders],
            ['admin.returns.index', 'admin.returns.*', '↩️', 'İade / Değişim', $pendingReturns],
            ['admin.coupons.index', 'admin.coupons.*', '🏷️', 'Kuponlar', null],
        ],
        'Müşteriler' => [
            ['admin.users.index', 'admin.users.*', '👥', 'Kullanıcılar', null],
            ['admin.reviews.index', 'admin.reviews.*', '⭐', 'Yorumlar', $pendingReviews],
        ],
    ];
@endphp

<div class="admin">
    <aside class="admin-sidebar">
        <a href="{{ route('admin.dashboard') }}" class="admin-logo">Kitap<span>Köşesi</span><small>YÖNETİM PANELİ</small></a>
        @foreach ($adminMenu as $group => $links)
            <div class="admin-menu-title">{{ $group }}</div>
            <nav class="admin-menu">
                @foreach ($links as [$mRoute, $mPattern, $mIcon, $mLabel, $mCount])
                    <a href="{{ route($mRoute) }}" class="{{ request()->routeIs($mPattern) ? 'active' : '' }}">
                        {{ $mIcon }} {{ $mLabel }}
                        @if ($mCount)
                            <span class="count">{{ $mCount }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>
        @endforeach
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <h1>@yield('title', 'Panel')</h1>
            <div class="flex">
                <a href="{{ route('home') }}" class="btn btn-outline btn-sm" target="_blank">🌐 Siteyi Görüntüle</a>
                <span class="text-muted">👤 {{ auth()->user()->full_name }}</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-dark btn-sm">Çıkış</button>
                </form>
            </div>
        </header>

        <main class="admin-content">
            @include('partials.alerts')
            @yield('content')
        </main>
    </div>
</div>

<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
