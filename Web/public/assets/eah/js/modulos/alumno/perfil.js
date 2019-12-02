var perfilAlumno = {};
perfilAlumno = (function ()/* - */ {
  $(document).ready(function ()/* - */ {
    $("input[name='horario']").change(function () {
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
  });
}());