# Registro de Correcciones - Seeders de Base de Datos

Este documento detalla las resoluciones de errores y mejoras implementadas en los datos de prueba del proyecto Reposa+.

---

## 1. Datos de demo insuficientes
- **Qué pasaba:** La base de datos tenía muy pocos registros (solo 3 productos y 2 usuarios), dejando vacías las secciones de historial, favoritos y direcciones.
- **Por qué:** El `DatabaseSeeder` no generaba datos suficientes para una demostración completa de la plataforma.
- **Qué decisiones se tomaron:** 
    - Se ha reescrito `DatabaseSeeder.php` para ampliar los datos de demostración.
    - Se ha pasado de 3 a 8 productos (almohadas) completos, con imágenes, material, firmeza y descripciones lógicas.
    - Se ha automatizado la creación de un perfil y una dirección por defecto para el usuario invitado (`user@reposaplus.com`).
    - Se ha añadido código para asignar 3 productos favoritos iniciales al usuario.
    - Se ha añadido la creación de 2 pedidos de prueba con `OrderItem` para que el historial de compras y el dashboard del administrador tengan datos que mostrar.
