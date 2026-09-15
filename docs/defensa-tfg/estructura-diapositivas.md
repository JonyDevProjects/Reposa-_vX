# Estructura y Arquitectura de Diapositivas para la Defensa del TFG — Reposa+

**Proyecto:** Reposa+ — E-Commerce Transaccional *Sleep Tech*  
**Autor:** Jonathan Quispe  
**Objetivo del Documento:** Servir como guía canónica de diseño, disposición visual y jerarquía conceptual para la presentación de 12 diapositivas ante el tribunal evaluador.  
**Tiempo Total Estimado:** 15 minutos (11 minutos de presentación teórica + 4 minutos de demostración práctica intercalada).

---

## Mapa General de Diapositivas

```text
[1. Portada] ──> [2. Nicho & Problema] ──> [3. Objetivos & Alcance] ──> [4. Arquitectura Software & Datos]
                                                                                       │
[8. Logística & Back-Office] <── [7. Live Demo (4 min)] <── [6. Innovación UX/UI] <── [5. Integridad Transaccional & ACID]
       │
       ▼
[9. Calidad: Testing Trophy] ──> [10. Pipeline CI/CD] ──> [11. Ecosistema IA] ──> [12. Conclusiones & Cierre]
```

---

## Catálogo Detallado de las 12 Diapositivas

---

### Diapositiva 1: Portada y Apertura Institucional
* **Tiempo Sugerido:** 00:00 - 00:30 (30 seg)
* **Título Principal:** Reposa+
* **Subtítulo:** Plataforma de Comercio Electrónico Transaccional de Alto Rendimiento Especializada en Descanso Ergonómico y *Sleep Tech*
* **Metadatos:**
  * Alumno: Jonathan Quispe
  * Titulación: Grado en Ingeniería Informática
  * Convocatoria: Curso Académico 2025/2026
