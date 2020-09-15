<?php

return [
    "urlSitioWebEmpresa" => env('URL_SITIO_WEB_EMPRESA'),
    "nombreComercialEmpresa" => env('NOMBRE_COMERCIAL_EMPRESA'),
    "minHorasClase" => env('MIN_HORAS_CLASE', '0.5'),
    "maxHorasClase" => env('MAX_HORAS_CLASE', '5'),
    "minHorario" => env('MIN_HORARIO_CLASES', '7'),
    "maxHorario" => env('MAX_HORARIO_CLASES', '22'),
    "numeroCorreosXEnvio" => env('NUMERO_MAX_CORREOS_X_ENVIO', 15),
    "maxTamanioArchivoSubida" => env('MAX_TAMANIO_BYTES_ARCHIVOS_SUBIDA', 5242880/*5 MB*/),
    "rangoMinutosBusquedaHorarioDocente" => env('RANGO_MINUTOS_BUSQUEDA_HORARIO_DOCENTE', 30),
    "apiKeyGoogleMaps" => env('GOOGLE_MAPS_API_KEY')
];
