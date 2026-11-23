@extends('layouts.admin')

@section('title', 'Kitaplar')

@section('content')
    <div class="card">
        <form method="GET" class="filter-bar">
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Kitap adı, yazar veya ISBN...">
            <select name="kategori" class="form-control">
                <option value="">Tüm kategoriler</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('kategori') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="stok" class="form-control">
                <option value="">Tüm stok durumları</option>
                <option value="az" @selected(request('stok') === 'az')>Azalan (1-5)</option>
                <option value="tukendi" @selected(request('stok') === 'tukendi')>Tükenen</option>
            </select>
            <button class="btn btn-dark btn-sm">Filtrele</button>
            @if (request()->hasAny(['q', 'kategori', 'stok']))
                <a href="{{ route('admin.books.index') }}" class="btn-link">Temizle</a>
            @endif
            <a href="{{ route('admin.books.create') }}" class="btn btn-primary btn-sm" style="margin-left:auto">+ Yeni Kitap</a>
        </form>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th></th><th>Kitap</th><th>Kategori</th><th>Fiyat</th><th>Stok</th><th>Satış</th><th>Durum</th><th class="text-right">İşlemler</th></tr>
                </thead>
                <tbody>
                    @forelse ($books as $book)
                        <tr>
                            <td><x-book-cover :book="$book" class="thumb" /></td>
                            <td>
                                <strong>{{ $book->title }}</strong>
                                @if ($book->is_featured) <span class="badge badge-primary">Öne Çıkan</span> @endif
                                <br><span class="text-small text-muted">{{ $book->author->name }} · {{ $book->publisher->name }}</span>
                            </td>
                            <td>{{ $book->category->name }}</td>
                            <td>
                                @money($book->price)
                                @if ($book->discount_rate > 0)
                                    <br><span class="old-price">@money($book->list_price)</span> <span class="badge badge-success">%{{ $book->discount_rate }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $book->stock == 0 ? 'badge-danger' : ($book->stock <= 5 ? 'badge-warning' : 'badge-secondary') }}">{{ $book->stock }}</span>
                            </td>
                            <td>{{ $book->sales_count }}</td>
                            <td><span class="badge {{ $book->is_active ? 'badge-success' : 'badge-secondary' }}">{{ $book->is_active ? 'Yayında' : 'Pasif' }}</span></td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('books.show', $book) }}" target="_blank" class="btn btn-outline btn-sm" title="Sitede gör">👁</a>
                                    <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-outline btn-sm">Düzenle</a>
                                    <form action="{{ route('admin.books.destroy', $book) }}" method="POST" data-confirm="“{{ $book->title }}” silinsin mi?">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Sil</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty-state">Kitap bulunamadı.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $books->links() }}
@endsection
