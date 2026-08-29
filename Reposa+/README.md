# Reposa+ (Tienda de Almohadas y Descanso)

Este proyecto es la solución a la **EPD3 de Tecnologías Avanzadas de Desarrollo**. Consiste en un e-commerce completamente funcional desarrollado en **Laravel 10** y **Bootstrap 5**, con un diseño personalizado, sistema de carrito, wishlist y panel de administración.

## Características Principales

*   **Autenticación de Usuarios**: Sistema de login y registro integrado (Fortify).
*   **Gestor de Perfil**: Panel donde el usuario puede editar sus datos, direcciones y ver su historial de pedidos.
*   **Wishlist Interactiva**: Sistema de Favoritos asíncrono, los usuarios pueden añadir/quitar productos con un click.
*   **Carrito Dual**: 
    *   **Invitados**: El carrito se guarda en sesión temporalmente.
    *   **Autenticados**: El carrito se guarda en base de datos de manera persistente.
*   **Checkout y Notificaciones**: Flujo de compra con control transaccional (`DB::transaction`) y envío real de correos de confirmación (SMTP / Mailtrap).
*   **Panel de Administración**: CRUD completo para Productos y Categorías, además de estadísticas usando Vistas SQL puras.
*   **Internacionalización (i18n)**: Traducción de la interfaz (Español e Inglés).
*   **Diseño Custom**: Uso avanzado de Bootstrap 5 con una paleta de colores corporativa (Índigo) y CSS personalizado para microinteracciones.

## Requisitos Previos

*   PHP >= 8.1
*   Composer
*   Node.js & NPM
*   Base de datos (MySQL/MariaDB/SQLite)

## Instalación

1.  **Clonar el repositorio** y entrar en la carpeta del proyecto.
2.  **Instalar dependencias de PHP y JS**:
    ```bash
    composer install
    npm install
    npm run build
    ```
3.  **Configurar entorno**:
    *   Copia el archivo `.env.example` a `.env`
    *   Genera la clave de la aplicación: `php artisan key:generate`
    *   Configura las credenciales de tu base de datos en el `.env`.
    *   Configura Mailtrap en el `.env` (MAIL_MAILER=smtp, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD) para que los correos de confirmación se envíen.
4.  **Ejecutar migraciones, vistas SQL y Seeders**:
    ```bash
    php artisan migrate:fresh --seed
    ```
5.  **Arrancar el servidor de desarrollo**:
    ```bash
    php artisan serve
    ```

## Usuarios de Demostración (Seeders)

La base de datos viene precargada con categorías, 8 productos de alta calidad, pedidos de prueba y los siguientes usuarios:

**Administrador**
*   **Email**: admin@reposaplus.com
*   **Password**: admin123

**Usuario Cliente**
*   **Email**: user@reposaplus.com
*   **Password**: user123

## Vistas SQL Implementadas (Problema 3)

Se han creado vistas a nivel de base de datos para optimizar reportes en el panel de administrador:
*   `v_order_summary`: Agrupa y resume los gastos y número de pedidos por usuario.
*   `v_top_favorited_products`: Cuenta y ordena los productos más marcados como favoritos.

## Integrantes del Grupo

*   **Jonathan Javier Quishpe Maldonado**
