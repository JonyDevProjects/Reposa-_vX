<x-mail::message>
# Pago no procesado

Hola {{ $order->user->name }},

Lamentablemente, el pago de tu pedido #{{ $order->id }} no pudo ser procesado.

**Motivo:** {{ $errorMessage }}

Tu pedido se mantiene en estado pendiente. Puedes intentar realizar el pago de nuevo desde tu carrito de compra.

<x-mail::button :url="config('app.url') . '/cart'">
Reintentar pago
</x-mail::button>

Si el problema persiste, no dudes en contactarnos.

Gracias,<br>
El equipo de {{ config('app.name') }}
</x-mail::message>
