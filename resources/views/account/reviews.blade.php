@extends('layouts.account')

@section('title', 'Yorumlarım')

@section('account')
    <h1>Yorumlarım</h1>

    <div class="card">
        @forelse ($reviews as $review)
            <div class="card-body" style="border-bottom:1px solid var(--border)">
                <div class="book-mini">
                    <x-book-cover :book="$review->book" />
                    <div style="flex:1">
                        <div class="flex-between">
                            @if ($review->book->isVisible())
                                <a href="{{ route('books.show', $review->book) }}" class="fw-bold">{{ $review->book->title }}</a>
                            @else
                                <strong>{{ $review->book->title }}</strong>
                            @endif
                            <span class="badge {{ $review->is_approved ? 'badge-success' : 'badge-warning' }}">{{ $review->is_approved ? 'Yayında' : 'Onay Bekliyor' }}</span>
                        </div>
                        <x-stars :rating="$review->rating" />
                        <p class="mb-0 mt-1">{{ $review->comment }}</p>
                        <span class="text-small text-muted">{{ $review->created_at->translatedFormat('d F Y') }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="icon">⭐</div>
                <h3>Henüz yorum yapmadınız</h3>
                <p>Okuduğunuz kitapları değerlendirerek diğer okurlara yol gösterebilirsiniz.</p>
            </div>
        @endforelse
    </div>

    {{ $reviews->links() }}
@endsection
