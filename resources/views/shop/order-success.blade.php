@extends('layouts.app')

@section('title', 'Siparişiniz Alındı')

@section('content')
    <div class="steps">
        <div class="step done">✔ 1. Sepet</div>
        <div class="step done">✔ 2. Adres ve Ödeme</div>
        <div class="step current">3. Onay</div>
    </div>

    <div class="card success-box">
        <div class="check">✔</div>
        <h1>Siparişiniz başarıyla alındı!</h1>
        <p>Sipariş numaranız: <strong class="text-primary">{{ $order->order_number }}</strong></p>
        <p class="text-muted">Siparişinizin durumunu “Siparişlerim” sayfasından takip edebilirsiniz.</p>

        <div class="card" style="max-width:520px;margin:20px auto;text-align:left">
            <div class="card-body">
                @foreach ($order->items as $item)
                    <div class="summary-row"><span>{{ $item->title }} × {{ $item->quantity }}</span><span>@money($item->total)</span></div>
                @endforeach
                @if ($order->discount > 0)
                    <div class="summary-row discount"><span>İndirim ({{ $order->coupon_code }})</span><span>-@money($order->discount)</span></div>
                @endif
                <div class="summary-row"><span>Kargo</span><span>@money($order->shipping_fee)</span></div>
                <div class="summary-row total"><span>Ödenen Tutar</span><span>@money($order->total)</span></div>
                <hr>
                <div class="text-small text-muted">
                    <strong>Teslimat:</strong> {{ $order->shipping_name }} — {{ $order->shipping_address }}<br>
                    <strong>Ödeme:</strong> **** **** **** {{ $order->card_last4 }} ({{ $order->installment == 1 ? 'Tek Çekim' : $order->installment . ' Taksit' }})
                </div>
            </div>
        </div>

        <div class="flex" style="justify-content:center">
            <a href="{{ route('account.orders.show', $order) }}" class="btn btn-primary">Sipariş Detayı</a>
            <a href="{{ route('home') }}" class="btn btn-outline">Alışverişe Devam Et</a>
        </div>
    </div>
@endsection
