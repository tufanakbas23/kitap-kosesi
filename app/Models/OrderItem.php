<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'book_id', 'title', 'author_name', 'price', 'quantity', 'total'];

    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function returnRequests(): HasMany
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function hasOpenRequest(): bool
    {
        return $this->returnRequests->whereIn('status', ['beklemede', 'onaylandi'])->isNotEmpty();
    }

    public function remainingReturnQuantity(): int
    {
        $completed = $this->returnRequests->where('status', 'tamamlandi')->sum('quantity');

        return max(0, $this->quantity - $completed);
    }

    public function restoreStock(int $quantity): void
    {
        if ($this->book_id) {
            Book::whereKey($this->book_id)->increment('stock', $quantity);
        }
    }
}
