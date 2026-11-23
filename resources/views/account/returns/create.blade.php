@extends('layouts.account')

@section('title', 'İade / Değişim Talebi')

@section('account')
    <div class="card">
        <div class="card-header"><h3>İade / Değişim Talebi Oluştur</h3></div>
        <div class="card-body">
            <div class="book-mini mb-2">
                <x-book-cover :book="$item->book" />
                <div>
                    <strong>{{ $item->title }}</strong><br>
                    <span class="text-muted text-small">Sipariş #{{ $item->order->order_number }} · {{ $item->quantity }} adet · @money($item->price)</span>
                </div>
            </div>

            <form action="{{ route('account.returns.store', $item) }}" method="POST" novalidate>
                @csrf
                <div class="form-group">
                    <label class="form-label">Talep Türü <span class="req">*</span></label>
                    <div class="flex">
                        @foreach ($types as $key => $label)
                            <label class="checkbox"><input type="radio" name="type" value="{{ $key }}" @checked(old('type', 'iade') === $key)> {{ $label }}</label>
                        @endforeach
                    </div>
                    @error('type') <small class="form-error">{{ $message }}</small> @enderror
                </div>

                <div class="form-row">
                    <x-field name="reason" label="Talep Nedeni" type="select" :options="$reasons" required />
                    <x-field name="quantity" label="Adet" type="select" :value="1" required
                             :options="array_combine(range(1, $remainingQuantity), range(1, $remainingQuantity))" />
                </div>

                <x-field name="description" label="Açıklama" type="textarea" rows="4"
                         placeholder="Talebinizle ilgili detayları yazın (“Diğer” seçildiyse zorunlu)" />

                <div class="alert alert-warning text-small">
                    İade edilecek ürünlerin kullanılmamış ve orijinal ambalajında olması gerekmektedir.
                    Talebinizin durumunu İade / Değişim sayfasından takip edebilirsiniz.
                </div>

                <div class="form-actions">
                    <a href="{{ route('account.orders.show', $item->order) }}" class="btn btn-outline">Vazgeç</a>
                    <button class="btn btn-primary">Talebi Gönder</button>
                </div>
            </form>
        </div>
    </div>
@endsection
