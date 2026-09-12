@php
    $mobileCartCount = Auth::check() 
        ? \App\Models\CartItem::where('user_id', Auth::id())->sum('quantity')
        : collect(session()->get('cart', []))->sum('quantity');
@endphp

<nav class="mobile-nav-bar d-md-none" aria-label="{{ __('messages.mobile.nav_label') }}">
    <div class="mobile-nav-inner">
        <!-- Catalog -->
        <a href="/catalog" 
           class="mobile-nav-item {{ request()->is('catalog*') && !request()->has('q') ? 'active' : '' }}" 
           aria-label="{{ __('messages.mobile.nav_catalog') }}"
           @if(request()->is('catalog*') && !request()->has('q')) aria-current="page" @endif>
            <i class="bi {{ request()->is('catalog*') && !request()->has('q') ? 'bi-grid-fill' : 'bi-grid' }} mobile-nav-icon"></i>
            <span class="mobile-nav-label">{{ __('messages.mobile.nav_catalog') }}</span>
        </a>

        <!-- Search (Opens Ergonomic Mobile Search Drawer/Modal) -->
        <button type="button" 
                class="mobile-nav-item mobile-nav-btn {{ request()->has('q') ? 'active' : '' }}" 
                data-bs-toggle="modal" 
                data-bs-target="#mobileSearchModal" 
                aria-label="{{ __('messages.mobile.nav_search') }}">
            <i class="bi bi-search mobile-nav-icon"></i>
            <span class="mobile-nav-label">{{ __('messages.mobile.nav_search') }}</span>
        </button>

        <!-- Cart with Reactive Spring Badge -->
        <a href="/cart" 
           class="mobile-nav-item position-relative {{ request()->is('cart*') ? 'active' : '' }}" 
           aria-label="{{ __('messages.mobile.nav_cart') }}"
           @if(request()->is('cart*')) aria-current="page" @endif>
            <div class="mobile-nav-icon-wrap position-relative">
                <i class="bi {{ request()->is('cart*') ? 'bi-cart-fill' : 'bi-cart3' }} mobile-nav-icon"></i>
                <span id="mobile-cart-badge" 
                      class="mobile-cart-badge js-cart-badge badge rounded-pill bg-danger" 
                      aria-live="polite">
                    {{ $mobileCartCount }}
                </span>
            </div>
            <span class="mobile-nav-label">{{ __('messages.mobile.nav_cart') }}</span>
        </a>

        <!-- Profile / Auth -->
        @guest
            <a href="/login" 
               class="mobile-nav-item {{ request()->is('login*') || request()->is('register*') ? 'active' : '' }}" 
               aria-label="{{ __('messages.mobile.nav_login') }}"
               @if(request()->is('login*')) aria-current="page" @endif>
                <i class="bi bi-person mobile-nav-icon"></i>
                <span class="mobile-nav-label">{{ __('messages.mobile.nav_login') }}</span>
            </a>
        @else
            <a href="/profile" 
               class="mobile-nav-item {{ request()->is('profile*') ? 'active' : '' }}" 
               aria-label="{{ __('messages.mobile.nav_profile') }}"
               @if(request()->is('profile*')) aria-current="page" @endif>
                <i class="bi {{ request()->is('profile*') ? 'bi-person-check-fill' : 'bi-person-check' }} mobile-nav-icon"></i>
                <span class="mobile-nav-label">{{ __('messages.mobile.nav_profile') }}</span>
            </a>
        @endguest
    </div>
</nav>

<!-- Accessible Mobile Search Modal -->
<div class="modal fade mobile-search-modal d-md-none" 
     id="mobileSearchModal" 
     tabindex="-1" 
     aria-labelledby="mobileSearchModalTitle" 
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-top m-0">
        <div class="modal-content border-0 rounded-0 rounded-bottom-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-navy h6 mb-0" id="mobileSearchModalTitle">
                    <i class="bi bi-search text-primary me-2"></i>{{ __('messages.mobile.search_modal_title') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar búsqueda"></button>
            </div>
            <div class="modal-body pt-3 pb-4">
                <form action="/catalog" method="GET" class="mb-3">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" 
                               name="q" 
                               class="form-control border-start-0 bg-light fs-6" 
                               placeholder="{{ __('messages.mobile.search_modal_input_placeholder') }}" 
                               value="{{ request('q') }}" 
                               aria-label="{{ __('messages.mobile.search_modal_input_placeholder') }}"
                               autofocus>
                        <button class="btn btn-primary fw-bold px-3" type="submit">
                            {{ __('messages.mobile.search_modal_btn') }}
                        </button>
                    </div>
                </form>

                <!-- Popular Quick Searches -->
                <div class="mobile-search-tags mt-3">
                    <span class="d-block text-muted small fw-semibold mb-2">
                        {{ __('messages.mobile.search_quick_links') }}:
                    </span>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="/catalog?q=Cervical" class="badge bg-light text-primary border text-decoration-none py-2 px-3 rounded-pill">
                            {{ __('messages.mobile.search_tag_cervical') }}
                        </a>
                        <a href="/catalog?q=Viscoelástica" class="badge bg-light text-primary border text-decoration-none py-2 px-3 rounded-pill">
                            {{ __('messages.mobile.search_tag_visco') }}
                        </a>
                        <a href="/catalog?q=Gel" class="badge bg-light text-primary border text-decoration-none py-2 px-3 rounded-pill">
                            {{ __('messages.mobile.search_tag_cooling') }}
                        </a>
                        <a href="/catalog?q=Látex" class="badge bg-light text-primary border text-decoration-none py-2 px-3 rounded-pill">
                            {{ __('messages.mobile.search_tag_latex') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
