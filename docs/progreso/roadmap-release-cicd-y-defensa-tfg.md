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
| **1** | **Verificación del Repositorio y Estado de Integración** | Chequeo de árbol limpio en `develop` y comprobación rápida de salud de suites Unit y Feature en contenedor. | ✅ Completada |
| **2** | **Automatización CI/CD en GitHub Actions** | Creación y ajuste de `.github/workflows/ci.yml` ejecutando secuencialmente Pint, Unit tests, Feature tests (MySQL+Redis), Vite build y Playwright E2E. | ✅ Completada |
| **3** | **Consolidación del Hito Base v1.0.0 (Core Transaccional)** | Etiquetado semántico oficial `v1.0.0` certificando el backend transaccional y la pirámide de 119 pruebas automatizadas. | ✅ Completada |
| **4.1** | **Estabilización Operativa Back-Office (Propuesta 1)** | Desviaciones D1 a D5 resueltas: logística bidireccional, comando `orders:reset-test-matrix`, PDF admin, fix error 500 Stripe y reembolsos directos (125 tests). | ✅ Completada |
| **4.2** | **Rediseño del Catálogo Storefront (Propuesta 1)** | Divulgación progresiva: Asesor Anatómico colapsado bajo demanda, píldoras de categoría, búsqueda tolerante/semántica y filtros despejados. | ⏳ Planificada (Próxima) |
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

## Fase 4: Ciclo de Estabilización y Refinamiento UI/UX — Tienda y Admin (Hacia la Release v1.1.0)

### 4.1 Contexto y Trazabilidad de la Iteración 4.1: Estabilización Operativa del Back-Office (Completada)

Durante la ejecución de las pruebas manuales e interactivas de la versión `release/v1.1.0`, surgieron desviaciones funcionales y de lógica transaccional en el panel de administración (`/admin/orders`) que trascendieron los meros retoques visuales. Se aplicó una iteración correctiva profunda para garantizar la integridad operativa:

| Desviación Detectada | Causa Raíz | Solución Implementada | Commit |
|---|---|---|:---:|
| **D1: Desincronización Logística** | El avance del transportista en paquetería no actualizaba simétricamente el estado del pedido, y pedidos cancelados o entregados podían ser avanzados erróneamente. | Sincronización bidireccional estricta en `AdminController`, guardia de estados terminales en `advanceShipment` y soporte de estado `cancelled`/`Anulada` en `Shipment`. | `bde86ba` |
| **D2: Inconsistencia en Datos de Prueba** | Falta de un entorno limpio para pruebas manuales repetibles tras múltiples transiciones. | Creación del comando Artisan `php artisan orders:reset-test-matrix` con 9 pedidos modelo (Stripe y Directos) cubriendo todo el ciclo de vida. | `5265e0e` |
| **D3: Bloqueo de Facturación Admin** | La descarga de PDF en `CartController::downloadInvoice` exigía que el pedido perteneciera al usuario autenticado, impidiendo a los administradores descargar facturas de clientes. | Incorporación del método `isAdmin(): bool` en `User` y autorización explícita para administradores en el controlador de descargas. | `5265e0e` |
| **D4: Error Fatal HTTP 500 en Stripe Refund** | Invocación de método inexistente `Cashier::stripe()->paymentIntents->refund(...)`. El fatal `\Error` de PHP escapaba del bloque `catch (\Exception $e)`. | Sustitución por `Cashier::stripe()->refunds->create(...)`, simulación en entorno de test local (`sk_test_`), y migración a `catch (\Throwable $e)`. | `61252fb` |
| **D5: Bloqueo de Reembolsos en Pedidos Directos** | El botón de reembolso en la columna Acciones exigía `payment_intent_id`, dejando a los pedidos directos (efectivo/sin pasarela) completados sin opción de devolución. | Universalización del botón `[Reembolsar]` en **Acciones** para cualquier pedido `Entregado` o `Completado` (Stripe o Directo), con reposición de inventario y registro contable. | `a68941c` |

* **Resultado de la Iteración 4.1:** La pirámide de pruebas creció de 119 a **125 pruebas en Pest** (+6 tests de ciclo de vida y reembolsos en `OrderShipmentLifecycleSyncTest` y `AdminTest`), certificando 0 regresiones con **8/8 pruebas E2E en Playwright** y 100% de cumplimiento en **Laravel Pint**.

---

### 4.2 Plan para la Iteración 4.2: Simplificación Cognitiva del Catálogo Storefront y Búsqueda Ágil (En Planificación)

