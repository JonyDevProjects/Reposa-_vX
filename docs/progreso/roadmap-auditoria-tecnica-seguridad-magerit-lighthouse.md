# Roadmap y Plan Técnico de Auditoría de Rendimiento, Accesibilidad y Seguridad (MAGERIT v.3 + Google Lighthouse)

## 1. Contexto, Justificación y Visión Estratégica

En el marco del desarrollo de **Reposa+** (E-Commerce transaccional de alto rendimiento especializado en descanso ergonómico *Sleep Tech*, construido sobre Laravel 11/12+ y PHP 8.4 bajo arquitectura contenerizada Docker) y habiendo culminado satisfactoriamente los hitos de:
1. Mitigación de Contenido Mixto (*Mixed Content*) en proxies inversos Nginx (`X-Forwarded-Proto`).
2. Implementación del estándar de indexación dinámica `/sitemap.xml` conforme al esquema oficial `sitemaps.org 0.9` enlazado en `public/robots.txt`.
3. Certificación del Quality Gate funcional y estilístico: **185 tests automatizados pasando al 100%** en Pest y cumplimiento estricto del estándar PSR-12 vía Laravel Pint (137 archivos certificados).
4. Caracterización empírica de barreras de red perimetrales en túneles efímeros y descarte de crawlers externos en favor de auditorías locales deterministas (`docs/informe-entorno-ngrok.md`).

Este documento establece el **Plan Técnico y Operativo de Auditoría** para evaluar de forma exhaustiva, cuantificable y reproducible la calidad global de la aplicación. Para asegurar el máximo rigor metodológico y académico acorde a la Escuela Politécnica Superior de la Universidad Pablo de Olavide (UPO), el proceso fusiona la **Tríada Metodológica del Proyecto** (Scrum/Kanban + Métrica v3 + SDD) con el marco formal de análisis de riesgos **MAGERIT v.3** de las administraciones públicas españolas, utilizando **Google Lighthouse** e inspección de vectores HTTP como herramientas técnicas de medición objetiva.

---

## 2. Metadatos de la Auditoría

* **Proyecto:** Reposa+ — E-Commerce Especializado en Descanso Ergonómico (*Sleep Tech*)
* **Titulación:** Grado en Ingeniería Informática en Sistemas de Información — Universidad Pablo de Olavide (UPO)
* **Autor:** Jonathan Quispe
* **Fecha:** 23 de septiembre de 2026
* **Rama de Trabajo:** `feature/informe-entorno-ngrok` (Commit base: `968c89d`)
* **Entorno de Evaluación:** `http://localhost:8000` (`reposaplus-dev-app` en Docker Compose con MySQL 8.0 en puerto 3306)
* **Herramientas de Auditoría:** Google Lighthouse CLI 13.5.0 (Headless Chromium), cURL HTTP Inspection y Pest Suite
* **Ruta del Documento:** `docs/progreso/roadmap-auditoria-tecnica-seguridad-magerit-lighthouse.md`

---

## 3. Marco Metodológico: La Tríada en la Auditoría

