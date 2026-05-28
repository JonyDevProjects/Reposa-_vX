# Registro de Correcciones - Eager Loading en Perfil

Este documento detalla las resoluciones de errores y mejoras de rendimiento implementadas en el perfil de usuario del proyecto Reposa+.

---

## 1. Consultas N+1 en favoritos
- **Qué pasaba:** La vista de perfil generaba consultas adicionales a la base de datos para cargar los favoritos del usuario.
- **Por qué:** El método `ProfileController@index` no incluía la relación `favorites` en la carga anticipada (`eager loading`).
- **Qué decisiones se tomaron:** 
    - (Por documentar durante el fix)
