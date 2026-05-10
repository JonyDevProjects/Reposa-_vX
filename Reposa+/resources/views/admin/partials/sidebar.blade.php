<div class="list-group shadow-sm mb-4">
    <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard
    </a>
    <a href="{{ route('admin.products') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
        <i class="bi bi-box-seam me-2"></i> Productos
    </a>
    <a href="{{ route('admin.orders') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
        <i class="bi bi-cart-check me-2"></i> Pedidos Globales
    </a>
</div>
