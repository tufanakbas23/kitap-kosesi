<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $requests = ReturnRequest::with('user', 'order', 'item')
            ->when($request->query('durum'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->query('tur'), fn ($q, $t) => $q->where('type', $t))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.returns.index', [
            'requests' => $requests,
            'statuses' => ReturnRequest::STATUSES,
            'types'    => ReturnRequest::TYPES,
        ]);
    }

    public function show(ReturnRequest $returnRequest)
    {
        return view('admin.returns.show', [
            'req'      => $returnRequest->load('user', 'order', 'item.book'),
            'statuses' => $returnRequest->editableStatuses(),
        ]);
    }

    public function update(Request $request, ReturnRequest $returnRequest)
    {
        $data = $request->validate([
            'status'     => ['required', Rule::in(array_keys(ReturnRequest::STATUSES))],
            'admin_note' => 'nullable|string|max:1000|required_if:status,reddedildi',
        ], [
            'admin_note.required_if' => 'Reddedilen talepler için müşteriye bir açıklama yazmalısınız.',
        ]);

        if (! array_key_exists($data['status'], $returnRequest->editableStatuses())) {
            return back()->with('error', 'Talep bu duruma geçirilemez.');
        }

        $changed = DB::transaction(function () use ($returnRequest, $data) {
            $updated = ReturnRequest::whereKey($returnRequest->id)->where('status', $returnRequest->status)->update($data);
            if (! $updated) {
                return false;
            }

            if ($data['status'] === 'tamamlandi' && $returnRequest->status !== 'tamamlandi' && $returnRequest->type === 'iade') {
                $returnRequest->item->restoreStock($returnRequest->quantity);
            }

            return true;
        }, 3);

        if (! $changed) {
            return back()->with('error', 'Talebin durumu değişti. Güncel durumu kontrol edin.');
        }

        return back()->with('success', 'Talep güncellendi.');
    }
}
