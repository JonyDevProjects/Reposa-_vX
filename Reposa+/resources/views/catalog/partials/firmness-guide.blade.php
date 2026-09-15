@php
    $currentLocale = app()->getLocale();
    $activeFirmness = request('firmness', '');
    $isEnglish = ($currentLocale === 'en');

    $firmnessData = [
        'soft' => [
            'level' => 2,
            'name' => $isEnglish ? 'Soft' : 'Suave',
            'desc' => $isEnglish ? 'Ultra-plush feel for gentle contouring and stomach sleepers.' : 'Acogida envolvente y perfil bajo, ideal para dormir boca abajo sin tensión.',
            'posture' => 'stomach',
            'relief' => '94%',
            'materials' => $isEnglish ? 'Gel-infused Visco + Soft AirFoam' : 'Viscoelástica perforada suave + Funda Tencel termorreguladora'
        ],
        'medium_soft' => [
            'level' => 4,
            'name' => $isEnglish ? 'Medium-Soft' : 'Media-Suave',
            'desc' => $isEnglish ? 'Balanced plushness with progressive cervical support.' : 'Soporte adaptable con suave amortiguación en cuello y hombros.',
            'posture' => 'back',
            'relief' => '96%',
            'materials' => $isEnglish ? 'Dual-density Memory Foam' : 'Núcleo bicapa visco-HR con adaptabilidad progresiva'
        ],
        'medium' => [
            'level' => 6,
            'name' => $isEnglish ? 'Medium' : 'Media',
            'desc' => $isEnglish ? 'The universal ergonomic balance for combination and back sleepers.' : 'Equilibrio universal anatómico; mantiene la curva lordótica natural.',
            'posture' => 'back',
            'relief' => '98%',
            'materials' => $isEnglish ? 'Anatomical Pocket Microsprings + Memory Foam' : 'Micro-muelles ensacados embolsados + Capa viscoelástica 50kg/m³'
        ],
        'medium_high' => [
            'level' => 8,
            'name' => $isEnglish ? 'Medium-High' : 'Media-Alta',
            'desc' => $isEnglish ? 'Firm ergonomic support bridging the gap for side sleepers.' : 'Soporte firme que rellena el espacio entre hombro y cabeza al dormir de lado.',
            'posture' => 'side',
            'relief' => '99%',
            'materials' => $isEnglish ? 'Reinforced HR Core + Ergonomic Contour Visco' : 'Núcleo HR de alta densidad + Contorno cervical anatómico'
        ],
        'high' => [
            'level' => 10,
            'name' => $isEnglish ? 'High' : 'Alta',
            'desc' => $isEnglish ? 'Maximum structural firmness preventing any cervical sinkage.' : 'Firmeza estructural máxima para mantener alineación estricta y hombros anchos.',
            'posture' => 'side',
            'relief' => '97%',
            'materials' => $isEnglish ? 'High Resilience Orthopedic Foam' : 'Espuma HR ortopédica estructural con canales de aireación 3D'
        ],
    ];
    $advisorOpen = request('advisor') == '1' || request()->has('open_advisor');
@endphp

