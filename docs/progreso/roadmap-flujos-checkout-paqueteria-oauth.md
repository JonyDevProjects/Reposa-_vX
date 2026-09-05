# Roadmap: Flujos de Checkout Adaptativo, Paquetería Estándar y Registro Social (Google OAuth 2.0) — Reposa+

## Contexto y Justificación

Este documento establece la planificación integral, trazabilidad técnica y protocolo de validación para las evoluciones funcionales críticas identificadas en el ciclo transaccional de **Reposa+**:
1. **Compra sin fricciones (*Guest Checkout*):** Permitir compras ágiles sin registro forzoso previo, garantizando la seguridad mediante tokens criptográficos para consultas y descarga de facturas, e incorporando un mecanismo de conversión a usuario registrado en un solo clic tras el pago.
2. **Dirección física obligatoria en el registro:** Solicitar y validar estrictamente los datos de entrega (`street`, `city`, `zip_code`, `phone`) en el formulario de registro estándar para asegurar la logística desde el primer contacto.
3. **Servicio Mock de Paquetería Estándar y Trazabilidad:** Simular la operativa técnica y visual de un operador de transporte nacional de primer nivel (*Correos Express*), incluyendo cálculo de tarifas, códigos de seguimiento realistas (`RPX2026...ES`), ciclo de vida de envíos, línea de tiempo interactiva para el cliente y panel de control con impresión de etiquetas térmicas A6 para el administrador.
4. **Registro y Login Social con Google OAuth 2.0:** Planificar la arquitectura e integración de Google Identity mediante Laravel Socialite, resolviendo el reto de capturar la dirección obligatoria de envío mediante un flujo de onboarding en dos pasos.
5. **Protocolo de Pruebas Manuales:** Definir el plan de validación funcional exhaustivo paso a paso para la verificación humana en entorno local/Docker.

**Fecha de creación:** 05 de septiembre de 2026  
**Rama activa de trabajo:** `feature/guest-checkout-and-shipping`  
**Documentos de referencia y análisis previo:**
- [`docs/progreso/investigacion_flujos_checkout_envios.md`](./investigacion_flujos_checkout_envios.md) (Estudio de flujos, mercado logístico y costes operativos).
- [`docs/artefactos/analisis-google-oauth-registro.md`](../artefactos/analisis-google-oauth-registro.md) (Diseño técnico y arquitectura de Google OAuth 2.0 con Socialite).
- [`docs/progreso/roadmap-diseno-ecommerce.md`](./roadmap-diseno-ecommerce.md) (Línea de diseño editorial Midnight Sanctuary y componentes Blade).

---

## Matriz Global de Fases y Estado de Ejecución

| Fase | Denominación / Enfoque | Entregables Clave | Estado |
|:---:|---|---|:---:|
| **1** | Captura de Dirección de Envío en el Registro | Validación estricta en Fortify, transacción atómica `User`+`Address`+`Profile`, rediseño de `register.blade.php`. | ✅ Completada |
| **2** | Servicio Mock de Paquetería Estándar y Trazabilidad | Contrato `ShippingServiceInterface`, servicio `MockStandardCourierService`, tracking timeline en `orders/show`, gestión de envíos y etiqueta A6 en admin. | ✅ Completada |
| **3** | Compra como Invitado (*Guest Checkout*) y Conversión 1-Clic | Vista unificada `/checkout`, migración de `orders` (`guest_token`, snapshots de envío), seguridad por token en facturas, claim account en confirmación. | ✅ Completada |
| **4** | Autenticación y Registro con Google OAuth 2.0 | Integración de `laravel/socialite`, migración `google_id`, flujo de onboarding en 2 pasos para dirección obligatoria, botones visuales Midnight Sanctuary. | ✅ Completada |
| **5** | Protocolo de Pruebas y Certificación E2E (Playwright) | Suite automatizada en Playwright para Casos 1 al 6 (E2E browser testing, guest checkout, tracking, claim account, albarán térmico y OAuth initiation). | ✅ Automatizada y Certificada |

---

## Fase 1: Captura Obligatoria de Dirección en el Registro

**Objetivo:** Garantizar que todo usuario que decida registrarse en la plataforma cuente desde el primer instante con un domicilio postal completo y verificado para la entrega de mercancía y facturación reglamentaria.

### 1.1 Backend y Validación Transaccional (`CreateNewUser.php`)
- Reglas de validación estrictas: `street` (max 255), `city` (max 255), `zip_code` (max 20, formato postal), `phone` (max 30, formato telefónico) y `province` (opcional, max 100).
- Ejecución dentro de una transacción de base de datos (`DB::transaction`):
  - Creación del modelo `User` con hash seguro de contraseña.
  - Creación automática de la dirección asociada en la tabla `addresses` con `is_main = true`.
  - Creación o actualización del perfil del cliente en `profiles` con el teléfono de contacto.