#### A. Diagnóstico de Fricción Heurística en la Vista del Catálogo (`/catalog`)
Actualmente, la vista [`catalog/index.blade.php`](file:///Users/jonathanquishpe/JoniDev/Reposa+_TFG/Reposa+/resources/views/catalog/index.blade.php) presenta un nivel excesivo de **sobrecarga cognitiva** y fatiga de decisión para el usuario:
1. **Pérdida de foco del producto (*Above the Fold*):** El "Selector Anatómico / Asesor de Firmeza" (`firmness-guide.blade.php`, 350+ líneas) se carga expandido por defecto (`collapse show`), acaparando prácticamente todo el primer viewport útil y obligando al comprador a hacer scroll para ver la primera almohada.
2. **Parálisis por Análisis:** Entre el selector biomecánico de 3 pasos (postura, nivel 1-10 y diagnóstico), la barra lateral con 5 tipos de filtros (búsqueda, categorías, materiales, firmezas y rangos numéricos de precio) y el desplegable de ordenación en la cabecera, se produce una sensación de caos de configuración.
3. **Búsqueda Rígida:** La búsqueda actual realiza un `LIKE %q%` estricto en JSON de nombre y descripción. Si un usuario busca *"dormir de lado"*, *"cuello"* o *"almohada dura"*, no obtiene coincidencias si esas palabras exactas no están en el título.

#### B. Objetivos de Diseño y Arquitectura (Filosofía Impeccable: *Distill, Clarify & Layout*)
* **Principio Rector:** **Divulgación Progresiva (*Progressive Disclosure*)**. El producto debe ser el protagonista indiscutible. Un comprador que simplemente quiere explorar almohadas debe ver el catálogo de inmediato sin barreras.
* **Asesor Anatómico bajo Demanda:** El recomendador anatómico debe transformarse en una herramienta de asistencia inteligente **colapsada por defecto**, accesible mediante un llamador visualmente refinado y sereno (*"🧠 ¿Dudas sobre qué almohada necesitas? Descubre tu almohada ideal según tu postura"*).
* **Navegación Rápida por Píldoras Horizontales (*Category Chips*):** Extraer las categorías principales a una botonera horizontal ágil (`Todas`, `Viscoelásticas`, `Cervicales`, `Ergonómicas`, `Fibra`) debajo del título para filtrado instantáneo en 1 clic sin fricción lateral.
* **Búsqueda Prominente y Tolerante (Semántica / Multiatributo):**
  - Barra de búsqueda limpia con botón de borrado rápido (`clear`).
  - Extensión en `ProductController::index` para que busque no solo en nombre y descripción, sino también en atributos clave de descanso (postura recomendada, firmeza, materiales) permitiendo que términos como *"cervical"*, *"lado"*, *"suave"* o *"firme"* arrojen los productos óptimos.
* **Filtros Secundarios Despejados (*Offcanvas / Collapsible Drawer*):**
  - Mover los filtros secundarios (materiales, nivel numérico de firmeza, rango de precio) a un botón desplegable compacto `"Filtros"`, reduciendo el ruido visual lateral y maximizando el espacio para un grid de 3 o 4 columnas de productos en monitores estándar.
  - Indicación clara de filtros activos mediante chips descartables (*tags*) y botón directo de `"Limpiar filtros"`.

#### C. Matriz de Tareas de la Iteración 4.2
1. **Tarea 4.2.1 — Refactorización Blade de `/catalog`:**
   - Colapsar por defecto el Asesor Anatómico y rediseñar su tarjeta cabecera como un banner de valor no invasivo.
   - Implementar la barra superior unificada con buscador y píldoras horizontales de categoría.
   - Reestructurar el grid de productos para ocupar ancho completo o 9/12 con panel de filtros colapsable/offcanvas.
2. **Tarea 4.2.2 — Optimización de Búsqueda y Filtros en `ProductController`:**
   - Mejorar la query de búsqueda para contemplar coincidencias en campos JSON transducibles y atributos ergonómicos.
   - Asegurar que la paginación y ordenación se mantengan fluidas con `withQueryString()`.
3. **Tarea 4.2.3 — Verificación y Certificación:**
   - Revisión visual y táctil en viewport móvil y escritorio.
   - Ejecución de la suite completa de 125 pruebas en Pest y 8 pruebas en Playwright.
   - Verificación de formateo con Laravel Pint.

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
