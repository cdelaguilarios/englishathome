# Sistema de administración de clases

La presente solución informática ha sido desarrollada para la creación de un sistema de gestión de datos para clases y procesos asociados.

## Modelo de Base de Datos

La siguiente tabla muestra detalles de los principales tablas que se utilizan para la persistencia de datos en MySQL.

| Tabla 	| Descripción                                                                                                                                                                  	| Principales campos                                                                                                                         	|
|-----------	|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------	|--------------------------------------------------------------------------------------------------------------------------------------------	|
| `Entidad`   	| Contiene datos de todas las entidades asociadas a las clases como por ejemplo profesores y alumnos.                                                                                               	| Nombre, Apellido, Correo electrónico, Estado                                                                                                     	|                                                                                   	|
| `Horario`    	| Contiene datos de los horarios de alumnos y los asociados a la disponibilidad de profesores.                                                          	| Dia de la semana, Hora de inicio y fin   |
| `Clase`   	| Contiene datos de todas las clases registradas.                                                                           	| ID alumno, ID profesor, Fecha inicio y  fin, comentarios, estado 	|
| `Pago`   	| Contiene datos de todos los pagos realizados por alumnos o para profesores. 	| Motivo, Monto, Cuenta, Fecha, Imágenes  de comprobantes                   	|

## Detalles técnicos

* El sistema ha sido desarrollado utilizando el framework `Laravel 5.2.45`.

## Dependencias externas

* **`Servicio gestor de correos`**, para el envío de correros electrónicos.

* **`Google maps`**, permite mostrar y gestionar datos de ubicaciones/direcciones de entidades (profesores y alumnos).

# Empezando

Las principales operaciones de este sistema son:

1. Gestión de datos de entidades asociadas a clases: profesores y alumnos.
2. Gestión de datos de horarios.
3. Gestión de datos de clases.
4. Gestión de datos de pagos asociados.

## Pre-requisitos

* `Apache 2.4.18 x86`
* `PHP 5.6.19`
* `MySQL 5.7.11`

## Variables de entorno

* **`APP_URL`**, URL del sistema.
* 
* **`URL_SITIO_WEB_EMPRESA`**, URL del sitio web de la empresa. Será mostrado en los correos de cotización.
* **`NOMBRE_COMERCIAL_EMPRESA`**, Nombre comercial de la empresa.
* **`MIN_HORAS_CLASE`**, Duración mínima de horas que tendrán las clases.
* **`MAX_HORAS_CLASE`**, Duración máxima de horas que tendrán las clases.
* **`MIN_HORARIO_CLASES`**, Hora inicial del día para el horario de clases (formato 24 horas).
* **`MAX_HORARIO_CLASES`**, Hora final del día para el horario de clases (formato 24 horas).
* **`NUMERO_MAX_CORREOS_X_ENVIO`**, Número máximo de correos que se enviaran por ejecución del `"CRON JOB ENVÍO DE CORREOS"` (Ver sección `Despliegue punto 3`).
* **`MAX_TAMANIO_BYTES_ARCHIVOS_SUBIDA`**, Máximo tamaño en bytes que podrán tener los archivos que se registren en el sistema.
* **`RANGO_MINUTOS_BUSQUEDA_HORARIO_DOCENTE`**, Para la búsqueda de docentes disponibles para una clase se debe considerar `rango de tiempo en minutos` para permitir al profesor pasar de una clase a otra.
* **`GOOGLE_MAPS_API_KEY`**, API KEY para conectarse al servicio GOOGLE MAPS.

* **`DB_CONNECTION`**, tipo de conexión a la base de datos. Actualmente se utiliza `mysql`.
* **`DB_HOST`**, URL o IP de acceso a la base de datos.
* **`DB_PORT`**, Puerto de acceso a la base de datos.
* **`DB_DATABASE`**, Nombre de la base de datos.
* **`DB_USERNAME`**, Usuario para acceder a la base de datos.
* **`DB_PASSWORD`**, Clave para acceder a la base de datos.

* **`MAIL_DRIVER`**, Gestor/controlador para el envío de correos. El framework soporta `"smtp", "mail", "sendmail", "mailgun", "mandrill", "ses", "sparkpost" y "log"`
* **`MAIL_HOST`**, URL o IP de acceso al servicio de correos.
* **`MAIL_PORT`**, Puerto de acceso al servicio de correos.
* **`MAIL_ENCRYPTION`**, Protocolo de encriptación utilizado por acceder al servicio de correos. Por ejemplo `"tls" o "ssl"`
* **`MAIL_USERNAME`**, Usuario para acceder al servicio de correos.
* **`MAIL_PASSWORD`**, Clave para acceder al servicio de correos.
* **`MAIL_FROM_ADDRESS`**, Correo electrónico que será utilizado como remitente.
* **`MAIL_FROM_NAME`**, Nombre identificador del remitente, puede ser igual al `NOMBRE_COMERCIAL_EMPRESA`.

Como referencia puede utilizar el archivo `.env.example`

## Despliegue

1. Ejecución de los siguientes scripts de BD que se encuentran en la carpeta `"adicionales/bd"`:

    * **`1_tablas.sql`**, para la creación de tablas.

    * **`2_registros_iniciales.sql`**, para el registro de datos iniciales.

2. Crear un archivo `.env` que contenga valores para las variables de entorno mencionadas en la sección anterior.

3. Abrir una ventana de línea de comandos y ejecutar `"composer install"` para la instalación de dependencias.

4. Establecer un `CRON JOB` para el envío de correos electrónicos el cual debe ejecutar cada cierto tiempo la URL `"[URL_BASE]/cron/enviarCorreos"`.

5. Copiar/reemplazar las siguientes imágenes asociadas a la empresa en la carpeta `"public/assets/eah/img"`:

    * **`favicon.ico`**, imagen icono del sistema.

    * **`logo.png`**, logo de la empresa.

6. Una vez levantado el sistema podrá ingresar utilizando las siguientes credenciales:

    * **`administrador@sistema.com`**

    * **`123456`**, se recomienda cambiar la contraseña inmediatamente.

7. Debe establecer los valores solicitados en el formulario de configuración que puede ser accedido ingresando a la URL `"[URL_BASE]/configuracion"`.
