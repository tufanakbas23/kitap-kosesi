<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Kayıt için mevcut bağlantıları koruyan benzersiz bir slug üretir.
 * Kullanan model, slug'ın hangi alandan üretileceğini $slugSource ile belirtir.
 */
trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::saving(function ($model) {
            if (empty($model->slug)) {
                $source = $model->slugSource ?? 'name';
                $model->slug = static::uniqueSlug($model->{$source}, $model->id);
            }
        });
    }

    protected static function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'kayit';
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
