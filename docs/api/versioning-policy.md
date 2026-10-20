# Política de versionado de Aula Viva API

## Versión actual

La versión actual de la API de Aula Viva es **v1**.

Los endpoints se publican utilizando versionado explícito en la URL:

`/api/v1/`

Ejemplo:

`GET /api/v1/aulas`

## Cambios compatibles

Los cambios que no rompen la compatibilidad con los clientes existentes
pueden incorporarse dentro de la misma versión de la API.

Ejemplos:

- Agregar nuevos endpoints.
- Agregar campos opcionales a una respuesta.
- Agregar nuevos parámetros opcionales.
- Corregir errores sin modificar el contrato existente.

## Cambios incompatibles

Los cambios que puedan romper clientes existentes requieren una nueva
versión mayor de la API.

Ejemplos:

- Eliminar o renombrar endpoints.
- Eliminar campos existentes.
- Cambiar el tipo de un campo.
- Convertir un parámetro opcional en obligatorio.
- Modificar de forma incompatible la estructura de una respuesta.

Por ejemplo, un cambio incompatible con `v1` deberá publicarse como:

`/api/v2/`

## Deprecación

Cuando una versión o endpoint vaya a dejar de utilizarse, primero será
marcado como obsoleto antes de su eliminación.

La documentación OpenAPI indicará los endpoints obsoletos mediante:

`deprecated: true`

Cuando corresponda, la API podrá utilizar encabezados HTTP como
`Deprecation` y `Sunset` para comunicar la retirada futura del recurso.

## Compatibilidad

Las versiones anteriores se mantendrán disponibles durante el período
de transición establecido para permitir que los clientes migren a la
versión más reciente.