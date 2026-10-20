# ADR 0002 — Tutor IA contextualizado mediante RAG

**Estado:** Propuesto
**Fecha:** 26-08-2026
**Autores:** Equipo AulaViva

## Contexto

AulaViva busca aumentar la retención de contenidos y el rendimiento escolar en un 25% durante el primer semestre de adopción de la plataforma en los colegios asociados.
Dentro del Impact Map se identificó al **estudiante** como uno de los actores principales y la necesidad de mejorar la resolución autónoma de dudas fuera del horario de clases.
Para responder a esta necesidad se definió como entregable una **interfaz de chat interactivo con un Tutor IA contextualizado por asignatura**.
Además, los estudiantes tendrán acceso a un repositorio de materiales de cada curso, incluyendo guías, lecturas y otros contenidos proporcionados por los docentes.
Por lo tanto, el Tutor IA debe ser capaz de utilizar estos materiales como fuente de información para responder consultas relacionadas con cada asignatura.

## Decisión

Se utilizará un enfoque RAG (Retrieval-Augmented Generation) para implementar el Tutor IA de AulaViva.
Cuando un estudiante realice una pregunta, el sistema deberá buscar información relevante dentro de los materiales correspondientes a su curso y utilizar el contenido recuperado como contexto para generar la respuesta.
El flujo general será:

Pregunta del estudiante → búsqueda en materiales del curso → recuperación de información relevante → generación de respuesta mediante IA → respuesta al estudiante.

El Tutor IA deberá limitar sus respuestas al contexto disponible en los materiales correspondientes al curso.
Si el sistema no encuentra información suficiente para responder una pregunta, deberá informar al estudiante que no dispone de información suficiente en lugar de generar una respuesta sin respaldo en los materiales.
Las tecnologías específicas para implementar el modelo de lenguaje, embeddings y almacenamiento de la información serán evaluadas en etapas posteriores del diseño de la arquitectura.

## Consecuencias

### Positivas

* Las respuestas del Tutor IA estarán contextualizadas según la asignatura y los materiales del curso.
* Los estudiantes podrán resolver dudas de manera autónoma fuera del horario de clases.
* Se reduce el riesgo de entregar respuestas que no estén respaldadas por los contenidos disponibles.
* Los docentes podrán influir en el contexto del Tutor IA mediante los materiales que incorporen a sus cursos.
* Los contenidos podrán mantenerse separados según el curso correspondiente.

### Negativas

* Los materiales de los cursos deberán ser procesados para que puedan ser utilizados por el sistema RAG.
* La calidad de las respuestas dependerá de la calidad y disponibilidad de los materiales proporcionados.
* Una recuperación incorrecta de información puede provocar respuestas poco relevantes.
* El proceso de recuperación y generación puede aumentar el tiempo necesario para entregar una respuesta.
* Será necesario manejar situaciones en las que el servicio de IA o la recuperación de información no estén disponibles.

## Alternativas consideradas

### 1. Utilizar únicamente un modelo de lenguaje

El estudiante realizaría una pregunta y el modelo generaría directamente una respuesta utilizando su conocimiento general.
Esta alternativa no fue seleccionada porque no garantiza que la respuesta esté basada en los contenidos específicos entregados por el docente para la asignatura.

### 2. Utilizar búsqueda tradicional de documentos

El sistema podría buscar palabras clave dentro de los materiales y mostrar documentos relacionados al estudiante.
Esta alternativa permite encontrar contenido, pero no entrega directamente una explicación o respuesta adaptada a la pregunta realizada.

### 3. Utilizar RAG sobre los materiales del curso

Esta alternativa permite recuperar información relevante de los materiales y entregarla como contexto al modelo de IA antes de generar una respuesta.
**Se selecciona esta alternativa** porque se ajusta al objetivo de disponer de un Tutor IA contextualizado por asignatura y permite aprovechar el repositorio de materiales definido para AulaViva.

## Criterios asociados

La decisión deberá permitir cumplir, al menos, los siguientes comportamientos definidos para el Tutor IA:

* Si existe información relevante en los materiales del curso, el Tutor IA debe generar una respuesta basada en ellos.
* Si no existe información suficiente, debe indicarlo al estudiante.
* Si el Tutor IA no está disponible, el sistema debe informar al estudiante que el servicio se encuentra temporalmente fuera de servicio.
* La información utilizada para responder debe corresponder al curso del estudiante.

## Resultado

AulaViva adoptará **RAG como enfoque para el Tutor IA**, utilizando los materiales de cada curso como fuente de contexto para generar respuestas a las consultas de los estudiantes.
La selección de tecnologías concretas para implementar el RAG se realizará posteriormente, cuando el equipo avance en la definición de la arquitectura del sistema.
