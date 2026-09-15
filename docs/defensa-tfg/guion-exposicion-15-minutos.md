# Guion de Exposición Oral para la Defensa Académica del TFG — Reposa+

**Proyecto:** Reposa+ — Plataforma de Comercio Electrónico Transaccional de Almohadas Ergonómicas y *Sleep Tech*  
**Autor:** Jonathan Quispe  
**Grado:** Grado en Ingeniería Informática  
**Duración Total de la Exposición:** 15 minutos exactos (más turno de preguntas)  
**Entorno de Soporte:** Diapositivas de alto impacto ([`estructura-diapositivas.md`](./estructura-diapositivas.md)) y Demostración Práctica ([`guion-demostracion-en-vivo.md`](./guion-demostracion-en-vivo.md))

---

## 1. Tabla Cronológica y Minutaje Estratégico

| Bloque | Minutaje | Diapositivas | Eje Temático y Núcleo Narrativo |
|:---:|:---:|:---:|---|
| **I** | **00:00 - 02:30** (2:30 min) | Slides 1 - 3 | **Introducción, Nicho de Negocio y Objetivos:** Sleep Tech, *"No vendemos almohadas, vendemos descanso"*, justificación técnica de Laravel frente a CMS empaquetados (Shopify/WooCommerce). |
| **II** | **02:30 - 06:00** (3:30 min) | Slides 4 - 6 | **Arquitectura del Sistema e Integridad Transaccional:** Arquitectura de 7 servicios Docker, Modelo E-R relacional, bloqueo pesimista `lockForUpdate()`, webhooks idempotentes de Stripe y UX persuasiva "The Midnight Sanctuary". |
| **III** | **06:00 - 10:00** (4:00 min) | Slides 7 - 8 | **Demostración Práctica en Vivo (*Live Demo*):** Compra integral como invitado (*Guest Checkout*), reactividad del carrito (`CartCalculator`), pasarela Stripe en staging, confirmación con `guest_token`, factura PDF y back-office con etiqueta térmica A6. |
| **IV** | **10:00 - 13:00** (3:00 min) | Slides 9 - 10 | **Estrategia de Calidad y Pipeline CI/CD:** El Trofeo de Pruebas (*Testing Trophy*) frente a la Pirámide de Cohn, 141 tests Pest en <3.3s, 8/8 Playwright E2E y flujo CI/CD con 5 jobs en GitHub Actions. |
| **V** | **13:00 - 15:00** (2:00 min) | Slides 11 - 12 | **Ecosistema de Agentes de IA, Conclusiones y Futuro:** El ingeniero como orquestador de agentes de IA (Antigravity SDK), logros alcanzados, trabajo futuro (suscripciones, analítica predictiva) y cierre formal. |

---

## 2. Desarrollo Narrativo de la Presentación Oral

---

### BLOQUE I: Introducción, Nicho de Negocio y Objetivos
**Tiempo asignado:** `00:00 - 02:30` (2 minutos y 30 segundos)  
**Diapositivas de soporte:** Diapositivas 1, 2 y 3  
**Objetivo comunicativo:** Captar el interés inmediato del tribunal, establecer la propuesta de valor diferencial y justificar por qué la solución requiere ingeniería de software a medida en lugar de un CMS comercial.

#### Transcripción Narrativa Sugerida:
> *"Buenos días, miembros del tribunal evaluador. Mi nombre es Jonathan Quispe y hoy tengo el honor de presentarles la defensa de mi Trabajo de Fin de Grado titulado: **Reposa+: Plataforma Transaccional de Comercio Electrónico de Alto Rendimiento Especializada en Descanso Ergonómico y Sleep Tech**.*
> 
> *Para entender el origen de este proyecto, debemos observar una paradoja contemporánea: un tercio de la población adulta padece trastornos del sueño derivados de una mala ergonomía cervical. Sin embargo, cuando un usuario intenta comprar una almohada online, se encuentra con catálogos masificados, descripciones genéricas y nulo asesoramiento biomecánico.*
> 
> *De aquí surge el lema central de Reposa+: **'No vendemos almohadas; vendemos noches de descanso profundo'**. Nuestro propósito fue conceptualizar y construir una plataforma web donde la prescripción ergonómica y la experiencia del usuario se combinan con un motor transaccional de máxima fiabilidad.*
> 
> *Frente a esta necesidad, la primera decisión crítica fue de índole técnica: ¿por qué no utilizar un gestor de contenidos empaquetado como WooCommerce, Magento o Shopify? La respuesta reside en las limitaciones intrínsecas de estas herramientas: sobrecarga computacional de plugins de terceros, código espagueti con graves riesgos de seguridad, opacidad en la gestión de concurrencia y dependencia de bases de datos no optimizadas para cargas transaccionales estrictas.*
> 
> *Reposa+ se concibe desde cero bajo el framework **Laravel** y el lenguaje PHP moderno, aplicando una rigurosa arquitectura de software basada en dominio, patrones de diseño limpios, persistencia relacional estricta y una pirámide de pruebas automatizadas que garantiza la integridad absoluta de cada euro y cada unidad de stock transaccionada."*

