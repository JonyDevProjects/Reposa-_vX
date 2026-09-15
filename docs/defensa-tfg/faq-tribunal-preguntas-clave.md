# FAQ para el Tribunal: Batería de Preguntas Clave y Argumentario Técnico — Reposa+

**Proyecto:** Reposa+ — E-Commerce Transaccional *Sleep Tech*  
**Autor:** Jonathan Quispe  
**Objetivo del Documento:** Proveer al alumno de un repertorio exhaustivo de respuestas técnicas, arquitectónicas y metodológicas para responder con seguridad, precisión y autoridad académica a las preguntas más inquisitivas del tribunal evaluador.

---

## Índice Temático de Preguntas

1. [Concurrencia de Inventario, Bloqueo Pesimista y Condiciones de Carrera](#1-concurrencia-de-inventario-bloqueo-pesimista-y-condiciones-de-carrera)
2. [Estrategia de Calidad: Testing Trophy frente a la Pirámide de Mike Cohn](#2-estrategia-de-calidad-testing-trophy-frente-a-la-pirámide-de-mike-cohn)
3. [Pasarela de Pagos Stripe: Webhooks Asíncronos, Idempotencia y Reintentos](#3-pasarela-de-pagos-stripe-webhooks-asíncronos-idempotencia-y-reintentos)
4. [Seguridad en Guest Checkout: `guest_token`, BFCache y Privacidad de Datos](#4-seguridad-en-guest-checkout-guest_token-bfcache-y-privacidad-de-datos)
5. [Optimización de Base de Datos: Mitigación del Problema N+1 y Vistas SQL Nativas](#5-optimización-de-base-de-datos-mitigación-del-problema-n1-y-vistas-sql-nativas)
6. [Uso de Agentes de IA (Antigravity SDK) y Autoría Intelectual del TFG](#6-uso-de-agentes-de-ia-antigravity-sdk-y-autoría-intelectual-del-tfg)
7. [Internacionalización Multilingüe y Accesibilidad Frontend (WCAG 2.1 AA)](#7-internacionalización-multilingüe-y-accesibilidad-frontend-wcag-21-aa)
8. [Escalabilidad Horizontal y Resiliencia en Producción](#8-escalabilidad-horizontal-y-resiliencia-en-producción)

---

### 1. Concurrencia de Inventario, Bloqueo Pesimista y Condiciones de Carrera

#### Pregunta del Tribunal:
> *"En un escenario de alta concurrencia como un Black Friday, donde cientos de usuarios compran a la vez y quedan pocas unidades de una almohada, ¿cómo garantiza su sistema que no se produzca sobreventa (overselling)? ¿Por qué eligió un bloqueo pesimista en lugar de optimista?"*

#### Argumentación Técnica Defensiva:
* **El Fenómeno de Condición de Carrera (*Race Condition*):**  
  Si dos hilos leen concurrentemente `stock = 1`, ambos validan que hay disponibilidad y proceden a restar una unidad, dejando el inventario en `0` pero habiendo confirmado dos pedidos distintos (sobreventa de 1 unidad).
* **Bloqueo Pesimista con `lockForUpdate()`:**  
  En Reposa+, la verificación y descuento del stock se ejecutan dentro de una transacción atómica `DB::transaction()` invocando el método Eloquent `Product::where('id', $id)->lockForUpdate()->first()`. Esto compila en SQL como:
  ```sql
  SELECT * FROM products WHERE id = ? FOR UPDATE;
  ```
  En el motor **InnoDB de MySQL**, esta cláusula adquiere un bloqueo exclusivo a nivel de fila (*exclusive row-level lock*). Cualquier otra transacción que intente leer esa misma fila con `FOR UPDATE` quedará bloqueada en cola hasta que la primera transacción ejecute `COMMIT` o `ROLLBACK`.
* **Justificación frente al Bloqueo Optimista (*Optimistic Locking*):**  
  El bloqueo optimista (basado en versiones `version_id` o timestamps) asume que las colisiones son raras y aborta la transacción si la versión cambió al intentar guardar. En eventos de alta contención sobre productos estrella con stock escaso, el bloqueo optimista provoca una tasa masiva de excepciones y rechazos para los usuarios. El bloqueo pesimista, en cambio, serializa ordenadamente las peticiones durante escasos milisegundos, garantizando que quien llegó primero asegura su compra y los demás reciben un error amigable de "Stock Agotado" sin inconsistencias en base de datos.

---

### 2. Estrategia de Calidad: Testing Trophy frente a la Pirámide de Mike Cohn

#### Pregunta del Tribunal:
> *"La literatura clásica de Ingeniería del Software (como la Pirámide de Cohn de 2009) sostiene que la base de las pruebas debe ser abrumadoramente unitaria. Sin embargo, su proyecto cuenta con 114 pruebas de integración y solo 27 unitarias. ¿No es esto un antipatrón en términos de coste y velocidad de ejecución?"*

#### Argumentación Técnica Defensiva:
* **Evolución Histórica y Epistemológica del Testing:**  
  La pirámide de Mike Cohn fue enunciada hace más de quince años, cuando levantar bases de datos relacionales para pruebas tomaba minutos debido a la lentitud de los discos mecánicos y la falta de contenedores. En ese contexto histórico, los tests unitarios con dobles de prueba (*mocks*) eran la única vía para obtener retroalimentación rápida.
* **El Modelo del Testing Trophy (Kent C. Dodds / Martin Fowler):**  
  En aplicaciones web transaccionales modernas, el mayor retorno de inversión (*ROI*) reside en la **capa de integración**. Un test unitario con mocks de Eloquent o de base de datos introduce el grave peligro de la **"falsa confianza"**: el test pasa en verde porque el desarrollador configuró el mock para responder lo que él esperaba, pero en producción el sistema falla ante una violación de clave foránea, un tipo de datos truncado o una consulta N+1.
* **Métricas Reales de Rendimiento en Reposa+:**  
  Gracias al uso de Docker Engine y transacciones atómicas en memoria con rollback (`RefreshDatabase` / transacciones InnoDB), nuestras **114 pruebas de integración** contra instancias reales de MySQL 8.0 y Redis se ejecutan en tan solo **3.2 segundos**.  
  Hemos obtenido la máxima fidelidad operacional a la velocidad de ejecución de pruebas unitarias.
* **Principio de Demarcación para Tests Unitarios:**  
  Las pruebas unitarias puras en Reposa+ (27 tests en 0.08s con SQLite en memoria) no fueron relegadas por azar; se reservaron estrictamente para algoritmos matemáticos y máquinas de estado en memoria pura:
  1. El autómata finito de transiciones de pedido (`OrderStateUnitTest`).
  2. El cálculo determinista de tarifas de envío (`ShippingRateCalculatorUnitTest`).
  3. El motor de cómputo en memoria de carrito (`CartCalculatorUnitTest`).

---

### 3. Pasarela de Pagos Stripe: Webhooks Asíncronos, Idempotencia y Reintentos

#### Pregunta del Tribunal:
> *"Si un cliente abona el pedido en Stripe, pero pierde la conexión o cierra el navegador antes de ser redirigido a la tienda, ¿el pedido queda como impagado? ¿Cómo evita que un cobro se procese dos veces si Stripe envía el webhook duplicado?"*

#### Argumentación Técnica Defensiva:
* **Arquitectura de Webhooks Asíncronos:**  
  Reposa+ nunca confía en la redirección síncrona del navegador del usuario como fuente de verdad para la confirmación contable del pedido. El verdadero cambio de estado de `pending` a `processing` se realiza en el backend al procesar el evento webhook de Stripe:
  ```text
  Stripe Server ──[POST /stripe/webhook (HMAC-SHA256)]──> Reposa+ Backend
  ```
  Aunque el usuario cierre su pestaña, el servidor de Stripe notifica directamente a nuestro servidor HTTPS.
* **Seguridad y Verificación Criptográfica:**  
  El endpoint de webhooks valida la firma en la cabecera `Stripe-Signature` utilizando el secreto compartido del webhook (`STRIPE_WEBHOOK_SECRET`) mediante HMAC-SHA256. Cualquier petición maliciosa o no firmada por Stripe es rechazada de inmediato con HTTP 400.
* **Idempotencia Transaccional:**  
  Por protocolo, Stripe reintenta el envío de webhooks si no recibe un código HTTP 200 (hasta 3 días con retroceso exponencial). Para garantizar idempotencia:
  1. Almacenamos el `payment_intent_id` y el `stripe_session_id` en la tabla `orders` indexados como campos únicos.
  2. Al recibir el evento `checkout.session.completed`, el manejador comprueba si el pedido ya se encuentra en estado `processing`, `shipped` o `completed`.
  3. Si ya fue procesado, responde inmediatamente `HTTP 200 OK` a Stripe sin volver a descontar stock ni reenviar correos electrónicos duplicados.

---

### 4. Seguridad en Guest Checkout: `guest_token`, BFCache y Privacidad de Datos

#### Pregunta del Tribunal:
> *"El proceso de compra como invitado (Guest Checkout) facilita la venta, pero plantea retos de seguridad: ¿cómo evita que un atacante adivine el identificador del pedido y robe los datos personales del comprador? ¿Y cómo solucionaron los problemas con la caché del navegador al navegar hacia atrás?"*

#### Argumentación Técnica Defensiva:
* **Protección Criptográfica mediante `guest_token`:**  
  Los identificadores autoincrementales de pedidos (`id: 42`) son predecibles y vulnerables a ataques de enumeración (*Insecure Direct Object References - IDOR*). Para mitigarlo:
  1. Cada pedido de invitado genera una cadena pseudoaleatoria criptográficamente segura de 64 caracteres en base64 (`guest_token = Str::random(64)`) almacenada en la base de datos con índice único.
  2. La ruta de acceso `/orders/{id}?token={guest_token}` valida estrictamente que el token proporcionado coincida con el hash del pedido. Si no coincide, devuelve un error HTTP 403 Prohibido, imposibilitando el acceso ilegítimo a la dirección de envío o a la factura PDF.
* **Resiliencia ante el Back-Forward Cache (BFCache):**  
  Los navegadores modernos almacenan una instantánea en memoria de la página al navegar a Stripe. Al pulsar "Atrás", la página se restaura con el botón de envío congelado en estado deshabilitado (*disabled/spinner*) y los campos vacíos.
  En Reposa+ neutralizamos esta fragilidad suscribiéndonos al evento nativo del DOM `window.addEventListener('pageshow', ...)`:
  ```javascript
  window.addEventListener('pageshow', function (event) {
      if (event.persisted) {
          resetSubmitButton();
          restoreFormFromSessionStorage();
      }
  });
  ```
  Si `event.persisted` es verdadero, el botón se rehabilita al instante y los datos de envío se recuperan desde `sessionStorage`, impidiendo que el cliente abandone frustrado.

---

### 5. Optimización de Base de Datos: Mitigación del Problema N+1 y Vistas SQL Nativas

#### Pregunta del Tribunal:
> *"El ORM Eloquent es conocido por incurrir fácilmente en el problema de consultas N+1. ¿Qué medidas concretas aplicó en Reposa+ para asegurar que el panel administrativo y el catálogo no degraden su rendimiento a medida que crezca el número de registros?"*

#### Argumentación Técnica Defensiva:
* **Mitigación del Antipatrón N+1 mediante Eager Loading:**  
  Por defecto, si se listan 50 pedidos y en el bucle Blade se invoca `$order->user->name` y `$order->items->count()`, Eloquent ejecutaría 1 consulta inicial más 50 consultas para usuarios y 50 para items (101 consultas SQL).  
  En Reposa+ aplicamos **carga ansiosa (*Eager Loading*)** en todos los controladores:
  ```php
  $orders = Order::with(['user', 'items.product', 'shipment'])->paginate(15);
  ```
  Esto reduce las 101 consultas a exactamente **4 consultas SQL optimizadas** con cláusulas `WHERE IN (...)`, independientemente del volumen de pedidos.
* **Vistas SQL Nativas para Reporting Complejo:**  
  Para el cuadro de mando administrativo (`/admin`), creamos dos vistas SQL nativas indexadas en MySQL:
  1. `v_order_summary`: Consolida en una sola tabla virtual los subtotales, totales de IVA, costes de envío y conteo de ítems por pedido.
  2. `v_top_favorited_products`: Agrega los productos con mayor número de interacciones de lista de deseos.  
  El uso de vistas SQL nativas delega los cálculos agregados al propio motor relacional C++ de MySQL, reduciendo los tiempos de respuesta del dashboard en un **30%** frente a colecciones PHP hidratadas en memoria.

---

### 6. Uso de Agentes de IA (Antigravity SDK) y Autoría Intelectual del TFG

#### Pregunta del Tribunal:
> *"En la memoria del proyecto se menciona el uso de un ecosistema de agentes autónomos de IA (Google Antigravity). ¿Hasta qué punto el software presentado es fruto de su autoría intelectual como ingeniero informático y no una mera generación automática de código?"*

#### Argumentación Técnica Defensiva:
* **El Paradigma de la Ingeniería Asistida por Agentes:**  
  La autoría de una obra de ingeniería no se mide por teclear caracteres, sino por la **capacidad arquitectónica de diseñar el sistema, formular las restricciones del problema, gobernar los contratos de interfaz y validar rigurosamente la solución**.
* **El Rol del Alumno como Director Técnico y Arquitecto:**  
  1. **Diseño y Dominio:** La IA no inventó el modelo de negocio *Sleep Tech*, ni concibió la máquina de estados de los pedidos, ni definió la política fiscal del 21% de IVA, ni diseñó el sistema de paquetería estándar con identificadores `RPX...ES`. Todo el modelado conceptual fue obra humana.
  2. **Detección de Desviaciones Críticas:** Durante la Iteración 4.1, los agentes generaron un código que fallaba al procesar reembolsos de Stripe (`Cashier::stripe()->paymentIntents->refund(...)`). Fue el criterio humano del alumno el que detectó el error de firma de la API, analizó el fatal error en los logs y reestructuró la invocación a `Cashier::stripe()->refunds->create(...)`.
  3. **Certificación de Calidad:** Cada línea de código propuesta por los asistentes fue sometida a la pirámide de 141 pruebas Pest, análisis estático con Laravel Pint y pruebas E2E con Playwright.  
  El ecosistema de agentes actuó como un equipo de desarrolladores junior altamente productivo, mientras que el alumno ejerció el papel indispensable de **Tech Lead / Arquitecto de Software**.

---

### 7. Internacionalización Multilingüe y Accesibilidad Frontend (WCAG 2.1 AA)

#### Pregunta del Tribunal:
> *"¿Cómo gestiona la plataforma el soporte multi-idioma (español e inglés) sin duplicar registros en la base de datos ni generar sobrecarga? ¿Y cómo garantiza la accesibilidad para usuarios con discapacidades visuales?"*

#### Argumentación Técnica Defensiva:
* **Internacionalización Basada en Columnas JSON Nativas:**  
  En lugar del antipatrón de crear tablas duplicadas (`products_es`, `products_en`), implementamos el paquete de referencia de la comunidad `spatie/laravel-translatable`. Las columnas de texto (`name`, `description`) se almacenan en MySQL como campos tipo `JSON`:
  ```json
  {"es": "Almohada Cervical Ergonómica", "en": "Ergonomic Cervical Pillow"}
  ```
  Esto permite que las consultas de catálogo consulten el idioma activo de la sesión (`app()->getLocale()`) sin sobrecoste de joins y manteniendo integridad referencial absoluta. Se homologaron **570 claves de traducción** en los catálogos `lang/es/` y `lang/en/` sin cadenas huérfanas.
* **Accesibilidad Frontend (WCAG 2.1 AA):**  
  La paleta cromática "The Midnight Sanctuary" fue auditada contra las directrices de contraste WCAG 2.1:
  1. Todos los textos sobre fondos oscuros o de botones cumplen un ratio de contraste superior a **4.5:1** para texto normal y **3.0:1** para texto grande o elementos interactivos.
  2. Los campos de formulario de checkout cuentan con etiquetas `<label>` semánticas, estados de error anunciados y soporte de navegación por teclado (`Tab`, `Enter`).
  3. Las imágenes de producto disponen de atributos `alt` descriptivos y los iconos decorativos incorporan `aria-hidden="true"`.

---

### 8. Escalabilidad Horizontal y Resiliencia en Producción

#### Pregunta del Tribunal:
> *"Si Reposa+ pasara a producción y experimentara un incremento repentino de tráfico de 100x, ¿qué elementos de la arquitectura permitirían escalar horizontalmente sin rediseñar el código?"*

#### Argumentación Técnica Defensiva:
* **Estatelessness en los Contenedores de Aplicación:**  
  La capa de aplicación PHP-FPM no almacena estado en disco local. Todas las sesiones de usuario (`SESSION_DRIVER=redis`) y la caché de consultas residen en el clúster de **Redis**. Esto permite levantar N réplicas del contenedor `reposaplus_app` detrás de un balanceador de carga Nginx con algoritmo *Round-Robin* sin desincronización de sesiones de clientes.
* **Desacoplamiento de Almacenamiento de Archivos (Object Storage):**  
  Las imágenes de productos y las facturas en PDF generadas se gestionan a través de la abstracción de Laravel Storage (Flysystem), permitiendo conmutar a **Amazon S3** o **MinIO** sin modificar una sola línea de código de los controladores.
* **Separación de Tráfico Transaccional y de Lectura en Base de Datos:**  
  Laravel soporta de forma nativa la división de conexiones de lectura y escritura en `config/database.php`:
  ```php
  'mysql' => [
      'read' => ['host' => ['192.168.1.11', '192.168.1.12']],
      'write' => ['host' => ['192.168.1.10']],
  ]
  ```
  Las consultas de búsqueda en el catálogo se derivan a réplicas de lectura, reservando el nodo maestro MySQL para las transacciones con bloqueo pesimista.
