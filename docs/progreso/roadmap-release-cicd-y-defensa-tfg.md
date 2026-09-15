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
| **2** | **Automatización CI/CD en GitHub Actions** | Creación y ajuste de `.github/workflows/ci.yml` ejecutando los 5 jobs especificados (Pint, Unit tests, Feature tests con MySQL 8 + Redis, Vite build y Playwright E2E). | ✅ Completada |
| **3** | **Consolidación del Hito Base v1.0.0 (Core Transaccional)** | Etiquetado semántico oficial `v1.0.0` certificando el backend transaccional y la pirámide de 119 pruebas automatizadas. | ✅ Completada |
| **4.1** | **Estabilización Operativa Back-Office (Propuesta 1)** | Desviaciones D1 a D5 resueltas: logística bidireccional, comando `orders:reset-test-matrix`, PDF admin, fix error 500 Stripe y reembolsos directos (125 tests). | ✅ Completada |
| **4.2** | **Rediseño del Catálogo Storefront (Propuesta 1)** | Divulgación progresiva: Asesor Anatómico colapsado bajo demanda, píldoras de categoría, búsqueda tolerante/semántica y filtros despejados. | ✅ Completada |
| **4.3** | **Refinamiento Reactivo y Resiliencia en Carrito y Checkout** | Cálculo en tiempo real con debounce y límites de stock, layout móvil, validación shake y scroll, persistencia en doble capa + BFCache, desglose fiscal transparente y 570/570 i18n (141 tests). | ✅ Completada |
| **5** | **Promoción de Release Final v1.1.0 a `main` (GitFlow)** | Fusión `--no-ff` a `main`, etiquetado oficial `v1.1.0-tfg-final` (y `v1.1.0`), back-merge hacia `develop` y limpieza de rama de release. | ✅ Completada |
| **6** | **Preparación del Material de Soporte para la Defensa del TFG** | Confección de `docs/defensa-tfg/` con guion temporalizado (15 min), catálogo de 12 diapositivas, guía de live demo (4 min) y FAQ para el tribunal. | ✅ Completada |

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

### 4.2 Trazabilidad de la Iteración 4.2: Simplificación Cognitiva del Catálogo Storefront y Búsqueda Ágil (Completada)

