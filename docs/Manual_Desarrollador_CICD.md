# Manual del Desarrollador — CI/CD y Entornos de Desarrollo

## Indice

1. [Vision General de Entornos](#1-vision-general-de-entornos)
2. [Entorno 1: Desarrollo Local (Manual)](#2-entorno-1-desarrollo-local-manual)
3. [Entorno 2: Dev Containers (VS Code / Cursor)](#3-entorno-2-dev-containers-vs-code--cursor)
4. [Entorno 3: Docker Compose — Desarrollo Ligero](#4-entorno-3-docker-compose--desarrollo-ligero)
5. [Entorno 4: Docker Compose — Produccion (Stack Completo)](#5-entorno-4-docker-compose--produccion-stack-completo)
6. [Entorno 5: Docker Compose — Tests E2E](#6-entorno-5-docker-compose--tests-e2e)
7. [Entorno 6: CI/CD con GitHub Actions](#7-entorno-6-cicd-con-github-actions)
8. [Tabla Resumen de Puertos y Servicios](#8-tabla-resumen-de-puertos-y-servicios)
9. [Credenciales de Prueba](#9-credenciales-de-prueba)
10. [Guia de Seleccion de Entorno](#10-guia-de-seleccion-de-entorno)
11. [Comandos Utiles de Referencia Rapida](#11-comandos-utiles-de-referencia-rapida)
12. [Troubleshooting](#12-troubleshooting)

---

## 1. Vision General de Entornos

Reposa+ dispone de **6 formas distintas** de ejecutarse y probarse. Cada una esta disenada para un contexto especifico del ciclo de desarrollo:

```
┌─────────────────────────────────────────────────────────────────┐
│                    CICLO DE DESARROLLO                          │
│                                                                 │
│  ┌──────────┐   ┌──────────────┐   ┌────────────────────────┐  │
│  │  Codigo  │──>│  Desarrollo  │──>│  Testing                │  │
│  │  (Git)   │   │  (Local/Dev) │   │  (Feature/E2E)         │  │
│  └──────────┘   └──────────────┘   └────────────────────────┘  │
│       │                │                      │                 │
│       │                ▼                      ▼                 │
│       │         ┌──────────────┐   ┌────────────────────────┐  │
│       │         │ Dev Container│   │  CI/CD (GitHub Actions)│  │
│       │         │  o Docker    │   │  Lint + Tests + Build  │  │
│       │         └──────────────┘   └────────────────────────┘  │
│       │                                      │                 │
│       │                                      ▼                 │
│       │                             ┌────────────────────────┐ │
│       └────────────────────────────>│  Produccion (Docker)   │ │
│                                     │  7 servicios           │ │
│                                     └────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

### Archivos de configuracion por entorno

| Entorno | Archivo principal | Base de datos | Puerto app |
|---------|-------------------|---------------|------------|
| Local manual | `.env` | SQLite o MySQL local | 8000 |
| Dev Container | `.env.dev` | MySQL (contenedor) | 8000 |
| Docker dev | `docker-compose.dev.yml` | MySQL (contenedor) | 8000 |
| Docker produccion | `docker-compose.yml` | MySQL (contenedor) | 80 |
| Docker E2E | `docker-compose.e2e.yml` | MySQL (contenedor) | 8081 |
| CI/CD | `.env.example` | SQLite (en memoria) | — |

---

## 2. Entorno 1: Desarrollo Local (Manual)

### Cuando usarlo

- Ediciones rapidas de codigo sin levantar Docker
- Trabajo con SQLite para prototipado rapido
- Cuando ya tienes PHP 8.4, Composer y Node.js instalados

### Requisitos previos

```bash
php -v       # >= 8.4
composer -V  # >= 2.x
node -v      # >= 18
npm -v       # >= 9
```

Extensiones PHP necesarias: `pdo_mysql`, `mbstring`, `xml`, `curl`, `zip`, `dom`, `gd`, `bcmath`, `intl`, `sockets`, `redis` (opcional).

### Pasos

```bash
# 1. Entrar en la carpeta del proyecto Laravel
cd Reposa+

# 2. Instalar dependencias
composer install
npm install
npm run build

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Base de datos (elegir una)

# Opcion A: SQLite (rapida, sin instalacion extra)
touch database/database.sqlite
# Editar .env: DB_CONNECTION=sqlite

# Opcion B: MySQL local (ya instalado)
# Editar .env con credenciales de tu MySQL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=reposaplus
# DB_USERNAME=root
# DB_PASSWORD=tu_password

# 5. Migrar y sembrar datos
php artisan migrate:fresh --seed

# 6. Arrancar servidor
php artisan serve
```

La app estara disponible en `http://localhost:8000`.

### Limitaciones

- No incluye Redis, MailHog ni MinIO
- Los emails se pierden (no hay servidor SMTP)
- Las colas de jobs se ejecutan de forma sincrona (no realista)

---

## 3. Entorno 2: Dev Containers (VS Code / Cursor)

### Cuando usarlo

- Desarrollo diario con todas las dependencias aisladas en Docker
- Trabajo en equipo con configuracion estandarizada
- Cuando no quieres instalar PHP/MySQL en tu maquina local
- IDE integrado con IntelliSense, debugging y terminal en el contenedor

### Requisitos previos

- **VS Code** o **Cursor** con extension [Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)
- **Docker Desktop** o **OrbStack** corriendo

### Pasos

1. Abrir **solo la carpeta `Reposa+`** en VS Code/Cursor (no la raiz del repo)
2. Esperar la notificacion **"Reopen in Container"** y hacer clic
3. El IDE ejecutara automaticamente:
   - `composer install`
   - Copia `.env.dev` a `.env`
   - Generacion de `APP_KEY`
   - `php artisan migrate:fresh --seed`
4. Abrir una terminal integrada y ejecutar:
   ```bash
   php artisan serve --host=0.0.0.0
   ```
5. Acceder a `http://localhost:8000`

### O sin IDE (solo Docker Compose)

```bash
cd Reposa+

# Levantar contenedores
docker compose -f docker-compose.dev.yml up -d

# Configurar dentro del contenedor
docker exec -it reposaplus-dev-app sh -c \
  "composer install --no-interaction && cp -n .env.dev .env && php artisan key:generate --force"

# Migrar y sembrar
docker exec -it reposaplus-dev-app php artisan migrate:fresh --seed --force

# Arrancar servidor
docker exec -it reposaplus-dev-app php artisan serve --host=0.0.0.0

# Detener
docker compose -f docker-compose.dev.yml down
```

### Servicios incluidos

| Servicio | Puerto | Proposito |
|----------|--------|-----------|
| app (PHP 8.4 CLI) | 8000 | Servidor de desarrollo |
| mysql-dev | 3306 | Base de datos MySQL 8.0 |

### Ventajas frente al stack completo

- Arranque rápido (~10s vs ~60s)
- Solo 2 contenedores (app + mysql)
- Código montado desde host (ediciones en tiempo real)
- Extensiones del IDE funcionando dentro del contenedor

### Conexión a MySQL desde Extensiones del IDE (Database Client / TablePlus)

- **Dentro de Dev Containers (extensiones de VS Code/Cursor):** El Host debe ser `mysql-dev` (o `reposaplus-dev-mysql`), **NO** `127.0.0.1`. Esto se debe a que la extensión corre dentro del contenedor `reposaplus-dev-app` y debe resolver el servicio MySQL vía DNS interno de Docker.
  * Host: `mysql-dev` | Puerto: `3306` | Usuario: `root` | Contraseña: `secret` | BD: `reposaplus_dev`
- **Desde fuera de Dev Containers (aplicaciones de escritorio en macOS):** El Host sí es `127.0.0.1`.

---

## 4. Entorno 3: Docker Compose — Desarrollo Ligero

Este entorno es idéntico al Dev Container pero ejecutado directamente desde la terminal, sin necesidad de IDE. Es el **entorno estándar utilizado por agentes de IA autónomos (Antigravity CLI)** y scripts de automatización.

> 📘 **Referencia Arquitectónica:** Para un análisis detallado sobre la equivalencia técnica 1:1 entre Dev Containers y Docker CLI Directo, el reparto de responsabilidades host-contenedor y la justificación de uso en agentes de IA, consulta [**Referencia de Entornos: Dev Containers vs. Docker CLI Directo**](./referencia-entorno-dev-containers-vs-docker-cli.md).

### Cuando usarlo

- Desarrollo sin IDE (terminal puro)
- Agentes de IA autónomos (Antigravity CLI / scripts headless)
- CI local o scripts de automatización
- Verificación rápida del entorno Docker de desarrollo

### Comandos

```bash
cd Reposa+

# Levantar
docker compose -f docker-compose.dev.yml up -d

# Ver estado
docker compose -f docker-compose.dev.yml ps

# Ejecutar comandos artisan
docker exec reposaplus-dev-app php artisan migrate:fresh --seed --force
docker exec reposaplus-dev-app php artisan serve --host=0.0.0.0

# Ejecutar tests Feature
docker exec reposaplus-dev-app php artisan test --testsuite=Feature

# Ver logs
docker compose -f docker-compose.dev.yml logs -f app

# Detener y limpiar
docker compose -f docker-compose.dev.yml down -v
```

### Variables de entorno (.env.dev)

Las variables clave estan preconfiguradas en el compose:

```env
DB_CONNECTION=mysql
DB_HOST=mysql-dev
DB_DATABASE=reposaplus_dev
QUEUE_CONNECTION=sync      # Sin Redis,Jobs sincronos
MAIL_MAILER=log            # Emails se escriben en log
CACHE_STORE=database       # Sin Redis
```

---

## 5. Entorno 4: Docker Compose — Produccion (Stack Completo)

### Cuando usarlo

- Verificar que la app funciona con el stack completo de produccion
- Probar Redis, colas de jobs, MailHog y MinIO
- Simular el entorno de despliegue real
- Pruebas de integracion con servicios externos

### Servicios

| Servicio | Container | Puerto | Proposito |
|----------|-----------|--------|-----------|
| nginx-lb | reposaplus_lb | :80 | Load balancer reverse proxy |
| app | reposaplus_app | interno | Nginx + PHP-FPM (produccion) |
| queue | reposaplus_queue | interno | Worker de colas Laravel |
| mysql | reposaplus_mysql | :3307 | Base de datos MySQL 8.0 |
| redis | reposaplus_redis | :6380 | Sesiones, cache, queue (persistente) |
| mailhog | reposaplus_mailhog | :8025 (web) / :1025 (SMTP) | Captura de emails |
| minio | reposaplus_minio | :9000 (API) / :9001 (Console) | Almacenamiento S3-compatible |

### Comandos

```bash
cd Reposa+

# Levantar stack completo
docker compose up -d

# Verificar que todos estan healthy
docker compose ps

# Migrar y sembrar
docker exec reposaplus_app php artisan migrate:fresh --seed --force

# La app estara en http://localhost:80
# MailHog en http://localhost:8025
# MinIO Console en http://localhost:9001 (minioadmin/minioadmin)

# Ejecutar tests Feature dentro del contenedor
docker exec reposaplus_app php artisan test --testsuite=Feature

# Ver logs de un servicio
docker compose logs -f app
docker compose logs -f queue

# Detener (los datos persisten en volumenes)
docker compose down

# Detener y eliminar datos (limpieza total)
docker compose down -v
```

### Variables de entorno (.env)

El compose usa el archivo `.env` de la raiz de `Reposa+/`. Configuracion recomendada para este entorno:

```env
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=reposaplus
DB_USERNAME=root
DB_PASSWORD=root
QUEUE_CONNECTION=redis
REDIS_HOST=redis
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
FILESYSTEM_DISK=local
```

### Datos persistentes

Los volumenes mantienen los datos entre reinicios:
- `mysql_data` — Base de datos
- `redis_data` — Cache y sesiones
- `minio_data` — Archivos S3
- `storage_data` — Storage de Laravel

Para limpiar todo: `docker compose down -v`

---

## 6. Entorno 5: Docker Compose — Tests E2E

### Cuando usarlo

- Ejecutar la suite completa de tests E2E (Pest + Playwright)
- Testing de flujos criticos: carrito, checkout, webhooks, admin
- Verificacion de integridad antes de un merge

### Requisitos previos

```bash
# Playwright debe estar instalado
npm install --save-dev @playwright/test
npx playwright install chromium
```

### Comandos

```bash
cd Reposa+

# Levantar stack E2E
docker compose -f docker-compose.e2e.yml up -d

# Verificar que todo esta healthy
docker compose -f docker-compose.e2e.yml ps

# Ejecutar todos los tests E2E
docker compose -f docker-compose.e2e.yml exec app \
  php artisan test --testsuite=Browser

# Ejecutar un test especifico
docker compose -f docker-compose.e2e.yml exec app \
  php artisan test --testsuite=Browser --filter="AddToCartTest"

# Ver logs
docker compose -f docker-compose.e2e.yml logs -f app

# Limpiar
docker compose -f docker-compose.e2e.yml down -v
```

### Servicios E2E

| Servicio | Puerto | Proposito |
|----------|--------|-----------|
| MySQL 8.0 | 3307 | Base de datos de pruebas |
| Redis 7 | 6380 | Cache + sesiones |
| MailHog | 8025 (web) / 1025 (SMTP) | Captura de correos |
| Laravel App | 8081 | Servidor de aplicacion |

### ejecucion local (sin Docker E2E)

Si ya tienes MailHog instalado localmente:

```bash
# Feature tests (rápidos, usan .env.testing)
APP_ENV=testing php artisan test --testsuite=Feature

# E2E tests (requieren servidor corriendo)
php artisan serve --port=8080 &
APP_ENV=testing php artisan test --testsuite=Browser
```

### Guia completa de tests E2E

Consultar `Reposa+/docs/e2e-testing-guide.md` para detalles sobre:
- Flujos de prueba implementados
- Simulacion de Stripe
- Uso de factories y helpers
- Buenas practicas

---

## 7. Entorno 6: CI/CD con GitHub Actions

### Pipeline automatica

El archivo `.github/workflows/ci.yml` ejecuta 3 jobs en paralelo cada vez que se hace push o PR a `develop` o `main`:

```
push/PR ──> ┌─── Lint (Pint) ──────── Verifica estilo de codigo
            ├─── Tests (PHPUnit) ──── Ejecuta todos los tests
            └─── Build (Vite) ─────── Compila assets frontend
```

### Job 1: Lint (PHP Pint)

```yaml
# Verifica que el codigo cumple con los estilos de Laravel
runs-on: ubuntu-latest
php-version: '8.3'
comando: ./vendor/bin/pint --test
```

### Job 2: Tests (PHPUnit)

```yaml
# Ejecuta todos los tests (Unit + Feature)
runs-on: ubuntu-latest
php-version: '8.3'
base de datos: SQLite (en memoria)
comando: php artisan test
```

### Job 3: Build (Vite)

```yaml
# Compila los assets CSS/JS del frontend
runs-on: ubuntu-latest
node-version: '20'
comando: npm ci && npm run build
```

### Cuando se ejecuta

- **Push a `develop`** — Ejecuta todos los jobs
- **Push a `main`** — Ejecuta todos los jobs
- **PR contra `develop`** — Ejecuta todos los jobs
- **PR contra `main`** — Ejecuta todos los jobs

### Que pasa si falla

- Si **Lint** falla: El PR tiene problemas de estilo. Ejecutar `./vendor/bin/pint` localmente para auto-corregir.
- Si **Tests** falla: Hay tests rotos. Ejecutar `php artisan test` localmente para investigar.
- Si **Build** falla: Hay errores en los assets. Ejecutar `npm run build` localmente.

### Credenciales en GitHub

Las secrets de GitHub Actions (si se necesitan) se configuran en:
`Settings > Secrets and variables > Actions`

Variables necesarias para E2E en CI (opcionales):

```
STRIPE_TEST_SECRET=sk_test_...
STRIPE_TEST_KEY=pk_test_...
```

---

## 8. Tabla Resumen de Puertos y Servicios

| Puerto | Servicio | Entorno | Descripcion |
|--------|----------|---------|-------------|
| 80 | nginx-lb | Produccion | Load balancer (acceso principal) |
| 8000 | app (artisan serve) | Dev / DevContainer | Servidor de desarrollo |
| 8025 | MailHog (web) | Produccion / E2E | Interfaz de visualizacion de emails |
| 8080 | app | E2E | Servidor de aplicacion para tests |
| 8081 | app | E2E (compose) | Servidor de aplicacion (mapeado) |
| 9000 | MinIO (API) | Produccion | API S3-compatible |
| 9001 | MinIO (Console) | Produccion | Consola de administracion MinIO |
| 1025 | MailHog (SMTP) | Produccion / E2E | Servidor SMTP de captura |
| 3306 | mysql-dev | Dev / DevContainer | MySQL (puerto del contenedor) |
| 3307 | mysql | Produccion / E2E | MySQL (puerto expuesto al host) |
| 6380 | redis | Produccion / E2E | Redis (puerto expuesto al host) |

---

## 9. Credenciales de Prueba

### Usuarios

| Rol | Email | Password | Uso |
|-----|-------|----------|-----|
| Administrador | `admin@reposaplus.com` | `admin123` | Panel admin, pedidos, dashboard |
| Cliente | `user@reposaplus.com` | `user123` | Tienda, carrito, perfil, favoritos |

### Servicios

| Servicio | Usuario | Password | URL |
|----------|---------|----------|-----|
| MinIO Console | `minioadmin` | `minioadmin` | http://localhost:9001 |
| MailHog | — | — | http://localhost:8025 |
| Stripe (test) | — | — | Dashboard: https://dashboard.stripe.com |

### Stripe Test Keys

Las claves de test estan en `.env.testing`:

```
STRIPE_SECRET=sk_test_51U9Fu2K0VMwtlVDAQ9rpBMlSkde7NhH5HSZYLfOz32EMdqrU05DfYTksrMRtAmWHTz32rjKKHwq0MJoR7F69xKrw00jAS7Og8q
STRIPE_KEY=pk_test_51U9Fu2K0VMwtlVDADl5i8DATOeLvL63oNGJVmsCvPZvs0Z4PFLDwnFwzBVjfBEvdyo6jFZBf4mzKtICyIjx99t7b00br7JMPmo
```

**Nota:** Estas son claves de TEST. Nunca usarlas en produccion.

---

## 10. Guia de Seleccion de Entorno

### Flujo de decision

```
¿Que necesitas hacer?
│
├─> Editar codigo rapidamente (1-2 archivos)
│   └─> Entorno 1: Desarrollo Local
│
├─> Desarrollo diario con IDE
│   ├─> Tienes VS Code/Cursor + Docker?
│   │   └─> Entorno 2: Dev Container
│   └─> No tienes IDE compatible?
│       └─> Entorno 3: Docker Compose dev
│
├─> Probar un feature completo
│   └─> Entorno 4: Docker Compose produccion (7 servicios)
│
├─> Ejecutar tests E2E
│   └─> Entorno 5: Docker Compose E2E
│
├─> Verificar que todo funciona antes de un merge
│   ├─> Localmente:
│   │   ├─> Feature tests: APP_ENV=testing php artisan test --testsuite=Feature
│   │   └─> E2E tests: Entorno 5
│   └─> Automaticamente:
│       └─> Entorno 6: GitHub Actions (push a develop)
│
└─> Simular entorno de produccion
    └─> Entorno 4: Docker Compose produccion
```

### Matriz de decision rapida

| Tarea | Local | DevContainer | Docker dev | Docker prod | Docker E2E | CI/CD |
|-------|:-----:|:------------:|:----------:|:-----------:|:----------:|:-----:|
| Editar PHP/Blade | ✅ | ✅ | ✅ | ✅* | ✅* | — |
| Ejecutar artisan | ✅ | ✅ | ✅ | ✅ | ✅ | — |
| Tests Feature | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Tests E2E | ⚠️ | ✅ | ⚠️ | ⚠️ | ✅ | — |
| Probar Redis | ❌ | ❌ | ❌ | ✅ | ✅ | — |
| Probar colas (queue) | ❌ | ❌ | ❌ | ✅ | ✅ | — |
| Probar emails | ❌ | ❌ | ❌ | ✅ (MailHog) | ✅ (MailHog) | — |
| Probar uploads S3 | ❌ | ❌ | ❌ | ✅ (MinIO) | ❌ | — |
| Verificar estilo (lint) | ✅ | ✅ | ✅ | — | — | ✅ |
| Verificar build assets | ✅ | ✅ | ✅ | — | — | ✅ |

\* Edicion dificil: el codigo esta dentro del contenedor. Usar bind mount o Dev Container.

---

## 11. Comandos Utiles de Referencia Rapida

### Servidor de desarrollo

```bash
# Local
cd Reposa+ && php artisan serve

# Dev Container / Docker dev
docker exec reposaplus-dev-app php artisan serve --host=0.0.0.0

# Docker produccion (ya incluido en nginx)
docker compose up -d
```

### Base de datos

```bash
# Reset completo (migraciones + seed)
php artisan migrate:fresh --seed

# Solo migraciones
php artisan migrate

# Ver estado de migraciones
php artisan migrate:status
```

### Tests

```bash
# Todos los tests
php artisan test

# Solo Feature tests
php artisan test --testsuite=Feature

# Solo Unit tests
php artisan test --testsuite=Unit

# Solo E2E tests (requiere servidor corriendo)
php artisan test --testsuite=Browser

# Test especifico
php artisan test --filter="CartTest"

# Con coverage (requiere xdebug o pcov)
php artisan test --coverage
```

### Lint y estilo

```bash
# Verificar estilo (sin modificar)
./vendor/bin/pint --test

# Auto-corregir problemas de estilo
./vendor/bin/pint
```

### Frontend

```bash
# Instalar dependencias JS
npm install

# Desarrollo (hot reload)
npm run dev

# Build para produccion
npm run build

# Build con observador
npm run watch
```

### Docker

```bash
# Ver estado de contenedores
docker compose ps

# Ver logs en tiempo real
docker compose logs -f

# Ejecutar comando en un contenedor
docker exec -it reposaplus_app php artisan <comando>

# Reconstruir contenedores (tras cambiar Dockerfile)
docker compose up -d --build

# Limpiar todo (contenedores + volumenes + redes)
docker compose down -v --remove-orphans
```

### Git

```bash
# Flujo de trabajo: GitFlow
git checkout develop          # Rama base
git checkout -b feature/nombre  # Crear feature branch
# ... desarrollar ...
git add . && git commit -m "feat: descripcion"
git checkout develop
git merge --no-ff feature/nombre
git push origin develop
```

---

## 12. Troubleshooting

### El servidor no arranca

```bash
# Verificar que el puerto no esta ocupado
lsof -i :8000

# Matar proceso que usa el puerto
kill -9 <PID>

# Probar otro puerto
php artisan serve --port=8001
```

### Errores de base de datos

```bash
# Verificar conexion
php artisan db:show

# Resetear completamente
php artisan migrate:fresh --seed --force

# Si usa SQLite, verificar que el archivo existe
touch database/database.sqlite
```

### Docker no arranca

```bash
# Verificar que Docker esta corriendo
docker info

# Verificar logs del contenedor con error
docker compose logs app

# Reconstruir desde cero
docker compose down -v
docker compose up -d --build
```

### Tests fallan

```bash
# Verificar que .env.testing existe y es correcto
cat .env.testing | grep DB_

# Ejecutar tests verbosamente
php artisan test --verbose

# Ejecutar un solo test para aislar el problema
php artisan test --filter="nombreDelTest"

# Verificar que las migraciones estan al dia
php artisan migrate:status
```

### MailHog no recibe emails

```bash
# Verificar que MailHog esta corriendo
curl http://localhost:8025/api/v2/search

# Verificar configuracion SMTP en .env
grep MAIL .env

# En Docker:
docker compose logs mailhog
```

### Stripe webhooks no funcionan

```bash
# Verificar ruta del webhook
php artisan route:list --name=stripe

# Verificar secreto en .env
grep STRIPE_WEBHOOK_SECRET .env

# Para desarrollo local con Stripe CLI:
stripe listen --forward-to localhost:8080/stripe/webhook
```

### Errores de Composer

```bash
# Limpiar cache
composer clear-cache

# Reinstalar desde cero
rm -rf vendor
composer install

# Verificar version de PHP
php -v  # Debe ser >= 8.4
```

---

*Documento generado para el proyecto Reposa+ TFG — Ultima actualizacion: Septiembre 2026*
