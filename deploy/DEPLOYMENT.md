# Despliegue de Reposa+ con Docker

## ⚠️ Antes de nada: rota las credenciales de Mailtrap
El `.env.example` del repo tiene credenciales **reales** de Mailtrap en texto plano
(`MAIL_USERNAME`, `MAIL_PASSWORD`). Como el repo es público, están expuestas a cualquiera.
1. Entra en mailtrap.io → tu inbox → regenera las credenciales SMTP.
2. Pon las nuevas **solo** en tu `.env` real (nunca en `.env.example`, que sí se commitea).

## Qué hace cada archivo
- **`Dockerfile`**: build en 2 etapas. Primero compila los assets (Vite + Bootstrap/SCSS) con
  Node; luego construye la imagen final de PHP 8.3-FPM con Nginx y Supervisor gestionando
  ambos procesos dentro del mismo contenedor.
- **`docker-compose.yml`**: levanta 2 servicios —
  - `app`: sirve la web en `http://localhost:8080`.
  - `queue`: mismo código, pero ejecuta `php artisan queue:work` para procesar los emails
    encolados (confirmación de pedido, recuperación de contraseña).
  Ambos comparten dos volúmenes (`sqlite_data`, `storage_data`) para que la base de datos
  SQLite y los ficheros de sesión/caché persistan aunque reinicies los contenedores.
- **`docker/nginx.conf`**: sirve `public/` y pasa las peticiones `.php` a PHP-FPM.
- **`docker/supervisord.conf`**: mantiene Nginx y PHP-FPM corriendo a la vez en el contenedor `app`.
- **`docker/entrypoint.sh`**: al arrancar, crea el fichero SQLite si no existe, genera la
  `APP_KEY` si falta, ejecuta migraciones y cachea config/rutas.
- **`.dockerignore`**: evita meter `node_modules`, `vendor`, `.env` o el SQLite local dentro
  de la imagen.

## Por qué SQLite y no MySQL en el despliegue
Es coherente con vuestra propia decisión documentada (`design-decisions.md`, decisión 10):
un único fichero, sin servidor de base de datos externo que mantener — más portable para un
entorno académico. Si en algún momento queréis demostrar también MySQL, es un `docker-compose`
alternativo con un tercer servicio `db: mysql:8` y cambiar `DB_CONNECTION` a `mysql` en el
`.env` — dímelo y te lo preparo aparte como plan B.

## Cómo levantarlo

1. Copia estos archivos a la raíz de `Reposa+/` (los de `docker/` dentro de una carpeta `docker/`).
2. Crea un `.env` real (no lo subas a git) a partir de `.env.example`, con las credenciales
   de Mailtrap ya rotadas.
3. Genera una `APP_KEY` y pégala en ese `.env`:
   ```bash
   php artisan key:generate --show
   ```
4. Levanta todo:
   ```bash
   docker compose up --build
   ```
5. Primera vez: carga los datos de ejemplo (productos, usuario admin/usuario):
   ```bash
   docker compose exec app php artisan db:seed --force
   ```
6. Abre `http://localhost:8080`.

## Requisitos mínimos (para la diapositiva de despliegue)
- Docker Engine 24+ y Docker Compose v2.
- ~1 vCPU y 1 GB de RAM libres para los 2 contenedores.
- Puerto `8080` libre en el host.
- Conexión a internet durante el build (descarga de imágenes base y dependencias).

## Plan de contingencia (para la rúbrica: "planes de contingencia")
| Problema | Causa probable | Solución |
|---|---|---|
| `docker compose up` falla en el build de Node | Versión de Node incompatible con Vite 8 | Confirma que usas la imagen `node:20-alpine` indicada, no una local distinta |
| La web carga sin estilos | Los assets de Vite no se compilaron antes de copiarlos | Revisa logs de la etapa `frontend`: `docker compose build app --progress=plain` |
| No llegan los emails de confirmación | Credenciales de Mailtrap caducadas/rotadas sin actualizar `.env` | Verifica `MAIL_USERNAME`/`MAIL_PASSWORD` en tu `.env` real |
| Error 500 en cualquier petición | Falta `APP_KEY` o permisos de `storage/` | Revisa que `.env` tiene `APP_KEY` y que el volumen `storage_data` se creó correctamente |
| Los pedidos no se completan | Contenedor `queue` caído | `docker compose ps` y `docker compose logs queue` |

---

## Prompt para Claude Code: aplicar y probar el despliegue de verdad

Pega esto en Claude Code, con el repo (rama `develop`) abierto y Docker Desktop/Engine
corriendo en tu máquina:

```
Voy a añadir soporte de despliegue con Docker a este proyecto Laravel. Ya tengo preparados
estos archivos (te los pego abajo, colócalos exactamente en estas rutas):
- Dockerfile (raíz)
- docker-compose.yml (raíz)
- docker/nginx.conf
- docker/supervisord.conf
- docker/entrypoint.sh
- .dockerignore (raíz)

[pega aquí el contenido de cada archivo]

Tareas:
1. Coloca cada archivo en su ruta exacta dentro de Reposa+/.
2. Crea un .env real a partir de .env.example (sin commitearlo), genera una APP_KEY nueva,
   y sustituye las credenciales de Mailtrap por placeholders vacíos que yo rellenaré a mano
   tras rotarlas en mailtrap.io.
3. Ejecuta `docker compose up --build` y soluciona cualquier error que aparezca (permisos,
   extensiones PHP que falten, rutas mal copiadas, etc.) hasta que la app cargue
   correctamente en http://localhost:8080.
4. Ejecuta `docker compose exec app php artisan db:seed --force` y confirma que el login con
   admin@reposaplus.com / admin123 funciona dentro del contenedor.
5. Confirma que el contenedor `queue` está activo y que un pedido de prueba genera un email
   visible en Mailtrap.
6. Dame un resumen de qué tuviste que ajustar respecto a los archivos originales y por qué.
```
