/**
 * Reposa+ Motion System & Microinteractions Module (interactions.js)
 * Impeccable Methodology - Phase 4.3 (animate)
 * Easing: cubic-bezier(0.16, 1, 0.3, 1)
 */

/**
 * Toast Notification Manager for Reposa+
 * Renders non-intrusive floating toasts with elastic entry and serene exit.
 */
export function showToast(type = 'success', message = '', options = {}) {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-sanctuary-container';
        container.setAttribute('aria-live', 'polite');
        container.setAttribute('aria-atomic', 'true');
        document.body.appendChild(container);
    }

    const duration = options.duration || 4000;
    const isSuccess = type === 'success';
    const isError = type === 'error';
    const badgeClass = isSuccess ? 'badge-success' : (isError ? 'badge-error' : 'badge-info');
    const progressClass = isSuccess ? 'progress-success' : (isError ? 'progress-error' : 'progress-info');
    
    // Icon selection
    let iconClass = 'bi-check2-circle';
    if (isError) iconClass = 'bi-exclamation-octagon-fill';
    else if (options.icon) iconClass = options.icon;
    else if (!isSuccess) iconClass = 'bi-info-circle-fill';

    // Default titles if not provided
    const defaultTitle = isSuccess 
        ? (options.title || 'Acción completada') 
        : (isError ? (options.title || 'Ha ocurrido un error') : (options.title || 'Información'));

    const toastElement = document.createElement('div');
    toastElement.className = 'toast-sanctuary';
    toastElement.setAttribute('role', isError ? 'alert' : 'status');

    toastElement.innerHTML = `
        <div class="toast-icon-badge ${badgeClass}" aria-hidden="true">
            <i class="bi ${iconClass}"></i>
        </div>
        <div class="toast-content">
            <div class="toast-title">${escapeHtml(defaultTitle)}</div>
            <p class="toast-message">${escapeHtml(message)}</p>
        </div>
        <button type="button" class="toast-btn-close" aria-label="Cerrar notificación">
            <i class="bi bi-x-lg"></i>
        </button>
        <div class="toast-progress ${progressClass}">
            <div class="toast-progress-bar" style="animation-duration: ${duration}ms;"></div>
        </div>
    `;

    container.appendChild(toastElement);

    let dismissTimer = null;
    let remainingTime = duration;
    let startTime = Date.now();

    function dismissToast() {
        if (toastElement.classList.contains('is-dismissing')) return;
        toastElement.classList.add('is-dismissing');
        
        const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const exitDelay = prefersReduced ? 10 : 280;

        setTimeout(() => {
            if (toastElement.parentNode) {
                toastElement.remove();
            }
        }, exitDelay);
    }

    // Auto-dismiss timer
    function startTimer() {
        startTime = Date.now();
        dismissTimer = setTimeout(dismissToast, remainingTime);
    }

    function pauseTimer() {
        if (dismissTimer) {
            clearTimeout(dismissTimer);
            remainingTime -= Date.now() - startTime;
            const progressBar = toastElement.querySelector('.toast-progress-bar');
            if (progressBar) {
                progressBar.style.animationPlayState = 'paused';
            }
        }
    }

    function resumeTimer() {
        if (remainingTime > 0) {
            const progressBar = toastElement.querySelector('.toast-progress-bar');
            if (progressBar) {
                progressBar.style.animationPlayState = 'running';
            }
            startTimer();
        } else {
            dismissToast();
        }
    }

    startTimer();

    // Hover pause behavior
    toastElement.addEventListener('mouseenter', pauseTimer);
    toastElement.addEventListener('mouseleave', resumeTimer);

    // Close button event
    const closeBtn = toastElement.querySelector('.toast-btn-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (dismissTimer) clearTimeout(dismissTimer);
            dismissToast();
        });
    }

    return toastElement;
}

/**
 * Escapes HTML characters for security
 */
function escapeHtml(string) {
    if (!string) return '';
    const div = document.createElement('div');
    div.textContent = string;
    return div.innerHTML;
}

/**
 * Initializes Add to Cart Interactivity
 */