### 1.2 Rediseño Ergonómico de la Interfaz (`register.blade.php`)
- Estructuración en dos bloques visuales claramente diferenciados con microcopy de descanso:
  - **Bloque 1: Datos de Acceso:** Nombre completo, correo electrónico, contraseña y confirmación.
  - **Bloque 2: Dirección de Entrega y Teléfono:** Calle y número, ciudad, código postal, provincia y teléfono móvil para notificaciones de transporte.
- Mensajes de error contextuales y persistencia de valores antiguos mediante `old()`.
- Soporte 100% bilingüe en [`lang/es/messages.php`](../../Reposa+/lang/es/messages.php) y [`lang/en/messages.php`](../../Reposa+/lang/en/messages.php).

### 1.3 Verificación Automatizada
- Suite específica [`tests/Feature/RegistrationTest.php`](../../Reposa+/tests/Feature/RegistrationTest.php) que verifica:
  - Renderizado correcto del formulario de registro.
  - Rechazo y errores de validación si faltan campos de envío o teléfono.
  - Registro exitoso con persistencia relacional (`users`, `addresses` principal y `profiles`).

**Archivos involucrados:**
- `Reposa+/app/Actions/Fortify/CreateNewUser.php`
- `Reposa+/resources/views/auth/register.blade.php`
- `Reposa+/lang/es/messages.php` y `Reposa+/lang/en/messages.php`
- `Reposa+/tests/Feature/RegistrationTest.php`

**Criterio de Aceptación:** 100% cumplido (3 tests pasando, 13 aserciones).

---

## Fase 2: Servicio Mock de Paquetería Estándar y Trazabilidad Logística

**Objetivo:** Desarrollar un subsistema logístico realista que emule la operativa de integración telemática con transportistas reales de la península (Correos Express / SEUR), proporcionando trazabilidad visual al cliente y herramientas de despacho al administrador.

### 2.1 Esquema de Base de Datos y Modelo de Expedición (`Shipment`)
- Migración [`2026_09_05_100000_create_shipments_table.php`](../../Reposa+/database/migrations/2026_09_05_100000_create_shipments_table.php):
  - Clave foránea `order_id` con borrado en cascada.
  - `tracking_number` único indexado con prefijo nacional.
  - Campos de transportista, tipo de servicio, nombre del servicio y coste logístico.
  - Estados logísticos: `pre_registered`, `in_transit`, `at_hub`, `out_for_delivery`, `delivered`, `incident`.
  - Snapshots de destinatario y dirección física de entrega.
  - Fechas operativas: `estimated_delivery_date`, `shipped_at`, `delivered_at`.
  - Campos JSON: `tracking_history` (array cronológico de eventos) y `label_data` (payload de etiqueta técnica).
- Modelo [`Shipment.php`](../../Reposa+/app/Models/Shipment.php) con relación `belongsTo(Order::class)`, constantes de estado y *accessors* (`status_label`, `status_color`).

### 2.2 Servicio de Paquetería (`MockStandardCourierService`)
- Contrato [`ShippingServiceInterface`](../../Reposa+/app/Services/Shipping/ShippingServiceInterface.php) con los métodos:
  - `calculateRates(float $cartTotal, ?string $postalCode, float $weightKg): array`
  - `createShipment(Order $order, array $recipientData, string $serviceType): Shipment`
  - `getTracking(string $trackingNumber): ?array`
  - `advanceTrackingStatus(Shipment $shipment, ?string $targetStatus): Shipment`
  - `generateLabel(Shipment $shipment): array`
- Implementación en [`MockStandardCourierService.php`](../../Reposa+/app/Services/Shipping/MockStandardCourierService.php):
  - Umbral de envío gratuito a partir de 50.00€ (coste base estándar 4.95€).
  - Opciones de envío alternativo: *Express 24h* (7.95€) y *Punto de Recogida CityPaq 48h* (3.50€).
  - Generador de números de seguimiento realistas: `RPX` + Año + 6 dígitos aleatorios + `ES` (ej: `RPX2026849201ES`).
  - Cálculo de entrega en días hábiles (excluyendo fines de semana).
  - Ciclo de vida logístico con actualización de eventos detallados (ubicaciones, descripciones) y sincronización con el estado de la orden (`shipped` y `delivered`).
  - Generador de albarán técnico para etiqueta térmica A6 (código de ruteo postal, código de barras simulado 128, pesos y dimensiones).
- Registro en el contenedor de servicios en [`AppServiceProvider.php`](../../Reposa+/app/Providers/AppServiceProvider.php).

