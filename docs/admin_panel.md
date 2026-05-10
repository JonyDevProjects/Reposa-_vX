# Registro de Mejoras - Panel de Administración

Este documento detalla las implementaciones y mejoras realizadas sobre el Panel de Administración y el Sistema de Roles en el proyecto Reposa+, consolidando la separación de capacidades y facilitando la gestión del e-commerce.

---

## 1. Refactorización de la Navegación (DRY)
- **Qué pasaba:** El menú lateral de navegación del administrador (sidebar) estaba duplicado en código en cada una de las vistas (`dashboard`, `products/index`, `orders/index`, etc).
- **Por qué:** Durante la fase inicial, la estructura HTML se replicaba en cada archivo, dificultando el mantenimiento. Si se requería un nuevo botón, implicaba editar cinco archivos distintos.
- **Qué decisiones se tomaron:** Se extrajo todo el bloque a un componente parcial (`resources/views/admin/partials/sidebar.blade.php`). Ahora las vistas lo invocan mediante `@include('admin.partials.sidebar')`, centralizando la lógica y automatizando el estado de "botón activo" de la interfaz con comprobadores de rutas (`request()->routeIs()`).

## 2. Gestión Activa del Estado de los Pedidos
- **Qué pasaba:** En el "Histórico Global", el administrador veía el estado de cada pedido a través de un desplegable, pero este estaba desactivado e impedía interactuar.
- **Por qué:** La vista carecía de un entorno de formulario y tampoco existía lógica a nivel de Backend que procesara los cambios de estado.
- **Qué decisiones se tomaron:** 
    - Se activó el `<select>` encapsulándolo dentro de un `<form action="...">`.
    - Se añadió un disparador inteligente JS en el HTML (`onchange="this.form.submit()"`) para guardar los datos al momento de seleccionar otra opción.
    - Se creó la ruta y su controlador (`AdminController@updateOrderStatus`) para validar y actualizar en base de datos (`pending`, `processing`, `shipped`, `delivered`, `cancelled`).

## 3. Paginación Integrada para Escalabilidad
- **Qué pasaba:** El sistema recuperaba el catálogo entero y todo el historial de pedidos para pintarlo de golpe en las pantallas de administración.
- **Por qué:** Las consultas de Eloquent terminaban en el método `get()`, un comportamiento que es inviable en escenarios de alta demanda de registros.
- **Qué decisiones se tomaron:** Se modificaron las consultas sustituyendo `get()` por el fragmentador `paginate()` (fragmentando en 10 productos y 15 pedidos por hoja). Se renderizaron también los respectivos componentes Bootstrap dinámicos con los botones "Página Siguiente/Anterior" al final de las tablas.

## 4. Accesibilidad Rápida desde el Frontend
- **Qué pasaba:** Cuando el administrador iniciaba sesión, era arrojado a la tienda sin atajos visibles hacia la trastienda, forzándolo a alterar la URL del navegador a mano.
- **Por qué:** El `layouts/app.blade.php` trataba a todos los perfiles de igual forma.
- **Qué decisiones se tomaron:** Se le aplicó un escudo de permisos en línea (`@if(Auth::user()->role === 'admin')`) dentro del cajón de acciones de perfil. En el caso de tener autorización, el sistema le inyecta al menú de usuario un enlace en rojo enlazando a su centro de mandos privado.

## 5. Superposición Visual del Menú (Conflicto de z-index)
- **Qué pasaba:** En la página del carrito, al desplegar las opciones de la cabecera (como "Panel de administración" o "Cerrar sesión"), estas quedaban escondidas tras la caja de "Resumen del Pedido".
- **Por qué:** La cabecera superior y la caja lateral usaban la clase Bootstrap `.sticky-top`. Al estar empatados en la jerarquía dimensional Z (`z-index: 1020`), el navegador otorgaba prioridad al bloque del resumen por estar codificado más abajo en el HTML.
- **Qué decisiones se tomaron:** Se restó poder dimensional explícitamente a la tarjeta del resumen en su código (`style="z-index: 10;"`). Esto respeta su comportamiento adhesivo durante el scroll vertical, garantizando al mismo tiempo la subordinación a cualquier menú desplegado desde arriba.
