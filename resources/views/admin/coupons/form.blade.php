@extends('layouts.admin')

@section('title', $coupon->exists ? 'Kuponu Düzenle' : 'Yeni Kupon')

@section('content')
    <div class="card" style="max-width: 720px">
        <div class="card-body">
            <form action="{{ $coupon->exists ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}" method="POST" novalidate>
                @csrf
                @if ($coupon->exists) @method('PUT') @endif

                <x-field name="code" label="Kupon Kodu" :value="$coupon->code" placeholder="ÖRN: KITAP20" hint="Sadece harf ve rakam; otomatik büyük harfe çevrilir." required />
                <div class="form-row">
                    <x-field name="type" label="İndirim Türü" type="select" :value="$coupon->type" required
                             :options="['percent' => 'Yüzde (%)', 'fixed' => 'Sabit Tutar (TL)']" />
                    <x-field name="value" label="İndirim Değeri" type="number" step="0.01" :value="$coupon->value" required />
                </div>
                <div class="form-row-3">
                    <x-field name="min_total" label="Min. Sepet Tutarı" type="number" step="0.01" :value="$coupon->min_total" />
                    <x-field name="usage_limit" label="Kullanım Limiti" type="number" :value="$coupon->usage_limit" hint="Boş = sınırsız" />
                    <x-field name="expires_at" label="Son Geçerlilik" type="date" :value="$coupon->expires_at?->format('Y-m-d')" />
                </div>
                <label class="checkbox mb-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active))> Aktif</label>

                <div class="form-actions">
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline">Vazgeç</a>
                    <button class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
@endsection
