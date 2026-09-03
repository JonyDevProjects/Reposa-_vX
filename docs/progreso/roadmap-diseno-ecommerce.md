# Roadmap: Excelencia en Diseño y UX/UI — Reposa+ (Impeccable)

## Contexto

Este documento define la planificación integral para transformar la interfaz, arquitectura de información y experiencia de usuario (UX/UI) de **Reposa+** en un e-commerce de descanso premium (*sleep wellness*), elevando el producto de un prototipo funcional académico a un estándar de diseño comercial de alta gama (*out-of-distribution craft*).

El plan adopta como metodología la suite de diseño **Impeccable**, incorporando de manera secuencial y estructurada sus **22 comandos especializados**, articulados en **8 fases de ejecución**.

**Fecha de creación:** 01 de septiembre de 2026  
**Última sesión:** 03/09/2026 — Cierre Fase 3, Aislamiento DB Testing, Seeders Deterministas y Refinado Visual Nocturno (Hero & Categorías)  
**Stack de frontend objetivo:** 100% alineado con el stack actual del proyecto — **Laravel 11 (Vistas y Componentes Blade)**, **Bootstrap 5.3 + SCSS personalizado** (`resources/sass/app.scss`), **Vite**, **Bootstrap Icons**, **Vanilla JS / Bootstrap 5 JS Bundle** y **Axios**. Se respeta y potencia la arquitectura actual sin migrar a otros frameworks ni introducir dependencias superfluas.

---

## Matriz Global de Comandos Impeccable

| Categoría | Comando | Propósito en Reposa+ | Fase Asignada |
|---|---|---|---|
| **Build** | `init` | Establecer la visión de producto, arquetipo de marca y pilares de diseño en `PRODUCT.md` | **Fase 1** |
| **Build** | `document` | Extraer y formalizar la guía de diseño de la interfaz existente en `DESIGN.md` | **Fase 1** |
| **Build** | `extract` | Crear sistema de tokens y biblioteca de componentes Blade reutilizables | **Fase 1** |
| **Evaluate** | `critique` | Evaluación heurística con scoring de usabilidad por superficie | **Fase 2** |
| **Evaluate** | `audit` | Auditoría técnica rigurosa de accesibilidad (WCAG AA), contraste y rendimiento | **Fase 2** |
| **Enhance** | `typeset` | Jerarquía tipográfica editorial y tabular para descanso y comercio | **Fase 3** |
| **Enhance** | `colorize` | Paleta cromática cromoterapéutica de bienestar y descanso (*Midnight Rest*) | **Fase 3** |
| **Enhance** | `layout` | Cuadrículas fluidas, ritmo vertical y arquitectura espacial balanceada | **Fase 3** |
| **Build** | `shape` | Modelado de flujos críticos de compra y descubrimiento antes de codificar | **Fase 4** |
| **Refine** | `bolder` | Elevar la autoridad visual de landing, hero sections y llamadas a la acción | **Fase 4** |
| **Enhance** | `animate` | Microinteracciones suaves con propósito físico de descanso | **Fase 4** |
| **Enhance** | `delight` | Momentos de satisfacción de marca (unboxing virtual, microcopy empático) | **Fase 4** |
| **Fix** | `clarify` | Microcopy de confianza, etiquetas transparentes, checkout sin fricciones | **Fase 5** |
| **Refine** | `harden` | Diseño defensivo ante estados extremos, textos largos y fallos de red | **Fase 5** |
| **Refine** | `onboard` | Pantallas de estado vacío atractivas y flujos de bienvenida contextuales | **Fase 5** |
| **Refine** | `distill` | Eliminación de sobrecarga cognitiva y elementos superfluos en checkout | **Fase 5** |
| **Refine** | `quieter` | Calmar y optimizar la densidad de información en backoffice y facturas | **Fase 6** |
| **Enhance** | `overdrive` | Elemento interactivo diferenciador: selector/simulador visual de firmeza | **Fase 6** |
| **Fix** | `adapt` | Experiencia táctil y ergonomía adaptativa para dispositivos móviles | **Fase 7** |
| **Fix** | `optimize` | Eliminación de Layout Shifts (CLS), carga perezosa y rendimiento de pintura | **Fase 7** |
| **Iterate** | `live` | Iteración y pruebas de variantes visuales en navegador en tiempo real | **Fase 8** |
| **Refine** | `polish` | Pase final de acabado estético, alineación al píxel y control de calidad | **Fase 8** |

---

## Estado del Roadmap de Diseño

| Fase | Denominación / Enfoque | Comandos Clave | Estado |
|---|---|---|:---:|
| **1** | Fundamentos, Autoridad Visual y Componentes Base | `init`, `document`, `extract` | ✅ Completada |
| **2** | Evaluación Heurística, Accesibilidad y Diagnóstico | `critique`, `audit` | ✅ Completada |
| **3** | Identidad Cromática, Tipografía y Armonía Espacial | `typeset`, `colorize`, `layout` | ✅ Completada |
| **4** | Experiencia Persuasiva y Emocional del Storefront | `shape`, `bolder`, `animate`, `delight` | ✅ Completada |
| **5** | Claridad Transaccional, Estados Vacíos y Resiliencia | `clarify`, `harden`, `onboard`, `distill` | ✅ Completada |
| **6** | Densidad Operativa (Admin, Facturas) y Feature Estrella | `quieter`, `overdrive` | ⏳ Pendiente |
| **7** | Ergonomía Móvil y Rendimiento Web Extremo | `adapt`, `optimize` | ⏳ Pendiente |
| **8** | Iteración en Vivo, Pulido Integral y Certificación | `live`, `polish` | ⏳ Pendiente |

---

## Fase 1: Fundamentos, Autoridad Visual y Componentes Base (PRIORIDAD ALTA)

**Objetivo:** Establecer una única fuente de verdad documental para el diseño de Reposa+, auditar los activos visuales preexistentes y encapsular los elementos de interfaz repetitivos en componentes Blade limpios con tokens centralizados.

**Comandos Impeccable vinculados:** `/impeccable init`, `/impeccable document`, `/impeccable extract`

