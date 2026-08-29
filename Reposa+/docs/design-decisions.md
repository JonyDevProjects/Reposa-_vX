# Decisiones de Diseño — Reposa+

> **Asignatura:** Tecnologías Avanzadas de Desarrollo (TFG)  
> **Proyecto:** Reposa+ — Tienda online de colchones y descanso  
> **Última actualización:** 30 de mayo de 2026

---

## Índice

1. [Fortify frente a Breeze para autenticación](#1-fortify-frente-a-breeze-para-autenticación)
2. [Carrito en sesión para usuarios invitados](#2-carrito-en-sesión-para-usuarios-invitados)
3. [Transacciones de base de datos en el checkout](#3-transacciones-de-base-de-datos-en-el-checkout)
4. [Vistas SQL para estadísticas de administración](#4-vistas-sql-para-estadísticas-de-administración)
5. [Toggle de Eloquent para favoritos](#5-toggle-de-eloquent-para-favoritos)
6. [SMTP con Mailtrap para desarrollo](#6-smtp-con-mailtrap-para-desarrollo)
7. [Middleware SetLocale para internacionalización](#7-middleware-setlocale-para-internacionalización)
8. [Middleware AdminMiddleware para protección de rutas](#8-middleware-adminmiddleware-para-protección-de-rutas)
9. [Personalización de Bootstrap 5 con SCSS](#9-personalización-de-bootstrap-5-con-scss)
10. [SQLite como base de datos de desarrollo](#10-sqlite-como-base-de-datos-de-desarrollo)

---

## 1. Fortify frente a Breeze para autenticación

**Decisión:** Utilizar **Laravel Fortify** en lugar de Laravel Breeze para la capa de autenticación.

**Justificación:**  
Fortify proporciona únicamente la lógica de backend para autenticación (registro, login, verificación de email, 2FA, etc.) **sin imponer ningún scaffolding de frontend**. Esto nos permitió diseñar las vistas con total libertad usando Bootstrap 5 y mantener una identidad visual personalizada para la marca Reposa+.

Breeze, en cambio, genera vistas con Tailwind CSS que habríamos tenido que reescribir por completo, duplicando el esfuerzo sin beneficio real.

---

## 2. Carrito en sesión para usuarios invitados

**Decisión:** Implementar el carrito de compra mediante **sesiones PHP** para usuarios no autenticados.

**Justificación:**  
Uno de los requisitos de la TFG es permitir que los visitantes puedan navegar y añadir productos al carrito **sin obligarles a registrarse**. Las sesiones de PHP ofrecen un mecanismo sencillo y eficaz para almacenar el carrito temporalmente en el servidor, sin necesidad de crear registros en base de datos para usuarios anónimos.

Cuando el usuario se autentica, el carrito de sesión se puede migrar a la tabla `cart_items` de forma transparente.

---

## 3. Transacciones de base de datos en el checkout

**Decisión:** Envolver la creación del pedido y el vaciado del carrito en una única **`DB::transaction()`**.

**Justificación:**  
El proceso de checkout implica varias operaciones atómicas:

1. Crear el registro en `orders`.
2. Crear los registros en `order_items`.
3. Decrementar el stock de cada producto.
4. Eliminar los `cart_items` del usuario.

Si cualquiera de estas operaciones falla (por ejemplo, stock insuficiente), la transacción se revierte automáticamente, evitando estados inconsistentes como pedidos sin líneas o carritos que desaparecen sin orden asociada.

```php
DB::transaction(function () use ($user, $cartItems) {
    $order = Order::create([...]);
    // crear order_items, decrementar stock...
    $user->cartItems()->delete();
});
```

---

## 4. Vistas SQL para estadísticas de administración

**Decisión:** Crear **vistas SQL** (`v_order_summary`, `v_top_favorited_products`) y exponerlas mediante **modelos Eloquent de solo lectura**.

**Justificación:**  
Las vistas SQL encapsulan consultas complejas (JOINs, agregaciones) directamente en la base de datos, proporcionando varias ventajas:

- **Rendimiento:** La base de datos optimiza la ejecución de la consulta internamente.
- **Reutilización:** Cualquier controlador puede acceder a las estadísticas con `OrderSummary::all()` sin duplicar la lógica SQL.
- **Separación de responsabilidades:** Los modelos Eloquent actúan como capa de acceso limpia, manteniendo los controladores esbeltos.

Este enfoque satisface el requisito del Problema 1 de la TFG sobre el uso de vistas en la base de datos.

---

## 5. Toggle de Eloquent para favoritos

**Decisión:** Utilizar el método **`toggle()`** de las relaciones `belongsToMany` de Eloquent para gestionar favoritos.

**Justificación:**  
El método `toggle()` añade el producto a favoritos si no existe la relación, o lo elimina si ya existe, todo en una sola llamada atómica:

```php
$user->favoriteProducts()->toggle($productId);
```

Esto simplifica el controlador, evita condiciones `if/else` manuales y reduce la posibilidad de errores de concurrencia (por ejemplo, dobles inserciones por clics rápidos).

---

## 6. SMTP con Mailtrap para desarrollo

**Decisión:** Configurar **Mailtrap** como servidor SMTP en el entorno de desarrollo.

**Justificación:**  
Mailtrap intercepta todos los correos salientes en un buzón sandbox, permitiendo:

- Verificar el contenido y formato de los emails (registro, verificación, reseteo de contraseña) de forma realista.
- Evitar el envío accidental de correos a direcciones reales durante las pruebas.
- Inspeccionar cabeceras, HTML y texto plano de cada mensaje.

La configuración se gestiona íntegramente a través del fichero `.env`, sin modificar el código de la aplicación.

---

## 7. Middleware SetLocale para internacionalización

**Decisión:** Crear un middleware personalizado **`SetLocaleMiddleware`** que lee el idioma de la sesión en cada petición.

**Justificación:**  
Laravel no persiste el locale entre peticiones de forma automática. Nuestro middleware:

1. Lee la clave `locale` almacenada en la sesión del usuario.
2. Llama a `App::setLocale($locale)` al inicio de cada request.
3. Garantiza que toda la interfaz se muestre en el idioma elegido (español o inglés) de forma persistente.

```php
public function handle($request, Closure $next)
{
    if (session()->has('locale')) {
        App::setLocale(session('locale'));
    }
    return $next($request);
}
```

---

## 8. Middleware AdminMiddleware para protección de rutas

**Decisión:** Implementar un middleware propio **`AdminMiddleware`** para proteger las rutas de administración.

**Justificación:**  
Fortify gestiona la autenticación (¿está el usuario logueado?), pero no la autorización por roles. Nuestro middleware complementa a Fortify verificando que el usuario autenticado tenga el rol `admin` antes de permitir el acceso a las rutas del panel de administración:

```php
public function handle(Request $request, Closure $next): Response
{
    if (Auth::check() && Auth::user()->role === 'admin') {
        return $next($request);
    }

    return redirect('/')->with('error', 'No tienes permisos para acceder a esta sección.');
}
```

Esto separa limpiamente las responsabilidades: Fortify se encarga de la **autenticación** y `AdminMiddleware` de la **autorización**. Cuando el usuario no tiene el rol `admin`, en lugar de devolver un error HTTP 403, es redirigido a la página principal con un mensaje flash de error, lo que resulta en una experiencia de usuario más coherente con el resto de la aplicación.

---

## 9. Personalización de Bootstrap 5 con SCSS

**Decisión:** Sobrescribir las **variables SCSS de Bootstrap 5** (`$primary`, `$secondary`, etc.) para crear la identidad visual de la marca Reposa+.

**Justificación:**  
En lugar de aplicar estilos inline o clases CSS ad-hoc, sobrescribimos las variables de Bootstrap **antes** de importar la librería. Esto garantiza que todos los componentes (botones, formularios, alertas, navbar) adopten automáticamente los colores de la marca Indigo:

```scss
// Variables personalizadas de Reposa+
$primary:   #4F46E5; // Indigo
$secondary: #7C3AED; // Violeta

@import "bootstrap/scss/bootstrap";
```

Además, se añaden efectos personalizados:

- **Hover en tarjetas de producto:** Elevación sutil con `transform: translateY(-4px)` y sombra aumentada.
- **Microinteracciones:** Transiciones suaves en botones y enlaces para mejorar la experiencia de usuario.

---

## 10. SQLite como base de datos de desarrollo

**Decisión:** Utilizar **SQLite** como motor de base de datos en el entorno local de desarrollo.

**Justificación:**  
SQLite simplifica enormemente la configuración del entorno de desarrollo:

- No requiere instalar ni configurar un servidor de base de datos externo (MySQL, PostgreSQL).
- La base de datos es un único fichero (`database.sqlite`), fácil de versionar o reiniciar.
- Es ideal para proyectos académicos donde la portabilidad es importante.

**Adaptación necesaria:**  
SQLite no soporta la sintaxis `CREATE OR REPLACE VIEW`. Por ello, las migraciones de vistas SQL utilizan `DROP VIEW IF EXISTS` seguido de `CREATE VIEW`:

```php
// En la migración
DB::statement('DROP VIEW IF EXISTS v_order_summary');
DB::statement('CREATE VIEW v_order_summary AS ...');
```

Esta adaptación es transparente y no afecta al funcionamiento de la aplicación ni a la compatibilidad con otros motores de base de datos en producción.

---

> **Nota final:** Todas estas decisiones fueron tomadas considerando los requisitos específicos de la TFG de la asignatura Tecnologías Avanzadas de Desarrollo, buscando el equilibrio entre simplicidad, buenas prácticas y cumplimiento académico.
