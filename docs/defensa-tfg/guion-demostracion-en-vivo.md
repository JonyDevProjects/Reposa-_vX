# Guion de Demostración Práctica en Vivo (*Live Demo*) — Reposa+

**Proyecto:** Reposa+ — E-Commerce Transaccional *Sleep Tech*  
**Autor:** Jonathan Quispe  
**Duración Exacta de la Demostración:** 4 minutos (dentro de los 15 minutos totales de la defensa)  
**Objetivo:** Guiar al ponente paso a paso para ejecutar una demostración funcional impecable, sin fricción técnica, anticipando posibles contingencias y dejando una impresión de fiabilidad industrial ante el tribunal evaluador.

---

## 1. Protocolo de Preparación Previa (*Pre-Flight Checklist*)

Para garantizar una ejecución sin tropiezos, el entorno debe prepararse **10 minutos antes** del inicio de la sesión de defensa:

### 1.1 Arranque y Verificación del Stack Docker
Si los contenedores no están levantados, iniciarlos en segundo plano desde la carpeta de la aplicación:
```bash
cd Reposa+ && docker compose up -d
```
Verificar que los servicios principales están activos y saludables:
```bash
docker compose ps
```
*Comprobar que `reposaplus_app`, `reposaplus_lb`, `reposaplus_mysql`, `reposaplus_redis` y `reposaplus_mailhog` se encuentran en estado `Up`.*

### 1.2 Inicialización de Datos de Prueba Deterministas (Rearme Limpio)
Ejecutar el comando de reinicio de la matriz de pruebas para garantizar stock fresco y pedidos limpios:
```bash
docker exec reposaplus_app php artisan orders:reset-test-matrix
```
*(Este comando vacía pedidos volátiles de prueba, siembra los 9 pedidos modelo del ciclo de vida y asegura stock suficiente en todos los productos del catálogo).*

### 1.3 Configuración de Pestañas en el Navegador Web (Google Chrome / Brave)
Abrir una ventana de navegador dedicada a pantalla completa con las siguientes **3 pestañas preparadas en orden de izquierda a derecha**:

1. **Pestaña 1 (Tienda / Catálogo):** `http://localhost:8000/catalog` (Modo Incógnito o sesión limpia sin autenticar; también accesible en `http://localhost/catalog`).
2. **Pestaña 2 (Bandeja de Correo MailHog):** `http://localhost:8025` (Bandeja limpia para visualizar emails transaccionales instantáneos).
3. **Pestaña 3 (Panel de Administración):** `http://localhost:8000/admin/orders` (Sesión previamente iniciada con `admin@reposaplus.com` / `admin123`).

### 1.4 Datos de Prueba a Mano (Copiar / Pegar Rápido)
* **Nombre del Cliente Invitado:** Carlos García
* **Email:** `carlos.garcia@example.com`
* **Teléfono:** `612345678`
* **Dirección:** Calle Sierpes 42, 2º B, 41004 Sevilla
* **Tarjeta de Pruebas Stripe:** `4242 4242 4242 4242` | Caducidad: `12/28` | CVC: `123` | Código Postal: `41004`

---

## 2. Cronograma Minuto a Minuto de la Demostración (4 Minutos)

---

### Minuto 00:00 - 00:45 | Catálogo Ergonómico, Píldoras de Categoría y Filtros Dinámicos
* **Ubicación:** Pestaña 1 (`http://localhost:8000/catalog`).
* **Acción del Ponente:**
  1. Mostrar el catálogo visual: diseño *Sleep Tech* con paleta "The Midnight Sanctuary", tarjetas de producto con insignias de descanso y precio directo sin fricciones.
  2. Demostrar la navegación ágil: hacer clic en las **píldoras de categoría** para alternar de inmediato entre colecciones (ej. *"Cervicales"*).
  3. Desplegar el botón de **"Filtros"**: mostrar el panel colapsable con segmentación por firmeza, material ergonómico (viscoelástica, látex) y rango de precio.
  4. Probar la búsqueda rápida: escribir `"cervical"` en el buscador integrado.
  5. Localizar la **Almohada Cervical Ergonómica** (Precio: 45,00€) y pulsar **"Añadir a la Cesta"** (o ver ficha).
* **Voz en Off del Ponente:**
  > *"Nos encontramos en la tienda. Observen cómo la interfaz prioriza el descubrimiento limpio del producto eliminando sobrecarga cognitiva. El catálogo sitúa los productos en primer plano de inmediato. Disponemos de píldoras de acceso rápido a categorías y un panel secundario de filtros para afinar por firmeza, material o precio. Añadimos una unidad de nuestra almohada cervical estrella por 45,00€ y nos dirigimos al carrito."*

---

### Minuto 00:45 - 01:30 | Carrito Reactivo y Motor `CartCalculator`
* **Ubicación:** Pestaña 1 (`http://localhost:8000/cart`).
* **Acción del Ponente:**
  1. Mostrar la barra de información logística: el subtotal es 45,00€ y el sistema alerta: *"¡Te faltan 5,00€ para disfrutar de Envío Gratuito!"* (Tarifa estándar calculada: 4,95€).
  2. Pulsar el botón **`+`** en la columna de cantidad para subir de 1 a 2 unidades.
  3. Notar la actualización asíncrona inmediata (300ms de debounce):
     - El subtotal cambia a 90,00€.
     - El envío se recalcula automáticamente a **0,00€** con un badge verde de felicitación.
     - La cuota de IVA al 21% se desglosa con exactitud matemática.
  4. Redimensionar brevemente el ancho de la ventana o pulsar F12 en modo móvil para mostrar la transformación de tabla a tarjeta táctil ergonómica.
  5. Pulsar el botón principal **"Finalizar Pedido"**.
