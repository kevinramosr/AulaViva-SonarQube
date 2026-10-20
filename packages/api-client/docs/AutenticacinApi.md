# AutenticacinApi

All URIs are relative to *http://localhost/Aula%20viva/api/v1*

| Method | HTTP request | Description |
|------------- | ------------- | -------------|
| [**iniciarSesion**](AutenticacinApi.md#iniciarsesion) | **POST** /auth/login | Iniciar sesión |



## iniciarSesion

> LoginResponse iniciarSesion(loginRequest)

Iniciar sesión

Autentica a un usuario mediante su identificador y contraseña e inicia una sesión mediante una cookie PHPSESSID. 

### Example

```ts
import {
  Configuration,
  AutenticacinApi,
} from '';
import type { IniciarSesionRequest } from '';

async function example() {
  console.log("🚀 Testing  SDK...");
  const api = new AutenticacinApi();

  const body = {
    // LoginRequest
    loginRequest: {"identificador":"profesor1","password":"password-de-prueba"},
  } satisfies IniciarSesionRequest;

  try {
    const data = await api.iniciarSesion(body);
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
| **loginRequest** | [LoginRequest](LoginRequest.md) |  | |

### Return type

[**LoginResponse**](LoginResponse.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`, `application/problem+json`


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
| **200** | Sesión iniciada correctamente. |  -  |
| **400** | La solicitud contiene datos inválidos. |  -  |
| **401** | No existe una sesión autenticada válida. |  -  |
| **405** | El método HTTP utilizado no está permitido. |  -  |
| **500** | Ocurrió un error interno del servidor. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)

