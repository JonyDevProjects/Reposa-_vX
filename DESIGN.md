---
name: Reposa+
description: E-commerce de descanso premium y salud postural
colors:
  primary: "#182447"
  secondary: "#42569a"
  accent: "#758ef9"
  light-tint: "#b1cdff"
  canvas: "#f8fafc"
  surface: "#ffffff"
  text-primary: "#182447"
  text-muted: "#6c757d"
  success: "#198754"
  warning: "#ffc107"
  danger: "#dc3545"
typography:
  display:
    fontFamily: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
    fontSize: "clamp(2.5rem, 5vw, 4rem)"
    fontWeight: 700
    lineHeight: 1.15
    letterSpacing: "-0.02em"
  headline:
    fontFamily: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
    fontSize: "2rem"
    fontWeight: 700
    lineHeight: 1.25
    letterSpacing: "-0.01em"
  title:
    fontFamily: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
    fontSize: "1.25rem"
    fontWeight: 600
    lineHeight: 1.4
  body:
    fontFamily: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
    fontSize: "1rem"
    fontWeight: 400
    lineHeight: 1.5
  label:
    fontFamily: "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 600
    lineHeight: 1.3
rounded:
  sm: "4px"
  md: "8px"
  lg: "15px"
  xl: "24px"
  pill: "50rem"
spacing:
  xs: "4px"
  sm: "8px"
  md: "16px"
  lg: "24px"
  xl: "32px"
  xxl: "48px"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.surface}"
    rounded: "{rounded.md}"
    padding: "10px 25px"
  button-primary-hover:
    backgroundColor: "{colors.secondary}"
  button-secondary:
    backgroundColor: "{colors.accent}"
    textColor: "{colors.surface}"
    rounded: "{rounded.md}"
    padding: "10px 25px"
  card-product:
    backgroundColor: "{colors.surface}"
    rounded: "{rounded.lg}"
    padding: "16px"
  badge-stock-in:
    backgroundColor: "{colors.success}"
    textColor: "{colors.surface}"
    rounded: "{rounded.pill}"
    padding: "6px 14px"
  badge-stock-out:
    backgroundColor: "{colors.danger}"
    textColor: "{colors.surface}"
    rounded: "{rounded.pill}"
    padding: "6px 14px"
---

# Design System: Reposa+

## Overview

**Creative North Star: "The Midnight Sanctuary" (El Santuario de Medianoche)**

Reposa+ sumerge al usuario en un entorno visual que evoca calma, protección y desconexión sensorial. Cada punto de contacto está pensado para preparar al cuerpo y a la mente para el descanso nocturno. La interfaz huye conscientemente del bullicio publicitario estridente (carteles rojos de descuento masivo o cuentas atrás agobiantes) y de la esterilidad aséptica del material ortopédico clínico de farmacia.

El sistema equilibra una rigurosa claridad técnica sobre ergonomía, materiales viscoelásticos y alturas posturales con una experiencia visual cálida, acogedora y refinada. Los elementos respiran con amplitudes de espacio generosas, fondos limpios en blanco y niebla azulada, y una paleta nocturna en tonos índigo que transmite confianza inmediata.

**Key Characteristics:**
- **Atmósfera Nocturna Serena:** Predominio del índigo profundo (`#182447`) complementado por suaves reflejos perlados y acentos periwinkle (`#758ef9`).
- **Respiración y Claridad Espacial:** Contenedores generosos, ritmo vertical fluido y eliminación de elementos visuales superfluos.
- **Microinteracción Suave y Orgánica:** Esquinas redondeadas (15px/24px) que evocan la ergonomía de una almohada, con elevación física agradable en hover.
- **Honestidad y Confianza Inmediata:** Precios legibles, indicadores de stock transparentes y sellos de beneficio (envío gratis, días de prueba) visibles sin fricción.

## Colors

La paleta cromática se articula en torno a la cromoterapia del sueño: tonos índigos que transmiten descanso profundo, serenidad y sofisticación, combinados con blancos puros que aportan sensación de higiene y frescura transpirable.

### Primary
- **Deep Sanctuary Navy** (`#182447`): El núcleo de identidad de la marca. Aplicado en barras de navegación, cabeceras principales, botones de compra primarios, tipografía de alta jerarquía y precios. Evoca el cielo nocturno y la estabilidad firme.

### Secondary
- **Twilight Indigo** (`#42569a`): Tono de transición crepuscular. Utilizado en estados `:hover` de botones primarios, degradados de cabeceras hero y elementos secundarios que requieren atención sin estridencias.

### Accent
- **Dream Periwinkle** (`#758ef9`): Acento cromático luminoso y moderno. Empleado en llamadas a la acción complementarias, barras decorativas de sección, insignias y estados `:focus-visible` accesibles.

