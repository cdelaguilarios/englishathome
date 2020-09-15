SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno`
--

CREATE TABLE `alumno` (
  `idEntidad` int(11) NOT NULL,
  `inglesLugarEstudio` varchar(255) DEFAULT NULL,
  `inglesPracticaComo` varchar(255) DEFAULT NULL,
  `inglesObjetivo` varchar(255) DEFAULT NULL,
  `conComputadora` tinyint(1) NOT NULL DEFAULT '0',
  `conInternet` tinyint(1) NOT NULL DEFAULT '0',
  `conPlumonPizarra` tinyint(1) NOT NULL DEFAULT '0',
  `conAmbienteClase` tinyint(1) NOT NULL DEFAULT '0',
  `numeroHorasClase` int(2) NOT NULL,
  `comentarioAdicional` varchar(8000) DEFAULT NULL,
  `fechaInicioClase` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `costoXHoraClase` decimal(14,4) NOT NULL,
  `idProfesorActual` int(11) DEFAULT NULL,
  `codigoVerificacionClases` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnobolsahoras`
--

CREATE TABLE `alumnobolsahoras` (
  `idAlumno` int(11) NOT NULL,
  `idPago` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clase`
--

CREATE TABLE `clase` (
  `id` int(11) NOT NULL,
  `idAlumno` int(11) NOT NULL,
  `idProfesor` int(11) DEFAULT NULL,
  `numeroPeriodo` int(11) NOT NULL,
  `fechaInicio` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fechaFin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `fechaConfirmacion` datetime DEFAULT NULL,
  `duracion` int(11) NOT NULL,
  `comentarioAlumno` text NOT NULL,
  `comentarioProfesor` text NOT NULL,
  `comentarioParaAlumno` text NOT NULL,
  `comentarioParaProfesor` text NOT NULL,
  `tipoCancelacion` varchar(100) DEFAULT NULL,
  `fechaCancelacion` datetime DEFAULT NULL,
  `idClaseCancelada` int(11) DEFAULT NULL,
  `estado` varchar(100) NOT NULL,
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `fechaRegistro` timestamp NULL DEFAULT NULL,
  `fechaUltimaActualizacion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `correo`
--

CREATE TABLE `correo` (
  `id` int(11) NOT NULL,
  `asunto` varchar(100) DEFAULT NULL,
  `correosAdicionales` varchar(255) DEFAULT NULL,
  `envioEnProceso` tinyint(1) NOT NULL DEFAULT '0',
  `enviado` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `curso`
--

CREATE TABLE `curso` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `imagen` varchar(100) DEFAULT NULL,
  `descripcion` text NOT NULL,
  `modulos` text NOT NULL,
  `metodologia` text NOT NULL,
  `incluye` text NOT NULL,
  `inversion` text NOT NULL,
  `incluirInversionCuotas` tinyint(1) NOT NULL DEFAULT '1',
  `inversionCuotas` text NOT NULL,
  `notasAdicionales` text NOT NULL,
  `adjuntos` varchar(4000) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `fechaRegistro` timestamp NULL DEFAULT NULL,
  `fechaUltimaActualizacion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamento`
--

CREATE TABLE `departamento` (
  `codigo` char(2) NOT NULL,
  `departamento` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `distrito`
--

CREATE TABLE `distrito` (
  `codigo` char(6) NOT NULL,
  `codigoProvincia` char(4) NOT NULL,
  `distrito` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entidad`
--

CREATE TABLE `entidad` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellido` varchar(255) NOT NULL,
  `fechaNacimiento` datetime DEFAULT NULL,
  `sexo` char(1) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `idTipoDocumento` int(11) DEFAULT NULL,
  `numeroDocumento` char(20) DEFAULT NULL,
  `correoElectronico` varchar(255) NOT NULL,
  `imagenPerfil` varchar(100) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `numeroDepartamento` varchar(255) DEFAULT NULL,
  `referenciaDireccion` varchar(255) DEFAULT NULL,
  `codigoUbigeo` char(6) DEFAULT NULL,
  `geoLatitud` float DEFAULT NULL,
  `geoLongitud` float DEFAULT NULL,
  `comentarioAdministrador` text,
  `tipo` varchar(100) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `fechaRegistro` timestamp NULL DEFAULT NULL,
  `fechaUltimaActualizacion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entidadcorreo`
--

CREATE TABLE `entidadcorreo` (
  `idEntidad` int(11) NOT NULL,
  `idCorreo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entidadcuentabancaria`
--

CREATE TABLE `entidadcuentabancaria` (
  `idEntidad` int(11) NOT NULL,
  `banco` varchar(255) NOT NULL,
  `numeroCuenta` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entidadcurso`
--

CREATE TABLE `entidadcurso` (
  `idEntidad` int(11) NOT NULL,
  `idCurso` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entidadnivelingles`
--

CREATE TABLE `entidadnivelingles` (
  `idEntidad` int(11) NOT NULL,
  `idNivelIngles` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entidadnotificacion`
--

CREATE TABLE `entidadnotificacion` (
  `idEntidad` int(11) NOT NULL,
  `idNotificacion` int(11) NOT NULL,
  `esObservador` tinyint(1) NOT NULL DEFAULT '0',
  `fechaRevision` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horario`
--

CREATE TABLE `horario` (
  `id` int(11) NOT NULL,
  `idEntidad` int(11) NOT NULL,
  `numeroDiaSemana` int(1) NOT NULL,
  `horaInicio` time NOT NULL,
  `horaFin` time NOT NULL,
  `fechaRegistro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fechaUltimaActualizacion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `interesado`
--

CREATE TABLE `interesado` (
  `idEntidad` int(11) NOT NULL,
  `consulta` varchar(255) DEFAULT NULL,
  `cursoInteres` varchar(255) NOT NULL,
  `costoXHoraClase` decimal(14,4) DEFAULT NULL,
  `comentarioAdicional` varchar(8000) DEFAULT NULL,
  `origen` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nivelingles`
--

CREATE TABLE `nivelingles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `fechaRegistro` timestamp NULL DEFAULT NULL,
  `fechaUltimaActualizacion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion`
--

CREATE TABLE `notificacion` (
  `id` int(11) NOT NULL,
  `idAlumno` int(11) DEFAULT NULL,
  `tipo` varchar(100) NOT NULL,
  `numeroClase` int(11) DEFAULT NULL,
  `idClase` int(11) DEFAULT NULL,
  `idPago` int(11) DEFAULT NULL,
  `idCorreo` int(11) DEFAULT NULL,
  `enviarCorreo` tinyint(1) NOT NULL DEFAULT '0',
  `enviarCorreoEntidades` tinyint(1) NOT NULL DEFAULT '0',
  `mostrarEnPerfil` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago`
--

CREATE TABLE `pago` (
  `id` int(11) NOT NULL,
  `motivo` varchar(100) NOT NULL,
  `cuenta` varchar(100) DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `descripcion` varchar(255) DEFAULT NULL,
  `imagenesComprobante` varchar(4000) DEFAULT NULL,
  `monto` decimal(14,4) NOT NULL,
  `saldoFavor` decimal(14,4) DEFAULT NULL,
  `saldoFavorUtilizado` tinyint(1) NOT NULL DEFAULT '0',
  `periodoClases` int(11) DEFAULT NULL,
  `costoXHoraClase` decimal(14,6) DEFAULT NULL,
  `pagoXHoraProfesor` decimal(14,4) DEFAULT NULL,
  `estado` varchar(100) NOT NULL,
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `fechaRegistro` timestamp NULL DEFAULT NULL,
  `fechaUltimaActualizacion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagoalumno`
--

CREATE TABLE `pagoalumno` (
  `idPago` int(11) NOT NULL,
  `idAlumno` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagoclase`
--

CREATE TABLE `pagoclase` (
  `idPago` int(11) NOT NULL,
  `idClase` int(11) NOT NULL,
  `duracionCubierta` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagoprofesor`
--

CREATE TABLE `pagoprofesor` (
  `idPago` int(11) NOT NULL,
  `idProfesor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `postulante`
--

CREATE TABLE `postulante` (
  `idEntidad` int(11) NOT NULL,
  `ultimosTrabajos` varchar(1000) DEFAULT NULL,
  `experienciaOtrosIdiomas` varchar(1000) DEFAULT NULL,
  `descripcionPropia` varchar(1000) DEFAULT NULL,
  `ensayo` varchar(1000) DEFAULT NULL,
  `cv` varchar(100) DEFAULT NULL,
  `certificadoInternacional` varchar(100) DEFAULT NULL,
  `imagenDocumentoIdentidad` varchar(100) DEFAULT NULL,
  `audio` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesor`
--

CREATE TABLE `profesor` (
  `idEntidad` int(11) NOT NULL,
  `ultimosTrabajos` varchar(1000) DEFAULT NULL,
  `experienciaOtrosIdiomas` varchar(1000) DEFAULT NULL,
  `descripcionPropia` varchar(1000) DEFAULT NULL,
  `ensayo` varchar(1000) DEFAULT NULL,
  `cv` varchar(100) DEFAULT NULL,
  `certificadoInternacional` varchar(100) DEFAULT NULL,
  `imagenDocumentoIdentidad` varchar(100) DEFAULT NULL,
  `audio` varchar(100) DEFAULT NULL,
  `comentarioPerfil` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `provincia`
--

CREATE TABLE `provincia` (
  `codigo` char(4) NOT NULL,
  `codigoDepartamento` char(2) NOT NULL,
  `provincia` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `relacionentidad`
--

CREATE TABLE `relacionentidad` (
  `idEntidadA` int(11) NOT NULL,
  `idEntidadB` int(11) NOT NULL,
  `tipo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `restablecimientocontrasenia`
--

CREATE TABLE `restablecimientocontrasenia` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarea`
--

CREATE TABLE `tarea` (
  `id` int(11) NOT NULL,
  `idUsuarioAsignado` int(11) NOT NULL,
  `estado` varchar(100) NOT NULL,
  `fechaRevision` datetime DEFAULT NULL,
  `fechaFinalizacion` datetime DEFAULT NULL,
  `fechaRealizacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareanotificacion`
--

CREATE TABLE `tareanotificacion` (
  `id` int(11) NOT NULL,
  `idUsuarioCreador` int(11) DEFAULT NULL,
  `titulo` varchar(100) DEFAULT NULL,
  `mensaje` varchar(4000) NOT NULL,
  `adjuntos` varchar(4000) DEFAULT NULL,
  `fechaProgramada` datetime DEFAULT NULL,
  `fechaNotificacion` datetime DEFAULT NULL,
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `fechaRegistro` timestamp NULL DEFAULT NULL,
  `fechaUltimaActualizacion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipodocumento`
--

CREATE TABLE `tipodocumento` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `eliminado` tinyint(1) NOT NULL DEFAULT '0',
  `fechaRegistro` timestamp NULL DEFAULT NULL,
  `fechaUltimaActualizacion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `idEntidad` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `rol` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `variablesistema`
--

CREATE TABLE `variablesistema` (
  `id` int(11) NOT NULL,
  `llave` varchar(50) NOT NULL,
  `valor` blob NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `recomendacionesAdicionales` varchar(100) DEFAULT NULL,
  `tipo` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumno`
--
ALTER TABLE `alumno`
  ADD PRIMARY KEY (`idEntidad`),
  ADD KEY `FK_entidad_alumnoProfesorActual` (`idProfesorActual`);

--
-- Indices de la tabla `alumnobolsahoras`
--
ALTER TABLE `alumnobolsahoras`
  ADD PRIMARY KEY (`idAlumno`,`idPago`),
  ADD KEY `FK_alumno_alumnoBolsaHoras` (`idAlumno`),
  ADD KEY `FK_pago_alumnoBolsaHoras` (`idPago`);

--
-- Indices de la tabla `clase`
--
ALTER TABLE `clase`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_FK_profesor_clase` (`idProfesor`),
  ADD KEY `IX_FK_alumno_clase` (`idAlumno`),
  ADD KEY `IX_FK_claseCancelada_clase` (`idClaseCancelada`);

--
-- Indices de la tabla `correo`
--
ALTER TABLE `correo`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `curso`
--
ALTER TABLE `curso`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `departamento`
--
ALTER TABLE `departamento`
  ADD PRIMARY KEY (`codigo`);

--
-- Indices de la tabla `distrito`
--
ALTER TABLE `distrito`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `IX_FK_provincia_distrito` (`codigoProvincia`);

--
-- Indices de la tabla `entidad`
--
ALTER TABLE `entidad`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_FK_tipoDocumento_entidad` (`idTipoDocumento`);

--
-- Indices de la tabla `entidadcorreo`
--
ALTER TABLE `entidadcorreo`
  ADD PRIMARY KEY (`idEntidad`,`idCorreo`),
  ADD KEY `FK_entidad_entidadCorreo` (`idEntidad`),
  ADD KEY `FK_correo_entidadCorreo` (`idCorreo`);

--
-- Indices de la tabla `entidadcuentabancaria`
--
ALTER TABLE `entidadcuentabancaria`
  ADD KEY `FK_entidad_entidadCuentaBancaria` (`idEntidad`);

--
-- Indices de la tabla `entidadcurso`
--
ALTER TABLE `entidadcurso`
  ADD PRIMARY KEY (`idEntidad`,`idCurso`),
  ADD KEY `FK_curso_entidadCurso` (`idCurso`);

--
-- Indices de la tabla `entidadnivelingles`
--
ALTER TABLE `entidadnivelingles`
  ADD PRIMARY KEY (`idEntidad`,`idNivelIngles`),
  ADD KEY `FK_nivelIngles_entidadNivelIngles` (`idNivelIngles`);

--
-- Indices de la tabla `entidadnotificacion`
--
ALTER TABLE `entidadnotificacion`
  ADD PRIMARY KEY (`idEntidad`,`idNotificacion`),
  ADD KEY `FK_entidad_entidadNotificacion` (`idEntidad`),
  ADD KEY `FK_notificacion_entidadNotificacion` (`idNotificacion`);

--
-- Indices de la tabla `horario`
--
ALTER TABLE `horario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IX_FK_entidad_horario` (`idEntidad`);

--
-- Indices de la tabla `interesado`
--
ALTER TABLE `interesado`
  ADD PRIMARY KEY (`idEntidad`);

--
-- Indices de la tabla `nivelingles`
--
ALTER TABLE `nivelingles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pagoalumno`
--
ALTER TABLE `pagoalumno`
  ADD PRIMARY KEY (`idPago`,`idAlumno`),
  ADD KEY `FK_alumno_pagoAlumno` (`idAlumno`);

--
-- Indices de la tabla `pagoclase`
--
ALTER TABLE `pagoclase`
  ADD PRIMARY KEY (`idPago`,`idClase`),
  ADD KEY `FK_clase_pagoClase` (`idClase`);

--
-- Indices de la tabla `pagoprofesor`
--
ALTER TABLE `pagoprofesor`
  ADD PRIMARY KEY (`idPago`,`idProfesor`),
  ADD KEY `FK_profesor_pagoProfesor` (`idProfesor`);

--
-- Indices de la tabla `postulante`
--
ALTER TABLE `postulante`
  ADD PRIMARY KEY (`idEntidad`);

--
-- Indices de la tabla `profesor`
--
ALTER TABLE `profesor`
  ADD PRIMARY KEY (`idEntidad`);

--
-- Indices de la tabla `provincia`
--
ALTER TABLE `provincia`
  ADD PRIMARY KEY (`codigo`),
  ADD KEY `IX_FK_departamento_provincia` (`codigoDepartamento`);

--
-- Indices de la tabla `relacionentidad`
--
ALTER TABLE `relacionentidad`
  ADD PRIMARY KEY (`idEntidadB`,`idEntidadA`),
  ADD KEY `FK_entidad_relacionEntidadA` (`idEntidadA`);

--
-- Indices de la tabla `tarea`
--
ALTER TABLE `tarea`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tareanotificacion`
--
ALTER TABLE `tareanotificacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_usuario_tareaNotificacion` (`idUsuarioCreador`);

--
-- Indices de la tabla `tipodocumento`
--
ALTER TABLE `tipodocumento`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`idEntidad`);

--
-- Indices de la tabla `variablesistema`
--
ALTER TABLE `variablesistema`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UN_llave` (`llave`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clase`
--
ALTER TABLE `clase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15656;
--
-- AUTO_INCREMENT de la tabla `curso`
--
ALTER TABLE `curso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT de la tabla `entidad`
--
ALTER TABLE `entidad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4710;
--
-- AUTO_INCREMENT de la tabla `horario`
--
ALTER TABLE `horario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15494;
--
-- AUTO_INCREMENT de la tabla `interesado`
--
ALTER TABLE `interesado`
  MODIFY `idEntidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4710;
--
-- AUTO_INCREMENT de la tabla `nivelingles`
--
ALTER TABLE `nivelingles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `pago`
--
ALTER TABLE `pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2341;
--
-- AUTO_INCREMENT de la tabla `profesor`
--
ALTER TABLE `profesor`
  MODIFY `idEntidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4621;
--
-- AUTO_INCREMENT de la tabla `tareanotificacion`
--
ALTER TABLE `tareanotificacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21810;
--
-- AUTO_INCREMENT de la tabla `tipodocumento`
--
ALTER TABLE `tipodocumento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `idEntidad` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4703;
--
-- AUTO_INCREMENT de la tabla `variablesistema`
--
ALTER TABLE `variablesistema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alumno`
--
ALTER TABLE `alumno`
  ADD CONSTRAINT `FK_entidad_alumno` FOREIGN KEY (`idEntidad`) REFERENCES `entidad` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_entidad_alumnoProfesorActual` FOREIGN KEY (`idProfesorActual`) REFERENCES `entidad` (`id`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `alumnobolsahoras`
--
ALTER TABLE `alumnobolsahoras`
  ADD CONSTRAINT `FK_alumno_alumnoBolsaHoras` FOREIGN KEY (`idAlumno`) REFERENCES `alumno` (`idEntidad`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_pago_alumnoBolsaHoras` FOREIGN KEY (`idPago`) REFERENCES `pago` (`id`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `clase`
--
ALTER TABLE `clase`
  ADD CONSTRAINT `FK_alumno_clase` FOREIGN KEY (`idAlumno`) REFERENCES `alumno` (`idEntidad`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_claseCancelada_clase` FOREIGN KEY (`idClaseCancelada`) REFERENCES `clase` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_profesor_clase` FOREIGN KEY (`idProfesor`) REFERENCES `profesor` (`idEntidad`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `correo`
--
ALTER TABLE `correo`
  ADD CONSTRAINT `FK_tareaNotificacion_correo` FOREIGN KEY (`id`) REFERENCES `tareanotificacion` (`id`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `distrito`
--
ALTER TABLE `distrito`
  ADD CONSTRAINT `FK_provincia_distrito` FOREIGN KEY (`codigoProvincia`) REFERENCES `provincia` (`codigo`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `entidad`
--
ALTER TABLE `entidad`
  ADD CONSTRAINT `FK_tipoDocumento_entidad` FOREIGN KEY (`idTipoDocumento`) REFERENCES `tipodocumento` (`id`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `entidadcorreo`
--
ALTER TABLE `entidadcorreo`
  ADD CONSTRAINT `FK_correo_entidadCorreo` FOREIGN KEY (`idCorreo`) REFERENCES `correo` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_entidad_entidadCorreo` FOREIGN KEY (`idEntidad`) REFERENCES `entidad` (`id`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `entidadcuentabancaria`
--
ALTER TABLE `entidadcuentabancaria`
  ADD CONSTRAINT `FK_entidad_entidadCuentaBancaria` FOREIGN KEY (`idEntidad`) REFERENCES `entidad` (`id`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `entidadcurso`
--
ALTER TABLE `entidadcurso`
  ADD CONSTRAINT `FK_curso_entidadCurso` FOREIGN KEY (`idCurso`) REFERENCES `curso` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_entidad_entidadCurso` FOREIGN KEY (`idEntidad`) REFERENCES `entidad` (`id`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `entidadnivelingles`
--
ALTER TABLE `entidadnivelingles`
  ADD CONSTRAINT `FK_entidad_entidadNivelIngles` FOREIGN KEY (`idEntidad`) REFERENCES `entidad` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_nivelIngles_entidadNivelIngles` FOREIGN KEY (`idNivelIngles`) REFERENCES `nivelingles` (`id`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `entidadnotificacion`
--
ALTER TABLE `entidadnotificacion`
  ADD CONSTRAINT `FK_entidad_entidadNotificacion` FOREIGN KEY (`idEntidad`) REFERENCES `entidad` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_notificacion_entidadNotificacion` FOREIGN KEY (`idNotificacion`) REFERENCES `notificacion` (`id`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `horario`
--
ALTER TABLE `horario`
  ADD CONSTRAINT `FK_entidad_horario` FOREIGN KEY (`idEntidad`) REFERENCES `entidad` (`id`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `interesado`
--
ALTER TABLE `interesado`
  ADD CONSTRAINT `FK_entidad_interesado` FOREIGN KEY (`idEntidad`) REFERENCES `entidad` (`id`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD CONSTRAINT `FK_tareaNotificacion_notificacion` FOREIGN KEY (`id`) REFERENCES `tareanotificacion` (`id`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `pagoalumno`
--
ALTER TABLE `pagoalumno`
  ADD CONSTRAINT `FK_alumno_pagoAlumno` FOREIGN KEY (`idAlumno`) REFERENCES `alumno` (`idEntidad`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_pago_pagoAlumno` FOREIGN KEY (`idPago`) REFERENCES `pago` (`id`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `pagoclase`
--
ALTER TABLE `pagoclase`
  ADD CONSTRAINT `FK_clase_pagoClase` FOREIGN KEY (`idClase`) REFERENCES `clase` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_pago_pagoClase` FOREIGN KEY (`idPago`) REFERENCES `pago` (`id`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `pagoprofesor`
--
ALTER TABLE `pagoprofesor`
  ADD CONSTRAINT `FK_pago_pagoProfesor` FOREIGN KEY (`idPago`) REFERENCES `pago` (`id`) ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_profesor_pagoProfesor` FOREIGN KEY (`idProfesor`) REFERENCES `profesor` (`idEntidad`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `postulante`
--
ALTER TABLE `postulante`
  ADD CONSTRAINT `FK_entidad_postulante` FOREIGN KEY (`idEntidad`) REFERENCES `entidad` (`id`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `profesor`
--
ALTER TABLE `profesor`
  ADD CONSTRAINT `FK_entidad_profesor` FOREIGN KEY (`idEntidad`) REFERENCES `entidad` (`id`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `provincia`
--
ALTER TABLE `provincia`
  ADD CONSTRAINT `FK_departamento_provincia` FOREIGN KEY (`codigoDepartamento`) REFERENCES `departamento` (`codigo`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `relacionentidad`
--
ALTER TABLE `relacionentidad`
  ADD CONSTRAINT `FK_entidad_relacionEntidadA` FOREIGN KEY (`idEntidadA`) REFERENCES `entidad` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_entidad_relacionEntidadB` FOREIGN KEY (`idEntidadB`) REFERENCES `entidad` (`id`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tarea`
--
ALTER TABLE `tarea`
  ADD CONSTRAINT `FK_tareaNotificacion_tarea` FOREIGN KEY (`id`) REFERENCES `tareanotificacion` (`id`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tareanotificacion`
--
ALTER TABLE `tareanotificacion`
  ADD CONSTRAINT `FK_usuario_tareaNotificacion` FOREIGN KEY (`idUsuarioCreador`) REFERENCES `usuario` (`idEntidad`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `FK_entidad_usuario` FOREIGN KEY (`idEntidad`) REFERENCES `entidad` (`id`) ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