### 1.1 Inicialización del Contexto de Producto (`init`)
- Ejecutar `/impeccable init` para registrar `PRODUCT.md` en la raíz del repositorio.
- Definir arquetipo de marca: *Wellness Premium / Sanctuary Sleep* (confianza, confort térmico y lumbar, estética serena no clínica).
- Especificar el público objetivo: adultos de 25-55 años que priorizan calidad de descanso, ergonomía y transparencia en envíos y devoluciones.
- Documentar restricciones del proyecto: fidelidad estricta al stack actual (Laravel 11, motor de plantillas Blade, sistema de cuadrícula y componentes de Bootstrap 5.3 + SCSS, Vite, localización `es`/`en` nativa).

### 1.2 Documentación del Sistema Visual Existente (`document`)
- Ejecutar `/impeccable document Reposa+` para inspeccionar las vistas Blade y generar `DESIGN.md`.
- Catalogar la paleta actual (`#182447`, `#758ef9`, `#42569a`, `#b1cdff`) y mapear inconsistencias de espaciado o botones directos.
- Registrar el mapa de superficies: Catálogo, Ficha de Producto, Carrito, Proceso de Pago, Historial/Perfil, Dashboard de Administración y Plantilla de Facturas.

### 1.3 Extracción de Tokens y Componentes Blade Reutilizables (`extract`)
- Centralizar variables de diseño en `Reposa+/resources/sass/_tokens.scss` y CSS Custom Properties (`--color-surface`, `--color-primary-navy`, `--shadow-soft`, `--radius-card`).
- Extraer componentes Blade reutilizables en `Reposa+/resources/views/components/`:
  - `x-product-card`: Tarjeta de producto unificada con imagen, badges de stock, precio formateado y acción de favoritos.
  - `x-price`: Representación semántica de divisas (soporte multi-idioma y alineación de decimales).
  - `x-badge-stock`: Indicador de disponibilidad visual (*En stock*, *Últimas unidades*, *Agotado*).
  - `x-button`: Variantes estandarizadas (`primary`, `secondary`, `outline`, `ghost`, `danger`) con estados de foco accesibles.
  - `x-alert`: Mensajes flash de sesión consistentes (éxito, aviso, error de stock).

**Archivos a crear/modificar:**
- `PRODUCT.md` (nuevo)
- `DESIGN.md` (nuevo)
- `Reposa+/resources/sass/_tokens.scss` (nuevo)
- `Reposa+/resources/sass/app.scss` (importar tokens)
- `Reposa+/resources/views/components/product-card.blade.php` (nuevo)
- `Reposa+/resources/views/components/price.blade.php` (nuevo)
- `Reposa+/resources/views/components/badge-stock.blade.php` (nuevo)
- `Reposa+/resources/views/components/button.blade.php` (nuevo)
- `Reposa+/resources/views/components/alert.blade.php` (nuevo)

**Criterios de Aceptación:**
- `PRODUCT.md` y `DESIGN.md` creados y validados por el contexto de Impeccable.
- Todos los botones principales y tarjetas de producto del catálogo sustituidos por sus componentes Blade correspondientes sin alterar funcionalidad.

---

## Fase 2: Evaluación Heurística, Accesibilidad y Diagnóstico (PRIORIDAD ALTA)

**Objetivo:** Obtener un diagnóstico exhaustivo de las debilidades de usabilidad, deficiencias de contraste, barreras de accesibilidad y problemas de jerarquía en todas las vistas públicas y privadas.

**Comandos Impeccable vinculados:** `/impeccable critique`, `/impeccable audit`

### 2.1 Crítica Heurística de Experiencia de Usuario (`critique`)
- Ejecutar evaluación sistemática sobre las tres superficies prioritarias:
  - Catálogo y Filtros (`Reposa+/resources/views/catalog/index.blade.php`)
  - Detalle de Producto (`Reposa+/resources/views/catalog/show.blade.php`)
  - Carrito y Checkout (`Reposa+/resources/views/cart/index.blade.php`)
- Puntuación en base a las 10 heurísticas de Nielsen (visibilidad del estado, correspondencia con el mundo real, control y libertad, prevención de errores).
- Detección de bloqueos de conversión (p. ej. información de gastos de envío oculta hasta el final, falta de visualización de medidas del colchón).

### 2.2 Auditoría Técnica de Calidad y Accesibilidad (`audit`)
- Auditoría WCAG 2.1 Nivel AA en contrastes de texto (`#758ef9` sobre blanco falla ratio 4.5:1 en tamaños pequeños).
- Inspección de navegación exclusiva por teclado (`Tab`, `:focus-visible`, `Enter`, `Space`) en menús desplegables y modales.
- Verificación de atributos ARIA en botones iconográficos (como el botón de favoritos `<button aria-label="Añadir a favoritos">`).
- Auditoría de etiquetas de formularios (`<label for="...">` explícitos en checkout y login).

**Archivos a crear/modificar:**
- `docs/progreso/auditoria-impeccable-diagnostico.md` (informe de hallazgos P0, P1, P2)
- `Reposa+/resources/views/layouts/app.blade.php` (enlaces *skip-to-content* y soporte focus)
- `Reposa+/resources/views/catalog/show.blade.php` (etiquetado accesible en selectores de cantidad)

**Criterios de Aceptación:**
- Matriz de hallazgos P0/P1 resuelta con ratio de contraste >= 4.5:1 en todo el texto visible.
- Navegación completa por teclado funcional desde la cabecera hasta el footer sin trampas de foco (*focus traps*).

---

## Fase 3: Identidad Cromática, Tipografía y Armonía Espacial (PRIORIDAD MEDIA-ALTA)

**Objetivo:** Transformar la estética genérica actual en una experiencia sensorial que comunique tranquilidad, descanso ergonómico y confianza médica/premium mediante tipografía cuidada y armonía cromática.

**Comandos Impeccable vinculados:** `/impeccable typeset`, `/impeccable colorize`, `/impeccable layout`

### 3.1 Tipografía Editorial y Comercial (`typeset`)
- Configurar escala tipográfica optimizada en `app.scss`:
  - **Titulares:** Tipografía de carácter sereno y orgánico (*Plus Jakarta Sans* o titulares con mayor personalidad) para transmitir frescura y descanso.
  - **Cuerpo y datos:** *Inter* con optimización de espaciado y `font-feature-settings: 'tnum'` (cifras tabulares) para precios y facturas.
- Definir escala armónica (`fs-display`, `fs-h1` a `fs-caption`) con límites estrictos de caracteres por línea (`max-inline-size: 65ch`) en descripciones de producto.
- Ajustar `line-height` y `letter-spacing` en encabezados y textos legales.

