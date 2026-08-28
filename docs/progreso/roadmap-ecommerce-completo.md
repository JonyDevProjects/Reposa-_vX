# Roadmap: E-commerce Completo — Reposa+

## Contexto

Este documento define las fases restantes para llevar Reposa+ al nivel de un e-commerce real. Se incluye el trabajo ya realizado (pasarela de pagos con Stripe) como parte integral del roadmap, y se detallan las funcionalidades pendientes organizadas por prioridad y dependencias.

**Fecha de creación:** 28 de agosto de 2026
**Última sesión:** Implementación de Stripe Checkout + GitFlow reorganization

---

## Estado Actual del Proyecto

### Completado ✅

| Fase | Funcionalidad | Estado |
|------|--------------|--------|
| 1 | Setup Laravel + Bootstrap 5 + autenticación (Fortify) | ✅ |
| 2 | Base de datos: esquema UML, migraciones, seeders, vistas SQL | ✅ |
| 3 | Catálogo público: Home, Catálogo, Ficha de Producto | ✅ |
| 4 | Carrito de compra (invitado + autenticado, AJAX) | ✅ |
| 5 | Checkout con transacciones DB y decremento de stock | ✅ |
| 6 | Notificaciones SMTP (Mailtrap + Queues) | ✅ |
| 7 | Panel de administración (CRUD productos, categorías, pedidos) | ✅ |
| 8 | Filtrado por categorías (relación N:M) | ✅ |
| 9 | Internacionalización (Español/Inglés) | ✅ |
| 10 | Perfil de usuario (direcciones, contraseña, historial) | ✅ |
| 11 | Lista de favoritos (Wishlist) con AJAX | ✅ |
| 12 | **Stripe Checkout con Laravel Cashier v16** | ✅ |
| 13 | Ecosistema de agentes IA (.agents/) | ✅ |
| 14 | Despliegue Docker (6 servicios) | ✅ |
| 15 | GitFlow y documentación del proyecto | ✅ |
| 16 | Webhook de Stripe (checkout.session.completed) | ✅ |
| 17 | Reserva de stock + validación en carrito | ✅ |
| 18 | Facturas PDF con dompdf | ✅ |
| 19 | Email de confirmación mejorado (Stripe + factura) | ✅ |
| 20 | Búsqueda, paginación y filtros del catálogo | ✅ |
| 21 | Estados de pedido expandidos + dashboard admin con Chart.js | ✅ |
| 22 | **Sistema de reembolsos con Stripe** | ✅ |
| 23 | **Suite de PHPUnit (46 tests, 107 assertions)** | ✅ |
| 24 | **CI/CD con GitHub Actions (lint + tests + build)** | ✅ |

---

## Fase 5: Seguridad del Pago y Webhooks (PRIORIDAD ALTA)

**Objetivo:** Garantizar que ningún pago quede en un estado inconsistente si el usuario cierra el navegador o pierde conexión.

### 5.1 Webhook de Stripe — `checkout.session.completed`
- Crear listener para el evento `checkout.session.completed` en `App\Listeners\StripeEventListener`
- Verificar la firma del webhook con `STRIPE_WEBHOOK_SECRET`
- Actualizar el estado del pedido a `completed` si el pago fue exitoso
- Decrementar stock de forma atómica dentro de una transacción
- Enviar email de confirmación si no se envió previamente
- Configurar `STRIPE_WEBHOOK_SECRET` en `.env`

### 5.2 Webhook de `payment_intent.payment_failed`
- Registrar el fallo en logs
- Notificar al usuario por email si el pago falla
- Mantener el pedido en estado `pending` para reintento

### 5.3 Exclusión CSRF para webhooks
- Ya configurada en `bootstrap/app.php` para `stripe/*`

**Archivos a crear/modificar:**
- `app/Listeners/StripeEventListener.php`
- `app/Providers/EventServiceProvider.php` (registrar listener)
- `.env` (añadir `STRIPE_WEBHOOK_SECRET`)
- `routes/web.php` (ruta `/stripe/webhook` ya existe por Cashier)

---

## Fase 6: Integridad del Stock (PRIORIDAD ALTA)

**Objetivo:** Prevenir la venta de unidades que ya no están disponibles.

### 6.1 Reserva de stock antes de Stripe Checkout
- En `CartController@stripeCheckout`, dentro de la transacción:
  - Verificar stock disponible para cada item antes de crear la Order
  - Bloquear filas con `lockForUpdate()` para prevenir concurrencia
  - Si stock insuficiente, cancelar y redirigir con error
  - El decremento real se confirma solo en `stripeSuccess()`

