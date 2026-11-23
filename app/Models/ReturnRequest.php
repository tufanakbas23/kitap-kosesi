<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRequest extends Model
{
    public const TYPES = [
        'iade'    => 'İade',
        'degisim' => 'Değişim',
    ];

    public const REASONS = [
        'hasarli'       => 'Ürün hasarlı / kusurlu geldi',
        'yanlis_urun'   => 'Yanlış ürün gönderildi',
        'eksik_sayfa'   => 'Eksik / hatalı baskı',
        'vazgectim'     => 'Ürünü beğenmedim / vazgeçtim',
        'gec_teslimat'  => 'Teslimat çok gecikti',
        'diger'         => 'Diğer',
    ];

    public const STATUSES = [
        'beklemede'  => 'İnceleniyor',
        'onaylandi'  => 'Onaylandı',
        'reddedildi' => 'Reddedildi',
        'tamamlandi' => 'Tamamlandı',
    ];

    public const STATUS_COLORS = [
        'beklemede'  => 'warning',
        'onaylandi'  => 'info',
        'reddedildi' => 'danger',
        'tamamlandi' => 'success',
    ];

    protected $fillable = [
        'request_number', 'user_id', 'order_id', 'order_item_id', 'type',
        'reason', 'description', 'quantity', 'status', 'admin_note',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getReasonLabelAttribute(): string
    {
        return self::REASONS[$this->reason] ?? $this->reason;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    public function editableStatuses(): array
    {
        return match ($this->status) {
            'onaylandi' => array_diff_key(self::STATUSES, ['beklemede' => true]),
            'tamamlandi', 'reddedildi' => array_intersect_key(self::STATUSES, [$this->status => true]),
            default => self::STATUSES,
        };
    }

    public static function generateNumber(): string
    {
        do {
            $number = 'IT' . now()->format('ymd') . random_int(100, 999);
        } while (self::where('request_number', $number)->exists());

        return $number;
    }
}
