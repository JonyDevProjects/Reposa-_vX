---
marp: true
theme: default
paginate: true
header: 'Reposa+ — Defensa de Trabajo de Fin de Grado (UPO)'
footer: 'Jonathan Quispe — Grado en Ingeniería Informática en Sistemas de Información'
style: |
  section {
    background-color: #0f172a;
    color: #f8fafc;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    padding: 40px 60px;
  }
  h1 {
    color: #818cf8;
    font-size: 2.2em;
    margin-bottom: 0.2em;
  }
  h2 {
    color: #38bdf8;
    font-size: 1.5em;
    border-bottom: 2px solid #334155;
    padding-bottom: 8px;
    margin-top: 0;
  }
  h3 {
    color: #a5b4fc;
    font-size: 1.15em;
  }
  p, li {
    font-size: 0.92em;
    line-height: 1.45;
    color: #cbd5e1;
  }
  strong {
    color: #ffffff;
  }
  table {
    font-size: 0.78em;
    border-collapse: collapse;
    width: 100%;
    margin: 15px 0;
  }
  th {
    background-color: #1e293b;
    color: #38bdf8;
    padding: 8px 12px;
    border: 1px solid #334155;
  }
  td {
    padding: 7px 12px;
    border: 1px solid #334155;
    background-color: #0f172a;
  }
  .badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 9999px;
    font-size: 0.75em;
    font-weight: bold;
    background: #4338ca;
    color: #e0e7ff;
  }
  .badge-success {
    background: #065f46;
    color: #6ee7b7;
  }
  .columns {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
  }
  .card {
    background-color: #1e293b;
    border: 1px solid #334155;
    border-radius: 8px;
    padding: 16px;
  }
  pre {
    background-color: #020617;
    border: 1px solid #334155;
    border-radius: 6px;
    padding: 10px;
    font-size: 0.72em;
  }
  code {
    color: #38bdf8;
  }
---

<!-- Slide 1: Portada -->
<!-- _header: '' -->
<!-- _footer: '' -->

# Reposa+
### Plataforma de Comercio Electrónico Transaccional de Alto Rendimiento Especializada en Descanso Ergonómico (*Sleep Tech*)

<br>

<div class="card">

**Defensa de Trabajo de Fin de Grado**  
* **Alumno:** Jonathan Quispe  
* **Titulación:** Grado en Ingeniería Informática en Sistemas de Información  
* **Tutor Académico:** Rubén Pérez Chacón (Código TFG: `25-26-C13`)  
* **Institución:** Escuela Politécnica Superior — Universidad Pablo de Olavide (UPO), Sevilla  
* **Convocatoria:** Curso Académico 2025/2026  

</div>

<br>
<span class="badge">Stack: Laravel 11/12+</span> <span class="badge">PHP 8.4</span> <span class="badge">MySQL 8 InnoDB</span> <span class="badge">7 Microservicios Docker</span> <span class="badge badge-success">141 Tests Pest + 8 Playwright E2E</span>

---

<!-- Slide 2: Nicho de Negocio & Problema -->

## 2. Contexto y Problema de Partida: La Paradoja del Descanso Online

<div class="columns">
<div class="card">

### ⚠️ La Fricción del Mercado Tradicional
* **Crisis Silenciosa de Salud:** El 33% de la población adulta sufre patologías cervicales vinculadas a una deficiente higiene postural nocturna.
* **El E-Commerce Generalista Falla:** Portales como Amazon o tiendas de colchones tratan la almohada como un comodín secundario genérico.
* **Intangibilidad y Desconfianza:** Catálogos masivos sin asesoramiento biomecánico derivan en un **78% de abandono de carrito**.

</div>
<div class="card">

### 🌙 La Solución Reposa+ (*Sleep Tech*)
* **Propuesta de Valor:** *"No vendemos almohadas; vendemos noches de descanso profundo y reparador"*.
* **Asesor Anatómico en Tiempo Real:** Segmentación del catálogo según postura (lateral, supino, prono) y densidad.
* **Psicología Visual "Midnight Sanctuary":** Paleta índigo/pizarra de bajo contraste pensada para reducir el estrés visual.

</div>
</div>

<br>

