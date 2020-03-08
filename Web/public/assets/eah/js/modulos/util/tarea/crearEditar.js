var crearEditarTarea = {};
crearEditarTarea = (function ()/* - */ {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery()/* - */ {
    ((window.jQuery && jQuery.ui) ? cargar() : window.setTimeout(esperarCargaJquery, 100));
  }

  //Privado
  function obtenerReferenciaFormulario() {
    return $("#formulario-tarea");
  }
  function cargar() {
    var formulario = obtenerReferenciaFormulario();
    formularioTarea.cargar(formulario);

    $("#btn-cancelar-tarea").click(function () {
      listaTareas.mostrar();
    });
  }
  function obtenerDatos(idTarea, funcionRetorno) {
    urlDatosPago = (typeof (urlDatosPago) === "undefined" ? "" : urlDatosPago);
    if (urlDatosPago !== "") {
      $.blockUI({message: "<h4>Cargando...</h4>", baseZ: 2000});
      util.llamadaAjax(urlDatosTarea.replace("/0", "/" + idTarea), "POST", {}, true,
              function (d) {
                if (funcionRetorno !== undefined) {
                  funcionRetorno(d);
                }
                $("body").unblock();
              },
              function (d) {
              },
              function (de) {
                $('body').unblock({
                  onUnblock: function () {
                    mensajes.agregar("errores", "Ocurrió un problema durante la carga de datos de la tarea seleccionada. Por favor inténtelo nuevamente.", true, "#sec-tareas-mensajes");
                  }
                });
              }
      );
    }
  }

  //Público
  function crear() {
    var formulario = obtenerReferenciaFormulario();

    $("#sec-tareas-crear-editar").find(".box-title").html("Nuevo evento");
    $("div[id^=sec-tareas-]").hide();
    formularioTarea.establecerDatos(formulario);
    $("#sec-tareas-crear-editar").show();
  }
  function editar(idTarea) {
    obtenerDatos(idTarea, function (d) {
      var formulario = obtenerReferenciaFormulario();

      $("#sec-tareas-crear-editar").find(".box-title").html("Editar evento");
      $("div[id^=sec-tareas-]").hide();
      formularioTarea.establecerDatos(formulario, d);
      $("#sec-tareas-crear-editar").show();
    });
  }

  return {
    crear: crear,
    editar: editar
  };
}());