```text
┌─────────────────────────────────────────────────────────────────────────────┐
│ 1. CAPA DE GOBIERNO (Scrumban)                                              │
│    • Timeboxing de la sesión de auditoría en 5 fases secuenciales           │
│    • Definition of Done (DoD) multidimensional y Quality Gates estrictos   │
│    • Control de WIP = 1 (Auditoría activa antes de consolidación)           │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │ gobierna
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ 2. CAPA NORMATIVA Y DE RIESGOS (Métrica v3 + MAGERIT v.3)                   │
│    • Trazabilidad formal de Requisitos No Funcionales (RNF-001 a RNF-012)   │
│    • Catalogación de Activos [ACT], Amenazas [TH], Salvaguardas [SF]        │
│    • Matriz de Riesgo Residual y Criterios de Aceptación/Mitigación         │
│    • Catalogación de Artefactos de Prueba (ART-AUD-001 a 008)              │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │ aterriza operativamente en
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ 3. CAPA DE PRODUCCIÓN TÉCNICA (Spec-Driven Development - SDD)               │
│    • Criterios de Aceptación Cuantificables (AC-AUD-01 a AC-AUD-08)         │
│    • Protocolo de ejecución automatizado Lighthouse (Mobile & Desktop)      │
│    • Umbrales numéricos de corte (SLOs) para Performance, a11y, BP y SEO    │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 3.1 Capa 1: Gobernanza Ágil (Scrum/Kanban)

#### Definition of Done (DoD) de la Sesión de Auditoría
Una tarea o fase de auditoría se considera **Completada (Done)** única y exclusivamente cuando:
1. Las 4 vistas críticas (`/`, `/catalog`, `/catalog/1`, `/sitemap.xml`) han sido evaluadas en modo Mobile y Desktop por Lighthouse sin errores de ejecución.
2. Los reportes HTML y JSON han sido generados y versionados en `docs/artefactos/`.
3. Todos los hallazgos han sido clasificados en la **Matriz de Riesgos MAGERIT v.3**.
4. Cualquier hallazgo clasificado con Riesgo Residual **Alto** o **Crítico** ha sido mitigado en código y verificado.
5. Los 185 tests automatizados de Pest continúan pasando al 100% y Laravel Pint no reporta desviaciones PSR-12.
6. La documentación de cierre de sesión ha sido redactada antes de proceder con el merge hacia la rama `develop`.

#### Quality Gates Escalonados
* **Gate 1 (Baseline Integrity):** Entorno Docker activo en `http://localhost:8000`, base de datos inicializada y 185 tests Pest en verde.
* **Gate 2 (Audit Execution):** Generación íntegra de artefactos Lighthouse y extracción de métricas sin falsos positivos de red.
* **Gate 3 (Threshold Compliance):** Cumplimiento de los umbrales mínimos establecidos en este plan (o mitigación técnica inmediata en caso de desvío).
* **Gate 4 (Security Hardening):** Validación de salvaguardas MAGERIT (cabeceras seguras, flags de cookies, sanitización).
* **Gate 5 (GitFlow Integration):** Commit atómico y merge estructurado a `develop`.

---

### 3.2 Capa 2: Métrica v3 — Trazabilidad de Requisitos y Artefactos

#### Matriz de Trazabilidad de Requisitos No Funcionales (RNF)

| Código Requisito | Tipo Métrica v3 | Denominación | Criterio de Auditoría |
|:---|:---:|:---|:---|
| **`RNF-001`** | Arquitectura | Runtime y Framework | Verificación de ejecución bajo Laravel 11/12+ y PHP 8.4 nativo en contenedor `reposaplus-dev-app`. |
| **`RNF-002`** | Seguridad | Criptografía y Hardening | Protección CSRF activa (`XSRF-TOKEN`), hashing Bcrypt, sanitización contra inyección. |
| **`RNF-004`** | Rendimiento | Latencia y Core Web Vitals | TTFB $\le 120$ ms en backend; LCP $\le 2.5$ s, TBT $\le 200$ ms y CLS $\le 0.1$ en auditoría de frontend. |
| **`RNF-005`** | Usabilidad | Accesibilidad Web (WCAG 2.1 AA) | Cumplimiento estricto de contrastes de color en paleta Índigo/Pizarra (*The Midnight Sanctuary*), jerarquía de encabezados (`h1`-`h6`), atributos `alt` en imágenes y etiquetas semánticas. |
| **`RNF-007`** | Seguridad / Red | Integridad TLS y Proxies Inversos | Propagación de cabeceras `X-Forwarded-*` y ausencia absoluta de contenido mixto en assets o links. |
| **`RNF-009`** | Seguridad HTTP | Hardening de Cabeceras HTTP | Verificación y configuración de `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy`, y neutralización de divulgación en `X-Powered-By`. |
| **`RNF-010`** | Seguridad Web | Higiene de Cookies y Sesiones | Configuración de flags `HttpOnly`, `SameSite=Lax` y preparación para `Secure` en cookies `reposa-session` y tokens. |
| **`RNF-011`** | Calidad / SEO | Indexabilidad y Metadatos | Estándar sitemap 0.9 validado, `robots.txt` consistente, etiquetas canónicas y metaetiquetas de viewport/descripción. |
| **`RF-018`** | Funcional / SEO | Endpoint Dinámico `/sitemap.xml` | Integridad de las URLs del catálogo completo (8 productos) con formato de fecha y prioridades correctas. |

