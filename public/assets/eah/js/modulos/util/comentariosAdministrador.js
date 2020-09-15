var comentariosAdministrador = {};
ubigeo = (function () {
  window.addEventListener("load", esperarCargaJquery, false);
  function esperarCargaJquery() {
    ((window.jQuery && jQuery.ui) ? cargarSeccion() : window.setTimeout(esperarCargaJquery, 100));
  }

  function  cargarSeccion() {
    $("#formulario-comentarios-administrador").validate({
      submitHandler: function (f) {
        if (confirm("¿Está seguro que desea guardar los cambios de estos comentarios?")) {
          $.blockUI({message: "<h4>Guardando...</h4>"});
          CKEDITOR.instances["comentarios-administrador"].updateElement();
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
                  },
                  function (de) {
                    $("body").unblock({
                      onUnblock: function () {
                        mensajes.agregar("errores", de["responseJSON"]["mensaje"], true);
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
    CKEDITOR.replace("comentarios-administrador");
  }
}());
