@extends('layouts.app')

@section('title', 'Yardım')

@section('content')
    <div class="page-title"><h1>Yardım</h1></div>
    <div class="card card-body">
        <section id="sikca-sorulan-sorular" class="mb-2">
            <h2>Sıkça Sorulan Sorular</h2>
            <p>KitapKöşesi bir ders projesidir. Alışveriş, kargo ve iade işlemleri örnek verilerle canlandırılır.</p>
            <p>Demo siparişlerinizi <a class="text-primary" href="{{ route('account.orders.index') }}">Siparişlerim</a> sayfasında görebilirsiniz.</p>
        </section>
        <section id="kargo-ve-teslimat" class="mb-2">
            <h2>Kargo ve Teslimat</h2>
            <p>İndirim sonrası sepet tutarı @money(config('shop.free_shipping_limit')) ve üzerindeyse kargo ücretsizdir. Altındaki siparişlerde demo kargo ücreti @money(config('shop.shipping_fee')) olarak hesaplanır.</p>
            <p>Yönetici takip numarası eklediğinde numara sipariş detayında görünür.</p>
        </section>
        <section id="iade-kosullari" class="mb-2">
            <h2>İade Koşulları</h2>
            <p>Teslim edildi olarak işaretlenen siparişlerde teslimden itibaren 14 gün içinde iade veya değişim talebi açabilirsiniz. Demo senaryosunda ürünün kullanılmamış ve orijinal ambalajında olması beklenir.</p>
            <p>Siparişlerim sayfasından siparişi açın ve ilgili ürünün <strong>İade / Değişim</strong> düğmesini kullanın. Talebinizin durumunu ve yönetici notunu <a class="text-primary" href="{{ route('account.returns.index') }}">İade / Değişim</a> sayfasında takip edebilirsiniz.</p>
        </section>
        <section id="on-bilgilendirme" class="mb-2">
            <h2>Ön Bilgilendirme</h2>
            <p>Siparişi onaylamadan önce teslimat adresinizi, kitap adetlerini, indirimi ve kargo ücretini özet ekranında kontrol edin. Ödemede sayfada belirtilen test kartı bilgilerini kullanın.</p>
        </section>
        <section id="mesafeli-satis" class="mb-2">
            <h2>Demo Satış Koşulları</h2>
            <p>Onay işlemi bir demo siparişi oluşturur. Karttan para çekilmez ve ürün gönderilmez. Onay bekleyen veya hazırlanan siparişleri Siparişlerim sayfasından iptal edebilirsiniz.</p>
        </section>
        <section id="uyelik-kosullari" class="mb-2">
            <h2>Demo Üyelik Koşulları</h2>
            <p>Üyelik, sipariş, adres ve favori akışlarını test etmenizi sağlar. Kayıt ve sipariş formlarını örnek bilgilerle doldurun.</p>
        </section>
        <section id="veri-kullanimi">
            <h2>Demo Veri Kullanımı</h2>
            <p>Hesap, adres, sipariş ve e-bülten tercihi bu kurulumun veritabanında tutulur. Siparişte test kartının son dört hanesi görünür. Gerçek kişisel bilgi veya kart bilgisi girmeyin.</p>
        </section>
    </div>
@endsection
