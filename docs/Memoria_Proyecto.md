# Memoria del Proyecto: Reposa+

## 1. Definición del Proyecto

**Español:**
"Reposa+" es una plataforma de comercio electrónico especializada en almohadas ergonómicas e inteligentes, diseñadas para mejorar la calidad del sueño. Es un producto de nicho enfocado en la salud y el descanso, dirigido a personas con problemas cervicales, insomnio o que simplemente buscan optimizar su descanso nocturno. El proyecto busca ofrecer una experiencia de usuario fluida, desde la exploración detallada de los beneficios del producto hasta la conversión final a través de un proceso de compra seguro.

**English:**
"Reposa+" is an e-commerce platform specializing in ergonomic and smart pillows designed to improve sleep quality. It is a niche product focused on health and rest, aimed at individuals with cervical issues, insomnia, or those simply looking to optimize their night's sleep. The project aims to provide a seamless user experience, from the detailed exploration of the product's benefits to the final conversion through a secure checkout process.

## 2. Resumen y Objetivos

El objetivo general del proyecto es desarrollar un e-commerce robusto, escalable y con una interfaz premium que genere confianza en los usuarios, utilizando Laravel como framework backend.

**Objetivos específicos:**
- **Navegación e Información:** Permitir a usuarios registrados y no registrados explorar un catálogo de productos altamente descriptivo.
- **Flujo de Compra Completo:** Implementar un sistema de carrito de compras y checkout fiable, con generación de tickets inmutables.
- **Autenticación y Seguridad:** Integrar un sistema de registro/login seguro, con roles diferenciados (Usuario/Administrador).
- **Gestión Administrativa:** Proveer un panel de control (Dashboard) para la administración de inventario, gestión de categorías (N:M) y visualización del histórico de pedidos.
- **Internacionalización (i18n):** Dar soporte multi-idioma (Español e Inglés) a toda la plataforma pública.
- **Fidelización:** Desarrollar un sistema de favoritos (Wishlist) y analítica de demanda para el administrador.

## 3. Análisis y Especificación de Requisitos

Los requisitos se han dividido en hitos medibles, abordados en tareas atómicas a lo largo de sprints iterativos:

- **Catálogo Público:** Visualización de fichas de producto con precio, características y beneficios para la salud sin requerir autenticación.
- **Panel de Usuario:** CRUD de direcciones de envío, gestión de contraseña (con recuperación vía correo electrónico SMTP) e histórico de pedidos.
- **Categorización:** Clasificación de almohadas mediante una relación "muchos a muchos" (N:M) que facilite los filtros de búsqueda (ej. "Cervical", "Térmica").
- **Flujo AJAX:** Añadir elementos al carrito y operar la cesta de forma dinámica y sin recargar la página.
- **Gestión Admin:** Capacidad CRUD completa para productos y sus etiquetas. Rutas protegidas mediante Middleware estricto para evitar accesos no autorizados.

## 4. Propuesta de Solución

### 4.1. Diseño UX/UI (Psicología del Color)
La interfaz hace uso de **Bootstrap 5** modificado significativamente mediante variables SASS para ajustarse al branding. El color principal es el **Índigo / Blue-Indigo**, que evoca la calma, serenidad y la profundidad de la noche, preparando psicológicamente al usuario para el descanso. 
- *Neutrales:* Blanco y negro para asegurar el contraste de la tipografía.
- *Primario Oscuro:* `#182447` (Navbar, Headers, Botones).
- *Secundarios:* `#758ef9` para llamadas a la acción, y `#b1cdff` para fondos de tarjetas destacadas.

Se cuenta con diagramas estructurados y **Wireframes** (ver anexo `docs/artefactos/wireframe-mocks.md`).

### 4.2. Arquitectura de Base de Datos
El esquema está diseñado para garantizar la integridad referencial y cumplir con las cardinalidades exigidas (1:1, 1:N y N:M):
- **1:N**: Un usuario (`USER`) puede tener múltiples direcciones (`ADDRESS`) y pedidos (`ORDER`).
- **N:M**: Productos (`PRODUCT`) se relacionan de forma múltiple con Categorías (`CATEGORY`) mediante la tabla pivote `CATEGORY_PRODUCT`.
- **N:M**: Lista de deseos, en la que un usuario marca como favorito múltiples productos (`FAVORITE_PRODUCT`).

Adicionalmente, se han empleado **Seeders** y *Factories* para poblar la base de datos de manera automatizada con datos de prueba realistas. Para facilitar el análisis de datos en el panel de administración (Dashboard), se han creado **Vistas SQL** personalizadas (ej. `v_order_summary` y `v_top_favorited_products`), encapsuladas mediante modelos de Eloquent para optimizar el rendimiento de lectura.

