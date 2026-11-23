@props(['book'])

<div class="book-card">
    @if ($book->discount_rate > 0)
        <span class="discount-badge">%{{ $book->discount_rate }}</span>
    @endif

    <a href="{{ route('books.show', $book) }}" class="cover-link">
        <x-book-cover :book="$book" />
    </a>

    <a href="{{ route('books.show', $book) }}" class="title" title="{{ $book->title }}">{{ $book->title }}</a>
    <a href="{{ route('authors.show', $book->author) }}" class="author">{{ $book->author->name }}</a>
    @if ($book->relationLoaded('publisher'))
        <a href="{{ route('publishers.show', $book->publisher) }}" class="publisher">{{ $book->publisher->name }}</a>
    @endif

    <div class="price-row">
        <span class="price">@money($book->price)</span>
        @if ($book->discount_rate > 0)
            <span class="old-price">@money($book->list_price)</span>
        @endif
    </div>

    @if ($book->in_stock)
        <form action="{{ route('cart.add', $book) }}" method="POST" class="add-form">
            @csrf
            <button class="btn btn-outline-primary btn-sm btn-block">Sepete Ekle</button>
        </form>
    @else
        <div class="out-of-stock">Stokta yok</div>
    @endif
</div>
