<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function index()
    {
        return view('account.addresses.index', ['addresses' => auth()->user()->addresses]);
    }

    public function create()
    {
        return view('account.addresses.form', [
            'address' => new Address(['full_name' => auth()->user()->full_name, 'phone' => auth()->user()->phone]),
        ]);
    }

    public function store(AddressRequest $request)
    {
        DB::transaction(function () use ($request) {
            $user = User::lockForUpdate()->findOrFail($request->user()->id);
            $isDefault = $request->boolean('is_default') || $user->addresses()->doesntExist();

            if ($isDefault) {
                $user->addresses()->update(['is_default' => false]);
            }

            $user->addresses()->create(['is_default' => $isDefault] + $request->validated());
        }, 3);

        // Ödeme sayfasından gelindiyse oraya geri dön
        return redirect($request->input('redirect_to') === 'checkout' ? route('checkout.index') : route('account.addresses.index'))
            ->with('success', 'Adresiniz kaydedildi.');
    }

    public function edit(Address $address)
    {
        $this->authorizeOwner($address);

        return view('account.addresses.form', ['address' => $address]);
    }

    public function update(AddressRequest $request, Address $address)
    {
        $this->authorizeOwner($address);

        DB::transaction(function () use ($request, $address) {
            $user = User::lockForUpdate()->findOrFail($request->user()->id);
            $address = $user->addresses()->findOrFail($address->id);
            if ($request->boolean('is_default')) {
                $user->addresses()->update(['is_default' => false]);
            }

            $address->update(['is_default' => $request->boolean('is_default') || $address->is_default] + $request->validated());
        }, 3);

        return redirect()->route('account.addresses.index')->with('success', 'Adresiniz güncellendi.');
    }

    public function destroy(Request $request, Address $address)
    {
        $this->authorizeOwner($address);
        DB::transaction(function () use ($request, $address) {
            $user = User::lockForUpdate()->findOrFail($request->user()->id);
            $address = $user->addresses()->findOrFail($address->id);
            $address->delete();

            if ($address->is_default && $first = $user->addresses()->first()) {
                $first->update(['is_default' => true]);
            }
        }, 3);

        return back()->with('success', 'Adres silindi.');
    }

    private function authorizeOwner(Address $address): void
    {
        abort_if($address->user_id != auth()->id(), 404);
    }
}