### 3.2 Paleta Cromática "Sanctuary Rest" (`colorize`)
- Reemplazar la paleta básica por una escala armónica de bienestar:
  - **Midnight Abyss (`#0B1329`):** Color primario para cabeceras y textos de máximo contraste.
  - **Restful Slate (`#1E293B` y `#334155`):** Textos secundarios y bordes estructurales.
  - **Cloud Mist (`#F8FAFC` y `#F1F5F9`):** Fondos de descanso y paneles secundarios.
  - **Serene Indigo / Lavender (`#4F46E5` / `#6366F1`):** Acciones primarias y estados activos.
  - **Restorative Sage (`#10B981` / `#059669`):** Disponibilidad de stock y confirmación de pago.
  - **Warm Amber (`#F59E0B`):** Aviso de últimas unidades en inventario.
- Asegurar conformidad AA en todos los emparejamientos de fondo y primer plano.

### 3.3 Rejilla Espacial y Armonía Estructural (`layout`)
- Implementar escala de espaciado basada en múltiplos de 8px (8, 16, 24, 32, 48, 64, 96px).
- Rediseñar el layout de la tienda:
  - Catálogo: Sidebar de filtros adhesivo (*sticky*) con acordeones colapsables para categorías y precios.
  - Ficha de Producto: Cuadrícula asimétrica 60/40 (galería de producto expansiva a la izquierda, bloque de compra persistente a la derecha).
  - Carrito: Resumen de compra adherido al scroll en desktop para mantener visible el botón de checkout.

**Archivos a crear/modificar:**
- `Reposa+/resources/sass/_tokens.scss`
- `Reposa+/resources/sass/_typography.scss` (nuevo)
- `Reposa+/resources/sass/_layout.scss` (nuevo)
- `Reposa+/resources/views/catalog/index.blade.php`
- `Reposa+/resources/views/catalog/show.blade.php`
- `Reposa+/resources/views/cart/index.blade.php`

**Criterios de Aceptación:**
- Cero saltos visuales en el ancho del contenedor entre páginas.
- Tipografía y paleta coherentes en todo el recorrido de compra.

**Resultados Entregados y Defectos Subsanados:**
- **DEF-01 (Contraste WCAG 2.1 AA):** Resuelto. Sustituido el acento secundario deficiente (`#758ef9`, 3.01:1) por **Serene Indigo** (`#4F46E5`, ratio 6.34:1) y ajustado el gris secundario a Restful Slate 600 (`#475569`, ratio 6.68:1 sobre canvas y 7.0:1 sobre blanco). Sincronizado en `_tokens.scss`, `DESIGN.md` y `.impeccable/design.json`.
- **DEF-04 (Recarga Síncrona en Selects):** Resuelto. Eliminado `onchange="this.form.submit()"` de los selectores de material y firmeza en `catalog/index.blade.php`. Implementados selectores estables con etiquetas `<label for="...">` y enlaces semánticos con IDs, manteniendo el envío controlado mediante el botón "Aplicar filtros".
- **DEF-09 (Jerarquía Semántica de Encabezados):** Resuelto. Incorporado `<h1>` semántico principal en el carrito (`cart/index.blade.php`), corregido el salto de nivel `<h6>` a `<h2 class="h5">` en especificaciones de ficha (`catalog/show.blade.php`), título de producto en tarjeta normalizado a `<h3>` en `components/product-card.blade.php` y títulos de secciones en home normalizados.
- **DEF-10 (Fuentes Inline Fuera de Escala):** Resuelto. Creado `Reposa+/resources/sass/_typography.scss` con escala armónica (`display`, `h1`-`h6`, `body`, `caption`), clases utilitarias (`.fs-caption`, `.fs-body-sm`), ancho máximo de lectura ergonómica (`65ch`) y cifras tabulares (`font-feature-settings: 'tnum'`) para precios y cantidades. Eliminados todos los estilos inline `0.7rem` y `0.9rem`.
- **DEF-11 (Contenedor Anidado y Fondos Fluidos):** Resuelto. Desacoplado el `<main class="container mt-4">` rígido de `layouts/app.blade.php` a `<main id="main-content" class="main-content flex-grow-1">` permitiendo fondos fluidos de ancho completo (*full-bleed*) en cabeceras y hero sections. Añadido enlace de salto accesible (*skip-link*) al inicio del DOM y creado `Reposa+/resources/sass/_layout.scss` con rejilla espacial de múltiplos de 8px.

---

## Fase 4: Experiencia Persuasiva y Emocional del Storefront (PRIORIDAD ALTA)

**Objetivo:** Convertir el proceso de compra en una experiencia memorable y seductora, comunicando el valor del descanso profundo e incrementando la tasa de conversión.

**Comandos Impeccable vinculados:** `/impeccable shape`, `/impeccable bolder`, `/impeccable animate`, `/impeccable delight`

### 4.1 Modelado del Recorrido de Descubrimiento (`shape`)
- Diseñar la experiencia de la página de inicio (`home.blade.php`):
  - Bloque de propuesta de valor clara: "El descanso que tu cuerpo necesita".
  - Filtro interactivo por postura de sueño (de lado, boca arriba, boca abajo) y tipo de confort (firme, medio, suave).
  - Acceso directo a categorías con fotografía y destacados visuales.

### 4.2 Autoridad Visual y Puntos Focales (`bolder`)
- Transformar la sección Hero en una pieza visual de alto impacto con gradiente suave, titular persuasivo y métricas de confianza ("+10.000 descansos reparadores", "100 noches de prueba", "Envío gratis 24/48h").
- Destacar los botones de acción principal (CTA) con sombras ambientales suaves y tamaño táctil generoso.
- Añadir insignias de garantía y pago seguro de Stripe con iconografía impecable.

### 4.3 Microinteracciones y Coreografía de Movimiento (`animate`)
- Animación sutil al interactuar con el botón de favoritos (efecto latido con rebote suave).
- Transición fluida al abrir y cerrar modales, selectores y acordeones (`cubic-bezier(0.16, 1, 0.3, 1)`).
- Efecto de elevación suave (*smooth lift*) con sombra progresiva al posar el cursor sobre las tarjetas de producto.
- Notificaciones toast flotantes no intrusivas para confirmación de acciones ("Producto añadido a tu carrito").

