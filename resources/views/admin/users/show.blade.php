@extends('layouts.admin')

@section('title', $user->full_name)

@section('content')
    <div class="admin-form">
        <div>
            <div class="card mb-2">
                <div class="card-header"><h3>Son Siparişler</h3></div>
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Sipariş No</th><th>Tarih</th><th>Ürün</th><th>Tutar</th><th>Durum</th></tr></thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td><a href="{{ route('admin.orders.show', $order) }}" class="text-primary fw-bold">{{ $order->order_number }}</a></td>
                                    <td>{{ $order->created_at->format('d.m.Y') }}</td>
                                    <td>{{ $order->items_count }}</td>
                                    <td>@money($order->total)</td>
                                    <td><span class="badge badge-{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-muted">Sipariş yok.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3>Adresler</h3></div>
                <div class="card-body">
                    @forelse ($user->addresses as $address)
                        <p><strong>{{ $address->title }}</strong> @if ($address->is_default)<span class="badge badge-primary">Varsayılan</span>@endif<br>
                            <span class="text-muted">{{ $address->full_address }}</span></p>
                    @empty
                        <span class="text-muted">Kayıtlı adres yok.</span>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>Kullanıcı Bilgileri</h3></div>
            <div class="card-body">
                <dl class="detail-list mb-2">
                    <dt>E-posta</dt><dd>{{ $user->email }}</dd>
                    <dt>Telefon</dt><dd>{{ $user->phone ?? '-' }}</dd>
                    <dt>Doğum Tarihi</dt><dd>{{ $user->birth_date?->format('d.m.Y') ?? '-' }}</dd>
                    <dt>Kayıt Tarihi</dt><dd>{{ $user->created_at->format('d.m.Y H:i') }}</dd>
                    <dt>Rol</dt><dd>{{ $user->is_admin ? 'Yönetici' : 'Müşteri' }}</dd>
                    <dt>E-bülten</dt><dd>{{ $user->newsletter ? 'Abone' : 'Abone değil' }}</dd>
                    <dt>Durum</dt><dd><span class="badge {{ $user->is_active ? 'badge-success' : 'badge-danger' }}">{{ $user->is_active ? 'Aktif' : 'Pasif' }}</span></dd>
                </dl>
                @if ($user->id != auth()->id())
                    <form action="{{ route('admin.users.toggle', $user) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="btn btn-block {{ $user->is_active ? 'btn-danger' : 'btn-success' }}">{{ $user->is_active ? 'Hesabı Pasif Yap' : 'Hesabı Aktif Yap' }}</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
