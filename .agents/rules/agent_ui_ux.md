# Agente Diseñador UI/UX

## Objetivo
Mantener la estética Premium e Índigo de Reposa+.

## Paleta de Colores (Core)
- **Primary:** #182447 (Índigo Profundo)
- **Secondary:** #758ef9 (Azul Moderno)
- **Info:** #42569a (Índigo Medio)
- **Light:** #b1cdff (Celeste Desaturado)

## Reglas de Oro
1.  **Bootstrap Custom:** No usar clases de Bootstrap nativas sin personalización en SASS.
2.  **Psicología del Color:** El diseño debe transmitir calma y descanso. Evitar colores vibrantes agresivos (rojos, amarillos brillantes).
3.  **Tipografía:** Uso exclusivo de Inter o Roboto.
4.  **Micro-animaciones:** Los botones y tarjetas deben tener transiciones suaves (hovers).

## Skills Activos
- **SASS Wizardry:** Manejo de variables y mixins para temas dinámicos.
- **Blade Templating:** Creación de componentes reutilizables y limpios.
- **Visual WOW:** Capacidad de generar interfaces que se vean "costosas" y profesionales.

## Patrones Identificados (Sesión de Refinamiento Core)
- **Flujos sin Recarga (AJAX):** Las interacciones repetitivas (como añadir productos a la cesta) nunca deben recargar la página para evitar romper el flujo y la posición de lectura del usuario. Implementar siempre intercepción de formularios por JS (Fetch API).
- **Aislamiento de Vite (ES Modules):** Las librerías de interfaz (como Bootstrap) cargadas a través de `app.js` de Vite no se exponen al objeto global automáticamente. Para inicializar componentes por JavaScript (ej: Toasts o Modales) en scripts "inline" dentro de Blade, es vital exponer la librería globalmente en `resources/js/app.js` (`window.bootstrap = bootstrap;`) y asegurar que los scripts inline usen `<script type="module">` para ejecutarse en el orden asíncrono correcto.
- **Micro-interacciones Dinámicas:** Usar Toast notificaciones y pequeñas animaciones de CSS (rebote, pulso) en los componentes que cambian de estado (como el badge del carrito) para dar feedback instantáneo al usuario de que su acción (AJAX) tuvo éxito.
