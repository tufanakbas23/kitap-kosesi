<div class="topbar">
    <div class="container">
        <span>📦 {{ number_format(config('shop.free_shipping_limit'), 0, ',', '.') }} TL ve üzeri alışverişlerde kargo bedava!</span>
        <div class="topbar-links">
            <a href="{{ route('books.bestsellers') }}">Çok Satanlar</a>
            <a href="{{ route('books.discounted') }}">Kampanyalar</a>
            @auth
                <a href="{{ route('account.orders.index') }}">Sipariş Takibi</a>
            @endauth
            <a href="{{ route('help') }}">Yardım</a>
        </div>
    </div>
</div>

<header class="site-header">
    <div class="container">
        <a href="{{ route('home') }}" class="logo">Kitap<span>Köşesi</span></a>

        <form action="{{ route('books.index') }}" method="GET" class="search-box" role="search">
            <input type="search" name="q" value="{{ is_string(request('q')) ? request('q') : '' }}" placeholder="Kitap adı, yazar, yayınevi veya ISBN ile ara..." aria-label="Arama">
            <button type="submit">Ara</button>
        </form>

        <div class="header-actions">
            @auth
                <a href="{{ route('account.favorites') }}" class="header-action" title="Favorilerim">
                    <span class="ico">♡</span>
                </a>

                <div class="user-menu">
                    <button type="button" class="header-action user-menu-toggle">
                        <span class="ico">👤</span>
                        <span><small>Hesabım</small>{{ auth()->user()->name }} ▾</span>
                    </button>
                    <div class="user-dropdown">
                        @if (auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}">⚙️ Yönetim Paneli</a>
                            <div class="divider"></div>
                        @endif
                        <a href="{{ route('account.dashboard') }}">Hesabım</a>
                        <a href="{{ route('account.orders.index') }}">Siparişlerim</a>
                        <a href="{{ route('account.returns.index') }}">İade / Değişim</a>
                        <a href="{{ route('account.favorites') }}">Favorilerim</a>
                        <a href="{{ route('account.addresses.index') }}">Adreslerim</a>
                        <a href="{{ route('account.profile') }}">Üyelik Bilgilerim</a>
                        <div class="divider"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit">Çıkış Yap</button>
                        </form>
                    </div>
                </div>
            @else
                <div class="auth-buttons">
                    <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Giriş Yap</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Üye Ol</a>
                </div>
            @endauth

            <a href="{{ route('cart.index') }}" class="header-action" title="Sepetim">
                <span class="ico">🛒</span>
                @if ($cartCount > 0)
                    <span class="cart-count">{{ $cartCount }}</span>
                @endif
                <span><small>Sepetim</small>{{ $cartCount }} ürün</span>
            </a>
        </div>
    </div>
</header>
