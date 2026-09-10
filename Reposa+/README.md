# Reposa+ (Tienda de Almohadas y Descanso)

Este proyecto es la solución a la **TFG de Tecnologías Avanzadas de Desarrollo**. Consiste en un e-commerce completamente funcional desarrollado en **Laravel 10** y **Bootstrap 5**, con un diseño personalizado, sistema de carrito, wishlist y panel de administración.

## Características Principales

*   **Autenticación Híbrida y Federada**: Sistema de login y registro integrado (Fortify) + autenticación con Google OAuth 2.0 y onboarding de dirección obligatorio.
*   **Gestor de Perfil**: Panel donde el usuario puede editar sus datos, direcciones y ver su historial de pedidos.
*   **Wishlist Interactiva**: Sistema de Favoritos asíncrono con guardado en base de datos.
*   **Carrito Dual Adaptativo**: 
    *   **Invitados**: Carrito en sesión temporal con soporte completo de checkout sin login.
    *   **Autenticados**: Carrito persistente en base de datos con sincronización automática tras login.
*   **Compra como Invitado (*Guest Checkout*)**: Tramitación de pedidos sin registro previo, protección criptográfica con `guest_token`, facturas PDF seguras y conversión de cuenta en 1 clic (*Claim Account*).
*   **Servicio de Paquetería Estándar**: Motor determinista de cálculo de tarifas (envío estándar gratis $\ge 50€$, express y puntos de recogida), generación de códigos de seguimiento `RPX2026...ES` y albaranes térmicos A6 (10x15cm) con código de barras Code 128.
*   **Checkout Seguro y Pasarela Stripe**: Flujo transaccional atómico con `DB::transaction` y bloqueo pesimista `lockForUpdate()`, integración con Stripe Checkout y webhooks asíncronos con verificación de firma.
*   **Panel de Administración Back-Office**: CRUD completo de productos y categorías, trazabilidad logística y estadísticas con Vistas SQL nativas.
*   **Internacionalización (i18n)**: Traducción completa de la interfaz en Español e Inglés (`messages.php`).
*   **Estrategia de Testing Tripartita (119 pruebas)**: Suite unitaria pura en memoria (22 tests en 0.06s), suite de integración con MySQL InnoDB (89 tests en 1.95s) y pruebas de sistema E2E con Playwright sobre Chromium real (8 tests en 10.7s). Executable en <12s globales.

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
