@extends('layouts.account')

@section('title', 'Sipariş Detayı')

@section('account')
    <div class="flex-between mb-2">
        <div>
            <h1 class="mb-0">Sipariş #{{ $order->order_number }}</h1>
            <span class="text-muted">{{ $order->created_at->translatedFormat('d F Y, H:i') }}</span>
        </div>
        <div class="flex">
            <span class="badge badge-{{ $order->status_color }}" style="font-size:13px">{{ $order->status_label }}</span>
            @if ($order->isCancellable())
                <form action="{{ route('account.orders.cancel', $order) }}" method="POST" data-confirm="Siparişinizi iptal etmek istediğinize emin misiniz?">
                    @csrf
                    <button class="btn btn-outline btn-sm">Siparişi İptal Et</button>
                </form>
            @endif
        </div>
    </div>

    {{-- Sipariş takibi --}}
    @if ($order->status !== 'iptal')
        @php
            $steps = ['beklemede' => 'Sipariş Alındı', 'hazirlaniyor' => 'Hazırlanıyor', 'kargoda' => 'Kargoda', 'teslim_edildi' => 'Teslim Edildi'];
            $reached = array_search($order->status, array_keys($steps));
        @endphp
        <div class="card mb-2">
            <div class="card-body">
                <div class="tracker">
                    @foreach (array_values($steps) as $i => $label)
                        <div class="tracker-step {{ $i <= $reached ? 'done' : '' }}">
                            <div class="dot">{{ $i <= $reached ? '✔' : $i + 1 }}</div>
                            {{ $label }}
                        </div>
                    @endforeach
                </div>
                @if ($order->tracking_number)
                    <p class="mb-0 text-center">🚚 <strong>{{ $order->cargo_company }}</strong> — Takip No: <strong>{{ $order->tracking_number }}</strong></p>
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-info">Bu demo sipariş iptal edildi.</div>
    @endif

    {{-- Ürünler --}}
    <div class="card mb-2">
        <div class="card-header">
            <h3>Ürünler</h3>
            @if ($order->isReturnable())
                <span class="text-small text-muted">Teslimden itibaren 14 gün içinde iade/değişim talebi oluşturabilirsiniz.</span>
            @endif
        </div>
        <div class="card-body">
            @foreach ($order->items as $item)
                <div class="order-item-row">
                    <x-book-cover :book="$item->book" />
                    <div>
                        @if ($item->book?->isVisible())
                            <a href="{{ route('books.show', $item->book) }}" class="fw-bold">{{ $item->title }}</a>
                        @else
                            <strong>{{ $item->title }}</strong>
                        @endif
                        <div class="text-muted text-small">{{ $item->author_name }}</div>
                        @foreach ($item->returnRequests as $rr)
                            <span class="badge badge-{{ $rr->status_color }}">{{ $rr->type_label }}: {{ $rr->status_label }}</span>
                        @endforeach
                    </div>
                    <span>{{ $item->quantity }} × @money($item->price)</span>
                    <strong>@money($item->total)</strong>
                    <div>
                        @if ($order->isReturnable() && ! $item->hasOpenRequest() && $item->remainingReturnQuantity() > 0)
                            <a href="{{ route('account.returns.create', $item) }}" class="btn btn-outline btn-sm">İade / Değişim</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-header"><h3>Teslimat Bilgileri</h3></div>
            <div class="card-body">
                <strong>{{ $order->shipping_name }}</strong><br>
                {{ $order->shipping_address }}<br>
                <span class="text-muted">📞 {{ $order->shipping_phone }}</span>
                @if ($order->note)
                    <hr><span class="text-muted">Not:</span> {{ $order->note }}
                @endif
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>Ödeme Bilgileri</h3></div>
            <div class="card-body">
                <div class="summary-row"><span>Ara Toplam</span><span>@money($order->subtotal)</span></div>
                @if ($order->discount > 0)
                    <div class="summary-row discount"><span>İndirim ({{ $order->coupon_code }})</span><span>-@money($order->discount)</span></div>
                @endif
                <div class="summary-row"><span>Kargo</span><span>{{ $order->shipping_fee > 0 ? number_format($order->shipping_fee, 2, ',', '.') . ' TL' : 'Ücretsiz' }}</span></div>
                <div class="summary-row total"><span>Toplam</span><span>@money($order->total)</span></div>
                <p class="text-small text-muted mb-0">💳 **** {{ $order->card_last4 }} — {{ $order->installment == 1 ? 'Tek Çekim' : $order->installment . ' Taksit' }}</p>
            </div>
        </div>
    </div>
@endsection
