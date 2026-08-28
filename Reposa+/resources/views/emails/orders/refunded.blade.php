<x-mail::message>
# Reembolso procesado

Hola {{ $order->user->name }},

Se ha procesado el reembolso de tu pedido **#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}**.

**Detalles del reembolso:**
| Concepto | Detalle |
| :--- | :--- |
| Pedido | #{{ str_pad($order->order_id ?? $order->id, 6, '0', STR_PAD_LEFT) }} |
| Importe reembolsado | {{ number_format($refund->amount, 2) }}€ |
| Motivo | {{ $refund->reason ?: 'Solicitado por el administrador' }} |

El importe será devuelto a tu método de pago original en un plazo de 5-10 días hábiles.

Si tienes alguna pregunta, no dudes en contactarnos.

<x-mail::button :url="config('app.url') . '/orders/' . $order->id">
Ver mi pedido
</x-mail::button>

Gracias,<br>
El equipo de {{ config('app.name') }}
</x-mail::message>
