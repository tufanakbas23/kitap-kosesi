{{-- Yatay kaydırılabilir kitap rafı: $title, $books, $link --}}
@if ($books->isNotEmpty())
    <section class="shelf">
        <div class="shelf-header">
            <h2>{{ $title }}</h2>
            @isset($link)
                <a href="{{ $link }}">Tümünü Gör ›</a>
            @endisset
        </div>
        <div class="shelf-scroll">
            @foreach ($books as $book)
                <x-book-card :book="$book" />
            @endforeach
        </div>
    </section>
@endif
