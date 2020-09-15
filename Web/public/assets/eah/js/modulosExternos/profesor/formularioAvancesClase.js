var formularioAvancesClase = {};
formularioAvancesClase = (function () {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery() {
    ((window.jQuery && jQuery.ui) ? cargarFormulario() : window.setTimeout(esperarCargaJquery, 100));
  }
  function cargarFormulario() {
    $("#formulario-avances-clase").validate({
      rules: {
        comentario: {
          required: true
        }
      },
      submitHandler: function (f) {
        if (confirm("¿Está seguro que desea guardar los avances de la clase seleccionada?")) {
          $.blockUI({message: "<h4>Guardando...</h4>"});
          var datos = utilFormularios.procesarDatos(f);
          util.llamadaAjax($(f).attr("action"), "POST", datos, true,
                  function (d) {
                    $("body").unblock({
                      onUnblock: function () {
                        mensajes.agregar("exitosos", d["mensaje"], true);
                      }
                    });
                  },
                  function (d) {
                    $("#mod-avances-clase").modal("hide");
                    $("#formulario-avances-clase").find("textarea[name='comentario']").val("");
                    $("#formulario-avances-clase").find("input[name='idClase']").val("");
                    $("#tab-lista-clases").DataTable().ajax.reload();
                  },
                  function (de) {
                    $("body").unblock({
                      onUnblock: function () {
                        var res = de["responseJSON"];
                        mensajes.agregar("errores", (res["mensaje"] ? res["mensaje"] : res[Object.keys(res)[0]][0]), true);
                      }
                    });
                  }
          );
        }
      },
      highlight: function () {
      },
      unhighlight: function () {
      },
      errorElement: "div",
      errorClass: "help-block-error",
      errorPlacement: function (error, element) {
        if (element.closest("div[class*=col-sm-]").length > 0) {
          element.closest("div[class*=col-sm-]").append(error);
        } else if (element.parent(".input-group").length) {
          error.insertAfter(element.parent());
        } else {
          error.insertAfter(element);
        }
      },
      onfocusout: false,
      onkeyup: false,
      onclick: false
    });
  }

  function abrirModal(idClase) {
    var tr = $("#" + idClase);
    var datosFila = $("#tab-lista-clases").DataTable().row(tr).data();
    $("#formulario-avances-clase").find("textarea[name='comentario']").val("");
    $("#formulario-avances-clase").find("input[name='idClase']").val(datosFila.id);
    $("#mod-avances-clase").modal("show");
  }

  return {
    abrirModal: abrirModal
  };
}());