### 4.4 Detalles Memorables de Marca (`delight`)
- Pantalla de confirmación de pedido (`orders/show.blade.php`) con animación sutil de celebración serena y timeline interactivo de preparación del envío.
- Mensajes contextuales agradables: "Prepárate para dormir como nunca" o "Tu nuevo descanso está en camino".

**Archivos a crear/modificar:**
- `Reposa+/resources/views/home.blade.php`
- `Reposa+/resources/views/catalog/index.blade.php`
- `Reposa+/resources/views/catalog/show.blade.php`
- `Reposa+/resources/views/orders/show.blade.php`
- `Reposa+/resources/js/interactions.js` (nuevo)
- `Reposa+/resources/sass/_animations.scss` (nuevo)

**Criterios de Aceptación:**
- La página de inicio transmite sensación de producto de gama alta con una jerarquía visual indudable.
- Animaciones a 60 FPS con respeto estricto a la preferencia del sistema `@media (prefers-reduced-motion: reduce)`.

**Resultados Entregados en Fase 4.1 (`shape`):**
- **Floating Sleep Finder ("Asesor Anatómico Reposa+"):** Implementado módulo interactivo de descubrimiento en la Home (`home.blade.php`) superpuesto de forma fluida tras la cabecera Hero. Incluye selector de 2 pasos ergonómicos: postura habitual (*De lado*, *Boca arriba*, *Boca abajo*) que preselecciona inteligentemente la firmeza anatómica recomendada, y nivel de firmeza (*Suave*, *Media*, *Firme/Anatómica*). Botón dinámico que computa y actualiza al vuelo la URL filtrada hacia `/catalog?firmness=...`.
- **Tarjetas Fotográficas de Categoría con Chips de Beneficio:** Sustituido el listado plano monocromático por tarjetas fotográficas de producto de alta resolución con overlay degradado sutil, tipografía jerárquica clara y badges destacados de beneficio ergonómico (*Alivio cervical*, *Efecto memoria*, *Frescor térmico*, *Respiración libre*, *Soporte activo*, *Cero presión*, *Confort portátil*, *Hipoalergénico*).
- **Internacionalización y Accesibilidad A11y:** Incorporadas cadenas completas en `lang/es/messages.php` y `lang/en/messages.php`. Soporte nativo de navegación por teclado (`aria-pressed`, `role="group"`), anillo de foco accesible y cláusula `@media (prefers-reduced-motion: reduce)` en `_layout.scss`.
- **Detector Impeccable:** Ejecutado `detect.mjs` arrojando `[]` (0 defectos de diseño). Suite de 60 tests de Feature al 100% verde.

**Resultados Entregados en Fase 4.2 (`bolder`):**
- **Hero de Alto Impacto y Gradiente Atmosférico:** Transformada la sección Hero en `home.blade.php` con un gradiente radial nocturno estratificado (*Midnight Sanctuary*) sobre `/images/hero.png` que garantiza contraste WCAG 2.1 AA superior a 6:1 en todo el bloque tipográfico. Titular persuasivo enfocado en beneficio emocional y descanso profundo.
- **Métricas de Confianza Focalizadas (Hero Trust Metrics):** Incorporada barra translúcida de métricas de autoridad ("+10.000 descansos reparadores", "100 noches de prueba sin riesgo", "Envío gratis 24/48h a península") con tipografía tabular (`tabular-nums`), cajas de icono de alto contraste e internacionalización completa ES/EN.
- **Botones de Acción Principal (CTA) Táctiles con Sombras Ambientales:** Rediseñados los botones de llamada a la acción principal (`btn-hero-primary`, `.btn-hero-secondary`, `.btn-cta-bold`) con altura táctil ergonómica generosa (mínimo 52px), padding táctil expandido, transiciones suaves y sombras ambientales multi-capa (`--shadow-ambient-cta`, `--shadow-hero-primary`, `--shadow-hero-lifted`) que eliminan la sensación plana y mejoran el punto focal sin estridencias.
- **Componente Reutilizable de Insignias y Sellos de Confianza (`<x-trust-seals>`):** Creado componente Blade polimórfico con variantes `cards` (rejilla de 4 pilares: 100 noches, 24/48h express, Stripe SSL 256-bit y OEKO-TEX), `compact` (ficha de producto `catalog/show.blade.php`), `checkout` (resumen de carrito `cart/index.blade.php`) y `footer` (pie de página global `layouts/app.blade.php`). Incluye isotipos vectoriales limpios de Stripe, Visa, Mastercard y candado de seguridad SSL sin dependencias externas.
- **Detector Impeccable y Verificación de Regresión:** Cero defectos reportados por `detect.mjs` en las vistas modificadas (`[]`). Suite de 60 tests de Feature al 100% verde (112 assertions).

**Resultados Entregados en Fase 4.3 (`animate`):**
- **Microinteracción del Botón de Favoritos (Heartbeat Spring Feedback):** Diseñada y coreografiada la animación del botón de favoritos con feedback de latido en dos pulsos con rebote amortiguado (`@keyframes heartBeatPulse`, `@keyframes heartIconPulse`) e interpolación de rotación y escala sobre el icono. Retirada de favoritos suave y desinflada (`@keyframes heartDeflate`). Integrado de forma universal en `<x-product-card>`, ficha de producto (`catalog/show.blade.php`) y perfil (`profile/index.blade.php`).
- **Transición Fluida y Amortiguada para Modales, Selectores y Desplegables:** Implementada la curva rectora `$ease-cushioned: cubic-bezier(0.16, 1, 0.3, 1)` en `_animations.scss` para modales (`.modal.fade .modal-dialog`), selectores e inputs con micro-elevación y resplandor indigo en foco (`.form-select:focus`, `.form-control:focus`), y acordeones/dropdowns con apertura serena.
- **Elevación Suave (*Smooth Lift*) en Tarjetas de Producto:** Tarjetas `<x-product-card>` con radio de 16px, reposo nítido con sombra ambiental suave (`box-shadow: 0 2px 8px rgba(24, 36, 71, 0.05)`) y elevación progresiva de `-6px` con sombra reactiva multicapa en hover (`0 20px 35px -8px rgba(24, 36, 71, 0.13), 0 8px 16px -4px rgba(24, 36, 71, 0.06)`), micro-zoom suave de la imagen (1.04) y soporte completo para navegación por teclado con `:focus-within`.
- **Notificaciones Toast Flotantes Reposa+ (Sanctuary Toasts):** Sistema de toasts no intrusivos (`.toast-sanctuary`) con pastilla cromática de estado, tipografía jerarquizada (título en negrita y mensaje secundario), temporizador de auto-cierre con barra de progreso visual, botón de cierre accesible y coreografía de entrada elástica (`@keyframes toastArrival`) y salida serena (`@keyframes toastSereneOut`). Integración con pulsación en el badge del carrito (`@keyframes cartBadgePulse`).
- **Módulo Dedicado de Interacciones y Accesibilidad:** Creado `resources/js/interactions.js` integrado en `app.js` eliminando scripts inline repetitivos. Respeto estricto a `@media (prefers-reduced-motion: reduce)` anulando transformaciones espaciales pero conservando el cambio de estado visual a 60 FPS sin saltos de layout (CLS = 0).
- **Detector Impeccable y Verificación de Tests:** Cero defectos detectados con `detect.mjs` (`[]`). Suite de 60 tests de Feature al 100% pasando (112 assertions).