#### Catalogación Formal de Artefactos de Prueba (Métrica v3)

| Identificador | Descripción del Artefacto | Formato / Destino |
|:---|:---|:---|
| **`ART-AUD-001`** | Reporte Lighthouse Home (`/`) Desktop | `docs/artefactos/lighthouse-home-desktop.html` / `.json` |
| **`ART-AUD-002`** | Reporte Lighthouse Home (`/`) Mobile | `docs/artefactos/lighthouse-home-mobile.html` / `.json` |
| **`ART-AUD-003`** | Reporte Lighthouse Catálogo (`/catalog`) Desktop | `docs/artefactos/lighthouse-catalog-desktop.html` / `.json` |
| **`ART-AUD-004`** | Reporte Lighthouse Catálogo (`/catalog`) Mobile | `docs/artefactos/lighthouse-catalog-mobile.html` / `.json` |
| **`ART-AUD-005`** | Reporte Lighthouse Ficha de Producto (`/catalog/1`) Desktop | `docs/artefactos/lighthouse-product-desktop.html` / `.json` |
| **`ART-AUD-006`** | Reporte Lighthouse Ficha de Producto (`/catalog/1`) Mobile | `docs/artefactos/lighthouse-product-mobile.html` / `.json` |
| **`ART-AUD-007`** | Verificación de Cabeceras y Esquema Sitemap (`/sitemap.xml`) | `docs/artefactos/sitemap-validation-report.txt` |
| **`ART-AUD-008`** | Matriz Consolidada de Riesgos y Hallazgos MAGERIT v.3 | `docs/artefactos/matriz-evaluacion-magerit-reposaplus.md` |

---

### 3.3 Capa 3: Spec-Driven Development (SDD) — Criterios de Aceptación Cuantificables

* **`AC-AUD-01` (Score de Rendimiento / Performance):** El score de rendimiento en Google Lighthouse debe ser $\ge 85/100$ en Mobile y $\ge 90/100$ en Desktop en todas las páginas clave.
* **`AC-AUD-02` (Core Web Vitals - LCP):** El *Largest Contentful Paint* no debe superar los $2.5$ segundos bajo condiciones de simulación estándar.
* **`AC-AUD-03` (Core Web Vitals - TBT):** El *Total Blocking Time* atribuible al hilo principal de JavaScript no debe exceder los $200$ ms.
* **`AC-AUD-04` (Core Web Vitals - CLS):** El *Cumulative Layout Shift* debe ser estrictamente $\le 0.1$, asegurando estabilidad visual en la carga.
* **`AC-AUD-05` (Accesibilidad Legal WCAG 2.1 AA):** El score de Accesibilidad debe ser $\ge 90/100$ en todas las vistas, sin violaciones críticas de contraste, foco en teclado o ausencia de textos descriptivos.
* **`AC-AUD-06` (Best Practices Web):** El score de Buenas Prácticas debe ser $\ge 90/100$, garantizando ausencia de librerías con vulnerabilidades conocidas y manejo de APIs modernas.
* **`AC-AUD-07` (SEO Técnico e Indexabilidad):** El score de SEO en Lighthouse debe alcanzar $\ge 90/100$ en Home, Catálogo y Ficha de producto, y `/sitemap.xml` debe retornar HTTP 200 con `Content-Type: text/xml; charset=utf-8` o `application/xml`.
* **`AC-AUD-08` (Mitigación de Fuga de Información):** Ninguna respuesta HTTP de la aplicación debe revelar la cabecera `X-Powered-By: PHP/...` en entornos productivos o expuestos.

