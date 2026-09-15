<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_token',
        'shipping_name',
        'shipping_email',
        'shipping_phone',
        'shipping_street',
        'shipping_city',
        'shipping_zip_code',
        'shipping_province',
        'shipping_country',
        'shipping_service_type',
        'shipping_cost',
        'total_amount',
        'status',
        'order_date',
        'stripe_session_id',
        'payment_intent_id',
    ];

    public function isGuest(): bool
    {
        return $this->user_id === null;
    }

    public function getCustomerNameAttribute(): string
    {
        if (! empty($this->shipping_name)) {
            return $this->shipping_name;
        }

        if ($this->relationLoaded('user') || ! empty($this->user_id)) {
            return $this->user?->name ?? 'Cliente Reposa+';
        }

        return 'Cliente Reposa+';
    }

    public function getCustomerEmailAttribute(): string
    {
        if (! empty($this->shipping_email)) {
            return $this->shipping_email;
        }

        if ($this->relationLoaded('user') || ! empty($this->user_id)) {
            return $this->user?->email ?? '';
        }

        return '';
    }

    const STATUS_PENDING = 'pending';

    const STATUS_PROCESSING = 'processing';

    const STATUS_SHIPPED = 'shipped';

    const STATUS_DELIVERED = 'delivered';

    const STATUS_COMPLETED = 'completed';

    const STATUS_CANCELLED = 'cancelled';

    const STATUS_REFUNDED = 'refunded';

    const STATUSES = [
        self::STATUS_PENDING => 'Pendiente',
        self::STATUS_PROCESSING => 'Procesando',
        self::STATUS_SHIPPED => 'Enviado',
        self::STATUS_DELIVERED => 'Entregado',
        self::STATUS_COMPLETED => 'Completado',
        self::STATUS_CANCELLED => 'Cancelado',
        self::STATUS_REFUNDED => 'Reembolsado',
    ];

    const ALLOWED_TRANSITIONS = [
        self::STATUS_PENDING => [self::STATUS_PROCESSING, self::STATUS_COMPLETED, self::STATUS_CANCELLED],
        self::STATUS_PROCESSING => [self::STATUS_SHIPPED, self::STATUS_CANCELLED],
        self::STATUS_SHIPPED => [self::STATUS_DELIVERED],
        self::STATUS_DELIVERED => [self::STATUS_COMPLETED, self::STATUS_REFUNDED],
        self::STATUS_COMPLETED => [self::STATUS_REFUNDED],
        self::STATUS_CANCELLED => [],
        self::STATUS_REFUNDED => [],
    ];

    const STATUS_COLORS = [
        self::STATUS_PENDING => 'warning',
        self::STATUS_PROCESSING => 'info',
        self::STATUS_SHIPPED => 'primary',
        self::STATUS_DELIVERED => 'success',
        self::STATUS_COMPLETED => 'success',
        self::STATUS_CANCELLED => 'danger',
        self::STATUS_REFUNDED => 'secondary',
    ];

    public static function getAllowedTransitions(string $currentStatus): array
    {
        return self::ALLOWED_TRANSITIONS[$currentStatus] ?? [];
    }

    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::ALLOWED_TRANSITIONS[$from] ?? []);
    }

    public static function getStatusLabel(string $status): string
    {
        $key = 'messages.order.status.'.$status;
        $translated = __($key);

        return $translated !== $key ? $translated : ucfirst($status);
    }

    public static function getStatusColor(string $status): string
    {
        return self::STATUS_COLORS[$status] ?? 'secondary';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }
}
