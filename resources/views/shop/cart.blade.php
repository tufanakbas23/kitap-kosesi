@extends('layouts.app')

@section('title', 'Sepetim')

@section('content')
    <div class="page-title">
        <h1>Sepetim</h1>
        <span class="text-muted">({{ $count }} ürün)</span>
    </div>

    @if ($items->isEmpty())
        <div class="card">
            <div class="empty-state">
                <div class="icon">🛒</div>
                <h3>Sepetinizde ürün bulunmuyor</h3>
                <p>Binlerce kitap arasından size uygun olanı bulmak için alışverişe başlayın.</p>
                <a href="{{ route('home') }}" class="btn btn-primary">Alışverişe Başla</a>
            </div>
        </div>
    @else
        <div class="cart-layout">
            <div class="card">
                @foreach ($items as $line)
                    <div class="cart-item">
                        <a href="{{ route('books.show', $line->book) }}"><x-book-cover :book="$line->book" /></a>

                        <div>
                            <a href="{{ route('books.show', $line->book) }}" class="title">{{ $line->book->title }}</a>
                            <div class="text-muted text-small">{{ $line->book->author->name }} · {{ $line->book->publisher->name }}</div>
                            <div class="text-small mt-1">
                                <span class="fw-bold">@money($line->book->price)</span>
                                @if ($line->book->discount_rate > 0)
                                    <span class="old-price">@money($line->book->list_price)</span>
                                @endif
                            </div>
                            @if ($line->book->stock === 0)
                                <div class="text-danger text-small">Stok tükendi. Devam etmek için ürünü sepetten çıkarın.</div>
                            @elseif ($line->quantity > $line->book->stock)
                                <div class="text-danger text-small">En fazla {{ $line->book->stock }} adet kaldı. Sepetteki adedi azaltın.</div>
                            @elseif ($line->book->stock <= 5)
                                <div class="text-danger text-small">Son {{ $line->book->stock }} ürün</div>
                            @endif
                        </div>

                        @if ($line->book->stock > 0)
                            <form action="{{ route('cart.update', $line->book) }}" method="POST">
                                @csrf @method('PATCH')
                                <div class="qty" data-autosubmit>
                                    <button type="button" data-step="-1" aria-label="Azalt">−</button>
                                    <input type="number" name="quantity" value="{{ $line->quantity }}" min="1"
                                           max="{{ min($line->book->stock, config('shop.max_quantity')) }}" aria-label="Adet">
                                    <button type="button" data-step="1" aria-label="Artır">+</button>
                                </div>
                            </form>
                        @else
                            <span class="text-muted">{{ $line->quantity }} adet</span>
                        @endif

                        <div class="line-total">@money($line->line_total)</div>

                        <form action="{{ route('cart.remove', $line->book) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="btn-link danger" title="Sepetten çıkar">🗑 Sil</button>
                        </form>
                    </div>
                @endforeach
            </div>

            @include('shop.partials.summary', ['showCoupon' => true])
        </div>
    @endif
@endsection
