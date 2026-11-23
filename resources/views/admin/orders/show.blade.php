@extends('layouts.admin')

@section('title', 'Sipariş #' . $order->order_number)

@section('content')
    <div class="admin-form">
        <div>
            <div class="card mb-2">
                <div class="card-header">
                    <h3>Sipariş Kalemleri</h3>
                    <span class="badge badge-{{ $order->status_color }}">{{ $order->status_label }}</span>
                </div>
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th></th><th>Ürün</th><th>Birim Fiyat</th><th>Adet</th><th class="text-right">Toplam</th></tr></thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td><x-book-cover :book="$item->book" class="thumb" /></td>
                                    <td><strong>{{ $item->title }}</strong><br><span class="text-small text-muted">{{ $item->author_name }}</span></td>
                                    <td>@money($item->price)</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-right">@money($item->total)</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr><td colspan="4" class="text-right">Ara Toplam</td><td class="text-right">@money($order->subtotal)</td></tr>
                            @if ($order->discount > 0)
                                <tr><td colspan="4" class="text-right">İndirim ({{ $order->coupon_code }})</td><td class="text-right text-success">-@money($order->discount)</td></tr>
                            @endif
                            <tr><td colspan="4" class="text-right">Kargo</td><td class="text-right">@money($order->shipping_fee)</td></tr>
                            <tr><td colspan="4" class="text-right">Genel Toplam</td><td class="text-right text-primary">@money($order->total)</td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="grid-2">
                <div class="card">
                    <div class="card-header"><h3>Müşteri</h3></div>
                    <div class="card-body">
                        <dl class="detail-list">
                            <dt>Ad Soyad</dt><dd><a href="{{ route('admin.users.show', $order->user) }}" class="text-primary">{{ $order->user->full_name }}</a></dd>
                            <dt>E-posta</dt><dd>{{ $order->user->email }}</dd>
                            <dt>Telefon</dt><dd>{{ $order->user->phone ?? '-' }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h3>Teslimat & Ödeme</h3></div>
                    <div class="card-body">
                        <dl class="detail-list">
                            <dt>Alıcı</dt><dd>{{ $order->shipping_name }} ({{ $order->shipping_phone }})</dd>
                            <dt>Adres</dt><dd>{{ $order->shipping_address }}</dd>
                            <dt>Kart</dt><dd>{{ $order->card_holder }} — **** {{ $order->card_last4 }}</dd>
                            <dt>Taksit</dt><dd>{{ $order->installment == 1 ? 'Tek çekim' : $order->installment . ' taksit' }}</dd>
                            @if ($order->note)<dt>Müşteri Notu</dt><dd>{{ $order->note }}</dd>@endif
                        </dl>
                    </div>
                </div>
            </div>

            @if ($order->returnRequests->isNotEmpty())
                <div class="card mt-2">
                    <div class="card-header"><h3>İade / Değişim Talepleri</h3></div>
                    <div class="table-wrap">
                        <table class="table">
                            @foreach ($order->returnRequests as $rr)
                                <tr>
                                    <td><a href="{{ route('admin.returns.show', $rr) }}" class="text-primary fw-bold">{{ $rr->request_number }}</a></td>
                                    <td>{{ $rr->item->title }}</td>
                                    <td>{{ $rr->type_label }}</td>
                                    <td><span class="badge badge-{{ $rr->status_color }}">{{ $rr->status_label }}</span></td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div>
            <div class="card">
                <div class="card-header"><h3>Durumu Güncelle</h3></div>
                <div class="card-body">
                    <dl class="detail-list mb-2">
                        <dt>Sipariş Tarihi</dt><dd>{{ $order->created_at->format('d.m.Y H:i') }}</dd>
                        @if ($order->delivered_at)<dt>Teslim Tarihi</dt><dd>{{ $order->delivered_at->format('d.m.Y H:i') }}</dd>@endif
                    </dl>

                    <form action="{{ route('admin.orders.update', $order) }}" method="POST" novalidate>
                        @csrf @method('PUT')
                        <x-field name="status" label="Sipariş Durumu" type="select" :value="$order->status" :options="$statuses" required />
                        <x-field name="cargo_company" label="Kargo Firması" type="select" :value="$order->cargo_company"
                                 :options="array_combine($companies, $companies)" hint="Kargoya verildi durumunda zorunlu" />
                        <x-field name="tracking_number" label="Kargo Takip No" :value="$order->tracking_number" />
                        <button class="btn btn-primary btn-block" @disabled($order->status === 'iptal')>Güncelle</button>
                    </form>
                    @if ($order->status === 'iptal')
                        <p class="text-small text-muted mt-1">İptal edilen siparişin durumu değiştirilemez.</p>
                    @endif
                </div>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-link mt-2">‹ Siparişlere dön</a>
        </div>
    </div>
@endsection
