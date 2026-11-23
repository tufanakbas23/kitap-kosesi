@extends('layouts.admin')

@section('title', $book->exists ? 'Kitabı Düzenle' : 'Yeni Kitap')

@section('content')
    <form action="{{ $book->exists ? route('admin.books.update', $book) : route('admin.books.store') }}" method="POST" enctype="multipart/form-data" class="admin-form" novalidate>
        @csrf
        @if ($book->exists) @method('PUT') @endif

        <div>
            <div class="card mb-2">
                <div class="card-header"><h3>Temel Bilgiler</h3></div>
                <div class="card-body">
                    <x-field name="title" label="Kitap Adı" :value="$book->title" required />
                    <div class="form-row-3">
                        <x-field name="author_id" label="Yazar" type="select" :value="$book->author_id" :options="$authors->pluck('name', 'id')->all()" required />
                        <x-field name="publisher_id" label="Yayınevi" type="select" :value="$book->publisher_id" :options="$publishers->pluck('name', 'id')->all()" required />
                        <x-field name="category_id" label="Kategori" type="select" :value="$book->category_id" :options="$categories->pluck('name', 'id')->all()" required />
                    </div>
                    <x-field name="description" label="Açıklama" type="textarea" :value="$book->description" rows="6" />
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-header"><h3>Fiyat ve Stok</h3></div>
                <div class="card-body form-row-3">
                    <x-field name="price" label="Satış Fiyatı (TL)" type="number" step="0.01" min="0" :value="$book->price" required />
                    <x-field name="list_price" label="Liste Fiyatı (TL)" type="number" step="0.01" min="0" :value="$book->list_price" hint="İndirim göstermek için satış fiyatından yüksek girin" />
                    <x-field name="stock" label="Stok Adedi" type="number" min="0" :value="$book->stock" required />
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3>Ürün Bilgileri</h3></div>
                <div class="card-body">
                    <div class="form-row-3">
                        <x-field name="isbn" label="ISBN" :value="$book->isbn" />
                        <x-field name="page_count" label="Sayfa Sayısı" type="number" min="1" :value="$book->page_count" />
                        <x-field name="publish_year" label="Basım Yılı" type="number" :value="$book->publish_year" />
                    </div>
                    <div class="form-row-3">
                        <x-field name="language" label="Dil" :value="$book->language" required />
                        <x-field name="cover_type" label="Kapak Türü" type="select" :value="$book->cover_type" required
                                 :options="['Karton Kapak' => 'Karton Kapak', 'Ciltli' => 'Ciltli', 'Spiralli' => 'Spiralli']" />
                        <x-field name="paper_type" label="Kağıt Cinsi" :value="$book->paper_type" />
                    </div>
                    <x-field name="dimensions" label="Boyut" :value="$book->dimensions" placeholder="13,5 x 21 cm" />
                </div>
            </div>
        </div>

        <div>
            <div class="card mb-2">
                <div class="card-header"><h3>Yayın Durumu</h3></div>
                <div class="card-body">
                    <label class="checkbox mb-1"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $book->is_active))> Sitede yayında</label>
                    <label class="checkbox"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $book->is_featured))> Ana sayfada öne çıkar</label>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-header"><h3>Kapak</h3></div>
                <div class="card-body">
                    @if ($book->exists)
                        <x-book-cover :book="$book" class="cover-preview" />
                    @endif
                    <x-field name="cover_image" label="Kapak Görseli" type="file" accept="image/*" hint="JPG/PNG, en fazla 2 MB. Yüklenmezse renkli kapak otomatik oluşturulur." />
                    @if ($book->cover_image)
                        <label class="checkbox mb-2"><input type="checkbox" name="remove_cover" value="1"> Mevcut görseli kaldır</label>
                    @endif
                    <div class="form-group">
                        <label class="form-label">Otomatik Kapak Rengi</label>
                        <div class="color-input">
                            <input type="color" value="{{ old('cover_color', $book->cover_color) }}">
                            <input type="text" name="cover_color" value="{{ old('cover_color', $book->cover_color) }}" class="form-control" maxlength="7">
                        </div>
                        @error('cover_color') <small class="form-error">{{ $message }}</small> @enderror
                    </div>
                </div>
            </div>

            <div class="card card-body">
                <button class="btn btn-primary btn-block btn-lg">{{ $book->exists ? 'Değişiklikleri Kaydet' : 'Kitabı Ekle' }}</button>
                <a href="{{ route('admin.books.index') }}" class="btn btn-outline btn-block mt-1">Vazgeç</a>
            </div>
        </div>
    </form>
@endsection
