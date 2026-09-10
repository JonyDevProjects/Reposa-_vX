# Roadmap: Pipeline CI/CD, Ciclo de Releases (v1.0.0 & v1.1.0 UI/UX), GitFlow y Defensa Académica del TFG — Reposa+

## Contexto y Visión Estratégica

Habiéndose completado con éxito la consolidación de la rama `feature/guest-checkout-and-shipping` hacia `develop`, la certificación de la pirámide de testing tripartita (**119 pruebas automatizadas en <12s**) y la formalización epistemológica del **Capítulo 6** de la [Memoria Oficial del TFG](../Memoria_Proyecto.md), el proyecto **Reposa+** encara su fase culminante hacia la entrega y defensa académica.

Este roadmap organiza la secuencia técnica para automatizar la integración continua en la nube (GitHub Actions), articular el ciclo de promociones de versión bajo GitFlow integrando la **Propuesta 1** (separando el hito de core transaccional `v1.0.0` del ciclo de refinamiento estético UI/UX `v1.1.0`) y confeccionar los materiales de soporte para la defensa ante el tribunal universitario.

---

## Metadatos del Documento

* **Fecha de Formalización:** 10 de septiembre de 2026
* **Autor:** Jonathan Quispe
* **Rama Base de Trabajo:** `develop`
* **Entorno de Verificación:** Docker Engine (`reposaplus_app`, `reposaplus_mysql`, `reposaplus_redis`) + Playwright en Host
* **Documentos de Referencia:**
  * [`docs/Memoria_Proyecto.md`](../Memoria_Proyecto.md) (Capítulo 6: Calidad, Arquitectura y Pruebas del Software).
  * [`docs/Manual_Desarrollador_CICD.md`](../Manual_Desarrollador_CICD.md) (Guía de entornos y arquitectura de contenedores).
  * [`docs/progreso/roadmap-estrategia-testing-y-pruebas-unitarias.md`](./roadmap-estrategia-testing-y-pruebas-unitarias.md) (Certificación de suites y matriz ROI).
  * [`docs/handoff-sesion-2026-09-10-consolidacion-develop.md`](../handoff-sesion-2026-09-10-consolidacion-develop.md) (Acta de relevo de consolidación).

---

## Diagrama de Ciclo de Ramas, Estabilización y Releases (GitFlow Canónico)

```text
develop ────────┬───────────────────────────────────┬──────────────┬──────────────> (desarrollo futuro)
                │                                   │              ▲
                ▼ (Merge base v1.0.0)               │ (Ajustes UI) │ (Back-merge)
              ┌───────────────────────────┐         │              │
              │ Release v1.0.0: Core      │         ▼              │
              │ Transaccional + 119 tests │   release/v1.1.0 ──────┘
              └─────────────┬─────────────┘   (Refinamiento UI/UX
                            │                  Storefront + Admin)
                            ▼ (Merge --no-ff)       │
main ───────────────────────┴───────────────────────┴─────────────────────────────>
                       Tag: v1.0.0             Tag: v1.1.0-tfg-final
                    (Core Certificado)          (Entrega Definitiva TFG)
```

---

## Matriz de Fases del Roadmap

| Fase | Denominación / Objetivo | Entregables Clave | Estado |
|:---:|---|---|:---:|
| **1** | **Verificación del Repositorio y Estado de Integración** | Chequeo de árbol limpio en `develop` y comprobación rápida de salud de suites Unit y Feature en contenedor. | ⏳ Planificada |
| **2** | **Automatización CI/CD en GitHub Actions** | Creación y ajuste de `.github/workflows/ci.yml` ejecutando secuencialmente Pint, Unit tests, Feature tests (MySQL+Redis), Vite build y Playwright E2E. | ⏳ Planificada |
| **3** | **Consolidación del Hito Base v1.0.0 (Core Transaccional)** | Etiquetado semántico inicial `v1.0.0` en `develop`/`main` documentando el blindaje del checkout, paquetería y testing tripartito. | ⏳ Planificada |
| **4** | **Ciclo de Refinamiento UI/UX — Tienda y Admin (Propuesta 1)** | Apertura de rama `release/v1.1.0`, implementación de mejoras de microinteracciones, ergonomía visual, tablas admin y certificación de no-regresión. | ⏳ Planificada |
| **5** | **Promoción de Release Final v1.1.0 a `main` (GitFlow)** | Fusión `--no-ff` a `main`, etiquetado oficial `v1.1.0-tfg-final` y back-merge hacia `develop`. | ⏳ Planificada |
| **6** | **Preparación del Material de Soporte para la Defensa del TFG** | Confección de `docs/defensa-tfg/` con guion temporalizado (15 min), catálogo de diapositivas y argumentario defensivo. | ⏳ Planificada |

