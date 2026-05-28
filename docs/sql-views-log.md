# Registro de Correcciones - Vistas SQL

Este documento detalla las resoluciones de errores y mejoras implementadas sobre el uso de Vistas SQL en el proyecto Reposa+.

---

## 1. Falta de Vistas SQL
- **Qué pasaba:** El enunciado requería explícitamente el uso de vistas SQL, pero no existía ninguna en el proyecto.
- **Por qué:** No se habían creado migraciones con `DB::statement('CREATE VIEW ...')`.
- **Qué decisiones se tomaron:** 
    - (Por documentar durante el fix)
