{{-- Sipariş özeti kutusu (sepet ve ödeme sayfalarında ortak) --}}
<aside class="card summary">
    <div class="card-header"><h3>Sipariş Özeti</h3></div>
    <div class="card-body">
        @if ($free_ship_remain > 0)
            @php $percent = min(100, 100 - ($free_ship_remain / config('shop.free_shipping_limit') * 100)); @endphp
            <div class="ship-progress">
                Ücretsiz kargo için <strong>@money($free_ship_remain)</strong> daha ürün ekleyin.
                <div class="bar"><span style="width: {{ $percent }}%"></span></div>
            </div>
        @else
            <div class="ship-progress">🎉 Kargo bedava!</div>
        @endif

        <div class="summary-row"><span>Ürünler ({{ $count }})</span><span>@money($subtotal)</span></div>
        <div class="summary-row">
            <span>Kargo</span>
            <span>{!! $shipping > 0 ? number_format($shipping, 2, ',', '.') . ' TL' : '<span class="text-success">Ücretsiz</span>' !!}</span>
        </div>
        @if ($discount > 0)
            <div class="summary-row discount"><span>Kupon ({{ $coupon->code }})</span><span>-@money($discount)</span></div>
        @endif
        <div class="summary-row total"><span>Toplam</span><span>@money($total)</span></div>
        @if ($saving > 0)
            <div class="text-success text-small text-right">Bu siparişte @money($saving) kazanç sağladınız</div>
        @endif

        @if ($showCoupon ?? false)
            @if ($coupon)
                <div class="applied-coupon">
                    <span>🏷 <strong>{{ $coupon->code }}</strong> — {{ $coupon->label }}</span>
                    <form action="{{ route('cart.coupon.remove') }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="btn-link danger">Kaldır</button>
                    </form>
                </div>
            @else
                <form action="{{ route('cart.coupon.apply') }}" method="POST" class="coupon-form">
                    @csrf
                    <input type="text" name="code" class="form-control" placeholder="Kupon kodu" aria-label="Kupon kodu" value="{{ old('code') }}">
                    <button class="btn btn-dark btn-sm">Uygula</button>
                </form>
                @error('code') <small class="form-error">{{ $message }}</small> @enderror
            @endif

            <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-lg btn-block mt-1">Alışverişi Tamamla</a>
            <a href="{{ route('home') }}" class="btn btn-link btn-block mt-1">‹ Alışverişe devam et</a>
        @endif

        {{ $slot ?? '' }}
    </div>
</aside>
