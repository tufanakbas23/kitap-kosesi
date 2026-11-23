@extends('layouts.admin')

@section('title', 'Siparişler')

@section('content')
    <div class="card">
        <div class="tabs-inline">
            <a href="{{ route('admin.orders.index') }}" class="{{ ! request('durum') ? 'active' : '' }}">Tümü <span class="n">({{ $counts->sum() }})</span></a>
            @foreach ($statuses as $key => $label)
                <a href="{{ route('admin.orders.index', ['durum' => $key]) }}" class="{{ request('durum') === $key ? 'active' : '' }}">
                    {{ $label }} <span class="n">({{ $counts[$key] ?? 0 }})</span>
                </a>
            @endforeach
        </div>
        <form method="GET" class="filter-bar">
            <input type="hidden" name="durum" value="{{ request('durum') }}">
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Sipariş no veya müşteri adı...">
            <button class="btn btn-dark btn-sm">Ara</button>
        </form>

        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Sipariş No</th><th>Müşteri</th><th>Tarih</th><th>Ürün</th><th>Tutar</th><th>Ödeme</th><th>Durum</th><th></th></tr></thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td><a href="{{ route('admin.orders.show', $order) }}" class="fw-bold text-primary">{{ $order->order_number }}</a></td>
                            <td>{{ $order->user->full_name }}<br><span class="text-small text-muted">{{ $order->shipping_city }}</span></td>
                            <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                            <td>{{ $order->items_count }} kalem</td>
                            <td><strong>@money($order->total)</strong></td>
                            <td class="text-small">**** {{ $order->card_last4 }}<br>{{ $order->installment == 1 ? 'Tek çekim' : $order->installment . ' taksit' }}</td>
                            <td><span class="badge badge-{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                            <td class="text-right"><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline btn-sm">Yönet</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty-state">Sipariş bulunamadı.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $orders->links() }}
@endsection
