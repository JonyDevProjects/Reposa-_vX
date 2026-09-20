# Auditoría de Requisitos Técnicos Iniciales — Reposa+

Análisis exhaustivo del código del proyecto cruzado punto por punto con los requisitos técnicos iniciales del TFG.

## 🔴 CRÍTICO — Requisitos Técnicos de Calidad

Requisitos arquitectónicos y de base de datos requeridos para garantizar la consistencia y la normalización del sistema.

### 1. Vistas SQL (SQL Views) — ❌ Pendiente en Fase Inicial

> **CAUTION**
> Requisito arquitectónico: «Es necesario hacer uso de aspectos avanzados con la integración de base de datos como: uso de seeders y vistas SQL relacionales»

Estado inicial: No existían vistas SQL en el proyecto.

Solución aplicada: Creación de vistas SQL mediante migraciones de Laravel usando `DB::statement('CREATE VIEW ...')`:

- Vista `v_order_summary` que resume pedidos por usuario (total gastado, número de pedidos).
- Vista `v_top_favorited_products` que muestra los productos más deseados con su conteo agrupado.

### 2. Transacciones de Base de Datos (ACID) — ❌ Pendiente en Fase Inicial

> **CAUTION**
> Requisito de integridad transaccional: «Garantizar el control estricto sobre transacciones concurrentes en el proceso de compra»

Estado inicial: El método `checkout()` de `CartController.php` creaba el pedido y eliminaba ítems sin envolver en bloqueos pesimistas ni transacciones ACID.

Solución aplicada: Envolver la lógica transaccional en `DB::transaction(function() { ... })` y aplicar bloqueo pesimista `lockForUpdate()` para evitar sobreventas.

### 3. Asignación Masiva: `image_url` en `$fillable` — 🐛 Corrección

> **WARNING**
> El campo `image_url` existe en la base de datos pero requería estar presente en la propiedad `$fillable` del modelo `Product.php`.

Solución aplicada: Añadir `'image_url'` al array `$fillable` del modelo `Product.php`.

## 🟡 IMPORTANTE — Optimizaciones de Rendimiento y Datos

Puntos de mejora identificados durante la auditoría técnica inicial:

### 4. README.md del proyecto — ❌ Es el README por defecto de Laravel

Estado actual: El README.md es literalmente el que viene de fábrica con Laravel ("About Laravel", "Learning Laravel"...). No tiene NADA del proyecto Reposa+.

Qué hay que hacer: Escribir un README profesional con: nombre del proyecto, descripción, instrucciones de instalación (composer install, npm install, php artisan migrate --seed), credenciales de demo, y lista de funcionalidades.

### 5. ProfileController NO carga la relación favorites — ⚠️ Bug de rendimiento

Estado actual: En ProfileController.php se hace $user->load(['profile', 'addresses', 'orders']) pero NO se incluye favorites. La vista de perfil accede a $user->favorites y funciona por lazy-loading de Eloquent, pero genera consultas N+1 innecesarias.

Qué hay que hacer: Añadir 'favorites' al $user->load().

### 6. Seeders insuficientes — ⚠️ Pocos datos de demo

Estado actual: Solo hay 3 productos, 2 usuarios, 0 pedidos, 0 direcciones y 0 favoritos de ejemplo. Al hacer la demo ante la profesora, el perfil de usuario estará vacío, el historial de pedidos vacío, la sección de favoritos vacía y el dashboard del admin sin datos.

Qué hay que hacer: Ampliar el DatabaseSeeder con más productos (8-10), crear pedidos de ejemplo, direcciones y favoritos precargados para que la demo lucirá poblada.

### 7. Documentación de Mockups — ❌ No existen

Estado actual: El enunciado pide «Mockups (papel, en línea, bocetos, etc.)» y no hay ningún archivo de mockups ni capturas en la carpeta docs/.

Qué hay que hacer: Crear al menos bocetos simples de las vistas principales (Home, Catálogo, Detalle, Carrito, Perfil, Admin) e incluirlos en la memoria.

## 🟢 LO QUE ESTÁ BIEN (No tocar)

| Funcionalidad | Estado |
| --- | --- |
| Sistema de autenticación (Login/Registro/Logout) | ✅ Completo |
| Recuperación de contraseña (Forgot/Reset) | ✅ Completo |
| Carrito para invitados (sesión) y usuarios (BD) | ✅ Completo |
| Proceso de checkout con toast visual | ✅ Completo |
| Mailable de confirmación de pedido | ✅ Completo |
| CRUD de productos (Admin) | ✅ Completo |
| CRUD de categorías (Admin) con multi-select | ✅ Completo |
| Filtrado de catálogo por categoría | ✅ Completo |
| i18n (Inglés/Español) en Home y Catálogo | ✅ Completo |
| Perfil: edición de datos, contraseña, direcciones | ✅ Completo |
| Historial de pedidos del usuario | ✅ Completo |
| Wishlist interactiva con AJAX | ✅ Completo |
| Top favoritos en dashboard admin | ✅ Completo |
| Middleware de protección de rutas admin | ✅ Completo |
| Personalización de Bootstrap con paleta Índigo | ✅ Completo |

## 📋 Matriz de Resolución de Calidad Técnica

| Prioridad | Tarea | Impacto en Calidad | Estado |
| --- | --- | --- | :---: |
| 🔴 1 | Añadir Vistas SQL (`v_order_summary`, `v_top_favorited_products`) | ALTO — optimización de consultas complejas | ✅ Implementado |
| 🔴 2 | Envolver checkout en `DB::transaction()` y `lockForUpdate()` | CRÍTICO — prevención de sobreventas y ACID | ✅ Implementado |
| 🔴 3 | Añadir `image_url` a `$fillable` del modelo Product | ALTO — persistencia íntegra de catálogo | ✅ Implementado |
| 🟡 4 | Documentación profesional y catálogo en `README.md` | ALTO — estándares de ingeniería | ✅ Implementado |
| 🟡 5 | Eager-load `favorites` en `ProfileController` | MEDIO — mitigación de problemas N+1 | ✅ Implementado |
| 🟡 6 | Comando `orders:reset-test-matrix` con datos sembrados | MEDIO — reproducibilidad en pruebas | ✅ Implementado |
| 🟡 7 | Configuración de MailHog en el stack Docker | MEDIO — inspección de emails transaccionales | ✅ Implementado |
| 🟢 8 | Diagramas UML y arquitectura Métrica v3 (Anexos I, II, III) | ALTO — cumplimiento normativo UPO | ✅ Implementado |
