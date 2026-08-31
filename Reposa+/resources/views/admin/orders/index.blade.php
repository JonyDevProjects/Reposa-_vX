@extends('layouts.app')

@section('title', __('messages.admin.orders.title'))

@section('content')
<div class="row">
    <div class="col-md-3">
        @include('admin.partials.sidebar')
    </div>
    <div class="col-md-9">
        <h2 class="fw-bold mb-4">{{ __('messages.admin.orders.history') }}</h2>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>{{ __('messages.admin.orders.customer') }}</th>
                                <th>{{ __('messages.admin.orders.products') }}</th>
                                <th>Total</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>{{ __('messages.admin.orders.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>
                                    <div class="fw-bold">{{ $order->user->name }}</div>
                                    <small class="text-muted">{{ $order->user->email }}</small>
                                </td>
                                <td>
                                    <ul class="list-unstyled mb-0 small">
                                        @foreach($order->orderItems as $item)
                                        <li>{{ $item->quantity }}x {{ $item->product->name }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="fw-bold text-primary">{{ number_format($order->total_amount, 2) }}€</td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @php $transitions = \App\Models\Order::getAllowedTransitions($order->status); @endphp
                                    @if(!empty($transitions))
                                        <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="{{ $order->status }}" disabled selected>
                                                    {{ \App\Models\Order::getStatusLabel($order->status) }}
                                                </option>
                                                @foreach($transitions as $transition)
                                                    <option value="{{ $transition }}">
                                                        → {{ \App\Models\Order::getStatusLabel($transition) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    @else
                                        <span class="badge bg-{{ \App\Models\Order::getStatusColor($order->status) }}">
                                            {{ \App\Models\Order::getStatusLabel($order->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if(in_array($order->status, ['completed', 'delivered']) && $order->payment_intent_id && !$order->refunds()->where('status', 'succeeded')->exists())
                                        <button class="btn btn-sm btn-outline-danger" type="button"
                                                data-bs-toggle="modal" data-bs-target="#refundModal{{ $order->id }}">
                                            <i class="bi bi-arrow-counterclockwise"></i> {{ __('messages.admin.orders.refund') }}
                                        </button>

                                        <div class="modal fade" id="refundModal{{ $order->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('admin.orders.refund', $order) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ __('messages.admin.orders.refund_title') }}{{ $order->id }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>{{ __('messages.admin.orders.refund_desc') }} <strong>{{ number_format($order->total_amount, 2) }}€</strong> {{ __('messages.admin.orders.refund_to_client') }} <strong>{{ $order->user->name }}</strong>.</p>
                                                            <p class="text-muted small">{{ __('messages.admin.orders.stock_restore') }}</p>
                                                            <div class="mb-3">
                                                                <label for="reason{{ $order->id }}" class="form-label">{{ __('messages.admin.orders.reason_optional') }}</label>
                                                                <input type="text" class="form-control" id="reason{{ $order->id }}" name="reason" maxlength="500" placeholder="Ej: Producto defectuoso, solicitud del cliente...">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.admin.orders.cancel') }}</button>
                                                            <button type="submit" class="btn btn-danger" onclick="return confirm('¿Confirmar reembolso de {{ number_format($order->total_amount, 2) }}€?')">
                                                                {{ __('messages.admin.orders.confirm_refund') }}
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($order->status === 'refunded')
                                        <span class="badge bg-secondary">{{ __('messages.admin.orders.refunded') }}</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
