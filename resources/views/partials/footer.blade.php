<section class="newsletter">
    <div class="container">
        <div>
            <h3>E-bülten tercihi</h3>
            <span>Yeni çıkan kitaplar ve kampanyalar için üyelik tercihinizi ayarlayın.</span>
        </div>
        <a href="{{ auth()->check() ? route('account.profile') . '#e-bulten' : route('register') . '#e-bulten' }}" class="btn btn-dark">E-bülten tercihini ayarla</a>
    </div>
</section>

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <a href="{{ route('home') }}" class="logo" style="color:#fff">Kitap<span>Köşesi</span></a>
                <p class="mt-1">Kitap kataloğu ve demo alışveriş.</p>
            </div>
            <div>
                <h4>Kategoriler</h4>
                <ul>
                    @foreach ($navCategories->take(6) as $cat)
                        <li><a href="{{ route('categories.show', $cat) }}">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4>Hesabım</h4>
                <ul>
                    <li><a href="{{ route('account.dashboard') }}">Hesabım</a></li>
                    <li><a href="{{ route('account.orders.index') }}">Siparişlerim</a></li>
                    <li><a href="{{ route('account.returns.index') }}">İade ve Değişim</a></li>
                    <li><a href="{{ route('account.favorites') }}">Favorilerim</a></li>
                    <li><a href="{{ route('cart.index') }}">Sepetim</a></li>
                </ul>
            </div>
            <div>
                <h4>Yardım</h4>
                <ul>
                    <li><a href="{{ route('help') }}#sikca-sorulan-sorular">Sıkça Sorulan Sorular</a></li>
                    <li><a href="{{ route('help') }}#kargo-ve-teslimat">Kargo ve Teslimat</a></li>
                    <li><a href="{{ route('help') }}#iade-kosullari">İade Koşulları</a></li>
                    <li><a href="{{ route('help') }}#mesafeli-satis">Demo Satış Koşulları</a></li>
                    <li><a href="{{ route('help') }}#veri-kullanimi">Demo Veri Kullanımı</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© {{ date('Y') }} {{ config('shop.name') }} — Yazılım Geçerleme ve Sınama dersi için hazırlanmış demo projedir.</span>
            <div class="payment-icons"><span>VISA</span><span>MasterCard</span><span>TROY</span></div>
        </div>
    </div>
</footer>
