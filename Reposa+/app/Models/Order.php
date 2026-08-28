<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'order_date',
        'stripe_session_id',
        'payment_intent_id',
    ];

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
        self::STATUS_PENDING    => [self::STATUS_PROCESSING, self::STATUS_COMPLETED, self::STATUS_CANCELLED],
        self::STATUS_PROCESSING => [self::STATUS_SHIPPED, self::STATUS_CANCELLED],
        self::STATUS_SHIPPED    => [self::STATUS_DELIVERED],
        self::STATUS_DELIVERED  => [self::STATUS_COMPLETED, self::STATUS_REFUNDED],
        self::STATUS_COMPLETED  => [self::STATUS_REFUNDED],
        self::STATUS_CANCELLED  => [],
        self::STATUS_REFUNDED   => [],
    ];

    const STATUS_COLORS = [
        self::STATUS_PENDING    => 'warning',
        self::STATUS_PROCESSING => 'info',
        self::STATUS_SHIPPED    => 'primary',
        self::STATUS_DELIVERED  => 'success',
        self::STATUS_COMPLETED  => 'success',
        self::STATUS_CANCELLED  => 'danger',
        self::STATUS_REFUNDED   => 'secondary',
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
        return self::STATUSES[$status] ?? ucfirst($status);
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
}
