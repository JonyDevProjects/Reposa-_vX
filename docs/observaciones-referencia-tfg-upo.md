# Observaciones y Pautas de Referencia Extraídas del TFG de Referencia UPO

**Documento Analizado:** [`EscobarVazquezNMemoria.pdf`](file:///Users/jonathanquishpe/Downloads/EscobarVazquezNMemoria.pdf) (307 páginas)  
**Proyecto de Referencia:** *“Plataforma para Búsqueda y Reserva de Encuentros Deportivos” (FutPlay)*  
**Autora:** Natalia Escobar Vázquez (77844081Y)  
**Tutor:** Rubén Pérez Chacón  
**Código TFG:** 25-26-C13  
**Institución:** Escuela Politécnica Superior — Universidad Pablo de Olavide (UPO), Sevilla  
**Titulación:** Grado en Ingeniería Informática en Sistemas de Información (Convocatoria: Mayo 2026)  
**Fecha de Análisis:** 16 de septiembre de 2026  
**Propósito:** Servir como guía canónica de estructura, estilo y metodología para la formalización definitiva de la documentación del TFG **Reposa+**.

---

## 1. Macro-Estructura Documental del TFG en la UPO

El documento no sigue una narrativa continua tradicional, sino un **compendio de 4 volúmenes normalizados** encuadernados bajo un único paginado continuo (1 a 307 páginas):

```text
Volumen Completo de Entrega TFG (307 páginas)
│
├── 1. MEMORIA EJECUTIVA O TRONCAL (Páginas 1 – 20)
│   └── Síntesis de alto nivel del proyecto para el tribunal (lectura rápida obligatoria).
│
├── 2. ANEXO I: PLAN DE PROYECTO (Páginas 21 – 41)
│   └── Planificación, estimación económica (400 h), WBS/EDT, gestión de riesgos y planes auxiliares.
│
├── 3. ANEXO II: DOCUMENTO DE ANÁLISIS — MÉTRICA v3 (Páginas 42 – 181)
│   └── Requisitos (RF/RNF), Casos de Uso (CU), Clases de Negocio (CN), Interfaces (IU) e Informes (IF).
│
└── 4. ANEXO III: DOCUMENTO DE DISEÑO — MÉTRICA v3 (Páginas 182 – 307)
    └── Arquitectura física, modelo físico de datos (SQL), clases de diseño (CL) y especificaciones técnicas.
```

---

## 2. Desglose Detallado por Bloque

---

### Bloque 1: Memoria Ejecutiva (Páginas 1 a 20)
Es el núcleo que leen inicialmente todos los miembros del tribunal. Su extensión está estrictamente contenida en **20 páginas**.

* **Página 1 | Portada Institucional:**
  * Cabecera con tipografía oficial: `UNIVERSIDAD PABLO DE OLAVIDE SEVILLA / ESCUELA POLITÉCNICA SUPERIOR / GRADO EN INGENIERÍA INFORMÁTICA...`.
  * Logotipo corporativo de la Universidad Pablo de Olavide centrado en alta resolución.
  * Título del TFG entre comillas y en mayúsculas destacadas.
  * Datos del Alumno, Tutor, Código de TFG (`25-26-C13`) y Mes/Año de convocatoria (`MAYO 2026`).
* **Página 2 | Agradecimientos:** Breve dedicatoria formal y personal.
* **Página 3 | Índice General:** Índice numerado que referencia tanto las secciones de la memoria como los puntos de entrada a cada uno de los tres Anexos.
* **Páginas 4 a 7 | Introducción, Contexto y Estado del Arte:**
  * Planteamiento del problema en el sector.
  * Análisis de soluciones comerciales competidoras.
  * **Tabla Comparativa de Competidores:** Matriz de características enfrentando 2 soluciones líderes del mercado frente a la solución del TFG (`SI / NO`).
* **Página 8 | Transición a Anexos:** Declaración formal indicando que el Plan de Proyecto reside en el Anexo I, el Análisis en el Anexo II y el Diseño en el Anexo III.
* **Páginas 9 a 14 | Codificación y Algoritmos Singulares:**
  * Justificación del stack tecnológico.
  * Estructura modular de paquetes de código.
  * Estrategia de validación en 4 niveles (Cliente, Formularios, Modelo y Restricciones Físicas de BD).
  * Explicación matemática del algoritmo central del proyecto (en su caso el cálculo ELO; en Reposa+ corresponde al algoritmo de tarifas logísticas y al servicio reactivo `CartCalculator`).
* **Páginas 15 a 16 | Pruebas:** Resumen cualitativo de pruebas funcionales, usabilidad, accesibilidad y seguridad.
* **Páginas 17 a 19 | Conclusiones y Trabajo Futuro:**
  * Conclusiones académicas sobre el TFG (retos de desarrollo individual, gestión del tiempo, aplicación transversal del grado).
  * Conclusiones sobre el producto final desarrollado.
  * Posibles mejoras futuras ordenadas temáticamente.
* **Página 20 | Referencias Bibliográficas:**
  * Citas de las asignaturas del Grado en la UPO (*Diseño de Bases de Datos*, *Ingeniería del Software I*, *Ingeniería de Proyectos*, *Integración de Tecnologías*).
  * Manuales técnicos de referencia con ISBN.

---

### Bloque 2: Anexo I — Plan de Proyecto (Páginas 21 a 41)
Documento formal de gestión de proyectos conforme a las directrices de la asignatura *Ingeniería de Proyectos*.

* **Control de Documento:** Tabla en la segunda página del anexo con Metadatos (Proyecto, Denominación, Fecha, Edición, Grupo, Autores) y tabla de *Registro de Cambios* (Versión, Descripción, Fecha).
* **Catálogo de Objetivos (`OBJ-xxx`):** Cada objetivo se formaliza en una ficha tabular cerrada:
  * `Código`, `Versión`, `Autores`, `Fuente`, `Descripción`, `Importancia (Alta/Media/Baja)`, `Estado (Aprobado)`, `Comentarios`.
* **Organigrama y Simulación de Roles:**
  * Diagrama jerárquico que descompone el esfuerzo en perfiles profesionales de mercado: **Jefe de Proyecto**, **Analista**, **Arquitecto de Software**, **Programador Backend** y **Programador Frontend**.
  * Todos los roles son asumidos por el alumno, pero se describen sus responsabilidades de forma desacoplada.
* **Metodología y Calendario de Tutorías:**
  * Justificación del modelo de ciclo de vida (Cascada / Métrica v3) y registro del ritmo de reuniones quincenales con el tutor.
* **Programa de Trabajo y Presupuesto Económico (Norma de 400 Horas):**
  * **EDT / WBS:** Diagrama gráfico en bloques (Inicio $\rightarrow$ Análisis $\rightarrow$ Diseño $\rightarrow$ Desarrollo $\rightarrow$ Despliegue).
  * **Tabla de Perfiles y Tarifas de Mercado:**
    * Jefe de Proyecto: 40 h a 45 €/h = 1.800 €
    * Analista: 60 h a 40 €/h = 2.400 €
    * Arquitecto Software: 60 h a 40 €/h = 2.400 €
    * Programador Backend: 130 h a 30 €/h = 3.900 €
    * Programador Frontend: 110 h a 30 €/h = 3.300 € (en la referencia 3.900 €)
    * **Total de Esfuerzo Estimado:** Exactamente **400 horas** con un coste simulado de **13.800 €**.
  * **Tabla de Costes Amplificada:** Desglose detallado tarea por tarea (código de tarea, descripción, miembro del equipo asignado, horas invertidas y coste en euros).
* **Evaluación y Planificación de Riesgos:**
  * Tabla exhaustiva con: *Origen del riesgo*, *Descripción*, *Probabilidad (Alta/Media/Baja)*, *Severidad*, *Plan de contingencia / Acciones propuestas* y *Prioridad*.
* **Planes de Gestión Auxiliares:**
  * Breves directrices para: Plan de Calidad, Plan de Seguridad, Plan de Gestión de la Configuración (uso de Git/GitHub) y Plan de Pruebas.
* **Temas Abiertos y Decisiones Pendientes:**
  * Registro de deuda técnica o decisiones postergadas para versiones post-grado.

---

### Bloque 3: Anexo II — Documento de Análisis (Páginas 42 a 181)
El anexo más extenso (140 páginas), siguiendo de forma estricta el estándar de la metodología **Métrica v3**.

* **Especificación Formal de Requisitos:**
  * **Requisitos Funcionales (`RF-xxx`):** Ficha tabular con *Código*, *Versión*, *Autores*, *Fuentes*, *Objetivos asociados (`OBJ-xxx`)*, *Descripción con viñetas*, *Actores participantes* y *Comentarios*.
  * **Requisitos No Funcionales (`RNF-xxx`):** Clasificados en Seguridad y Autenticación, Integridad Transaccional, Usabilidad y Rendimiento/Mantenibilidad.
  * **Matriz de Trazabilidad Objetivos – Requisitos:** Tabla de doble entrada con cruce de marcas (`X`).
* **Especificación de Casos de Uso:**
  * Identificación y descripción textual de cada **Actor del Sistema**.
  * **Diagrama Global de Casos de Uso (UML):** Representación visual con relaciones de herencia, `<<include>>` y `<<extend>>`.
  * **Fichas Individuales de Casos de Uso (`CU-xxx`):**
    * *Código*, *Nombre*, *Versión*, *Autores*, *Descripción*, *Dependencias (`<<include>>`/`<<extend>>`)*, *Actores*, *Precondición*, *Postcondición*, *Puntos de Extensión*, *Importancia*, *Frecuencia*.
    * **Flujo Normal:** Pasos cronológicos numerados (`1. El actor... 2. El sistema...`).
    * **Flujos Alternativos:** Casos de excepción numerados (`3.a. Datos inválidos: 1. El sistema informa... 2. Regresa al paso 2`).
    * *Observaciones*.
  * **Matriz de Trazabilidad Requisitos – Casos de Uso.**
* **Subsistemas de Análisis y Clases de Negocio:**
  * Partición del sistema en subsistemas lógicos (`SUB01` a `SUB05`).
  * Matriz de relaciones entre subsistemas y matriz de Actores – Subsistemas.
  * **Fichas de Clases de Negocio (`CN-xxx`):** Diagrama de clases local del subsistema + ficha por clase con *Responsabilidades*, tabla de *Atributos* (Nombre, Tipo, Descripción) y lista de *Casos de Uso en los que participa*.
* **Catálogo Exhaustivo de Interfaces de Usuario (`IU-xxx`):**
  * Para **cada pantalla del sistema** (desde menús públicos hasta modales de administración):
    1. Captura de pantalla o mockup a color centrado.
    2. Ficha tabular `IU-xxx`:
       * *Descripción del módulo*.
       * **Tabla de Campos:** *Nombre*, *Tipo de Datos*, *Editable/Consulta*, *Obligatorio (Sí/No)*, *Descripción de negocio*.
       * **Tabla de Botones/Enlaces:** *Nombre del control*, *Acción disparada*.
  * **Matriz de Trazabilidad Interfaces – Actores.**
* **Catálogo de Módulos de Informe (`IF-xxx`):**
  * Especificación de cada reporte, gráfica o listado analítico del sistema: captura visual, descripción, tabla de datos y ordenación (`1, Ascendente / 2, Descendente`), y campos acumulados del resumen.
  * Matriz de Trazabilidad Informes – Actores.
* **Plan de Pruebas de Aceptación y Glosario:**
  * Tabla de Pruebas de Aceptación del Sistema: *Descripción de la prueba* vs. *Resultado esperado de aceptación*.
  * Glosario estructurado dividiendo términos del dominio de negocio frente a términos puramente tecnológicos.

---

### Bloque 4: Anexo III — Documento de Diseño (Páginas 182 a 307)
Enfocado en la traslación de los modelos lógicos a especificaciones físicas de construcción y despliegue.

* **Definición Física y Diagrama de Despliegue:**
  * Diagrama de nodos físicos (Cliente Web, Servidor de Aplicación, Base de Datos, APIs externas).
  * Requisitos No Funcionales específicos de arquitectura, operación (`RNF-OP`) y seguridad física (`RNF-SEG`).
* **Modelo Físico de Datos (Esquema Relacional Exhaustivo):**
  * Diagrama Entidad-Relación físico completo con cardinalidades.
  * **Ficha Tabular por Tabla Física:**
    * *Nombre físico* (ej. `auth_user`, `orders`, `products`).
    * *Descripción*.
    * **Tabla de Atributos:** *Campo*, *Tipo de dato SQL (Varchar, Integer, Decimal, Boolean, DateTime)*, *Obligatorio (S/N)*, *Descripción*.
    * **Clave Primaria:** Nombre de la constraint, columnas y secuencia.
    * **Claves Ajenas (FK):** Nombre de la constraint, tabla destino y columna vinculada.
    * **Claves Únicas:** Nombre del índice y columnas.
    * **Restricciones CHECK:** Reglas de validación física en base de datos (ej. `price >= 0`, `matches_played = wins + losses + draws`).
* **Diseño de Clases de Controladores (`CL-xxx`):**
  * Diagrama de clases de diseño por subsistema.
  * Ficha tabular por controlador: *Código*, *Nombre del Controlador*, *Descripción*, *Atributos de sesión/request* y **Tabla de Operaciones/Métodos** (Nombre del método, visibilidad, parámetros y descripción algorítmica).
* **Especificaciones de Construcción y Migraciones:**
  * Estructura del árbol de directorios de la aplicación.
  * Procedimiento paso a paso para levantar el entorno de desarrollo (`pip install`, migraciones de base de datos, levantamiento de servidores).
  * Procedimiento de carga inicial de datos (Seeders / fixture de superusuario).
* **Requisitos de Implantación:**
  * Especificaciones para paso a producción real: requisitos de formación por perfil, infraestructura de hosting, certificado SSL/TLS obligatorio y nombre de dominio.

---

## 3. Elementos de Formato e Identidad Visual para Replicar

Para que la memoria de Reposa+ sea visualmente indistinguible del estándar oficial de la Escuela Politécnica Superior de la UPO:

1. **Cabecera Institucional en Anexos:**
   * Margen superior: Logo oficial azul de la Universidad Pablo de Olavide (izquierda), caja de texto centrada con:
     ```text
     [Anexo X: Nombre del Documento] - Reposa+
     JONATHAN QUISPE — [DNI]
     ```
     y caja a la derecha con `Página X de Y`.
2. **Tablas con Formato Estricto:**
   * Cabecera de tabla con fondo gris claro (`#e2e8f0` o similar) y texto en negrita.
   * Bordes delgados (`0.5pt` / gris neutro).
   * Columnas con anchos proporcionales y justificación de texto limpia.
3. **Matrices de Trazabilidad:**
   * Siempre presentes tras cada etapa para justificar que ningún requisito quedó huérfano ni ningún caso de uso carece de base funcional.

---

## 4. Gap Analysis: Oportunidades y Ventajas Competitivas de Reposa+

Al contrastar la referencia de Natalia con lo desarrollado en **Reposa+**, se evidencia una clara ventaja técnica en nuestro proyecto que debemos potenciar al redactar la memoria:

| Eje de Evaluación | Proyecto de Referencia (FutPlay) | Reposa+ ([`docs/Memoria_Proyecto.md`](file:///Users/jonathanquishpe/JoniDev/Reposa+_TFG/docs/Memoria_Proyecto.md)) | Estrategia de Redacción para Reposa+ |
|---|---|---|---|
| **Estrategia de Testing** | Pruebas manuales en localhost; sin automatización ni pipelines. | **Testing Trophy moderno:** 141 tests Pest (29 Unit + 112 Feature) ejecutados en 2.89s, más 8 tests Playwright E2E. | **Destacar con rotundidad:** Es el punto más débil de la referencia y nuestro mayor logro de ingeniería de software. |
| **Integración Continua (CI/CD)** | No implementada (solo repositorio GitHub sin Actions). | **Pipeline completo en GitHub Actions:** 5 jobs automatizados (Pint, Unit SQLite, Feature MySQL/Redis, Vite, Playwright). | **Documentar con capturas:** Demostrar madurez DevOps en la nube. |
| **Arquitectura de Ejecución** | Monolito local básico con base de datos SQLite y servidor Django simple. | **Stack de 7 microservicios Docker:** Nginx, PHP-FPM 8.4, MySQL 8.0, Redis 7, MailHog, MinIO y Load Balancer. | **Diagrama de despliegue rico:** Mostrar el desacoplamiento real de infraestructura. |
| **Integridad Transaccional** | Transacciones atómicas de Django básicas; sin pasarela de pagos real. | **Bloqueo pesimista `lockForUpdate()`:** prevención de sobreventas, Stripe Checkout con webhooks asíncronos HMAC e idempotencia. | **Poner en valor:** Detallar el blindaje ante condiciones de carrera en el Documento de Diseño. |
| **Ecosistema de IA** | No contemplado. | **Uso pionero de Agentes Autónomos (Google Antigravity SDK):** El ingeniero como director técnico y arquitecto. | **Capítulo vanguardista:** Factor de innovación que prestigia el proyecto ante el tribunal. |
| **Metodología de Requisitos (Métrica v3)** | Muy desarrollada y exhaustiva (140 págs de fichas `OBJ`, `RF`, `CU`, `IU`). | Narrativa técnica y casos de uso en capítulos, pero sin fichas normalizadas. | **Área de adopción directa:** Adoptar las plantillas de fichas para el Anexo II y Anexo III de Reposa+. |
| **Estimación Económica** | Presupuesto formal de 400 horas valoradas en 13.800 €. | Registro de sprints ágiles, sin tabla económica formal de horas/hombre. | **Área de adopción directa:** Incorporar la tabla de 400 horas y costes según tarifas estándar en el Anexo I. |

---

## 5. Hoja de Ruta para la Adaptación Documental de Reposa+

Con base en estas observaciones, la documentación de Reposa+ se organizará de la siguiente manera:

1. **Memoria Principal (`docs/Memoria_Proyecto.md`):**
   - Reducir/sintetizar el cuerpo principal a ~20-25 páginas ejecutivas para el tribunal (Capítulos 1 al 7), incorporando la tabla comparativa de competidores del sector *Sleep Tech*.
2. **Generación del Anexo I (Plan de Proyecto):**
   - Catálogo de objetivos `OBJ-001` a `OBJ-008`.
   - Organigrama de perfiles asumidos y estimación de **400 horas** (presupuesto económico ~14.000 €).
   - Matriz de riesgos y planes auxiliares (destacando el Plan de Calidad y el entorno CI/CD).
3. **Generación del Anexo II (Documento de Análisis):**
   - Fichas Métrica v3 de Requisitos Funcionales (`RF-xxx`) y No Funcionales (`RNF-xxx`).
   - Fichas de Casos de Uso (`CU-xxx`) de los flujos de Catálogo, Carrito, Guest Checkout, Stripe y Admin.
   - Fichas de Interfaces de Usuario (`IU-xxx`) con las capturas de pantalla reales del storefront "The Midnight Sanctuary" y del panel de pedidos.
4. **Generación del Anexo III (Documento de Diseño):**
   - Diagrama de despliegue Docker de los 7 contenedores.
   - Fichas físicas de las tablas de base de datos (`products`, `orders`, `order_items`, `shipments`, `users`, `refunds`, vistas SQL `v_order_summary`).
   - Controladores de diseño (`CartController`, `ProductController`, `AdminController`, `ShippingController`) y especificaciones de construcción.
