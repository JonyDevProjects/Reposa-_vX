@extends('layouts.app')

@section('title', __('messages.profile.title'))

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
                    <a href="#overview" class="list-group-item list-group-item-action border-0 px-0 active"><i class="bi bi-person me-2"></i> {{ __('messages.profile.sidebar.profile') }}</a>
                    <a href="#orders" class="list-group-item list-group-item-action border-0 px-0"><i class="bi bi-box me-2"></i> {{ __('messages.profile.sidebar.orders') }}</a>
                    <a href="#addresses" class="list-group-item list-group-item-action border-0 px-0"><i class="bi bi-geo-alt me-2"></i> {{ __('messages.profile.sidebar.addresses') }}</a>
                    <a href="#favorites" class="list-group-item list-group-item-action border-0 px-0"><i class="bi bi-heart me-2"></i> Favoritos</a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="list-group-item list-group-item-action border-0 px-0 text-danger"><i class="bi bi-box-arrow-right me-2"></i> {{ __('messages.nav.logout') }}</button>
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
                                <h6 class="text-muted small mb-1">{{ __('messages.profile.total_spent') }}</h6>
                                <h4 class="fw-bold mb-0 text-primary">{{ number_format($user->orderSummary->total_spent, 2) }}€</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm bg-primary bg-opacity-10 rounded-4 p-3 d-flex flex-row align-items-center">
                            <div class="fs-1 text-primary me-3"><i class="bi bi-bag-check"></i></div>
                            <div>
                                <h6 class="text-muted small mb-1">{{ __('messages.profile.orders_placed') }}</h6>
                                <h4 class="fw-bold mb-0 text-primary">{{ $user->orderSummary->total_orders }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="card border-0 shadow-sm rounded-4 p-5 mb-4">
                    <h4 class="fw-bold mb-4">{{ __('messages.profile.personal_data') }}</h4>
                    <form method="POST" action="{{ route('user-profile-information.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('messages.profile.full_name') }}</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('messages.profile.email') }}</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('messages.profile.phone') }}</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->profile->phone ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('messages.profile.sleep_preference') }}</label>
                                <input type="text" name="sleep_preference" class="form-control" value="{{ old('sleep_preference', $user->profile->sleep_preference ?? '') }}">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-4">{{ __('messages.profile.save_changes') }}</button>
                    </form>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-5">
                    <h4 class="fw-bold mb-4">{{ __('messages.profile.change_password') }}</h4>
                    <form method="POST" action="{{ route('user-password.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-bold text-muted">{{ __('messages.profile.current_password') }}</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('messages.profile.new_password') }}</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">{{ __('messages.profile.confirm_password') }}</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-outline-primary mt-4">{{ __('messages.profile.update_password') }}</button>
                    </form>
                </div>
            </div>

            <!-- Orders Section -->
            <div id="orders" class="mb-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <h4 class="fw-bold mb-4 text-navy">{{ __('messages.profile.recent_orders') }}</h4>
                    @if($user->orders->isEmpty())
                        <x-empty-state 
                            icon="bi-bag-check"
                            :title="__('messages.profile.no_orders')"
                            description="Tus pedidos y facturas oficiales de descanso aparecerán aquí en cuanto comiences a dormir mejor."
                            actionUrl="/catalog"
                            :actionText="__('messages.profile.go_to_store')"
                            actionIcon="bi-chevron-right"
                        />
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.profile.order_id') }}</th>
                                        <th>{{ __('messages.profile.date') }}</th>
                                        <th>{{ __('messages.profile.total') }}</th>
                                        <th>{{ __('messages.profile.status') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->orders as $order)
                                        <tr>
                                            <td class="fw-bold">#{{ $order->id }}</td>
                                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                            <td class="tabular-nums">{{ number_format($order->total_amount, 2) }}€</td>
                                            <td>
                                                <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : 'warning' }} px-3 py-2">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">{{ __('messages.profile.view_details') }}</a>
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
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <h4 class="fw-bold mb-0 text-navy">{{ __('messages.profile.shipping_addresses') }}</h4>
                        <button class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                            <i class="bi bi-plus-lg me-1"></i>{{ __('messages.profile.new_address') }}
                        </button>
                    </div>
                    @if($user->addresses->isEmpty())
                        <x-empty-state 
                            icon="bi-geo-alt"
                            :title="__('messages.profile.no_addresses')"
                            description="Añade tu dirección postal de entrega para recibir tus almohadas en 24/48h con total comodidad."
                        >
                            <div class="mt-2">
                                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                                    <i class="bi bi-plus-lg me-1"></i>{{ __('messages.profile.new_address') }}
                                </button>
                            </div>
                        </x-empty-state>
                    @else
                        <div class="row g-3">
                            @foreach($user->addresses as $address)
                                <div class="col-md-6">
                                    <div class="border rounded-4 p-4 position-relative bg-white shadow-2xs h-100 d-flex flex-column">
                                        @if($address->is_main)
                                            <span class="badge bg-secondary position-absolute top-0 end-0 m-3">{{ __('messages.profile.main_address') }}</span>
                                        @endif
                                        <h6 class="fw-bold text-navy mb-1 text-break">{{ $address->street }}</h6>
                                        <p class="text-muted small mb-0">{{ $address->zip_code }} - {{ $address->city }}</p>
                                        <div class="mt-auto pt-3 d-flex gap-3">
                                            <a href="#" class="small text-decoration-none fw-semibold" data-bs-toggle="modal" data-bs-target="#editAddressModal{{ $address->id }}">
                                                <i class="bi bi-pencil me-1"></i>{{ __('messages.profile.edit') }}
                                            </a>
                                            <form action="{{ route('profile.address.destroy', $address) }}" method="POST" onsubmit="return confirm('{{ __('messages.profile.confirm_delete_address') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link btn-sm text-danger p-0 text-decoration-none small">
                                                    <i class="bi bi-trash me-1"></i>{{ __('messages.profile.delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Address Modal -->
                                <div class="modal fade" id="editAddressModal{{ $address->id }}" tabindex="-1" aria-labelledby="editAddressTitle{{ $address->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg rounded-4">
                                            <form action="{{ route('profile.address.update', $address) }}" method="POST" class="needs-validation">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header border-bottom-0 pb-0">
                                                    <h5 class="modal-title fw-bold text-navy" id="editAddressTitle{{ $address->id }}">{{ __('messages.profile.edit_address') }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body py-3">
                                                    <div class="mb-3">
                                                        <label for="edit_street_{{ $address->id }}" class="form-label small fw-semibold text-navy">{{ __('messages.profile.street') }} *</label>
                                                        <input type="text" 
                                                               id="edit_street_{{ $address->id }}" 
                                                               name="street" 
                                                               class="form-control rounded-3" 
                                                               value="{{ $address->street }}" 
                                                               required 
                                                               minlength="3" 
                                                               maxlength="255" 
                                                               autocomplete="street-address">
                                                        <div class="invalid-feedback small">Por favor, introduce una dirección válida.</div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="edit_city_{{ $address->id }}" class="form-label small fw-semibold text-navy">{{ __('messages.profile.city') }} *</label>
                                                        <input type="text" 
                                                               id="edit_city_{{ $address->id }}" 
                                                               name="city" 
                                                               class="form-control rounded-3" 
                                                               value="{{ $address->city }}" 
                                                               required 
                                                               minlength="2" 
                                                               maxlength="100" 
                                                               autocomplete="address-level2">
                                                        <div class="invalid-feedback small">Por favor, indica la localidad o ciudad.</div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="edit_zip_{{ $address->id }}" class="form-label small fw-semibold text-navy">{{ __('messages.profile.zip_code') }} *</label>
                                                        <input type="text" 
                                                               id="edit_zip_{{ $address->id }}" 
                                                               name="zip_code" 
                                                               class="form-control rounded-3" 
                                                               value="{{ $address->zip_code }}" 
                                                               required 
                                                               pattern="[A-Za-z0-9\s\-]{3,10}" 
                                                               maxlength="10" 
                                                               autocomplete="postal-code">
                                                        <div class="invalid-feedback small">Introduce un código postal válido (ej. 28013).</div>
                                                    </div>
                                                    <div class="form-check mt-3">
                                                        <input type="hidden" name="is_main" value="0">
                                                        <input class="form-check-input" type="checkbox" name="is_main" value="1" id="isMain{{ $address->id }}" {{ $address->is_main ? 'checked' : '' }}>
                                                        <label class="form-check-label small" for="isMain{{ $address->id }}">
                                                            {{ __('messages.profile.mark_as_main') }}
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0 pt-0">
                                                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">{{ __('messages.profile.cancel') }}</button>
                                                    <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('messages.profile.save_changes') }}</button>
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
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                    <h4 class="fw-bold mb-4 text-navy">{{ __('messages.favorites.title') }}</h4>
                    @if($user->favorites->isEmpty())
                        <x-empty-state 
                            icon="bi-heart"
                            :title="__('messages.favorites.empty_title')"
                            :description="__('messages.favorites.empty_subtitle')"
                            :highlight="__('messages.favorites.empty_social_proof')"
                            actionUrl="/catalog"
                            :actionText="__('messages.favorites.btn_catalog')"
                            actionIcon="bi-chevron-right"
                        />

                        @if(isset($recommendedProducts) && $recommendedProducts->isNotEmpty())
                            <div class="mt-5 pt-4 border-top">
                                <div class="mb-3">
                                    <h5 class="fw-bold text-navy mb-1">{{ __('messages.favorites.recommended_title') }}</h5>
                                    <p class="text-muted small mb-0">{{ __('messages.favorites.recommended_subtitle') }}</p>
                                </div>
                                <div class="row g-3">
                                    @foreach($recommendedProducts as $rec)
                                        <div class="col-md-4">
                                            <x-product-card :product="$rec" :favoriteIds="[]" />
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="row g-4">
                            @php $userFavIds = $user->favorites->pluck('id')->toArray(); @endphp
                            @foreach($user->favorites as $product)
                                <div class="col-md-6" id="fav-card-{{ $product->id }}">
                                    <x-product-card :product="$product" :favoriteIds="$userFavIds" />
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
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('profile.address.store') }}" method="POST" class="needs-validation">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-navy" id="addAddressTitle">{{ __('messages.profile.new_address') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label for="add_street" class="form-label small fw-semibold text-navy">{{ __('messages.profile.street') }} *</label>
                        <input type="text" 
                               id="add_street" 
                               name="street" 
                               class="form-control rounded-3" 
                               required 
                               minlength="3" 
                               maxlength="255" 
                               autocomplete="street-address" 
                               placeholder="Ej. Calle Gran Vía 42, 3º B">
                        <div class="invalid-feedback small">Por favor, introduce una dirección de entrega válida.</div>
                    </div>
                    <div class="mb-3">
                        <label for="add_city" class="form-label small fw-semibold text-navy">{{ __('messages.profile.city') }} *</label>
                        <input type="text" 
                               id="add_city" 
                               name="city" 
                               class="form-control rounded-3" 
                               required 
                               minlength="2" 
                               maxlength="100" 
                               autocomplete="address-level2" 
                               placeholder="Ej. Madrid">
                        <div class="invalid-feedback small">Por favor, indica tu ciudad o localidad.</div>
                    </div>
                    <div class="mb-3">
                        <label for="add_zip" class="form-label small fw-semibold text-navy">{{ __('messages.profile.zip_code') }} *</label>
                        <input type="text" 
                               id="add_zip" 
                               name="zip_code" 
                               class="form-control rounded-3" 
                               required 
                               pattern="[A-Za-z0-9\s\-]{3,10}" 
                               maxlength="10" 
                               autocomplete="postal-code" 
                               placeholder="Ej. 28013">
                        <div class="invalid-feedback small">Introduce un código postal válido (ej. 28013).</div>
                    </div>
                    <div class="form-check mt-3">
                        <input type="hidden" name="is_main" value="0">
                        <input class="form-check-input" type="checkbox" name="is_main" value="1" id="isMainNew">
                        <label class="form-check-label small" for="isMainNew">
                            {{ __('messages.profile.mark_as_main') }}
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">{{ __('messages.profile.cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">{{ __('messages.profile.save_address') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
