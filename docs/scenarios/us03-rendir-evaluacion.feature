Feature: Rendición de evaluación autocorregida
  Como estudiante
  Quiero rendir evaluaciones autocorregidas
  Para conocer de inmediato mi resultado y retroalimentación

  Scenario: Envío y retroalimentación inmediata
    Given un estudiante que finalizó de responder las 10 preguntas de su test
    When presiona el botón "Finalizar y Enviar"
    Then el sistema muestra su nota obtenida junto con la corrección de respuestas erróneas

  Scenario: Envío de evaluación con preguntas sin responder
    Given un estudiante rindiendo un examen con 2 preguntas pendientes
    When presiona el botón "Finalizar y Enviar"
    Then el sistema despliega un diálogo de advertencia "Tienes preguntas sin contestar. ¿Deseas enviar de todas formas?"

  Scenario: Cierre por expiración de tiempo límite
    Given un estudiante rindiendo una prueba con cronómetro activo
    When el tiempo límite llega a 00:00
    Then el sistema bloquea las entradas y envía automáticamente las respuestas registradas hasta ese momento
