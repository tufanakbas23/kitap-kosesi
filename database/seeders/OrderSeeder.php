<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $books = Book::with('author')->where('stock', '>', 0)->get();
        $test  = User::where('email', 'test@kitapkosesi.test')->first();

        // Test kullanıcısının farklı durumlardaki siparişleri
        $delivered = $this->makeOrder($test, $books->slice(0, 3), 'teslim_edildi', now()->subDays(6));
        $this->makeOrder($test, $books->slice(5, 2), 'kargoda', now()->subDays(2));
        $this->makeOrder($test, $books->slice(10, 1), 'beklemede', now()->subHours(3));
        $this->makeOrder($test, $books->slice(14, 2), 'iptal', now()->subDays(20));

        // Bir iade talebi örneği
        $item = $delivered->items->first();
        ReturnRequest::create([
            'request_number' => ReturnRequest::generateNumber(),
            'user_id' => $test->id, 'order_id' => $delivered->id, 'order_item_id' => $item->id,
            'type' => 'degisim', 'reason' => 'hasarli', 'quantity' => 1,
            'description' => 'Kitabın kapağı kargoda ezilmiş, değişim talep ediyorum.',
        ]);

        // Diğer müşterilerin son 10 güne yayılmış siparişleri (dashboard grafiği için)
        $statuses = ['teslim_edildi', 'teslim_edildi', 'kargoda', 'hazirlaniyor', 'beklemede'];
        $others = User::where('is_admin', false)->where('id', '!=', $test->id)->get();

        foreach (range(0, 17) as $i) {
            $user = $others[$i % $others->count()];
            $this->makeOrder(
                $user,
                $books->shuffle()->take(1 + $i % 3),
                $statuses[$i % count($statuses)],
                now()->subDays($i % 10)->subHours($i * 2)
            );
        }
    }

    private function makeOrder(User $user, $books, string $status, Carbon $date): Order
    {
        $address = $user->addresses()->first();
        $subtotal = 0;
        $lines = [];

        foreach ($books as $k => $book) {
            $qty = 1 + ($k % 2);
            $subtotal += $book->price * $qty;
            $lines[] = [
                'book_id' => $book->id, 'title' => $book->title, 'author_name' => $book->author->name,
                'price' => $book->price, 'quantity' => $qty, 'total' => $book->price * $qty,
            ];
        }

        $shipping = $subtotal >= 250 ? 0 : 39.90;

        $order = Order::create([
            'order_number' => Order::generateNumber(),
            'user_id' => $user->id, 'status' => $status,
            'subtotal' => $subtotal, 'discount' => 0, 'shipping_fee' => $shipping,
            'total' => $subtotal + $shipping,
            'shipping_name' => $address->full_name, 'shipping_phone' => $address->phone,
            'shipping_city' => $address->city, 'shipping_district' => $address->district,
            'shipping_address' => $address->full_address,
            'card_holder' => mb_strtoupper($user->full_name), 'card_last4' => '1111',
            'cargo_company' => in_array($status, ['kargoda', 'teslim_edildi']) ? 'Yurtiçi Kargo' : null,
            'tracking_number' => in_array($status, ['kargoda', 'teslim_edildi']) ? (string) random_int(100000000, 999999999) : null,
            'delivered_at' => $status === 'teslim_edildi' ? $date->copy()->addDays(2) : null,
        ]);

        $order->items()->createMany($lines);

        // created_at'i geçmişe çek
        $order->forceFill(['created_at' => $date, 'updated_at' => $date])->saveQuietly();

        return $order->load('items');
    }
}
