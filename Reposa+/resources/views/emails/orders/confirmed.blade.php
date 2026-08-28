<x-mail::message>
# ¡Gracias por tu pedido en Reposa+!

Hola {{ $order->user->name }},

Tu pedido **#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}** ha sido confirmado con éxito.

**Resumen del Pedido:**
| Producto | Cantidad | Precio |
| :--- | :---: | :--- |
@foreach ($order->orderItems as $item)
| {{ $item->product->name }} | {{ $item->quantity }} | {{ number_format($item->price_at_purchase, 2) }}€ |
@endforeach
| **Total** | | **{{ number_format($order->total_amount, 2) }}€** |

@if($order->stripe_session_id)
**Pago procesado vía Stripe Checkout**
@endif

Nos pondremos en marcha para que recibas tus almohadas lo antes posible.

<x-mail::button :url="$invoiceUrl">
Descargar Factura PDF
</x-mail::button>

<x-mail::button :url="config('app.url') . '/orders/' . $order->id">
Ver mi pedido
</x-mail::button>

Gracias,<br>
El equipo de {{ config('app.name') }}
</x-mail::message>
