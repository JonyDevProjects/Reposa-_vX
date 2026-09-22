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

> **Documentación completa de Engram:** Ver [docs/referencia-rapida-engram.md](referencia-rapida-engram.md)

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

## 6. Caso Práctico y Lección de Red: Conexión a MySQL desde Extensiones del IDE

Durante las sesiones de desarrollo se exploró la visualización gráfica de los registros de la base de datos (alternativa tipo phpMyAdmin integrada en el editor). Este caso práctico aportó una valiosa lección de topología de red Docker que queda registrada a continuación:

### A. El Problema Observado
Al intentar conectar la extensión de base de datos de VS Code/Cursor (**Database Client** — `cweijan.vscode-database-client2`) configurando:
* **Host:** `127.0.0.1` *(o localhost)*
* **Port:** `3306`
* **Username:** `root`
* **Password:** `secret`
* **Database:** `reposaplus_dev`

**La conexión no era posible y era rechazada.**

### B. Causa Raíz: Topología de Red Host vs. Dev Container
La causa residía en el contexto de ejecución de la extensión:
* Al tener VS Code/Cursor abierto en modo **Dev Container** (*Reopen in Container*), todas las extensiones instaladas se ejecutan **dentro del contenedor de la aplicación** (`reposaplus-dev-app`), no en el sistema operativo anfitrión (macOS).
* Dentro del contenedor `reposaplus-dev-app`, la dirección loopback `127.0.0.1` apunta a sí mismo (al entorno PHP), donde no se está ejecutando el servicio de base de datos.
* El servicio MySQL reside en un contenedor hermano (`reposaplus-dev-mysql`) conectado a través de la red bridge de Docker (`reposa_default`).

### C. La Solución Confirmada y en Funcionamiento
Para conectar extensiones de base de datos **desde dentro de Dev Containers**, se debe emplear la resolución DNS interna de Docker:
* **Host:** `mysql-dev` *(o `reposaplus-dev-mysql`)* ✅ **(Opción que funciona con Dev Containers)**
* **Port:** `3306`
* **Username:** `root`
* **Password:** `secret`
* **Database:** `reposaplus_dev`

> **Regla nemotécnica de conexión:**
> * **Desde dentro de Dev Containers (extensiones de VS Code/Cursor):** Usar `Host: mysql-dev`.
> * **Desde fuera de Dev Containers (aplicaciones de escritorio en macOS como TablePlus, DBeaver):** Usar `Host: 127.0.0.1`.

### D. Ajuste de Compatibilidad de Autenticación (MySQL 8)
MySQL 8.0 utiliza por defecto el plugin `caching_sha2_password`. Muchos drivers de extensiones basados en Node.js presentan problemas de compatibilidad al negociar este protocolo sin certificados RSA.  
Para garantizar compatibilidad universal e inmediata, se configuró el usuario `root` con el plugin clásico:
```sql
ALTER USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'secret';
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'secret';
FLUSH PRIVILEGES;
```
Este ajuste permite la conexión inmediata desde cualquier cliente o extensión sin alterar el comportamiento de Laravel ni la suite de tests (verificado al 100% con 86 tests Feature pasando).

### E. Matriz de Alternativas Contempladas para Visualización de Datos

| Alternativa | Descripción | Configuración de Host | Evaluación |
|---|---|:---:|---|
| **1. Extensión IDE en Dev Container (Database Client)** | Inspección y edición visual directa en pestañas de VS Code/Cursor sin salir del entorno. | `mysql-dev:3306` | **Adoptada y funcionando con éxito**. La más integrada para desarrollo. |
| **2. phpMyAdmin oficial en contenedor Docker** | Despliegue de un contenedor ligero (`phpmyadmin/phpmyadmin`) en la red `reposa_default` expuesto en el puerto 8080. | Web: `http://localhost:8080` (PMA_HOST=`reposaplus-dev-mysql`) | Excelente alternativa web sin instalar extensiones en el IDE. Comando: `docker run -d --name reposaplus-pma --network reposa_default -p 8080:80 -e PMA_HOST=reposaplus-dev-mysql -e PMA_PORT=3306 phpmyadmin/phpmyadmin`. |
| **3. Clientes GUI de Escritorio en Mac (TablePlus / DBeaver / Beekeeper)** | Clientes nativos de macOS conectándose a través del puerto publicado `3306:3306`. | `127.0.0.1:3306` | Muy potente y rápida para gestión avanzada de bases de datos desde el host. |
| **4. Herramientas CLI nativas de Laravel (Artisan / Tinker)** | `docker exec -it reposaplus-dev-app php artisan db:table <tabla>` y `php artisan tinker`. | Interno Laravel | Ideal para consultas rápidas o scripts de verificación sin interfaz gráfica. |

---

*Documento aprobado y archivado como referencia técnica para el Trabajo de Fin de Grado (TFG 2026) Reposa+.*