---

## Fase 1: Verificación del Repositorio y Estado de Integración

### 1.1 Objetivos de la Fase
Garantizar que el entorno local y la rama de trabajo `develop` se encuentran en un estado inmaculado antes de iniciar la configuración de los pipelines de integración continua o la creación de nuevas ramas de release.

### 1.2 Tareas Específicas
1. **Inspección de Git:**
   - Comprobar que no existan modificaciones no rastreadas (*untracked*) ni cambios pendientes en el árbol de trabajo (`git status`).
   - Confirmar que la rama actual es `develop` y que incluye los commits consolidados `cf0ed46` (merge de feature) y `9cbb281` (memoria del TFG).
2. **Health-Check en Contenedores Docker:**
   - Verificar la salud de los 7 contenedores del stack (`docker ps`).
   - Ejecutar la comprobación rápida de las suites PHPUnit dentro del contenedor de aplicación:
     ```bash
     docker exec reposaplus_app php artisan test --testsuite=Unit
     docker exec reposaplus_app php artisan test --testsuite=Feature
     ```
   - *Criterio de Aceptación:* 22 Unit tests en <0.1s (0 fallos) y 89 Feature tests en <2.5s (0 fallos).

---

## Fase 2: Automatización del Pipeline CI/CD en GitHub Actions (`.github/workflows/ci.yml`)

### 2.1 Objetivos de la Fase
Diseñar, implementar y certificar el flujo de trabajo automatizado de GitHub Actions en el repositorio remoto, reproduciendo con exactitud la pirámide de testing tripartita sobre la infraestructura en la nube de GitHub.

