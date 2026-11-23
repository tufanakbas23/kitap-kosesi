<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Book;
use App\Models\Coupon;
use App\Models\Order;
use App\Services\CartService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct(private CartService $cart)
    {
    }

    public function index()
    {
        $summary = $this->cart->summary();
        if ($summary['items']->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Sepetiniz boş.');
        }

        return view('shop.checkout', $summary + [
            'addresses'    => auth()->user()->addresses,
            'installments' => config('shop.installments'),
            'months'       => collect(range(1, 12))->mapWithKeys(fn ($m) => [$m => str_pad($m, 2, '0', STR_PAD_LEFT)])->all(),
            'years'        => collect(range(now()->year, now()->year + 10))->mapWithKeys(fn ($y) => [$y => $y])->all(),
        ]);
    }

    public function store(CheckoutRequest $request)
    {
        $summary = $this->cart->summary();

        if ($summary['items']->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Sepetiniz boş.');
        }

        $address = $request->user()->addresses()->findOrFail($request->address_id);

        $order = DB::transaction(function () use ($summary, $address, $request) {
            foreach ($summary['items']->sortBy('book.id') as $line) {
                $reserved = Book::active()->whereKey($line->book->id)
                    ->where('stock', '>=', $line->quantity)
                    ->decrement('stock', $line->quantity, [
                        'sales_count' => DB::raw('sales_count + ' . (int) $line->quantity),
                    ]);

                if (! $reserved) {
                    $this->checkoutError("“{$line->book->title}” için yeterli stok yok. Sepetteki adedi kontrol edin.");
                }
            }

            if ($coupon = $summary['coupon']) {
                $used = Coupon::whereKey($coupon->id)->where('is_active', true)
                    ->where('type', $coupon->type)->where('value', $coupon->value)
                    ->where('code', $coupon->code)->where('min_total', '<=', $summary['subtotal'])
                    ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', today()->toDateString()))
                    ->where(fn ($q) => $q->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit'))
                    ->increment('used_count');

                if (! $used) {
                    $this->checkoutError('Kupon artık kullanılamıyor. Sepet özetini kontrol edin.');
                }
            }

            $order = Order::create([
                'order_number'      => Order::generateNumber(),
                'user_id'           => $request->user()->id,
                'status'            => 'beklemede',
                'subtotal'          => $summary['subtotal'],
                'discount'          => $summary['discount'],
                'shipping_fee'      => $summary['shipping'],
                'total'             => $summary['total'],
                'coupon_code'       => $summary['coupon']?->code,
                'shipping_name'     => $address->full_name,
                'shipping_phone'    => $address->phone,
                'shipping_city'     => $address->city,
                'shipping_district' => $address->district,
                'shipping_address'  => $address->full_address,
                'card_holder'       => mb_strtoupper($request->card_holder),
                'card_last4'        => substr($request->card_number, -4),
                'installment'       => $request->installment,
                'note'              => $request->note,
            ]);

            foreach ($summary['items'] as $line) {
                $order->items()->create([
                    'book_id'     => $line->book->id,
                    'title'       => $line->book->title,
                    'author_name' => $line->book->author->name,
                    'price'       => $line->book->price,
                    'quantity'    => $line->quantity,
                    'total'       => $line->line_total,
                ]);
            }

            return $order;
        }, 3);

        $this->cart->clear();

        return redirect()->route('checkout.success', $order);
    }

    public function success(Order $order)
    {
        abort_if($order->user_id != auth()->id(), 404);

        return view('shop.order-success', ['order' => $order->load('items')]);
    }

    private function checkoutError(string $message): never
    {
        throw new HttpResponseException(redirect()->route('cart.index')->with('error', $message));
    }
}
