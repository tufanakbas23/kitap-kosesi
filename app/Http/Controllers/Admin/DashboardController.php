<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\Review;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $valid = fn () => Order::where('status', '!=', 'iptal');

        // Son 7 günün satış tutarları (grafik için)
        $days = collect(range(6, 0))->map(function ($ago) use ($valid) {
            $date = now()->subDays($ago);
            return [
                'label' => $date->translatedFormat('d M'),
                'total' => (float) $valid()->whereDate('created_at', $date->toDateString())->sum('total'),
            ];
        });

        return view('admin.dashboard', [
            'stats' => [
                'revenue'        => $valid()->sum('total'),
                'todayRevenue'   => $valid()->whereDate('created_at', today())->sum('total'),
                'orders'         => Order::count(),
                'pendingOrders'  => Order::where('status', 'beklemede')->count(),
                'customers'      => User::where('is_admin', false)->count(),
                'books'          => Book::count(),
                'pendingReturns' => ReturnRequest::where('status', 'beklemede')->count(),
                'pendingReviews' => Review::where('is_approved', false)->count(),
            ],
            'days'         => $days,
            'maxDay'       => max(1, $days->max('total')),
            'recentOrders' => Order::with('user')->latest()->take(8)->get(),
            'lowStock'     => Book::with('author')->where('stock', '<=', 5)->orderBy('stock')->take(8)->get(),
            'topBooks'     => Book::with('author')->orderByDesc('sales_count')->take(5)->get(),
        ]);
    }
}
