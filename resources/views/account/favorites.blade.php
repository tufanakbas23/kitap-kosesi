@extends('layouts.account')

@section('title', 'Favorilerim')

@section('account')
    <h1>Favorilerim</h1>

    <div class="card card-body">
        @if ($books->isEmpty())
            <div class="empty-state">
                <div class="icon">♡</div>
                <h3>Favori listeniz boş</h3>
                <p>Beğendiğiniz kitapları kitap detay sayfasından favorilerinize ekleyebilirsiniz.</p>
                <a href="{{ route('books.index') }}" class="btn btn-primary">Kitaplara Göz At</a>
            </div>
        @else
            <div class="shelf-row">
                @foreach ($books as $book)
                    <div>
                        <x-book-card :book="$book" />
                        <form action="{{ route('favorites.toggle', $book) }}" method="POST" class="mt-1">
                            @csrf
                            <button class="btn-link danger text-small">✖ Favorilerden çıkar</button>
                        </form>
                    </div>
                @endforeach
            </div>
            {{ $books->links() }}
        @endif
    </div>
@endsection