export function initCartInteractions() {
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!form || !form.action || !form.action.includes('/cart/add/')) {
            return;
        }

        e.preventDefault();

        const btn = form.querySelector('button[type="submit"]') || form.querySelector('.btn-cta-bold') || form.querySelector('button');
        if (!btn) return;

        const originalContent = btn.innerHTML;
        const isRoundBtn = btn.classList.contains('rounded-circle');

        // Loading indicator
        btn.disabled = true;
        if (isRoundBtn) {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="width: 1rem; height: 1rem;"></span>';
        } else {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + originalContent;
        }

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update badge with elastic spring bump
                const badge = document.getElementById('cart-badge');
                if (badge) {
                    badge.innerText = data.cartCount;
                    badge.classList.remove('badge-cart-pulse');
                    // Force reflow to retrigger animation
                    void badge.offsetWidth;
                    badge.classList.add('badge-cart-pulse');
                    badge.addEventListener('animationend', () => {
                        badge.classList.remove('badge-cart-pulse');
                    }, { once: true });
                }

                // Show Sanctuary Toast
                showToast('success', data.message || 'Almohada añadida a tu carrito.', {
                    title: '¡Añadido al carrito!',
                    icon: 'bi-cart-check-fill'
                });
            }
        })
        .catch(error => {
            console.error('Error adding to cart:', error);
            showToast('error', 'No se pudo añadir el producto al carrito. Por favor, inténtalo de nuevo.', {
                title: 'Error de compra'
            });
        })
        .finally(() => {
            btn.innerHTML = originalContent;
            btn.disabled = false;
        });
    });
}

/**
 * Initializes Favorite Button Microinteractions (Heartbeat Spring)
 */
export function initFavoriteInteractions() {
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-favorite');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const form = btn.closest('form');
        const url = btn.dataset.url || (form ? form.action : null);
        if (!url) return;

        const productId = btn.dataset.productId;
        const icon = btn.querySelector('i');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        btn.disabled = true;

        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => {
            if (response.status === 401) {
                return response.json().then(data => {
                    showToast('error', data.message || 'Inicia sesión para guardar tus almohadas favoritas.', {
                        title: 'Acceso requerido'
                    });
                    if (data.redirect) {
                        setTimeout(() => window.location.href = data.redirect, 1200);
                    }
                    throw new Error('Unauthorized');
                });
            }
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (data.is_favorite) {
                    // Switch to favorited state
                    btn.classList.add('btn-danger', 'text-white', 'is-favorited-anim');
                    btn.classList.remove('btn-outline-danger', 'is-unfavorited-anim');
                    if (icon) {
                        icon.className = 'bi bi-heart-fill';
                    }
                    btn.setAttribute('title', 'Eliminar de favoritos');
                    btn.setAttribute('aria-label', 'Eliminar de favoritos');

                    btn.addEventListener('animationend', () => {
                        btn.classList.remove('is-favorited-anim');
                    }, { once: true });

                    showToast('success', data.message || 'Almohada guardada en tus favoritos.', {
                        title: 'Favorito guardado',
                        icon: 'bi-heart-fill'
                    });
                } else {
                    // Switch to unfavorited state
                    btn.classList.remove('btn-danger', 'text-white', 'is-favorited-anim');
                    btn.classList.add('btn-outline-danger', 'is-unfavorited-anim');
                    if (icon) {
                        icon.className = 'bi bi-heart';
                    }
                    btn.setAttribute('title', 'Añadir a favoritos');
                    btn.setAttribute('aria-label', 'Añadir a favoritos');

                    btn.addEventListener('animationend', () => {
                        btn.classList.remove('is-unfavorited-anim');
                    }, { once: true });

                    showToast('info', data.message || 'Almohada eliminada de tus favoritos.', {
                        title: 'Favorito eliminado',
                        icon: 'bi-heart'
                    });

                    // Remove card smoothly if on profile favorites page
                    if (productId) {
                        const favCard = document.getElementById(`fav-card-${productId}`);
                        if (favCard) {
                            favCard.style.transition = 'opacity 0.32s cubic-bezier(0.16, 1, 0.3, 1), transform 0.32s cubic-bezier(0.16, 1, 0.3, 1)';
                            favCard.style.opacity = '0';
                            favCard.style.transform = 'scale(0.9) translateY(12px)';

                            setTimeout(() => {
                                favCard.remove();
                                const container = document.getElementById('favorites');
                                if (container && container.querySelectorAll('[id^="fav-card-"]').length === 0) {
                                    const cardBody = container.querySelector('.card');
                                    if (cardBody) {
                                        cardBody.innerHTML = `
                                            <h4 class="fw-bold mb-4">Las almohadas que más me gustan</h4>
                                            <div class="text-center py-5">
                                                <i class="bi bi-heart fs-1 text-muted"></i>
                                                <p class="mt-3 text-muted">Aún no tienes almohadas en tus favoritos.</p>
                                                <a href="/catalog" class="btn btn-primary mt-2">Explorar Almohadas</a>
                                            </div>
                                        `;
                                    }
                                }
                            }, 320);
                        }
                    }
                }
            }
        })
        .catch(error => {
            if (error.message !== 'Unauthorized') {
                console.error('Error toggling favorite:', error);
                showToast('error', 'No se pudo actualizar favoritos. Inténtalo de nuevo.', {
                    title: 'Error'
                });
            }
        })
        .finally(() => {
            btn.disabled = false;
        });
    });
}

