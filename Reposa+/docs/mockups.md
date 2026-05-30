# Reposa+ - Mockups y Wireframes

Este documento contiene los bocetos y esquemas de diseño estructural (wireframes) de la aplicación web Reposa+ para cumplir con los requisitos de la EPD3 (Problema 0).

Debido a que el proyecto ya se encuentra en fase de desarrollo, presentamos los wireframes arquitectónicos principales.

## 1. Página de Inicio (Home)

La página de inicio está diseñada para atrapar la atención del usuario (Hero), generar confianza (Beneficios) y mostrar directamente el producto principal.

```text
+-------------------------------------------------------+
|  [Logo Reposa+]     Inicio | Catálogo | Login | 🛒(0) |
+-------------------------------------------------------+
|                                                       |
|   +-----------------------------------------------+   |
|   |                                               |   |
|   |   Descubre el descanso perfecto con Reposa+   |   |
|   |   Almohadas premium...                        |   |
|   |                                               |   |
|   |            [ Explorar Catálogo ]              |   |
|   |                                               |   |
|   +-----------------------------------------------+   |
|                                                       |
|   CATEGORÍAS                                          |
|   [Viscoelásticas] [Bambú] [Látex] [Viaje]            |
|                                                       |
|   PRODUCTOS DESTACADOS                                |
|   +-------+   +-------+   +-------+   +-------+       |
|   | [Img] |   | [Img] |   | [Img] |   | [Img] |       |
|   | Título|   | Título|   | Título|   | Título|       |
|   | Precio|   | Precio|   | Precio|   | Precio|       |
|   | [Ver] |   | [Ver] |   | [Ver] |   | [Ver] |       |
|   +-------+   +-------+   +-------+   +-------+       |
|                                                       |
|   NUESTROS VALORES                                    |
|   (🌟 Envío Gratis) (🌟 100 Días Prueba) (🌟 +Calidad)|
|                                                       |
+-------------------------------------------------------+
|  © 2026 Reposa+. Todos los derechos reservados.       |
+-------------------------------------------------------+
```

## 2. Catálogo de Productos

Vista donde los usuarios pueden filtrar por categoría y ver toda la oferta.

```text
+-------------------------------------------------------+
|  [Logo Reposa+]     Inicio | Catálogo | Login | 🛒(2) |
+-------------------------------------------------------+
|                                                       |
|   CATÁLOGO DE PRODUCTOS                               |
|                                                       |
|   Filtros: [Todas] [Viscoelásticas] [Látex]           |
|                                                       |
|   +-------+   +-------+   +-------+                   |
|   | [Img] |   | [Img] |   | [Img] |                   |
|   | ♥ Fav |   | ♥ Fav |   | ♥ Fav |                   |
|   | Título|   | Título|   | Título|                   |
|   | Precio|   | Precio|   | Precio|                   |
|   | [Añadir al carrito]                           |                   |
|   +-------+   +-------+   +-------+                   |
|                                                       |
|   +-------+   +-------+   +-------+                   |
|   | [Img] |   | [Img] |   | [Img] |                   |
|   | ♥ Fav |   | ♥ Fav |   | ♥ Fav |                   |
|   | Título|   | Título|   | Título|                   |
|   | Precio|   | Precio|   | Precio|                   |
|   | [Añadir al carrito]                           |                   |
|   +-------+   +-------+   +-------+                   |
|                                                       |
+-------------------------------------------------------+
```

## 3. Perfil de Usuario (Panel y Wishlist)

```text
+-------------------------------------------------------+
|  [Logo Reposa+]  Inicio | Catálogo | Mi Perfil | 🛒 |
+-------------------------------------------------------+
|                                                       |
|  [ Panel Lateral ]      [ Contenido Principal ]       |
|  - Mis Datos            MIS FAVORITOS                 |
|  - Mis Direcciones      +-------------------------+   |
|  - Mis Pedidos          | [Img] Almohada Visco... |   |
|  - Mis Favoritos        | Precio: 45.99€          |   |
|  - Cerrar Sesión        | [Añadir a Carrito] [X]  |   |
|                         +-------------------------+   |
|                                                       |
|                         +-------------------------+   |
|                         | [Img] Almohada Bambú... |   |
|                         | Precio: 29.99€          |   |
|                         | [Añadir a Carrito] [X]  |   |
|                         +-------------------------+   |
|                                                       |
+-------------------------------------------------------+
```

## 4. Panel de Administración (Dashboard)

```text
+-------------------------------------------------------+
|  [Reposa+ ADMIN]            Hola, Admin | Salir       |
+-------------------------------------------------------+
|                                                       |
|  [ Menú ]               RESUMEN ESTADÍSTICO           |
|  - Dashboard                                          |
|  - Categorías           [ Productos Totales: 8 ]      |
|  - Productos            [ Usuarios Activos: 2 ]       |
|                         [ Pedidos Hoy: 5 ]            |
|                                                       |
|                         TOP PRODUCTOS FAVORITOS       |
|                         1. Almohada Visco (15 favs)   |
|                         2. Almohada Gel (10 favs)     |
|                                                       |
+-------------------------------------------------------+
```
