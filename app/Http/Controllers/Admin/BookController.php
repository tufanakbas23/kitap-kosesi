<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $term = $request->query('q');
        $books = Book::with('author', 'publisher', 'category')
            ->search(is_string($term) ? $term : '')
            ->when($request->query('kategori'), fn ($q, $id) => $q->where('category_id', $id))
            ->when($request->query('stok') === 'tukendi', fn ($q) => $q->where('stock', 0))
            ->when($request->query('stok') === 'az', fn ($q) => $q->whereBetween('stock', [1, 5]))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.books.index', [
            'books'      => $books,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.books.form', $this->formData(new Book([
            'language' => 'Türkçe', 'cover_type' => 'Karton Kapak', 'cover_color' => '#5b6c8f',
            'is_active' => true, 'stock' => 0,
        ])));
    }

    public function store(BookRequest $request)
    {
        $this->save($request);

        return redirect()->route('admin.books.index')->with('success', 'Kitap eklendi.');
    }

    public function edit(Book $book)
    {
        return view('admin.books.form', $this->formData($book));
    }

    public function update(BookRequest $request, Book $book)
    {
        $this->save($request, $book->id);

        return redirect()->route('admin.books.index')->with('success', 'Kitap güncellendi.');
    }

    public function destroy(Book $book)
    {
        $book->delete();
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        return back()->with('success', 'Kitap silindi.');
    }

    /* ------------------------------------------------------------------ */

    private function formData(Book $book): array
    {
        return [
            'book'       => $book,
            'authors'    => Author::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ];
    }

    private function save(BookRequest $request, ?int $id = null): void
    {
        $data = $request->safe()->except('cover_image');
        $data['is_active']   = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        $newCover = null;
        if ($request->hasFile('cover_image')) {
            $newCover = $request->file('cover_image')->store('covers', 'public');
            if (! $newCover) {
                throw ValidationException::withMessages(['cover_image' => 'Kapak görseli kaydedilemedi.']);
            }

            $data['cover_image'] = $newCover;
        } elseif ($request->boolean('remove_cover')) {
            $data['cover_image'] = null;
        }

        try {
            $oldCover = DB::transaction(function () use ($id, $data) {
                $record = $id === null ? new Book() : Book::lockForUpdate()->findOrFail($id);
                $oldCover = $record->cover_image;
                $record->fill($data)->save();

                return $oldCover;
            }, 3);
        } catch (Throwable $error) {
            if ($newCover) {
                Storage::disk('public')->delete($newCover);
            }

            throw $error;
        }

        if ($oldCover && array_key_exists('cover_image', $data)) {
            Storage::disk('public')->delete($oldCover);
        }
    }
}
