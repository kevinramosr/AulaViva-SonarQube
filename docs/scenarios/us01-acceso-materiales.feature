Feature: Acceso a materiales del curso
  Como estudiante
  Quiero acceder a los materiales de mi curso-
  Para estudiar los contenidos correspondientes

  Scenario: Descarga o visualización exitosa de material
    Given un estudiante autenticado dentro de su curso de "Matemáticas"
    When hace clic en la guía "Álgebra Básica.pdf"
    Then el sistema abre el archivo en el visor integrado para su estudio

  Scenario: Visualización de curso sin materiales cargados
    Given un estudiante autenticado dentro de su curso de "Historia"
    When accede a la sección de contenidos y el docente no ha subido archivos
    Then el sistema muestra el mensaje "No hay materiales disponibles para este curso"

  Scenario: Error al cargar archivo inexistente o dañado
    Given un estudiante intenta abrir un recurso cuyo enlace está roto en el servidor
    When presiona el botón "Descargar"
    Then el sistema muestra un mensaje de alerta "El archivo no se encuentra disponible. Contacte al docente"
