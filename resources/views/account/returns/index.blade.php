@extends('layouts.account')

@section('title', 'İade / Değişim Taleplerim')

@section('account')
    <h1>İade / Değişim Taleplerim</h1>

    <div class="alert alert-info">
        İade veya değişim talebi oluşturmak için <a href="{{ route('account.orders.index', ['durum' => 'teslim_edildi']) }}" class="fw-bold">&nbsp;teslim edilen siparişlerinize&nbsp;</a> gidip ilgili ürünün yanındaki “İade / Değişim” butonunu kullanın.
    </div>

    <div class="card table-wrap">
        <table class="table">
            <thead>
                <tr><th>Talep No</th><th>Ürün</th><th>Tür</th><th>Neden</th><th>Tarih</th><th>Durum</th></tr>
            </thead>
            <tbody>
                @forelse ($requests as $req)
                    <tr>
                        <td><strong>{{ $req->request_number }}</strong><br>
                            <a href="{{ route('account.orders.show', $req->order) }}" class="text-small text-muted">Sipariş #{{ $req->order->order_number }}</a></td>
                        <td>{{ $req->item->title }} <span class="text-muted">({{ $req->quantity }} adet)</span></td>
                        <td>{{ $req->type_label }}</td>
                        <td>{{ $req->reason_label }}</td>
                        <td>{{ $req->created_at->format('d.m.Y') }}</td>
                        <td>
                            <span class="badge badge-{{ $req->status_color }}">{{ $req->status_label }}</span>
                            @if ($req->admin_note)
                                <div class="text-small text-muted mt-1">💬 {{ $req->admin_note }}</div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-state">Henüz bir iade veya değişim talebiniz bulunmuyor.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $requests->links() }}
@endsection
