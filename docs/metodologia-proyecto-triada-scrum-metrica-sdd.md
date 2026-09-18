# Marco Metodológico Reposa+: La Tríada Scrum/Kanban + Métrica v3 + Spec-Driven Development (SDD)

**Proyecto:** Reposa+ — E-Commerce Transaccional de Alto Rendimiento Especializado en Descanso Ergonómico (*Sleep Tech*)  
**Autor:** Jonathan Quispe  
**Titulación:** Grado en Ingeniería Informática en Sistemas de Información  
**Institución:** Escuela Politécnica Superior — Universidad Pablo de Olavide (UPO), Sevilla  
**Fecha de Establecimiento:** 17 de septiembre de 2026  
**Clasificación Metodológica:** Metodología Híbrida Formal-Ágil de Tres Capas (Gestión, Normativa y Producción)

---

## 1. Justificación y Fundamentación Epistemológica

Uno de los dilemas metodológicos más recurrentes en la realización de Trabajos de Fin de Grado en el ámbito de la Ingeniería Informática radica en la **tensión entre la normativa académica institucional y las prácticas reales de la industria del software**:

1. **La exigencia normativa institucional:** Universidades como la Universidad Pablo de Olavide (UPO) y los órganos de la administración pública española exigen la adopción de **Métrica v3**, una metodología formal, estructurada y documental concebida para garantizar la exhaustividad, el control de configuración y la trazabilidad estricta de requisitos. Sin embargo, su aplicación rígida tradicional (modelo en cascada puro) penaliza la adaptabilidad, ralentiza el ciclo de retroalimentación e ignora las dinámicas ágiles modernas.
2. **La realidad de la industria y el desarrollo transaccional:** Los proyectos modernos de comercio electrónico operan bajo **marcos de trabajo ágiles (Scrum, Kanban o Scrumban)**, priorizando la entrega continua de valor, la mitigación temprana de riesgos y la capacidad de pivotar ante requerimientos emergentes (como la integración asíncrona de pasarelas de pago o la resiliencia en dispositivos móviles).
3. **El cambio de paradigma en la era de los Agentes de IA:** La incorporación de herramientas de Inteligencia Artificial generativa y agentes autónomos (como el **Google Antigravity SDK**) introduce una nueva exigencia técnica: los agentes no pueden operar eficazmente sobre descripciones vagas de usuario ("historias de usuario" informales) sin incurrir en alucinaciones o degradación de arquitectura. Requieren **especificaciones técnicas previas, no ambiguas, estructuradas y contrastables**.

Para resolver este desafío de manera óptima y con el máximo rigor académico, el proyecto **Reposa+** formaliza un modelo metodológico innovador: **La Tríada Scrum/Kanban + Métrica v3 + Spec-Driven Development (SDD)**.

---

## 2. Arquitectura del Modelo: La Tríada en Tres Capas

La metodología desacopla tres dimensiones complementarias dentro del ciclo de vida del software, asignando a cada una el marco más eficiente:

```text
┌─────────────────────────────────────────────────────────────────────────────┐
│ 1. CAPA DE GOBIERNO Y GESTIÓN (Scrumban: Scrum + Kanban)                    │
│    • Timeboxing en Sprints/Hitos alineados con GitFlow                      │
│    • Visualización del flujo continuo y control de WIP en Tablero Kanban   │
│    • Planificación de 400 h y simulación de equipo profesional en el Anexo I│
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │ gobierna
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ 2. CAPA NORMATIVA, ESTRUCTURAL Y DOCUMENTAL (Métrica v3 adaptada a la UPO)  │
│    • PSI (Plan de Proyecto): WBS/EDT, gestión de riesgos y costes (€)       │
│    • ASI (Análisis): Fichas OBJ-xxx, RF-xxx, RNF-xxx, CU-xxx e Informes IF   │
│    • DSI (Diseño): Modelo Físico Relacional SQL, Clases CL-xxx y Despliegue │
│    • Matrices de Trazabilidad Bidireccional Cruzada                         │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │ aterriza operativamente en
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ 3. CAPA DE PRODUCCIÓN TÉCNICA E IMPLEMENTACIÓN (Spec-Driven Development)   │
│    • Roadmaps técnicos detallados por incremento (Fuente Única de Verdad)   │
│    • Orquestación de Agentes de IA (Google Antigravity SDK) dirigidos por spec│
│    • Testing Trophy (141 tests Pest + 8 Playwright E2E) como spec ejecutable│
│    • Calidad continua en CI/CD (GitHub Actions con 5 jobs en la nube)       │
│    • Memoria viva incremental y handoffs en Engram CLI                      │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Capa 1: Procesos de Gestión del Proyecto — Scrumban (Scrum + Kanban)

La gestión del proyecto asume una formulación híbrida **Scrumban**, combinando la estructura temporal y los hitos de Scrum con la fluidez operativa de Kanban:

### 3.1 Timeboxing por Sprints e Hitos de Release (Scrum)
El desarrollo general de Reposa+ se estructura en bloques temporales orientados a entregables funcionales potencialmente desplegables:
* **Hito Base v1.0.0 (Core Transaccional):** Consolidación de la persistencia relacional, checkout de invitados (*guest token*), integración inicial con Stripe, servicio mock de paquetería estándar y certificación de la pirámide tripartita base (119 pruebas).
* **Hito de Release v1.1.0 (Refinamiento Heurístico UI/UX y Back-Office):**
  * *Sprint 4.1 (Estabilización Back-Office):* Desviaciones operativas D1 a D5 (sincronización logística bidireccional, botón universal de reembolso, etc.).
  * *Sprint 4.2 (Simplificación del Catálogo):* Divulgación progresiva del Asesor Anatómico y búsqueda multiatributo tolerante.
  * *Sprint 4.3 (Carrito y Checkout Resiliente):* Motor reactivo `CartCalculator`, neutralización del BFCache y homologación fiscal y de i18n (141 pruebas).
* **Hito Final (Entrega Académica y CI/CD):** Pipeline de 5 jobs en GitHub Actions y material de defensa para el tribunal universitario.

### 3.2 Visualización del Flujo Continuo y Control de WIP (Kanban)
A nivel de desarrollo diario en solitario, el alumno gestiona el flujo de trabajo mediante un tablero Kanban dinámico compuesto por 5 estados:
1. **Backlog Priorizado:** Requisitos e historias de usuario derivadas de los objetivos del proyecto.
2. **En Especificación (Spec Drafting):** Redacción del roadmap técnico y modelado de interfaces antes de codificar.
3. **En Desarrollo (In Progress):** Construcción de código y componentes asistida por agentes de IA bajo límite estricto de WIP (*Work In Progress = 1* para evitar dispersión cognitiva).
4. **En Testing & CI Gate:** Verificación en local (Pest, Playwright, Pint) y comprobación en el runner de GitHub Actions.
5. **Completado (Done / Closed):** Merge `--no-ff` en Git, redacción de handoff y persistencia de lecciones en Engram.

### 3.3 Norma de 400 Horas y Simulación de Perfiles Profesionales
Siguiendo la pauta estricta observada en el TFG de referencia de la Escuela Politécnica Superior de la UPO, el proyecto simula la distribución del esfuerzo en 5 roles de ingeniería desempeñados por el alumno, modelando un total de **400 horas** valoradas en **13.800 €** a precios estándar de mercado:
* **Jefe de Proyecto:** 40 h a 45 €/h = 1.800 €
* **Analista de Sistemas:** 60 h a 40 €/h = 2.400 €
* **Arquitecto de Software:** 60 h a 40 €/h = 2.400 €
* **Programador Backend:** 130 h a 30 €/h = 3.900 €
* **Programador Frontend:** 110 h a 30 €/h = 3.300 € (ajustado a 3.900 €)

---

## 4. Capa 2: Procesos Normativos, Estructurales y Documentales — Métrica v3 (UPO)

Métrica v3 proporciona la arquitectura documental y el marco de trazabilidad formal exigido por la Universidad Pablo de Olavide, articulándose en los tres Anexos normativos que acompañan a la Memoria Ejecutiva:

### 4.1 Planificación de Sistemas de Información (PSI) $\rightarrow$ Anexo I: Plan de Proyecto
* Definición formal del alcance y catálogo de objetivos generales y específicos (`OBJ-001` a `OBJ-008`).
* Estructura de Descomposición de Trabajo (EDT / WBS) dividida en 5 fases (Inicio, Análisis, Diseño, Construcción y Despliegue).
* Tabla de costes amplificada tarea por tarea.
* Matriz de riesgos con planes de contingencia y niveles de probabilidad/severidad.
* Planes auxiliares: Gestión de Configuración (Git/GitFlow), Plan de Calidad y Estrategia de Pruebas.

### 4.2 Análisis de Sistemas de Información (ASI) $\rightarrow$ Anexo II: Documento de Análisis
* **Catálogo de Requisitos en Fichas Tabulares:**
  * Requisitos Funcionales (`RF-01` a `RF-09`): Código, Versión, Objetivos asociados, Descripción con viñetas, Actores y Comentarios.
  * Requisitos No Funcionales (`RNF-01` a `RNF-04`): Seguridad, Integridad Transaccional, Usabilidad y Eficiencia.
* **Matrices de Trazabilidad Bidireccional:**
  * Matriz Objetivos – Requisitos (`OBJ` $\leftrightarrow$ `RF/RNF`).
  * Matriz Requisitos – Casos de Uso (`RF` $\leftrightarrow$ `CU`).
* **Especificación de Casos de Uso (`CU-0001` a `CU-0025`):** Fichas normalizadas con actores, precondiciones, postcondiciones, puntos de extensión, flujo normal paso a paso y flujos alternativos numerados (`3.a`, `4.b`).
* **Catálogo Exhaustivo de Interfaces de Usuario (`IU-01` a `IU-34`):** Cada vista del storefront y del panel de control incluye captura visual centrada, tabla de campos (nombre, tipo, editable/consulta, obligatoriedad, descripción) y tabla de botones/enlaces (nombre y acción).
* **Módulos de Informe (`IF-01` a `IF-05`):** Reportes analíticos con criterios de ordenación (`1, Ascendente / 2, Descendente`) y campos de acumulación/resumen.

### 4.3 Diseño de Sistemas de Información (DSI) $\rightarrow$ Anexo III: Documento de Diseño
* **Diseño Físico y Despliegue:** Diagrama de despliegue con la topología de los 7 contenedores Docker y especificación de requisitos no funcionales de arquitectura (`RNF-OP` y `RNF-SEG`).
* **Modelo Físico de Datos:** Diagrama relacional completo y fichas técnicas por tabla de base de datos (`products`, `orders`, `shipments`, etc.), especificando nombres de constraints, columnas, tipos SQL, claves primarias, claves foráneas con tabla destino, índices únicos y restricciones `CHECK`.
* **Clases de Diseño de Controladores (`CL-0001` a `CL-0006`):** Atributos de petición y tablas de métodos con visibilidad, parámetros y descripción técnica de la lógica de negocio.
* **Especificaciones de Construcción e Implantación:** Comandos de orquestación, scripts de migración y seeders, requisitos de dominio y certificados SSL/TLS para producción.

---

## 5. Capa 3: Procesos de Producción Técnica — Spec-Driven Development (SDD)

Mientras Métrica v3 documenta qué se debe construir y Scrumban organiza cuándo se aborda, **Spec-Driven Development (SDD)** gobierna **cómo se produce el software en el día a día técnico**, especialmente al integrar un ecosistema de agentes de IA:

```text
               ┌───────────────────────────────────────────────┐
               │ 1. Spec Drafting (Roadmap Técnico en docs/ )  │
               │    - Contratos de Interfaz (PHP Interfaces)   │
               │    - Modelos de Datos y Migraciones           │
               │    - Reglas de Negocio y Casos Límite         │
               │    - Criterios de Aceptación Cuantificables   │
               └──────────────────────┬────────────────────────┘
                                      │ guía directamente
                                      ▼
               ┌───────────────────────────────────────────────┐
               │ 2. Executable Specs (Testing Trophy)          │
               │    - Casos E2E en Playwright (@playwright)    │
               │    - Feature Tests con MySQL 8.0 y Redis      │
               │    - Unit Tests en memoria pura (0 dependencias)│
               └──────────────────────┬────────────────────────┘
                                      │ contextualiza
                                      ▼
               ┌───────────────────────────────────────────────┐
               │ 3. Agent-Orchestrated Implementation (Code)   │
               │    - Alumno: Arquitecto y Tech Lead           │
               │    - Agentes (Antigravity SDK): Generación    │
               │      mecánica estricta según spec y reglas    │
               └──────────────────────┬────────────────────────┘
                                      │ audita
                                      ▼
               ┌───────────────────────────────────────────────┐
               │ 4. Quality Gate Automatizado (CI/CD Actions)  │
               │    - Job 1: Pint PSR-12 (135 archivos limpios)│
               │    - Job 2: Unit Tests (SQLite memoria)       │
               │    - Job 3: Feature Tests (MySQL + Redis)     │
               │    - Job 4: Vite Build (Assets compilados)    │
               │    - Job 5: Playwright E2E (Chromium real)    │
               └──────────────────────┬────────────────────────┘
                                      │ consolida
                                      ▼
               ┌───────────────────────────────────────────────┐
               │ 5. Living Spec Update & Engram Memory Logging │
               │    - Trazabilidad de desviaciones (D1 a D5)   │
               │    - GitFlow Merge (--no-ff) y Tag Semántico  │
               │    - Acta de Handoff & Memoria Engram CLI     │
               └───────────────────────────────────────────────┘
