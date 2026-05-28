# Workflow: Feature Generation (Laravel Specialist)

Este workflow guía al agente en la creación de nuevas funcionalidades siguiendo los estándares y la estricta metodología GitFlow de Reposa+.

// turbo-all

## Pasos

1. **Inicialización y GitFlow**:
   - Moverse a la rama `develop` y asegurar que esté actualizada (`git pull`).
   - Crear una nueva rama para la funcionalidad usando el prefijo en plural: `git checkout -b features/{nombre-feature}`.

2. **Documentación Continua (Logs)**:
   - Crear un archivo de registro en `docs/{nombre-feature}-log.md`.
   - Usar la plantilla estándar (basada en `checkout-fixes-log.md`) para documentar allí paso a paso cada error encontrado, su causa y las decisiones tomadas durante el desarrollo.

3. **Revisión de Arquitectura**:
   - Consultar `docs/EsquemaBBDD.md` para verificar relaciones.
   - Consultar `.agents/rules/reposa_core.md` para reglas de branding.

4. **Andamiaje Artisan**:
   - Ejecutar `php artisan make:model {name} -mfs` (Modelo, Migración, Factory, Seeder).
   - Crear el controlador: `php artisan make:controller {name}Controller`.

5. **Codificación Estandarizada**:
   - Aplicar tipos estrictos (PHP 8.2) según el skill `laravel-specialist`.
   - Implementar relaciones Eloquent y `FormRequest` para validaciones.

6. **Ciclo de Pruebas**:
   - Crear Test de Pest/PHPUnit.
   - Ejecutar `php artisan test`.
   - Verificar que no existan consultas N+1.

7. **Cierre de Feature**:
   - Actualizar `docs/Informe_Objetivos_Fases.md` reflejando el progreso global.
   - Realizar los commits necesarios en la rama `features/{nombre-feature}`.
   - Sincronizar con el repositorio remoto: `git push -u origin features/{nombre-feature}`.
