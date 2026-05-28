# Informe y Plan de Acción: E-Commerce (Reposa+)

Este documento estructura los primeros pasos y el desarrollo de un e-commerce orientado a un nicho específico, basado en los requisitos de la EPD3 (Laravel). Aplicaremos un esquema conceptual y de branding coherente con la temática, y detallaremos la solución a cada uno de los problemas planteados.

---

## 1. Visión General y Branding (Base para el Problema 0)

### 1.1. Idea del E-Commerce y Objetivos
Se desarrollará una tienda online especializada en **almohadas ergonómicas e inteligentes pensadas para mejorar la calidad del sueño**. Es un producto de nicho enfocado en la salud y el descanso, dirigido a personas con problemas cervicales, insomnio o simplemente buscando optimizar su descanso nocturno.

- **Propuesta de valor:** "No vendemos almohadas, vendemos noches de sueño profundo y reparador."
- **Caso de uso principal (comprar):** Un cliente navega por el catálogo buscando una almohada específica (ej. para aliviar dolor cervical), consulta sus características (material, firmeza), la añade al carrito, se registra o autentica, y finaliza el proceso de compra.
- **Objetivos y Requisitos:** Ofrecer una plataforma funcional, intuitiva y estéticamente agradable, donde se resalte las propiedades de cada producto para el descanso, con un sistema de filtrado claro y un proceso de compra confiable y sin fricciones, cumpliendo con la exigencia de presentar un comercio electrónico lo más realista posible.

### 1.2. Diseño y Colores (Psicología del color)
El color principal asignado es el **Índigo / Blue-Indigo**, el cual encaja a la perfección con la psicología del sueño. El índigo y los tonos azules oscuros transmiten calma, serenidad, profundidad y están intrínsecamente asociados con la noche, preparando psicológicamente al usuario para el descanso e inspirando confianza.

Usaremos la siguiente paleta de colores aplicando **Bootstrap 5**, y personalizaremos exhaustivamente una plantilla base mediante variables CSS/SASS para garantizar que las modificaciones sean significativas y acorde a nuestra imagen de marca (tal como estipula el Problema 0):

- **Neutral Fuerte y Acentos:** `#000000` y Blancos puros para contraste óptimo.
- **Primario Oscuro:** `#182447` (Navbar, Headers, Botones Primarios - Recordando el cielo nocturno profundo).
- **Primario Medio:** `#42569a` (Hovers, Bordes, Fondos secundarios - Sensación de transición y relajación visual).
- **Secundario (Accent):** `#758ef9` (Llamadas a la acción secundarias, Detalles visuales - Un toque moderno e inteligente).
- **Fondos Suaves:** `#b1cdff` e índigos muy desaturados (Para destacar tarjetas de producto limpias que evocan ligereza y confort absoluto).

---

## 2. Fase de Setup y Configuración Inicial (Problema 0)

Para asegurar un desarrollo progresivo y ordenado cumpliendo las bases metodológicas, se establecen las siguientes acciones preliminares de setup:

1. **Gestión de Equipo y Roles:**
   - Confirmar el grupo de 3 integrantes y distribuir tareas a lo largo del proceso.
2. **Control de Versiones y Metodología Ágil (Tablero Kanban):**
   - Creación de un repositorio público en GitHub. Será indispensable ya que se deberá entregar la URL de una "Release" funcional.
   - Configuración de un Tablero Kanban (GitHub Projects o Trello) documentando historias de usuario, requerimientos y dividiendo el trabajo en fases como *To-do*, *In Progress* y *Done*.
3. **Setup de Backend (Laravel) y Frontend (Bootstrap 5):**
   - Inicialización del proyecto Laravel haciendo uso de Composer.
   - Integración de sistema de autenticación, por ejemplo Laravel Fortify o Laravel Breeze.
   - Integración de la plantilla base combinada con Vite (o Laravel Mix) para sobreescribir los estilos nativos de Bootstrap 5 y compilar los assets propios (SASS), aplicando nuestra paleta del sueño.
4. **Diseño de Interfaz e Interacción (Mockups):**
   - Generar Mockups previos o bocetos funcionales de: la página de Inicio (*Home* con *hero section* de descanso), vista de Catálogo, Ficha Detalle de Producto, vista del Carrito, y Panel/Dashboard de Admin.

---

