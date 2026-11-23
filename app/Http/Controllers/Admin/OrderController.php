<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('user')->withCount('items')
            ->when($request->query('durum'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->query('q'), fn ($q, $term) => $q->where(fn ($w) => $w
                ->where('order_number', 'like', "%{$term}%")
                ->orWhere('shipping_name', 'like', "%{$term}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders'   => $orders,
            'statuses' => Order::STATUSES,
            'counts'   => Order::selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status'),
        ]);
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', [
            'order'     => $order->load('user', 'items.book', 'returnRequests.item'),
            'statuses'  => $order->editableStatuses(),
            'companies' => Order::CARGO_COMPANIES,
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status'          => ['required', Rule::in(array_keys(Order::STATUSES))],
            'cargo_company'   => ['nullable', 'required_if:status,kargoda', Rule::in(Order::CARGO_COMPANIES)],
            'tracking_number' => 'nullable|required_if:status,kargoda|string|max:50',
        ], [
            'cargo_company.required_if'   => 'Kargoya verilen sipariş için kargo firması seçilmelidir.',
            'tracking_number.required_if' => 'Kargoya verilen sipariş için takip numarası girilmelidir.',
        ]);

        if (! array_key_exists($data['status'], $order->editableStatuses())) {
            return back()->with('error', 'Sipariş bu duruma geçirilemez.');
        }

        if ($data['status'] === 'teslim_edildi' && ! $order->delivered_at) {
            $data['delivered_at'] = now();
        }

        $changed = DB::transaction(function () use ($order, $data) {
            $updated = Order::whereKey($order->id)->where('status', $order->status)->update($data);
            if (! $updated) {
                return false;
            }

            if ($data['status'] === 'iptal' && $order->status !== 'iptal') {
                foreach ($order->items as $item) {
                    $item->restoreStock($item->quantity);
                }
            }

            return true;
        }, 3);

        if (! $changed) {
            return back()->with('error', 'Siparişin durumu değişti. Güncel durumu kontrol edin.');
        }

        return back()->with('success', 'Sipariş durumu güncellendi: ' . Order::STATUSES[$data['status']]);
    }
}
