@extends('layouts.admin')

@section('title', 'Talep #' . $req->request_number)

@section('content')
    <div class="admin-form">
        <div class="card">
            <div class="card-header">
                <h3>{{ $req->type_label }} Talebi</h3>
                <span class="badge badge-{{ $req->status_color }}">{{ $req->status_label }}</span>
            </div>
            <div class="card-body">
                <div class="book-mini mb-2">
                    <x-book-cover :book="$req->item->book" />
                    <div>
                        <strong>{{ $req->item->title }}</strong><br>
                        <span class="text-muted">{{ $req->quantity }} adet × @money($req->item->price) = @money($req->quantity * $req->item->price)</span>
                    </div>
                </div>
                <dl class="detail-list">
                    <dt>Müşteri</dt><dd><a href="{{ route('admin.users.show', $req->user) }}" class="text-primary">{{ $req->user->full_name }}</a> ({{ $req->user->email }})</dd>
                    <dt>Sipariş</dt><dd><a href="{{ route('admin.orders.show', $req->order) }}" class="text-primary">{{ $req->order->order_number }}</a> — Teslim: {{ $req->order->delivered_at?->format('d.m.Y') ?? '-' }}</dd>
                    <dt>Talep Tarihi</dt><dd>{{ $req->created_at->format('d.m.Y H:i') }}</dd>
                    <dt>Neden</dt><dd>{{ $req->reason_label }}</dd>
                    <dt>Açıklama</dt><dd>{{ $req->description ?: '-' }}</dd>
                </dl>
            </div>
        </div>

        <div>
            <div class="card">
                <div class="card-header"><h3>Talebi Yanıtla</h3></div>
                <div class="card-body">
                    <form action="{{ route('admin.returns.update', $req) }}" method="POST" novalidate>
                        @csrf @method('PUT')
                        <x-field name="status" label="Durum" type="select" :value="$req->status" :options="$statuses" required />
                        <x-field name="admin_note" label="Müşteriye Not" type="textarea" :value="$req->admin_note" rows="4"
                                 hint="Reddedilen taleplerde zorunlu. Müşteri bu notu “İade/Değişim Taleplerim” sayfasında görür." />
                        <button class="btn btn-primary btn-block">Kaydet</button>
                    </form>
                    @if ($req->type === 'iade')
                        <p class="text-small text-muted mt-1">ℹ “Tamamlandı” durumunda iade edilen adet stoğa geri eklenir.</p>
                    @endif
                </div>
            </div>
            <a href="{{ route('admin.returns.index') }}" class="btn btn-link mt-2">‹ Taleplere dön</a>
        </div>
    </div>
@endsection
