@extends('layouts.app')

@section('title', $title)

@section('content')
    @include('shop.partials.breadcrumb', ['items' => $breadcrumbs])

    <div class="page-title">
        <h1>{{ $title }}</h1>
        <span class="text-muted">({{ $books->total() }} ürün)</span>
    </div>
    @if ($subtitle)
        <p class="listing-subtitle">{{ $subtitle }}</p>
    @endif

    <div class="listing">
        {{-- Filtreler --}}
        <aside>
            <form method="GET" class="filters">
                @if ($searchTerm !== '')
                    <input type="hidden" name="q" value="{{ $searchTerm }}">
                @endif
                <input type="hidden" name="sirala" value="{{ $currentSort }}">
                <input type="hidden" name="goster" value="{{ $perPage }}">

                <div class="filter-block">
                    <h4>Kategoriler</h4>
                    <div class="filter-list">
                        @if ($activeCategory)
                            @foreach ($filterCategories as $cat)
                                <a href="{{ route('categories.show', $cat) }}" class="{{ $cat->id === $activeCategory->id ? 'active' : '' }}">{{ $cat->icon }} {{ $cat->name }}</a>
                            @endforeach
                        @else
                            @foreach ($filterCategories as $cat)
                                <label class="checkbox">
                                    <input type="checkbox" name="kategori[]" value="{{ $cat->id }}" @checked(in_array($cat->id, (array) request('kategori')))>
                                    {{ $cat->name }}
                                </label>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="filter-block">
                    <h4>Fiyat Aralığı</h4>
                    <div class="price-inputs">
                        <input type="number" name="min" value="{{ request('min') }}" class="form-control" placeholder="En az" min="0">
                        <span>-</span>
                        <input type="number" name="max" value="{{ request('max') }}" class="form-control" placeholder="En çok" min="0">
                    </div>
                </div>

                @if ($filterPublishers->count() > 1)
                    <div class="filter-block">
                        <h4>Yayınevi</h4>
                        <div class="filter-list">
                            @foreach ($filterPublishers as $publisher)
                                <label class="checkbox">
                                    <input type="checkbox" name="yayinevi[]" value="{{ $publisher->id }}" @checked(in_array($publisher->id, (array) request('yayinevi')))>
                                    {{ $publisher->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="filter-block">
                    <h4>Diğer</h4>
                    <div class="filter-list">
                        <label class="checkbox"><input type="checkbox" name="stokta" value="1" @checked(request('stokta'))> Sadece stoktakiler</label>
                        <label class="checkbox"><input type="checkbox" name="indirimli" value="1" @checked(request('indirimli'))> Sadece indirimliler</label>
                    </div>
                </div>

                <div class="filter-actions">
                    <button class="btn btn-primary btn-sm btn-block">Filtrele</button>
                    <a href="{{ url()->current() }}{{ $searchTerm !== '' ? '?q=' . urlencode($searchTerm) : '' }}" class="btn btn-outline btn-sm">Temizle</a>
                </div>
            </form>
        </aside>

        {{-- Sonuçlar --}}
        <section>
            <form method="GET" class="toolbar" id="sort-form">
                @foreach (request()->except(['sirala', 'goster', 'page']) as $key => $value)
                    @foreach ((array) $value as $v)
                        <input type="hidden" name="{{ is_array($value) ? $key . '[]' : $key }}" value="{{ $v }}">
                    @endforeach
                @endforeach

                <span class="text-muted">
                    {{ $books->firstItem() ?? 0 }}-{{ $books->lastItem() ?? 0 }} arası gösteriliyor, toplam {{ $books->total() }} ürün
                </span>
                <div class="flex">
                    <label for="sirala" class="text-muted">Sırala:</label>
                    <select name="sirala" id="sirala" onchange="this.form.submit()">
                        @foreach ($sorts as $key => $label)
                            <option value="{{ $key }}" @selected($currentSort === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <select name="goster" onchange="this.form.submit()" aria-label="Sayfa başına ürün">
                        @foreach ([12, 24, 48] as $n)
                            <option value="{{ $n }}" @selected($perPage === $n)>{{ $n }} ürün</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <div class="results">
                @if ($books->isEmpty())
                    <div class="empty-state">
                        <div class="icon">🔍</div>
                        <h3>Aradığınız kriterlere uygun ürün bulunamadı</h3>
                        <p>Filtreleri değiştirerek veya farklı bir kelimeyle tekrar arayabilirsiniz.</p>
                        <a href="{{ route('books.index') }}" class="btn btn-primary">Tüm Kitaplara Göz At</a>
                    </div>
                @else
                    <div class="shelf-row">
                        @foreach ($books as $book)
                            <x-book-card :book="$book" />
                        @endforeach
                    </div>
                    {{ $books->links() }}
                @endif
            </div>
        </section>
    </div>
@endsection
