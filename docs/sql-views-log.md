# Registro de Correcciones - Vistas SQL

Este documento detalla las resoluciones de errores y mejoras implementadas sobre el uso de Vistas SQL en el proyecto Reposa+.

---

## 1. Falta de Vistas SQL
- **Qué pasaba:** El enunciado requería explícitamente el uso de vistas SQL, pero no existía ninguna en el proyecto.
- **Por qué:** No se habían creado migraciones con `DB::statement('CREATE VIEW ...')`.
- **Qué decisiones se tomaron:** 
    - Se creó la migración `create_sql_views` para alojar dos vistas en la base de datos usando `DB::statement()`.
    - **Vista `v_order_summary`:** Agrupa a los usuarios y cruza sus pedidos usando un `LEFT JOIN`, permitiendo extraer el número de pedidos (`total_orders`) y el importe total gastado (`total_spent`) por usuario, optimizando el rendimiento.
    - **Vista `v_top_favorited_products`:** Cruza la tabla de productos con la tabla pivote de favoritos `favorite_product` mediante un `INNER JOIN`, contando la cantidad de favoritos (`favorited_by_count`) que tiene cada producto.
    - Se mapearon estas vistas en Eloquent creando modelos `OrderSummary` y `TopFavoritedProduct` configurados en modo de solo lectura (sin timestamps).
    - El `AdminController@dashboard` fue refactorizado para utilizar el modelo `TopFavoritedProduct` en lugar de una consulta agregada con Eloquent en bruto, haciendo el código más limpio.
    - En el `ProfileController@index`, se cargó eager-loading a la nueva relación `orderSummary` en el modelo `User`. La vista del perfil (`profile/index.blade.php`) se mejoró insertando dos nuevas tarjetas visuales de resumen empleando esta vista SQL, sumando gran valor a la experiencia de usuario de la tienda.
