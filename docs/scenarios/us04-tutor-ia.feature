Feature: Consulta al Tutor IA
  Como estudiante
  Quiero consultar al Tutor IA basándome en los contenidos del curso
  Para resolver dudas puntuales de estudio

  Scenario: Consulta y respuesta contextual exitosa
    Given un estudiante autenticado dentro del módulo de "Biología Celular"
    When formula la pregunta "¿Cuál es la función de la mitocondria?" en el chat
    Then el Tutor IA responde explicando la síntesis de ATP basado en el texto de la unidad

  Scenario: Envío de mensaje en blanco o caracteres repetitivos
    Given un estudiante con la ventana del chat abierta
    When presiona "Enviar" sin escribir texto o solo con espacios
    Then el botón permanece deshabilitado y se resalta el campo de texto

  Scenario: Caída o indisponibilidad del servicio de IA
    Given un estudiante enviando una duda conceptual válida
    When el servicio de inferencia no responde tras el tiempo de espera límite
    Then el sistema muestra el mensaje "El Tutor IA no está disponible en este momento. Inténtalo más tarde"
