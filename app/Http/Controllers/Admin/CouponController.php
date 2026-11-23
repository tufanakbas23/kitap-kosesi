<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index()
    {
        return view('admin.coupons.index', ['coupons' => Coupon::latest()->paginate(20)]);
    }

    public function create()
    {
        return view('admin.coupons.form', ['coupon' => new Coupon(['type' => 'percent', 'is_active' => true])]);
    }

    public function store(Request $request)
    {
        Coupon::create($this->validated($request));

        return redirect()->route('admin.coupons.index')->with('success', 'Kupon oluşturuldu.');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.form', ['coupon' => $coupon]);
    }

    public function update(Request $request, Coupon $coupon)
    {
        $coupon->update($this->validated($request, $coupon));

        return redirect()->route('admin.coupons.index')->with('success', 'Kupon güncellendi.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return back()->with('success', 'Kupon silindi.');
    }

    private function validated(Request $request, ?Coupon $coupon = null): array
    {
        $request->merge(['code' => mb_strtoupper(trim((string) $request->code))]);

        $data = $request->validate([
            'code'        => ['required', 'alpha_num', 'max:30', Rule::unique('coupons')->ignore($coupon?->id)],
            'type'        => 'required|in:percent,fixed',
            'value'       => ['required', 'numeric', 'min:1', $request->type === 'percent' ? 'max:90' : 'max:10000'],
            'min_total'   => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at'  => 'nullable|date',
        ], [], [
            'code' => 'kupon kodu', 'type' => 'indirim türü', 'value' => 'indirim değeri',
            'min_total' => 'minimum sepet tutarı', 'usage_limit' => 'kullanım limiti', 'expires_at' => 'son geçerlilik tarihi',
        ]);

        $data['min_total'] = $data['min_total'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
