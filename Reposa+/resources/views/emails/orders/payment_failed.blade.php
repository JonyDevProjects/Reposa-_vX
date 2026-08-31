<x-mail::message>
# {{ __('messages.email.payment_failed.title') }}

{{ __('messages.email.payment_failed.hello', ['name' => $order->user->name]) }}

{{ __('messages.email.payment_failed.message', ['id' => $order->id]) }}

**{{ __('messages.email.payment_failed.reason') }}** {{ $errorMessage }}

{{ __('messages.email.payment_failed.pending_msg') }}

<x-mail::button :url="config('app.url') . '/cart'">
{{ __('messages.email.payment_failed.retry') }}
</x-mail::button>

{{ __('messages.email.payment_failed.persist') }}

{{ __('messages.email.payment_failed.thanks') }}<br>
{{ __('messages.email.payment_failed.team', ['name' => config('app.name')]) }}
</x-mail::message>
