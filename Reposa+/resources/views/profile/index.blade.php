@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="text-center mb-4">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <h5 class="fw-bold mb-0">{{ $user->name }}</h5>
                    <p class="text-muted small">{{ $user->email }}</p>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#overview" class="list-group-item list-group-item-action border-0 px-0 active"><i class="bi bi-person me-2"></i> Mi Perfil</a>
                    <a href="#orders" class="list-group-item list-group-item-action border-0 px-0"><i class="bi bi-box me-2"></i> Mis Pedidos</a>
                    <a href="#addresses" class="list-group-item list-group-item-action border-0 px-0"><i class="bi bi-geo-alt me-2"></i> Mis Direcciones</a>
                    <a href="#favorites" class="list-group-item list-group-item-action border-0 px-0"><i class="bi bi-heart me-2"></i> Favoritos</a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="list-group-item list-group-item-action border-0 px-0 text-danger"><i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Overview Section -->
            <div id="overview" class="mb-5">
                <!-- Resumen de Pedidos (Vía SQL View) -->
                @if($user->orderSummary)
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm bg-primary bg-opacity-10 rounded-4 p-3 d-flex flex-row align-items-center">
                            <div class="fs-1 text-primary me-3"><i class="bi bi-wallet2"></i></div>
                            <div>
                                <h6 class="text-muted small mb-1">Total Gastado</h6>
                                <h4 class="fw-bold mb-0 text-primary">{{ number_format($user->orderSummary->total_spent, 2) }}€</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm bg-primary bg-opacity-10 rounded-4 p-3 d-flex flex-row align-items-center">
                            <div class="fs-1 text-primary me-3"><i class="bi bi-bag-check"></i></div>
                            <div>
                                <h6 class="text-muted small mb-1">Pedidos Realizados</h6>
                                <h4 class="fw-bold mb-0 text-primary">{{ $user->orderSummary->total_orders }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="card border-0 shadow-sm rounded-4 p-5 mb-4">
                    <h4 class="fw-bold mb-4">Datos Personales</h4>
                    <form method="POST" action="{{ route('user-profile-information.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Nombre Completo</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Correo Electrónico</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Teléfono</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->profile->phone ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Preferencia de Sueño</label>
                                <input type="text" name="sleep_preference" class="form-control" value="{{ old('sleep_preference', $user->profile->sleep_preference ?? '') }}">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-4">Guardar Cambios</button>
                    </form>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-5">
                    <h4 class="fw-bold mb-4">Cambiar Contraseña</h4>
                    <form method="POST" action="{{ route('user-password.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">Contraseña Actual</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Nueva Contraseña</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Confirmar Nueva Contraseña</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-outline-primary mt-4">Actualizar Contraseña</button>
                    </form>
                </div>
            </div>

            <!-- Orders Section -->
            <div id="orders" class="mb-5">
                <div class="card border-0 shadow-sm rounded-4 p-5">
                    <h4 class="fw-bold mb-4">Mis Pedidos Recientes</h4>
                    @if($user->orders->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-bag-x fs-1 text-muted"></i>
                            <p class="mt-3">Aún no has realizado ningún pedido.</p>
                            <a href="/catalog" class="btn btn-primary">Ir a la tienda</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>ID Pedido</th>
                                        <th>Fecha</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->orders as $order)
                                        <tr>
                                            <td class="fw-bold">#{{ $order->id }}</td>
                                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                            <td>{{ number_format($order->total_amount, 2) }}€</td>
                                            <td>
                                                <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : 'warning' }} px-3 py-2">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">Ver detalles</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Addresses Section -->
            <div id="addresses">
                <div class="card border-0 shadow-sm rounded-4 p-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold mb-0">Mis Direcciones de Envío</h4>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal"><i class="bi bi-plus-lg me-1"></i> Nueva Dirección</button>
                    </div>
                    @if($user->addresses->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-geo fs-1 text-muted"></i>
                            <p class="mt-3">No tienes direcciones registradas.</p>
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($user->addresses as $address)
                                <div class="col-md-6">
                                    <div class="border rounded-4 p-4 position-relative">
                                        @if($address->is_main)
                                            <span class="badge bg-secondary position-absolute top-0 end-0 m-3">Principal</span>
                                        @endif
                                        <h6 class="fw-bold">{{ $address->street }}</h6>
                                        <p class="text-muted small mb-0">{{ $address->zip_code }} - {{ $address->city }}</p>
                                        <div class="mt-3 d-flex gap-3">
                                            <a href="#" class="small text-decoration-none" data-bs-toggle="modal" data-bs-target="#editAddressModal{{ $address->id }}">Editar</a>
                                            <form action="{{ route('profile.address.destroy', $address) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta dirección?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link btn-sm text-danger p-0 text-decoration-none">Eliminar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Address Modal -->
                                <div class="modal fade" id="editAddressModal{{ $address->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('profile.address.update', $address) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Editar Dirección</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Calle</label>
                                                        <input type="text" name="street" class="form-control" value="{{ $address->street }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Ciudad</label>
                                                        <input type="text" name="city" class="form-control" value="{{ $address->city }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Código Postal</label>
                                                        <input type="text" name="zip_code" class="form-control" value="{{ $address->zip_code }}" required>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="hidden" name="is_main" value="0">
                                                        <input class="form-check-input" type="checkbox" name="is_main" value="1" id="isMain{{ $address->id }}" {{ $address->is_main ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="isMain{{ $address->id }}">
                                                            Marcar como principal
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Favorites Section -->
            <div id="favorites" class="mt-5 mb-5">
                <div class="card border-0 shadow-sm rounded-4 p-5">
                    <h4 class="fw-bold mb-4">{{ __('messages.favorites.title') }}</h4>
                    @if($user->favorites->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-heart fs-1 text-muted"></i>
                            <p class="mt-3 text-muted">{{ __('messages.favorites.empty') }}</p>
                            <a href="/catalog" class="btn btn-primary mt-2">{{ __('messages.favorites.btn_catalog') }}</a>
                        </div>
                    @else
                        <div class="row g-4">
                            @foreach($user->favorites as $product)
                                <div class="col-md-6" id="fav-card-{{ $product->id }}">
                                    <div class="card h-100 shadow-sm border-0 position-relative">
                                        <button type="button" 
                                                class="btn btn-danger text-white btn-sm rounded-circle position-absolute top-0 end-0 m-3 btn-favorite" 
                                                data-product-id="{{ $product->id }}"
                                                data-url="{{ route('favorites.toggle', $product) }}"
                                                title="{{ __('messages.favorites.removed') }}">
                                            <i class="bi bi-heart-fill"></i>
                                        </button>
                                        <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">
                                            <img src="https://placehold.co/400x300/182447/ffffff?text={{ urlencode($product->name) }}" class="card-img-top" alt="{{ $product->name }}">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="badge bg-light text-primary border">{{ $product->material }}</span>
                                                    <span class="text-muted small"><i class="bi bi-star-fill text-warning"></i> 4.8</span>
                                                </div>
                                                <h5 class="card-title fw-bold mb-1">{{ $product->name }}</h5>
                                                <p class="card-text text-muted small mb-3">{{ Str::limit($product->description, 60) }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fs-5 fw-bold text-primary">{{ number_format($product->price, 2) }}€</span>
                                                    <form action="{{ route('cart.add', $product) }}" method="POST" class="m-0">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-primary btn-sm rounded-circle"><i class="bi bi-cart-plus"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('profile.address.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Nueva Dirección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Calle</label>
                        <input type="text" name="street" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ciudad</label>
                        <input type="text" name="city" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Código Postal</label>
                        <input type="text" name="zip_code" class="form-control" required>
                    </div>
                    <div class="form-check">
                        <input type="hidden" name="is_main" value="0">
                        <input class="form-check-input" type="checkbox" name="is_main" value="1" id="isMainNew">
                        <label class="form-check-label" for="isMainNew">
                            Marcar como principal
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Dirección</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
