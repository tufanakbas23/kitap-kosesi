{{-- Kitap kapağı: yüklenmiş görsel varsa onu, yoksa CSS ile üretilmiş kapağı gösterir --}}
@props(['book', 'size' => null])

<div {{ $attributes->class(['cover', 'cover-lg' => $size === 'lg']) }}>
    @if ($book?->cover_image)
        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" loading="lazy">
    @else
        <div class="cover-generated" style="background-color: {{ $book?->cover_color ?? '#888' }}">
            <div>
                <div class="c-title">{{ $book?->title ?? 'Kitap' }}</div>
                <div class="c-line"></div>
                <div class="c-author">{{ $book?->author?->name }}</div>
            </div>
            <div class="c-publisher">{{ $book?->publisher?->name }}</div>
        </div>
    @endif
</div>
