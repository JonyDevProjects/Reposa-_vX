# Registro de Correcciones - Mockups del Proyecto

Este documento detalla las resoluciones de errores y mejoras implementadas sobre la documentación gráfica del proyecto Reposa+.

---

## 1. Ausencia de Mockups en la documentación
- **Qué pasaba:** No existían bocetos ni capturas de las vistas del proyecto en la carpeta `/docs`.
- **Por qué:** Faltaba crear la representación gráfica (mockups) solicitada en el enunciado del problema.
- **Qué decisiones se tomaron:** 
    - Se analizó la estructura de la aplicación y las rutas disponibles (`routes/web.php`) para identificar las pantallas clave: Home, Catálogo, Producto, Carrito, Perfil y Panel de Administración.
    - Se crearon esquemas estructurales (*wireframes* de baja fidelidad) en un documento unificado usando diagramas de bloques (sintaxis `block-beta` de Mermaid).
    - Se guardaron los diagramas en un nuevo archivo `wireframe-mocks.md` dentro de la carpeta `/docs` para resolver la falta de documentación gráfica requerida.