### Soft Tint & Backgrounds
- **Celestial Mist** (`#b1cdff`): Tinte suave y relajante utilizado en chips de material, insignias sutiles y fondos de soporte que aligeran el peso visual.
- **Slate Mist** (`#f8fafc`): Fondo general del lienzo web (`body`). Aporta un matiz ligeramente frío que mitiga el deslumbramiento de pantallas en horas nocturnas.
- **Pure Linen** (`#ffffff`): Fondo de tarjetas, superficies elevadas y contenedores de compra. Representa la limpieza y transpirabilidad de la ropa de cama.

### Status & Feedback
- **Restored Green** (`#198754`): Disponibilidad confirmada (*En stock*) y beneficios comerciales (*Envío gratuito*).
- **Amber Caution** (`#ffc107`): Valoraciones de clientes (estrellas) y alertas preventivas de inventario (*Últimas unidades*).
- **Exhausted Red** (`#dc3545`): Producto agotado, alertas críticas de stock y botón de favoritos activos.

### Named Rules
**The Sanctuary Rarity Rule.** El azul acento (*Dream Periwinkle*) nunca ocupa más del 15% del área visible en pantalla. Su función es orientar el ojo hacia la acción clave con delicadeza, sin saturar la atmósfera de relajación.
**The No-Aggression Rule.** Quedan prohibidos los fondos de urgencia roja estridente o las tipografías parpadeantes para forzar la compra impulsiva; la urgencia se comunica mediante microcopy sobrio y respetuoso.

## Typography

**Display Font:** Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif  
**Body Font:** Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif  

**Character:** Inter aporta una legibilidad geométrica cristalina con proporciones humanistas contemporáneas. Sus pesos altos (600 y 700) transmiten solidez postural y autoridad de marca, mientras que el peso regular (400) ofrece una lectura descansada y sin fatiga visual.

### Hierarchy
- **Display** (700, clamp(2.5rem, 5vw, 4rem), 1.15): Titulares de impacto en cabeceras de bienvenida (*Hero*) y grandes lemas de producto.
- **Headline** (700, 2rem / 32px, 1.25): Títulos de categorías, encabezados de catálogo y fichas de producto.
- **Title** (600, 1.25rem / 20px, 1.4): Títulos en tarjetas de producto, títulos de resumen de pedido y títulos de modales.
- **Body** (400, 1rem / 16px, 1.5): Textos explicativos, descripciones de producto (longitud máxima aconsejada de 65ch para confort de lectura) y filas de tablas.
- **Label** (600, 0.875rem / 14px, 1.3): Etiquetas de formulario, nombres de especificaciones técnicas y textos en botones.
- **Caption / Tiny** (500, 0.75rem / 12px, 1.2): Indicadores de stock, chips de material y notas legales a pie de página.

### Named Rules
**The Posture Alignment Rule.** Todo precio principal debe presentarse con peso 700 y alineación visual directa con su acción de compra o ficha descriptiva, sin saltos tipográficos intermedios.

## Layout

El sistema espacial adopta la cuadrícula de 12 columnas de Bootstrap 5 con un contenedor central fluido (`max-width: 1320px` en pantallas grandes) y márgenes de respiración consistentes:

- **Contenedores y Gutters:** Espaciado entre tarjetas mediante `gap-4` (24px) y `gap-5` (48px en páginas de producto).
- **Ritmo Vertical:** Secciones delimitadas por `py-5` (48px) y cabeceras con `mb-4` o `mb-5`.
- **Estructura Flotante y Adherente:** Barras laterales de filtros (`catalog`) y resumen de cesta (`cart`) utilizan posicionamiento `sticky-top` con distancia de seguridad (`top: 100px`) para acompañar la decisión de compra sin desorientar al usuario.

## Elevation & Depth

Reposa+ utiliza un modelo híbrido **Estratificado y Ambiental (Layered & Ambient)**. En estado de reposo, la interfaz es predominantemente plana y limpia, con fondos blancos apoyados sobre el lienzo `#f8fafc` delimitados por bordes ultra-suaves o sombras atmosféricas imperceptibles.

La elevación física es reactiva y táctil: se activa al posar el cursor o interactuar, transmitiendo la sensación física de levantar y probar la almohada.

### Shadow Vocabulary
- **Ambient Low** (`box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08)`): Sombras de reposo para tarjetas de producto, inputs y barras de navegación.
- **Interactive Lift** (`box-shadow: 0 10px 20px rgba(24, 36, 71, 0.10)`): Elevación física acompañada de `transform: translateY(-5px)` en hover de tarjetas de producto y botones flotantes.
- **Floating Badge** (`box-shadow: 0 4px 12px rgba(24, 36, 71, 0.15)`): Insignias flotantes en imágenes de catálogo para asegurar legibilidad sobre cualquier fondo de fotografía.

### Named Rules
**The Rest-to-Touch Rule.** Las superficies están en reposo estático sin sombras pesadas. Las sombras profundas solo emergen ante la intención directa del usuario (hover, foco o modal abierto).

