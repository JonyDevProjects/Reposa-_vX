<x-mail::message>
# {{ __('messages.email.refunded.title') }}

{{ __('messages.email.confirmed.hello', ['name' => $order->user->name]) }}

{{ __('messages.email.refunded.order_refunded', ['id' => str_pad($order->id, 6, '0', STR_PAD_LEFT)]) }}

**{{ __('messages.email.refunded.details') }}**
| {{ __('messages.email.refunded.concept') }} | {{ __('messages.email.refunded.detail') }} |
| :--- | :--- |
| {{ __('messages.email.refunded.order') }} | #{{ str_pad($order->order_id ?? $order->id, 6, '0', STR_PAD_LEFT) }} |
| {{ __('messages.email.refunded.amount') }} | {{ number_format($refund->amount, 2) }}€ |
| {{ __('messages.email.refunded.reason') }} | {{ $refund->reason ?: __('messages.email.refunded.admin_reason') }} |

{{ __('messages.email.refunded.return_msg') }}

{{ __('messages.email.refunded.question') }}

<x-mail::button :url="config('app.url') . '/orders/' . $order->id">
{{ __('messages.email.refunded.view_order') }}
</x-mail::button>

{{ __('messages.email.refunded.thanks') }}<br>
{{ __('messages.email.refunded.team', ['name' => config('app.name')]) }}
</x-mail::message>
