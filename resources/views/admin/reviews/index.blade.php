@extends('layouts.admin')

@section('title', 'Yorumlar')

@section('content')
    <div class="card">
        <div class="tabs-inline">
            <a href="{{ route('admin.reviews.index') }}" class="{{ ! request('durum') ? 'active' : '' }}">Tümü</a>
            <a href="{{ route('admin.reviews.index', ['durum' => 'bekleyen']) }}" class="{{ request('durum') === 'bekleyen' ? 'active' : '' }}">Onay Bekleyenler</a>
            <a href="{{ route('admin.reviews.index', ['durum' => 'onayli']) }}" class="{{ request('durum') === 'onayli' ? 'active' : '' }}">Yayındakiler</a>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Kitap</th><th>Kullanıcı</th><th>Puan</th><th style="width:35%">Yorum</th><th>Tarih</th><th>Durum</th><th class="text-right">İşlemler</th></tr></thead>
                <tbody>
                    @forelse ($reviews as $review)
                        <tr>
                            <td><a href="{{ route('books.show', $review->book) }}" target="_blank" class="fw-bold">{{ $review->book->title }}</a></td>
                            <td>{{ $review->user->full_name }}</td>
                            <td><x-stars :rating="$review->rating" /></td>
                            <td class="text-small">{{ $review->comment }}</td>
                            <td>{{ $review->created_at->format('d.m.Y') }}</td>
                            <td><span class="badge {{ $review->is_approved ? 'badge-success' : 'badge-warning' }}">{{ $review->is_approved ? 'Yayında' : 'Bekliyor' }}</span></td>
                            <td>
                                <div class="actions">
                                    <form action="{{ route('admin.reviews.toggle', $review) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm {{ $review->is_approved ? 'btn-outline' : 'btn-success' }}">{{ $review->is_approved ? 'Yayından Kaldır' : 'Onayla' }}</button>
                                    </form>
                                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" data-confirm="Yorum silinsin mi?">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Sil</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty-state">Yorum bulunamadı.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $reviews->links() }}
@endsection