### 2.3 Experiencia de Usuario: Trazabilidad en Tiempo Real (`orders/show.blade.php`)
- Integración de la tarjeta **Seguimiento del Paquete en Tiempo Real** (`shipment-tracking-heading`):
  - Badge del operador logístico (*Correos Express*).
  - Código de seguimiento en tipografía monoespaciada con estilo visual de código de barras.
  - Badge cromático del estado actual.
  - Fecha estimada de entrega en domicilio.
  - Línea de tiempo vertical de eventos con nodos visuales (punto activo, check en eventos completados), ubicación física de la plataforma y marca de tiempo legible.

### 2.4 Panel de Administración y Albarán Térmico A6 (`admin/orders/index.blade.php`)
- Nueva columna **"Seguimiento"** en la tabla de pedidos globales del panel administrativo:
  - Visualización del número de tracking y badge de estado.
  - Botón de acción rápida **"Avanzar"** (`admin.shipments.advance`): permite a los administradores simular el avance del paquete por los diferentes estados de la cadena logística (`pre_registered` &rarr; `in_transit` &rarr; `at_hub` &rarr; `out_for_delivery` &rarr; `delivered`).
  - Botón **"Etiqueta"** (`admin.shipments.label`): abre la vista de expedición.
- Vista técnica de impresión térmica [`admin/shipments/label.blade.php`](../../Reposa+/resources/views/admin/shipments/label.blade.php):
  - Formato oficial A6 (100mm &times; 150mm) optimizado para impresoras térmicas Zebra / Dymo.
  - Reglas CSS `@media print` con ocultación de elementos de navegación y márgenes cero.
  - Código de ruteo (`MAD-28014-Z01`), código de barras Code-128 simulado, bloques de remitente/destinatario y especificaciones técnicas del bulto textil.

**Archivos involucrados:**
- `Reposa+/database/migrations/2026_09_05_100000_create_shipments_table.php`
- `Reposa+/app/Models/Shipment.php`
- `Reposa+/app/Services/Shipping/ShippingServiceInterface.php`
- `Reposa+/app/Services/Shipping/MockStandardCourierService.php`
- `Reposa+/app/Providers/AppServiceProvider.php`
- `Reposa+/resources/views/orders/show.blade.php`
- `Reposa+/resources/views/admin/orders/index.blade.php`
- `Reposa+/resources/views/admin/shipments/label.blade.php`
- `Reposa+/tests/Feature/ShippingServiceTest.php`

**Criterio de Aceptación:** 100% cumplido (6 tests pasando, 41 aserciones).

---

## Fase 3: Compra como Invitado (*Guest Checkout*) y Conversión en 1 Clic

**Objetivo:** Eliminar la principal barrera de conversión del e-commerce permitiendo finalizar pedidos sin crear cuenta previamente, preservando la seguridad de los datos y ofreciendo una conversión sin fricción en la confirmación.

### 3.1 Adaptación del Esquema de Pedidos (`orders`)
- Migración [`2026_09_05_110000_add_guest_and_shipping_fields_to_orders_table.php`](../../Reposa+/database/migrations/2026_09_05_110000_add_guest_and_shipping_fields_to_orders_table.php):
  - Modificación de `user_id` a tipo `nullable()`.
  - Inclusión de `guest_token` (string indexado de 40 caracteres criptográficos para seguridad de acceso público).
  - Inclusión de snapshots inmutables: `shipping_name`, `shipping_email`, `shipping_phone`, `shipping_street`, `shipping_city`, `shipping_zip_code`, `shipping_province`, `shipping_country`, `shipping_service_type`, `shipping_cost`.
- Modelo [`Order.php`](../../Reposa+/app/Models/Order.php):
  - Métodos y propiedades auxiliares: `isGuest()`, `customer_name`, `customer_email`.
  - Relación `hasOne(Shipment::class)`.

### 3.2 Vista Unificada y Adaptativa de Checkout (`checkout/index.blade.php`)
- Ruta pública `GET /checkout` gestionada por `CartController@checkoutPage`:
  - Si el carrito está vacío, redirige a `/cart` con mensaje informativo.
  - Para usuarios autenticados: autocompleta los datos y ofrece selector de direcciones guardadas.
  - Para invitados: renderiza el formulario de datos de entrega y contacto con enlace sugerido a inicio de sesión (*"¿Ya tienes una cuenta Reposa+?"*).
  - Selector de método de envío dinámico con desglose de importes y tiempos estimados.
  - Resumen lateral de compra con desglose de IVA (21%), gastos de envío y garantías de descanso.
  - Opciones de pago: Confirmar pedido directo o tramitar vía pasarela segura Stripe.

