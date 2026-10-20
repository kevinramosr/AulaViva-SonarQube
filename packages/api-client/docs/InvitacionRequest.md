
# InvitacionRequest


## Properties

Name | Type
------------ | -------------
`email` | string
`rut` | string
`username` | string
`rol` | string

## Example

```typescript
import type { InvitacionRequest } from ''

// TODO: Update the object below with actual values
const example = {
  "email": alumno5@prueba.cl,
  "rut": 55555555-5,
  "username": alumno5,
  "rol": alumno,
} satisfies InvitacionRequest

console.log(example)

// Convert the instance to a JSON string
const exampleJSON: string = JSON.stringify(example)
console.log(exampleJSON)

// Parse the JSON string back to an object
const exampleParsed = JSON.parse(exampleJSON) as InvitacionRequest
console.log(exampleParsed)
```

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