### 6.2 Estado de stock en tiempo real
- Mostrar "Quedan X unidades" en la ficha de producto
- Deshabilitar botón "Añadir al carrito" si stock = 0
- Validar stock al actualizar cantidad en el carrito

**Archivos a modificar:**
- `app/Http/Controllers/CartController.php` (stripeCheckout, update)
- `resources/views/catalog/show.blade.php`
- `app/Http/Controllers/ProductController.php`

---

## Fase 7: Facturas y Recibos PDF (PRIORIDAD MEDIA)

**Objetivo:** Generar facturas descargables tras cada compra completada.

### 7.1 Instalar dompdf
```bash
composer require dompdf/dompdf
```

### 7.2 Crear vista de factura
- Crear `resources/views/emails/invoice.blade.php` con diseño personalizado (paleta àndigo)
- Datos: número de pedido, fecha, productos, cantidades, precios, total, datos del cliente

### 7.3 Método de descarga
- Añadir ruta `GET /orders/{order}/invoice` en `web.php`
- Usar `$user->downloadInvoice()` de Cashier o implementar con dompdf
- Botón "Descargar factura" en la vista de detalle de pedido

### 7.4 Incluir en email de confirmación
- Adjuntar PDF al email `OrderConfirmed`
- O enlazar descarga directa en el email

**Archivos a crear/modificar:**
- `resources/views/emails/invoice.blade.php`
- `app/Http/Controllers/OrderController.php`
- `app/Mail/OrderConfirmed.php`
- `routes/web.php`

---

## Fase 8: Email Post-Pago Mejorado (PRIORIDAD MEDIA)

**Objetivo:** El email de confirmación debe reflejar el pago con Stripe.

### 8.1 Actualizar mailable OrderConfirmed
- Incluir ID de transacción Stripe (`stripe_session_id`)
- Mostrar método de pago (tarjeta terminación ****4242)
- Actualizar diseño con identidad de marca (Índigo)

### 8.2 Email de fallo de pago
- Crear mailable `PaymentFailed` para notificar al usuario
- Incluir enlace para reintentar el pago

**Archivos a modificar:**
- `app/Mail/OrderConfirmed.php`
- `resources/views/emails/` (plantilla)

---

## Fase 9: Búsqueda y Paginación (PRIORIDAD MEDIA)

**Objetivo:** Mejorar la navegación del catálogo con búsqueda y paginación.

### 9.1 Búsqueda de productos
- Añadir barra de búsqueda en el header o en la vista de catálogo
- Búsqueda por nombre y descripción (LIKE con Eloquent)
- Resultados destacando el término buscado

### 9.2 Paginación del catálogo
- Usar `paginate()` en `ProductController@index`
- Componente de paginación Bootstrap en la vista

### 9.3 Filtros avanzados
- Filtrar por rango de precio
- Filtrar por material y firmeza
- Ordenar por: precio, nombre, más recientes

**Archivos a modificar:**
- `app/Http/Controllers/ProductController.php`
- `resources/views/catalog/index.blade.php`
- `resources/views/layouts/app.blade.php` (barra de búsqueda)

---

## Fase 10: Estados de Pedido y Gestión Admin (PRIORIDAD MEDIA)

**Objetivo:** Ciclo de vida completo del pedido con estados reales.

### 10.1 Estados expandidos
- `pending` → Pago pendiente
- `completed` → Pago confirmado
- `processing` → En preparación
- `shipped` → Enviado
- `delivered` → Entregado
- `cancelled` → Cancelado

### 10.2 Transiciones en el admin
- Dropdown funcional para cambiar estado (ya parcialmente implementado)
- Validar transiciones permitidas (ej: no pasar de `cancelled` a `processing`)
- Registrar historial de cambios de estado

### 10.3 Dashboard mejorado
- Métricas de Stripe: ingresos totales, pedidos por estado
- Gráfico de ventas mensuales (Chart.js)
- Productos más vendidos vs más favoritos

**Archivos a modificar:**
- `app/Http/Controllers/AdminController.php`
- `resources/views/admin/dashboard.blade.php`
- `app/Models/Order.php`

---

## Fase 11: Reembolsos (PRIORIDAD BAJA)

**Objetivo:** Permitir al administrador reembolsar pedidos desde el panel.

### 11.1 Endpoint de reembolso
- Crear ruta `POST /admin/orders/{order}/refund`
- Usar la API de Stripe para procesar el reembolso: `$order->refund()`
- Actualizar estado del pedido a `refunded`

