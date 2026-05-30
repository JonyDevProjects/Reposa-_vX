# Análisis de Ramas y Duplicidad en Git

## Hallazgo
Durante la sincronización del repositorio, se ha identificado una duplicidad en el nombramiento de las ramas. Existen ramas idénticas (apuntando al mismo commit) con los prefijos `feature/` (singular) y `features/` (plural).

- `feature/roles-admin-panel` y `features/roles-admin-panel`
- `feature/notificacion-correo` y `features/notificacion-correo`

## Explicación Lógica
Este tipo de duplicidad generalmente responde a una de estas causas:

1. **Error Humano / Desconocimiento de Convenciones (GitFlow):** El estándar más común en la industria (GitFlow) sugiere usar el prefijo en singular (`feature/`). Es muy común que, al trabajar en equipo, un desarrollador utilice el plural por costumbre o error tipográfico, creando ramas mixtas.
2. **Renombrado Incompleto de Ramas:** Es probable que un desarrollador notara el error localmente, renombrara su rama para seguir la convención, y volviera a hacer `push` al remoto. Si no se elimina manualmente la rama antigua del repositorio remoto (ej. GitHub), ambas ramas coexistirán apuntando al mismo hash de commit.
3. **Cambio de Estándar en el Proyecto:** Puede que el equipo haya acordado cambiar la nomenclatura de `feature/` a `features/` (dado que las ramas más recientes usan el plural). Al hacer la transición, las ramas viejas se pudieron subir de nuevo con el nuevo nombre para homogeneizar el historial.

## Decisión Tomada
Dado que los últimos trabajos (incluyendo el inicio de la **Fase 2**) utilizan la nomenclatura plural, nos hemos alineado a este formato. El entorno local ha sido cambiado exitosamente a la rama **`features/sistema-categorias`**.

## Hallazgo Adicional: Ramas del Problema 4 (Internacionalización y Perfil)
Se ha detectado confusión respecto a las ramas destinadas a la Fase 3 - Problema 4 (Internacionalización y Perfil de Usuario v2.1). En el repositorio coexisten tres ramas relacionadas:
- `features/internacionalizacion-perfil-usuario`
- `features/soporte-multi-idioma`
- `features/perfil-cliente`

### Explicación y Estado Actual
1. **`features/internacionalizacion-perfil-usuario`**: Esta es una rama antigua remota. Su último commit (`5361139 zip grupo indigo`) **ya se encuentra integrado (merged) en `develop`**. Dado que su código ya forma parte del historial principal, la rama puede ser eliminada del remoto con seguridad para evitar confusión en el equipo.
2. **`fix/update-agent-workflows`**: Esta rama fue utilizada para actualizar las reglas de Gitflow y el ecosistema de agentes, y **también se encuentra mergeada en `develop`** correctamente. El ecosistema de agentes se actualizó de forma independiente.
3. **Nuevas Ramas Oficiales (`features/soporte-multi-idioma` y `features/perfil-cliente`)**: Para mantener la estricta separación de responsabilidades (Single Responsibility Principle) y facilitar el trabajo en paralelo, el Problema 4 se dividió de forma proactiva en estas dos ramas independientes. Ambas son las ramas oficiales y limpias sobre las que el equipo debe implementar la Fase 3.
