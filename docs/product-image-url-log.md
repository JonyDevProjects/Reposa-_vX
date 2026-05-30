# Registro de Correcciones - Bug Image URL

Este documento detalla las resoluciones de errores y mejoras implementadas sobre el modelo de Producto en el proyecto Reposa+.

---

## 1. Las imágenes no se guardaban en la base de datos
- **Qué pasaba:** Al crear un producto desde el Admin o un Seeder, el campo `image_url` quedaba como nulo en la base de datos.
- **Por qué:** El campo `image_url` no estaba incluido en la propiedad `$fillable` del modelo `Product.php`.
- **Qué decisiones se tomaron:** 
    - Se agregó el campo `image_url` al array `$fillable` en el modelo `Product.php` para habilitar la asignación masiva, permitiendo que la URL de la imagen se guarde en la base de datos al crear un producto mediante el panel de administración o un seeder.
