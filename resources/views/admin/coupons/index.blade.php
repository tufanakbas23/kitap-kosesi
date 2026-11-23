@extends('layouts.admin')

@section('title', 'Kuponlar')

@section('content')
    <div class="card">
        <div class="filter-bar">
            <span class="text-muted">Sepet sayfasında kullanılabilecek indirim kodları</span>
            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-sm" style="margin-left:auto">+ Yeni Kupon</a>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Kod</th><th>İndirim</th><th>Min. Sepet</th><th>Kullanım</th><th>Son Tarih</th><th>Durum</th><th class="text-right">İşlemler</th></tr></thead>
                <tbody>
                    @forelse ($coupons as $coupon)
                        @php $expired = $coupon->expires_at && $coupon->expires_at->endOfDay()->isPast(); @endphp
                        <tr>
                            <td><strong>{{ $coupon->code }}</strong></td>
                            <td>{{ $coupon->label }}</td>
                            <td>{{ $coupon->min_total > 0 ? number_format($coupon->min_total, 2, ',', '.') . ' TL' : '-' }}</td>
                            <td>{{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}</td>
                            <td>{{ $coupon->expires_at?->format('d.m.Y') ?? 'Süresiz' }}</td>
                            <td>
                                @if ($expired)
                                    <span class="badge badge-danger">Süresi Doldu</span>
                                @else
                                    <span class="badge {{ $coupon->is_active ? 'badge-success' : 'badge-secondary' }}">{{ $coupon->is_active ? 'Aktif' : 'Pasif' }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-outline btn-sm">Düzenle</a>
                                    <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" data-confirm="Kupon silinsin mi?">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Sil</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty-state">Henüz kupon yok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $coupons->links() }}
@endsection
