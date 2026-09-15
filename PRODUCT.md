# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

- **Público primario:** Adultos de 25 a 55 años que experimentan molestias cervicales, tensión muscular al despertar, dificultades para conciliar el sueño o insomnio, así como personas conscientes de la higiene postural que desean optimizar la calidad de su descanso nocturno.
- **Necesidad y situación:** Buscan una almohada con soporte ergonómico real, materiales transpirables y especificaciones claras (altura, firmeza, composición). Valoran la transparencia en costes de envío, tiempos de entrega y políticas de garantía/prueba.
- **Público secundario (operativo):** Administradores de la tienda encargados de la catalogación, actualización de stock, gestión de pedidos e inspección de clientes.

## Product Purpose

- Reposa+ es un e-commerce vertical especializado en descanso y ergonomía, centrado en almohadas técnicas y confortables.
- **Propuesta de valor:** *"No vendemos almohadas, vendemos noches de sueño profundo y reparador."*
- **Definición de éxito:** Brindar una experiencia de compra fluida, serena y transparente que transmita confianza inmediata desde el primer contacto visual hasta el checkout y la recepción del pedido, reduciendo la ansiedad de compra y devoluciones.

## Positioning

- **Arquetipo:** *Sanctuary Sleep / Wellness Premium* — equilibrio entre respaldo ergonómico de precisión y una atmósfera cálida, acogedora y estética, alejada tanto de la frialdad clínica ortopédica como del bullicio publicitario estridente del retail generalista.
- **Diferenciación honesta:** Información visual detallada de las propiedades biomecánicas (firmeza, transpirabilidad, soporte lumbar/cervical), disponibilidad de stock transparente en tiempo real y proceso de compra sin patrones oscuros.

## Operating Context

- **Entorno del usuario:** Navegación en móvil en momentos de descanso nocturno o relajación en el hogar; comparativas pausadas y tramitación de pedidos en escritorio.
- **Entorno del sistema:** Tienda web con arquitectura de renderizado en servidor (Laravel Blade), soporte bilingüe nativo (español e inglés), pasarela de pago dual (Stripe Checkout y compra directa en entorno local/test), facturas descargables en PDF y notificaciones transaccionales por email.

## Capabilities and Constraints

- **Stack tecnológico estricto:** Laravel 11, plantillas y componentes Blade (`<x-...>`), Bootstrap 5.3 con personalización SCSS (`resources/sass/app.scss`), Vite, Bootstrap Icons, Vanilla JS / Bootstrap bundle y Axios.
- **Prohibición de frameworks SPA:** No introducir React, Vue, Inertia u otras dependencias externas que distorsionen el renderizado Blade del proyecto.
- **Integridad de lógica y localización:** Respeto absoluto a los controladores existentes, modelos Eloquent, transacciones de checkout y funciones de traducción de Laravel (`{{ __('messages.section.key') }}`).
- **Mantenimiento de tests:** Salvaguardar el paso en verde de la suite completa de 60 tests de Feature y E2E (112 assertions).

## Brand Commitments

- **Nombre y tono:** Reposa+. Voz empática, serena, transparente y profesional. Nunca recurrir a promesas milagrosas pseudocientíficas ni tecnicismos incomprensibles.
- **Identidad cromática base (Índigo de descanso):**
  - Índigo profundo nocturno (`#182447`): Cabeceras, navegación y elementos de autoridad.
  - Índigo medio de transición (`#42569a`): Enlaces, estados hover y bordes suaves.
  - Azul acento de descanso (`#758ef9`): Llamadas a la acción y elementos clave.
  - Tintes índigo desaturados y blancos puros (`#b1cdff`, `#ffffff`): Fondos de tarjetas, contraste limpio y sensación de ingravidez.

## Evidence on Hand

- Catálogo existente de almohadas y categorías implementadas con modelos Eloquent (`Product`, `Category`, `Order`, `OrderItem`, `User`, `Address`, `Favorite`).
- Documentación conceptual de partida en `docs/memoria_inicial/Inf-E-Comerce.md` y `docs/artefactos/wireframe-mocks.md`.
- Vistas Blade funcionales completas en `Reposa+/resources/views/`.
- Suite de pruebas automatizadas en `Reposa+/tests/Feature/`.

## Product Principles

1. **Paz visual y descanso cognitivo:** Cada pantalla debe transmitir tranquilidad y orden, con espacios de respiración generosos y jerarquías tipográficas que faciliten la lectura relajada.
2. **Claridad técnica y honestidad:** Presentar firmezas, dimensiones y materiales de forma precisa para permitir decisiones informadas sobre la salud postural.
3. **Fricción cero en el descubrimiento y pago:** Acceso ágil al catálogo, carrito y checkout sin pasos redundantes ni sorpresas en costes o disponibilidad.
4. **Ingeniería de componentes modular:** Construcción de elementos reutilizables mediante componentes Blade encapsulados y tokens de diseño SCSS estandarizados.

## Accessibility & Inclusion

- Adhesión al estándar **WCAG 2.1 Nivel AA**.
- Ratios de contraste adecuados en textos y botones de acción (mínimo 4.5:1).
- Navegación completa por teclado accesible con estados `:focus-visible` discernibles.
- Semántica accesible y etiquetas ARIA en controles interactivos, badges y elementos iconográficos.
