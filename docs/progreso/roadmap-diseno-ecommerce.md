# Roadmap: Excelencia en Diseño y UX/UI — Reposa+ (Impeccable)

## Contexto

Este documento define la planificación integral para transformar la interfaz, arquitectura de información y experiencia de usuario (UX/UI) de **Reposa+** en un e-commerce de descanso premium (*sleep wellness*), elevando el producto de un prototipo funcional académico a un estándar de diseño comercial de alta gama (*out-of-distribution craft*).

El plan adopta como metodología la suite de diseño **Impeccable**, incorporando de manera secuencial y estructurada sus **22 comandos especializados**, articulados en **8 fases de ejecución**.

**Fecha de creación:** 01 de septiembre de 2026  
**Última sesión:** 01/09/2026 — Planificación del Roadmap de Diseño  
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
| **2** | Evaluación Heurística, Accesibilidad y Diagnóstico | `critique`, `audit` | ⏳ Pendiente |
| **3** | Identidad Cromática, Tipografía y Armonía Espacial | `typeset`, `colorize`, `layout` | ⏳ Pendiente |
| **4** | Experiencia Persuasiva y Emocional del Storefront | `shape`, `bolder`, `animate`, `delight` | ⏳ Pendiente |
| **5** | Claridad Transaccional, Estados Vacíos y Resiliencia | `clarify`, `harden`, `onboard`, `distill` | ⏳ Pendiente |
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
- `Reposa+/resources/views/components/empty-state.blade.php` (nuevo)
- `Reposa+/lang/es/messages.php`
- `Reposa+/lang/en/messages.php`

**Criterios de Aceptación:**
- Ningún estado de error o pantalla vacía muestra mensajes genéricos o pantallas en blanco.
- El flujo de pago presenta 0 ambigüedad sobre costes, tiempos de entrega y políticas de garantía.

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
| **Fase 1** | Contexto `PRODUCT.md` y `DESIGN.md` inicializados | ⏳ Pendiente |
| **Fase 1** | Tokens SCSS y primeros componentes Blade extraídos | ⏳ Pendiente |
| **Fase 2** | Auditoría heurística y de accesibilidad completada | ⏳ Pendiente |
| **Fase 2** | Contrastes WCAG AA y accesibilidad por teclado resueltos | ⏳ Pendiente |
| **Fase 3** | Nueva escala tipográfica dual integrada | ⏳ Pendiente |
| **Fase 3** | Paleta cromática de descanso aplicada en toda la app | ⏳ Pendiente |
| **Fase 3** | Layouts y espaciados armónicos en catálogo y producto | ⏳ Pendiente |
| **Fase 4** | Home persuasiva con propuesta de valor y Hero renovado | ⏳ Pendiente |
| **Fase 4** | Microinteracciones y animaciones de favoritos y carrito | ⏳ Pendiente |
| **Fase 4** | Confirmación de pedido emocional y de marca | ⏳ Pendiente |
| **Fase 5** | Microcopy claro y transparente en checkout | ⏳ Pendiente |
| **Fase 5** | Casos límite cubiertos (textos largos, fallback de fotos) | ⏳ Pendiente |
| **Fase 5** | Estados vacíos de carrito, wishlist y búsqueda implementados | ⏳ Pendiente |
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