* **Voz en Off del Ponente:**
  > *"En el carrito, el servicio desacoplado `CartCalculator` recalcula subtotales e impuestos en tiempo real sin recargar la página. Al incrementar a dos unidades, superamos el umbral de 50€ y el envío pasa automáticamente a ser gratuito. Pulsamos 'Finalizar Pedido' para iniciar el checkout."*

---

### Minuto 01:30 - 02:45 | Guest Checkout, Resiliencia BFCache y Stripe
* **Ubicación:** Pestaña 1 (`http://localhost:8000/checkout`).
* **Acción del Ponente:**
  1. Destacar que **no se exige registro ni login previo**.
  2. Rellenar los campos con los datos del cliente invitado (Carlos García).
  3. **Demostración de Resiliencia (BFCache):**
     - Seleccionar "Pagar con Tarjeta (Stripe)" y pulsar el botón de pago.
     - En cuanto cargue la página segura de Stripe Checkout, pulsar el botón **"Atrás"** del navegador.
     - Demostrar que los datos de Carlos García permanecen intactos gracias a `sessionStorage` y que el botón de pago está inmediatamente activo (neutralización del BFCache mediante `pageshow`).
  4. Volver a pulsar "Pagar con Tarjeta" e introducir los datos de prueba de Stripe (`4242 4242 4242 4242`, fecha futura `12/28`, CVC `123`).
  5. Completar el pago $\rightarrow$ Redirección automática a la vista de confirmación.
* **Voz en Off del Ponente:**
  > *"El checkout de invitados elimina la mayor barrera de conversión del comercio electrónico. Si el cliente retrocede desde la pasarela, nuestra arquitectura en doble capa preserva los datos y reactiva el botón. Introducimos los datos de prueba en Stripe y completamos la transacción."*

---

### Minuto 02:45 - 03:45 | Token Criptográfico, Factura PDF y Back-Office
* **Ubicación:** Pestaña 1 (`/orders/{id}?token=...`) y Pestaña 3 (`/admin/orders`).
* **Acción del Ponente:**
  1. En la página de confirmación, señalar la URL protegida con `guest_token` criptográfico.
  2. Pulsar **"Descargar Factura en PDF"** $\rightarrow$ el navegador abre o descarga el PDF oficial con diseño corporativo, desglose de IVA y validez fiscal.
  3. Mostrar el botón **"Crear Cuenta para Seguir Pedido"** (*Claim Account en 1 Clic*).
  4. Cambiar a la **Pestaña 3 (`/admin/orders`)** y recargar:
     - El pedido de Carlos García aparece en primera posición con estado **"Procesando"** y método de pago **Stripe**.
     - Abrir el desplegable de acciones y pulsar **"Imprimir Etiqueta de Envío"**.
     - Se abre la etiqueta térmica en formato estándar A6 con código de barras Code 128 y código `RPX2026...ES`.
     - Mostrar el botón de **"Reembolsar"** directo sincronizado con la API de Stripe.
* **Voz en Off del Ponente:**
  > *"El pedido queda blindado mediante un token criptográfico de 64 caracteres. El usuario puede descargar su factura fiscal en PDF generada en el servidor y reclamar su cuenta en un clic. En el back-office del administrador, el pedido se sincroniza al instante: podemos generar la etiqueta térmica A6 de paquetería estándar para el almacén o tramitar un reembolso con reversión automática de stock."*

---

### Minuto 03:45 - 04:00 | Cierre de la Demostración
* **Acción del Ponente:** Cerrar las pestañas y volver a proyectar la presentación de diapositivas (Diapositiva 9: Estrategia de Calidad).
* **Voz en Off del Ponente:**
  > *"Como han podido observar, el ciclo completo —desde la búsqueda anatómica hasta la emisión de la etiqueta logística y la factura— opera de forma continua, rápida y transaccionalmente segura. Regresemos a la presentación para analizar la estrategia de pruebas que garantiza esta fiabilidad."*

---

## 3. Plan de Contingencia y Recuperación Rápida (*Plan B*)

En una defensa académica pueden ocurrir imprevistos de red o infraestructura. Este protocolo garantiza una respuesta instantánea:

| Escenario de Fallo | Causa Probable | Solución Inmediata en Vivo (Plan B) |
|---|---|---|
| **Stripe Checkout no carga o responde con timeout** | Pérdida de conexión a internet en la sala de grados. | Seleccionar la opción de **"Pago Directo / En Efectivo"** (implementada específicamente en Reposa+ para entornos de pruebas locales). El pedido se consolida igualmente en MySQL sin depender de llamadas HTTP externas a Stripe. |
| **El webhook de Stripe no llega localmente** | Falta de Stripe CLI listener activo en local. | La aplicación cuenta con fallback síncrono: al redirigir al cliente a `/checkout/success`, el controlador verifica el estado de la sesión mediante la API de Cashier y confirma el pedido inmediatamente. |
| **Un contenedor Docker se detiene** | Consumo de memoria o reinicio inesperado. | Abrir una terminal secundaria oculta y ejecutar en 3 segundos: `docker compose up -d`. |
| **Error en datos de prueba o stock agotado** | Pruebas previas consumieron las existencias. | Ejecutar en la terminal el comando de rescate: `docker exec reposaplus_app php artisan orders:reset-test-matrix` (tarda menos de 1 segundo). |
