<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    use HasSlug;

    protected $fillable = ['name', 'slug', 'bio'];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