## Shapes

El lenguaje formal está inspirado en las curvas anatómicas y el contorno mullido de las almohadas ergonómicas:

- **Tarjetas de Producto:** Radio curvo amplio de `15px` (`border-radius: 15px`) que transmite suavidad y ergonomía.
- **Contenedores de Ficha y Módulos de Confianza:** Esquinas redondeadas extra generosas de `24px` (`rounded-4`).
- **Botones:** Radio ergonómico de `8px` (`border-radius: 8px`) para botones de acción rectangulares, o formato píldora (`rounded-pill`) para llamadas a la acción envolventes.
- **Botones de Acción Rápida:** Círculos perfectos (`rounded-circle`) para añadir a la cesta o marcar favoritos en la parrilla del catálogo.
- **Badges:** Cápsula completa (`rounded-pill`) para estados de stock y disponibilidad.

## Components

### Buttons
- **Shape:** Esquinas redondeadas de 8px (`rounded-md`) o píldora completa (`rounded-pill`).
- **Primary:** Fondo `Deep Sanctuary Navy` (`#182447`), texto blanco, padding `10px 25px`, fuente Inter 600.
- **Hover / Focus:** Transición a `Twilight Indigo` (`#42569a`), elevación de -1px; anillo de foco visible `2px solid #758ef9` con offset de 2px.
- **Secondary:** Fondo `Dream Periwinkle` (`#758ef9`), texto blanco, padding `10px 25px`.
- **Outline / Ghost:** Borde de 1px en `#182447` o `#dc3545` (favoritos) con fondo transparente; transición al color sólido en hover.

### Product Card (`card-product`)
- **Corner Style:** 15px de radio curvo (`border-radius: 15px`), sin bordes exteriores visibles (`border: none`).
- **Background:** Blanco puro (`#ffffff`) sobre lienzo `#f8fafc`.
- **Shadow Strategy:** `shadow-sm` en reposo; `0 10px 20px rgba(24, 36, 71, 0.10)` con desplazamiento de `-5px` en hover (transición de 0.3s cubic-bezier).
- **Internal Padding:** 16px a 20px en el cuerpo de la tarjeta.
- **Hierarchy:** Imagen superior con relación de aspecto equilibrada, chip de material, título de producto bold en dos líneas máximo, extracto descriptivo en gris suave, precio destacado en fs-4 e iconos circulares de compra y favoritos.

### Stock Badges (`badge-stock`)
- **In Stock:** Cápsula verde esmeralda (`#198754`), texto blanco, icono de check `bi-check-circle`.
- **Low Stock:** Cápsula ambarina (`#fff3cd`), texto `#856404`, icono de caja `bi-box-seam`.
- **Out of Stock:** Cápsula roja (`#dc3545`), texto blanco, icono de cierre `bi-x-circle`.

### Inputs & Search Bars
- **Style:** Fondo `#ffffff` o `#f8fafc`, borde de 1px `#dee2e6`, radio de 8px.
- **Focus:** Borde en `#758ef9`, resplandor tenue en azul descanso (`box-shadow: 0 0 0 0.25rem rgba(117, 142, 249, 0.25)`).

### Navigation
- **Navbar:** Fondo `Deep Sanctuary Navy` (`#182447`), texto e iconos en blanco, logotipo con icono de luna `bi-moon-stars-fill`, menú desplegable con fondo blanco e indicador de idioma.

## Do's and Don'ts

### Do:
- **Do** mantener el espacio de respiración generoso en todas las vistas de catálogo y producto (mínimo 24px entre tarjetas y 48px entre secciones).
- **Do** emplear esquinas curvas suaves (15px en tarjetas, 8px en botones) para conservar la coherencia ergonómica de la marca.
- **Do** asegurar que todo texto sobre fondos de color cumpla un ratio de contraste mínimo de 4.5:1 (WCAG AA).
- **Do** indicar siempre el estado de disponibilidad del producto y las opciones de prueba/envío antes de solicitar el pago.
- **Do** utilizar componentes Blade reutilizables (`<x-...>`) para cualquier repetición de tarjetas, precios, badges o alertas.

### Don't:
- **Don't** recurrir a colores fluorescentes o banners de descuento estridentes que rompan la atmósfera de serenidad y descanso.
- **Don't** aplicar bordes negros pesados o ángulos rectos cortantes en elementos interactivos; Reposa+ es orgánico y envolvente.
- **Don't** ocultar el coste final o los gastos de envío hasta el último paso del proceso de compra.
- **Don't** emplear terminología médica o diagnósticos clínicos alarmistas en las descripciones de almohadas ergonómicas.
- **Don't** romper la compatibilidad de los 60 tests de Feature existentes ni sustituir el sistema de localización nativo de Laravel `__()`.
