# Documentación del Ecosistema de Agentes: Reposa+

Este documento resume la configuración de inteligencia implementada para el proyecto Reposa+, siguiendo los estándares de Antigravity.

## 1. Estructura de Directorios
La configuración reside en la raíz del espacio de trabajo para ser detectada globalmente:
- `.agents/rules/`: Reglas de comportamiento y contexto.
- `.agents/skills/`: Habilidades técnicas especializadas.
- `.agents/workflows/`: Guías de ejecución paso a paso.

## 2. Agentes Configurados (Rules)
| Agente | Archivo | Función |
| :--- | :--- | :--- |
| **Core** | `reposa_core.md` | Guarda la identidad visual (Índigo), branding y contexto del negocio. |
| **Backend** | `agent_backend.md` | Estándares de Laravel, seguridad y lógica MVC. |
| **Database** | `agent_database.md` | Integridad del esquema UML y optimización de Eloquent. |
| **UI/UX** | `agent_ui_ux.md` | Estética premium, SASS y psicología del color. |

## 3. Habilidades (Skills)
- **laravel-specialist**: (Configurado por el usuario) Proporciona estándares de PHP 8.2, uso de Enums, Service Containers y cobertura de tests >85%.

## 4. Flujos de Trabajo (Workflows)
Se han implementado flujos para automatizar tareas repetitivas y críticas:
- `feature_generation.md`: Creación de nuevas funcionalidades (M-V-C-T).
- `purchase_flow_test.md`: Verificación del ciclo de venta.
- `i18n_localization.md`: Gestión de multi-idioma.
- `security_audit.md`: Auditoría de accesos y validaciones.
- `image_branding_sync.md`: Integración de IA Generativa en el catálogo.

## 5. Uso
Para invocar estas capacidades, simplemente solicita una tarea relacionada. Por ejemplo:
*"Usa el workflow de feature_generation para crear el modelo de Carrito."*