---

### BLOQUE II: Arquitectura del Sistema e Integridad Transaccional
**Tiempo asignado:** `02:30 - 06:00` (3 minutos y 30 segundos)  
**Diapositivas de soporte:** Diapositivas 4, 5 y 6  
**Objetivo comunicativo:** Demostrar solidez técnica en ingeniería de software, dominio de bases de datos, prevención de condiciones de carrera (*race conditions*) y arquitectura de integración de pagos.

#### Transcripción Narrativa Sugerida:
> *"Entrando en la arquitectura del sistema, Reposa+ está desplegado sobre un ecosistema de **7 microservicios desacoplados orquestados mediante Docker**: contenedor de aplicación PHP 8, servidor web Nginx de alto rendimiento, base de datos relacional MySQL 8.0, caché persistente en memoria Redis, servidor de correo simulado MailHog y balanceadores dedicados.*
> 
> *A nivel de datos, el Modelo Entidad-Relación fue diseñado para soportar las exigencias de un comercio maduro: internacionalización nativa con campos JSON traducibles (`spatie/laravel-translatable`), trazabilidad de envíos con números de tracking estandarizados (`RPX2026...ES`) y **vistas SQL nativas** (`v_order_summary` y `v_top_favorited_products`) que reducen la latencia del panel de control mitigando sistemáticamente el antipatrón de consultas N+1 mediante *Eager Loading*.*
> 
> *Sin embargo, el corazón crítico de cualquier plataforma de comercio electrónico reside en la **integridad transaccional y la prevención de condiciones de carrera**. En un escenario de alta demanda —como una campaña de Black Friday— dos usuarios concurrentes podrían intentar adquirir la última unidad disponible de una almohada cervical viscoelástica.*
> 
> *En Reposa+, este problema se resuelve a nivel de base de datos mediante **bloqueo pesimista**. Dentro de una transacción atómica `DB::transaction()`, invocamos el método `lockForUpdate()` sobre el registro del inventario. Esto genera un bloqueo a nivel de fila (*row-level lock*) en el motor InnoDB de MySQL, forzando a la segunda petición a esperar hasta que la primera confirme o cancele su operación. Si el stock no es suficiente, la transacción ejecuta un rollback inmediato, impidiendo de raíz la sobreventa.*
> 
> *En el ámbito de la pasarela de pagos, integramos **Stripe Checkout** bajo un paradigma asíncrono y resiliente. No dependemos de que el navegador del cliente regrese a la tienda tras el pago. Escuchamos el webhook criptográfico `checkout.session.completed` firmado con clave HMAC-SHA256, aplicando **idempotencia estricta**: si Stripe reintenta el envío de un evento duplicado, el sistema verifica el `payment_intent_id` y evita cobros o confirmaciones redundantes.*
> 
> *A nivel de experiencia de usuario, implementamos el sistema de diseño **The Midnight Sanctuary**: una interfaz serena inspirada en tonos índigo y pizarra, con divulgación progresiva en el catálogo y un servicio reactivo `CartCalculator` que computa subtotales, cuotas de IVA al 21% y el umbral de envío gratuito en tiempo real con debounce adaptativo."*

---

### BLOQUE III: Demostración Práctica en Vivo (*Live Demo*)
**Tiempo asignado:** `06:00 - 10:00` (4 minutos exactos)  
**Diapositivas de soporte:** Diapositivas 7 y 8 (proyección del navegador)  
**Objetivo comunicativo:** Probar ante el tribunal que el software es real, robusto, interactivo y opera de forma impecable en tiempo real.

#### Secuencia de Acciones y Guion en Vivo:
*(El ponente pasa al navegador web con pestañas previamente preparadas: Storefront en `/catalog`, MailHog en `:8025` y Panel de Administración en `/admin/orders`).*