---

## 4. Marco Formal de Auditoría y Gestión de Riesgos: MAGERIT v.3

La metodología **MAGERIT v.3** (Metodología de Análisis y Gestión de Riesgos de los Sistemas de Información, del Consejo Superior de Administración Electrónica / CCN-CERT) proporciona el modelo sistemático para analizar los riesgos de seguridad sobre los activos de la aplicación.

### 4.1 Identificación y Valoración de Activos

Los activos del sistema se clasifican bajo las categorías estándar de MAGERIT y se evalúan en una escala discreta de 1 a 5 (**1: Bajo, 2: Medio, 3: Alto, 4: Muy Alto, 5: Crítico**) atendiendo a las 5 dimensiones de seguridad:
* **[D] Disponibilidad:** El activo es accesible y utilizable por los usuarios cuando se requiere.
* **[I] Integridad:** La información y procesos se mantienen exactos, completos y no manipulados.
* **[C] Confidencialidad:** La información solo es accesible por personas o procesos autorizados.
* **[A] Autenticidad:** Garantía inequívoca de la procedencia e identidad del emisor o dato.
* **[T] Trazabilidad:** Capacidad de reconstruir el historial y las acciones realizadas sobre el activo.

| Código Activo | Tipología MAGERIT | Denominación del Activo | Descripción en Reposa+ | [D] | [I] | [C] | [A] | [T] | Valor Global |
|:---:|:---:|:---|:---|:---:|:---:|:---:|:---:|:---:|:---:|
| **`[ACT-01]`** | `[DAT]` | Catálogo de Productos y Precios | Datos maestros de descanso ergonómico (8 productos, descripciones, tarifas, stock y variantes). | 4 | 5 | 1 | 4 | 4 | **Alto (3.6)** |
| **`[ACT-02]`** | `[COM/SER]` | Endpoints Públicos y API Web | Rutas HTTP de navegación (`/`, `/catalog`, `/catalog/{id}`, `/sitemap.xml`, `/login`, etc.). | 5 | 4 | 2 | 3 | 3 | **Alto (3.4)** |
| **`[ACT-03]`** | `[DAT/SEC]` | Datos de Sesión y Cookies | Cookies `reposa-session`, `XSRF-TOKEN`, tokens de autenticación y persistencia de carritos de invitado. | 4 | 5 | 5 | 5 | 4 | **Crítico (4.6)** |
| **`[ACT-04]`** | `[SER]` | Disponibilidad y Capacidad de Servicio | Rendimiento del contenedor web `reposaplus-dev-app`, latencia TTFB y fluidez UX (Core Web Vitals). | 4 | 3 | 1 | 2 | 3 | **Medio (2.6)** |
| **`[ACT-05]`** | `[SW]` | Código Frontend Entregado y Assets | Plantillas Blade, scripts compilados por Vite, hojas de estilo CSS y metadatos accesibles. | 4 | 4 | 1 | 3 | 2 | **Medio (2.8)** |
| **`[ACT-06]`** | `[HW/SO]` | Runtime del Servidor y Contenedor | Servidor web Nginx, motor PHP 8.4-FPM y motor de persistencia MySQL 8.0. | 5 | 5 | 4 | 4 | 4 | **Muy Alto (4.4)** |

---

### 4.2 Identificación de Amenazas

De acuerdo con el catálogo de amenazas de MAGERIT v.3 (adaptadas al ámbito del comercio electrónico y arquitecturas web modernas):

