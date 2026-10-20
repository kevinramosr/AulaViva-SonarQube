
# Invitacion


## Properties

Name | Type
------------ | -------------
`id` | number
`email` | string
`rut` | string
`username` | string
`rol` | string
`expiraEn` | Date
`urlActivacion` | string

## Example

```typescript
import type { Invitacion } from ''

// TODO: Update the object below with actual values
const example = {
  "id": 8,
  "email": alumno5@prueba.cl,
  "rut": 55555555-5,
  "username": alumno5,
  "rol": alumno,
  "expiraEn": 2026-10-02T00:19:15Z,
  "urlActivacion": /activar_cuenta.php?token=token-de-ejemplo,
} satisfies Invitacion

console.log(example)

// Convert the instance to a JSON string
const exampleJSON: string = JSON.stringify(example)
console.log(exampleJSON)

// Parse the JSON string back to an object
const exampleParsed = JSON.parse(exampleJSON) as Invitacion
console.log(exampleParsed)
```

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