/**
 * Initializes Server Flash Messages as Sanctuary Toasts
 */
export function initServerFlashToasts() {
    const sessionSuccessEl = document.getElementById('flash-session-success');
    if (sessionSuccessEl && sessionSuccessEl.dataset.message) {
        showToast('success', sessionSuccessEl.dataset.message, {
            title: 'Operación completada'
        });
    }

    const sessionErrorEl = document.getElementById('flash-session-error');
    if (sessionErrorEl && sessionErrorEl.dataset.message) {
        showToast('error', sessionErrorEl.dataset.message, {
            title: 'Atención'
        });
    }
}

/**
 * Interactive Timeline Step Inspector on Order Detail / Confirmation Page (Phase 4.4 delight)
 */
export function initOrderTimelineInteractions() {
    const timelineContainer = document.querySelector('.order-timeline-card');
    if (!timelineContainer) return;

    const stepButtons = timelineContainer.querySelectorAll('.timeline-node-btn');
    const panel = timelineContainer.querySelector('.timeline-detail-panel');
    const panelIcon = panel ? panel.querySelector('.detail-panel-icon i') : null;
    const panelTitle = panel ? panel.querySelector('.detail-panel-title') : null;
    const panelText = panel ? panel.querySelector('.detail-panel-text') : null;

    if (!stepButtons.length || !panel) return;

    stepButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const stepLi = btn.closest('.timeline-step');
            if (!stepLi) return;

            stepButtons.forEach(b => {
                b.setAttribute('aria-expanded', 'false');
                b.classList.remove('is-inspecting');
            });

            btn.setAttribute('aria-expanded', 'true');
            btn.classList.add('is-inspecting');

            const title = stepLi.dataset.stepTitle || '';
            const detail = stepLi.dataset.stepDetail || '';
            const iconClass = stepLi.dataset.stepIcon || 'bi-info-circle';

            const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (prefersReduced) {
                if (panelIcon) panelIcon.className = `bi ${iconClass}`;
                if (panelTitle) panelTitle.textContent = title;
                if (panelText) panelText.textContent = detail;
                return;
            }

            panel.style.transition = 'opacity 0.16s cubic-bezier(0.16, 1, 0.3, 1)';
            panel.style.opacity = '0.35';

            setTimeout(() => {
                if (panelIcon) panelIcon.className = `bi ${iconClass}`;
                if (panelTitle) panelTitle.textContent = title;
                if (panelText) panelText.textContent = detail;
                panel.style.opacity = '1';
            }, 160);
        });
    });
}

/**
 * Initialize all microinteractions when DOM is ready
 */
export function initInteractions() {
    initCartInteractions();
    initFavoriteInteractions();
    initServerFlashToasts();
    initOrderTimelineInteractions();
}
