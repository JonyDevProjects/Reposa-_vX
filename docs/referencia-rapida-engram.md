# Referencia Rápida: Engram CLI

**Repositorio oficial:** [github.com/Gentleman-Programming/engram](https://github.com/Gentleman-Programming/engram)

---

## ¿Qué es Engram?

Engram es un sistema de memoria persistente para agentes de IA. Almacena observaciones (decisiones, arquitectura, sesiones, etc.) en una base de datos SQLite local, permitiendo que los agentes mantengan contexto entre sesiones.

---

## Comandos Clave en Este Proyecto

### Guardar una observación
```bash
engram save "<Título>" "<Detalle>" --type <tipo> --project reposaplus-tfg --scope <scope>
```

**Tipos disponibles:** `session`, `architecture`, `decision`, `discovery`, `lesson`, `observation`, `manual`, `reference`, `troubleshooting`, `learned`, `prompt`, `guideline`

**Scopes:** `project`, `architecture`, `decision`, `discovery`

**Ejemplo:**
```bash
engram save "Decisión: Usar Redis para sesiones" "**What**: Se decidió usar Redis para sesiones por rendimiento.
**Why**: MySQL tiene latencia de 5ms vs 0.5ms de Redis." --type decision --project reposaplus-tfg --scope decision
```

### Buscar observaciones
```bash
engram search "<query>" --project reposaplus-tfg --limit 10
```

### Ver línea de tiempo
```bash
engram timeline <obs_id> --before 5 --after 5
```

### Sincronizar (exportar/importar chunks)
```bash
# Exportar observaciones de la BD a archivos .engram/chunks/
engram sync

# Importar chunks desde archivos a la BD
engram sync --import

# Ver estado de sincronización
engram sync --status
```

### Lanzar TUI (interfaz interactiva)
```bash
engram tui
```

### Verificar estado
```bash
engram stats
```

---

## Arquitectura de Engram

```
~/.engram/engram.db          ← Fuente de verdad (SQLite + FTS5)
         ↓
    Engram TUI / CLI lee de aquí
         ↓
    .engram/chunks/           ← Exportaciones (backup/sync entre dispositivos)
```

**Punto crítico:** La BD SQLite es la fuente de verdad. Los archivos `.engram/chunks/` son exportaciones que pueden quedarse obsoletas si no se ejecuta `engram sync` regularmente.

---

## Integración en Este Proyecto

### Ubicación
- **BD:** `~/.engram/engram.db`
- **Chunks:** `.engram/chunks/` (excluido de Git via `.gitignore`)
- **Binario:** `/opt/homebrew/bin/engram`

### Proyecto en Engram
```bash
--project reposaplus-tfg
```

### Flujo de Trabajo Recomendado

1. **Durante la sesión:** Usar `engram save` para persistir decisiones clave
2. **Al finalizar sesión:** Ejecutar `engram sync` para exportar cambios
3. **En otra máquina:** Clonar repo + `engram sync --import` para traer memorias

---

## Errores Comunes

### 1. Chunks desincronizados
**Síntoma:** La TUI muestra IDs que no existen o datos inconsistentes.
**Causa:** Los chunks en `.engram/chunks/` están obsoletos.
**Solución:** Ejecutar `engram sync` para actualizar, o eliminar chunks obsoletos.

### 2. Conflicto de IDs
**Síntoma:** Al importar chunks, se sobreescriben observaciones existentes.
**Causa:** Los IDs en los chunks ya existen en la BD.
**Solución:** No importar chunks de otras fuentes sin verificar IDs.

### 3. Directorio chunks vacío
**Síntoma:** `.engram/chunks/` no tiene archivos.
**Normal:** Es el estado esperado si no se usa `engram sync`.

---

## Documentación Adicional

- **Repositorio:** [github.com/Gentleman-Programming/engram](https://github.com/Gentleman-Programming/engram)
- **Documentación oficial:** Ver README en el repositorio
- **Instalación:** `brew install engram` o descargar desde GitHub Releases
- **MCP Server:** `engram mcp` para integrar con agentes de IA
- **HTTP API:** `engram serve` para acceso programático

---

## Convenciones en Este Proyecto

| Campo | Valor |
|-------|-------|
| **Project** | `reposaplus-tfg` |
| **Tipos más usados** | `session`, `decision`, `architecture` |
| **Alcance (scope)** | `project` para decisiones generales, `architecture` para diseño, `decision` para elecciones técnicas |

---

**Documento generado:** 2026-09-23  
**Versión de Engram:** v1.20.0 (actualizable a v2.0.0)