| Código Amenaza | Código MAGERIT | Denominación de la Amenaza | Vector de Ataque / Manifestación Técnica | Activos Afectados |
|:---:|:---:|:---|:---|:---:|
| **`[TH-01]`** | `[E.8 / I.3]` | **Fuga de Información Técnica en Cabeceras** | Divulgación del runtime en cabeceras HTTP (`X-Powered-By: PHP/8.4.25`), permitiendo a atacantes mapear vulnerabilidades conocidas de la versión. | `[ACT-06]`, `[ACT-02]` |
| **`[TH-02]`** | `[E.7 / A.2]` | **Ataques XSS / Clickjacking e Inyección de Recursos** | Inyección de código malicioso o anidamiento en frames fraudulentos por carencia de `Content-Security-Policy` o `X-Frame-Options`. | `[ACT-03]`, `[ACT-05]` |
| **`[TH-03]`** | `[E.4 / I.1]` | **Interceptación / Man-in-the-Middle y Contenido Mixto** | Tráfico de sesión o assets servidos sin protección TLS estricta (falta de HSTS `Strict-Transport-Security` o flags `Secure` en cookies). | `[ACT-03]`, `[ACT-05]` |
| **`[TH-04]`** | `[E.14 / D.2]` | **Degradación de Experiencia y Penalización SEO** | Métricas Core Web Vitals deficientes (LCP $\gt 2.5$ s, CLS $\gt 0.1$, TBT $\gt 200$ ms) que inducen abandono de compra y pérdida de ranking orgánico. | `[ACT-04]`, `[ACT-01]` |
| **`[TH-05]`** | `[A.9 / Leg]` | **Incumplimiento Legal de Accesibilidad Web** | Infracción de WCAG 2.1 Nivel AA (Directiva UE 2019/882 y UNE-EN 301 549) por contraste de color deficiente, falta de atributos `alt` o trampas de foco. | `[ACT-05]`, `[ACT-02]` |
| **`[TH-06]`** | `[E.1 / D.1]` | **Deficiencias de Indexabilidad y Sitemap Roto** | Mala configuración de `robots.txt`, ausencia de tags canónicos o fallos en `/sitemap.xml` que impiden el descubrimiento y rastreo por motores de búsqueda. | `[ACT-01]`, `[ACT-02]` |

---

### 4.3 Evaluación de Salvaguardas

Las salvaguardas se estructuran en medidas preventivas, detectivas y correctivas:

```text
┌─────────────────────────────────────────────────────────────────────────────┐
│ SALVAGUARDAS TÉCNICAS REPOSA+                                               │
├─────────────────────────────────────────────────────────────────────────────┤
│ • SF-01 [Preventiva]: Forzado de HTTPS en AppServiceProvider y X-Forwarded-Proto│
│ • SF-02 [Preventiva]: Cookies con flag HttpOnly y SameSite=Lax en config/session│
│ • SF-03 [Preventiva]: Protección CSRF bidireccional en todas las rutas POST/PUT│
│ • SF-04 [Preventiva]: Hardening de Cabeceras HTTP (Middleware SecurityHeaders) │
│ • SF-05 [Correctiva]: Ocultación de X-Powered-By en php.ini / fastcgi_hide_header│
│ • SF-06 [Detectiva] : Google Lighthouse CLI para evaluación de CWV y a11y   │
│ • SF-07 [Detectiva] : Suite de 185 tests automatizados Pest para regresión  │
│ • SF-08 [Preventiva]: Sitemap XML dinámico acoplado a la base de datos de catálogo│
└─────────────────────────────────────────────────────────────────────────────┘
```

#### Mapeo Amenaza vs. Salvaguarda

| Amenaza | Salvaguardas Aplicadas / a Auditar | Efectividad Esperada |
|:---|:---|:---:|
| `[TH-01]` Fuga de información en cabeceras | `SF-04`, `SF-05` (Eliminación de `X-Powered-By` y Server tokens) | **Total** |
| `[TH-02]` XSS / Clickjacking / Inyección | `SF-03`, `SF-04` (`X-Frame-Options`, `X-Content-Type-Options: nosniff`) | **Muy Alta** |
| `[TH-03]` Man-in-the-Middle y Contenido Mixto | `SF-01`, `SF-02` (Esquema HTTPS forzado en proxies y cookies protegidas) | **Alta** |
| `[TH-04]` Degradación CWV y rebote | `SF-06`, optimización de imágenes (`webp`), CSS purgado | **Alta** |
| `[TH-05]` Incumplimiento de Accesibilidad | `SF-06` (Auditoría axe-core en Lighthouse, contraste $\ge 4.5:1$, labels) | **Muy Alta** |
| `[TH-06]` Deficiencias de Indexabilidad | `SF-08`, validación de `robots.txt` y metadatos OpenGraph | **Total** |

