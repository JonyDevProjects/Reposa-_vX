# Registro de Cambios: fix/mailtrap-env

## Tareas Completadas
- [x] Se ha analizado el archivo de configuración `.env` y `.env.example`.
- [x] Se ha cambiado el controlador de correo (`MAIL_MAILER`) de `log` a `smtp`.
- [x] Se han introducido credenciales reales de Mailtrap para un entorno de pruebas (`sandbox.smtp.mailtrap.io`).
- [x] Se ha configurado el remitente a `hello@reposaplus.com` para mejorar el realismo.
- [x] Se ha actualizado el archivo `.env.example` para que el resto del equipo tenga la configuración base al clonar.

## Archivos Modificados
- `.env`
- `.env.example`