### 2.2 Arquitectura del Workflow
El archivo de pipeline [`.github/workflows/ci.yml`](file:///Users/jonathanquishpe/JoniDev/Reposa+_TFG/.github/workflows/ci.yml) se estructurará con servicios desacoplados y ejecución por etapas:

```text
Trigger: push/PR (develop, main)
   │
   ├── Job 1: Linting & Static Analysis (PHP Pint / Syntax)
   │     └── ./vendor/bin/pint --test
   │
   ├── Job 2: Unit Tests (Pure Memory Runner)
   │     └── php artisan test --testsuite=Unit
   │
   ├── Job 3: Feature Tests (Integration with MySQL 8.0 + Redis)
   │     ├── Services: mysql:8.0, redis:7-alpine
   │     ├── Migraciones automáticas: php artisan migrate:fresh --seed
   │     └── php artisan test --testsuite=Feature
   │
   ├── Job 4: Frontend Assets Build
   │     └── npm ci && npm run build
   │
   └── Job 5: End-to-End System Tests (Playwright Chromium)
         ├── npx playwright install --with-deps chromium
         ├── Background server: php artisan serve &
         └── npx playwright test
```

### 2.3 Especificación Técnica de Servicios en GitHub Actions
* **Servicio MySQL:** Contenedor `mysql:8.0` con variables `MYSQL_DATABASE=reposaplus_test`, `MYSQL_ROOT_PASSWORD=secret`, healthcheck activo sobre puerto `3306`.
* **Servicio Redis:** Contenedor `redis:7-alpine` sobre puerto `6379`.
* **Runner Environment:** `ubuntu-latest` con PHP 8.4 (o 8.3 con extensiones `pdo_mysql`, `mbstring`, `bcmath`, `gd`, `intl`, `redis`).
* **Matriz de Secretos:** Configuración de secretos de prueba para Stripe y Google OAuth en variables del runner (evitando llamadas reales a APIs externas en tests unitarios/features).

---

## Fase 3: Hito de Release Base v1.0.0 (Core Transaccional y Testing Tripartito)

### 3.1 Objetivos de la Fase
Establecer un hito formal de versión semántica que congele y certifique el núcleo funcional y transaccional alcanzado hasta la fecha.

### 3.2 Alcance Funcional de la Release v1.0.0
* **Motor de Checkout Híbrido:** Soporte dual para compras de invitados (`guest_token`) y usuarios registrados.
* **Paquetería Estándar:** Algoritmo determinista de tarifas (umbral gratis $\ge 50€$), identificadores `RPX2026...ES` y albarán térmico A6 con código de barras Code 128.
* **Autenticación Federada:** Google OAuth 2.0 con onboarding de dirección obligatoria y fusión de carrito.
* **Pasarela de Pago Segura:** Stripe Checkout con webhooks asíncronos y soporte de proxies inversos.
* **Pirámide Tripartita Certificada:** 119 pruebas automatizadas (22 Unit + 89 Feature + 8 Playwright E2E) con 100% de éxito.

### 3.3 Protocolo GitFlow de Tagging
```bash
git tag -a v1.0.0 -m "Release v1.0.0: Core Transaccional, Guest Checkout, Paquetería Estándar y Testing Tripartito"
git push origin v1.0.0
```

---

## Fase 4: Ciclo de Refinamiento UI/UX — Tienda y Admin (Hacia la Release v1.1.0)

### 4.1 Fundamentación de la Propuesta 1
Siguiendo las mejores prácticas de la disciplina, las mejoras estéticas y de ergonomía visual no deben mezclarse con las refactorizaciones de lógica de negocio o transaccionales. La rama de estabilización `release/v1.1.0` se abrirá específicamente para alojar estos ajustes sin alterar el modelo de dominio ni las reglas financieras.

```bash
# Apertura de la rama de estabilización según GitFlow
git checkout -b release/v1.1.0 develop
```

### 4.2 Catálogo de Ajustes UI/UX en el Storefront (Lado Usuario)
1. **Feedback Háptico y Visual en Carrito y Checkout:**
   - Refinamiento de microinteracciones en los botones de "Añadir al carrito" y "Comprar ahora" (animación sutil de confirmación visual y prevención de doble clic).
   - Clarificación visual de la tarjeta de selección de método de envío en `/checkout`, destacando de forma más prominente el badge de "Envío Gratuito" al superar los 50.00€.
2. **Ergonomía de Formularios y Estados de Carga:**
   - Mejoras en los mensajes de validación inline para direcciones postales, asegurando contraste legible según normativa WCAG 2.1 AA.
   - Skeletons de carga suaves en la transición entre la selección de método de pago y la redirección a Stripe Checkout.
3. **Pulido Visual del Onboarding:**
   - Afinado del diseño de la pantalla `/onboarding/shipping` para usuarios de Google OAuth, reduciendo el ruido visual y guiando el foco hacia el botón de confirmación.

### 4.3 Catálogo de Ajustes UI/UX en el Panel de Administración (Lado Admin)
1. **Densidad Operativa en `/admin/orders`:**
   - Reorganización de columnas en la tabla de pedidos para facilitar la lectura en monitores de alta resolución (priorización visual de ID, Cliente, Importe, Estado Logístico y Tracking).
   - Acceso directo con un clic a la impresión de la etiqueta térmica A6 (10x15cm) desde la propia fila de la tabla sin requerir entrar en el detalle.
2. **Feedback en Transición de Estados:**
   - Confirmación mediante modal no invasivo o toast sereno al avanzar el estado de un pedido (`processing` $\rightarrow$ `shipped` $\rightarrow$ `delivered`), evitando clics accidentales.
   - Filtros combinados rápidos por transportista (Correos Express) y rango de fechas.

### 4.4 Certificación de No-Regresión
Tras aplicar los ajustes en archivos Blade y Sass/CSS:
* Compilación obligatoria de assets: `npm run build`.
* Verificación de la suite de 119 pruebas en `develop` / `release/v1.1.0`:
  ```bash
  docker exec reposaplus_app php artisan test --testsuite=Unit
  docker exec reposaplus_app php artisan test --testsuite=Feature
  npx playwright test
  ```
* *Punto de Control Crítico:* Los selectores probados por Playwright E2E (`#cart-count`, `.badge`, formularios de login/checkout) deben conservarse intactos.

---

## Fase 5: Promoción de Release Final v1.1.0 a `main` (GitFlow Release)

### 5.1 Protocolo de Fusión y Etiquetado Oficial
Una vez homologados y testeados los ajustes de UI/UX, se procede al cierre formal de la release hacia producción:

```bash
# 1. Cambiar a la rama de producción
git checkout main
git pull origin main

# 2. Fusionar la rama de release con preservación de grafo
git merge --no-ff release/v1.1.0 -m "Merge branch 'release/v1.1.0' into main — Entrega Oficial TFG"

# 3. Etiquetar la versión semántica definitiva
git tag -a v1.1.0 -m "Release v1.1.0: Refinamiento Heurístico UI/UX, Alta Densidad Admin y Entrega Oficial del TFG"
git push origin main --tags

# 4. Sincronizar de vuelta (back-merge) hacia develop
git checkout develop
git merge --no-ff release/v1.1.0 -m "Merge branch 'release/v1.1.0' into develop"
git push origin develop

# 5. Eliminar la rama de release temporal
git branch -d release/v1.1.0
```

---

## Fase 6: Preparación del Material de Soporte para la Defensa del TFG

### 6.1 Estructura del Directorio de Defensa (`docs/defensa-tfg/`)
Se creará un directorio dedicado con toda la documentación estratégica para afrontar la exposición oral ante el tribunal evaluador:

```text
docs/defensa-tfg/
├── guion-exposicion-15-minutos.md    (Cronograma y minutaje de la presentación oral)
├── estructura-diapositivas.md        (Guía de diseño de 12 slides de alto impacto)
├── guion-demostracion-en-vivo.md     (Paso a paso para la demo funcional de 4 minutos)
└── faq-tribunal-preguntas-clave.md   (Respuestas a preguntas inquisitivas del tribunal)
```

### 6.2 Minutaje de la Exposición Oral (15 Minutos Totales)
* **Minutos 00:00 - 02:30 | Introducción y Nicho de Negocio:**
  - Justificación de Reposa+ (*Sleep Tech & Ergonomics*).
  - El lema central: *"No vendemos almohadas, vendemos noches de descanso profundo"*.
  - Justificación de Laravel frente a CMS empaquetados.
* **Minutos 02:30 - 06:00 | Arquitectura del Sistema e Integridad Transaccional:**
  - Modelo Entidad-Relación, vistas SQL nativas y mitigación de problemas N+1.
  - Concurrencia de stock: demostración técnica de `lockForUpdate()` y transacciones ACID.
  - Ecosistema de pagos: Stripe Checkout asíncrono y webhooks con idempotencia.
* **Minutos 06:00 - 10:00 | Demostración en Vivo (*Live Demo*):**
  - Compra completa como invitado con selección de paquetería estándar y envío gratis $\ge 50€$.
  - Generación de `guest_token`, factura en PDF y conversión de cuenta en un clic (*Claim Account*).
  - Panel de administración: cambio de estado logístico e impresión de etiqueta térmica A6.
* **Minutos 10:00 - 13:00 | Estrategia de Calidad: El Trofeo de Pruebas frente a Cohn:**
  - Argumentación del Testing Trophy (Dodds/Fowler).
  - Desmitificación de la lentitud de la integración: 119 pruebas en <12s.
  - Demostración de la suite unitaria pura en microsegundos (22 tests en 0.06s).
* **Minutos 13:00 - 15:00 | Conclusiones y Ecosistema de Agentes de IA:**
  - Reflexión sobre el rol del ingeniero de software como orquestador de agentes de IA (Antigravity).
  - Cierre y apertura del turno de preguntas del tribunal.

---

## Registro de Cambios y Trazabilidad

| Fecha | Autor | Versión | Resumen de Cambios |
|---|---|:---:|---|
| **10/09/2026** | Jonathan Quispe | `v1.0.0` | Definición formal del roadmap de CI/CD, ciclo de releases bajo GitFlow (Propuesta 1: v1.0.0 core transaccional + v1.1.0 refinamiento UI/UX) y material de soporte para la defensa del TFG. |
