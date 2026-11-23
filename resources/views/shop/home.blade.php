@extends('layouts.app')

@section('content')
    {{-- Kampanya slider --}}
    <section class="hero">
        <div class="hero-slide active" style="background: linear-gradient(120deg, #f0661a, #b23c0a)">
            <h2>Kitaplarda indirim fırsatları</h2>
            <p>Klasiklerden çok satanlara, indirimli kitapları keşfet.</p>
            <a href="{{ route('books.discounted') }}" class="btn btn-lg">İndirimleri Keşfet</a>
            <span class="big">📚</span>
        </div>
        <div class="hero-slide" inert style="background: linear-gradient(120deg, #2e4a7a, #142640)">
            <h2>Sepette %10 indirim</h2>
            <p>Sepette <strong>HOSGELDIN10</strong> kodunu kullan, indirimini anında kazan.</p>
            <a href="{{ route('books.index') }}" class="btn btn-lg">Kitaplara Göz At</a>
            <span class="big">🎁</span>
        </div>
        @if ($scienceFiction)
        <div class="hero-slide" inert style="background: linear-gradient(120deg, #3d6b4f, #1d3b29)">
            <h2>Bilim kurgu ve fantastik dünyalar</h2>
            <p>Dune, Vakıf, 1984 ve daha fazlası... Hayal gücünün sınırlarını zorla.</p>
            <a href="{{ route('categories.show', $scienceFiction) }}" class="btn btn-lg">Bilim Kurgu Kitapları</a>
            <span class="big">🚀</span>
        </div>
        @endif
        <div class="hero-dots">
            <button class="active" aria-label="1. kampanya" aria-pressed="true"></button>
            <button aria-label="2. kampanya" aria-pressed="false"></button>
            @if ($scienceFiction)<button aria-label="3. kampanya" aria-pressed="false"></button>@endif
        </div>
    </section>

    <section class="features">
        <div class="feature"><span class="ico">🚚</span><div><strong>Ücretsiz Kargo</strong><span>@money(config('shop.free_shipping_limit')) ve üzeri</span></div></div>
        <div class="feature"><span class="ico">💳</span><div><strong>Demo Ödeme</strong><span>Test kartıyla sipariş</span></div></div>
        <div class="feature"><span class="ico">↩️</span><div><strong>14 Gün İade</strong><span>Kolay iade ve değişim</span></div></div>
        <div class="feature"><span class="ico">💳</span><div><strong>Taksit İmkânı</strong><span>9 aya varan taksit</span></div></div>
    </section>

    @include('shop.partials.shelf', ['title' => 'Çok Satanlar', 'books' => $bestsellers, 'link' => route('books.bestsellers')])
    @include('shop.partials.shelf', ['title' => 'Editörün Seçtikleri', 'books' => $featured, 'link' => route('books.featured')])

    <section class="shelf">
        <div class="shelf-header"><h2>Kategoriler</h2></div>
        <div class="category-tiles">
            @foreach ($categories as $cat)
                <a href="{{ route('categories.show', $cat) }}" class="category-tile">
                    <span class="ico">{{ $cat->icon }}</span>
                    <strong>{{ $cat->name }}</strong>
                    <span>{{ $cat->books_count }} kitap</span>
                </a>
            @endforeach
        </div>
    </section>

    @include('shop.partials.shelf', ['title' => 'Kaçırılmayacak İndirimler', 'books' => $discounted, 'link' => route('books.discounted')])
    @include('shop.partials.shelf', ['title' => 'Yeni Çıkanlar', 'books' => $newReleases, 'link' => route('books.index', ['sirala' => 'yeni'])])

    <section class="shelf">
        <div class="shelf-header"><h2>Popüler Yayınevleri</h2></div>
        <div class="chips">
            @foreach ($publishers as $publisher)
                <a href="{{ route('publishers.show', $publisher) }}" class="chip">{{ $publisher->name }} <span class="text-muted">({{ $publisher->books_count }})</span></a>
            @endforeach
        </div>
    </section>
@endsection
