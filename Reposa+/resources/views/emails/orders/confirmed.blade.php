<x-mail::message>
# {{ __('messages.email.confirmed.title') }}

{{ __('messages.email.confirmed.hello', ['name' => $order->user->name]) }}

{{ __('messages.email.confirmed.order_confirmed', ['id' => str_pad($order->id, 6, '0', STR_PAD_LEFT)]) }}

**{{ __('messages.email.confirmed.order_summary') }}**
| {{ __('messages.email.confirmed.product') }} | {{ __('messages.email.confirmed.quantity') }} | {{ __('messages.email.confirmed.price') }} |
| :--- | :---: | :--- |
@foreach ($order->orderItems as $item)
| {{ $item->product->name }} | {{ $item->quantity }} | {{ number_format($item->price_at_purchase, 2) }}€ |
@endforeach
| **{{ __('messages.email.confirmed.total') }}** | | **{{ number_format($order->total_amount, 2) }}€** |

@if($order->stripe_session_id)
**{{ __('messages.email.confirmed.stripe_payment') }}**
@endif

{{ __('messages.email.confirmed.shipping_msg') }}

<x-mail::button :url="$invoiceUrl">
{{ __('messages.email.confirmed.download_invoice') }}
</x-mail::button>

<x-mail::button :url="config('app.url') . '/orders/' . $order->id">
{{ __('messages.email.confirmed.view_order') }}
</x-mail::button>

{{ __('messages.email.confirmed.thanks') }}<br>
{{ __('messages.email.confirmed.team', ['name' => config('app.name')]) }}
</x-mail::message>
