# Registro de Correcciones - Transacciones en Checkout

Este documento detalla las resoluciones de errores y mejoras implementadas sobre las transacciones de base de datos en el Checkout del proyecto Reposa+.

---

## 1. Ausencia de Transacciones en el Checkout
- **Qué pasaba:** El proceso de compra no estaba envuelto en una transacción de base de datos.
- **Por qué:** El método `CartController@checkout` realizaba múltiples operaciones de escritura secuenciales sin protección `DB::transaction()`.
- **Qué decisiones se tomaron:** 
    - Se envolvió la creación del `Order`, sus `OrderItem`s y el vaciado del `CartItem` en el método `checkout` dentro de `DB::transaction()` para garantizar la atomicidad de las operaciones y evitar pedidos huérfanos en caso de error.
