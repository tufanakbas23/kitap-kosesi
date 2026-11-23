<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        return view('account.reviews', [
            'reviews' => $request->user()->reviews()->with('book.category')->latest()->paginate(10),
        ]);
    }

    public function store(Request $request, Book $book)
    {
        abort_unless($book->isVisible(), 404);

        if ($book->reviews()->where('user_id', $request->user()->id)->exists()) {
            return back()->with('error', 'Bu kitap için zaten bir değerlendirme yaptınız.');
        }

        $data = $request->validate([
            'rating'  => 'required|integer|between:1,5',
            'comment' => 'required|string|min:10|max:1000',
        ], [], ['rating' => 'puan', 'comment' => 'yorum']);

        $book->reviews()->create($data + ['user_id' => $request->user()->id]);

        return back()->with('success', 'Yorumunuz alındı. Onaylandıktan sonra yayınlanacaktır.');
    }
}
