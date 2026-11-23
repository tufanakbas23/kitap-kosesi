<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasSlug;

    private const SEARCH_CHARACTERS = [
        'Ç' => 'c', 'ç' => 'c', 'Ğ' => 'g', 'ğ' => 'g',
        'İ' => 'i', 'ı' => 'i', 'Ö' => 'o', 'ö' => 'o',
        'Ş' => 's', 'ş' => 's', 'Ü' => 'u', 'ü' => 'u',
    ];

    protected string $slugSource = 'title';

    protected $fillable = [
        'title', 'slug', 'author_id', 'publisher_id', 'category_id', 'isbn',
        'price', 'list_price', 'stock', 'page_count', 'publish_year', 'language',
        'cover_type', 'paper_type', 'dimensions', 'description', 'cover_image',
        'cover_color', 'is_active', 'is_featured', 'sales_count',
    ];

    protected $casts = [
        'price'       => 'decimal:2',
        'list_price'  => 'decimal:2',
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
    ];

    /* ---------------- İlişkiler ---------------- */

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('is_approved', true);
    }

    /* ---------------- Scope'lar ---------------- */

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('books.is_active', true)
            ->whereHas('category', fn (Builder $category) => $category->where('is_active', true));
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = mb_strtolower(strtr(trim($term), self::SEARCH_CHARACTERS));
        if ($term === '') {
            return $query;
        }

        $pattern = '%' . strtr($term, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';

        return $query->where(function (Builder $books) use ($pattern) {
            $this->matchSearchColumn($books, 'title', $pattern)
                ->orWhereRaw("isbn LIKE ? ESCAPE '!'", [$pattern])
                ->orWhereHas('author', fn (Builder $author) => $this->matchSearchColumn($author, 'name', $pattern))
                ->orWhereHas('publisher', fn (Builder $publisher) => $this->matchSearchColumn($publisher, 'name', $pattern));
        });
    }

    private function matchSearchColumn(Builder $query, string $column, string $pattern): Builder
    {
        $expression = $query->getQuery()->getGrammar()->wrap($column);
        $bindings = [];

        foreach (self::SEARCH_CHARACTERS as $character => $replacement) {
            $expression = "REPLACE({$expression}, ?, ?)";
            $bindings[] = $character;
            $bindings[] = $replacement;
        }

        return $query->whereRaw("LOWER({$expression}) LIKE ? ESCAPE '!'", [...$bindings, $pattern]);
    }

    public function scopeDiscounted(Builder $q): Builder
    {
        return $q->whereNotNull('list_price')->whereColumn('list_price', '>', 'price');
    }

    /* ---------------- Yardımcılar ---------------- */

    public function getDiscountRateAttribute(): int
    {
        if (! $this->list_price || $this->list_price <= $this->price) {
            return 0;
        }

        return (int) round((1 - $this->price / $this->list_price) * 100);
    }

    public function getInStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    public function isVisible(): bool
    {
        return $this->is_active && $this->category->is_active;
    }

    public function getAverageRatingAttribute(): float
    {
        return round((float) $this->approvedReviews()->avg('rating'), 1);
    }
}