> **Conclusión de Mercado:** Se precisa una plataforma hiper-especializada que combine el rigor biomédico del descanso con una experiencia transaccional sin fricción.

---

<!-- Slide 3: Objetivos y Justificación Técnica -->

## 3. Objetivos Estratégicos: ¿Por Qué Ingeniería a Medida frente a CMS?

<div class="columns">
<div class="card">

### 🎯 Objetivos de Ingeniería
1. **Modelado Relacional 3FN:** Relaciones 1:1 (`Profile`), 1:N (`Orders`) y N:M explícitas (`Categories`, `Favorites`).
2. **Checkout Híbrido:** Compra como invitado con `guest_token` y conversión de cuenta post-pago (*Claim Account*).
3. **Logística Integrada:** Tarifas deterministas (envío gratis $\ge 50$ €) y albaranes térmicos A6 (`RPX...ES`).
4. **Resiliencia Transaccional:** Concurrencia ACID y webhooks idempotentes con firma HMAC.

</div>
<div class="card">

### ⚖️ Descarte Técnico de CMS (WooCommerce / Shopify)
* **Vulnerabilidades y Seguridad:** Eliminación total de plugins de terceros no auditados en PHP.
* **Rendimiento Puro:** Ausencia de *bloatware*; control milimétrico del **TTFB (<100 ms)**.
* **Integridad Transaccional Real:** Un CMS empaquetado no ofrece bloqueos pesimistas a nivel de fila (`SELECT ... FOR UPDATE`) ante avalanchas de tráfico.

</div>
</div>

---

<!-- Slide 4: Arquitectura Software y Docker -->

## 4. Arquitectura del Sistema: 7 Microservicios Docker y Modelo E-R

<div class="columns">
<div>

### 🐳 Ecosistema de 7 Contenedores
* **`reposaplus_lb`:** Nginx Proxy Inverso y Balanceador (HTTP 80/8000).
* **`reposaplus_app`:** PHP 8.4-FPM + Supervisor + Nginx.
* **`reposaplus_queue`:** Worker de colas asíncronas para correos.
* **`reposaplus_mysql`:** MySQL 8.0 motor InnoDB transaccional.
* **`reposaplus_redis`:** Caché y broker de colas Redis 7.
* **`reposaplus_mailhog`:** SMTP Sandbox en desarrollo (8025).
* **`reposaplus_minio`:** Almacenamiento S3 para facturas/fotos.

</div>
<div>

### 🗄️ Modelo Relacional y Vistas SQL
* **Tablas Maestras:** `users`, `profiles`, `addresses`, `products`, `orders`, `order_items`, `shipments`, `refunds`.
* **Tablas Pivote N:M:** `category_product`, `favorite_product`.
* **Vistas SQL Nativas:**
  * `v_order_summary`: Métricas de ventas diarias en sub-milisegundos.
  * `v_top_favorited_products`: Detección temprana de demanda.

</div>
</div>

---

<!-- Slide 5: Integridad Transaccional y Concurrencia -->

## 5. Blindaje contra Condiciones de Carrera y Pasarela Stripe

### 🔒 Bloqueo Pesimista en Inventario (`lockForUpdate`)
* **Problema:** Dos compradores concurrentes compitiendo por la última almohada en stock.
* **Solución de Ingeniería en Laravel:**

```php
DB::transaction(function () use ($productId, $quantity) {
    $product = Product::where('id', $productId)->lockForUpdate()->first();
    if ($product->stock < $quantity) {
        throw new InsufficientStockException("Stock insuficiente");
    }
    $product->decrement('stock', $quantity);
});
```

### 💳 Stripe Checkout Asíncrono e Idempotente
* **Seguridad PCI-DSS:** El cliente es redirigido a una sesión protegida de Stripe; el servidor jamás toca tarjetas.
* **Webhooks con Firma HMAC-SHA256:** Sincronización asíncrona verificando el `webhook_secret`.
* **Idempotencia Estricta:** El `payment_intent_id` descarta retransmisiones duplicadas sin alterar stock dos veces.

---

<!-- Slide 6: Innovación UX/UI -->

## 6. Innovación UX/UI: "The Midnight Sanctuary" y Reactividad

