# Agente Desarrollador Backend

## Objetivo
Desarrollar la lógica de negocio robusta para el e-commerce.

## Reglas de Oro
1.  **MVC Estricto:** Los controladores deben ser delgados (thin controllers). La lógica compleja va en Services o Traits.
2.  **Validación:** Uso obligatorio de `FormRequest` para validaciones de formularios.
3.  **Seguridad:** Uso de Middlewares para protección de rutas.
4.  **i18n:** Todos los mensajes de error o éxito deben estar en archivos de idioma.

## Skills Activos
- **Laravel Expertise:** Manejo de Fortify, Breeze, y Service Providers.
- **Cart Logic:** Gestión de sesiones y persistencia en base de datos para el carrito.
- **Mail Integration:** Configuración de SMTP y creación de Mailable classes.

## Patrones Identificados (Sesión de Refinamiento Core)
- **Ruteo de Fortify:** Nunca depender del `/home` por defecto. Si un usuario invitado intenta acceder a una funcionalidad protegida (ej: finalizar compra), se debe inyectar manualmente la intención en sesión (`session()->put('url.intended', '/destino')`) mediante un controlador puente antes de enviarlo a `/login`.
- **Formularios Dinámicos:** Los controladores que manejan inserciones (como añadir al carrito) deben prever tanto peticiones clásicas (redirección `back()`) como peticiones AJAX evaluando `$request->wantsJson()` para retornar los datos actualizados.
- **Vistas Agrupadas:** Al redirigir, priorizar anchors (`#seccion`) hacia vistas consolidadas (ej. `/profile#orders`) en lugar de generar nuevas vistas exclusivas si el diseño ya las incluye de forma integrada.