**Resultados Entregados en Fase 4.4 (`delight`) y Cierre de Fase 4:**
- **Pantalla de Confirmación y Detalle de Pedido Rediseñada (`orders/show.blade.php`):** Celebración serena y sobria acorde a la atmósfera *Midnight Sanctuary* / descanso premium con insignia animada de aura restauradora (`sereneAuraGlow`), evitando confeti estridente o ruidoso. Encabezado inmersivo con badge dinámico de estado, referencia, fecha y confirmación de pago seguro.
- **Timeline Interactivo de Fases de Descanso:** 4 etapas secuenciales (*Confirmado*, *Preparando descanso*, *En reparto express*, *Entregado*) con conector visual compositado en GPU (`transform: scaleX(...)`), control de estado dinámico por pedido y panel interactivo accesible que actualiza al vuelo los detalles técnicos y de logística ergonómica al interactuar con cada fase.
- **Microcopy Cálido y Empático de Marca:** Titulares y mensajes reconfortantes (*"Prepárate para dormir como nunca"*, *"Tu nuevo descanso está en camino"*) y bloque memorable del *Ritual de bienvenida a tu almohada* (aireado de 24h, periodo de adaptación cervical de 7-14 noches y 100 noches de garantía).
- **Tarjeta de Resumen Económico y Acceso Directo a Factura PDF:** Tarjeta lateral adherente (*sticky-top*) con desglose transparente (subtotal, envío express gratuito 24/48h, IVA 21%, total destacado con tipografía tabular), botón de descarga directa de factura PDF (`orders.invoice`) y datos de entrega con soporte postural.
- **Internacionalización Completa ES/EN:** Cadenas 100% bilingües en `lang/es/messages.php` y `lang/en/messages.php`.
- **Rendimiento y Cero Defectos Impeccable:** Animación de progreso por GPU mediante `transform: scaleX()`, resolviendo la alerta de reflow `layout-transition` detectada por `detect.mjs` (`[]` - 0 defectos). Assets compilados limpiamente con Vite (`npm run build`).
- **Suite de Tests:** 60 tests de Feature ejecutados en Docker pasando al 100% (112 assertions).
- **Cierre de Fase 4:** Fase 4 completada al 100% en todas sus subfases (4.1 shape, 4.2 bolder, 4.3 animate, 4.4 delight).

---

## Fase 5: Claridad Transaccional, Estados Vacíos y Resiliencia (PRIORIDAD ALTA)

**Objetivo:** Despejar fricciones e incertidumbres durante el checkout, gestionar con elegancia todos los casos límite y transformar las pantallas vacías en oportunidades de descubrimiento.

**Comandos Impeccable vinculados:** `/impeccable clarify`, `/impeccable harden`, `/impeccable onboard`, `/impeccable distill`

### 5.1 Microcopy y Transparencia Transaccional (`clarify`)
- Clarificar el resumen de costos en el carrito y checkout: desglose inequívoco de subtotal, IVA incluido, gastos de envío (y umbral de "Faltan X € para envío gratuito").
- Mensajes de confianza al iniciar Stripe Checkout: "Pago seguro encriptado SSL con Stripe", "Garantía de devolución de 30 días sin compromiso".
- Actualizar y revisar todas las cadenas de traducción en `lang/es/messages.php` y `lang/en/messages.php` asegurando tono y terminología profesional.

### 5.2 Diseño Defensivo y Resiliencia UI (`harden`)
- Truncado elegante y soporte para nombres de productos largos y descripciones técnicas sin romper el grid.
- Sistema de fallback para imágenes que no carguen o productos sin foto asignada (placeholder SVG estilizado con la luna y estrella de Reposa+).
- Manejo visual de alertas de stock en tiempo real (bloqueo disabled del botón si stock = 0 con texto "Temporalmente agotado").
- Estados de error en formularios con mensajes contextuales bajo cada input y resaltado accesible.

### 5.3 Estados Vacíos Inspiradores y Activación (`onboard`)
- Rediseñar todas las pantallas vacías (*empty states*) con ilustraciones vectoriales amables y llamadas a la acción:
  - Carrito vacío: "Tu carrito está descansando. Descubre nuestros colchones estrella" con botón directo al catálogo.
  - Lista de deseos vacía: "Aún no has guardado favoritos. Guarda los productos que más te gusten para verlos luego".
  - Búsqueda sin resultados: "No encontramos lo que buscas. Prueba con otros términos o mira los más vendidos".
  - Historial de pedidos vacío: "Aún no tienes pedidos registrados".

### 5.4 Reducción de Ruido Cognitivo en Checkout (`distill`)
- Eliminar distracciones visuales en la pantalla previa a la pasarela de pago: suprimir banners secundarios o menús saturados.
- Resumen conciso de artículos con miniaturas nítidas, cantidad editable directa y botón de eliminación rápido sin recarga completa.

**Archivos a crear/modificar:**
- `Reposa+/resources/views/cart/index.blade.php`
- `Reposa+/resources/views/profile/index.blade.php`
- `Reposa+/resources/views/catalog/index.blade.php`
- `Reposa+/resources/views/catalog/show.blade.php`
- `Reposa+/resources/views/components/product-card.blade.php`
- `Reposa+/resources/views/components/empty-state.blade.php` (nuevo)
- `Reposa+/public/images/product-placeholder.svg` (nuevo)
- `Reposa+/lang/es/messages.php`
- `Reposa+/lang/en/messages.php`
- `Reposa+/resources/sass/_layout.scss`
- `Reposa+/app/Http/Controllers/ProfileController.php`

