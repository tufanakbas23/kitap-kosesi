<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('account.dashboard', [
            'user'          => $user,
            'recentOrders'  => $user->orders()->with('items.book.category')->take(3)->get(),
            'orderCount'    => $user->orders()->count(),
            'favoriteCount' => $user->favorites()->active()->count(),
            'addressCount'  => $user->addresses()->count(),
            'returnCount'   => $user->returnRequests()->count(),
        ]);
    }
}
