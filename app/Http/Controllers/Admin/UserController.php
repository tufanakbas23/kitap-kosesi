<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::withCount('orders')
            ->withSum(['orders as spent' => fn ($q) => $q->where('status', '!=', 'iptal')], 'total')
            ->when($request->query('q'), fn ($q, $term) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$term}%")
                ->orWhere('surname', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")))
            ->when($request->query('rol') === 'admin', fn ($q) => $q->where('is_admin', true))
            ->when($request->query('rol') === 'musteri', fn ($q) => $q->where('is_admin', false))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', ['users' => $users]);
    }

    public function show(User $user)
    {
        return view('admin.users.show', [
            'user'   => $user->load('addresses'),
            'orders' => $user->orders()->withCount('items')->take(10)->get(),
        ]);
    }

    public function toggle(User $user)
    {
        if ($user->id == auth()->id()) {
            return back()->with('error', 'Kendi hesabınızı pasif yapamazsınız.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->full_name . ($user->is_active ? ' aktif edildi.' : ' pasif edildi.'));
    }
}