---

### 4.4 Determinación del Riesgo Residual y Plan de Mitigación

MAGERIT determina el riesgo como una función del **Impacto (I)** y la **Probabilidad de Ocurrencia (P)**:

$$\text{Riesgo} = \text{Impacto} \times \text{Probabilidad}$$

La escala combina niveles de 1 a 5 para determinar cuatro zonas de riesgo:
* **Crítico (16 - 25):** Inadmisible. Bloquea de inmediato el paso a producción o merge a `develop`. Requiere subsanación técnica obligatoria en la misma sesión.
* **Alto (10 - 15):** Inaceptable para release. Requiere mitigación directa antes de certificar el incremento.
* **Medio (5 - 9):** Tolerable bajo justificación técnica documentada si existen salvaguardas compensatorias, o mitigar si el coste es bajo.
* **Bajo (1 - 4):** Riesgo aceptado. Monitorización rutinaria.

#### Criterios de Decisión para Mitigación Técnica en la Sesión
1. **Regla de Bloqueo:** Si Lighthouse reporta un score $< 80$ en cualquiera de las categorías o si se detecta una vulnerabilidad en cabeceras clasificada con riesgo $\ge 10$, se detiene el flujo hacia `develop` y se ejecuta la fase de **Mitigaciones Técnicas**.
2. **Ciclo de Subsanación Inmediata:** Modificación de archivos fuente (middleware Laravel, plantillas Blade, configuración Nginx o Vite), re-ejecución de Pest y re-ejecución focalizada de Lighthouse para validar la erradicación del riesgo.

---

## 5. Herramienta Técnica Principal: Google Lighthouse

### 5.1 Vistas Críticas Seleccionadas

1. **Página de Inicio (`/`):**
   * *Justificación:* Puerta de entrada principal del usuario, banner interactivo (*Hero Section*), destacados de descanso ergonómico y punto con mayor carga inicial de assets.
2. **Catálogo de Productos (`/catalog`):**
   * *Justificación:* Vista con interacción de filtrado, múltiples tarjetas de producto (8 productos en seeders), consultas de agregación a base de datos y paginación.
3. **Ficha de Detalle de Producto (`/catalog/1`):**
   * *Justificación:* Página transaccional crítica con selector de variantes ergonómicas, galería de imágenes del producto, acordeones de especificaciones técnicas y botón de compra directa hacia el carrito.
4. **Endpoint de Indexación (`/sitemap.xml`):**
   * *Justificación:* Eje de rastreabilidad técnica y SEO, verificación de tiempo de generación, cabecera `Content-Type` adecuada y ausencia de overhead de HTML.

### 5.2 Parámetros de Medición y Emulación

Para cada una de las 3 vistas visuales (`/`, `/catalog`, `/catalog/1`), se realizarán dos ejecuciones independientes:
1. **Perfil Desktop:** Resolución de pantalla completa ($1350 \times 940$), CPU y red sin emulación de estrangulamiento artificial. Representa el entorno corporativo y compras de escritorio.
2. **Perfil Mobile:** Emulación estándar de Google Lighthouse (pantalla móvil simulada $412 \times 823$, estrangulamiento 4G y slowdown de CPU $4\times$). Representa el tráfico predominante en comercio electrónico moderno.

---

## 6. Umbrales Mínimos de Aceptación (Score Targets / SLIs & SLOs)

