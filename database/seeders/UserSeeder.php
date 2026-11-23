<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Coupon;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Yönetici
        User::create([
            'name' => 'Site', 'surname' => 'Yöneticisi',
            'email' => 'admin@kitapkosesi.test', 'phone' => '05550000000',
            'password' => 'admin123', 'is_admin' => true,
        ]);

        // Test müşterisi (sunumda kullanılacak hesap)
        $test = User::create([
            'name' => 'Ahmet', 'surname' => 'Yılmaz',
            'email' => 'test@kitapkosesi.test', 'phone' => '05321234567',
            'birth_date' => '1998-04-12', 'gender' => 'erkek',
            'password' => '123456',
        ]);

        $test->addresses()->createMany([
            [
                'title' => 'Ev', 'full_name' => 'Ahmet Yılmaz', 'phone' => '05321234567',
                'city' => 'İstanbul', 'district' => 'Kadıköy', 'neighborhood' => 'Caferağa Mah.',
                'address_line' => 'Moda Cad. No: 12 Daire: 4', 'postal_code' => '34710', 'is_default' => true,
            ],
            [
                'title' => 'İş', 'full_name' => 'Ahmet Yılmaz', 'phone' => '05321234567',
                'city' => 'İstanbul', 'district' => 'Şişli', 'neighborhood' => 'Esentepe Mah.',
                'address_line' => 'Büyükdere Cad. No: 185 Kat: 7', 'postal_code' => '34394',
            ],
        ]);

        // Diğer müşteriler
        $people = [
            ['Zeynep', 'Kaya'], ['Mehmet', 'Demir'], ['Elif', 'Şahin'],
            ['Can', 'Çelik'], ['Ayşe', 'Arslan'], ['Burak', 'Doğan'],
        ];
        $cities = ['Ankara', 'İzmir', 'Bursa', 'Antalya', 'Eskişehir', 'Konya'];

        foreach ($people as $i => [$name, $surname]) {
            $user = User::create([
                'name' => $name, 'surname' => $surname,
                'email' => mb_strtolower($this->ascii($name)) . '@ornek.test',
                'phone' => '0533' . str_pad((string) (1000000 + $i * 12345), 7, '0'),
                'password' => '123456',
                'is_active' => $i !== 5, // bir kullanıcı pasif
            ]);

            $user->addresses()->create([
                'title' => 'Ev', 'full_name' => "$name $surname", 'phone' => $user->phone,
                'city' => $cities[$i], 'district' => 'Merkez',
                'address_line' => 'Atatürk Cad. No: ' . ($i + 3), 'is_default' => true,
            ]);
        }

        // Kuponlar
        Coupon::insert([
            ['code' => 'HOSGELDIN10', 'type' => 'percent', 'value' => 10, 'min_total' => 0, 'usage_limit' => null, 'used_count' => 3, 'expires_at' => now()->addMonths(3)->toDateString(), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'KITAP50', 'type' => 'fixed', 'value' => 50, 'min_total' => 300, 'usage_limit' => 100, 'used_count' => 12, 'expires_at' => now()->addMonth()->toDateString(), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'YAZ2025', 'type' => 'percent', 'value' => 20, 'min_total' => 0, 'usage_limit' => null, 'used_count' => 40, 'expires_at' => now()->subMonths(2)->toDateString(), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Yorumlar
        $comments = [
            [5, 'Kesinlikle okunması gereken bir kitap. Kargo da çok hızlı geldi.'],
            [4, 'Çok beğendim, baskı kalitesi de gayet iyi.'],
            [5, 'Bir solukta okudum, herkese tavsiye ederim.'],
            [3, 'Güzel ama beklentimin biraz altında kaldı.'],
            [4, 'Paketleme özenliydi, kitap sağlam ulaştı.'],
        ];
        $users = User::where('is_admin', false)->get();

        Book::where('is_featured', true)->get()->each(function (Book $book, $i) use ($users, $comments) {
            foreach ($users->take(3 + $i % 3) as $j => $user) {
                [$rating, $text] = $comments[($i + $j) % count($comments)];
                Review::create([
                    'user_id' => $user->id, 'book_id' => $book->id,
                    'rating' => $rating, 'comment' => $text,
                    'is_approved' => $j !== 0 || $i % 4 !== 0, // birkaç yorum onay bekliyor
                ]);
            }
        });
    }

    private function ascii(string $s): string
    {
        return strtr($s, ['ş' => 's', 'Ş' => 'S', 'ı' => 'i', 'İ' => 'I', 'ğ' => 'g', 'Ğ' => 'G', 'ü' => 'u', 'Ü' => 'U', 'ö' => 'o', 'Ö' => 'O', 'ç' => 'c', 'Ç' => 'C']);
    }
}
