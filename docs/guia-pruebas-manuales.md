# Guía Rápida de Pruebas Manuales — Reposa+

Esta guía reúne de forma centralizada todas las credenciales, URLs de servicios, datos de pago y los pasos recomendados para realizar las pruebas manuales de extremo a extremo en el entorno local/staging de **Reposa+**.

---

## 1. URLs de Acceso a Servicios Locales

| Servicio | URL Local | Descripción |
|---|---|---|
| **Tienda / Storefront** | [http://localhost](http://localhost) (o `:8000`) | Catálogo de almohadas, carrito, onboarding y checkout. |
| **Panel de Administración** | [http://localhost/admin](http://localhost/admin) | Dashboard, gestión de productos, pedidos y logística. |
| **Buzón de Correos (MailHog)** | [http://localhost:8025](http://localhost:8025) | Intercepta todos los emails transaccionales (confirmación, facturas, reembolsos). |
| **Consola MinIO (S3)** | [http://localhost:9001](http://localhost:9001) | Almacenamiento de imágenes de catálogo y facturas PDF (`minioadmin` / `minioadmin`). |

---

## 2. Credenciales de Usuario Preconfiguradas (Seeders)

Las cuentas creadas automáticamente por los seeders (`php artisan db:seed`) son:

### A) Administrador de la Tienda
* **Email:** `admin@reposaplus.com`
* **Contraseña:** `admin123`
* **Permisos:** Acceso completo al back-office (`/admin`), avance de estados logísticos, impresión de albaranes A6 y gestión de reembolsos.

### B) Cliente Registrado Estándar
* **Email:** `user@reposaplus.com`
* **Contraseña:** `user123`
* **Perfil:** Cuenta con dirección predeterminada configurada en Madrid, historial de pedidos y lista de favoritos.

### C) Comprador Invitado (*Guest Checkout*)
* No requiere credenciales previas. Cualquier correo no registrado (ej. `invitado@ejemplo.com`) puede comprar directamente. El sistema genera un `guest_token` criptográfico de 40 caracteres para el seguimiento de la orden.

---

## 3. Datos de Pago para Pruebas (Stripe Test Mode)

Al seleccionar **"Tarjeta Bancaria / Stripe"** en la pantalla de `/checkout`, la pasarela redirige a la página segura de Stripe en modo pruebas.

### A) Tarjeta de Pago Exitoso (Flujo Feliz)
* **Número de tarjeta:** `4242 4242 4242 4242`
* **Caducidad:** Cualquier fecha futura (ej. `12/28` o `08/30`)
* **CVC:** `123` (o cualquier combinación de 3 dígitos)
* **Nombre:** Cualquier nombre (ej. `Carlos Pruebas`)
* **Código Postal:** Cualquier código postal válido (ej. `28013` o `08018`)

### B) Tarjeta con Autenticación Bancaria 3D Secure (SCA)
* **Número de tarjeta:** `4000 0027 6000 3184`
* **Comportamiento:** Despliega la ventana de autorización bancaria simulada; pulsar en *"Complete Authentication"* para aprobar el pago.

### C) Tarjeta Rechazada / Fondos Insuficientes
* **Número de tarjeta:** `4000 0000 0000 0002`
* **Comportamiento:** Simula un fallo de cobro bancario y redirige a la tienda mostrando el mensaje de error correspondiente para reintentar el pago.

### D) Pedido Directo / Contra Reembolso (Sin Tarjeta)
* Si no deseas ingresar tarjetas, selecciona la opción **"Pago contra reembolso / Pedido directo"** en `/checkout`. Confirma el pedido al instante en un solo clic.

---

## 4. Escenarios Clave Recomendados para Pruebas Manuales

### Escenario 1: Compra como Invitado con Envío Gratuito ($\ge 50€$)
1. Ve a `/catalog` y añade una almohada de precio $\ge 50€$ (o varias unidades de menor importe).
2. Dirígete a `/checkout`.
3. Observa el **banner verde de felicitación** y el badge `¡ENVÍO GRATUITO!` en la tarifa Correos Express Estándar.
4. Rellena los datos de envío del invitado (Nombre, Email, Calle, Ciudad, CP, Teléfono).
5. Selecciona el método de pago deseado y confirma el pedido.
6. Comprueba que eres redirigido a `/orders/{id}?token={guest_token}`.

### Escenario 2: Conversión de Invitado en 1 Clic (*Claim Account*)
1. En la pantalla de confirmación del Escenario 1, localiza la tarjeta inferior **"Guarda tu cuenta en 1 clic"**.
2. Introduce una contraseña (ej. `MiPassword123!`) y confirma.
3. El sistema vinculará automáticamente el pedido y la dirección postal a tu nueva cuenta y te iniciará sesión en `/profile`.

### Escenario 3: Verificación de Factura en PDF y Seguridad de Rutas
1. En la pantalla de confirmación, pulsa en **"Descargar Factura PDF"**. Comprueba que el archivo PDF se descarga con el desglose del pedido y el IVA (21%).
2. Abre una ventana en modo incógnito (sin sesión ni cookies) e intenta acceder directamente a `/orders/{id}` sin token. Comprueba que el sistema bloquea el acceso con **HTTP 403 Forbidden**.
3. Añade el parámetro `?token={guest_token}` y verifica que ahora sí permite la visualización y descarga de la factura.

### Escenario 4: Operativa Logística en el Panel Admin y Etiqueta Térmica A6
1. Inicia sesión con el administrador (`admin@reposaplus.com` / `admin123`).
2. Ve a `/admin/orders`.
3. Localiza el pedido creado. Observa el número de seguimiento `RPX2026...ES` y el estado del envío.
4. Pulsa el botón **"Avanzar"** en la columna de paquetería para transicionar el estado del envío (*Etiqueta creada* $\rightarrow$ *En tránsito* $\rightarrow$ *Entregado*).
5. Pulsa en **"Etiqueta A6"** para abrir en una nueva pestaña el albarán térmico estándar (10x15cm) con código de barras Code 128 listo para impresión.
6. Cambia el estado del pedido en el selector y comprueba que solicita confirmación serena antes de aplicar la transición.

### Escenario 5: Inspección de Correos en MailHog
1. Abre [http://localhost:8025](http://localhost:8025) en el navegador.
2. Comprueba la bandeja de entrada: verás el correo de confirmación de pedido recibido con el resumen y el número de tracking de Correos Express.

---

## 5. Documentos Relacionados en el Repositorio

* [`docs/Manual_Desarrollador_CICD.md`](./Manual_Desarrollador_CICD.md) — Sección 8 (Puertos/Servicios) y Sección 9 (Credenciales y entorno Docker).
* [`docs/userpass.txt`](./userpass.txt) — Credenciales resumidas de acceso admin y cliente.
* [`docs/Memoria_Proyecto.md`](./Memoria_Proyecto.md) — Sección 6.7 (Credenciales y entorno de evaluación para el tribunal).
* [`Reposa+/e2e/casos-1-to-5.spec.js`](../Reposa+/e2e/casos-1-to-5.spec.js) — Especificación automatizada con los 8 flujos de prueba reproducibles.
