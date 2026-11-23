<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        return view('account.favorites', [
            'books' => $request->user()->favorites()->active()->with('author', 'publisher')->latest('favorites.created_at')->paginate(12),
        ]);
    }

    public function toggle(Request $request, Book $book)
    {
        abort_unless($book->isVisible(), 404);

        $result = $request->user()->favorites()->toggle($book->id);

        $message = count($result['attached'])
            ? "“{$book->title}” favorilerinize eklendi."
            : "“{$book->title}” favorilerinizden çıkarıldı.";

        return back()->with('success', $message);
    }
}
