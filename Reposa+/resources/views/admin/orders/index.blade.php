@extends('layouts.app')

@section('title', 'Historial Global de Pedidos')

@section('content')
<div class="row">
    <div class="col-md-3">
        @include('admin.partials.sidebar')
    </div>
    <div class="col-md-9">
        <h2 class="fw-bold mb-4">Historial Global de Transacciones</h2>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Productos</th>
                                <th>Total</th>
                                <th>Fecha</th>
                                <th>Estado</th>
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
