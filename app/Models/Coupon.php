<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'type', 'value', 'min_total', 'usage_limit', 'used_count', 'expires_at', 'is_active'];

    protected $casts = [
        'value'      => 'decimal:2',
        'min_total'  => 'decimal:2',
        'expires_at' => 'date',
        'is_active'  => 'boolean',
    ];

    /**
     * Kupon verilen sepet tutarı için kullanılabilir mi?
     * Kullanılamıyorsa hata mesajını, kullanılabiliyorsa null döner.
     */
    public function validationError(float $subtotal): ?string
    {
        if (! $this->is_active) {
            return 'Bu kupon aktif değil.';
        }
        if ($this->expires_at && $this->expires_at->endOfDay()->isPast()) {
            return 'Bu kuponun süresi dolmuş.';
        }
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return 'Bu kuponun kullanım limiti dolmuş.';
        }
        if ($subtotal < (float) $this->min_total) {
            return 'Bu kupon en az ' . number_format($this->min_total, 2, ',', '.') . ' TL tutarındaki sepetlerde geçerlidir.';
        }

        return null;
    }

    public function discountFor(float $subtotal): float
    {
        $discount = $this->type === 'percent'
            ? $subtotal * ((float) $this->value / 100)
            : (float) $this->value;

        return round(min($discount, $subtotal), 2);
    }

    public function getLabelAttribute(): string
    {
        return $this->type === 'percent'
            ? '%' . (int) $this->value . ' indirim'
            : number_format($this->value, 2, ',', '.') . ' TL indirim';
    }
}