### 3.3 Lógica del Controlador y Seguridad Transaccional (`CartController.php`)
- `checkout()` y `stripeCheckout()`:
  - Soporte integral tanto para `Auth::user()` como para carritos en sesión de invitados (`session('cart')`).
  - Validación de stock con bloqueo de concurrencia (`lockForUpdate()`).
  - Generación de `guest_token` criptográfico (`Str::random(40)`).
  - Alta automática de la expedición en `MockStandardCourierService`.
  - Almacenamiento seguro del token en la sesión del navegador (`guest_order_token`).
- `showOrder()` y `downloadInvoice()`:
  - **Mecanismo de seguridad de acceso:**
    - Si el pedido pertenece a un usuario registrado (`user_id !== null`), se requiere sesión activa y coincidencia de ID (`abort_if(!auth()->check() || $order->user_id !== auth()->id(), 403)`).
    - Si el pedido es de invitado (`user_id === null`), se exige coincidencia estricta del `guest_token` suministrado por parámetro de consulta (`?token=...`) o registrado en la sesión activa (`session('guest_order_token')`). De lo contrario, se aborta con **HTTP 403 Forbidden**.
  - Generación de facturas PDF seguras mediante `dompdf` consumiendo `$order->customer_name`, `$order->customer_email` y dirección de envío snapshot sin dependencias forzadas de modelos de usuario.

### 3.4 Conversión de Invitado a Usuario Registrado en 1 Clic (`claimAccount`)
- En [`orders/show.blade.php`](../../Reposa+/resources/views/orders/show.blade.php), si el pedido es de invitado y el usuario no ha iniciado sesión, se despliega la tarjeta destacada **"Guarda tu cuenta en 1 clic"**.
- El usuario solo debe ingresar y confirmar su nueva contraseña.
- `CartController@claimAccount`:
  - Valida el token del pedido y que el correo no esté registrado previamente.
  - Ejecuta una transacción que:
    1. Crea el `User` con el nombre y correo del pedido.
    2. Actualiza el pedido vinculando el nuevo `user_id`.
    3. Registra en `addresses` la dirección del pedido como dirección principal (`is_main = true`).
    4. Crea el `Profile` con el teléfono del pedido.
    5. Inicia sesión automáticamente con `Auth::login($user)` y redirige a `/profile`.

**Archivos involucrados:**
- `Reposa+/database/migrations/2026_09_05_110000_add_guest_and_shipping_fields_to_orders_table.php`
- `Reposa+/app/Models/Order.php`
- `Reposa+/app/Http/Controllers/CartController.php`
- `Reposa+/routes/web.php`
- `Reposa+/resources/views/checkout/index.blade.php`
- `Reposa+/resources/views/cart/index.blade.php`
- `Reposa+/resources/views/orders/show.blade.php`
- `Reposa+/resources/views/invoices/invoice.blade.php`
- `Reposa+/tests/Feature/GuestCheckoutTest.php`

**Criterio de Aceptación:** 100% cumplido (8 tests pasando, 50 aserciones).

---

## Fase 4: Autenticación y Registro con Google OAuth 2.0 (COMPLETADA)

**Objetivo:** Incorporar el registro y login en un solo clic mediante **Google Identity** (*Social Sign-On*), eliminando la fricción de inventar y recordar contraseñas, e implementando una solución elegante para la captura de la dirección de entrega que Google no provee.

> [!NOTE]
> Esta fase se encuentra diseñada en detalle en el documento técnico de arquitectura:  
> [`docs/artefactos/analisis-google-oauth-registro.md`](../artefactos/analisis-google-oauth-registro.md).

### 4.1 Dependencias y Configuración de Proveedor
- [x] Instalar el paquete oficial: `composer require laravel/socialite`.
- [x] Configurar el proveedor en `config/services.php`:
  ```php
  'google' => [
      'client_id' => env('GOOGLE_CLIENT_ID'),
      'client_secret' => env('GOOGLE_CLIENT_SECRET'),
      'redirect' => env('GOOGLE_REDIRECT_URI', '/auth/google/callback'),
  ],
  ```
- [x] Variables de entorno en `.env.example` y `.env`: `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`.

### 4.2 Migración de Esquema de Usuarios (`users`)
- [x] Crear migración `add_google_fields_to_users_table`:
  - `google_id` (string, nullable, unique, indexado).
  - `avatar` (string, nullable).
  - Hacer `password` nullable para usuarios OAuth.

### 4.3 Controlador de Autenticación Social (`GoogleAuthController`)
- [x] Crear controlador `app/Http/Controllers/Auth/GoogleAuthController.php`:
  - `redirect()`: Redirige a Google con los scopes estándar `openid`, `profile`, `email`.
  - `callback()`:
    - Recupera el usuario con `Socialite::driver('google')->user()`.
    - **Escenario A (Usuario con `google_id`):** Inicia sesión directamente (`Auth::login($user, true)`) y redirige al destino previsto (`intended` o `/catalog`).
    - **Escenario B (Usuario con email existente sin `google_id`):** Vincula el `google_id` y avatar a la cuenta existente, inicia sesión y notifica al usuario.
    - **Escenario C (Usuario nuevo):**
      - Crea el `User` (`name`, `email`, `google_id`, `avatar`, `email_verified_at = now()`).
      - Inicia sesión.
      - Al no disponer de dirección de entrega obligatoria, redirige inmediatamente a la pantalla de **Onboarding de Dirección de Envío** (`/onboarding/shipping-address`).

