@extends('layouts.admin')

@section('title', 'Gösterge Paneli')

@section('content')
    <div class="stat-cards">
        <div class="stat-card">
            <div><div class="label">Toplam Ciro</div><div class="value">@money($stats['revenue'])</div><div class="sub">Bugün: @money($stats['todayRevenue'])</div></div>
            <div class="ico">💰</div>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="stat-card">
            <div><div class="label">Siparişler</div><div class="value">{{ $stats['orders'] }}</div><div class="sub">{{ $stats['pendingOrders'] }} onay bekliyor</div></div>
            <div class="ico">📦</div>
        </a>
        <a href="{{ route('admin.users.index') }}" class="stat-card">
            <div><div class="label">Müşteriler</div><div class="value">{{ $stats['customers'] }}</div><div class="sub">Kayıtlı üye</div></div>
            <div class="ico">👥</div>
        </a>
        <a href="{{ route('admin.books.index') }}" class="stat-card">
            <div><div class="label">Kitaplar</div><div class="value">{{ $stats['books'] }}</div><div class="sub">{{ $stats['pendingReturns'] }} iade talebi · {{ $stats['pendingReviews'] }} yorum bekliyor</div></div>
            <div class="ico">📚</div>
        </a>
    </div>

    <div class="dash-grid">
        <div class="card">
            <div class="card-header"><h3>Son 7 Günlük Satışlar</h3></div>
            <div class="card-body">
                <div class="bar-chart">
                    @foreach ($days as $day)
                        <div class="bar-col">
                            <span class="bar-value">{{ number_format($day['total'], 0, ',', '.') }} ₺</span>
                            <div class="bar" style="height: {{ max(1, $day['total'] / $maxDay * 100) }}%"></div>
                            <span class="bar-label">{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>En Çok Satanlar</h3></div>
            <div class="card-body">
                @foreach ($topBooks as $i => $book)
                    <div class="book-mini mb-1">
                        <strong class="text-muted">{{ $i + 1 }}.</strong>
                        <x-book-cover :book="$book" class="thumb" />
                        <div style="flex:1">
                            <a href="{{ route('admin.books.edit', $book) }}" class="fw-bold">{{ $book->title }}</a><br>
                            <span class="text-muted text-small">{{ $book->author->name }}</span>
                        </div>
                        <span class="badge badge-primary">{{ $book->sales_count }} adet</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="dash-grid">
        <div class="card">
            <div class="card-header"><h3>Son Siparişler</h3><a href="{{ route('admin.orders.index') }}" class="text-primary">Tümü ›</a></div>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Sipariş</th><th>Müşteri</th><th>Tarih</th><th>Tutar</th><th>Durum</th></tr></thead>
                    <tbody>
                        @foreach ($recentOrders as $order)
                            <tr>
                                <td><a href="{{ route('admin.orders.show', $order) }}" class="fw-bold text-primary">{{ $order->order_number }}</a></td>
                                <td>{{ $order->user->full_name }}</td>
                                <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                <td>@money($order->total)</td>
                                <td><span class="badge badge-{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>⚠ Stoğu Azalanlar</h3></div>
            <div class="table-wrap">
                <table class="table">
                    <tbody>
                        @forelse ($lowStock as $book)
                            <tr>
                                <td><a href="{{ route('admin.books.edit', $book) }}">{{ $book->title }}</a></td>
                                <td class="text-right"><span class="badge {{ $book->stock == 0 ? 'badge-danger' : 'badge-warning' }}">{{ $book->stock }} adet</span></td>
                            </tr>
                        @empty
                            <tr><td class="text-muted">Stoğu azalan ürün yok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