Ver diagrama completo en `docs/artefactos/EsquemaBBDD.md`.

### 4.3. Tecnologías Seleccionadas
- **Backend:** Laravel (PHP) con Eloquent ORM por su robustez, seguridad (CSRF, prepared statements) y fácil escalabilidad.
- **Frontend:** Blade Templating, JavaScript Vanilla/AJAX, Bootstrap 5 y Vite (compilación de SASS).
- **Notificaciones:** Mailtrap para simular el envío de correos transaccionales y recuperación de cuentas.

## 5. Plan de Trabajo

La metodología empleada sigue los principios ágiles orientados a flujo continuo (Kanban):

- **Fase 1 (v1.0):** Setup técnico, migraciones BD, catálogo público y panel básico de administración.
- **Fase 2 (v2.0):** Gestión compleja de categorías (N:M) y filtrado exploratorio visual en la Home.
- **Fase 3 (v2.1):** Internacionalización (i18n) y expansión del panel de cliente (cambio de contraseñas, historial).
- **Fase 4 (v2.2):** Retención (Wishlist de Favoritos) y analítica de datos en el Dashboard administrativo.

El control de versiones utiliza un flujo organizado (ver `docs/artefactos/gitflow-fix.md`), donde `main` siempre es estable, `develop` recibe integraciones y existen ramas de tipo `feature/` y `hotfix/`.

## 6. Desarrollo de la Solución

El aplicativo cumple con todas las reglas del patrón **Modelo-Vista-Controlador (MVC)**, aplicando buenas prácticas del ecosistema Laravel:
- **Seguridad:** Uso nativo de Middleware para segmentación de zonas de usuario/administrador y prevención de inyecciones a través del ORM Eloquent.
- **Calidad de Código:** Funciones delegadas a controladores atómicos. Sincronización ágil en tablas N:M mediante el método `sync()` de Eloquent sin afectar el rendimiento.
- **Persistencia Segura y Transacciones:** Todo pedido tramitado inserta su información mediante registros inmutables, protegiendo el inventario e impidiendo alteraciones retroactivas de los tickets. El proceso crítico de "Checkout" se encuentra protegido mediante **Transacciones de Base de Datos** (`DB::transaction`), garantizando que la creación del pedido y el vaciado del carrito se ejecuten de manera atómica, previniendo cualquier inconsistencia de datos (rollback automático en caso de fallo).

La solución abarca los requisitos funcionales con creces, demostrando la capacidad del framework de ofrecer un desarrollo ágil de tipo "Enterprise".

## 7. Despliegue e Instalación

El entorno de desarrollo y despliegue inicial se fundamenta en **Docker**, empleando la herramienta **Laravel Sail**. Esto asegura que el stack de servicios (PHP 8.2+, MySQL, Redis, Mailpit) levante idénticamente en cualquier sistema operativo, minimizando la disparidad "en mi máquina funciona".

Para el despliegue en un servidor real de producción, el sistema cuenta con los archivos de configuración `.env` orientados a facilitar migraciones hacia instancias en la nube (como contenedores en Digital Ocean), preparando la aplicación para integraciones automatizadas (CI/CD) en el futuro.

## 8. Evolución y Trabajo Futuro

El proyecto está diseñado pensando en la escalabilidad a futuro. Las siguientes etapas contemplarían:
1. **Pasarela de Pago Real:** Sustitución de la compra simulada actual por la API de Stripe, permitiendo la integración de métodos de cobro en tarjeta y wallets (Apple/Google Pay) bajo cumplimiento PCI.
2. **Métricas Avanzadas de Negocio:** Ampliación del panel del administrador para mostrar analítica de conversión en tiempo real, utilizando Redis para agilizar consultas estadísticas sobre las ventas, tasas de abandono de carritos e ingresos diarios.
3. **Optimización SEO y Accesibilidad:** Refuerzo del marcado semántico y validaciones W3C / WCAG, garantizando que el diseño "Índigo" contraste correctamente para personas con diversidad visual.

## 9. Bibliografía

1. Laravel Foundation. (2024). *Laravel Documentation* (v11.x). Recuperado de https://laravel.com/docs
2. Bootstrap Core Team. (2024). *Bootstrap 5 Documentation*. Recuperado de https://getbootstrap.com/docs/5.3/
3. Mailtrap. (2024). *Testing Emails in Laravel*. Recuperado de https://mailtrap.io/blog/laravel-send-email/
4. Docker Inc. (2024). *Docker Documentation*. Recuperado de https://docs.docker.com/
