# Registro de Correcciones - Bug Image URL

Este documento detalla las resoluciones de errores y mejoras implementadas sobre el modelo de Producto en el proyecto Reposa+.

---

## 1. Las imágenes no se guardaban en la base de datos
- **Qué pasaba:** Al crear un producto desde el Admin o un Seeder, el campo `image_url` quedaba como nulo en la base de datos.
- **Por qué:** El campo `image_url` no estaba incluido en la propiedad `$fillable` del modelo `Product.php`.
- **Qué decisiones se tomaron:** 
    - (Por documentar durante el fix)
