# S08 - CI Quality Gates

## Objetivo

Definir los controles mínimos que deberá ejecutar el pipeline de
Integración Continua de AulaViva antes de permitir la construcción,
publicación y promoción de una versión.

## Quality Gates

El pipeline deberá ejecutar los siguientes controles:

1. Lint
2. Tests automatizados
3. Cobertura mínima de 70 %
4. SAST mediante Semgrep

Si uno de los controles obligatorios falla, el pipeline deberá detener
el proceso y no deberá continuar automáticamente hacia la publicación
o promoción.

## Lint

El pipeline utilizará el comando oficial definido por Desarrollo para
validar el código de aplicación.

Actualmente no existe una configuración de lint para el código de
aplicación en la rama S08.

Estado: pendiente de definición por Desarrollo.

## Tests

El pipeline deberá ejecutar una suite automatizada con aserciones
reales.

Los tests deberán devolver un código de salida distinto de cero cuando
existan fallos.

Actualmente no existe una suite automatizada disponible directamente
en la rama S08.

Estado: pendiente de disponibilidad de Desarrollo/QA.

## Cobertura

El pipeline deberá verificar una cobertura mínima de:

70 %

La cobertura deberá calcularse sobre tests reales y no mediante valores
simulados.

Actualmente no existe configuración de cobertura disponible en la
rama S08.

Estado: bloqueo pendiente de configuración por Desarrollo/QA.

## SAST - Semgrep

El pipeline deberá ejecutar Semgrep como control de seguridad estático.

La política de bloqueo deberá definirse explícitamente antes de
considerar el quality gate como completado.

Actualmente Semgrep no está configurado en la rama S08.

Estado: pendiente de implementación DevSecOps.

## Dependencias

Para que los quality gates puedan ejecutarse se requiere:

- Código de aplicación disponible.
- Dependencias reproducibles.
- Comando oficial de lint.
- Suite automatizada de tests.
- Configuración de cobertura.
- Entorno reproducible para las pruebas.

## Política de bloqueo

La etapa de construcción y publicación no deberá ejecutarse cuando los
quality gates obligatorios fallen.

La promoción hacia GitOps tampoco deberá ejecutarse si la calidad de la
versión no ha sido validada.

## Responsabilidades

### DevSecOps

- Implementar los controles en CI.
- Configurar Semgrep.
- Definir las condiciones de bloqueo.
- Mantener el workflow.
- Verificar los resultados del pipeline.

### Desarrollo

- Proporcionar código ejecutable.
- Definir dependencias.
- Proporcionar comandos de lint.
- Mantener la aplicación compatible con el proceso de construcción.

### QA / Desarrollo

- Proporcionar tests automatizados.
- Definir y mantener la cobertura.
- Asegurar que los tests tengan aserciones reales.

## Estado actual

Los quality gates están definidos conceptualmente, pero el pipeline
completo no puede validarse todavía porque las dependencias técnicas
necesarias no están disponibles en la rama S08.

No se utilizarán tests ficticios, cobertura simulada ni comandos
inventados para declarar el pipeline como exitoso.