#### A. Diagnóstico de Fricción Heurística en la Vista del Catálogo (`/catalog`)
Originalmente, la vista [`catalog/index.blade.php`](file:///Users/jonathanquishpe/JoniDev/Reposa+_TFG/Reposa+/resources/views/catalog/index.blade.php) presentaba sobrecarga cognitiva y fatiga de decisión:
1. **Pérdida de foco del producto (*Above the Fold*):** El "Selector Anatómico / Asesor de Firmeza" (`firmness-guide.blade.php`, 350+ líneas) se cargaba expandido por defecto (`collapse show`), acaparando prácticamente todo el primer viewport útil y obligando al comprador a hacer scroll para ver la primera almohada.
2. **Parálisis por Análisis:** Competencia visual entre el selector biomecánico de 3 pasos, la barra lateral con 5 tipos de filtros y la dispersión de opciones.
3. **Búsqueda Rígida:** Coincidencia `LIKE %q%` estricta solo en nombre y descripción, fallando ante búsquedas de postura (*"dormir de lado"*), dolencias (*"cuello"*, *"cervical"*) o materiales (*"visco"*).

#### B. Soluciones Implementadas (Filosofía Impeccable: *Distill, Clarify & Layout*)
* **Divulgación Progresiva (*Progressive Disclosure*):** El Asesor Anatómico ahora está colapsado por defecto y se presenta como un banner de valor no invasivo (*"🧠 ¿Dudas sobre qué almohada necesitas? Descubre tu almohada ideal según tu postura"*), permitiendo que los productos sean visibles inmediatamente (*above the fold*).
* **Navegación Rápida por Píldoras Horizontales (*Category Chips* en 1 Clic):** Barra horizontal de chips interactivos (`Todas`, `Viscoelásticas`, `Cervicales`, `Ergonómicas`, `Fibra`, etc.) con estado activo/inactivo claro y sin recarga manual.
* **Búsqueda Prominente y Multiatributo Tolerante:**
  - Buscador visualmente prominente con botón de borrado rápido (`clear`).
  - Motor de búsqueda en `ProductController::index` enriquecido con mapeo semántico y postural (`lado`, `cuello`, `cervical`, `firme`, `suave`, `visco`, `bambú`), coincidencia en categorías, materiales y firmezas, y resolución insensible a mayúsculas mediante `LOWER(JSON_UNQUOTE(...))`.
* **Filtros Secundarios Despejados:**
  - Panel colapsable compacto con contador de filtros activos.
  - Tags/chips descartables individuales (`&times;`) para cada criterio activo y botón directo de `"Limpiar filtros"`.
  - Grid de productos a ancho completo (`col-12`) con distribución fluida de 3 a 4 columnas.

#### C. Certificación de Calidad y Resultados
* **Vite Assets:** Compilación exitosa (`npm run build`).
* **Pirámide de Pruebas Pest:** 125 pruebas automatizadas (22 Unit + 103 Feature, 566 aserciones) en 2.82s (100% verde, 0 regresiones).
* **Pruebas End-to-End Playwright:** 8/8 pruebas superadas en 11.1s.
* **Estilo de Código:** Laravel Pint 100% aprobado.

---

### 4.3 Trazabilidad de la Iteración 4.3: Refinamiento Reactivo y Resiliencia en Carrito y Checkout (Completada)

#### A. Diagnóstico de Experiencia de Usuario y Desafíos de Resiliencia
Tras la simplificación del catálogo en la Iteración 4.2, la auditoría heurística sobre el embudo final de conversión (Carrito y Pasarela de Checkout) identificó puntos críticos de fricción interactiva, fragilidad de sesión y pequeñas inconsistencias fiscales:
1. **Falta de Reactividad en Cantidades:** El ajuste de unidades en `/cart` requería recargas síncronas o carecía de debounce adaptativo, generando riesgos de desincronización de stock y saltos bruscos en el cálculo de gastos de envío (umbral de 50€).
2. **Degradación Visual en Dispositivos Móviles:** La estructura tabular clásica de `/cart` desbordaba horizontalmente en viewports estrechos (<380px) y el estado vacío carecía de centrado y proporciones ergonómicas.
3. **Pérdida de Contexto en Cancelaciones de Stripe y BFCache:** Al retroceder desde Stripe Checkout o navegar hacia atrás en el navegador, el mecanismo de *Back-Forward Cache* (BFCache) congelaba los botones de envío en estado deshabilitado (*loading*) y los datos de envío del invitado se disipaban en la vista.
4. **Validación Silenciosa o Rígida en Checkout:** La detección de errores en el formulario de envío no proporcionaba suficiente anclaje cognitivo visual, dificultando la identificación rápida de campos faltantes.
5. **Transparencia Fiscal y Cobertura de Internacionalización:** Discrepancias menores en la presentación de la base imponible frente al total con IVA en la confirmación `/orders/{id}`, junto a cadenas huérfanas sin traducir en el catálogo de idiomas.

#### B. Soluciones de Ingeniería e Interacción Implementadas

| Área Técnica | Desafío Abordado | Solución Implementada | Commits |
|---|---|---|:---:|
| **Motor de Cálculo y Reactividad** | Latencia y sobrecarga en actualización de líneas de carrito. | Creación del servicio desacoplado `CartCalculator`, controlador AJAX optimizado con debounce de 300ms, actualización reactiva del DOM (subtotal, IVA, selector de envío gratis y total) y validación estricta de límites de stock con badges informativos. | `2b9a3c9` |
| **Responsive Design en Carrito** | Desbordamiento horizontal en pantallas móviles estrechas. | Transformación CSS fluida de filas de tabla en tarjetas móviles (`card-layout`), centrado vertical/horizontal de estados vacíos sin margen negativo, reducción de paddings en contenedores y reubicación de acciones táctiles. | `63f8554`<br>`0342db1` |
| **Ergonomía de Conversión (CTAs)** | Ambigüedad en llamadas a la acción en el carrito. | Sustitución del texto del CTA principal por el imperativo claro *"Finalizar Pedido"* e incorporación de botón secundario simétrico *"Continuar Comprando"*. | `d091cfd` |
| **Validación Multinivel en Checkout** | Errores de validación inadvertidos o fuera de vista. | Incorporación de microinteracción con animación CSS `@keyframes shake` en el contenedor de errores, scroll suave adaptativo `scrollIntoView({ behavior: 'smooth', block: 'center' })` y autofoco accesible en el primer campo inválido. | `ebaea07` |
| **Persistencia Resiliente de Invitados** | Pérdida de dirección tras redirección o cancelación de Stripe. | Arquitectura de persistencia en doble capa: almacenamiento en sesión de servidor Laravel sincronizado con `sessionStorage` en el cliente, rellenado automático de campos de invitado y preservación total al abortar el pago. | `f3d6d9f`<br>`99fdd99` |
| **Neutralización del BFCache** | Botón de envío bloqueado y estado congelado al pulsar "Atrás". | Suscripción al evento nativo del navegador `window.addEventListener('pageshow', ...)` verificando `event.persisted`, restaurando inmediatamente el estado del botón de compra, limpiando spinners y recuperando los datos del formulario. | `85a7030` |
| **Desglose Fiscal Transparente** | Desalineación de conceptos en vista de confirmación `/orders/{id}`. | Homogeneización de la fórmula fiscal: Base Imponible + Cuota IVA (21%) + Coste de Envío = Total del Pedido, con badges de garantía y políticas post-venta debidamente tipificadas. | `11afb2c` |
| **Auditoría Exhaustiva de i18n** | Claves sin traducir en español e inglés detectadas en vistas clave. | Resolución y normalización de la totalidad de claves del proyecto (570/570 strings bilingües verificados), garantizando navegación 100% localizada en ES y EN sin placeholders crudos. | `822c365` |

#### C. Certificación de Calidad y Resultados
* **Pirámide de Pruebas Pest:** Crecimiento de la suite a **141 pruebas automatizadas** (27 Unit + 114 Feature, 679 aserciones) ejecutadas en **~3.3s** (100% superadas, 0 fallos).
* **Pruebas End-to-End Playwright:** **8/8 pruebas E2E** ejecutadas satisfactoriamente en entorno real (**11.5s**, 100% de éxito).
* **Compilación de Assets:** Vite compilando limpiamente bundles de producción (`npm run build`).
* **Internacionalización:** Cobertura del 100% (570/570 claves validadas).
* **Base de Conocimiento:** Registro de evidencias y decisiones arquitectónicas en Engram (`#308` a `#317` en `reposaplus-tfg`).

---

## Fase 5: Promoción de Release Final v1.1.0 a `main` (GitFlow Release)

### 5.1 Protocolo de Fusión, Etiquetado Oficial y Back-Merge
Concluido el ciclo de refinamiento UI/UX y generado el material de defensa, se procedió al cierre formal de la rama de release bajo el modelo GitFlow canónico:

```bash
# 1. Posicionarse en la rama de producción y sincronizar cambios
git checkout main

# 2. Fusionar la rama de release preservando el grafo explícito (--no-ff)
# (En caso de colisiones históricas con snapshots previos, se aplica la estrategia '-X theirs'
# manteniendo la versión certificada de 'release/v1.1.0' y resolviendo Reposa+/Dockerfile)
git merge --no-ff -X theirs release/v1.1.0 -m "Merge branch 'release/v1.1.0' into main — Entrega Oficial TFG"
git add Reposa+/Dockerfile
git commit -m "Merge branch 'release/v1.1.0' into main — Entrega Oficial TFG"

# 3. Etiquetar la versión semántica definitiva y el tag académico
git tag -a v1.1.0-tfg-final -m "Release v1.1.0-tfg-final: Entrega Definitiva del TFG Reposa+ con Pipeline CI/CD y Material de Defensa"
git tag -a v1.1.0 -m "Release v1.1.0: Refinamiento Heurístico UI/UX, Alta Densidad Admin y Entrega Oficial del TFG"

# 4. Sincronizar de retorno (back-merge) hacia la rama de desarrollo
git checkout develop
git merge --no-ff release/v1.1.0 -m "Merge branch 'release/v1.1.0' into develop"

# 5. Limpieza ordenada de la rama de release temporal
git branch -d release/v1.1.0
```

---

## Fase 6: Preparación del Material de Soporte para la Defensa del TFG

### 6.1 Estructura del Directorio de Defensa (`docs/defensa-tfg/`)
Se ha consolidado el directorio especializado [`docs/defensa-tfg/`](../defensa-tfg/) con 4 documentos estratégicos para la exposición ante el tribunal universitario:

1. **[`guion-exposicion-15-minutos.md`](../defensa-tfg/guion-exposicion-15-minutos.md):**
   - Cronograma y minutaje estricto distribuido en 5 bloques narrativos (Nicho, Arquitectura ACID, Live Demo, Testing Trophy y Agentes de IA).
   - Guion oral con transcripción recomendada para el orador y pautas de lenguaje corporal y control del tiempo.
2. **[`estructura-diapositivas.md`](../defensa-tfg/estructura-diapositivas.md):**
   - Arquitectura detallada de 12 diapositivas de alto impacto visual y conceptual.
   - Especificación de layouts, mockups en pantalla dividida, diagramas de secuencia transaccionales y notas del presentador.
3. **[`guion-demostracion-en-vivo.md`](../defensa-tfg/guion-demostracion-en-vivo.md):**
   - Protocolo pre-vuelo (*pre-flight checklist*) para dejar el stack Docker y los datos listos mediante `php artisan orders:reset-test-matrix`.
   - Paso a paso cronometrado de 4 minutos (catálogo con asesor anatómico, carrito reactivo con `CartCalculator`, checkout de invitado resiliente con neutralización de BFCache, factura PDF oficial y back-office con etiquetas térmicas A6).
   - Plan de contingencia (*Plan B*) con atajos y comandos de rescate de 5 segundos.
4. **[`faq-tribunal-preguntas-clave.md`](../defensa-tfg/faq-tribunal-preguntas-clave.md):**
   - Batería de 8 preguntas inquisitivas y respuestas técnicas exhaustivas sobre concurrencia con `lockForUpdate()`, Testing Trophy vs. Cohn, webhooks e idempotencia de Stripe, seguridad de `guest_token`, optimización N+1 con vistas SQL, rol del alumno frente a agentes IA, e internacionalización con accesibilidad WCAG 2.1 AA.

---

## Registro de Cambios y Trazabilidad

| Fecha | Autor | Versión | Resumen de Cambios |
|---|---|:---:|---|
| **10/09/2026** | Jonathan Quispe | `v1.0.0` | Definición formal del roadmap de CI/CD, ciclo de releases bajo GitFlow (Propuesta 1: v1.0.0 core transaccional + v1.1.0 refinamiento UI/UX) y material de soporte para la defensa del TFG. |
| **15/09/2026** | Jonathan Quispe | `v1.1.0` | Registro y cierre de la Iteración 4.3: Refinamiento reactivo en carrito (`CartCalculator`), resiliencia móvil, persistencia de invitados con neutralización de BFCache (`pageshow`), validación multinivel con shake/scroll, homologación fiscal en `/orders/{id}` y certificación de 141 tests Pest y 8/8 Playwright E2E. |
| **15/09/2026** | Jonathan Quispe | `v1.1.0` | Ejecución integral de las Fases 5 y 6: Creación del material de soporte para la defensa del TFG en `docs/defensa-tfg/` (4 documentos estratégicos), promoción GitFlow a `main` con `--no-ff`, doble etiquetado oficial `v1.1.0-tfg-final` y `v1.1.0`, back-merge hacia `develop` y cierre de la rama de release. |

