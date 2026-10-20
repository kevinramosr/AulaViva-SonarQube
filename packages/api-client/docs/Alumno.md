
# Alumno


## Properties

Name | Type
------------ | -------------
`id` | number
`rut` | string
`username` | string
`nombre` | string
`apellido` | string

## Example

```typescript
import type { Alumno } from ''

// TODO: Update the object below with actual values
const example = {
  "id": 1,
  "rut": 11111111-1,
  "username": alumno1,
  "nombre": Alumno,
  "apellido": Prueba,
} satisfies Alumno

console.log(example)

// Convert the instance to a JSON string
const exampleJSON: string = JSON.stringify(example)
console.log(exampleJSON)

// Parse the JSON string back to an object
const exampleParsed = JSON.parse(exampleJSON) as Alumno
console.log(exampleParsed)
```

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