### 11.2 UI en el admin
- Botón "Reembolsar" en el detalle del pedido
- Confirmación antes de procesar
- Mostrar estado de reembolso en el historial

**Archivos a crear/modificar:**
- `app/Http/Controllers/AdminController.php`
- `resources/views/admin/orders/` (vista de detalle)

---

## Fase 12: Tests PHPUnit (PRIORIDAD MEDIA)

**Objetivo:** Cobertura de pruebas para las funcionalidades críticas.

### 12.1 Tests del flujo de pago
- Test de creación de sesión de Stripe Checkout
- Test del handler de éxito (stripeSuccess)
- Test del handler de cancelación (stripeCancel)
- Test de webhook de confirmación de pago

### 12.2 Tests del carrito
- Test de añadir/eliminar/actualizar items
- Test de sincronización sesión → BD al loguearse
- Test de validación de stock

### 12.3 Tests del admin
- Test de CRUD de productos
- Test de middleware de roles
- Test de cambio de estado de pedido

**Archivos a crear:**
- `tests/Feature/StripeCheckoutTest.php`
- `tests/Feature/CartTest.php`
- `tests/Feature/AdminTest.php`

---

## Fase 13: Despliegue y CI/CD (PRIORIDAD BAJA)

**Objetivo:** Automatizar el despliegue y asegurar la calidad del código.

### 13.1 GitHub Actions
- Pipeline de CI: lint (Pint), tests (PHPUnit), build (Vite)
- Trigger en push a `develop` y `main`

### 13.2 Despliegue en producción
- Configurar Stripe en modo live (no sandbox)
- Variables de entorno seguras (no en `.env` del repo)
- HTTPS obligatorio para webhooks

**Archivos a crear:**
- `.github/workflows/ci.yml`

---

## Resumen de Prioridades

| Fase | Descripción | Prioridad | Dependencias |
|------|------------|-----------|--------------|
| 5 | Webhooks de Stripe | 🔴 Alta | Fase 12 (Stripe ya instalado) |
| 6 | Integridad de stock | 🔴 Alta | Fase 5 |
| 7 | Facturas PDF | 🟡 Media | — |
| 8 | Email post-pago | 🟡 Media | Fase 5 |
| 9 | Búsqueda y paginación | 🟡 Media | — |
| 10 | Estados de pedido | 🟡 Media | Fase 5 |
| 11 | Reembolsos | 🟢 Baja | Fase 10 |
| 12 | Tests PHPUnit | 🟡 Media | Fases 5-6 |
| 13 | CI/CD | 🟢 Baja | Fase 12 |

---

## Checklist de Fases (Actualizar al finalizar cada sesión)

| Fase | Estado |
|------|--------|
| 1-4 | ✅ Completadas (previas al roadmap) |
| 5 — Webhooks de Stripe | ✅ Completada |
| 6 — Integridad de stock | ✅ Completada |
| 7 — Facturas PDF | ✅ Completada |
| 8 — Email post-pago | ✅ Completada |
| 9 — Búsqueda y paginación | ✅ Completada |
| 10 — Estados de pedido | ✅ Completada |
| 11 — Reembolsos | ✅ Completada |
| 12 — Tests PHPUnit | ✅ Completada |
| 13 — CI/CD | ✅ Completada |

---

## Desviaciones del Plan

Registro de cambios respecto al plan original. Se actualiza al final de cada sesión para mantener trazabilidad.