**Criterios de Aceptación:**
- Ningún estado de error o pantalla vacía muestra mensajes genéricos o pantallas en blanco.
- El flujo de pago presenta 0 ambigüedad sobre costes, tiempos de entrega y políticas de garantía.

**Resultados Entregados en Fase 5 (5.1 clarify, 5.2 harden, 5.3 onboard, 5.4 distill):**
- **Transparencia Transaccional en Cesta (`cart/index.blade.php`):** Desglose nítido y sin letra pequeña: Base imponible neta desglosada, IVA (21% incluido), coste de envío express 24/48h (GRATIS a península) y medidor dinámico de umbral de envío gratuito (50,00€) con barra de progreso reactiva y cálculo exacto de importe restante. Microcopy de confianza de alta autoridad previa al pago: protocolo cifrado SSL 256-bit y 3D Secure, 100 noches de prueba y aviso claro de redirección protegida a la pasarela bancaria de Stripe.
- **Diseño Defensivo y Resiliencia UI (`harden`):** Implementado placeholder vectorial exclusivo de la marca (`product-placeholder.svg`) con lienzo nocturno y silueta anatómica de descanso, erradicando fallbacks a dominios externos (`placehold.co` / `via.placeholder.com`). Soporte multi-línea con `line-clamp-2` y `line-clamp-3`, `word-break: break-word` y `min-width: 0` en tarjetas (`x-product-card`), tabla de cesta y fichas. Formularios de direcciones en perfil con validaciones semánticas, etiquetas `for`/`id` explícitas, mensajes de error contextuales en línea y estilizado moderno `:user-invalid`.
- **Estados Vacíos Persuasivos (`onboard`):** Creado componente universal `<x-empty-state>` con aura reposada, prueba social ("94% de alivio cervical") y doble llamada a la acción. Rediseñadas las 4 superficies vacías principales: Carrito ("Tu descanso aún te está esperando"), Búsqueda sin resultados en catálogo (con consejos de búsqueda y acceso directo al Asesor Anatómico), Historial de pedidos y Favoritos de perfil con inyección de almohadas top valoradas para descubrimiento instantáneo.
- **Destilación Cognitiva en Checkout (`distill`):** Eliminadas distracciones y ruido visual en la cesta; disposición jerárquica clara con panel lateral *sticky-top*, CTA dominante con distinción entre invitados y usuarios registrados, y visualización nítida de artículos con selector numérico accesible.
- **Detector Impeccable y Suite de Tests:** Cero defectos detectados con `detect.mjs` (`[]`). Suite de 60 tests de Feature ejecutados en Docker pasando al 100% (112 assertions). Cadenas 100% internacionalizadas en ES y EN. Assets compilados limpiamente con Vite.

---

## Fase 6: Densidad Operativa (Admin, Facturas) y Feature Estrella (PRIORIDAD MEDIA)

**Objetivo:** Diseñar un backoffice administrativo eficiente y libre de fatiga visual, perfeccionar las facturas PDF y concebir un elemento diferenciador (*signature feature*) en el storefront.

**Comandos Impeccable vinculados:** `/impeccable quieter`, `/impeccable overdrive`

### 6.1 Calma y Densidad de Datos en Administración (`quieter`)
- Rediseñar el panel de administración (`admin/dashboard.blade.php`):
  - Tipografía más compacta y controlada para tablas de productos, categorías y pedidos.
  - Gráficos de Chart.js estilizados con la paleta sobria (`#182447`, `#758ef9`, `#10B981`) y tooltips legibles.
  - Insignias de estado de pedido compactas (`completed`, `shipped`, `pending`, `refunded`, `cancelled`).
  - Filtros y acciones por fila bien jerarquizados (ver, editar, eliminar) sin sobrecargar la vista.

### 6.2 Factura PDF y Documentos de Marca (`quieter`)
- Rediseñar la plantilla de factura PDF (`invoices/invoice.blade.php`) generada por dompdf:
  - Cabecera limpia con logotipo vectorial y datos fiscales claros.
  - Tabla de productos alineada meticulosamente con cifras tabulares.
  - Desglose de impuestos (Base Imponible, IVA 21%, Total) formateado con precisión suiza.
  - Pie de página con cláusulas legales de desistimiento y contacto oficial.

### 6.3 Característica Estrella: Asesor de Firmeza y Descanso (`overdrive`)
- Crear un selector visual interactivo de firmeza (*Firmness Guide*) en la ficha de producto y en el catálogo:
  - Escala interactiva del 1 al 10 (Suave, Medio, Firme, Extra Firme) con indicador visual de alivio de presión.
  - Comparativa rápida de capas de materiales (Viscoelástica, Muelles ensacados, Espuma HR).

**Archivos a crear/modificar:**
- `Reposa+/resources/views/admin/dashboard.blade.php`
- `Reposa+/resources/views/admin/orders/index.blade.php`
- `Reposa+/resources/views/admin/products/index.blade.php`
- `Reposa+/resources/views/admin/partials/sidebar.blade.php`
- `Reposa+/resources/views/invoices/invoice.blade.php`
- `Reposa+/resources/views/catalog/partials/firmness-guide.blade.php` (nuevo)

**Criterios de Aceptación:**
- El panel de administración permite revisar 20 pedidos en una sola pantalla sin necesidad de scroll horizontal.
- La factura PDF se imprime y renderiza de forma impecable en un único folio A4 estándar.

---

## Fase 7: Ergonomía Móvil y Rendimiento Web Extremo (PRIORIDAD ALTA)

**Objetivo:** Garantizar que la experiencia en smartphones y tablets sea nativa, fluida y con tiempos de carga instantáneos.

**Comandos Impeccable vinculados:** `/impeccable adapt`, `/impeccable optimize`

### 7.1 Ergonomía Táctil y Navegación Móvil (`adapt`)
- Barra de navegación inferior móvil (*Mobile Bottom Bar*) con accesos directos al Catálogo, Búsqueda, Carrito (con badge numérico flotante) y Perfil.
- Barra flotante fija inferior en la ficha de producto en móvil con precio y botón "Comprar ahora / Añadir al carrito" para evitar perder la acción al hacer scroll.
- Dimensiones mínimas de objetivos táctiles de 48×48px en botones e inputs.
- Galería de fotos de producto optimizada para gestos táctiles (deslizamiento horizontal suave o miniaturas accesibles).