<div class="firmness-advisor-wrapper mb-4" id="firmness-guide-section">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden firmness-advisor-card">
        {{-- Card Header: Non-invasive Smart Value Banner --}}
        <div class="card-header border-0 py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3 bg-navy-sanctuary text-white">
            <div class="d-flex align-items-center gap-3">
                <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-15 text-warning rounded-circle flex-shrink-0" style="width: 42px; height: 42px; box-shadow: 0 0 12px rgba(251, 191, 36, 0.25);">
                    <i class="bi bi-stars fs-5"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <span class="advisor-badge-pill">
                            <i class="bi bi-cpu me-1"></i> {{ __('messages.firmness_guide.badge') }}
                        </span>
                        <span class="text-white-75 small d-none d-sm-inline">&bull; {{ __('messages.firmness_guide.banner_subtitle') }}</span>
                    </div>
                    <h2 class="h6 fw-bold mb-0 text-white">
                        {{ __('messages.firmness_guide.banner_prompt') }}
                    </h2>
                </div>
            </div>
            <button class="btn btn-sm btn-advisor-toggle rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 text-nowrap ms-auto" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#advisorContentCollapse" 
                    aria-expanded="{{ $advisorOpen ? 'true' : 'false' }}" 
                    aria-controls="advisorContentCollapse" 
                    id="toggleAdvisorBtn">
                <i class="bi bi-sliders me-1"></i> 
                <span id="advisorToggleText">{{ $advisorOpen ? __('messages.firmness_guide.toggle_close') : __('messages.firmness_guide.toggle_btn') }}</span>
                <i class="bi bi-chevron-down ms-1" id="advisorToggleChevron" style="transition: transform 0.25s ease; {{ $advisorOpen ? 'transform: rotate(180deg);' : '' }}"></i>
            </button>
        </div>

        {{-- Collapsible Interactive Body (Collapsed by default for Progressive Disclosure) --}}
        <div class="collapse {{ $advisorOpen ? 'show' : '' }}" id="advisorContentCollapse">
            <div class="card-body p-4 bg-white border-top border-light-subtle">
                <div class="row g-4">
                    {{-- Left Column: Interactive Controls --}}
                    <div class="col-lg-7">
                        {{-- Step 1: Posture Selector --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-navy mb-2 d-flex align-items-center gap-2">
                                <span class="badge rounded-circle bg-primary-subtle text-primary" style="width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.72rem;">1</span>
                                {{ __('messages.firmness_guide.posture_title') }}
                            </label>
                            <div class="row g-2" role="radiogroup" aria-label="{{ __('messages.firmness_guide.posture_title') }}">
                                {{-- Back Sleeper --}}
                                <div class="col-4">
                                    <button type="button" class="posture-select-btn w-100 p-3 text-center border rounded-3 transition-all active" 
                                            data-posture="back" data-default-firmness="medium" data-level="6"
                                            aria-label="{{ __('messages.firmness_guide.posture_back') }}">
                                        <div class="posture-icon mb-2">
                                            <i class="bi bi-person-arms-up fs-3 text-primary"></i>
                                        </div>
                                        <div class="fw-bold small text-navy mb-1">{{ __('messages.firmness_guide.posture_back') }}</div>
                                        <div class="text-muted posture-desc-sub">{{ __('messages.firmness_guide.posture_back_desc') }}</div>
                                    </button>
                                </div>

                                {{-- Side Sleeper --}}
                                <div class="col-4">
                                    <button type="button" class="posture-select-btn w-100 p-3 text-center border rounded-3 transition-all" 
                                            data-posture="side" data-default-firmness="medium_high" data-level="8"
                                            aria-label="{{ __('messages.firmness_guide.posture_side') }}">
                                        <div class="posture-icon mb-2">
                                            <i class="bi bi-person-standing fs-3 text-primary"></i>
                                        </div>
                                        <div class="fw-bold small text-navy mb-1">{{ __('messages.firmness_guide.posture_side') }}</div>
                                        <div class="text-muted posture-desc-sub">{{ __('messages.firmness_guide.posture_side_desc') }}</div>
                                    </button>
                                </div>

                                {{-- Stomach Sleeper --}}
                                <div class="col-4">
                                    <button type="button" class="posture-select-btn w-100 p-3 text-center border rounded-3 transition-all" 
                                            data-posture="stomach" data-default-firmness="soft" data-level="2"
                                            aria-label="{{ __('messages.firmness_guide.posture_stomach') }}">
                                        <div class="posture-icon mb-2">
                                            <i class="bi bi-cloud-moon fs-3 text-primary"></i>
                                        </div>
                                        <div class="fw-bold small text-navy mb-1">{{ __('messages.firmness_guide.posture_stomach') }}</div>
                                        <div class="text-muted posture-desc-sub">{{ __('messages.firmness_guide.posture_stomach_desc') }}</div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Step 2: Continuous Tactile Firmness Slider (Scale 1 to 10) --}}
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="firmnessRangeSlider" class="form-label fw-bold text-navy mb-0 d-flex align-items-center gap-2">
                                    <span class="badge rounded-circle bg-primary-subtle text-primary" style="width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.72rem;">2</span>
                                    {{ __('messages.firmness_guide.scale_title') }}
                                </label>
                                <span class="badge bg-navy-sanctuary text-white px-2 py-1 tabular-nums" id="sliderValueBadge">
                                    Nivel <span id="currentLevelNumber">6</span>/10
                                </span>
                            </div>
                            <p class="text-muted small mb-2">{{ __('messages.firmness_guide.scale_hint') }}</p>

                            <div class="range-slider-wrapper position-relative py-2">
                                <input type="range" class="form-range firmness-slider" id="firmnessRangeSlider" 
                                       min="1" max="10" step="1" value="6" 
                                       aria-label="Escala de firmeza de 1 a 10"
                                       aria-valuemin="1" aria-valuemax="10" aria-valuenow="6">
                                <div class="d-flex justify-content-between text-muted small mt-1 scale-markers">
                                    <span class="marker-point" data-target-level="2">1-3 {{ __('messages.firmness_guide.feel_soft') }}</span>
                                    <span class="marker-point" data-target-level="4">4-5 {{ __('messages.firmness_guide.feel_medium_soft') }}</span>
                                    <span class="marker-point fw-bold text-primary" data-target-level="6">6-7 {{ __('messages.firmness_guide.feel_medium') }}</span>
                                    <span class="marker-point" data-target-level="8">8 {{ __('messages.firmness_guide.feel_medium_high') }}</span>
                                    <span class="marker-point" data-target-level="10">9-10 {{ __('messages.firmness_guide.feel_high') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Quick Firmness Pills --}}
                        <div class="d-flex flex-wrap gap-2 align-items-center pt-1">
                            <span class="text-muted small me-1">Atajo:</span>
                            @foreach($firmnessData as $key => $item)
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-3 quick-firmness-pill {{ $key === 'medium' ? 'active' : '' }}"
                                        data-key="{{ $key }}" data-level="{{ $item['level'] }}" data-name="{{ $item['name'] }}">
                                    {{ $item['name'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Right Column: Anatomical Feedback & Dynamic Action --}}
                    <div class="col-lg-5">
                        <div class="advisor-preview-card p-4 rounded-3 h-100 d-flex flex-column justify-content-between border">
                            <div>
                                {{-- Spine & Ergonomic Indicator --}}
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="spine-pulse-dot"></span>
                                        <span class="text-uppercase text-muted fw-bold small" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                                            {{ __('messages.firmness_guide.pressure_relief') }}
                                        </span>
                                    </div>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small fw-bold tabular-nums" id="reliefPercentage">
                                        <i class="bi bi-shield-check me-1"></i>98% Alivio
                                    </span>
                                </div>

                                {{-- Dynamic Recommendation Box --}}
                                <div class="recommendation-hero mb-3 p-3 rounded-3 bg-white border shadow-sm">
                                    <span class="text-muted small d-block mb-1">{{ __('messages.firmness_guide.recommendation_title') }}:</span>
                                    <div class="h4 fw-bold text-navy mb-1" id="recommendedFirmnessName">
                                        {{ $isEnglish ? 'Medium' : 'Media' }}
                                    </div>
                                    <p class="text-muted small mb-2" id="recommendedFirmnessDesc">
                                        {{ $isEnglish ? 'The universal ergonomic balance for combination and back sleepers.' : 'Equilibrio universal anatómico; mantiene la curva lordótica natural.' }}
                                    </p>
                                    
                                    {{-- Material Layer Preview --}}
                                    <div class="p-2 rounded bg-light border-0 mt-2">
                                        <span class="text-navy fw-semibold small d-block mb-1">
                                            <i class="bi bi-layers me-1 text-primary"></i>{{ __('messages.firmness_guide.materials_title') }}:
                                        </span>
                                        <span class="text-muted small" id="recommendedMaterials">
                                            {{ $isEnglish ? 'Anatomical Pocket Microsprings + Memory Foam' : 'Micro-muelles ensacados embolsados + Capa viscoelástica 50kg/m³' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Direct Catalog Filter Action Button --}}
                            <div class="mt-3">
                                <a href="/catalog?firmness={{ urlencode($isEnglish ? 'Medium' : 'Media') }}" 
                                   id="applyFirmnessFilterBtn" 
                                   class="btn btn-primary w-100 py-2 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm">
                                    <i class="bi bi-funnel-fill"></i>
                                    <span>{{ __('messages.firmness_guide.filter_cta') }} «<span id="ctaFirmnessLabel">{{ $isEnglish ? 'Medium' : 'Media' }}</span>»</span>
                                </a>
                                <p class="text-center text-muted small mt-2 mb-0" style="font-size: 0.75rem;">
                                    <i class="bi bi-check2-circle text-success me-1"></i>100 noches de prueba &middot; Envío gratis
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Inline Lightweight Interactive Script for the Overdrive Experience --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const firmnessMap = {
        1: { key: 'soft', name: '{{ $isEnglish ? 'Soft' : 'Suave' }}', desc: '{{ $isEnglish ? 'Ultra-plush feel for gentle contouring and stomach sleepers.' : 'Acogida envolvente y perfil bajo, ideal para dormir boca abajo sin tensión.' }}', relief: '94%', materials: '{{ $isEnglish ? 'Gel-infused Visco + Soft AirFoam' : 'Viscoelástica perforada suave + Funda Tencel termorreguladora' }}', posture: 'stomach' },
        2: { key: 'soft', name: '{{ $isEnglish ? 'Soft' : 'Suave' }}', desc: '{{ $isEnglish ? 'Ultra-plush feel for gentle contouring and stomach sleepers.' : 'Acogida envolvente y perfil bajo, ideal para dormir boca abajo sin tensión.' }}', relief: '94%', materials: '{{ $isEnglish ? 'Gel-infused Visco + Soft AirFoam' : 'Viscoelástica perforada suave + Funda Tencel termorreguladora' }}', posture: 'stomach' },
        3: { key: 'soft', name: '{{ $isEnglish ? 'Soft' : 'Suave' }}', desc: '{{ $isEnglish ? 'Ultra-plush feel for gentle contouring and stomach sleepers.' : 'Acogida envolvente y perfil bajo, ideal para dormir boca abajo sin tensión.' }}', relief: '95%', materials: '{{ $isEnglish ? 'Gel-infused Visco + Soft AirFoam' : 'Viscoelástica perforada suave + Funda Tencel termorreguladora' }}', posture: 'stomach' },
        4: { key: 'medium_soft', name: '{{ $isEnglish ? 'Medium-Soft' : 'Media-Suave' }}', desc: '{{ $isEnglish ? 'Balanced plushness with progressive cervical support.' : 'Soporte adaptable con suave amortiguación en cuello y hombros.' }}', relief: '96%', materials: '{{ $isEnglish ? 'Dual-density Memory Foam' : 'Núcleo bicapa visco-HR con adaptabilidad progresiva' }}', posture: 'back' },
        5: { key: 'medium_soft', name: '{{ $isEnglish ? 'Medium-Soft' : 'Media-Suave' }}', desc: '{{ $isEnglish ? 'Balanced plushness with progressive cervical support.' : 'Soporte adaptable con suave amortiguación en cuello y hombros.' }}', relief: '97%', materials: '{{ $isEnglish ? 'Dual-density Memory Foam' : 'Núcleo bicapa visco-HR con adaptabilidad progresiva' }}', posture: 'back' },
        6: { key: 'medium', name: '{{ $isEnglish ? 'Medium' : 'Media' }}', desc: '{{ $isEnglish ? 'The universal ergonomic balance for combination and back sleepers.' : 'Equilibrio universal anatómico; mantiene la curva lordótica natural.' }}', relief: '98%', materials: '{{ $isEnglish ? 'Anatomical Pocket Microsprings + Memory Foam' : 'Micro-muelles ensacados embolsados + Capa viscoelástica 50kg/m³' }}', posture: 'back' },
        7: { key: 'medium', name: '{{ $isEnglish ? 'Medium' : 'Media' }}', desc: '{{ $isEnglish ? 'The universal ergonomic balance for combination and back sleepers.' : 'Equilibrio universal anatómico; mantiene la curva lordótica natural.' }}', relief: '98%', materials: '{{ $isEnglish ? 'Anatomical Pocket Microsprings + Memory Foam' : 'Micro-muelles ensacados embolsados + Capa viscoelástica 50kg/m³' }}', posture: 'back' },
        8: { key: 'medium_high', name: '{{ $isEnglish ? 'Medium-High' : 'Media-Alta' }}', desc: '{{ $isEnglish ? 'Firm ergonomic support bridging the gap for side sleepers.' : 'Soporte firme que rellena el espacio entre hombro y cabeza al dormir de lado.' }}', relief: '99%', materials: '{{ $isEnglish ? 'Reinforced HR Core + Ergonomic Contour Visco' : 'Núcleo HR de alta densidad + Contorno cervical anatómico' }}', posture: 'side' },
        9: { key: 'high', name: '{{ $isEnglish ? 'High' : 'Alta' }}', desc: '{{ $isEnglish ? 'Maximum structural firmness preventing any cervical sinkage.' : 'Firmeza estructural máxima para mantener alineación estricta y hombros anchos.' }}', relief: '97%', materials: '{{ $isEnglish ? 'High Resilience Orthopedic Foam' : 'Espuma HR ortopédica estructural con canales de aireación 3D' }}', posture: 'side' },
        10: { key: 'high', name: '{{ $isEnglish ? 'High' : 'Alta' }}', desc: '{{ $isEnglish ? 'Maximum structural firmness preventing any cervical sinkage.' : 'Firmeza estructural máxima para mantener alineación estricta y hombros anchos.' }}', relief: '97%', materials: '{{ $isEnglish ? 'High Resilience Orthopedic Foam' : 'Espuma HR ortopédica estructural con canales de aireación 3D' }}', posture: 'side' }
    };

    const slider = document.getElementById('firmnessRangeSlider');
    const levelNumber = document.getElementById('currentLevelNumber');
    const recommendedName = document.getElementById('recommendedFirmnessName');
    const recommendedDesc = document.getElementById('recommendedFirmnessDesc');
    const recommendedMaterials = document.getElementById('recommendedMaterials');
    const reliefBadge = document.getElementById('reliefPercentage');
    const ctaLabel = document.getElementById('ctaFirmnessLabel');
    const ctaBtn = document.getElementById('applyFirmnessFilterBtn');
    const postureBtns = document.querySelectorAll('.posture-select-btn');
    const quickPills = document.querySelectorAll('.quick-firmness-pill');

    function updateState(level, source) {
        level = parseInt(level, 10);
        if (isNaN(level) || level < 1) level = 1;
        if (level > 10) level = 10;

        const data = firmnessMap[level] || firmnessMap[6];

        // Update Slider if not triggered by slider
        if (slider && source !== 'slider') {
            slider.value = level;
        }

        // Update Text Elements
        if (levelNumber) levelNumber.textContent = level;
        if (recommendedName) recommendedName.textContent = data.name;
        if (recommendedDesc) recommendedDesc.textContent = data.desc;
        if (recommendedMaterials) recommendedMaterials.textContent = data.materials;
        if (reliefBadge) reliefBadge.innerHTML = '<i class="bi bi-shield-check me-1"></i>' + data.relief + ' {{ $isEnglish ? 'Relief' : 'Alivio' }}';
        if (ctaLabel) ctaLabel.textContent = data.name;

        // Update CTA Link URL
        if (ctaBtn) {
            const currentUrl = new URL(window.location.origin + '/catalog');
            currentUrl.searchParams.set('firmness', data.name);
            ctaBtn.href = currentUrl.toString();
        }

        // Sync Posture Buttons
        postureBtns.forEach(btn => {
            if (btn.dataset.posture === data.posture) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        // Sync Quick Pills
        quickPills.forEach(pill => {
            if (pill.dataset.key === data.key) {
                pill.classList.add('active');
            } else {
                pill.classList.remove('active');
            }
        });
    }

    // Slider Event
    if (slider) {
        slider.addEventListener('input', function(e) {
            updateState(e.target.value, 'slider');
        });
    }

    // Posture Buttons Event
    postureBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const level = this.dataset.level || 6;
            updateState(level, 'posture');
        });
    });

    // Quick Pills Event
    quickPills.forEach(pill => {
        pill.addEventListener('click', function() {
            const level = this.dataset.level || 6;
            updateState(level, 'pill');
        });
    });

    // Scale Marker Points
    document.querySelectorAll('.marker-point').forEach(marker => {
        marker.addEventListener('click', function() {
            const level = this.dataset.targetLevel;
            if (level) updateState(level, 'marker');
        });
    });

    // Toggle button label and chevron
    const collapseEl = document.getElementById('advisorContentCollapse');
    const toggleText = document.getElementById('advisorToggleText');
    const toggleChevron = document.getElementById('advisorToggleChevron');
    if (collapseEl && toggleText) {
        collapseEl.addEventListener('hidden.bs.collapse', function() {
            toggleText.textContent = '{{ __('messages.firmness_guide.toggle_btn') }}';
            if (toggleChevron) toggleChevron.style.transform = 'rotate(0deg)';
        });
        collapseEl.addEventListener('shown.bs.collapse', function() {
            toggleText.textContent = '{{ __('messages.firmness_guide.toggle_close') }}';
            if (toggleChevron) toggleChevron.style.transform = 'rotate(180deg)';
        });
    }

    // Initialize with current filter if set
    @if($activeFirmness)
        const currentActive = '{{ $activeFirmness }}'.toLowerCase();
        let initLevel = 6;
        if (currentActive.includes('suave') || currentActive.includes('soft')) {
            initLevel = currentActive.includes('media') || currentActive.includes('medium') ? 4 : 2;
        } else if (currentActive.includes('alta') || currentActive.includes('high')) {
            initLevel = currentActive.includes('media') || currentActive.includes('medium') ? 8 : 10;
        }
        updateState(initLevel, 'init');
    @endif
});
</script>
@endpush
