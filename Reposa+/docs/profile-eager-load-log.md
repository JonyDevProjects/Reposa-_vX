# Registro de Cambios: fix/profile-eager-load

## Tareas Completadas
- [x] Se ha analizado el controlador `ProfileController`.
- [x] Se ha detectado un problema de N+1 consultas (queries) al renderizar la sección de "Favoritos" en la vista de perfil.
- [x] Se ha modificado el método `index()` en `app/Http/Controllers/ProfileController.php`.
- [x] Se ha añadido la relación `favorites` a la lista de relaciones precargadas (`$user->load()`).

## Archivos Modificados
- `app/Http/Controllers/ProfileController.php`
