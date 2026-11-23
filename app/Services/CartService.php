<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Coupon;
use Illuminate\Support\Collection;

/**
 * Oturum (session) tabanlı alışveriş sepeti.
 * Yapı: session('cart') = [book_id => adet], session('coupon') = 'KOD'
 */
class CartService
{
    private const KEY = 'cart';
    private const COUPON_KEY = 'coupon';

    public function raw(): array
    {
        return session(self::KEY, []);
    }

    public function add(Book $book, int $qty = 1): void
    {
        $cart = $this->raw();
        $cart[$book->id] = min(($cart[$book->id] ?? 0) + $qty, $this->maxFor($book));
        session([self::KEY => $cart]);
    }

    public function update(Book $book, int $qty): void
    {
        $cart = $this->raw();
        if ($qty <= 0) {
            unset($cart[$book->id]);
        } else {
            $cart[$book->id] = min($qty, $this->maxFor($book));
        }
        session([self::KEY => $cart]);
    }

    public function remove(Book $book): void
    {
        $cart = $this->raw();
        unset($cart[$book->id]);
        session([self::KEY => $cart]);
    }

    public function clear(): void
    {
        session()->forget([self::KEY, self::COUPON_KEY]);
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    /**
     * Sepet satırları: her satırda book, quantity, line_total.
     * Silinmiş / pasif kitaplar otomatik olarak sepetten temizlenir.
     */
    public function items(): Collection
    {
        $cart = $this->raw();
        if (empty($cart)) {
            return collect();
        }

        $books = Book::with('author', 'publisher')->active()->whereIn('id', array_keys($cart))->get()->keyBy('id');

        // Artık bulunmayan kitapları sepetten düş
        $clean = array_intersect_key($cart, $books->all());
        if (count($clean) !== count($cart)) {
            session([self::KEY => $clean]);
        }

        return collect($clean)->map(fn ($qty, $id) => (object) [
            'book'       => $books[$id],
            'quantity'   => $qty,
            'line_total' => round($books[$id]->price * $qty, 2),
        ])->values();
    }

    /* ---------------- Kupon ---------------- */

    public function applyCoupon(string $code): ?string
    {
        $items = $this->items();
        if ($items->isEmpty()) {
            return 'Kupon kullanmak için önce sepetinize ürün ekleyin.';
        }

        $coupon = Coupon::where('code', mb_strtoupper(trim($code)))->first();
        if (! $coupon) {
            return 'Geçersiz kupon kodu.';
        }
        if ($error = $coupon->validationError($this->subtotal($items))) {
            return $error;
        }
        session([self::COUPON_KEY => $coupon->code]);

        return null;
    }

    public function removeCoupon(): void
    {
        session()->forget(self::COUPON_KEY);
    }

    private function coupon(float $subtotal): ?Coupon
    {
        $code = session(self::COUPON_KEY);
        if (! $code) {
            return null;
        }

        $coupon = Coupon::where('code', $code)->first();

        // Sepet değiştiyse kupon artık geçersiz olabilir
        if (! $coupon || $coupon->validationError($subtotal)) {
            $this->removeCoupon();
            return null;
        }

        return $coupon;
    }

    /* ---------------- Tutarlar ---------------- */

    public function subtotal(?Collection $items = null): float
    {
        return round(($items ?? $this->items())->sum('line_total'), 2);
    }

    /**
     * Tüm tutarları tek seferde hesaplar (görünümler ve sipariş için).
     */
    public function summary(): array
    {
        $items    = $this->items();
        $subtotal = $this->subtotal($items);
        $coupon   = $this->coupon($subtotal);
        $discount = $coupon ? $coupon->discountFor($subtotal) : 0;
        $afterDiscount = $subtotal - $discount;

        $limit    = config('shop.free_shipping_limit');
        $shipping = ($items->isEmpty() || $afterDiscount >= $limit) ? 0 : config('shop.shipping_fee');

        // Liste fiyatı üzerinden kazanç (Kitapyurdu'ndaki "Kazancınız" alanı)
        $listSaving = $items->sum(fn ($i) => $i->book->list_price ? ($i->book->list_price - $i->book->price) * $i->quantity : 0);

        return [
            'items'            => $items,
            'count'            => $items->sum('quantity'),
            'subtotal'         => $subtotal,
            'coupon'           => $coupon,
            'discount'         => $discount,
            'shipping'         => $shipping,
            'total'            => round($afterDiscount + $shipping, 2),
            'saving'           => round($listSaving + $discount, 2),
            'free_ship_remain' => max(0, round($limit - $afterDiscount, 2)),
        ];
    }

    private function maxFor(Book $book): int
    {
        return min($book->stock, config('shop.max_quantity'));
    }
}
