# Referencia de Entornos: Dev Containers (IDE) vs. Docker CLI Directo (Terminal & Agentes IA)
## Guía Operativa y Registro de Decisión Técnica (ADR)
**Proyecto:** Reposa+ E-Commerce (TFG 2026)  
**Fecha de consolidación:** Septiembre de 2026  
**Estado:** Activo y Vigente  

---

## 1. Contexto y Propósito

El proyecto **Reposa+** cuenta con una infraestructura de virtualización multicapa diseñada tanto para desarrolladores humanos como para agentes de inteligencia artificial autónomos (como Antigravity CLI).

En la documentación técnica ([`Manual_Desarrollador_CICD.md`](./Manual_Desarrollador_CICD.md)) se tipifican formalmente dos entornos de desarrollo ligero basados en los mismos servicios (PHP 8.4 CLI + MySQL 8.0):
1. **Entorno 2 — Dev Containers (VS Code / Cursor):** Diseñado para desarrolladores humanos que utilizan una interfaz gráfica (IDE) con la extensión oficial de Microsoft `ms-vscode-remote.remote-containers`.
2. **Entorno 3 — Docker Compose / Docker CLI Directo:** Diseñado para trabajo en terminal pura, scripts de automatización, pipelines de CI/CD y **agentes de IA autónomos**.

El objetivo de este documento es registrar formalmente por qué en los desarrollos automatizados se utiliza la alternativa de **Docker CLI Directo** en lugar de Dev Containers, cómo se garantiza la equivalencia técnica absoluta entre ambos y cómo operar este flujo en futuros desarrollos.

---

## 2. Declaración de la Realidad Operativa: ¿Se usó la alternativa a Dev Containers?

**Sí.** Durante la ejecución de las fases de desarrollo y rediseño de Reposa+, **no se interactuó a través de la extensión gráfica de Dev Containers de VS Code**. En su lugar, se operó mediante la alternativa oficial de desarrollo ligero: **Entorno 3 (Docker Compose CLI directo vía `docker exec`)**, orquestando las tareas de backend dentro del contenedor y las tareas de frontend/memoria directamente en el host.

---

## 3. Justificación Técnica y Arquitectónica

### A. Naturaleza de los Agentes de IA Autónomos (CLI Headless)
Los agentes de desarrollo autónomo (como Google Antigravity CLI) se ejecutan a nivel de proceso en la terminal del sistema operativo anfitrión (*host*, ej. macOS `zsh`). Un agente de terminal no tiene acceso a la interfaz gráfica ni al protocolo de ventanas de VS Code/Cursor para hacer clic en *"Reopen in Container"* o adjuntar el socket gráfico del editor. La vía determinista, rápida y reproducible para interactuar con el entorno aislado es invocar el cliente de Docker desde la terminal (`docker exec ...`).

### B. Equivalencia Técnica 1:1 entre Entorno 2 y Entorno 3
No existe divergencia en el software ni en el runtime. Ambos entornos comparten exactamente la misma configuración:
* **Mismo archivo Dockerfile:** Tanto [`.devcontainer/devcontainer.json`](../Reposa+/.devcontainer/devcontainer.json) como [`docker-compose.dev.yml`](../Reposa+/docker-compose.dev.yml) construyen la imagen a partir de [`.devcontainer/Dockerfile`](../Reposa+/.devcontainer/Dockerfile) (PHP 8.4 CLI con extensiones `pdo_mysql`, `zip`, `gd`, `pcntl`, etc.).
* **Mismo nombre de contenedor:** Ambos levantan el contenedor `reposaplus-dev-app` y la base de datos `reposaplus-dev-mysql`.
* **Mismo volumen compartido (`bind mount`):** El código fuente de `Reposa+/` se monta bidireccionalmente en `/var/www/html`. Cualquier archivo modificado en el host o en el contenedor se refleja al milisegundo en el otro extremo.

Por tanto, ejecutar un comando mediante `docker exec reposaplus-dev-app php artisan ...` produce **el mismo resultado idéntico** que ejecutarlo dentro de la terminal integrada de un Dev Container en VS Code.

### C. Arquitectura Híbrida: Separación de Responsabilidades Host vs. Contenedor
El entorno de desarrollo ligero de Reposa+ opta deliberadamente por un reparto eficiente de cargas entre el contenedor y el host:

```
┌────────────────────────────────────────────────────────────────────────┐
│                        SISTEMA HOST (macOS)                            │
│                                                                        │
│  • Node.js v20+ / Vite (npm run build) ──> Compilación ultrarrápida    │
│  • Auditoría Impeccable (node detect.mjs)                              │
│  • Memoria Persistente Engram (/opt/homebrew/bin/engram)               │
│  • Control de versiones Git y claves SSH                               │
│                                                                        │
│         │ Bind Mount (.:/var/www/html)          │ docker exec          │
│         ▼                                       ▼                      │
│  ┌──────────────────────────────────────────────────────────────────┐  │
│  │             CONTENEDOR DOCKER (reposaplus-dev-app)               │  │
│  │                                                                  │  │
│  │  • PHP 8.4 CLI + Extensiones oficiales                           │  │
│  │  • Composer / Vendor PHP                                         │  │
│  │  • Laravel Artisan (serve :8000, migrate, seed)                  │  │
│  │  • Pest / PHPUnit Test Suite (Feature & Unit)                    │  │
│  │                                                                  │  │
│  │  Red interna Docker ──> MySQL 8.0 (reposaplus-dev-mysql :3306)   │  │
│  └──────────────────────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────────────────────┘
```

1. **Compilación de Assets (Vite):** Compilar Sass y JavaScript directamente en el host toma ~1.5 segundos con acceso directo al hardware, volcando los bundles a `public/build/`. Gracias al bind mount, PHP en el contenedor los sirve inmediatamente.
2. **Herramientas del Ecosistema:** El motor de memoria de agentes (**Engram CLI** en `/opt/homebrew/bin/engram`) y los scripts de auditoría de diseño residen en el host y no necesitan sobrecargar la imagen de PHP con dependencias innecesarias.

### D. Control Fino de Aislamiento de Base de Datos en Tests (Lección Engram #264 / #265)
Al ejecutar la suite de pruebas de Laravel con el trait `RefreshDatabase`, ejecutar `php artisan test` dentro del contenedor sin parámetros heredaba el entorno por defecto (`APP_ENV=local` y `DB_DATABASE=reposaplus_dev`), lo que reseteaba y borraba los datos de desarrollo tras cada test.  
La invocación directa mediante CLI permite inyectar variables de forma determinista y segura:
```bash
docker exec -e APP_ENV=testing reposaplus-dev-app php artisan test --env=testing --testsuite=Feature
```
Esto garantiza que los tests se ejecuten exclusivamente contra la base de datos aislada `reposaplus_testing` sin alterar la tienda de desarrollo.

---

## 4. Guía Práctica de Comandos para Futuros Desarrollos

Para replicar este flujo de trabajo ágil y robusto en cualquier sesión futura (humana o automatizada por agentes):

### 1. Iniciar el entorno ligero
Desde la carpeta raíz del repositorio:
```bash
cd Reposa+
docker compose -f docker-compose.dev.yml up -d
```
Verificar que los contenedores estén activos:
```bash
docker ps --filter "name=reposaplus-dev"
# Debe mostrar reposaplus-dev-app (puerto 8000) y reposaplus-dev-mysql (puerto 3306)
```

### 2. Ejecutar comandos de backend (PHP / Laravel)
```bash
# Migraciones y seeders
docker exec reposaplus-dev-app php artisan migrate:fresh --seed --force

# Limpieza y refresco de caché
docker exec reposaplus-dev-app php artisan optimize:clear

# Comprobaciones rápidas de código PHP
docker exec reposaplus-dev-app php -r "echo 'PHP OK';"
```

### 3. Ejecutar la suite de tests garantizando aislamiento
```bash
docker exec -e APP_ENV=testing reposaplus-dev-app php artisan test --env=testing --testsuite=Feature
```

### 4. Compilar assets y verificar diseño (desde el host)
```bash
cd Reposa+
npm run build

# Auditoría con el detector Impeccable (desde la raíz del repo)
cd ..
node .agent/skills/impeccable/scripts/detect.mjs --json Reposa+/resources/views/ Reposa+/resources/sass/
```

### 5. Registrar memoria de sesión (desde el host)
```bash
engram save "<Título>" "<Detalle>" --project reposaplus-tfg
```

---

## 5. Tabla Comparativa de Entornos para Toma de Decisiones

| Criterio | Dev Containers (Entorno 2) | Docker CLI Directo (Entorno 3) |
|---|:---:|:---:|
| **Ideal para** | Desarrollador humano con VS Code / Cursor | Agentes autónomos de IA, CI/CD, scripts bash |
| **Requiere IDE gráfico** | Sí (extensión Dev Containers) | **No (100% terminal)** |
| **Velocidad de arranque** | Media (~15s vinculando socket IDE) | **Instantánea (~2s)** |
| **Entorno PHP/MySQL** | Idéntico (`reposaplus-dev-app`) | Idéntico (`reposaplus-dev-app`) |
| **Tooling Host disponible** | Requiere reenviar puertos/túneles | **Acceso directo nativo (Engram, Node)** |
| **Aislamiento de BD en tests** | Requiere configuración en IDE | **Totalmente explícito por comando** |

---

*Documento aprobado y archivado como referencia técnica para el Trabajo de Fin de Grado (TFG 2026) Reposa+.*