### 4.4 Flujo de Onboarding de Dirección de Envío
- [x] Vista `resources/views/auth/onboarding-shipping.blade.php`:
  - Mensaje cálido personalizado con el nombre y avatar del usuario de Google.
  - Formulario con campos obligatorios: `street`, `city`, `zip_code`, `province`, `phone`.
- [x] Middleware `EnsureHasShippingAddress` para redirigir al onboarding si un usuario recién autenticado vía Google intenta navegar sin haber configurado su dirección inicial.
- [x] Controlador `OnboardingController@storeShipping`:
  - Guarda la dirección con `is_main = true`.
  - Guarda el teléfono en `profiles`.
  - Redirige al checkout pendiente o al catálogo.

### 4.5 Componentes Visuales y Vistas
- [x] Botón *"Continuar con Google"* diseñado según las guías de identidad de Google y adaptado a la estética *Midnight Sanctuary*:
  - Integración en `resources/views/auth/login.blade.php`.
  - Integración en `resources/views/auth/register.blade.php`.
  - Integración en el banner de invitados de `resources/views/checkout/index.blade.php`.
- [x] Traducciones correspondientes al 100% en `lang/es/messages.php` y `lang/en/messages.php`.

### 4.6 Pruebas Automatizadas
- [x] Suite de pruebas [`tests/Feature/GoogleOAuthTest.php`](../../Reposa+/tests/Feature/GoogleOAuthTest.php):
  - Mock del driver de Socialite (`Socialite::shouldReceive('driver->user')...`).
  - Prueba de redirección a Google.
  - Prueba de login de usuario existente con `google_id` (Escenario A).
  - Prueba de vinculación de cuenta existente por email (Escenario B).
  - Prueba de registro de nuevo usuario y redirección obligatoria a onboarding (Escenario C).
  - Prueba del middleware `EnsureHasShippingAddress`.
  - Prueba del guardado de dirección en onboarding.
  - Prueba de validación de campos obligatorios en onboarding.
  - Prueba de captura elegante de excepciones en callback OAuth.

---

## Fase 5: Protocolo de Pruebas Manuales y Verificación E2E de Usuario

**Objetivo:** Brindar al desarrollador/usuario una guía de prueba manual exhaustiva, con pasos específicos, datos de prueba sugeridos y resultados esperados para validar cada una de las funcionalidades en el navegador sobre el entorno Docker.

### 5.1 Entorno y Pre-requisitos de Prueba
Asegurarse de que el entorno Docker esté levantado y con migraciones al día:
```bash
# Verificar contenedores
docker compose ps

# Comprobar base de datos y migraciones
docker exec reposaplus-dev-app php artisan migrate:status

# Ejecutar suite automatizada completa antes de las pruebas manuales
docker exec reposaplus-dev-app php artisan test --testsuite=Feature,Unit
```
URL base del e-commerce: `http://localhost:8000` (o el puerto mapeado en Docker).

---

### 5.2 Guía de Casos de Prueba Manuales

#### Caso de Prueba 1: Registro de Usuario con Dirección Obligatoria
- **Objetivo:** Verificar que el formulario de registro exige y guarda la dirección física y el teléfono.
- **Pasos a ejecutar:**
  1. Abrir navegador en modo incógnito e ir a `http://localhost:8000/register`.
  2. Verificar visualmente la división en dos bloques: *"1. Datos de Acceso"* y *"2. Dirección de Entrega y Contacto"*.
  3. Intentar enviar el formulario vacío &rarr; Verificar que el navegador o el backend marcan errores en todos los campos requeridos (`street`, `city`, `zip_code`, `phone`).
  4. Rellenar los datos con un usuario de prueba:
     - Nombre: `Carlos Pruebas`
     - Email: `carlos.pruebas@reposatest.es`
     - Contraseña: `Password123!`
     - Calle: `Calle Mayor 45, 3º A`
     - Ciudad: `Madrid`
     - Código Postal: `28013`
     - Provincia: `Madrid`
     - Teléfono: `+34 600 123 456`
     - Aceptar términos.
  5. Clic en **"Crear Cuenta y Comenzar a Descansar"**.
