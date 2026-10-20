
# UsuarioSesion


## Properties

Name | Type
------------ | -------------
`id` | number
`username` | string
`nombre` | string
`apellido` | string
`rol` | string

## Example

```typescript
import type { UsuarioSesion } from ''

// TODO: Update the object below with actual values
const example = {
  "id": 2,
  "username": profesor1,
  "nombre": Profesor,
  "apellido": Prueba,
  "rol": profesor,
} satisfies UsuarioSesion

console.log(example)

// Convert the instance to a JSON string
const exampleJSON: string = JSON.stringify(example)
console.log(exampleJSON)

// Parse the JSON string back to an object
const exampleParsed = JSON.parse(exampleJSON) as UsuarioSesion
console.log(exampleParsed)
```

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


