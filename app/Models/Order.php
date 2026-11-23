<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUSES = [
        'beklemede'     => 'Onay Bekliyor',
        'hazirlaniyor'  => 'Hazırlanıyor',
        'kargoda'       => 'Kargoya Verildi',
        'teslim_edildi' => 'Teslim Edildi',
        'iptal'         => 'İptal Edildi',
    ];

    /** Durumların rozet renkleri (CSS sınıfı) */
    public const STATUS_COLORS = [
        'beklemede'     => 'warning',
        'hazirlaniyor'  => 'info',
        'kargoda'       => 'primary',
        'teslim_edildi' => 'success',
        'iptal'         => 'danger',
    ];

    public const CARGO_COMPANIES = ['Yurtiçi Kargo', 'Aras Kargo', 'MNG Kargo', 'PTT Kargo', 'Sürat Kargo'];

    protected $fillable = [
        'order_number', 'user_id', 'status', 'subtotal', 'discount', 'shipping_fee', 'total',
        'coupon_code', 'shipping_name', 'shipping_phone', 'shipping_city', 'shipping_district',
        'shipping_address', 'payment_method', 'card_holder', 'card_last4', 'installment',
        'cargo_company', 'tracking_number', 'note', 'delivered_at',
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'discount'     => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total'        => 'decimal:2',
        'delivered_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function returnRequests(): HasMany
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    /** Müşteri siparişi iptal edebilir mi? */
    public function isCancellable(): bool
    {
        return in_array($this->status, ['beklemede', 'hazirlaniyor'], true);
    }

    public function editableStatuses(): array
    {
        return match ($this->status) {
            'beklemede' => self::STATUSES,
            'hazirlaniyor' => array_diff_key(self::STATUSES, ['beklemede' => true]),
            'kargoda' => array_intersect_key(self::STATUSES, ['kargoda' => true, 'teslim_edildi' => true]),
            default => array_intersect_key(self::STATUSES, [$this->status => true]),
        };
    }

    /** İade/değişim talebi açılabilir mi? (teslimden sonraki 14 gün) */
    public function isReturnable(): bool
    {
        return $this->status === 'teslim_edildi'
            && $this->delivered_at
            && now()->betweenIncluded($this->delivered_at, $this->delivered_at->copy()->addDays(14));
    }

    public static function generateNumber(): string
    {
        do {
            $number = 'KK' . now()->format('ymd') . random_int(1000, 9999);
        } while (self::where('order_number', $number)->exists());

        return $number;
    }
}
