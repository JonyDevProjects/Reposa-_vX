<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'tracking_number',
        'carrier',
        'service_type',
        'service_name',
        'status',
        'shipping_cost',
        'recipient_name',
        'recipient_email',
        'recipient_phone',
        'street',
        'city',
        'zip_code',
        'province',
        'country',
        'estimated_delivery_date',
        'shipped_at',
        'delivered_at',
        'tracking_history',
        'label_data',
    ];

    protected function casts(): array
    {
        return [
            'shipping_cost' => 'decimal:2',
            'estimated_delivery_date' => 'date',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'tracking_history' => 'array',
            'label_data' => 'array',
        ];
    }

    const STATUS_PRE_REGISTERED = 'pre_registered';

    const STATUS_IN_TRANSIT = 'in_transit';

    const STATUS_AT_HUB = 'at_hub';

    const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';

    const STATUS_DELIVERED = 'delivered';

    const STATUS_INCIDENT = 'incident';

    const STATUS_LABELS = [
        self::STATUS_PRE_REGISTERED => 'Etiqueta creada / Pre-admitido',
        self::STATUS_IN_TRANSIT => 'En tránsito',
        self::STATUS_AT_HUB => 'En plataforma de distribución',
        self::STATUS_OUT_FOR_DELIVERY => 'En reparto',
        self::STATUS_DELIVERED => 'Entregado',
        self::STATUS_INCIDENT => 'Incidencia en reparto',
    ];

    const STATUS_COLORS = [
        self::STATUS_PRE_REGISTERED => 'info',
        self::STATUS_IN_TRANSIT => 'primary',
        self::STATUS_AT_HUB => 'warning',
        self::STATUS_OUT_FOR_DELIVERY => 'indigo',
        self::STATUS_DELIVERED => 'success',
        self::STATUS_INCIDENT => 'danger',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }
}