| Fecha | Fase afectada | Descripción de la desviación | Motivo |
|-------|--------------|------------------------------|--------|
| 28/08/2026 | — (Fases 1-4 previas) | Se implementó Stripe Checkout directamente en `main` en lugar de en una feature branch | No se siguió GitFlow desde el inicio. Corregido: se creó `feature/stripe-checkout` desde `develop` y se migraron los cambios |
| 28/08/2026 | Fase 12 (Stripe) | Se implementó antes de lo previsto en el roadmap original | El cliente solicitó integrar la pasarela de pagos como prioridad, adelantando las fases 5-6 del roadmap |
| 28/08/2026 | Fase 12 (Stripe) | Se usó `managed_payments[enabled]=false` en la sesión de Checkout | Stripe activa Managed Payments por defecto en cuentas nuevas, requiriendo `tax_code` por producto. Se desactivó para simplificar |
| 28/08/2026 | Fase 12 (Stripe) | Se eliminó `payment_method_types` de los parámetros de Checkout | Stripe API v2025-06-30.basil no permite este parámetro con Managed Payments habilitado |
| 28/08/2026 | Fases 5 y 6 | Se implementaron las fases 5 y 6 en sesiones separadas del roadmap original | Webhooks y reserva de stock son críticos para integridad de pagos |
| 28/08/2026 | Fase 5 (Webhook) | Se creó `StripeWebhookController` en lugar de `StripeEventListener` del roadmap | Enfoque más limpio: extender Cashier WebhookController evita registrar rutas manuales y reutiliza verificación de firma |
| 28/08/2026 | Fase 7 (Facturas PDF) | Se usó dompdf v3.1.6 en lugar de `downloadInvoice()` de Cashier | Cashier's `downloadInvoice()` genera facturas básicas; dompdf permite diseño personalizado con paleta índigo |
| 28/08/2026 | Fase 7+8 | Se implementaron juntas en una sola feature branch (`feature/pdf-invoices-and-emails`) | Son complementarias: la factura PDF se vincula al email de confirmación |
| 28/08/2026 | Fase 10 (Dashboard) | Se usó Chart.js CDN en lugar de librería local | Para un TFG no es necesario bundlar Chart.js; CDN simplifica el setup |
| 28/08/2026 | Fase 10 (Estados) | Se añadió `completed` al mapa de estados (no estaba en el roadmap) | El webhook de Stripe necesita un estado terminal distinto de `delivered` para pedidos pagados online |
| 28/08/2026 | Fase 11 (Reembolsos) | Se creó tabla `refunds` y columna `payment_intent_id` en orders | Necesario para rastrear reembolsos y conectar con Stripe PaymentIntents |
| 28/08/2026 | Fase 11 (Reembolsos) | Se añadió webhook `charge.refunded` como safety net | Cubre reembolsos iniciados desde el dashboard de Stripe directamente |
| 28/08/2026 | Fase 12 (Tests) | Se crearon factories faltantes (Order, OrderItem, CartItem) | Los modelos originales no tenían `HasFactory`; se añadió el trait |
| 28/08/2026 | Fase 12 (Tests) | Se omitieron tests de llamada real a Stripe API | La API de Stripe no es mockable con `Cashier::stripe()` estático; se testean validaciones y estados de modelo |

---

## Notas para la Siguiente Sesión

1. **Todas las fases del roadmap completadas (1-13)**
2. **Siguiente paso:** Push a origin para activar el pipeline CI en GitHub

### Pendiente para producción — Webhook de Stripe

El webhook `checkout.session.completed` está implementado y funcional, pero usa un secreto placeholder (`whsec_test_placeholder`). **Antes de desplegar en producción** se debe:

1. **Registrar el endpoint en Stripe Dashboard:**
   - Ir a Developers → Webhooks → Add endpoint
   - URL: `https://tudominio.com/stripe/webhook`
   - Eventos: `checkout.session.completed`, `payment_intent.payment_failed`, `charge.refunded`
   - Copiar el Signing Secret generado

2. **Alternativa local con Stripe CLI** (para desarrollo):
   ```bash
   stripe login
   stripe listen --forward-to localhost:8080/stripe/webhook
   ```
   El CLI muestra un `whsec_...` específico de la sesión → copiarlo a `STRIPE_WEBHOOK_SECRET` en `.env`

3. **Actualizar `.env`** con el secreto real:
   ```
   STRIPE_WEBHOOK_SECRET=whsec_1ABC...tu_secreto_real
   ```

4. **Verificar** que `config/cashier.php` carga el secreto desde la env var (ya configurado).

> **Nota:** Sin el secreto real, la verificación de firma se salta silenciosamente (Cashier solo aplica `VerifyWebhookSignature` cuando `cashier.webhook.secret` tiene un valor distinto de placeholder). En producción, un webhook sin firma válida no debería procesarse.

---

## Protocolo de Cierre de Sesión

Al finalizar cada sesión de desarrollo, verificar:

1. **Engram:** ¿Se guardaron todas las observaciones relevantes? (`engram stats` para verificar)
2. **Roadmap:** ¿Se actualizó el checklist de fases con los estados completados?
3. **Desviaciones:** ¿Se registró alguna desviación del plan original en la tabla de desviaciones?
4. **Git:** ¿Están commiteados los cambios en la feature branch correcta?
5. **Prompt:** ¿Se generó el prompt para la siguiente sesión?

---

*Documento generado automáticamente — Reposa+ TFG 2026*
