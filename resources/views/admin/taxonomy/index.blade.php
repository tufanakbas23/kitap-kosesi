{{-- Kategori / Yazar / Yayınevi listeleri için ORTAK görünüm --}}
@extends('layouts.admin')

@section('title', $labels['cogul'])

@section('content')
    <div class="card">
        <form method="GET" class="filter-bar">
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ $labels['tekil'] }} ara...">
            <button class="btn btn-dark btn-sm">Ara</button>
            <a href="{{ route("admin.$route.create") }}" class="btn btn-primary btn-sm" style="margin-left:auto">+ Yeni {{ $labels['tekil'] }}</a>
        </form>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ $labels['tekil'] }} Adı</th>
                        @isset($fields['is_active'])<th>Durum</th>@endisset
                        <th>Kitap Sayısı</th>
                        <th>Eklenme</th>
                        <th class="text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td class="text-muted">{{ $item->id }}</td>
                            <td><strong>{{ $item->icon ?? '' }} {{ $item->name }}</strong><br><span class="text-small text-muted">/{{ $item->slug }}</span></td>
                            @isset($fields['is_active'])
                                <td><span class="badge {{ $item->is_active ? 'badge-success' : 'badge-secondary' }}">{{ $item->is_active ? 'Aktif' : 'Pasif' }}</span></td>
                            @endisset
                            <td>{{ $item->books_count }}</td>
                            <td>{{ $item->created_at->format('d.m.Y') }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route("admin.$route.edit", $item->id) }}" class="btn btn-outline btn-sm">Düzenle</a>
                                    <form action="{{ route("admin.$route.destroy", $item->id) }}" method="POST" data-confirm="Bu kaydı silmek istediğinize emin misiniz?">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm" @disabled($item->books_count > 0) title="{{ $item->books_count > 0 ? 'Bağlı kitaplar varken silinemez' : 'Sil' }}">Sil</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-state">Kayıt bulunamadı.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $items->links() }}
@endsection
