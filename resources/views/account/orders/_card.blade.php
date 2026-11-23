{{-- Sipariş özet kartı (Siparişlerim listesi ve hesap özetinde ortak) --}}
<div class="order-card" style="margin: 0; border-radius: 0; border-width: 0 0 1px">
    <div class="order-card-head">
        <div><span>Sipariş No</span><strong>{{ $order->order_number }}</strong></div>
        <div><span>Tarih</span><strong>{{ $order->created_at->translatedFormat('d F Y') }}</strong></div>
        <div><span>Tutar</span><strong>@money($order->total)</strong></div>
        <div><span>Durum</span><span class="badge badge-{{ $order->status_color }}">{{ $order->status_label }}</span></div>
        <a href="{{ route('account.orders.show', $order) }}" class="btn btn-outline btn-sm">Detay</a>
    </div>
    <div class="order-card-body">
        @foreach ($order->items->take(6) as $item)
            @if ($item->book?->isVisible())
                <a href="{{ route('books.show', $item->book) }}" title="{{ $item->title }}">
                    <x-book-cover :book="$item->book" />
                </a>
            @else
                <x-book-cover :book="$item->book" />
            @endif
        @endforeach
        <span class="text-muted text-small">{{ $order->items->sum('quantity') }} ürün</span>
    </div>
</div>
