<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        return view('account.returns.index', [
            'requests' => $request->user()->returnRequests()->with('order', 'item')->paginate(10),
        ]);
    }

    public function create(OrderItem $item)
    {
        $this->guard($item);

        return view('account.returns.create', [
            'item'              => $item->load('order', 'book'),
            'types'             => ReturnRequest::TYPES,
            'reasons'           => ReturnRequest::REASONS,
            'remainingQuantity' => $item->remainingReturnQuantity(),
        ]);
    }

    public function store(Request $request, OrderItem $item)
    {
        $return = DB::transaction(function () use ($request, $item) {
            $item = OrderItem::lockForUpdate()->findOrFail($item->id);
            $this->guard($item);

            $data = $request->validate([
                'type'        => ['required', Rule::in(array_keys(ReturnRequest::TYPES))],
                'reason'      => ['required', Rule::in(array_keys(ReturnRequest::REASONS))],
                'quantity'    => ['required', 'integer', 'min:1', 'max:' . $item->remainingReturnQuantity()],
                'description' => ['nullable', 'string', 'max:1000', 'required_if:reason,diger'],
            ], [
                'description.required_if' => '“Diğer” seçeneğinde açıklama yazmanız gerekmektedir.',
            ], [
                'type' => 'talep türü', 'reason' => 'talep nedeni', 'quantity' => 'adet', 'description' => 'açıklama',
            ]);

            return $item->returnRequests()->create($data + [
                'request_number' => ReturnRequest::generateNumber(),
                'user_id'        => $request->user()->id,
                'order_id'       => $item->order_id,
            ]);
        }, 3);

        return redirect()->route('account.returns.index')
            ->with('success', "{$return->type_label} talebiniz alındı. Talep no: {$return->request_number}");
    }

    private function guard(OrderItem $item): void
    {
        $order = $item->order;

        abort_if($order->user_id != auth()->id(), 404);

        if (! $order->isReturnable()) {
            abort(redirect()->route('account.orders.show', $order)
                ->with('error', 'İade/değişim talebi yalnızca teslim edilen siparişler için 14 gün içinde oluşturulabilir.'));
        }

        if ($item->hasOpenRequest()) {
            abort(redirect()->route('account.orders.show', $order)
                ->with('error', 'Bu ürün için zaten açık bir talebiniz bulunuyor.'));
        }

        if ($item->remainingReturnQuantity() === 0) {
            abort(redirect()->route('account.orders.show', $order)
                ->with('error', 'Bu sipariş kaleminde talep açabileceğiniz ürün kalmadı.'));
        }
    }
}
