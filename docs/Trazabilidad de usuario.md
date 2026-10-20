# Matriz de Trazabilidad de Historias de Usuario

| ID | Historia de usuario | Actor involucrado | Seguimiento de la historia |
|---|---|---|---|
| HU-01 | **Acceso a materiales del curso**: Como estudiante quiero acceder a los materiales de mi curso para estudiar los contenidos correspondientes. | Estudiante / Docente | **Estado: En desarrollo.** Se debe validar el acceso autenticado al curso, visualización y descarga de archivos disponibles. Pendiente: integración del almacenamiento de documentos, visor de archivos y manejo de errores cuando un recurso no esté disponible. |
| HU-02 | **Creación de evaluación por el docente**: Como docente quiero crear una evaluación digital con pauta de corrección para medir el aprendizaje de mis estudiantes. | Docente / Estudiantes | **Estado: En desarrollo.** Se implementa el formulario de creación de evaluaciones, configuración de preguntas, puntajes y publicación para estudiantes. Pendiente: validación de puntajes, guardado de borradores y recuperación ante pérdida de conexión. |
| HU-03 | **Rendición de evaluación autocorregida**: Como estudiante quiero rendir evaluaciones autocorregidas para conocer inmediatamente mi resultado y retroalimentación. | Estudiante | **Estado: Planificada.** Se debe desarrollar el módulo de resolución de evaluaciones, corrección automática, cálculo de resultados y entrega de retroalimentación. Pendiente: control de tiempo, envío automático y validación de preguntas sin responder. |
| HU-04 | **Consulta al Tutor IA**: Como estudiante quiero consultar al Tutor IA basándome en los contenidos del curso para resolver dudas puntuales de estudio. | Estudiante / Tutor IA | **Estado: Investigación / Prototipo.** Se busca integrar un asistente inteligente conectado con los contenidos del curso. Pendiente: integración del modelo IA, respuestas contextualizadas y manejo de disponibilidad del servicio. |
| HU-05 | **Visualización de progreso por el apoderado**: Como apoderado quiero visualizar el progreso y calificaciones de mi pupilo para hacer seguimiento de su desempeño escolar. | Apoderado / Estudiante / Administración escolar | **Estado: Pendiente de desarrollo.** Se requiere implementar la asociación entre apoderado y estudiante, consulta de notas y métricas académicas. Pendiente: validación de permisos y seguridad de información académica. |

---

## Priorización MoSCoW

| Historia | Prioridad | Justificación |
|---|---|---|
| HU-01 - Acceso a materiales | Must Have | Funcionalidad principal para permitir el acceso al contenido académico. |
| HU-02 - Creación de evaluaciones | Must Have | Permite al docente gestionar actividades de aprendizaje. |
| HU-03 - Evaluaciones autocorregidas | Must Have | Permite medir resultados y entregar retroalimentación inmediata. |
| HU-05 - Progreso del apoderado | Should Have | Complementa el seguimiento académico, pero no bloquea el funcionamiento principal. |
| HU-04 - Tutor IA | Could Have | Funcionalidad innovadora que mejora la experiencia, pero no es esencial para el funcionamiento inicial. |