### 7.2 Optimización Web y Core Web Vitals (`optimize`)
- Asignación de dimensiones fijas `width` y `height` en todas las imágenes para eliminar el Cumulative Layout Shift (CLS = 0).
- Incorporación nativa de `loading="lazy"` y `decoding="async"` en todas las fotos de catálogo fuera del primer viewport.
- Optimización de fuentes de Google Fonts con `display=swap` y preconexión DNS.
- Compresión y purga de reglas CSS en Vite para entregar un paquete ligero.

**Archivos a crear/modificar:**
- `Reposa+/resources/views/layouts/app.blade.php`
- `Reposa+/resources/views/layouts/partials/mobile-nav.blade.php` (nuevo)
- `Reposa+/resources/views/catalog/show.blade.php`
- `Reposa+/resources/sass/_mobile.scss` (nuevo)

**Criterios de Aceptación:**
- Puntuación Core Web Vitals en verde en Chrome DevTools / Lighthouse (LCP < 1.8s, CLS < 0.05).
- Experiencia de compra completa realizable con una sola mano en dispositivos móviles estándar.

---

## Fase 8: Iteración en Vivo, Pulido Integral y Certificación (PRIORIDAD MEDIA)

**Objetivo:** Probar variantes en caliente con el servidor de desarrollo, refinar cada detalle visual al píxel y certificar la entrega completa para la defensa del TFG.

**Comandos Impeccable vinculados:** `/impeccable live`, `/impeccable polish`

### 8.1 Iteración en Navegador en Tiempo Real (`live`)
- Emplear el comando interactivo `/impeccable live` contra el servidor en ejecución en el puerto 8000 para experimentar con alternativas de tarjetas de producto, botones y filtros en vivo con feedback directo del usuario.
- Probar variantes A/B visuales de llamadas a la acción antes de consolidarlas en las vistas Blade.

### 8.2 Pase de Pulido Final y Micro-alineaciones (`polish`)
- Revisión microscópica de alineaciones verticales, espaciados entre iconos y texto, sombras consistentes y bordes suaves (`border-radius`).
- Comprobación en múltiples navegadores (Google Chrome, Mozilla Firefox, Apple Safari) y motores de renderizado.
- Verificación del cambio fluido de idioma (`/lang/es` y `/lang/en`) sin roturas tipográficas en botones o cabeceras.
- Limpieza final de clases CSS obsoletas y comentarios de depuración.

**Archivos a modificar:**
- `Reposa+/resources/views/` (revisión general de vistas)
- `Reposa+/resources/sass/app.scss`
- `docs/progreso/certificacion-diseno-final.md` (informe final de entrega de diseño)

**Criterios de Aceptación:**
- Interfaz calificada como de calidad comercial lista para defensa del TFG.
- Cero advertencias visuales en el detector de Impeccable (`detect.mjs`).

---

## Checklist de Fases y Progreso

| Fase | Hito / Entregable | Estado |
|---|---|:---:|
| **Fase 1** | Contexto `PRODUCT.md` y `DESIGN.md` inicializados | ✅ Completada |
| **Fase 1** | Tokens SCSS y primeros componentes Blade extraídos | ✅ Completada |
| **Fase 2** | Auditoría heurística y de accesibilidad completada | ✅ Completada |
| **Fase 2** | Contrastes WCAG AA y accesibilidad por teclado diagnosticados | ✅ Completada |
| **Fase 3** | Nueva escala tipográfica dual integrada (`_typography.scss`) | ✅ Completada |
| **Fase 3** | Paleta cromática de descanso aplicada en toda la app (`_tokens.scss`) | ✅ Completada |
| **Fase 3** | Layouts y espaciados armónicos en catálogo y producto (`_layout.scss`) | ✅ Completada |
| **Fase 4** | Home persuasiva con propuesta de valor y Hero renovado | ✅ Completada (4.1 shape y 4.2 bolder) |
| **Fase 4** | Microinteracciones y animaciones de favoritos y carrito | ✅ Completada (4.3 animate) |
| **Fase 4** | Confirmación de pedido emocional y de marca | ✅ Completada (4.4 delight) |
| **Fase 5** | Microcopy claro y transparente en checkout | ✅ Completada (5.1 clarify y 5.4 distill) |
| **Fase 5** | Casos límite cubiertos (textos largos, fallback de fotos) | ✅ Completada (5.2 harden) |
| **Fase 5** | Estados vacíos de carrito, wishlist y búsqueda implementados | ✅ Completada (5.3 onboard) |
| **Fase 6** | Panel de administración rediseñado con alta densidad limpia | ⏳ Pendiente |
| **Fase 6** | Factura PDF con diseño corporativo impecable | ⏳ Pendiente |
| **Fase 6** | Guía/selector interactivo de firmeza en el catálogo | ⏳ Pendiente |
| **Fase 7** | Navegación móvil y sticky purchase bar adaptadas | ⏳ Pendiente |
| **Fase 7** | Optimización Core Web Vitals (CLS = 0, lazy loading) | ⏳ Pendiente |
| **Fase 8** | Sesión de variantes en vivo con `/impeccable live` | ⏳ Pendiente |
| **Fase 8** | Pase de pulido final y certificación de entrega | ⏳ Pendiente |

---

## Desviaciones del Plan

*Registro de adaptaciones y decisiones que surjan durante la ejecución de las fases de diseño respecto a la planificación inicial.*

