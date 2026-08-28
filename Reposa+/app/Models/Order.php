<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'order_date',
        'stripe_session_id',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_PENDING => 'Pendiente',
        self::STATUS_PROCESSING => 'Procesando',
        self::STATUS_SHIPPED => 'Enviado',
        self::STATUS_DELIVERED => 'Entregado',
        self::STATUS_COMPLETED => 'Completado',
        self::STATUS_CANCELLED => 'Cancelado',
    ];

    const ALLOWED_TRANSITIONS = [
        self::STATUS_PENDING    => [self::STATUS_PROCESSING, self::STATUS_COMPLETED, self::STATUS_CANCELLED],
        self::STATUS_PROCESSING => [self::STATUS_SHIPPED, self::STATUS_CANCELLED],
        self::STATUS_SHIPPED    => [self::STATUS_DELIVERED],
        self::STATUS_DELIVERED  => [self::STATUS_COMPLETED],
        self::STATUS_COMPLETED  => [],
        self::STATUS_CANCELLED  => [],
    ];

    const STATUS_COLORS = [
        self::STATUS_PENDING    => 'warning',
        self::STATUS_PROCESSING => 'info',
        self::STATUS_SHIPPED    => 'primary',
        self::STATUS_DELIVERED  => 'success',
        self::STATUS_COMPLETED  => 'success',
        self::STATUS_CANCELLED  => 'danger',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
