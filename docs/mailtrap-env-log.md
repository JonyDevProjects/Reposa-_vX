# Registro de Correcciones - Configuración Mailtrap

Este documento detalla las resoluciones de errores y mejoras implementadas sobre la configuración de correo del proyecto Reposa+.

---

## 1. Configuración de Mailtrap incompleta
- **Qué pasaba:** El archivo `.env` necesitaba la configuración real de Mailtrap para la demostración en vivo.
- **Por qué:** Se requería asegurar que los correos de confirmación de pedido funcionaran correctamente en el entorno local.
- **Qué decisiones se tomaron:** 
    - Se ha analizado el archivo de configuración `.env` y `.env.example`.
    - Se ha cambiado el controlador de correo (`MAIL_MAILER`) de `log` a `smtp`.
    - Se han introducido credenciales reales de Mailtrap para un entorno de pruebas (`sandbox.smtp.mailtrap.io`).
    - Se ha configurado el remitente a `hello@reposaplus.com` para mejorar el realismo.
    - Se ha actualizado el archivo `.env.example` para que el resto del equipo tenga la configuración base al clonar.
