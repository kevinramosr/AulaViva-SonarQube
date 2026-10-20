Feature: Visualización de progreso por el apoderado
  Como apoderado
  Quiero visualizar el progreso y calificaciones de mi pupilo
  Para hacer seguimiento de su desempeño escolar

  Scenario: Consulta de informe de notas actualizado
    Given un apoderado vinculado a su pupilo autenticado en el portal
    When accede a la pestaña "Rendimiento Académico"
    Then visualiza la lista de asignaturas, el promedio actual y las últimas evaluaciones calificadas

  Scenario: Apoderado con más de un pupilo a cargo
    Given un apoderado que tiene dos hijos inscritos en el colegio
    When hace clic en el selector de estudiante en la cabecera
    Then la vista actualiza las métricas y asignaturas mostrando los datos del alumno seleccionado

  Scenario: Intento de visualización sin vinculación de estudiante confirmada
    Given un usuario registrado con rol apoderado cuya matrícula aún no está validada por secretaría
    When entra a la vista de notas
    Then el sistema muestra el aviso "No hay estudiantes asociados a tu cuenta. Contacte a Coordinación"
