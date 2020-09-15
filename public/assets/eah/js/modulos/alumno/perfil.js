var perfilAlumno = {};
perfilAlumno = (function () {
  $(document).ready(function () {
    $("input[name='horario']").change(function () {
      urlActualizarHorario = (typeof (urlActualizarHorario) === "undefined" ? "" : urlActualizarHorario);
      if (urlActualizarHorario !== "" && $(this).val() !== "") {
        $.blockUI({message: "<h4>Actualizando horario...</h4>"});
        util.llamadaAjax(urlActualizarHorario, "POST", {"horario": $(this).val()}, true,
                function (d) {
                  $("body").unblock({
                    onUnblock: function () {
                      mensajes.agregar("exitosos", "Actualización de horario exitosa.", true);
                    }
                  });
                },
                function (d) {
                },
                function (de) {
                  $("body").unblock({
                    onUnblock: function () {
                      mensajes.agregar("errores", "Ocurrió un problema durante la actualización del horario del alumno. Por favor inténtelo nuevamente.", true);
                    }
                  });
                }
        );
      }
    });

    $("#btn-seleccionar-profesor-actual").click(function () {
      docentesDisponibles.mostrar(function (datosDocente) {
        console.log(datosDocente);
        urlPerfilProfesor = (typeof (urlPerfilProfesor) === "undefined" ? "" : urlPerfilProfesor);
        urlActualizarProfesor = (typeof (urlActualizarProfesor) === "undefined" ? "" : urlActualizarProfesor);

        if (urlPerfilProfesor !== "" && urlActualizarProfesor !== "" && datosDocente.id !== "") {
          $.blockUI({message: "<h4>Guardando cambios...</h4>"});
          util.llamadaAjax(urlActualizarProfesor.replace("/0", "/" + datosDocente.id), "POST", {}, true,
                  function (d) {
                    $("body").unblock({
                      onUnblock: function () {
                        $("#sec-nombre-profesor-actual").html('<a href="' + urlPerfilProfesor.replace("/0", "/" + datosDocente.id) + '" target="_blank">' +
                                datosDocente.nombreCompleto +
                                '</a>');
                      }
                    });
                  },
                  function (d) {
                  },
                  function (de) {
                    $("body").unblock({
                      onUnblock: function () {
                        mensajes.agregar("errores", "Ocurrió un problema durante la actualización del profesor del alumno. Por favor inténtelo nuevamente.", true);
                      }
                    });
                  }
          );
        }
      });
    });
  });
}());