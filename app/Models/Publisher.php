<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Publisher extends Model
{
    use HasSlug;

    protected $fillable = ['name', 'slug', 'description'];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
