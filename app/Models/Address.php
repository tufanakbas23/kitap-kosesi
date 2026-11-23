<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'title', 'full_name', 'phone', 'city', 'district',
        'neighborhood', 'address_line', 'postal_code', 'is_default',
    ];

    protected $casts = ['is_default' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute(): string
    {
        return collect([$this->neighborhood, $this->address_line, $this->district . ' / ' . $this->city, $this->postal_code])
            ->filter()
            ->implode(', ');
    }
}