1. **Catálogo y Divulgación Progresiva (06:00 - 06:45):**
   > *"Veamos ahora la plataforma en funcionamiento. Nos encontramos en la página del catálogo. Noten cómo aplicamos el principio de **divulgación progresiva**: en lugar de abrumar al comprador con formularios masivos, el 'Asesor Anatómico de Firmeza' se presenta como un banner colapsable. Si lo abro, el usuario puede filtrar almohadas según su postura favorita —de lado, boca arriba o boca abajo—. Además, la barra de filtros rápidos por píldora permite alternar entre categorías con un solo clic.*
   > *Voy a seleccionar la **Almohada Cervical Ergonómica** (45,00€) y añadirla a nuestra cesta."*

2. **Carrito Reactivo y Umbral de Envío Gratuito (06:45 - 07:30):**
   > *"Al acceder al carrito, observamos el motor reactivo `CartCalculator`. El subtotal es de 45,00€, por lo que el sistema indica que faltan 5,00€ para obtener envío gratis (tarifa estándar: 4,95€). Si incremento la cantidad a 2 unidades mediante los controles táctiles, el sistema actualiza el subtotal en tiempo real mediante una petición asíncrona sin recarga de página: el importe supera los 50€ y el envío pasa automáticamente a 0,00€ con un badge de felicitación.*
   > *Adicionalmente, la interfaz es totalmente adaptable: en pantallas móviles, la tabla se transforma automáticamente en tarjetas ergonómicas."*

3. **Guest Checkout y Pasarela Stripe en Staging (07:30 - 08:30):**
   > *"Procedemos al Checkout. Un pilar fundamental de nuestra arquitectura es el **Guest Checkout**: no obligamos al cliente a registrarse ni a crear contraseñas antes de pagar, minimizando la tasa de abandono. Completamos el formulario con datos de prueba: 'Carlos García', dirección postal en Sevilla.*
   > *Al pulsar 'Pagar con Stripe', el sistema persiste estos datos en sesión y en `sessionStorage` para neutralizar el *Back-Forward Cache* del navegador: si el usuario retrocede desde la pasarela, el botón se restaura y los campos no se pierden.*
   > *Introducimos la tarjeta de pruebas de Stripe `4242...`, completamos el flujo 3D-Secure y somos redirigidos a la confirmación de pedido."*

4. **Confirmación, Token Criptográfico, Factura PDF y Back-Office (08:30 - 10:00):**
   > *"La compra se ha consolidado. Observen la URL: contiene un `guest_token` criptográfico de 64 caracteres que autoriza exclusivamente al poseedor de este enlace a visualizar los detalles del pedido y a descargar su **Factura Oficial en PDF** generada al vuelo con DomPDF, con desglose transparente de base imponible e IVA.*
   > *Además, desde esta misma pantalla, el invitado puede reclamar su cuenta en un clic (*Claim Account*) simplemente asignando una contraseña.*
   > *Pasemos ahora al **Panel de Administración**. Iniciamos sesión como administrador. En la sección de pedidos vemos inmediatamente la compra de Carlos García en estado 'Procesando'. Si entramos a la ficha logística, podemos hacer clic en **'Imprimir Etiqueta de Envío'**: el sistema genera un albarán térmico en formato estándar A6 con código de barras Code 128 y código de seguimiento oficial `RPX2026...ES` listo para ser adherido al paquete.*
   > *Asimismo, ante cualquier eventualidad, el administrador dispone de un botón de reembolso automático sincronizado con la API de devoluciones de Stripe."*

---

### BLOQUE IV: Estrategia de Calidad y Pipeline CI/CD
**Tiempo asignado:** `10:00 - 13:00` (3 minutos)  
**Diapositivas de soporte:** Diapositivas 9 y 10  
**Objetivo comunicativo:** Demostrar madurez ingenieril justificando las decisiones de testing, argumentando contra dogmas caducos y exhibiendo la automatización de integración continua en la nube.