| Dimensión de Auditoría | Umbral Mínimo Mobile | Umbral Mínimo Desktop | Métrica Clave Asociada (SLO) | Acción si no se alcanza |
|:---|:---:|:---:|:---|:---|
| **Performance (Rendimiento)** | $\ge \mathbf{85}$ | $\ge \mathbf{90}$ | $\text{LCP} \le 2.5\text{ s, } \text{TBT} \le 200\text{ ms, } \text{CLS} \le 0.1$ | Optimización de assets, Lazy-Loading en imágenes, defer de scripts. |
| **Accessibility (Accesibilidad)** | $\ge \mathbf{90}$ | $\ge \mathbf{90}$ | 0 fallos críticos en contrastes WCAG 2.1 AA, labels en botones y roles ARIA. | Corrección de clases de contraste en Blade y atributos `aria-label`. |
| **Best Practices (Buenas Prácticas)** | $\ge \mathbf{90}$ | $\ge \mathbf{90}$ | Ausencia de APIs deprecadas, resolución de recursos segura, sin errores de consola. | Revisión de console logs y cabeceras de servidor. |
| **SEO (Optimización en Buscadores)** | $\ge \mathbf{90}$ | $\ge \mathbf{90}$ | Etiqueta `meta viewport`, títulos de página descriptivos y `robots.txt` accesible. | Ajuste de metadatos en layout maestro `app.blade.php`. |

---

## 7. Protocolo Exacto de Ejecución y Almacenamiento de Artefactos

Para garantizar la no concurrencia y no colapso de la sesión interactiva, el protocolo de ejecución técnica se apoyará en comandos no bloqueantes con `--chrome-flags="--headless --no-sandbox"` y sin la flag interactiva `--view`, redirigiendo las salidas a los ficheros normalizados de `docs/artefactos/`.

### 7.1 Matriz de Comandos de Auditoría

```bash
# 1. Comprobación preliminar de salud y cabeceras del servidor
curl -sI http://localhost:8000/
curl -sI http://localhost:8000/sitemap.xml

# 2. Auditoría Home (/)
npx lighthouse http://localhost:8000/ --preset=desktop --output=html,json --output-path=docs/artefactos/lighthouse-home-desktop --chrome-flags="--headless --no-sandbox"
npx lighthouse http://localhost:8000/ --form-factor=mobile --output=html,json --output-path=docs/artefactos/lighthouse-home-mobile --chrome-flags="--headless --no-sandbox"

# 3. Auditoría Catálogo (/catalog)
npx lighthouse http://localhost:8000/catalog --preset=desktop --output=html,json --output-path=docs/artefactos/lighthouse-catalog-desktop --chrome-flags="--headless --no-sandbox"
npx lighthouse http://localhost:8000/catalog --form-factor=mobile --output=html,json --output-path=docs/artefactos/lighthouse-catalog-mobile --chrome-flags="--headless --no-sandbox"

# 4. Auditoría Ficha de Producto (/catalog/1)
npx lighthouse http://localhost:8000/catalog/1 --preset=desktop --output=html,json --output-path=docs/artefactos/lighthouse-product-desktop --chrome-flags="--headless --no-sandbox"
npx lighthouse http://localhost:8000/catalog/1 --form-factor=mobile --output=html,json --output-path=docs/artefactos/lighthouse-product-mobile --chrome-flags="--headless --no-sandbox"

# 5. Verificación de endpoint sitemap (/sitemap.xml)
curl -sI http://localhost:8000/sitemap.xml > docs/artefactos/sitemap-validation-report.txt
curl -s http://localhost:8000/sitemap.xml | head -n 30 >> docs/artefactos/sitemap-validation-report.txt
```

*(Nota: Lighthouse añadirá automáticamente la extensión `.report.html` y `.report.json` a los nombres de salida proporcionados).*

---

## 8. Fases Secuenciales de la Sesión de Auditoría

