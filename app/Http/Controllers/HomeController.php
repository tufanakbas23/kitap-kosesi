<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;

class HomeController extends Controller
{
    public function __invoke()
    {
        $base = fn () => Book::active()->with('author', 'publisher');
        $categories = Category::active()->withCount(['books' => fn ($books) => $books->active()])->get();

        return view('shop.home', [
            'featured'    => $base()->where('is_featured', true)->latest()->take(10)->get(),
            'bestsellers' => $base()->orderByDesc('sales_count')->take(10)->get(),
            'newReleases' => $base()->orderByDesc('publish_year')->latest('id')->take(10)->get(),
            'discounted'  => $base()->discounted()->orderByRaw('(list_price - price) / list_price DESC')->take(10)->get(),
            'categories'  => $categories,
            'scienceFiction' => $categories->firstWhere('slug', 'bilim-kurgu-fantastik'),
            'publishers'  => Publisher::whereHas('books', fn ($books) => $books->active())
                ->withCount(['books' => fn ($books) => $books->active()])
                ->orderByDesc('books_count')->take(8)->get(),
        ]);
    }
}