```

### 5.1 Principios Operativos de SDD en Reposa+
1. **Ninguna línea de código sin especificación previa:** Antes de modificar un archivo PHP o Blade, el desarrollador redacta la especificación técnica en el roadmap correspondiente (`docs/progreso/roadmap-*.md`). Esto elimina la ambigüedad y previene la dispersión del alcance (*scope creep*).
2. **La especificación es la base de pruebas (*Executable Specification*):** La especificación define formalmente los criterios de aceptación, que se traducen inmediatamente en aserciones de prueba en Pest o Playwright. El software no está terminado cuando el programador "cree" que funciona, sino cuando la suite automatizada certifica el 100% de la especificación.
3. **El Ingeniero como Orquestador Arquitectónico:** Al apoyarse en agentes de IA (Google Antigravity SDK), la especificación formal actúa como el contrato de trabajo de los agentes. El alumno define las restricciones y reglas de arquitectura (ej. uso obligatorio de `lockForUpdate()`, no ejecutar consultas dentro de bucles Blade, etc.), mientras los agentes implementan los cambios mecánicos siguiendo la directriz.
4. **Resiliencia de Conocimiento con Engram CLI:** Para evitar la pérdida de contexto entre sesiones de trabajo, cada hallazgo crítico, decisión de arquitectura o corrección de defectos se persiste en el motor de memoria permanente **Engram** (`engram save "<Título>" "<Detalle>" --project reposaplus-tfg`), asegurando memoria histórica viva para futuras sesiones.

---

## 6. Matriz de Trazabilidad Integral: De la Universidad al Código

La gran fortaleza de esta tríada metodológica es que permite demostrar una **cadena de custodia ininterrumpida** entre cualquier requerimiento académico y su verificación técnica en el repositorio:

| Nivel Metodológico | Artefacto Generado | Ejemplo Concreto en Reposa+ |
|---|---|---|
| **Estratégico (Métrica v3)** | Objetivo General / Específico | **`OBJ-002`**: Diseñar e implementar un flujo transaccional de compra sin fricciones con soporte para invitados y usuarios registrados. |
| **Normativo (Métrica v3)** | Requisito Funcional | **`RF-04` / `RF-05`**: El sistema debe permitir la compra directa a usuarios anónimos mediante un formulario adaptativo sin requerir registro previo obligatorio. |
| **Lógico (Métrica v3)** | Caso de Uso Formal | **`CU-0014` (Guest Checkout)**: Especificación con flujo normal (relleno de dirección, selección de tarifa, pago en Stripe) y flujos alternativos (pago cancelado, stock insuficiente). |
| **Táctico (Scrumban)** | Ítem de Tablero / Sprint | **Tarea Kanban**: *"Implementar Checkout Adaptativo de Invitados con Persistencia Doble Capa y Token Criptográfico"*. |
| **Operativo (SDD)** | Especificación en Roadmap | [`docs/progreso/roadmap-flujos-checkout-paqueteria-oauth.md`](file:///Users/jonathanquishpe/JoniDev/Reposa+_TFG/docs/progreso/roadmap-flujos-checkout-paqueteria-oauth.md) (Fase 3: definición del campo `guest_token`, snapshots de envío y flujo de confirmación). |
| **Construcción (Código)** | Clases, Rutas y Vistas | [`CartController.php`](file:///Users/jonathanquishpe/JoniDev/Reposa+_TFG/Reposa+/app/Http/Controllers/CartController.php), [`checkout/index.blade.php`](file:///Users/jonathanquishpe/JoniDev/Reposa+_TFG/Reposa+/resources/views/checkout/index.blade.php), migración `add_guest_and_shipping_fields_to_orders_table.php`. |
| **Verificación (Testing)** | Pruebas Automatizadas | [`tests/Feature/GuestCheckoutTest.php`](file:///Users/jonathanquishpe/JoniDev/Reposa+_TFG/Reposa+/tests/Feature/GuestCheckoutTest.php) (11 tests) y [`e2e/casos-1-to-5.spec.js`](file:///Users/jonathanquishpe/JoniDev/Reposa+_TFG/Reposa+/e2e/casos-1-to-5.spec.js) (Caso 2: Guest Checkout en Chromium real). |
| **Calidad (CI/CD Gate)** | Pipeline en la Nube | [`.github/workflows/ci.yml`](file:///Users/jonathanquishpe/JoniDev/Reposa+_TFG/.github/workflows/ci.yml) ejecutando los 5 jobs en GitHub Actions (100% verde). |
| **Gestión de Configuración** | GitFlow Commit & Tag | Commits `3755bef` y `5208f7d` fusionados con `--no-ff` hacia `main` con tag oficial `v1.1.0-tfg-final`. |
| **Memoria Histórica** | Handoff y Engram | [`docs/handoff-sesion-2026-09-05.md`](file:///Users/jonathanquishpe/JoniDev/Reposa+_TFG/docs/handoff-sesion-2026-09-05.md) y memorias de Engram `#276` a `#282`. |

---

## 7. Ventajas Académicas y Profesionales del Modelo

1. **Alineación 100% con la UPO:** Cumple escrupulosamente con el formato en 4 volúmenes (Memoria Ejecutiva + 3 Anexos), la norma de 400 horas, el catálogo tabular Métrica v3 y las matrices de trazabilidad exigidas por el tribunal.
2. **Eficiencia en Desarrollo Solitario:** Kanban evita la parálisis por sobrecarga y el WIP=1 mantiene el foco en resolver una sola tarea de principio a fin.
3. **Gobierno Riguroso de la Inteligencia Artificial:** SDD resuelve la principal crítica que los tribunales formulan sobre el uso de IA: la falta de autoría o control. Con SDD, el alumno demuestra que la IA solo ejecutó tareas técnicas subordinadas a especificaciones formales diseñadas por el propio ingeniero.
4. **Garantía Industrial de Calidad:** A diferencia de proyectos académicos que se limitan a capturas estáticas y pruebas manuales no verificables, la tríada culmina en una suite de 141 pruebas automatizadas y un pipeline CI/CD activo.