```text
┌──────────────┐     ┌──────────────┐     ┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│   FASE 1     │     │   FASE 2     │     │   FASE 3     │     │   FASE 4     │     │   FASE 5     │
│ Planificación│────▶│  Ejecución   │────▶│  Matriz de   │────▶│ Mitigaciones │────▶│ Merge Final  │
│  y Aprobación│     │  Lighthouse  │     │ Riesgos      │     │  (Si aplica) │     │ a develop    │
│  (Este Doc)  │     │  y Cabeceras │     │ y Hallazgos  │     │ y Re-testing │     │  y Cierre    │
└──────────────┘     └──────────────┘     └──────────────┘     └──────────────┘     └──────────────┘
```

### Fase 1: Planificación Formal y Aprobación del Roadmap
* **Objetivo:** Establecer la base metodológica de la auditoría (Tríada + MAGERIT v.3), formalizar los requisitos y definir los umbrales numéricos de aceptación.
* **Entregable:** Este documento aprobado en `docs/progreso/roadmap-auditoria-tecnica-seguridad-magerit-lighthouse.md`.

### Fase 2: Ejecución de la Auditoría Técnica
* **Objetivo:** Ejecutar las mediciones automatizadas con Lighthouse CLI en local e inspeccionar las cabeceras HTTP de respuesta mediante cURL.
* **Acciones:**
  * Lanzamiento de las 6 pasadas de Lighthouse (3 vistas $\times$ 2 perfiles).
  * Inspección y volcado de cabeceras de seguridad y cookies de sesión.
  * Inspección sintáctica del XML en `/sitemap.xml`.
* **Entregables:** Artefactos generados en `docs/artefactos/` (`lighthouse-*.report.html`, `.json` y `sitemap-validation-report.txt`).

### Fase 3: Elaboración de la Matriz Consolidada de Riesgos y Hallazgos MAGERIT v.3
* **Objetivo:** Estructurar los resultados numéricos obtenidos, calcular los scores consolidados y tipificar cada deficiencia o advertencia técnica en una matriz formal de riesgos.
* **Acciones:**
  * Extracción de métricas clave (Scores 0-100, LCP, TBT, CLS).
  * Valoración de impacto y probabilidad para cada hallazgo detectado.
  * Cálculo del Riesgo Residual y determinación de si se requiere paso por la Fase 4.
* **Entregable:** Documento `docs/artefactos/matriz-evaluacion-magerit-reposaplus.md`.

### Fase 4: Mitigaciones Técnicas y Verificación de Salvaguardas (Condicional)
* **Objetivo:** Subsanar inmediatamente cualquier hallazgo que incumpla los umbrales o presente riesgo residual inaceptable (p. ej. fuga de `X-Powered-By`, falta de cabeceras `nosniff`/`SAMEORIGIN`, o desajustes de contraste en Blade).
* **Acciones:**
  * Implementación de middleware de cabeceras o refinamiento de vistas.
  * Ejecución de Quality Gate de regresión: `php artisan test` (Pest) y `vendor/bin/pint`.
  * Re-auditoría puntual para certificar la elevación de scores.

### Fase 5: Consolidación, Quality Gate Final y Merge a `develop`
* **Objetivo:** Cerrar el ciclo GitFlow de la rama de trabajo con todas las garantías de calidad.
* **Acciones:**
  * Verificación final de integridad de suite (185 tests Pest).
  * Commit semántico estructurado.
  * Merge sin avance rápido (`--no-ff`) hacia la rama `develop`.
  * Redacción del informe final de sesión en `docs/progreso/`.

---

## 9. Estado Actual del Roadmap

* [x] **Fase 1:** Planificación MAGERIT v.3 y especificación técnica del plan redactada.
* [x] **Fase 2:** Ejecución técnica de Lighthouse y volcado de artefactos en `docs/artefactos/`.
* [x] **Fase 3:** Consolidación de la Matriz de Riesgos y Hallazgos MAGERIT.
* [x] **Fase 4:** Mitigaciones técnicas y re-verificación con Pest/Pint (si procede).
* [x] **Fase 5:** Quality Gate de integración y merge a `develop`.
