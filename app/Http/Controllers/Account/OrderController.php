<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('durum');

        $orders = $request->user()->orders()
            ->with('items.book.category')
            ->when($status && array_key_exists($status, Order::STATUSES), fn ($q) => $q->where('status', $status))
            ->paginate(10)
            ->withQueryString();

        return view('account.orders.index', [
            'orders'   => $orders,
            'statuses' => Order::STATUSES,
            'current'  => $status,
        ]);
    }

    public function show(Order $order)
    {
        abort_if($order->user_id != auth()->id(), 404);

        return view('account.orders.show', [
            'order' => $order->load('items.book.category', 'items.returnRequests'),
        ]);
    }

    public function cancel(Order $order)
    {
        abort_if($order->user_id != auth()->id(), 404);

        if (! $order->isCancellable()) {
            return back()->with('error', 'Bu sipariş artık iptal edilemez.');
        }

        $cancelled = DB::transaction(function () use ($order) {
            $updated = Order::whereKey($order->id)->where('status', $order->status)->update(['status' => 'iptal']);
            if (! $updated) {
                return false;
            }

            foreach ($order->items as $item) {
                $item->restoreStock($item->quantity);
            }

            return true;
        }, 3);

        if (! $cancelled) {
            return back()->with('error', 'Siparişin durumu değişti. Güncel durumu kontrol edin.');
        }

        return back()->with('success', 'Demo siparişiniz iptal edildi.');
    }
}
