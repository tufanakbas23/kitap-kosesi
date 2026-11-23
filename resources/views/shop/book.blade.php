@extends('layouts.app')

@section('title', $book->title . ' - ' . $book->author->name)

@section('content')
    @include('shop.partials.breadcrumb', ['items' => [
        [$book->category->name, route('categories.show', $book->category)],
        [$book->title, null],
    ]])

    <section class="product">
        {{-- Kapak --}}
        <div>
            <x-book-cover :book="$book" size="lg" />
        </div>

        {{-- Bilgiler --}}
        <div class="product-info">
            <h1>{{ $book->title }}</h1>
            <div class="product-meta">
                <span>Yazar: <a href="{{ route('authors.show', $book->author) }}">{{ $book->author->name }}</a></span>
                <span>Yayınevi: <a href="{{ route('publishers.show', $book->publisher) }}">{{ $book->publisher->name }}</a></span>
            </div>

            <div class="rating-line">
                <x-stars :rating="$avgRating" />
                <span>{{ $avgRating > 0 ? number_format($avgRating, 1, ',', '') : 'Henüz puan yok' }}</span>
                <a href="#yorumlar" class="text-primary" onclick="document.querySelector('[data-target=tab-reviews]').click()">({{ $reviews->count() }} değerlendirme)</a>
            </div>

            <p>{{ \Illuminate\Support\Str::limit($book->description, 260) }}</p>

            <table class="info-table">
                <tr><td>Kategori</td><td><a href="{{ route('categories.show', $book->category) }}">{{ $book->category->name }}</a></td></tr>
                <tr><td>Sayfa Sayısı</td><td>{{ $book->page_count ?? '-' }}</td></tr>
                <tr><td>Basım Yılı</td><td>{{ $book->publish_year ?? '-' }}</td></tr>
                <tr><td>Dil</td><td>{{ $book->language }}</td></tr>
                <tr><td>Kapak Türü</td><td>{{ $book->cover_type }}</td></tr>
                <tr><td>ISBN</td><td>{{ $book->isbn ?? '-' }}</td></tr>
            </table>
        </div>

        {{-- Satın alma kutusu --}}
        <div class="buy-box">
            @if ($book->discount_rate > 0)
                <div class="flex">
                    <span class="old-price">@money($book->list_price)</span>
                    <span class="discount-badge">%{{ $book->discount_rate }} İndirim</span>
                </div>
            @endif
            <div class="price">@money($book->price)</div>
            @if ($book->discount_rate > 0)
                <div class="saving">Kazancınız: @money($book->list_price - $book->price)</div>
            @endif

            <div class="stock-info">
                @if ($book->stock > 5)
                    <span class="text-success fw-bold">✔ Stokta var</span> — Tahmini kargoya teslim: 1-2 iş günü
                @elseif ($book->stock > 0)
                    <span class="text-danger fw-bold">⚠ Son {{ $book->stock }} ürün!</span>
                @else
                    <span class="text-danger fw-bold">✖ Stokta yok</span>
                @endif
            </div>

            @if ($book->in_stock)
                <form action="{{ route('cart.add', $book) }}" method="POST">
                    @csrf
                    <div class="qty-row">
                        <div class="qty">
                            <button type="button" data-step="-1" aria-label="Azalt">−</button>
                            <input type="number" name="quantity" value="1" min="1" max="{{ min($book->stock, config('shop.max_quantity')) }}" aria-label="Adet">
                            <button type="button" data-step="1" aria-label="Arttır">+</button>
                        </div>
                        <button class="btn btn-primary" style="flex:1">🛒 Sepete Ekle</button>
                    </div>
                </form>
            @endif

            @auth
                <form action="{{ route('favorites.toggle', $book) }}" method="POST">
                    @csrf
                    <button class="btn btn-outline btn-block">{{ $isFavorite ? '♥ Favorilerimden Çıkar' : '♡ Favorilerime Ekle' }}</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline btn-block">♡ Favorilere eklemek için giriş yapın</a>
            @endauth

            <div class="perks">
                <span>🚚 @money(config('shop.free_shipping_limit')) üzeri ücretsiz kargo</span>
                <span>↩️ 14 gün içinde kolay iade</span>
                <span>💳 Test kartıyla demo ödeme</span>
            </div>
        </div>
    </section>

    @php $showReviewErrors = $errors->hasAny(['rating', 'comment']); @endphp
    <section class="tabs" data-tabs id="yorumlar">
        <div class="tab-nav">
            <button @class(['active' => ! $showReviewErrors]) data-target="tab-desc" aria-controls="tab-desc" aria-pressed="{{ $showReviewErrors ? 'false' : 'true' }}">Kitap Açıklaması</button>
            <button data-target="tab-info" aria-controls="tab-info" aria-pressed="false">Ürün Bilgileri</button>
            <button @class(['active' => $showReviewErrors]) data-target="tab-reviews" aria-controls="tab-reviews" aria-pressed="{{ $showReviewErrors ? 'true' : 'false' }}">Yorumlar ({{ $reviews->count() }})</button>
        </div>

        <div @class(['tab-panel', 'active' => ! $showReviewErrors]) id="tab-desc">
            <p>{{ $book->description }}</p>
        </div>

        <div class="tab-panel" id="tab-info">
            <table class="info-table" style="max-width:520px">
                <tr><td>Kitap Adı</td><td>{{ $book->title }}</td></tr>
                <tr><td>Yazar</td><td>{{ $book->author->name }}</td></tr>
                <tr><td>Yayınevi</td><td>{{ $book->publisher->name }}</td></tr>
                <tr><td>ISBN</td><td>{{ $book->isbn ?? '-' }}</td></tr>
                <tr><td>Sayfa Sayısı</td><td>{{ $book->page_count ?? '-' }}</td></tr>
                <tr><td>Basım Yılı</td><td>{{ $book->publish_year ?? '-' }}</td></tr>
                <tr><td>Dil</td><td>{{ $book->language }}</td></tr>
                <tr><td>Kapak</td><td>{{ $book->cover_type }}</td></tr>
                <tr><td>Kağıt Cinsi</td><td>{{ $book->paper_type ?? '-' }}</td></tr>
                <tr><td>Boyut</td><td>{{ $book->dimensions ?? '-' }}</td></tr>
            </table>
        </div>

        <div @class(['tab-panel', 'active' => $showReviewErrors]) id="tab-reviews">
            <div class="grid-2">
                <div>
                    @forelse ($reviews as $review)
                        <div class="review">
                            <div class="review-head">
                                <strong>{{ $review->user->name }} {{ mb_substr($review->user->surname, 0, 1) }}.</strong>
                                <span class="text-muted">{{ $review->created_at->translatedFormat('d F Y') }}</span>
                            </div>
                            <x-stars :rating="$review->rating" />
                            <p class="mt-1 mb-0">{{ $review->comment }}</p>
                        </div>
                    @empty
                        <p class="text-muted">Bu kitap için henüz yorum yapılmamış. İlk yorumu siz yapın!</p>
                    @endforelse
                </div>

                <div>
                    @auth
                        @if ($canReview)
                            <form action="{{ route('reviews.store', $book) }}" method="POST" class="card card-body">
                                @csrf
                                <h3>Bu kitabı değerlendirin</h3>
                                <div class="form-group">
                                    <label class="form-label">Puanınız <span class="req">*</span></label>
                                    <div class="rating-input">
                                        @for ($i = 5; $i >= 1; $i--)
                                            <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" aria-label="{{ $i }} yıldız" @checked(old('rating') == $i)>
                                            <label for="star{{ $i }}" title="{{ $i }} yıldız">★</label>
                                        @endfor
                                    </div>
                                    @error('rating') <small class="form-error">{{ $message }}</small> @enderror
                                </div>
                                <x-field name="comment" label="Yorumunuz" type="textarea" placeholder="Kitap hakkındaki düşüncelerinizi paylaşın (en az 10 karakter)" required />
                                <button class="btn btn-primary">Yorumu Gönder</button>
                            </form>
                        @else
                            <div class="alert alert-info">Bu kitabı daha önce değerlendirdiniz. Teşekkürler!</div>
                        @endif
                    @else
                        <div class="alert alert-info">Yorum yapabilmek için <a href="{{ route('login') }}" class="fw-bold">&nbsp;giriş yapın</a>.</div>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    @include('shop.partials.shelf', ['title' => 'Yazarın Diğer Kitapları', 'books' => $authorBooks, 'link' => route('authors.show', $book->author)])
    @include('shop.partials.shelf', ['title' => 'Benzer Kitaplar', 'books' => $similarBooks, 'link' => route('categories.show', $book->category)])
@endsection
