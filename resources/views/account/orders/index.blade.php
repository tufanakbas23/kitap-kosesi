@extends('layouts.account')

@section('title', 'Siparişlerim')

@section('account')
    <h1>Siparişlerim</h1>

    <div class="status-filter">
        <a href="{{ route('account.orders.index') }}" class="{{ ! $current ? 'active' : '' }}">Tümü</a>
        @foreach ($statuses as $key => $label)
            <a href="{{ route('account.orders.index', ['durum' => $key]) }}" class="{{ $current === $key ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="card">
        @forelse ($orders as $order)
            @include('account.orders._card', ['order' => $order])
        @empty
            <div class="empty-state">
                <div class="icon">📦</div>
                <h3>Bu kriterde siparişiniz bulunmuyor</h3>
                <a href="{{ route('home') }}" class="btn btn-primary">Alışverişe Başla</a>
            </div>
        @endforelse
    </div>

    {{ $orders->links() }}
@endsection
