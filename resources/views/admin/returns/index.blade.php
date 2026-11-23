@extends('layouts.admin')

@section('title', 'İade / Değişim Talepleri')

@section('content')
    <div class="card">
        <form method="GET" class="filter-bar">
            <select name="durum" class="form-control">
                <option value="">Tüm durumlar</option>
                @foreach ($statuses as $key => $label)
                    <option value="{{ $key }}" @selected(request('durum') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="tur" class="form-control">
                <option value="">Tüm türler</option>
                @foreach ($types as $key => $label)
                    <option value="{{ $key }}" @selected(request('tur') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="btn btn-dark btn-sm">Filtrele</button>
        </form>

        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Talep No</th><th>Müşteri</th><th>Sipariş</th><th>Ürün</th><th>Tür</th><th>Neden</th><th>Tarih</th><th>Durum</th><th></th></tr></thead>
                <tbody>
                    @forelse ($requests as $req)
                        <tr>
                            <td><strong>{{ $req->request_number }}</strong></td>
                            <td>{{ $req->user->full_name }}</td>
                            <td><a href="{{ route('admin.orders.show', $req->order) }}" class="text-primary">{{ $req->order->order_number }}</a></td>
                            <td>{{ $req->item->title }} ({{ $req->quantity }})</td>
                            <td><span class="badge {{ $req->type === 'iade' ? 'badge-danger' : 'badge-info' }}">{{ $req->type_label }}</span></td>
                            <td class="text-small">{{ $req->reason_label }}</td>
                            <td>{{ $req->created_at->format('d.m.Y') }}</td>
                            <td><span class="badge badge-{{ $req->status_color }}">{{ $req->status_label }}</span></td>
                            <td class="text-right"><a href="{{ route('admin.returns.show', $req) }}" class="btn btn-outline btn-sm">İncele</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="empty-state">Talep bulunamadı.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $requests->links() }}
@endsection