- **Resultado Esperado:**
  - Redirección con éxito al catálogo o perfil.
  - Al ingresar a `http://localhost:8000/profile`, en la pestaña *"Mis Direcciones"*, aparece `Calle Mayor 45, 3º A, 28013 Madrid` marcada como **Principal**.
  - En *"Datos Personales"*, el teléfono `+34 600 123 456` está guardado correctamente.

---

#### Caso de Prueba 2: Flujo Completo de Compra como Invitado (*Guest Checkout*)
- **Objetivo:** Probar la compra directa sin iniciar sesión a través de la pantalla unificada `/checkout`.
- **Pasos a ejecutar:**
  1. En una ventana de incógnito sin sesión, ir a `http://localhost:8000/catalog`.
  2. Añadir una almohada al carrito (ej. *Almohada Cervical Viscoelástica Plus*).
  3. Ir al carrito (`http://localhost:8000/cart`) y pulsar en **"Tramitar Pedido"** (`/checkout`).
  4. Verificar que se carga la vista adaptativa con el aviso *"¿Ya tienes una cuenta Reposa+?"*.
  5. Rellenar los campos de envío del invitado:
     - Nombre: `Laura Invitada`
     - Email: `laura.invitada@reposatest.es`
     - Teléfono: `+34 611 998 877`
     - Dirección: `Avenida Diagonal 120, 1º`
     - Ciudad: `Barcelona`
     - Código Postal: `08018`
     - Provincia: `Barcelona`
  6. Seleccionar método de envío (ej. *Reposa+ Estándar 48h* o *Express 24h*). Verificar que el total se actualiza.
  7. Seleccionar **"Confirmar Pedido Directo"** y pulsar en **"Confirmar y Pagar"**.
- **Resultado Esperado:**
  - Redirección inmediata a `http://localhost:8000/orders/{id}?token={guest_token}`.
  - La pantalla muestra el Hero de confirmación serena con el número de pedido `#00000X`.
  - El stock del producto en el catálogo se ha decrementado en la cantidad comprada.
  - El carrito de la sesión queda vacío.

---

#### Caso de Prueba 3: Seguridad de Pedidos y Facturas de Invitados
- **Objetivo:** Garantizar que ningún tercero puede espiar pedidos de invitados sin el token secreto.
- **Pasos a ejecutar:**
  1. Tomar la URL del pedido creado en el Caso 2: `http://localhost:8000/orders/X?token=ABCDEF...`
  2. Abrir una **tercera ventana de incógnito** distinta (sin cookies ni sesión).
  3. Intentar acceder a la URL **sin el token**: `http://localhost:8000/orders/X`.
  4. Intentar acceder con un token falso: `http://localhost:8000/orders/X?token=token_invalido_123`.
  5. Intentar descargar la factura sin token: `http://localhost:8000/orders/X/invoice`.
  6. Intentar descargar la factura con el token correcto: `http://localhost:8000/orders/X/invoice?token=ABCDEF...`.
- **Resultado Esperado:**
  - Pasos 3, 4 y 5 retornan un error estricto **HTTP 403 Forbidden**.
  - Paso 6 descarga el archivo oficial PDF de la factura (`Factura_Reposa+_00000X.pdf`), formateada correctamente con los datos de `Laura Invitada` y su dirección en Barcelona.

---

#### Caso de Prueba 4: Conversión de Invitado a Usuario Registrado en 1 Clic
- **Objetivo:** Comprobar que el cliente invitado puede guardar su cuenta desde la pantalla de confirmación sin reintroducir datos.
- **Pasos a ejecutar:**
  1. Estando en la pantalla de confirmación del pedido del Caso 2 (`http://localhost:8000/orders/X?token=ABCDEF...`).
  2. Localizar la tarjeta: **"Guarda tu cuenta en 1 clic"** (con el badge *Acceso Rápido* y el email `laura.invitada@reposatest.es`).
  3. Introducir una contraseña (ej: `MiSecreto2026!`) y su confirmación.
  4. Pulsar en **"Crear mi cuenta y vincular pedido"**.
- **Resultado Esperado:**
  - Redirección automática a `http://localhost:8000/profile` con sesión iniciada como `Laura Invitada`.
  - En la pestaña *"Mis Pedidos"*, el pedido `#00000X` aparece registrado en su historial.
  - En *"Mis Direcciones"*, la dirección de Barcelona aparece guardada como principal.
  - Si se vuelve a visitar `http://localhost:8000/orders/X`, la tarjeta de reclamo en 1 clic ya no aparece.

---

