# Registro de Cambios: fix/database-seeders

## Tareas Completadas
- [x] Se ha reescrito `DatabaseSeeder.php` para ampliar los datos de demostración.
- [x] Se ha pasado de 3 a 8 productos (almohadas) completos, con imágenes, material, firmeza y descripciones lógicas.
- [x] Se ha automatizado la creación de un perfil y una dirección por defecto para el usuario invitado (`user@reposaplus.com`).
- [x] Se ha añadido código para asignar 3 productos favoritos iniciales al usuario.
- [x] Se ha añadido la creación de 2 pedidos de prueba con `OrderItem` para que el historial de compras y el dashboard del administrador tengan datos que mostrar.

## Archivos Modificados
- `database/seeders/DatabaseSeeder.php`
