# Informe de Objetivos y Fases: Proyecto Reposa+

## 1. Introducción
Este informe detalla la hoja de ruta para el desarrollo de **Reposa+**, un e-commerce especializado en almohadas ergonómicas e inteligentes. El proyecto se enmarca en los requisitos de la **EPD 3: Frameworks – Laravel**, integrando un diseño premium basado en la psicología del color (Índigo) y una arquitectura técnica robusta.

---

## 2. Objetivos del Proyecto
Basados en el documento de requisitos `EPD3 - 2526.md`, los objetivos principales son:

- **Operaciones CRUD:** Implementar la gestión completa de productos, categorías y pedidos utilizando Laravel y Bootstrap 5.
- **Caso de Uso Principal:** Garantizar el flujo completo de compra para usuarios registrados, permitiendo a los no registrados navegar por el catálogo.
- **Autenticación y Seguridad:** Sistema completo de Login/Signin con roles diferenciados (Usuario vs Administrador) y recuperación de contraseñas vía SMTP.
- **Arquitectura de Datos:** Implementación fiel del esquema UML con relaciones 1:1, 1:N y N:M.
- **Internacionalización:** Soporte multi-idioma (Español/Inglés) en toda la plataforma.
- **Experiencia de Usuario (UX):** Diseño de interfaces realistas y funcionales con personalización significativa de Bootstrap 5.

---

## 3. Fases del Proyecto
El desarrollo se divide en fases incrementales alineadas con los problemas definidos en la EPD:

### Fase 1: Cimentación y Core (v1.0) - *Problema 0, 1 y 2*
- **Setup Técnico:** Configuración de Laravel, Bootstrap 5 y sistema de autenticación (Fortify/Breeze).
- **Base de Datos:** Migración del esquema UML (Refactorizado para el nicho de almohadas).
- **Catálogo Público:** Implementación de la vista de productos accesible para todos los usuarios.
- **Flujo de Compra:** Desarrollo del carrito, proceso de checkout y generación de pedidos.
- **Notificaciones:** Integración de Mailtrap para correos de confirmación de pedido y recuperación de claves.
- **Panel Admin v1:** Gestión básica de productos y visualización de pedidos.

### Fase 2: Clasificación y Gestión Avanzada (v2.0) - *Problema 3*
- **Sistema de Categorías:** Implementación de la relación N:M entre productos y categorías (Ej: Cervical, Viscoelástica).
- **CRUD de Categorías:** Interfaz administrativa para gestionar etiquetas y su asociación con almohadas.
- **Filtrado:** Navegación por categorías en el front-end.

### Fase 3: Internacionalización y Perfil (v2.1) - *Problema 4*
- **Multi-idioma:** Traducción de la Home y vistas principales a Inglés y Español.
- **Gestión de Perfil:** Panel de usuario para cambiar contraseña, gestionar direcciones (CRUD) y ver historial de pedidos.

### Fase 4: Fidelización y Analítica (v2.2) - *Problema 5*
- **Lista de Favoritos (Wishlist):** Funcionalidad de clic rápido para guardar/quitar productos de favoritos.
- **Analíticas Admin:** Panel para que el administrador vea las almohadas más populares entre los usuarios.

---

## 4. Estado Actual del Proyecto (Actualizado)
Al finalizar la sesión actual, el proyecto ha alcanzado la **v1.1-beta**:

- **Ecosistema de Agentes:** 🟢 COMPLETADO. Estructura `.agents/` oficial configurada con reglas, skills y workflows.
- **Base de Datos (Hito 1):** 🟢 COMPLETADO. Esquema UML íntegro con datos de prueba.
- **Identidad y Catálogo (Hito 2):** 🟢 COMPLETADO. Home, Catálogo y Ficha de Producto funcionales.
- **Autenticación y Perfil (Hito 3):** 🟢 COMPLETADO. Login/Registro y CRUD de direcciones activos.
- **Flujo de Compra (Problema 2):** 🟢 COMPLETADO. Carrito sin barreras en sesión, Checkout protegido con volcado inmutable a BBDD (`orders` / `order_items`) y Notificaciones dinámicas visuales (Toasts).
- **Filtrado Exploratorio (Problema 3):** 🟢 COMPLETADO. Navegación por categorías cruzadas con Sidebar y Cajas Visuales, integradas eficientemente con el modelo.

**Próximo Paso Crítico:** Continuar con la Fase 2 (CRUD administrativo de categorías) y Fase 3 (Internacionalización Multi-idioma).

---

## 5. Próximos Pasos Inmediatos
1.  **Panel Admin (Gestión de Categorías):** Implementar la interfaz administrativa para el CRUD de categorías y su asociación con almohadas.
2.  **Internacionalización (i18n):** Traducir la vista `Home` y el flujo de compra a Inglés y Español.
3.  **Wishlist y Favoritos:** Iniciar la Fase 4 para añadir almohadas a favoritos (Problema 5).
