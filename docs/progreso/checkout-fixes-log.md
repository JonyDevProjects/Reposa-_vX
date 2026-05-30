# Registro de Correcciones y Refinamientos - Core Flow (Compra)

Este documento detalla las resoluciones de errores y mejoras de Experiencia de Usuario (UX) implementadas sobre el Proceso de Compra (Problema 2) en el proyecto Reposa+.

---

## 1. Error de Vista Inexistente tras Checkout (Error 500)
- **Qué pasaba:** Al completar un pedido y hacer clic en "Finalizar Compra", la aplicación arrojaba un Internal Server Error indicando `View [profile.orders] not found.`.
- **Por qué:** El método `CartController@checkout` redirigía a la ruta `orders.index`, cuyo controlador intentaba buscar el archivo `profile.orders.blade.php`. Sin embargo, la interfaz de usuario se había consolidado previamente de manera que el historial de pedidos reside dentro de la vista principal del perfil (`profile/index.blade.php`).
- **Qué decisiones se tomaron:** Se modificó la redirección final del `checkout` para que apunte directamente a `/profile#orders`. Esto devuelve al usuario a su panel de control y automáticamente hace scroll hacia la sección pertinente.

## 2. Error 404 al Loguearse como Invitado desde el Carrito
- **Qué pasaba:** Si un usuario sin registrar navegaba hasta la cesta de la compra y pulsaba en "FINALIZAR COMPRA", el sistema lo enviaba al Login. Al ingresar, el usuario se encontraba con un Error 404 Not Found (intentando acceder a `/home`).
- **Por qué:** El botón para los usuarios invitados era un simple hipervínculo `<a href="/login">`. Laravel no guardaba en sesión de dónde venía el usuario (`url.intended`). Tras el login, Laravel Fortify recurría a la ruta de "rescate" por defecto configurada en `config/fortify.php`, la cual era `/home` (una ruta inexistente en el proyecto).
- **Qué decisiones se tomaron:** 
    1. Se creó un método puente `CartController@requireLogin` y la ruta `/cart/login`. Al pulsar el botón, este controlador susurra a Laravel que la ruta a la que debe volver el usuario tras el login es `/cart` y entonces redirige a la pantalla de validación.
    2. Se cambió la variable `home` en la configuración de Fortify apuntando a `/profile` para mitigar fallos similares a futuro.

## 3. Problema UX: Recargas Molestas al Añadir al Carrito
- **Qué pasaba:** Al pulsar el icono del carrito en las tarjetas del catálogo o inicio, el producto se añadía bien, pero la página se recargaba por completo, reseteando la posición del scroll de forma brusca hacia arriba de la web.
- **Por qué:** El formulario enviaba una petición clásica `POST` al servidor y el controlador respondía con un `back()`. Esto forzaba al navegador a redibujar toda la interfaz, arruinando la sensación de "App Moderna".
- **Qué decisiones se tomaron:** Se integró Javascript asíncrono (AJAX) en `layouts/app.blade.php`. Ahora el JS intercepta todos los formularios de "añadir producto", manda la petición de fondo y el `CartController` devuelve un paquete JSON con el nuevo total. Esto permite actualizar el globo rojo de la cesta en la cabecera e invocar un Toast verde, *todo ello sin recargar la pantalla actual*.

## 4. Botón Inoperativo en la Ficha de Producto
- **Qué pasaba:** Al entrar en el detalle de una almohada (p. ej. "Almohada Viscoelástica Premium") e intentar añadir productos al carrito mediante el botón grande, la página no hacía nada.
- **Por qué:** A nivel de HTML en `catalog/show.blade.php`, todo el bloque de botones y selector numérico era solo estructura visual (etiquetas `<div>`, `<button>`); no estaban empaquetados dentro de un formulario `<form>` que conectase con la ruta correspondiente en el servidor.
- **Qué decisiones se tomaron:**
    - Se envolvió el conjunto en un `<form action="...">`.
    - Se incluyó lógica nativa de JS (`stepUp` / `stepDown`) en los botones de `-` y `+` para que aumentaran/disminuyeran un `<input type="number" name="quantity">`.
    - Se ajustó la lógica en el `CartController` para asimilar el valor de `quantity` y sumar N cantidad en lugar de 1 siempre.
    - Al usar este enfoque, el sistema heredó la magia de AJAX automáticamente gracias al interceptor general definido en el punto 3.

## 5. Notificación Dinámica Invisible por Conflicto con Vite
- **Qué pasaba:** A pesar de haber cumplido todos los requisitos tras tramitar el pago (redirección y sesión *flashed*), el Toast verde emergente post-compra no se pintaba en la interfaz al llegar a `/profile`.
- **Por qué:** Laravel usa **Vite** para compilar su código, procesando JavaScript mediante módulos (ES Modules - `type="module"`), lo que aísla las librerías para no contaminar el objeto global `window`. El bloque de código que dibujaba nuestro Toast intentaba llamar a `new bootstrap.Toast()`, pero como `bootstrap` no estaba expuesto, daba un error fatal silencioso (`ReferenceError`), deteniendo la ejecución antes de invocar la notificación.
- **Qué decisiones se tomaron:**
    - Se procedió a editar `resources/js/app.js` ordenándole a Vite que exponga la instancia al ecosistema (`window.bootstrap = bootstrap;`).
    - Se recompiló la aplicación mediante `npm run build`.
    - Se reconfiguró el *script* en el HTML para que asuma también el rol de módulo encolándose cronológicamente detrás de Bootstrap, asegurando el disparo correcto de los componentes gráficos del servidor.