#### Transcripción Narrativa Sugerida:
> *"Una aplicación transaccional solo es tan fiable como la suite de pruebas que la respalda. Para Reposa+, diseñamos una estrategia de calidad que aborda de forma crítica el paradigma clásico de la **Pirámide de Pruebas de Mike Cohn** (2009).*
> 
> *Cohn proponía una base masiva de pruebas unitarias y muy pocas pruebas de integración, una premisa fundamentada en que hace quince años levantar bases de datos era un proceso extremadamente lento. Hoy, referentes como **Martin Fowler** y **Kent C. Dodds** defienden el modelo del **Testing Trophy (Trofeo de Pruebas)**, donde el mayor retorno de inversión (*ROI*) reside en las **pruebas de integración**.*
> 
> *¿Por qué? Porque en un e-commerce transaccional, un test unitario con mocks no puede verificar si un bloqueo `lockForUpdate()` previene sobreventas, ni si una restricción de clave foránea en cascada protege la integridad relacional de la base de datos. Un mock solo valida lo que el programador asumió.*
> 
> *Por ello, ejecutamos 114 pruebas de integración contra instancias reales de MySQL 8.0 y Redis. Gracias a la virtualización y transacciones atómicas con rollback automático, nuestra suite completa de **141 pruebas en Pest (27 Unitarias + 114 de Integración)** se ejecuta en apenas **3.3 segundos**.*
> 
> *Las pruebas unitarias puras las reservamos estrictamente para la computación en memoria sin dependencias de I/O: la máquina de estados finitos del pedido (`OrderState`), el cálculo logístico (`ShippingRateCalculator`) y las reglas de `CartCalculator`.*
> 
> *En la cúspide del trofeo, implementamos **8 pruebas End-to-End con Microsoft Playwright**, que automatizan navegadores Chromium reales para verificar desde la interfaz gráfica todo el ciclo de vida del usuario.*
> 
> *Todo este ecosistema está blindado mediante un pipeline de **Integración Continua en GitHub Actions** (`.github/workflows/ci.yml`) estructurado en 5 etapas independientes:*
> 1. *Análisis estático y formato de código con **Laravel Pint** (135 archivos limpios).*
> 2. *Pruebas Unitarias aisladas en memoria.*
> 3. *Pruebas de Integración con servicios reales de MySQL 8 y Redis.*
> 4. *Compilación y bundling de assets frontend con **Vite**.*
> 5. *Pruebas de Sistema E2E en navegador real con Playwright.*
> *El 100% del pipeline se valida en verde antes de cada fusión a las ramas troncales."*

---

### BLOQUE V: Ecosistema de Agentes de IA, Conclusiones y Futuro
**Tiempo asignado:** `13:00 - 15:00` (2 minutos)  
**Diapositivas de soporte:** Diapositivas 11 y 12  
**Objetivo comunicativo:** Cerrar con una reflexión de vanguardia sobre la profesión del ingeniero de software, sintetizar las metas logradas y dejar una impresión impecable ante el tribunal antes del turno de preguntas.

#### Transcripción Narrativa Sugerida:
> *"Para concluir, deseo compartir una reflexión metodológica sobre el desarrollo de este proyecto. Reposa+ no solo ha sido un ejercicio de desarrollo web, sino un caso de estudio sobre el nuevo paradigma de la ingeniería de software asistida por **Ecosistemas de Agentes Autónomos de Inteligencia Artificial (Google Antigravity SDK)**.*
> 
> *Durante el proyecto, utilizamos agentes especializados para la auditoría heurística de interfaz, el formateo sintáctico y la generación de casos límite en pruebas de integración. Esto demostró que el rol del ingeniero contemporáneo ha evolucionado: ya no consiste en escribir líneas de código mecánicas y repetitivas, sino en actuar como un **arquitecto y orquestador de sistemas**, responsable de definir el modelo conceptual, formular las restricciones de negocio, diseñar las salvaguardas de concurrencia y validar rigurosamente la calidad del software.*
> 
> *Como balance final, Reposa+ cumple al 100% los objetivos planteados: un sistema de comercio electrónico robusto, transaccionalmente seguro, con checkout híbrido para invitados y usuarios registrados, logística de transporte integrada y una cobertura de pruebas integral certificada en integración continua.*
> 
> *Como líneas de trabajo futuro, contemplamos la incorporación de pagos recurrentes para suscripciones de descanso y la implementación de modelos de aprendizaje automático para predecir la demanda de reposición de inventario.*
> 
> *Quedo a la entera disposición de los miembros del tribunal para responder a cuantas preguntas u observaciones deseen formular. Muchas gracias por su atención."*

---

## 3. Pautas Clave para el Orador Durante la Defensa

1. **Contacto Visual y Postura:** Mantener la mirada alternando entre los tres miembros del tribunal. No leer textualmente las diapositivas; usarlas únicamente como anclaje conceptual.
2. **Control del Cronómetro:** Colocar un reloj o cronómetro visible sobre el atril. A los 6 minutos exactos debe iniciarse la demostración en vivo, y a los 10 minutos debe concluirse para dar paso a la sección de calidad y CI/CD.
3. **Manejo de la Demo en Vivo:** Mantener el cursor pausado y seguro. No hacer clics frenéticos. Explicar qué se va a hacer *antes* de pulsar el botón.
4. **Seguridad Dialéctica en Preguntas:** Si el tribunal interrumpe o formula una objeción, escuchar con atención plena, agradecer la pregunta y responder basándose en hechos verificables del código, la memoria del proyecto o la suite de pruebas.
