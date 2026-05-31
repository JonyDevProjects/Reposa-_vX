# Auditoría EPD3 — Reposa+ (¿Por qué un 4?)

Análisis exhaustivo del código del proyecto cruzado punto por punto con los requisitos del enunciado de la EPD3.

## 🔴 CRÍTICO — Requisitos explícitos que FALTAN completamente

Estos son requisitos que la profesora pide textualmente en el enunciado de la EPD3 y que no están implementados en absoluto. Son la razón más probable del 4.

### 1. Vistas SQL (SQL Views) — ❌ NO EXISTE

> **CAUTION**
> El enunciado del Problema 3 dice textualmente: «es necesario hacer uso de aspectos avanzados con la integración de nuestra base de datos como: uso de seeders y vistas SQL»

Estado actual: No hay NI UNA SOLA vista SQL en todo el proyecto. Ni en migraciones, ni en ningún sitio.

Qué hay que hacer: Crear al menos 1-2 vistas SQL mediante una migración de Laravel usando DB::statement('CREATE VIEW ...'). Por ejemplo:

- Una vista v_order_summary que resuma pedidos por usuario (total gastado, número de pedidos)
- Una vista v_top_favorited_products que muestre los productos más deseados con su conteo

### 2. Transacciones de Base de Datos — ❌ NO EXISTE

> **CAUTION**
> El enunciado del Problema 3 dice textualmente: «Sin olvidar el control sobre las transacciones»

Estado actual: El método checkout() de CartController.php crea un pedido, recorre los items del carrito, crea OrderItems, elimina los CartItems y envía un email... todo SIN ningún DB::transaction(). Si falla a mitad del proceso, la base de datos queda en un estado inconsistente (pedido creado pero sin items, o items sin eliminar del carrito).

Qué hay que hacer: Envolver la lógica de checkout en DB::transaction(function() { ... }).

### 3. Bug Crítico: image_url NO está en $fillable del Modelo Product — 🐛 BUG

> **WARNING**
> El campo image_url existe en la base de datos (tiene su migración propia), el Seeder intenta guardar URLs de imágenes, el formulario del Admin tiene un campo para la URL de la imagen... pero image_url NO está en la propiedad $fillable del modelo Product. Esto hace que Laravel lo ignore silenciosamente en cada Product::create().

Consecuencia: Todas las imágenes de productos son null en la base de datos. Las vistas usan placehold.co como fallback, así que parece que funciona, pero NO está guardando las imágenes reales.

Qué hay que hacer: Añadir 'image_url' al array $fillable del modelo Product.php.

## 🟡 IMPORTANTE — Cosas que faltan o están incompletas

Estos puntos no son "suspenso directo" pero sí restan nota significativamente.

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

## 📋 Prioridad de implementación (para subir nota)

| Prioridad | Tarea | Impacto en nota | Tiempo estimado |
| --- | --- | --- | --- |
| 🔴 1 | Añadir Vistas SQL (migración) | MUY ALTO — requisito explícito | 10 min |
| 🔴 2 | Envolver checkout en DB::transaction() | MUY ALTO — requisito explícito | 5 min |
| 🔴 3 | Añadir image_url a $fillable del Product | ALTO — bug silencioso | 1 min |
| 🟡 4 | Reescribir el README.md profesional | ALTO — imagen de marca del proyecto | 10 min |
| 🟡 5 | Eager-load favorites en ProfileController | MEDIO | 1 min |
| 🟡 6 | Ampliar el Seeder con más datos de demo | MEDIO — la demo lucirá vacía si no | 15 min |
| 🟡 7 | Configurar Mailtrap real en .env | MEDIO — para demo en vivo | 5 min |
| 🟢 8 | Crear mockups básicos para la memoria | BAJO — complementario | 20 min |
