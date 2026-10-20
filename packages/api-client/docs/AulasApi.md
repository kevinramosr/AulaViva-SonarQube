# AulasApi

All URIs are relative to *http://localhost/Aula%20viva/api/v1*

| Method | HTTP request | Description |
|------------- | ------------- | -------------|
| [**obtenerAlumnosAula**](AulasApi.md#obteneralumnosaula) | **GET** /aulas/{aulaId}/alumnos | Obtener los alumnos de un aula |
| [**obtenerAula**](AulasApi.md#obteneraula) | **GET** /aulas/{aulaId} | Obtener un aula |
| [**obtenerAulas**](AulasApi.md#obteneraulas) | **GET** /aulas | Obtener las aulas del profesor |



## obtenerAlumnosAula

> AlumnosResponse obtenerAlumnosAula(aulaId)

Obtener los alumnos de un aula

Devuelve los alumnos activos pertenecientes al aula indicada. 

### Example

```ts
import {
  Configuration,
  AulasApi,
} from '';
import type { ObtenerAlumnosAulaRequest } from '';

async function example() {
  console.log("🚀 Testing  SDK...");
  const config = new Configuration({ 
    // To configure API key authorization: sessionCookie
    apiKey: "YOUR API KEY",
  });
  const api = new AulasApi(config);

  const body = {
    // number | Identificador único del aula.
    aulaId: 1,
  } satisfies ObtenerAlumnosAulaRequest;

  try {
    const data = await api.obtenerAlumnosAula(body);
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
| **aulaId** | `number` | Identificador único del aula. | [Defaults to `undefined`] |

### Return type

[**AlumnosResponse**](AlumnosResponse.md)

### Authorization

[sessionCookie](../README.md#sessionCookie)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
| **200** | Lista de alumnos activos del aula. |  -  |
| **400** | La solicitud contiene datos inválidos. |  -  |
| **401** | No existe una sesión autenticada válida. |  -  |
| **403** | El usuario no tiene permiso para acceder al recurso. |  -  |
| **405** | El método HTTP utilizado no está permitido. |  -  |
| **500** | Ocurrió un error interno del servidor. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


## obtenerAula

> AulaResponse obtenerAula(aulaId)

Obtener un aula

Devuelve la información de un aula específica a la que pertenece el profesor autenticado. 

### Example

```ts
import {
  Configuration,
  AulasApi,
} from '';
import type { ObtenerAulaRequest } from '';

async function example() {
  console.log("🚀 Testing  SDK...");
  const config = new Configuration({ 
    // To configure API key authorization: sessionCookie
    apiKey: "YOUR API KEY",
  });
  const api = new AulasApi(config);

  const body = {
    // number | Identificador único del aula.
    aulaId: 1,
  } satisfies ObtenerAulaRequest;

  try {
    const data = await api.obtenerAula(body);
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
| **aulaId** | `number` | Identificador único del aula. | [Defaults to `undefined`] |

### Return type

[**AulaResponse**](AulaResponse.md)

### Authorization

[sessionCookie](../README.md#sessionCookie)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
| **200** | Información del aula solicitada. |  -  |
| **400** | La solicitud contiene datos inválidos. |  -  |
| **401** | No existe una sesión autenticada válida. |  -  |
| **403** | El usuario no tiene permiso para acceder al recurso. |  -  |
| **405** | El método HTTP utilizado no está permitido. |  -  |
| **500** | Ocurrió un error interno del servidor. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)


## obtenerAulas

> AulasResponse obtenerAulas()

Obtener las aulas del profesor

Devuelve las aulas activas asociadas al profesor autenticado. 

### Example

```ts
import {
  Configuration,
  AulasApi,
} from '';
import type { ObtenerAulasRequest } from '';

async function example() {
  console.log("🚀 Testing  SDK...");
  const config = new Configuration({ 
    // To configure API key authorization: sessionCookie
    apiKey: "YOUR API KEY",
  });
  const api = new AulasApi(config);

  try {
    const data = await api.obtenerAulas();
    console.log(data);
  } catch (error) {
    console.error(error);
  }
}

// Run the test
example().catch(console.error);
```

### Parameters

This endpoint does not need any parameter.

### Return type

[**AulasResponse**](AulasResponse.md)

### Authorization

[sessionCookie](../README.md#sessionCookie)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`, `application/problem+json`


### HTTP response details
| Status code | Description | Response headers |
|-------------|-------------|------------------|
| **200** | Lista de aulas activas asignadas al profesor. |  -  |
| **401** | No existe una sesión autenticada válida. |  -  |
| **403** | El usuario no tiene permiso para acceder al recurso. |  -  |
| **405** | El método HTTP utilizado no está permitido. |  -  |
| **500** | Ocurrió un error interno del servidor. |  -  |

[[Back to top]](#) [[Back to API list]](../README.md#api-endpoints) [[Back to Model list]](../README.md#models) [[Back to README]](../README.md)

