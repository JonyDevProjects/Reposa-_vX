<div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4 admin-sidebar-card">
    <div class="card-header bg-white py-3 px-3 border-bottom">
        <span class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.08em;">
            <i class="bi bi-shield-check me-1 text-primary"></i> Reposa+ Backoffice
        </span>
    </div>
    <div class="list-group list-group-flush admin-sidebar-nav">
        <a href="{{ route('admin.dashboard') }}" 
           class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2 px-3 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span><i class="bi bi-speedometer2 me-2"></i> {{ __('messages.admin.sidebar.dashboard') }}</span>
            @if(request()->routeIs('admin.dashboard'))
                <i class="bi bi-chevron-right small opacity-75"></i>
            @endif
        </a>
        <a href="{{ route('admin.orders') }}" 
           class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2 px-3 {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
            <span><i class="bi bi-receipt me-2"></i> {{ __('messages.admin.sidebar.orders') }}</span>
            @if(request()->routeIs('admin.orders*'))
                <i class="bi bi-chevron-right small opacity-75"></i>
            @endif
        </a>
        <a href="{{ route('admin.products') }}" 
           class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2 px-3 {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
            <span><i class="bi bi-box-seam me-2"></i> {{ __('messages.admin.sidebar.products') }}</span>
            @if(request()->routeIs('admin.products*'))
                <i class="bi bi-chevron-right small opacity-75"></i>
            @endif
        </a>
        <a href="{{ route('admin.categories') }}" 
           class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2 px-3 {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
            <span><i class="bi bi-tags me-2"></i> {{ __('messages.admin.sidebar.categories') }}</span>
            @if(request()->routeIs('admin.categories*'))
                <i class="bi bi-chevron-right small opacity-75"></i>
            @endif
        </a>
    </div>
    <div class="card-footer bg-light py-2 px-3 border-top d-flex align-items-center justify-content-between">
        <a href="{{ url('/catalog') }}" class="text-decoration-none text-muted small d-flex align-items-center gap-1 hover-navy">
            <i class="bi bi-arrow-left-short fs-6"></i>
            <span>{{ __('messages.admin.sidebar.storefront') }}</span>
        </a>
        <form action="{{ route('logout') }}" method="POST" class="d-inline mb-0">
            @csrf
            <button type="submit" class="btn btn-link text-danger p-0 small text-decoration-none d-flex align-items-center" title="{{ __('messages.nav.logout') }}">
                <i class="bi bi-box-arrow-right me-1"></i><span>{{ __('messages.nav.logout') }}</span>
            </button>
        </form>
    </div>
</div>
