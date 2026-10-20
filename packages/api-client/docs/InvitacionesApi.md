# InvitacionesApi

All URIs are relative to *http://localhost/Aula%20viva/api/v1*

| Method | HTTP request | Description |
|------------- | ------------- | -------------|
| [**crearInvitacion**](InvitacionesApi.md#crearinvitacion) | **POST** /invitaciones | Crear una invitación |



## crearInvitacion

> InvitacionReplayResponse crearInvitacion(idempotencyKey, invitacionRequest)

Crear una invitación

Crea una invitación de manera idempotente. Si se repite una solicitud con la misma Idempotency-Key, la API devuelve la invitación creada anteriormente en vez de crear una nueva. 

### Example

```ts
import {
  Configuration,
  InvitacionesApi,
} from '';
import type { CrearInvitacionRequest } from '';

async function example() {
  console.log("🚀 Testing  SDK...");
  const config = new Configuration({ 
    // To configure API key authorization: sessionCookie
    apiKey: "YOUR API KEY",
  });
  const api = new InvitacionesApi(config);

  const body = {
    // string | Clave única proporcionada por el cliente para evitar que un reintento cree una segunda invitación. 
    idempotencyKey: invitacion-alumno5-001,
    // InvitacionRequest
    invitacionRequest: {"email":"alumno5@prueba.cl","rut":"55555555-5","username":"alumno5","rol":"alumno"},
  } satisfies CrearInvitacionRequest;

  try {
    const data = await api.crearInvitacion(body);
    console.log(data);
  } catch (error) {
    console.error(error);
  }
}

// Run the test
example().catch(console.error);
```

### Parameters


| Name | Type | Description  | Notes |
|------------- | ------------- | ------------- | -------------|
| **idempotencyKey** | `string` | Clave única proporcionada por el cliente para evitar que un reintento cree una segunda invitación.  | [Defaults to `undefined`] |
| **invitacionRequest** | [InvitacionRequest](InvitacionRequest.md) |  | |

### Return type

[**InvitacionReplayResponse**](InvitacionReplayResponse.md)

### Authorization

[sessionCookie](../README.md#sessionCookie)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`, `application/problem+json`


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
| **201** | Invitación creada correctamente. |  -  |
| **200** | Solicitud idempotente repetida. Se devuelve la invitación correspondiente a la operación original.  |  * Idempotent-Replayed - Indica que se reutilizó el resultado anterior. <br>  |
| **400** | La solicitud contiene datos inválidos. |  -  |
| **401** | No existe una sesión autenticada válida. |  -  |
| **403** | El usuario no tiene permiso para acceder al recurso. |  -  |
| **409** | La operación entra en conflicto con un recurso existente. |  -  |
| **405** | El método HTTP utilizado no está permitido. |  -  |
| **500** | Ocurrió un error interno del servidor. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)