#### Caso de Prueba 5: Operativa Logística y Etiquetas en Panel de Administración
- **Objetivo:** Verificar la línea de tiempo de paquetería, el avance de estados y la generación de etiquetas térmicas.
- **Pasos a ejecutar:**
  1. Iniciar sesión como administrador en `http://localhost:8000/login` con credenciales de admin (o crear un admin con `role = 'admin'`).
  2. Acceder al panel de administración de pedidos: `http://localhost:8000/admin/orders`.
  3. Comprobar la nueva columna **"Seguimiento"**:
     - Muestra el transportista (*Correos Express*), el código de tracking (`RPX...ES`) y el estado inicial (*Etiqueta creada / Pre-admitido*).
     - Si el pedido es de invitado, el nombre muestra el badge `Invitado`.
  4. Pulsar sobre el botón **"Avanzar"** junto al pedido:
     - El estado del envío cambia a *En tránsito* (badge azul).
     - El pedido cambia automáticamente a estado *shipped*.
  5. Abrir en otra pestaña la vista de cliente del pedido (`/orders/X`):
     - El componente **"Seguimiento del Paquete en Tiempo Real"** muestra el evento de recogida en *Coslada Hub Central* y el estado actualizado.
  6. En el panel admin, pulsar el botón **"Etiqueta"**:
     - Se abre la ventana con la etiqueta térmica A6 ([`admin/shipments/label.blade.php`](../../Reposa+/resources/views/admin/shipments/label.blade.php)).
     - Verificar código de ruteo, código de barras simulado y datos del destinatario.
     - Pulsar el botón **"Imprimir Etiqueta"** y verificar en la vista previa del navegador que se formatea a 100mm &times; 150mm sin botones ni cabeceras.

---

#### Caso de Prueba 6: Registro e Inicio de Sesión con Google OAuth 2.0 y Onboarding de Dirección de Envío
- **Objetivo:** Validar la autenticación delegada con Google (*Social Sign-On*), el intercambio seguro de tokens, la protección por middleware de nuevos usuarios y la captura obligatoria de dirección postal y teléfono en el paso 2 de onboarding.

##### Escenario 6.1: Registro de Nuevo Usuario vía Google y Onboarding Obligatorio
- **Pre-requisitos:** Abrir una ventana de navegador en modo incógnito (`http://localhost:8000`), sin sesión previa en la plataforma Reposa+.
- **Pasos a ejecutar:**
  1. Acceder a `http://localhost:8000/register` o `http://localhost:8000/login`.
  2. Comprobar que en la parte superior del formulario se muestra el botón oficial **"Continuar con Google"** con el imagotipo oficial SVG de 4 colores de Google y el divisor estético *"o bien con correo electrónico"*.
  3. Pulsar en el botón **"Continuar con Google"**.
  4. En la pantalla de autenticación y consentimiento de Google (`accounts.google.com`), seleccionar o iniciar sesión con la cuenta de Google deseada y autorizar el acceso al perfil público y correo.
  5. Tras la autorización, verificar que Google redirige a `http://localhost:8000/auth/google/callback` y la aplicación procesa la respuesta sin errores de sesión ni `redirect_uri_mismatch`.
  6. Al tratarse de un usuario recién registrado (sin dirección de entrega física registrada en la base de datos), comprobar que el sistema lo redirige de inmediato a la pantalla de onboarding:
     `http://localhost:8000/onboarding/shipping-address`
  7. En la pantalla de onboarding, verificar los siguientes elementos visuales del diseño *Midnight Sanctuary*:
     - Cabecera en azul noche con icono de ubicación y título *"Dirección de Entrega"*.
     - Insignia de usuario verificado con la foto de avatar de Google, nombre y correo electrónico.
     - Indicador de progreso: *"Paso 2 / 2"*.
     - Alerta informativa: *"¡Bienvenido a Reposa+! Para gestionar el envío de tus almohadas, completa tu dirección."*
  8. **Verificación de Seguridad del Middleware (`EnsureHasShippingAddress`):**
     - Sin rellenar el formulario, intentar navegar manualmente a cualquier otra ruta de la aplicación (por ejemplo `http://localhost:8000/catalog` o `http://localhost:8000/profile`).
     - Comprobar que el middleware intercepta la navegación y redirige de vuelta forzosamente a `/onboarding/shipping-address` mostrando la advertencia: *"Para garantizar la correcta entrega de tus pedidos, completa tu dirección de envío."*
  9. **Validación de Campos Obligatorios:**
     - Pulsar el botón **"Completar registro y empezar a descansar"** dejando los campos vacíos.
     - Comprobar que el sistema resalta los errores de validación en calle, ciudad, código postal y teléfono de contacto.
  10. **Completar Datos de Envío Reales/Prueba:**
      - Dirección: `Calle Princesa 18, 2º B`
      - Ciudad: `Madrid`
      - Código Postal: `28008`
      - Provincia: `Madrid`
      - Teléfono: `+34 655 443 322`
  11. Pulsar en **"Completar registro y empezar a descansar"**.
