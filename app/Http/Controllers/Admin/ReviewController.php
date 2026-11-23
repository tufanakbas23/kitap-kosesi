<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::with('user', 'book')
            ->when($request->query('durum') === 'bekleyen', fn ($q) => $q->where('is_approved', false))
            ->when($request->query('durum') === 'onayli', fn ($q) => $q->where('is_approved', true))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reviews.index', ['reviews' => $reviews]);
    }

    public function toggle(Review $review)
    {
        $review->update(['is_approved' => ! $review->is_approved]);

        return back()->with('success', $review->is_approved ? 'Yorum onaylandı.' : 'Yorum yayından kaldırıldı.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Yorum silindi.');
    }
}