| Fecha | Fase afectada | Descripción de la desviación | Motivo |
|---|---|---|---|
| 01/09/2026 | Planificación | Integración del 100% de los 22 comandos de Impeccable en 8 fases coherentes | Maximizar la calidad del diseño y aprovechar al máximo las capacidades de la suite |
| 02/09/2026 | Fase 1 (Fundamentos) | Generación formal de `.impeccable/design.json` además de tokens en SCSS | Permitir interoperabilidad técnica con herramientas de diseño automatizado e inspección JSON bidireccional |
| 02/09/2026 | Fase 2 (Diagnóstico) | Formalización de 11 defectos específicos (DEF-01 a DEF-11) y doble scoring metodológico (Nielsen 23/40 e Impeccable 10/20) en `auditoria-heuristica-ux.md` | Transformar hallazgos abstractos en una lista de trabajo técnica priorizada y medible para las fases posteriores |
| 02/09/2026 | Fase 3 (Identidad y Layout) | Priorización inmediata en Fase 3 de los 5 defectos clave de Fase 2: DEF-01 (contraste Serene Indigo), DEF-04 (recarga selectores), DEF-09 (encabezados semánticos), DEF-10 (fuentes inline) y DEF-11 (desacople de `.container` rígido) | Resolver de raíz las barreras de accesibilidad y estabilidad de layout antes de abordar las fases persuasivas y emocionales |
| 02/09/2026 | Fase 3 (Infraestructura / DB) | Determinismo en `DatabaseSeeder` y control anti-duplicados en `CategoryFactory` | `CategoryFactory` lanzaba excepciones `1062 Duplicate entry` por colisión de slugs únicos, interrumpiendo silenciosamente el sembrado y dejando la BD sin productos |
| 02/09/2026 | Fase 3 (Testing / CI) | Aislamiento completo de la base de datos de test (`reposaplus_testing` en MySQL y `phpunit.xml`) | La suite de tests (Pest) se ejecutaba contra `reposaplus_dev` con el trait `RefreshDatabase`, borrando todos los productos de la tienda de desarrollo tras cada comprobación técnica |
| 02/09/2026 | Fase 3 (Frontend / Tipografía) | Cambio de color rígido en encabezados a `color: inherit` en `_typography.scss` y clases explícitas `text-white` | La regla inicial asignaba `color: var(--color-text-primary)` (`#182447` azul marino) a todos los encabezados y a `.navbar-brand`, provocando que el logotipo y el titular del hero fueran invisibles sobre fondos oscuros |
| 02/09/2026 | Fase 3 (Frontend / Interacciones) | Rediseño del hover en tarjetas de categoría: sustitución de la inversión de fondo azul marino por elevación táctil luminosa (`4px`, sombra suave, fondo blanco persistente) | Invertir la tarjeta a azul oscuro sobre un lienzo blanco causaba un efecto de "agujero negro" y daba la sensación al usuario de que el contenido desaparecía |
| 02/09/2026 | Fase 3 (Frontend / Identidad Visual) | Implementación de estilo Glassmorphism nocturno (`backdrop-filter: blur(12px)`, `rgba(255, 255, 255, 0.18)`) en los botones CTA de la cabecera Hero | Los botones con fondo sólido (púrpura `#4F46E5` o blanco puro) generaban un contraste desmedido y estridente sobre la fotografía nocturna, rompiendo la atmósfera de descanso |
| 03/09/2026 | Fase 4 (Frontend / Descubrimiento) | Sustitución de accesos planos por 'Floating Sleep Finder' en 2 pasos y tarjetas de categoría fotográficas con chips de beneficio anatómico | Maximizar la capacidad persuasiva desde el primer pliegue de la Home, guiando al usuario sin fricción técnica hacia su almohada ideal |
| 03/09/2026 | Fase 4 (Infraestructura / Testing) | Forzado estricto de aislamiento con `force="true"` en `DB_DATABASE` dentro de `phpunit.xml` | El contenedor Docker inyectaba `DB_DATABASE=reposaplus_dev` como variable de SO. Al tener `force="false"` por defecto en PHPUnit, los tests ignoraban la BD de test y vaciaban `reposaplus_dev` con `RefreshDatabase` |
| 03/09/2026 | Fase 4.3 (Frontend / Motion) | Modularización de interacciones en `interactions.js` y `_animations.scss`, reemplazando scripts inline con Toasts Sanctuary y curva `cubic-bezier(0.16, 1, 0.3, 1)` | Centralizar lógica de microinteracciones, eliminar scripts inline dispersos, garantizar CLS 0 a 60 FPS y cumplimiento estricto con el detector Impeccable |
| 03/09/2026 | Fase 4.4 (Frontend / Rendimiento) | Sustitución de animación de `width` por `transform: scaleX(...)` con `transform-origin: left center` en la barra de progreso del timeline del pedido | Evitar recálculos de layout (*layout thrash* / *reflow*) detectados por la regla `layout-transition` del detector Impeccable, garantizando animación pura por GPU a 60 FPS |
| 03/09/2026 | Fase 5 (Frontend / Resiliencia) | Sustitución de placeholders externos (`placehold.co` / `via.placeholder.com`) por activo SVG vectorial local de marca (`product-placeholder.svg`) con degradado nocturno y luna Reposa+ | Eliminar llamadas HTTP externas a servicios de terceros, garantizar funcionamiento 100% offline y resiliencia en entornos de testing sin red |
| 03/09/2026 | Fase 5 (Frontend / Conversión) | Implementación de medidor dinámico de envío gratuito (50,00€) y desglose neto de base imponible + 21% IVA en `cart/index.blade.php` | Reducir fricción y abandono de carrito aportando certidumbre absoluta sobre costes y tiempos de entrega antes de Stripe Checkout |
| 03/09/2026 | Fase 5 (Frontend / Onboarding) | Inyección de almohadas top valoradas en estado vacío de favoritos (`profile/index.blade.php`) y consejos con acceso directo a Asesor Anatómico en búsqueda vacía (`catalog/index.blade.php`) | Transformar pantallas vacías en vías activas de descubrimiento y persuasión hacia la compra |

---

## Protocolo de Cierre de Sesión

Al finalizar cada sesión de desarrollo orientada al diseño:

1. **Detector Impeccable:** Ejecutar `node .agent/skills/impeccable/scripts/detect.mjs --json <archivos modificados>` para comprobar que no existan patrones degradados de diseño.
2. **Roadmap:** Actualizar el checklist de este archivo (`docs/progreso/roadmap-diseno-ecommerce.md`) con las fases y subfases completadas.
3. **Desviaciones:** Registrar cualquier decisión estética o técnica imprevista en la tabla de desviaciones.
4. **Git:** Crear los commits semánticos en la rama de trabajo correspondiente (`feat/ui-...`, `style/...`).
5. **Prompt:** Redactar el estado de avance y el punto de reanudación exacto para la siguiente sesión.

---

*Documento generado para Reposa+ TFG 2026 — Metodología Impeccable*
