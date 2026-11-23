<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cart)
    {
    }

    public function index()
    {
        return view('shop.cart', $this->cart->summary());
    }

    public function add(Request $request, Book $book)
    {
        $data = $request->validate(['quantity' => 'sometimes|required|integer|min:1']);
        $qty = min($data['quantity'] ?? 1, config('shop.max_quantity'));

        if (! $book->isVisible() || $book->stock < 1) {
            return back()->with('error', 'Bu ürün şu anda stokta bulunmuyor.');
        }

        $this->cart->add($book, $qty);

        return back()->with('success', "“{$book->title}” sepetinize eklendi.");
    }

    public function update(Request $request, Book $book)
    {
        $data = $request->validate(['quantity' => 'required|integer|min:0']);
        $qty = (int) $data['quantity'];
        if (! $book->isVisible()) {
            $this->cart->remove($book);

            return back()->with('error', 'Bu kitap artık satışta olmadığı için sepetten çıkarıldı.');
        }


        if ($qty > $book->stock) {
            $this->cart->update($book, $book->stock);
            return back()->with('error', "Bu üründen stokta en fazla {$book->stock} adet bulunuyor.");
        }

        $this->cart->update($book, $qty);

        return back()->with('success', 'Sepetiniz güncellendi.');
    }

    public function remove(Book $book)
    {
        $this->cart->remove($book);

        return back()->with('success', "“{$book->title}” sepetinizden çıkarıldı.");
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string|max:30'], [], ['code' => 'kupon kodu']);

        if ($error = $this->cart->applyCoupon($request->input('code'))) {
            return back()->with('error', $error);
        }

        return back()->with('success', 'Kupon kodu uygulandı.');
    }

    public function removeCoupon()
    {
        $this->cart->removeCoupon();

        return back()->with('success', 'Kupon kaldırıldı.');
    }
}
