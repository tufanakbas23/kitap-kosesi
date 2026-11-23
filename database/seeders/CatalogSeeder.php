<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /** Kategori anahtarı => [ad, ikon, açıklama-cümlesi] */
    private array $categories = [
        'turk-klasik'  => ['Türk Klasikleri', '🏛️', 'Türk edebiyatının kalıcı eserlerinden biri olan bu kitap, yayımlandığı günden bu yana kuşaktan kuşağa okunmaya devam ediyor.'],
        'roman'        => ['Roman', '📖', 'Güçlü karakterleri ve sürükleyici kurgusuyla okuru ilk sayfadan itibaren hikâyenin içine çeken bir roman.'],
        'dunya-klasik' => ['Dünya Klasikleri', '🌍', 'Dünya edebiyatının başyapıtları arasında gösterilen bu eser, insanı ve toplumu derinlemesine ele alıyor.'],
        'bilimkurgu'   => ['Bilim Kurgu & Fantastik', '🚀', 'Hayal gücünün sınırlarını zorlayan bu eser, türünün en çok okunan ve tartışılan kitaplarından biri.'],
        'polisiye'     => ['Polisiye & Gerilim', '🔍', 'Son sayfaya kadar merakı diri tutan, ipuçlarıyla örülü bir gizem ve gerilim hikâyesi.'],
        'cocuk'        => ['Çocuk Kitapları', '🧸', 'Her yaştan okurun keyifle okuyabileceği, dostluk ve hayal gücü üzerine sıcak bir hikâye.'],
        'tarih'        => ['Tarih', '📜', 'Geçmişi anlamak ve bugünü yorumlamak isteyen okurlar için kapsamlı ve özenli bir tarih çalışması.'],
        'felsefe'      => ['Felsefe', '🤔', 'Düşünce tarihinin temel metinlerinden biri; varoluş, ahlak ve bilgi üzerine ufuk açıcı sorular soruyor.'],
        'kisisel'      => ['Kişisel Gelişim', '🌱', 'Günlük hayatta uygulanabilir önerileriyle okura kendini geliştirme yolunda pratik bir rehber sunuyor.'],
        'bilim'        => ['Bilim', '🔬', 'Karmaşık bilimsel konuları sade ve anlaşılır bir dille anlatan, merak uyandırıcı bir popüler bilim kitabı.'],
        'psikoloji'    => ['Psikoloji', '🧠', 'İnsan zihninin işleyişine dair çarpıcı gözlemler sunan, okuru kendi düşünce kalıplarını sorgulamaya davet eden bir eser.'],
        'siir'         => ['Şiir', '✒️', 'Türk şiirinin önemli isimlerinden birinin dizeleri; dili, imgeleri ve duyarlığıyla zamansız bir seçki.'],
    ];

    private array $publishers = [
        'Yapı Kredi Yayınları', 'Dergah Yayınları', 'İletişim Yayınları', 'İnkılap Kitabevi', 'Can Yayınları',
        'Doğan Kitap', 'İş Bankası Kültür Yayınları', 'Sel Yayıncılık', 'İthaki Yayınları', 'Metis Yayınları',
        'Alfa Yayınları', 'Altın Kitaplar', 'Kolektif Kitap', 'TÜBİTAK Yayınları', 'Pan Yayıncılık',
        'Pegasus Yayınları', 'Okuyan Us', 'Varlık Yayınları',
    ];

    /** [başlık, yazar, yayınevi, kategori, fiyat, liste fiyatı, sayfa, yıl, öne çıkan] */
    private array $books = [
        ['Kürk Mantolu Madonna', 'Sabahattin Ali', 'Yapı Kredi Yayınları', 'turk-klasik', 98.00, 140.00, 160, 2023, true],
        ['İçimizdeki Şeytan', 'Sabahattin Ali', 'Yapı Kredi Yayınları', 'turk-klasik', 112.00, 160.00, 264, 2022, false],
        ['Saatleri Ayarlama Enstitüsü', 'Ahmet Hamdi Tanpınar', 'Dergah Yayınları', 'turk-klasik', 186.00, 232.50, 382, 2021, true],
        ['Huzur', 'Ahmet Hamdi Tanpınar', 'Dergah Yayınları', 'turk-klasik', 198.00, null, 432, 2020, false],
        ['Tutunamayanlar', 'Oğuz Atay', 'İletişim Yayınları', 'turk-klasik', 285.00, 380.00, 724, 2023, true],
        ['Tehlikeli Oyunlar', 'Oğuz Atay', 'İletişim Yayınları', 'turk-klasik', 210.00, 280.00, 488, 2022, false],
        ['Çalıkuşu', 'Reşat Nuri Güntekin', 'İnkılap Kitabevi', 'turk-klasik', 145.00, null, 544, 2021, false],
        ['Aşk-ı Memnu', 'Halit Ziya Uşaklıgil', 'Can Yayınları', 'turk-klasik', 168.00, 210.00, 512, 2022, false],
        ['Sinekli Bakkal', 'Halide Edib Adıvar', 'Can Yayınları', 'turk-klasik', 155.00, null, 448, 2020, false],

        ['Masumiyet Müzesi', 'Orhan Pamuk', 'Yapı Kredi Yayınları', 'roman', 265.00, 330.00, 592, 2023, false],
        ['Benim Adım Kırmızı', 'Orhan Pamuk', 'Yapı Kredi Yayınları', 'roman', 245.00, null, 472, 2022, true],
        ['Kar', 'Orhan Pamuk', 'Yapı Kredi Yayınları', 'roman', 230.00, 270.00, 436, 2021, false],
        ['İnce Memed 1', 'Yaşar Kemal', 'Yapı Kredi Yayınları', 'roman', 220.00, 275.00, 436, 2023, false],
        ['Puslu Kıtalar Atlası', 'İhsan Oktay Anar', 'İletişim Yayınları', 'roman', 135.00, 180.00, 238, 2024, true],
        ['Bit Palas', 'Elif Şafak', 'Doğan Kitap', 'roman', 175.00, null, 368, 2021, false],

        ['Suç ve Ceza', 'Fyodor Dostoyevski', 'İş Bankası Kültür Yayınları', 'dunya-klasik', 189.00, 252.00, 687, 2023, true],
        ['Karamazov Kardeşler', 'Fyodor Dostoyevski', 'İş Bankası Kültür Yayınları', 'dunya-klasik', 295.00, 369.00, 984, 2022, false],
        ['Anna Karenina', 'Lev Tolstoy', 'İş Bankası Kültür Yayınları', 'dunya-klasik', 275.00, null, 960, 2021, false],
        ['Sefiller', 'Victor Hugo', 'Can Yayınları', 'dunya-klasik', 320.00, 400.00, 1288, 2022, false],
        ['Dönüşüm', 'Franz Kafka', 'Can Yayınları', 'dunya-klasik', 65.00, 85.00, 96, 2024, false],
        ['Yüzyıllık Yalnızlık', 'Gabriel García Márquez', 'Can Yayınları', 'dunya-klasik', 210.00, 262.50, 464, 2023, true],
        ['Fareler ve İnsanlar', 'John Steinbeck', 'Sel Yayıncılık', 'dunya-klasik', 92.00, null, 111, 2023, false],
        ['Yabancı', 'Albert Camus', 'Can Yayınları', 'dunya-klasik', 88.00, 110.00, 112, 2024, false],
        ['Simyacı', 'Paulo Coelho', 'Can Yayınları', 'dunya-klasik', 125.00, null, 188, 2022, false],

        ['1984', 'George Orwell', 'Can Yayınları', 'bilimkurgu', 135.00, 180.00, 352, 2024, true],
        ['Hayvan Çiftliği', 'George Orwell', 'Can Yayınları', 'bilimkurgu', 79.00, 99.00, 152, 2023, false],
        ['Cesur Yeni Dünya', 'Aldous Huxley', 'İthaki Yayınları', 'bilimkurgu', 145.00, null, 272, 2022, false],
        ['Dune', 'Frank Herbert', 'İthaki Yayınları', 'bilimkurgu', 395.00, 465.00, 712, 2024, true],
        ['Vakıf', 'Isaac Asimov', 'İthaki Yayınları', 'bilimkurgu', 175.00, 210.00, 280, 2023, false],
        ['Yüzüklerin Efendisi: Yüzük Kardeşliği', 'J.R.R. Tolkien', 'Metis Yayınları', 'bilimkurgu', 340.00, null, 552, 2022, false],
        ['Otostopçunun Galaksi Rehberi', 'Douglas Adams', 'Alfa Yayınları', 'bilimkurgu', 140.00, 175.00, 216, 2023, false],

        ['Doğu Ekspresinde Cinayet', 'Agatha Christie', 'Altın Kitaplar', 'polisiye', 115.00, 145.00, 256, 2023, false],
        ['Ve Sonra Hiç Kimse Kalmadı', 'Agatha Christie', 'Altın Kitaplar', 'polisiye', 115.00, null, 248, 2022, false],
        ['Baskerville\'lerin Köpeği', 'Arthur Conan Doyle', 'İş Bankası Kültür Yayınları', 'polisiye', 72.00, 90.00, 184, 2023, false],
        ['Da Vinci Şifresi', 'Dan Brown', 'Altın Kitaplar', 'polisiye', 225.00, 280.00, 528, 2021, false],

        ['Küçük Prens', 'Antoine de Saint-Exupéry', 'Can Yayınları', 'cocuk', 69.00, 92.00, 112, 2024, true],
        ['Şeker Portakalı', 'José Mauro de Vasconcelos', 'Can Yayınları', 'cocuk', 110.00, null, 196, 2023, false],
        ['Charlie\'nin Çikolata Fabrikası', 'Roald Dahl', 'Can Yayınları', 'cocuk', 98.00, 122.50, 208, 2023, false],
        ['Pal Sokağı Çocukları', 'Ferenc Molnár', 'İş Bankası Kültür Yayınları', 'cocuk', 75.00, null, 216, 2022, false],

        ['Nutuk', 'Mustafa Kemal Atatürk', 'İş Bankası Kültür Yayınları', 'tarih', 165.00, 220.00, 640, 2023, true],
        ['Sapiens: Hayvanlardan Tanrılara', 'Yuval Noah Harari', 'Kolektif Kitap', 'tarih', 285.00, 356.00, 412, 2024, true],
        ['Osmanlı İmparatorluğu Klasik Çağ', 'Halil İnalcık', 'Yapı Kredi Yayınları', 'tarih', 240.00, null, 368, 2022, false],
        ['Tüfek, Mikrop ve Çelik', 'Jared Diamond', 'TÜBİTAK Yayınları', 'tarih', 260.00, 300.00, 532, 2021, false],

        ['Böyle Buyurdu Zerdüşt', 'Friedrich Nietzsche', 'İş Bankası Kültür Yayınları', 'felsefe', 145.00, 180.00, 384, 2023, false],
        ['Devlet', 'Platon', 'İş Bankası Kültür Yayınları', 'felsefe', 155.00, null, 392, 2022, false],
        ['Sofie\'nin Dünyası', 'Jostein Gaarder', 'Pan Yayıncılık', 'felsefe', 235.00, 290.00, 560, 2023, false],

        ['Atomik Alışkanlıklar', 'James Clear', 'Pegasus Yayınları', 'kisisel', 210.00, 280.00, 352, 2024, true],
        ['İnsan Ne İle Yaşar', 'Lev Tolstoy', 'İş Bankası Kültür Yayınları', 'kisisel', 45.00, 60.00, 72, 2024, false],

        ['Zamanın Kısa Tarihi', 'Stephen Hawking', 'Alfa Yayınları', 'bilim', 165.00, 199.00, 232, 2023, false],
        ['Kozmos', 'Carl Sagan', 'Altın Kitaplar', 'bilim', 245.00, null, 448, 2022, false],

        ['İnsanın Anlam Arayışı', 'Viktor E. Frankl', 'Okuyan Us', 'psikoloji', 125.00, 160.00, 168, 2024, true],
        ['Hızlı ve Yavaş Düşünme', 'Daniel Kahneman', 'Varlık Yayınları', 'psikoloji', 295.00, 350.00, 560, 2023, false],

        ['Memleketimden İnsan Manzaraları', 'Nazım Hikmet', 'Yapı Kredi Yayınları', 'siir', 195.00, 240.00, 520, 2022, false],
        ['Sevda Sözleri', 'Cemal Süreya', 'Yapı Kredi Yayınları', 'siir', 175.00, null, 384, 2023, false],
        ['Bütün Şiirleri', 'Orhan Veli Kanık', 'Yapı Kredi Yayınları', 'siir', 110.00, 135.00, 232, 2023, false],
    ];

    private array $colors = [
        '#7a2e2e', '#2e4a7a', '#3d6b4f', '#8a5a1f', '#5a3d7a', '#1f6b73',
        '#a0522d', '#34495e', '#6b2e5a', '#2f5d50', '#8b3a3a', '#4a5d23',
    ];

    public function run(): void
    {
        $categoryIds = [];
        $order = 1;
        foreach ($this->categories as $key => [$name, $icon, $sentence]) {
            $categoryIds[$key] = Category::create([
                'name'        => $name,
                'icon'        => $icon,
                'description' => $name . ' kategorisindeki en sevilen kitaplar.',
                'sort_order'  => $order++,
            ])->id;
        }

        $publisherIds = [];
        foreach ($this->publishers as $name) {
            $publisherIds[$name] = Publisher::create([
                'name'        => $name,
                'description' => $name . ', Türkiye\'nin köklü yayınevlerinden biridir.',
            ])->id;
        }

        $authorIds = [];
        foreach ($this->books as $i => [$title, $author, $publisher, $cat, $price, $list, $pages, $year, $featured]) {
            $authorIds[$author] ??= Author::create([
                'name' => $author,
                'bio'  => $author . ', eserleri pek çok dile çevrilmiş ve geniş okur kitlelerine ulaşmış bir yazardır.',
            ])->id;

            Book::create([
                'title'        => $title,
                'author_id'    => $authorIds[$author],
                'publisher_id' => $publisherIds[$publisher],
                'category_id'  => $categoryIds[$cat],
                'isbn'         => '978975' . str_pad((string) (1000000 + $i * 7919 % 9000000), 7, '0', STR_PAD_LEFT),
                'price'        => $price,
                'list_price'   => $list,
                'stock'        => $i % 11 === 0 ? 0 : ($i % 7 === 0 ? 3 : 15 + ($i * 13) % 85),
                'page_count'   => $pages,
                'publish_year' => $year,
                'language'     => 'Türkçe',
                'cover_type'   => $pages > 600 ? 'Ciltli' : 'Karton Kapak',
                'paper_type'   => $i % 2 ? 'Kitap Kağıdı' : '2. Hamur',
                'dimensions'   => $i % 3 ? '13,5 x 21 cm' : '12 x 19 cm',
                'description'  => $title . ', ' . $author . ' imzasını taşıyor. ' . $this->categories[$cat][2]
                    . ' ' . $publisher . ' tarafından özenle hazırlanan bu baskı, ' . $pages . ' sayfadan oluşuyor.',
                'cover_color'  => $this->colors[$i % count($this->colors)],
                'is_featured'  => $featured,
                'sales_count'  => ($i * 37) % 250 + ($featured ? 150 : 0),
            ]);
        }
    }
}
