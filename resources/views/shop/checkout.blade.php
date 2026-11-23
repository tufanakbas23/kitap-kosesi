@extends('layouts.app')

@section('title', 'Ödeme')

@section('content')
    <div class="steps">
        <div class="step done">✔ 1. Sepet</div>
        <div class="step current">2. Adres ve Ödeme</div>
        <div class="step">3. Onay</div>
    </div>

    @include('partials.error-summary')

    <form action="{{ route('checkout.store') }}" method="POST" class="cart-layout" novalidate>
        @csrf
        <div>
            {{-- 1) Teslimat adresi --}}
            <div class="card mb-2">
                <div class="card-header">
                    <h3>📍 Teslimat Adresi</h3>
                    <a href="{{ route('account.addresses.create', ['redirect_to' => 'checkout']) }}" class="btn btn-outline btn-sm">+ Yeni Adres</a>
                </div>
                <div class="card-body">
                    @if ($addresses->isEmpty())
                        <div class="alert alert-warning">Kayıtlı adresiniz bulunmuyor. Devam etmek için lütfen bir adres ekleyin.</div>
                    @endif
                    <div class="address-options">
                        @foreach ($addresses as $address)
                            <label class="address-option">
                                <input type="radio" name="address_id" value="{{ $address->id }}"
                                       @checked(old('address_id', $addresses->first()?->id) == $address->id)>
                                <strong>{{ $address->title }}</strong>
                                {{ $address->full_name }}<br>
                                <span class="text-muted">{{ $address->full_address }}</span><br>
                                <span class="text-muted">{{ $address->phone }}</span>
                            </label>
                        @endforeach
                        <a href="{{ route('account.addresses.create', ['redirect_to' => 'checkout']) }}" class="address-option address-add">+ Yeni Adres Ekle</a>
                    </div>
                    @error('address_id') <small class="form-error">{{ $message }}</small> @enderror
                </div>
            </div>

            {{-- 2) Ödeme bilgileri --}}
            <div class="card mb-2">
                <div class="card-header"><h3>💳 Test Kartı Bilgileri</h3><span class="badge badge-info">Demo ödeme</span></div>
                <div class="card-body grid-2">
                    <div>
                        <x-field name="card_holder" id="card_holder" label="Kart Üzerindeki İsim" placeholder="AD SOYAD" autocomplete="cc-name" required />
                        <x-field name="card_number" id="card_number" label="Kart Numarası" placeholder="0000 0000 0000 0000" inputmode="numeric" maxlength="19" autocomplete="cc-number" required />
                        <div class="form-row-3">
                            <x-field name="card_month" id="card_month" label="Ay" type="select" placeholder="Ay" required
                                     :options="$months" />
                            <x-field name="card_year" id="card_year" label="Yıl" type="select" placeholder="Yıl" required
                                     :options="$years" />
                            <x-field name="card_cvv" label="CVV" placeholder="•••" inputmode="numeric" maxlength="3" autocomplete="cc-csc" required />
                        </div>
                    </div>
                    <div>
                        <div class="card-preview">
                            <div class="flex-between"><div class="chip-icon"></div><strong>KART</strong></div>
                            <div class="number" data-preview="number">•••• •••• •••• ••••</div>
                            <div class="bottom">
                                <span data-preview="holder">AD SOYAD</span>
                                <span data-preview="expiry">AA/YY</span>
                            </div>
                        </div>
                        <p class="text-small text-muted">Test kartı <strong>4111 1111 1111 1111</strong>, gelecekteki bir tarih ve <strong>123</strong> güvenlik kodunu kullanın. Gerçek ödeme alınmaz.</p>
                    </div>
                </div>
            </div>

            {{-- 3) Taksit --}}
            <div class="card mb-2">
                <div class="card-header"><h3>Taksit Seçenekleri</h3></div>
                <div class="card-body">
                    <table class="installment-table">
                        @foreach ($installments as $n)
                            <tr>
                                <td style="width:40px"><input type="radio" name="installment" value="{{ $n }}" id="inst{{ $n }}" @checked(old('installment', 1) == $n)></td>
                                <td><label for="inst{{ $n }}">{{ $n === 1 ? 'Tek Çekim' : $n . ' Taksit' }}</label></td>
                                <td class="text-right">{{ $n === 1 ? '' : $n . ' x ' . number_format($total / $n, 2, ',', '.') . ' TL' }}</td>
                                <td class="text-right fw-bold">@money($total)</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <x-field name="note" label="Sipariş Notu (isteğe bağlı)" type="textarea" placeholder="Kargo görevlisine iletmek istediğiniz not..." rows="2" />
                </div>
            </div>
        </div>

        {{-- Özet --}}
        <div>
            <div class="card mb-2">
                <div class="card-header"><h3>Ürünler</h3><a href="{{ route('cart.index') }}" class="text-primary text-small">Düzenle</a></div>
                <div class="card-body">
                    @foreach ($items as $line)
                        <div class="order-mini">
                            <x-book-cover :book="$line->book" />
                            <div style="flex:1">{{ $line->book->title }}<br><span class="text-muted">{{ $line->quantity }} adet</span></div>
                            <strong>@money($line->line_total)</strong>
                        </div>
                    @endforeach
                </div>
            </div>

            @component('shop.partials.summary', compact('count', 'subtotal', 'shipping', 'discount', 'coupon', 'total', 'saving', 'free_ship_remain'))
                <label class="checkbox mt-2">
                    <input type="checkbox" name="agreement" value="1" @checked(old('agreement'))>
                    <span><a href="{{ route('help') }}#on-bilgilendirme" class="text-primary" target="_blank" rel="noopener">Ön bilgilendirmeyi</a> ve <a href="{{ route('help') }}#mesafeli-satis" class="text-primary" target="_blank" rel="noopener">demo satış koşullarını</a> okudum.</span>
                </label>
                @error('agreement') <small class="form-error">{{ $message }}</small> @enderror
                <button type="submit" class="btn btn-primary btn-lg btn-block mt-2" @disabled($addresses->isEmpty())>Siparişi Onayla</button>
            @endcomponent
        </div>
    </form>
@endsection
