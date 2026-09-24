# Proyecto TAD - Reposa+

Este repositorio contiene el código y la documentación del proyecto **Reposa+**.

La aplicación principal (desarrollada en Laravel) se encuentra en la carpeta [`Reposa+`](./Reposa+).

## Documentación del Proyecto

Toda la documentación relacionada con el diseño, los requisitos, la base de datos y la estrategia del proyecto se encuentra consolidada en el directorio [`/docs`](./docs).

### Documentación Académica Oficial (Estándar EPS-UPO / Métrica v3)

- 📖 [**Memoria del Proyecto (Memoria Troncal)**](./docs/Memoria_Proyecto.md): Documento principal de 20-25 páginas con justificación, arquitectura, calidad, resultados y conclusiones.
- 📋 [**Anexo I: Plan de Proyecto (PSI)**](./docs/Anexo_I_Plan_de_Proyecto.md): Planificación temporal, EDT/WBS, catálogo de objetivos (OBJ-001..008), presupuesto canónico (400h / 13.800€) y gestión de riesgos.
- 🔍 [**Anexo II: Documento de Análisis (ASI)**](./docs/Anexo_II_Documento_de_Analisis.md): Catálogo exhaustivo de requisitos (RF-001..017, RNF-001..006), casos de uso tabulares (CU-001..015), interfaces de usuario e interfaz externa.
- 🏗️ [**Anexo III: Documento de Diseño (DSI)**](./docs/Anexo_III_Documento_de_Diseno.md): Arquitectura de 7 contenedores Docker, DDL relacional 3FN, diseño OO (CL-001..006), seguridad OWASP y patrones de integración.

### Material de Soporte para la Defensa Académica

- 📊 [**Presentación de Diapositivas (PDF Oficial)**](./docs/defensa-tfg/presentacion-defensa.pdf) | [**Versión Web Interactiva**](./docs/defensa-tfg/presentacion-defensa.html) | [**Fuente Marp**](./docs/defensa-tfg/presentacion-defensa.marp.md): Deck de 12 diapositivas de alto impacto visual.
- ⏱️ [**Guion de Exposición de 15 Minutos**](./docs/defensa-tfg/guion-exposicion-15-minutos.md): Minutaje exacto, narrativa por bloques y pautas de oratoria para el tribunal.
- 🎬 [**Guion de Demostración en Vivo**](./docs/defensa-tfg/guion-demostracion-en-vivo.md): Protocolo pre-vuelo (`orders:reset-test-matrix`), flujo guiado de 4 minutos y plan de contingencia.
- ❓ [**FAQ Tribunal — Preguntas Clave**](./docs/defensa-tfg/faq-tribunal-preguntas-clave.md): 15 respuestas técnicas blindadas a preguntas de arquitectura, concurrencia, IA y testing.

### Documentación Técnica y Roadmaps de Ingeniería

- 🚀 [**Manual del Desarrollador — CI/CD**](./docs/Manual_Desarrollador_CICD.md): Entornos Docker, Dev Containers, configuración hermética y pipeline de GitHub Actions.
- 🛡️ [**Matriz de Evaluación de Riesgos MAGERIT v.3**](./docs/artefactos/matriz-evaluacion-magerit-reposaplus.md): Identificación de activos (ACT-01..06), evaluación de amenazas, efectividad de salvaguardas y riesgo residual aceptado.
- ⚡ [**Roadmap de Auditoría Técnica y Seguridad (Lighthouse & MAGERIT)**](./docs/progreso/roadmap-auditoria-tecnica-seguridad-magerit-lighthouse.md): Medición rigurosa de Core Web Vitals (SEO 100, CLS 0, Performance 95-100), sitemap dinámico y hardening HTTP.
- 🌐 [**Informe de Entorno de Red, Proxies y Túneles**](./docs/informe-entorno-ngrok.md): Resolución de contenido mixto TLS (`X-Forwarded-Proto`), bypass de interstitials y análisis de viabilidad perimetral.
- 🧪 [**Roadmap de Estrategia de Testing**](./docs/progreso/roadmap-estrategia-testing-y-pruebas-unitarias.md): Trofeo de pruebas (*Testing Trophy*), pirámide tripartita (154 tests: Pest + Playwright) y justificación metodológica.
- 📦 [**Roadmap de Checkout, Paquetería y Google OAuth**](./docs/progreso/roadmap-flujos-checkout-paqueteria-oauth.md): Pasarelas de pago, autenticación federada y neutralización de BFCache.
- 🚀 [**Roadmap de CI/CD, Releases y GitFlow**](./docs/progreso/roadmap-release-cicd-y-defensa-tfg.md): Automatización del pipeline, ciclo de promociones semánticas y trazabilidad.
- 🤖 [**Ecosistema de Agentes de IA**](./docs/Documentacion_Ecosistema_Agentes.md): Arquitectura multi-agente, subagentes especializados y gobernanza mediante *Spec-Driven Development*.
- 🧠 [**Referencia Rápida de Engram CLI**](./docs/referencia-rapida-engram.md): Arquitectura de memoria persistente para agentes IA en SQLite FTS5 y comandos del proyecto.
- 🐳 [**Referencia de Entornos: Dev Containers vs. Docker CLI Directo**](./docs/referencia-entorno-dev-containers-vs-docker-cli.md): Decisión técnica y operatividad de desarrollo.

---
*Para información específica del framework Laravel, puedes consultar el [README interno de Reposa+](./Reposa+/README.md).*
