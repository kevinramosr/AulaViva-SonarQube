# S08 - DevSecOps Readiness

## Objetivo

Registrar las dependencias y bloqueos identificados antes de implementar
el pipeline CI/CD y la estrategia GitOps de AulaViva.

## Estado actual

La rama S08 parte desde main y actualmente contiene documentación,
pero las entregas de aplicación e infraestructura de sesiones anteriores
se encuentran en ramas separadas.

## Dependencias

### Aplicación

Se requiere disponer de código ejecutable, dependencias y comandos
oficiales para lint y pruebas automatizadas.

Responsable:
Desarrollo / Tech Lead

### Tests y cobertura

Se requiere una suite automatizada que permita medir cobertura y
cumplir el mínimo del 70%.

Responsable:
QA / Desarrollo

### Docker

Se requiere un Dockerfile funcional y compatible con la aplicación.

Responsable:
Responsable S07 / Desarrollo

### Kubernetes

Se requieren manifiestos compatibles con la aplicación y preparados
para la estrategia GitOps.

Responsable:
Responsable S07 / Infraestructura

## Quality Gates S08

El pipeline deberá controlar:

- Lint
- Tests
- Cobertura >= 70%
- SAST con Semgrep
- Construcción de imagen
- Publicación de imagen
- Promoción mediante GitOps

## Bloqueos actuales

- La rama S08 todavía no contiene la aplicación.
- No existe actualmente una suite automatizada de tests con cobertura.
- No existe configuración CI/CD.
- La infraestructura GitOps todavía debe prepararse.
- La imagen Docker debe ser compatible con el runtime real de AulaViva.

## Criterio para continuar

La implementación completa del pipeline se realizará cuando estén
disponibles las dependencias necesarias, sin modificar ni reemplazar
las entregas anteriores de otros integrantes.