- **Resultado Esperado:**
  - Redirección con éxito al catálogo (`http://localhost:8000/catalog`) con el mensaje flash: *"¡Dirección configurada con éxito! Tu cuenta está lista para disfrutar del descanso."*
  - La navegación por todo el sitio queda completamente desbloqueada.
  - En la esquina superior derecha, la barra de navegación muestra el avatar y nombre del usuario de Google.
  - Al ingresar a `http://localhost:8000/profile`, la dirección `Calle Princesa 18, 2º B` figura guardada como principal y el teléfono en el perfil.

---

##### Escenario 6.2: Inicio de Sesión Recurrente con Cuenta de Google ya Registrada
- **Pre-requisitos:** Haber completado el Escenario 6.1 y cerrar la sesión actual (`POST /logout` o desde el menú de usuario).
- **Pasos a ejecutar:**
  1. Ir a `http://localhost:8000/login`.
  2. Pulsar sobre **"Continuar con Google"**.
  3. Seleccionar la misma cuenta de Google utilizada previamente.
- **Resultado Esperado:**
  - Acceso instantáneo en 1 solo clic sin solicitar contraseñas.
  - Al tener ya su dirección postal guardada, **no pasa por el onboarding** y redirige directamente al catálogo o a la página prevista con el mensaje: *"¡Sesión iniciada con Google correctamente!"*.

---

##### Escenario 6.3: Vinculación Automática de Cuenta Tradicional Existente
- **Pre-requisitos:** Existencia previa de un usuario registrado por formulario tradicional (email y contraseña) cuyo correo coincida con una cuenta de Google (ej: `usuario@gmail.com`).
- **Pasos a ejecutar:**
  1. Ir a `http://localhost:8000/login`.
  2. Pulsar en **"Continuar con Google"** seleccionando la cuenta de Gmail coincidente.
- **Resultado Esperado:**
  - El sistema detecta el correo electrónico ya verificado por Google y vincula de forma transparente el `google_id` y avatar sin duplicar el registro en la tabla `users`.
  - Inicia la sesión y notifica: *"¡Tu cuenta de Google ha sido vinculada con éxito!"*.

---

##### Escenario 6.4: Autenticación con Google desde el Checkout Adaptativo y Fusión de Carrito
- **Pre-requisitos:** Navegador sin sesión iniciada.
- **Pasos a ejecutar:**
  1. Navegar a `http://localhost:8000/catalog` y añadir 2 productos al carrito.
  2. Ir a `http://localhost:8000/checkout`.
  3. En la tarjeta superior de aviso para invitados, pulsar el botón de acceso rápido **"Google"**.
  4. Autorizar la cuenta de Google.
- **Resultado Esperado:**
  - La sesión de Google se inicia y el listener `MergeCartOnLogin` traslada automáticamente los artículos del carrito de la sesión al usuario en base de datos.
  - El usuario vuelve al flujo de `/checkout` como usuario registrado, mostrando sus direcciones guardadas para seleccionar en un solo clic.

### 5.3 Automatización y Certificación E2E con Playwright

Para garantizar la reproducibilidad y ejecución en pipelines de CI/CD, los flujos descritos en los Casos de Prueba 1 al 6 se han automatizado mediante una suite end-to-end completa con **Playwright** (`@playwright/test`):

- **Archivo de especificación:** [`Reposa+/e2e/casos-1-to-5.spec.js`](../../Reposa+/e2e/casos-1-to-5.spec.js)
- **Configuración:** [`Reposa+/playwright.config.js`](../../Reposa+/playwright.config.js) (headless Chromium contra `http://localhost:8000`)
- **Comandos de ejecución:**
  ```bash
  # Ejecución de la suite E2E completa
  npm run test:playwright
  # Equivalente: npx playwright test

  # Ejecución en modo visible (headed) para inspección visual
  npm run test:playwright:headed
  ```
- **Resultados de certificación:** **6/6 tests pasados con éxito (100% de aserciones cumplidas en ~8s)**.

---

## Registro de Cambios y Trazabilidad

| Fecha | Autor | Versión | Resumen de Cambios |
|---|---|:---:|---|
| **05/09/2026** | Jonathan Quispe | `v1.0.0` | Creación del roadmap integral de checkout adaptativo, paquetería estándar mock, integración Google OAuth 2.0 y protocolo de pruebas manuales. |
| **05/09/2026** | Jonathan Quispe | `v1.1.0` | Implementación completa de Fase 4 (Google OAuth 2.0, Socialite, onboarding en 2 pasos) y formalización detallada de los 4 sub-escenarios de prueba manual en el Caso de Prueba 6. |
| **05/09/2026** | Jonathan Quispe | `v1.2.0` | Automatización y certificación de la Fase 5 con suite E2E en Playwright (`Reposa+/e2e/casos-1-to-5.spec.js`) pasando 6/6 tests al 100%. |