<div class="columns">
<div class="card">

### 🎨 Dirección de Diseño Emocional
* **Paleta Nocturna Índigo:** Azules profundos (#0f172a, #6366f1) que evocan serenidad y descanso.
* **Divulgación Progresiva:** El Asesor Anatómico se despliega bajo demanda; los productos son visibles *above-the-fold* de inmediato.
* **Componentes Blade Reutilizables:** Modulares y limpios, maquetados con Bootstrap 5 y SCSS.

</div>
<div class="card">

### ⚡ Reactividad Frontend y Resiliencia
* **Motor `CartCalculator`:** Peticiones AJAX debounced (300 ms) que recalculan subtotal, IVA (21%) y umbral de envío gratis sin refrescar pantalla.
* **Neutralización del BFCache:** Al pulsar "Atrás" tras cancelar en Stripe, el evento `pageshow` restaura los formularios y desbloquea botones.
* **Internacionalización (i18n):** Cambio dinámico bilingüe (ES / EN) persistido en sesión.

</div>
</div>

---

<!-- Slide 7: Live Demo Transition -->
<!-- _header: '' -->
<!-- _footer: '' -->

# 🚀 7. Demostración Práctica en Vivo (4 Minutos)

<br>

<div class="card" style="text-align: center; padding: 30px;">

### Ruta de la Demostración Transaccional sobre Entorno Docker Local
<br>

**1. Storefront:** Búsqueda en catálogo $\rightarrow$ Asesor de Postura $\rightarrow$ Carrito Reactivo AJAX  
⬇  
**2. Checkout:** Compra libre como invitado $\rightarrow$ Dirección postal $\rightarrow$ Simulación de Pago  
⬇  
**3. Post-Venta:** Confirmación con `guest_token` $\rightarrow$ Factura en PDF $\rightarrow$ Conversión *Claim Account*  
⬇  
**4. Back-Office:** `/admin/orders` $\rightarrow$ Albarán térmico A6 con Code 128 $\rightarrow$ Reembolso y reposición de stock  

</div>

<br>
<p style="text-align: center; color: #94a3b8;"><em>[Paso a proyectar el entorno local en Docker: http://localhost:8000]</em></p>

---

<!-- Slide 8: Logística y Back-Office -->

## 8. Arquitectura Logística y Panel de Alta Densidad Operativa

<div class="columns">
<div class="card">

### 📦 Motor Logístico Determinista
* **Tarifas Claras:** Tarifa estándar de 4,95 € con **bonificación al 100% (0,00 €)** en cestas $\ge 50,00$ €.
* **Códigos Normalizados:** Formato `/^RPX\d{4}\d{6}ES$/` para trazabilidad postal nacional.
* **Sincronización Bidireccional:** Avanzar el paquete a `in_transit` actualiza automáticamente el pedido a `shipped`.
* **Albarán Térmico A6 (10x15 cm):** Código de barras **Code 128** generado vectorialmente para almacén.

</div>
<div class="card">

### 📊 Back-Office Administrativo
* **Control RBAC:** Acceso restringido por middleware `admin` (código HTTP 403 ante intrusiones).
* **Gestión Universal de Reembolsos:** Soporte tanto para reembolsos de Stripe vía API como para pagos directos con restitución de inventario.
* **Vistas SQL Nativas:** `v_order_summary` alimentando el cuadro de mando sin penalizar la CPU.

</div>
</div>

---

<!-- Slide 9: Testing Trophy -->

## 9. Estrategia de Calidad: El Trofeo de Pruebas frente a Cohn

<div class="columns">
<div class="card">

### 🏛️ Epistemología: Dodds/Fowler vs Cohn (2009)
* **El Mito de la Pirámide Clásica:** Concebida cuando levantar bases de datos tomaba minutos. Los mocks masivos en transacciones ocultan fallos de claves foráneas y sobreventas.
* **Testing Trophy en Reposa+:** El mayor retorno de inversión (**ROI**) reside en la **Integración** contra instancias reales de MySQL 8 y Redis en Docker.

</div>
<div class="card">

### 🏆 Métricas Empíricas de la Suite en Pest
* **29 Tests Unitarios Puros (0.09s):** Autómata de estados finitos `Order` y motor de tarifas logísticas en memoria.
* **112 Tests de Integración (2.80s):** Bloqueo pesimista, sesiones, OAuth y webhooks.
* **8 Tests Playwright E2E (10.3s):** Pruebas de sistema sobre Chromium real.
* **TOTAL:** **149 pruebas automatizadas (679 aserciones) en <14 segundos**.

</div>
</div>

---

<!-- Slide 10: CI/CD Pipeline -->

## 10. Automatización CI/CD: 5 Jobs en GitHub Actions

```text
       ┌─────────────────────────────────────────────────────────────┐
       │             PIPELINE CI/CD (GitHub Actions)                 │
       └──────────────────────────────┬──────────────────────────────┘
                                      │
        ┌───────────────┬─────────────┴─┬───────────────┬───────────────┐
        ▼               ▼               ▼               ▼               ▼
   [Job 1: Lint]  [Job 2: Unit]  [Job 3: Feature]  [Job 4: Build]  [Job 5: E2E]
   Laravel Pint    Pest en mem.   MySQL 8 + Redis   Vite Assets     Playwright
   PSR-12 (135 f)  0 dependencias  Base datos real   Minificado      Chromium Real
```

* **Puertas de Calidad Automatizadas (*Quality Gates*):** Ningún código se integra en `develop` ni en `main` sin superar los 5 jobs.
* **Aislamiento Total:** Base de datos `reposaplus_testing` recreada y migrada en cada ciclo de integración.
* **Resultados en la Nube:** Pipeline verde garantizado en cada pull request y release oficial.

---

<!-- Slide 11: Ecosistema de Agentes de IA -->

## 11. Metodología: El Ingeniero como Orquestador de Agentes de IA

<div class="columns">
<div class="card">

### 🤖 Google Antigravity SDK & Spec-Driven Development
* **Superación del "Chatbot Reactivo":** No se utiliza la IA como un oráculo de autocompletado, sino como una **red de agentes autónomos**.
* **El Rol del Ingeniero:** Arquitecto de Software y Tech Lead:
  * Redacción previa de especificaciones técnicas no ambiguas (Roadmaps).
  * Gobernanza mediante reglas estrictas (`agent_rules`) y skills especializados (`laravel-specialist`).
  * Validación empírica obligatoria mediante pruebas automáticas.

</div>
<div class="card">

### 📈 Impacto en el Proyecto
* **Productividad Cuadruplicada:** Automatización de tareas mecánicas (scaffolding, seeders masivos, traducción de vistas Blade).
* **Cero Deuda Técnica:** Refactorización inmediata bajo directrices PSR-12 y tipos estrictos de PHP 8.4.
* **Memoria Viva con Engram:** Registro persistente de decisiones arquitectónicas entre sesiones.

</div>
</div>

---

<!-- Slide 12: Conclusiones y Cierre -->
<!-- _footer: '' -->

## 12. Conclusiones y Trabajo Futuro

### 🎓 Balance Académico y Profesional
* **Desarrollo Integral a Medida:** Demostración de competencias completas de Ingeniería Informática (MVC, concurrencia, bases de datos relacionales, seguridad, frontend y DevOps).
* **Calidad Industrial Certificada:** Pirámide tripartita (149 tests), GitFlow riguroso y release `v1.1.0-tfg-final`.
* **Innovación Metodológica:** Metodología Tríada (Scrumban + Métrica v3 + SDD) con agentes de IA.

### 🔮 Líneas Futuras de Evolución
1. **Suscripciones de Descanso Recurrentes:** Sustitución periódica de almohadas cada 18 meses con Stripe Billing.
2. **Machine Learning en Aprovisionamiento:** Algoritmos predictivos de rotura de stock según estacionalidad.
3. **Certificación de Accesibilidad WCAG 2.1 AAA:** Contraste dinámico y compatibilidad integral con lectores de pantalla.

<br>

<div style="text-align: center;">
<h3 style="color: #38bdf8;">¡Muchas gracias por su atención!</h3>
<p style="color: #94a3b8;">Quedo a la entera disposición del tribunal para el turno de preguntas y defensa técnica.</p>
</div>
