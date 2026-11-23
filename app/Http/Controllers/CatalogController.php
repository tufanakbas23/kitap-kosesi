<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Kitap listeleme (arama, kategori, yazar, yayınevi) ve kitap detay sayfası.
 * Tüm liste sayfaları aynı filtre/sıralama altyapısını ve aynı görünümü kullanır.
 */
class CatalogController extends Controller
{
    public const SORTS = [
        'onerilen'     => 'Önerilen',
        'cok-satan'    => 'Çok Satanlar',
        'yeni'         => 'En Yeniler',
        'fiyat-artan'  => 'Fiyat (Artan)',
        'fiyat-azalan' => 'Fiyat (Azalan)',
        'indirim'      => 'İndirim Oranı',
        'ad'           => 'Kitap Adı (A-Z)',
    ];

    public function index(Request $request)
    {
        $q = $this->searchTerm($request);

        return $this->listing($request, Book::query(), [
            'title'       => $q !== '' ? "“{$q}” için arama sonuçları" : 'Tüm Kitaplar',
            'breadcrumbs' => [['Kitaplar', null]],
        ]);
    }

    public function category(Request $request, Category $category)
    {
        abort_unless($category->is_active, 404);

        return $this->listing($request, $category->books()->getQuery(), [
            'title'       => $category->name,
            'subtitle'    => $category->description,
            'breadcrumbs' => [['Kitaplar', route('books.index')], [$category->name, null]],
            'activeCategory' => $category,
        ]);
    }

    public function author(Request $request, Author $author)
    {
        return $this->listing($request, $author->books()->getQuery(), [
            'title'       => $author->name,
            'subtitle'    => $author->bio,
            'breadcrumbs' => [['Yazarlar', null], [$author->name, null]],
        ]);
    }

    public function publisher(Request $request, Publisher $publisher)
    {
        return $this->listing($request, $publisher->books()->getQuery(), [
            'title'       => $publisher->name,
            'subtitle'    => $publisher->description,
            'breadcrumbs' => [['Yayınevleri', null], [$publisher->name, null]],
        ]);
    }

    public function bestsellers(Request $request)
    {
        $request->mergeIfMissing(['sirala' => 'cok-satan']);

        return $this->listing($request, Book::query(), [
            'title'       => 'Çok Satan Kitaplar',
            'breadcrumbs' => [['Çok Satanlar', null]],
        ]);
    }

    public function featured(Request $request)
    {
        return $this->listing($request, Book::query()->where('is_featured', true), [
            'title'       => 'Editörün Seçtikleri',
            'breadcrumbs' => [['Editörün Seçtikleri', null]],
        ]);
    }

    public function discounted(Request $request)
    {
        $request->mergeIfMissing(['sirala' => 'indirim']);

        return $this->listing($request, Book::query()->discounted(), [
            'title'       => 'İndirimli Kitaplar',
            'breadcrumbs' => [['İndirimli Kitaplar', null]],
        ]);
    }

    public function show(Book $book)
    {
        $book->load('author', 'publisher', 'category');
        abort_unless($book->isVisible(), 404);

        $reviews = $book->approvedReviews()->with('user')->get();

        return view('shop.book', [
            'book'         => $book,
            'reviews'      => $reviews,
            'avgRating'    => round((float) $reviews->avg('rating'), 1),
            'isFavorite'   => auth()->check() && auth()->user()->hasFavorited($book),
            'canReview'    => auth()->check() && ! $book->reviews()->where('user_id', auth()->id())->exists(),
            'authorBooks'  => Book::active()->with('author')->where('author_id', $book->author_id)
                                ->where('id', '!=', $book->id)->take(6)->get(),
            'similarBooks' => Book::active()->with('author')->where('category_id', $book->category_id)
                                ->where('id', '!=', $book->id)->inRandomOrder()->take(6)->get(),
        ]);
    }

    /* ------------------------------------------------------------------ */

    /**
     * Ortak listeleme: filtre + sıralama + sayfalama.
     */
    private function listing(Request $request, Builder $query, array $meta)
    {
        $query->active()->with('author', 'publisher')->search($this->searchTerm($request));

        // Filtre seçeneklerini (yayınevi listesi) filtre uygulanmadan önce topla
        $publisherOptions = Publisher::whereIn('id', (clone $query)->select('publisher_id'))
            ->orderBy('name')->get();

        if ($request->filled('kategori')) {
            $query->whereIn('category_id', (array) $request->query('kategori'));
        }
        if ($request->filled('yayinevi')) {
            $query->whereIn('publisher_id', (array) $request->query('yayinevi'));
        }
        if ($request->filled('min')) {
            $query->where('price', '>=', (float) $request->query('min'));
        }
        if ($request->filled('max')) {
            $query->where('price', '<=', (float) $request->query('max'));
        }
        if ($request->boolean('stokta')) {
            $query->where('stock', '>', 0);
        }
        if ($request->boolean('indirimli')) {
            $query->discounted();
        }

        $requestedSort = $request->query('sirala');
        $sort = is_string($requestedSort) && isset(self::SORTS[$requestedSort]) ? $requestedSort : 'onerilen';

        match ($sort) {
            'cok-satan'    => $query->orderByDesc('sales_count'),
            'yeni'         => $query->orderByDesc('publish_year')->orderByDesc('id'),
            'fiyat-artan'  => $query->orderBy('price'),
            'fiyat-azalan' => $query->orderByDesc('price'),
            'indirim'      => $query->orderByRaw('CASE WHEN list_price > price THEN (list_price - price) / list_price ELSE 0 END DESC'),
            'ad'           => $query->orderBy('title'),
            default        => $query->orderByDesc('is_featured')->orderByDesc('sales_count'),
        };

        $perPage = in_array((int) $request->query('goster'), [12, 24, 48]) ? (int) $request->query('goster') : 24;

        return view('shop.listing', $meta + [
            'subtitle'         => null,
            'activeCategory'   => null,
            'books'            => $query->paginate($perPage)->withQueryString(),
            'sorts'            => self::SORTS,
            'currentSort'      => $sort,
            'perPage'          => $perPage,
            'filterCategories' => Category::active()->get(),
            'filterPublishers' => $publisherOptions,
            'searchTerm'       => $this->searchTerm($request),
        ]);
    }

    private function searchTerm(Request $request): string
    {
        $term = $request->query('q');

        return is_string($term) ? trim($term) : '';
    }
}
