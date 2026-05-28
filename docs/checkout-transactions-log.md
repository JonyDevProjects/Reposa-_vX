# Registro de Correcciones - Transacciones en Checkout

Este documento detalla las resoluciones de errores y mejoras implementadas sobre las transacciones de base de datos en el Checkout del proyecto Reposa+.

---

## 1. Ausencia de Transacciones en el Checkout
- **Qué pasaba:** El proceso de compra no estaba envuelto en una transacción de base de datos.
- **Por qué:** El método `CartController@checkout` realizaba múltiples operaciones de escritura secuenciales sin protección `DB::transaction()`.
- **Qué decisiones se tomaron:** 
    - (Por documentar durante el fix)
