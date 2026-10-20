Feature: Creación de evaluación por el docente
  Como docente
  Quiero crear una evaluación digital con pauta de corrección
  Para medir el aprendizaje de mis estudiantes

  Scenario: Creación exitosa de cuestionario
    Given un docente autenticado en el panel de su asignatura
    When completa el formulario de evaluación con 5 preguntas de selección múltiple y presiona "Publicar"
    Then la evaluación queda visible y programada para los estudiantes del curso

  Scenario: Intento de publicación con puntaje total inválido
    Given un docente configurando una evaluación
    When asigna 0 puntos a todas las preguntas e intenta guardar
    Then el sistema bloquea la publicación indicando "Debe asignar un puntaje mayor a 0"

  Scenario: Pérdida de conexión al guardar borrador
    Given un docente redactando una evaluación
    When se corta la conexión a internet e intenta guardar los cambios
    Then el sistema notifica "Sin conexión. Los cambios se guardarán localmente hasta reconectar"