## 3. Arquitectura y Esquema de Base de Datos (Problema 1)

El esquema UML de base de datos se estructurará abarcando todas las necesidades fundamentales del e-commerce requeridas, asegurando las cardinalidades exigidas (mínimo una de 1:1, 1:N y N:M):

### 3.1. Entidades Principales y Relaciones
- **Usuarios, Perfiles y Direcciones (1:1 / 1:N):**
  - Un usuario (`USER`) tiene 1 o varias `ADDRESS` (direcciones de envío) demostrando relación 1:N. Podría integrarse un perfil de estadísticas o preferencias de sueño en una relación 1:1 estricta.
- **Productos (Almohadas) y Pedidos (1:N):**
  - La tabla `PRODUCT` centralizará las propiedades de las almohadas (material de relleno, grado de firmeza, precio, stock).
  - Un usuario ejecuta 0 o varios pedidos (`ORDER`) (1:N). Múltiples pedidos pueden tener múltiples productos conectados gracias a la tabla de detalle `ORDER_ITEM`.
- **Categorías (N:M) [Resolviendo el Problema 3]:**
  - Existirán varias clasificaciones (ej. Cervical, Anti-Ronquidos, Látex, Espuma Viscoelástica). Un producto tiene muchas categorías y una categoría muchos productos.
  - Generación de la tabla pivote `CATEGORY_PRODUCT`.
- **Favoritos (N:M) [Resolviendo el Problema 5]:**
  - Relación de pertenencia afectiva entre `USER` y `PRODUCT`.
  - Definición de la tabla pivote de deseos como `FAVORITE_PRODUCT`.

*A nivel de framework, se programarán las Migraciones correspondientes para reflejar el esquema UML a SQL, así como los Seeders y Factories para poblar el catálogo de almohadas de inmediato y disponer de usuarios y administradores de validación.*

---

## 4. Implementación del Core: Caso de Uso Principal v1.0 (Problema 2)

El desarrollo crítico inicial habilitará el flujo básico: navegar por la tienda, registrarse simulando una compra (cesta) y gestionar los pedidos.

1. **Catálogo y Compra sin Barreras:**
   - Todo visitante no registrado o anónimo puede observar el espectro total de almohadas en la tienda, además de entrar a fichas detalladas averiguando beneficios ergonómicos concretos y añadiendo sus ítems a la cesta de pre-compra.
2. **Proceso de Compra (Checkout) y Autenticación:**
   - La acción de "Tramitar Pedido" o revisar efectivamente el ticket final exigirá ser un usuario logueado en la plataforma.
   - De ser un usuario validado, el acto de compra insertará registros inmutables en `ORDER` y asociará el inventario `ORDER_ITEM`.
   - Aparecerá obligatoriamente una notificación dinámica a nivel visual en el front manifestando la concreción de la operación.
3. **Notificación por Correo (SMTP):**
   - Configuración de la pasarela local/en entorno, como **Mailtrap**, parametrizada desde el `.env`. Se lanzará un correo automático post-compra remitiendo el ticket generado, y adicionalmente el sistema SMTP cubrirá la eventual "Recuperación de Contraseñas" que Laravel estipula mediante *Forgot Password/Reset*.
4. **Roles y Panel del Administrador (Separación de Capacidades):**
   - Programación de Middleware estricto para seccionar de por vida las rutas web (Ej: un User carece de poder sobre las directrices `/admin/*`).
   - El admin utilizará un CRUD para inyectar al sistema diferentes almohadas, manipular inventario de productos y ejercer una visión omnipresente sobre el histórico global de transacciones gestionadas por el cliente.

---

## 5. Gestión de Categorías v2.0 (Problema 3)

Se evoluciona la plataforma con un sistema de clasificación complejo acorde al nicho:

1. **Gestión Backend en Código:**
   - Uso de las relaciones n-n nativas de Eloquent (`belongsToMany`) interactuando entre Modelos `Product` (almohada) y `Category` interactuando contra la BD Pivote.
   - El formulario oficial del CRUD de administrador para Alta/Edición de almohadas será dotado de un área múltiple o checkboxes, capaz de inyectar o quitar (sync) todas las categorías pertinentes a esa almohada a la vez.
