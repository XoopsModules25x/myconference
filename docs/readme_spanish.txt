Features del Modulo para Eventos:

Tablas:
Formularios:
	1) Evento (fecha/dias)
	- CVs
	- Tracks
	- Charlas
		- Tipos (Conferencia / Keynote / definibles)
		- Upload de presentaciones
	- Evaluacion de la charla?
	- Envio de charlas por los visitantes del sitio?
	- Auspiciantes/Organizador?
		- Preguntar si se los ordena por orden alfabetico
		- Categorias de sponsors (tildar si se pone en el footer)
Bloque:
	- Tracks
	- Programa

Features:
	- Registracion
		-impresion de formulario de ingreso ``rapido''
		-registracion externa
		-formulario armable por el usuario?
		-estadisticas de registracion
	- Armado automatico de grilla (programa)
	- Deteccion de inconcistencias, superposiciones
	- Seccion Prensa?
	- comentarios de gente sobre las charlas?
	- Notificacion de cambio?
	- PDF con el programa (+ sponsors y organizador)

## db ##
    - conference_congress
    - conference_speakers
    - conference_speech_type
    - conference_speech
    - conference_tracks
    - conference_sponsors
    - conference_speech_eval

+ conference_congress
	- conid
	- sdate
	- edate
	- title
	- subtitle
	- subsubtitle
	- logo

+ conference_speakers
	- speakerid
	- name
	- email
	- descrip
	- photo
	- url



