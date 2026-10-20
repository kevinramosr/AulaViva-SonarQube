
# Problem


## Properties

Name | Type
------------ | -------------
`type` | string
`title` | string
`status` | number
`detail` | string
`instance` | string

## Example

```typescript
import type { Problem } from ''

// TODO: Update the object below with actual values
const example = {
  "type": /errors/no-autenticado,
  "title": No autenticado,
  "status": 401,
  "detail": Debes iniciar sesión para utilizar este recurso.,
  "instance": /Aula%20viva/api/v1/aulas/,
} satisfies Problem

console.log(example)

// Convert the instance to a JSON string
const exampleJSON: string = JSON.stringify(example)
console.log(exampleJSON)

// Parse the JSON string back to an object
const exampleParsed = JSON.parse(exampleJSON) as Problem
console.log(exampleParsed)
```

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