2. **Filtrado Exploratorio Eficiente:**
   - A nivel Front, la *Home page* poseerá visibilidad a nivel de categorización cruzada, visualizando tanto desde un Sidebar como de cajas visuales, todas las propiedades orgánicas de interés (Ej: "Las mejores almohadas Cervicales" o "Almohadas Térmicas").
   - Las consultas por modelo recogerán dicho target y refrescarán la capa visual limitando la parrilla de productos expuestos, mejorando la usabilidad.

---

## 6. Internacionalización y Perfil de Usuario v2.1 (Problema 4)

1. **Internacionalización (i18n):**
   - Construcción de ficheros nativos de idioma (`lang/en`, `lang/es`) dentro del árbol de Laravel.
   - Plena transcripción funcional de la vista principal (*Main Index/Home*) del sistema. Todo argumento de salud, ergonomía y botón de compra de una almohada operará bajo un formato condicional dual *(Castellano/Inglés)* dictaminado por la variable de entorno y la propia selección manual realizada por el usuario en vida, grabadas en memoria.
2. **Administración Extensa de Perfil de Cliente:**
   - Centralización bajo un enlace "Ajustes de Perfil/Mi Perfil" propio de toda cuenta válida de cliente.
   - Habilitación para visualización de su historial individual de tickets y envíos de sus almohadas.
   - Habilitación para Gestión y alteración (CRUD) de domicilios / Puntos de Entrega.
   - Inclusión de capacidades internas para reconfiguración del acceso propio (Cambiar Password) validado por Hash y la consecuente función genérica de abandono de Log (Cierre de sesión).

---

## 7. Sistema de Retención de Clientes v2.2 (Problema 5)

Estrategia funcional para mejorar la permanencia y conversión apelando de nuevo a las decisiones dinámicas (Lista de deseados):

1. **Lista de Favoritos Interactiva (Wishlist):**
   - Empotrar de forma elegante, con la iconografía pertinente a la paleta, una marca para "Fav o Corazon" bajo toda cuadrícula particularizada de las distintas Almohadas.
   - Al clicar el elemento, usando la entidad N:M, el usuario anclará el objeto de manera automática. Al volver a oprimir, lo extinguirá del favoritismo.
2. **Espacio Dedicado Intra-Perfil:**
   - Extensión paralela del Panel de Perfil de Usuario, aportando una pestaña vitalícia titulada "Las almohadas que más me gustan" para recordar compras futuras del sujeto y tenerlas en su radar.
3. **Analítica de Demanda para el Staff de Venta:**
   - Los moderadores o roles Admin contarán en la entrada del panel interior de la plataforma, un espacio donde recabar con rapidez analítica, enumerando al menos una estadística ordenada, como el *Top de Almohadas con mayores expectativas de compra* apoyado por métodos como `withCount()` cruzado con SQL de agrupación.

---

## 8. Documentación Continua (Problema 6)

El ciclo vital de entrega obligatoria requerirá un proceso documental perenne apoyando todo cambio:

1. **Esquema Técnico Transversal:** Conservar, publicar y actualizar los Diagramas UML finalistas a medida que se inserten o crucen esquemas en la fase Back-End o muten campos cardinales. Documentar si fuera el caso uso de Vistas o Eventos puramente SQL.
2. **Justificación Decisoria Global:** Justificar mediante breves minutas de forma oficial la apropiación selecta de determinados complementos, cómo se alteró sustancialmente la plantilla Bootstrap con CSS hasta adquirir el tono "Índigo/sueño".
3. **Contrastación con la Realidad (Mockups vs Realidad):** Proveer las pruebas documentales que atestiguan cómo las pantallas planeadas preliminarmente contrastan respecto al aplicativo HTML final emitido por el compilador Blade de Laravel.
4. **Validaciones Release:** Cargar copias empaquetadas correctas localizadas mediante URL y links dentro del registro original o memoria de final del periodo en la Asignatura.

**Estado de Ejecución:** La fase de cimentación (v1.0), la gestión compleja de categorías (v2.0) y la internacionalización de la plataforma (i18n) para la Home y el Catálogo han sido completadas con éxito en la rama `features/soporte-multi-idioma`.

**Próximo plan de acción técnico:** Continuar la Fase 3 (v2.1) extendiendo las capacidades del Perfil de Usuario con el cambio de credenciales, gestión de direcciones y el historial detallado de pedidos en su rama correspondiente (`features/perfil-cliente`).