* **Disposición Visual:**
  * Fondo sereno en paleta *Midnight Slate* (#0f172a) con sutil resplandor índigo (#6366f1).
  * Logotipo de la universidad en la esquina superior derecha y logotipo vectorizado de Reposa+ centrado.
* **Mensaje Clave del Orador:** Saludo formal al tribunal, presentación personal y declaración de intenciones.

---

### Diapositiva 2: El Nicho de Negocio y la Paradoja del Descanso
* **Tiempo Sugerido:** 00:30 - 01:30 (1 min)
* **Título:** Contexto y Problema de Partida: La Paradoja del Descanso Online
* **Estructura en 2 Columnas Comparativas:**
  * **Columna Izquierda (La Fricción del Mercado):**
    * 33% de los adultos sufren patologías cervicales vinculadas a un mal apoyo nocturno.
    * Los comercios electrónicos tradicionales tratan la almohada como un comodín genérico.
    * Catálogos masivos sin asesoramiento biomecánico ni recomendaciones por postura.
  * **Columna Derecha (La Solución Reposa+):**
    * Lema: *"No vendemos almohadas; vendemos noches de descanso profundo"*.
    * Segmentación guiada según antropometría y hábitos de sueño (de lado, supino, prono).
    * Foco en ergonomía médica, materiales viscoelásticos y transpirabilidad probada.
* **Elemento Visual:** Gráfico de impacto mostrando el porcentaje de población con cervicalgia frente al abandono en carritos no especializados (78%).

---

### Diapositiva 3: Objetivos del TFG y Justificación Técnica de Laravel
* **Tiempo Sugerido:** 01:30 - 02:30 (1 min)
* **Título:** Objetivos Estratégicos: ¿Por Qué Ingeniería a Medida frente a un CMS?
* **Puntos Clave:**
  1. **Motor de Checkout Híbrido:** Flujo libre para invitados (*Guest Checkout*) sin fricción y conversión de cuenta post-pago (*Claim Account*).
  2. **Trazabilidad Logística Integrada:** Paquetería estándar con etiquetas térmicas A6 y códigos `RPX...ES`.
  3. **Descarte Técnico de CMS (WooCommerce / Shopify / Magento):**
     * **Seguridad y Vulnerabilidades:** Eliminación del riesgo de plugins de terceros no auditados.
     * **Rendimiento y Bloatware:** Ausencia de código espagueti; control milimétrico sobre el tiempo de respuesta TTFB (<100ms).
     * **Integridad Transaccional:** Necesidad de transacciones ACID y bloqueos pesimistas imposibles de garantizar en bases de datos genéricas de CMS.
* **Elemento Visual:** Matriz comparativa "CMS Empaquetado vs. Arquitectura Laravel a Medida".

---

### Diapositiva 4: Arquitectura de Software y Modelo de Dominio
* **Tiempo Sugerido:** 02:30 - 03:45 (1 min 15 seg)
* **Título:** Arquitectura del Sistema, Microservicios Docker y Modelo E-R
* **Contenido Técnico:**
  * **Ecosistema de 7 Contenedores Docker:** `reposaplus_app` (PHP 8.4-FPM), `reposaplus_nginx`, `reposaplus_mysql` (8.0), `reposaplus_redis` (7), `reposaplus_mailhog`, `reposaplus_vite` y balanceador Nginx.
  * **Modelo Entidad-Relación:**
    * Tablas pivote y normalización en 3FN.
    * Campos multilingües JSON nativos con `spatie/laravel-translatable`.
    * Máquina de estados finitos en `orders` (`pending` $\rightarrow$ `processing` $\rightarrow$ `shipped` $\rightarrow$ `completed` / `refunded`).
  * **Vistas SQL Nativas:** `v_order_summary` y `v_top_favorited_products` para analítica administrativa en sub-milisegundos, mitigando el problema N+1 mediante *Eager Loading* selectivo.
* **Elemento Visual:** Diagrama de bloques de la arquitectura Docker interconectada y fragmento del diagrama Entidad-Relación destacando la entidad `Shipment` y la vista SQL.

---

### Diapositiva 5: Integridad Transaccional, Bloqueo Pesimista y Stripe Asíncrono
* **Tiempo Sugerido:** 03:45 - 05:00 (1 min 15 seg)
* **Título:** Blindaje contra Condiciones de Carrera y Pasarela Stripe
* **Núcleo Técnico:**
  * **El Problema de la Sobreventa (*Race Conditions*):** Dos compras concurrentes sobre la última unidad en inventario.
  * **Solución de Reposa+:** Bloqueo Pesimista en MySQL InnoDB:
    ```php
    DB::transaction(function () use ($productId, $quantity) {
        $product = Product::where('id', $productId)->lockForUpdate()->first();
        if ($product->stock < $quantity) {
            throw new InsufficientStockException();
        }
        $product->decrement('stock', $quantity);
    });
    ```
  * **Stripe Checkout Asíncrono e Idempotente:**
    * Webhook `checkout.session.completed` validado con firma HMAC-SHA256 (`webhook_secret`).
    * Idempotencia estricta en base de datos: si Stripe retransmite el evento, el `payment_intent_id` existente descarta la duplicidad sin alterar inventario.
* **Elemento Visual:** Diagrama de secuencia temporal ilustrando dos peticiones concurrentes donde `lockForUpdate()` pone en cola la segunda transacción.

---

### Diapositiva 6: Innovación UX/UI: "The Midnight Sanctuary" y Reactividad
* **Tiempo Sugerido:** 05:00 - 06:00 (1 min)
* **Título:** Diseño de Experiencia: Psicología del Descanso y Resiliencia Frontend
* **Pilares de Interfaz:**
  1. **Identidad Visual "The Midnight Sanctuary":** Paleta índigo/pizarra de bajo contraste nocturno, tipografía armónica y componentes Blade reutilizables.
  2. **Divulgación Progresiva (*Progressive Disclosure*):** Asesor Anatómico colapsable por defecto; el producto es visible *above the fold* inmediatamente.
  3. **Motor Reactivo `CartCalculator`:** Peticiones AJAX debounced (300ms) que recalculan subtotal, IVA (21%) y el umbral de envío gratis (50€) sin refresco de página.
  4. **Resiliencia ante BFCache y Cancelaciones:** Suscripción al evento `pageshow` restaurando botones bloqueados y preservación de datos en `sessionStorage`.
* **Elemento Visual:** Mockups de pantalla dividida: Desktop (diseño limpio con chips de categoría) y Mobile (conversión de tabla a tarjetas táctiles).

---

### Diapositiva 7: Demostración en Vivo (*Live Demo* — Transición Interactiva)
* **Tiempo Sugerido:** 06:00 - 10:00 (4 min)
* **Título:** Demostración Funcional en Vivo del Sistema Reposa+
* **Contenido de la Diapositiva (Pantalla de Espera / Ruta de Navegación):**
  * **Fase A (Storefront):** Búsqueda semántica $\rightarrow$ Asesor de postura $\rightarrow$ Carrito reactivo.
  * **Fase B (Checkout):** Compra como invitado $\rightarrow$ Pasarela Stripe en staging $\rightarrow$ BFCache test.
  * **Fase C (Post-Venta):** Token criptográfico $\rightarrow$ Factura en PDF $\rightarrow$ Claim Account.
  * **Fase D (Back-Office):** Gestión de pedidos $\rightarrow$ Etiqueta térmica A6 $\rightarrow$ Reembolso.
* **Nota del Orador:** *"Paso a proyectar el entorno local en Docker para ejecutar la demostración funcional en tiempo real."*

---

### Diapositiva 8: Arquitectura Logística y Back-Office de Alta Densidad
* **Tiempo Sugerido:** 10:00 - 10:30 (30 seg)
* **Título:** Logística de Paquetería Estándar y Panel Administrativo
* **Aspectos Destacados:**
  * Algoritmo determinista de gastos de envío: tarifa plana de 4,95€ con bonificación a 0,00€ en pedidos $\ge 50,00€$.
  * Generación automatizada de etiquetas térmicas de expedición en formato estándar A6 con código de barras Code 128 y prefijo `RPX2026...ES`.
  * Sincronización logística bidireccional: el avance de paquetería actualiza automáticamente el ciclo de vida del pedido.
  * Reembolsos universales auditados: soporte tanto para cancelaciones en Stripe como para compras directas con reposición automática de stock.
* **Elemento Visual:** Ejemplo real de etiqueta térmica A6 renderizada y captura del panel de pedidos `/admin/orders`.

---

### Diapositiva 9: Estrategia de Calidad: El Trofeo de Pruebas frente a Cohn
* **Tiempo Sugerido:** 10:30 - 11:45 (1 min 15 seg)
* **Título:** Arquitectura de Pruebas: El Testing Trophy (Dodds / Fowler)
* **Argumentación Epistemológica:**
  * **Crítica a la Pirámide de Cohn (2009):** Concebida cuando levantar bases de datos tomaba minutos. Los mocks masivos en e-commerce transaccional inducen una falsa sensación de seguridad.
  * **El Testing Trophy:** El mayor ROI radica en la **capa de integración**, probando contra instancias reales de MySQL 8.0 y Redis en Docker.
  * **Métricas de la Suite en Pest:**
    * **27 Unit Tests (0.08s):** Máquina de estados finitos, cálculo logístico y lógica pura en memoria.
    * **114 Feature Tests (3.20s):** Persistencia SQL, bloqueos pesimistas, sesiones y transacciones.
    * **Total:** 141 tests automatizados ejecutados en **<3.3 segundos** (100% de éxito).
    * **8 Playwright E2E Tests (11.5s):** Pruebas de sistema sobre navegadores reales.
* **Elemento Visual:** Gráfico comparativo entre la Pirámide de Cohn (pirámide invertida de coste/valor) y el Trofeo de Pruebas de Reposa+.

---

### Diapositiva 10: Automatización CI/CD en GitHub Actions
* **Tiempo Sugerido:** 11:45 - 12:45 (1 min)
* **Título:** Pipeline CI/CD: 5 Jobs Automatizados en la Nube
* **Estructura del Workflow (`.github/workflows/ci.yml`):**
  1. **Job 1 (Linting):** Laravel Pint verificando conformidad PSR-12 (135 archivos inspeccionados).
  2. **Job 2 (Unit Suite):** Pest ejecutándose en memoria pura con SQLite aislado.
  3. **Job 3 (Feature Suite):** Servicios orquestados de `mysql:8.0` y `redis:7-alpine`, migraciones y base de datos relacional real.
  4. **Job 4 (Frontend Build):** Vite compilando bundles de producción minificados.
  5. **Job 5 (Playwright E2E):** Instalación de navegadores Chromium y ejecución de tests de sistema con servidor en background controlado.
* **Elemento Visual:** Captura real del pipeline de GitHub Actions con los 5 jobs ejecutados con éxito en color verde.

---

### Diapositiva 11: Ecosistema de Agentes de IA: El Ingeniero como Orquestador
* **Tiempo Sugerido:** 12:45 - 13:45 (1 min)
* **Título:** Metodología: El Ingeniero como Orquestador de Agentes de IA
* **Reflexión sobre el Rol Profesional:**
  * Integración del **Google Antigravity SDK** en el flujo de desarrollo.
  * **Delegación Táctica vs. Dirección Arquitectónica:**
    * La IA ejecuta tareas mecánicas: refactorización sintáctica, generación de mocks de prueba, análisis estático y scaffolding.
    * El Ingeniero Humano preserva el control crítico: diseño del modelo de dominio, definición de salvaguardas transaccionales, auditoría de seguridad y formulación de casos de prueba límite.
  * **Resultados:** Incremento de la velocidad de entrega en un 40% sin comprometer en ningún momento la calidad del código ni la integridad conceptual.
* **Elemento Visual:** Diagrama conceptual mostrando al Ingeniero de Software en el centro orquestando agentes especializados (Subagentes de Testing, Refactoring y Documentación).

---

### Diapositiva 12: Conclusiones, Trabajo Futuro y Agradecimientos
* **Tiempo Sugerido:** 13:45 - 15:00 (1 min 15 seg)
* **Título:** Conclusiones y Futuro del Proyecto Reposa+
* **Balance de Metas Alcanzadas:**
  * Plataforma transaccional de *Sleep Tech* 100% funcional y certificada.
  * Modelo de testing moderno (Testing Trophy) con 141 tests automatizados y CI/CD en la nube.
  * GitFlow canónico culminado con la release oficial `v1.1.0-tfg-final`.
* **Trabajo Futuro:**
  * Pagos recurrentes y suscripciones de descanso (sustitución programada de almohadas cada 18 meses).
  * Algoritmos de Machine Learning en el panel de control para predicción de demanda de inventario.
  * Auditoría formal de accesibilidad WCAG 2.1 AAA.
* **Cierre:** *"Muchas gracias por su atención. Quedo a su disposición para el turno de preguntas."*
* **Elemento Visual:** Logotipo final de Reposa+, enlace al repositorio GitHub y datos de contacto profesional.
